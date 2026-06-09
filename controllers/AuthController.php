<?php

require_once __DIR__ . '/../config/Banco.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::conectar();
    }

    public function cadastrar(
        string $nome,
        string $email,
        string $cpf,
        string $dataNascimento,
        string $senha
    ): array {
        $erros = $this->validarCadastro($nome, $email, $cpf, $dataNascimento, $senha);
        if (!empty($erros)) {
            return ['ok' => false, 'erros' => $erros];
        }

        $cpfLimpo = $this->soDigitos($cpf);

        if ($this->existeCampo('email', $email)) {
            return ['ok' => false, 'erros' => ['Este e-mail ja esta cadastrado.']];
        }
        if ($this->existeCampo('cpf', $cpfLimpo)) {
            return ['ok' => false, 'erros' => ['Este CPF ja esta cadastrado.']];
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $sql  = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, senha)
                 VALUES (:nome, :email, :cpf, :nasc, :senha)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome',  $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':cpf',   $cpfLimpo);
        $stmt->bindParam(':nasc',  $dataNascimento);
        $stmt->bindParam(':senha', $hash);
        $stmt->execute();

        return ['ok' => true, 'erros' => []];
    }

    public function login(string $email, string $senha, bool $lembrar = false): bool {
        $sql  = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$linha || !password_verify($senha, $linha['senha'])) {
            return false;
        }

        $this->iniciarSessao($linha);

        if ($lembrar) {
            $this->criarCookieLembrar((int) $linha['id']);
        }
        return true;
    }

    public function loginPorCookie(string $token): bool {
        $sql  = "SELECT * FROM usuarios WHERE remember_token = :token LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$linha) {
            return false;
        }
        $this->iniciarSessao($linha);
        return true;
    }

    public function logout(): void {
        if (!empty($_SESSION['usuario_id'])) {
            $id   = (int) $_SESSION['usuario_id'];
            $stmt = $this->pdo->prepare("UPDATE usuarios SET remember_token = NULL WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }

        $_SESSION = [];
        session_destroy();
        setcookie('lembrar_token', '', time() - 3600, '/');
    }

    public function recuperarSenha(string $cpf, string $dataNascimento, string $novaSenha): array {
        $cpfLimpo = $this->soDigitos($cpf);

        $sql  = "SELECT * FROM usuarios WHERE cpf = :cpf LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':cpf', $cpfLimpo);
        $stmt->execute();
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$linha) {
            return ['ok' => false, 'erros' => ['CPF nao encontrado.']];
        } elseif ($linha['data_nascimento'] !== $dataNascimento) {
            return ['ok' => false, 'erros' => ['A data de nascimento nao confere com o CPF.']];
        } elseif (strlen($novaSenha) < 6) {
            return ['ok' => false, 'erros' => ['A nova senha deve ter ao menos 6 caracteres.']];
        } else {
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $id   = (int) $linha['id'];
            $up   = $this->pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
            $up->bindParam(':senha', $hash);
            $up->bindParam(':id', $id, PDO::PARAM_INT);
            $up->execute();
            return ['ok' => true, 'erros' => []];
        }
    }

    public function estaLogado(): bool {
        return !empty($_SESSION['usuario_id']);
    }

    public function usuarioLogado(): ?Usuario {
        if (!$this->estaLogado()) {
            return null;
        }
        return new Usuario(
            (int) $_SESSION['usuario_id'],
            $_SESSION['usuario_nome'],
            $_SESSION['usuario_email'],
            $_SESSION['usuario_cpf']  ?? '',
            $_SESSION['usuario_nasc'] ?? '',
            (bool) ($_SESSION['usuario_admin'] ?? false)
        );
    }

    public function exigirLogin(): void {
        if (!$this->estaLogado()) {
            header('Location: /projeto/views/auth/login.php');
            exit;
        }
    }

    public function exigirAdmin(): void {
        $this->exigirLogin();
        if (empty($_SESSION['usuario_admin'])) {
            header('Location: /projeto/index.php');
            exit;
        }
    }

    private function iniciarSessao(array $linha): void {
        session_regenerate_id(true);
        $_SESSION['usuario_id']    = (int) $linha['id'];
        $_SESSION['usuario_nome']  = $linha['nome'];
        $_SESSION['usuario_email'] = $linha['email'];
        $_SESSION['usuario_cpf']   = $linha['cpf'];
        $_SESSION['usuario_nasc']  = $linha['data_nascimento'];
        $_SESSION['usuario_admin'] = (bool) $linha['is_admin'];
    }

    private function criarCookieLembrar(int $usuarioId): void {
        $token = bin2hex(random_bytes(32));
        $stmt  = $this->pdo->prepare("UPDATE usuarios SET remember_token = :token WHERE id = :id");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        setcookie('lembrar_token', $token, time() + (7 * 24 * 60 * 60), '/');
    }

    private function existeCampo(string $campo, string $valor): bool {
        $sql  = "SELECT COUNT(*) FROM usuarios WHERE $campo = :valor";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':valor', $valor);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    private function soDigitos(string $valor): string {
        return preg_replace('/\D/', '', $valor);
    }

    private function validarCadastro(
        string $nome,
        string $email,
        string $cpf,
        string $nasc,
        string $senha
    ): array {
        $erros = [];
        if (strlen(trim($nome)) < 3) {
            $erros[] = 'Informe seu nome completo.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = 'E-mail invalido.';
        }
        if (!$this->cpfValido($cpf)) {
            $erros[] = 'CPF invalido.';
        }
        if (empty($nasc)) {
            $erros[] = 'Informe a data de nascimento.';
        }
        if (strlen($senha) < 6) {
            $erros[] = 'A senha deve ter ao menos 6 caracteres.';
        }
        return $erros;
    }

    private function cpfValido(string $cpf): bool {
        $cpf = $this->soDigitos($cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($i = 0; $i < $t; $i++) {
                $soma += (int) $cpf[$i] * (($t + 1) - $i);
            }
            $digito = ((10 * $soma) % 11) % 10;
            if ((int) $cpf[$t] !== $digito) {
                return false;
            }
        }
        return true;
    }
}

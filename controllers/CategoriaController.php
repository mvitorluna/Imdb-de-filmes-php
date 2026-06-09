<?php

require_once __DIR__ . '/../config/Banco.php';
require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Banco::conectar();
    }

    public function listar(string $ordenarPor = 'nome'): array {
        $coluna = in_array($ordenarPor, ['nome', 'id'], true) ? $ordenarPor : 'nome';
        $sql    = "SELECT id, nome, descricao FROM categorias ORDER BY $coluna ASC";
        $stmt   = $this->pdo->query($sql);
        $rows   = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $categorias = [];
        foreach ($rows as $row) {
            $categorias[] = new Categoria(
                (int) $row['id'],
                $row['nome'],
                $row['descricao'] ?? ''
            );
        }
        return $categorias;
    }

    public function buscarPorId(int $id): ?Categoria {
        $sql  = "SELECT id, nome, descricao FROM categorias WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }
        return new Categoria((int) $row['id'], $row['nome'], $row['descricao'] ?? '');
    }

    public function criar(string $nome, string $descricao = ''): array {
        $erros = $this->validar($nome);
        if (!empty($erros)) {
            return ['ok' => false, 'erros' => $erros];
        }
        $sql  = "INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->execute();
        return ['ok' => true, 'erros' => []];
    }

    public function atualizar(int $id, string $nome, string $descricao = ''): array {
        $erros = $this->validar($nome);
        if (!empty($erros)) {
            return ['ok' => false, 'erros' => $erros];
        }
        $sql  = "UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return ['ok' => true, 'erros' => []];
    }

    public function deletar(int $id): bool {
        $sql  = "DELETE FROM categorias WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function validar(string $nome): array {
        $erros = [];
        if (strlen(trim($nome)) < 2) {
            $erros[] = 'O nome da categoria deve ter ao menos 2 caracteres.';
        }
        return $erros;
    }
}

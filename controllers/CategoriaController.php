<?php

require_once __DIR__ . '/../config/Banco.php';
require_once __DIR__ . '/../models/Categoria.php';

// CRUD de categorias usando PDO (prepare e bindParam)
// Eduardo - tarefa Banco, PDO & Classes Model

class CategoriaController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Banco::conectar();
    }

    // lista todas as categorias (ordena por nome por padrao)
    public function listar(string $ordenarPor = 'nome'): array
    {
        // so deixa ordenar por colunas conhecidas pra evitar SQL injection
        if ($ordenarPor != 'nome' && $ordenarPor != 'id') {
            $ordenarPor = 'nome';
        }

        $sql  = "SELECT id, nome, descricao FROM categorias ORDER BY $ordenarPor ASC";
        $stmt = $this->pdo->query($sql);

        $categorias = [];
        foreach ($stmt->fetchAll() as $linha) {
            $categorias[] = Categoria::fromArray($linha);
        }
        return $categorias;
    }

    public function buscarPorId(int $id): ?Categoria
    {
        $sql  = "SELECT id, nome, descricao FROM categorias WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $linha = $stmt->fetch();
        if (!$linha) {
            return null;
        }
        return Categoria::fromArray($linha);
    }

    public function criar(string $nome, string $descricao = ''): array
    {
        $nome  = trim($nome);
        $erros = $this->validar($nome);

        if ($this->nomeExiste($nome)) {
            $erros[] = 'Ja existe uma categoria com esse nome.';
        }

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

    public function atualizar(int $id, string $nome, string $descricao = ''): array
    {
        $nome  = trim($nome);
        $erros = $this->validar($nome);

        if ($this->nomeExiste($nome, $id)) {
            $erros[] = 'Ja existe outra categoria com esse nome.';
        }

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

    public function deletar(int $id): bool
    {
        $sql  = "DELETE FROM categorias WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // verifica se o nome ja existe (no editar a gente ignora o proprio id)
    private function nomeExiste(string $nome, int $ignorarId = 0): bool
    {
        $sql  = "SELECT COUNT(*) FROM categorias WHERE nome = :nome AND id <> :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':id', $ignorarId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    private function validar(string $nome): array
    {
        $erros = [];
        if (strlen(trim($nome)) < 2) {
            $erros[] = 'O nome da categoria deve ter ao menos 2 caracteres.';
        }
        return $erros;
    }
}

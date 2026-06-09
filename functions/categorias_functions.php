<?php

// Funcoes de categorias com tipagem (parametros e retorno tipados)
// Eduardo - tarefa Banco, PDO & Classes Model

require_once __DIR__ . '/../config/Banco.php';

// conta quantas categorias tem no banco
function contar_categorias(PDO $pdo): int
{
    $stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
    return (int) $stmt->fetchColumn();
}

// pega o nome da categoria pelo id (se nao achar retorna o valor padrao)
function nome_categoria(PDO $pdo, int $id, string $padrao = 'Sem categoria'): string
{
    $stmt = $pdo->prepare("SELECT nome FROM categorias WHERE id = :id LIMIT 1");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $nome = $stmt->fetchColumn();
    if ($nome === false) {
        return $padrao;
    }
    return $nome;
}

// retorna as categorias em formato id => nome (pra usar num <select>)
function opcoes_categorias(PDO $pdo): array
{
    $stmt   = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome ASC");
    $opcoes = [];
    foreach ($stmt->fetchAll() as $linha) {
        $opcoes[$linha['id']] = $linha['nome'];
    }
    return $opcoes;
}

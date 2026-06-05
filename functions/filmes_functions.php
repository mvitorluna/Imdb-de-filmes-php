<?php

require_once __DIR__ . '/../config/Banco.php';
require_once __DIR__ . '/../models/Filme.php';

function buscarTodosFilmes(): array {
    $pdo  = Banco::conectar();
    $sql  = "SELECT f.id, f.titulo, f.genero, f.descricao, f.ano, AVG(a.nota) AS nota_media
             FROM filmes f
             LEFT JOIN avaliacoes a ON a.filme_id = f.id
             GROUP BY f.id";
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filmes = [];
    foreach ($rows as $row) {
        $filmes[] = new Filme(
            (int)   $row['id'],
                    $row['titulo'],
                    $row['genero'],
                    $row['descricao'],
            (int)   $row['ano'],
            (float) ($row['nota_media'] ?? 0)
        );
    }
    return $filmes;
}

function buscarFilmesPorNome(string $nome): array {
    $pdo  = Banco::conectar();
    $sql  = "SELECT f.id, f.titulo, f.genero, f.descricao, f.ano, AVG(a.nota) AS nota_media
             FROM filmes f
             LEFT JOIN avaliacoes a ON a.filme_id = f.id
             WHERE f.titulo LIKE :nome
             GROUP BY f.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':nome' => '%' . $nome . '%']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filmes = [];
    foreach ($rows as $row) {
        $filmes[] = new Filme(
            (int)   $row['id'],
                    $row['titulo'],
                    $row['genero'],
                    $row['descricao'],
            (int)   $row['ano'],
            (float) ($row['nota_media'] ?? 0)
        );
    }
    return $filmes;
}

function buscarFilmesPorGenero(string $genero): array {
    $pdo = Banco::conectar();

    if ($genero === '' || $genero === 'todos') {
        return buscarTodosFilmes();
    } else {
        $sql  = "SELECT f.id, f.titulo, f.genero, f.descricao, f.ano, AVG(a.nota) AS nota_media
                 FROM filmes f
                 LEFT JOIN avaliacoes a ON a.filme_id = f.id
                 WHERE f.genero = :genero
                 GROUP BY f.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':genero' => $genero]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $filmes = [];
    foreach ($rows as $row) {
        $filmes[] = new Filme(
            (int)   $row['id'],
                    $row['titulo'],
                    $row['genero'],
                    $row['descricao'],
            (int)   $row['ano'],
            (float) ($row['nota_media'] ?? 0)
        );
    }
    return $filmes;
}

function buscarFilmesMaisAvaliados(int $limite = 5): array {
    $pdo  = Banco::conectar();
    $sql  = "SELECT f.id, f.titulo, f.genero, f.descricao, f.ano, AVG(a.nota) AS nota_media
             FROM filmes f
             LEFT JOIN avaliacoes a ON a.filme_id = f.id
             GROUP BY f.id
             ORDER BY nota_media DESC
             LIMIT :limite";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filmes = [];
    foreach ($rows as $row) {
        $filmes[] = new Filme(
            (int)   $row['id'],
                    $row['titulo'],
                    $row['genero'],
                    $row['descricao'],
            (int)   $row['ano'],
            (float) ($row['nota_media'] ?? 0)
        );
    }
    return $filmes;
}
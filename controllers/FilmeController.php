<?php

require_once __DIR__ . '/../config/Banco.php';
require_once __DIR__ . '/../models/Filme.php';

// CRUD de filmes usando PDO (prepare e bindParam)
// Controller que recebe POST e chama o método correto da classe

class FilmeController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Banco::conectar();
    }

    // Lista todos os filmes com nota média
    public function listar(string $ordenarPor = 'titulo'): array
    {
        // Só permite ordenar por colunas conhecidas (evita SQL injection)
        $colunasPermitidas = ['titulo', 'id', 'ano', 'genero'];
        if (!in_array($ordenarPor, $colunasPermitidas)) {
            $ordenarPor = 'titulo';
        }

        $sql  = "SELECT f.id, f.titulo, f.genero, f.descricao, f.ano, 
                        COALESCE(AVG(a.nota), 0) AS nota_media
                 FROM filmes f
                 LEFT JOIN avaliacoes a ON a.filme_id = f.id
                 GROUP BY f.id
                 ORDER BY $ordenarPor ASC";
        $stmt = $this->pdo->query($sql);

        $filmes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $filmes[] = Filme::fromArray($linha);
        }
        return $filmes;
    }

    // Busca filme por ID
    public function buscarPorId(int $id): ?Filme
    {
        $sql  = "SELECT f.id, f.titulo, f.genero, f.descricao, f.ano,
                        COALESCE(AVG(a.nota), 0) AS nota_media
                 FROM filmes f
                 LEFT JOIN avaliacoes a ON a.filme_id = f.id
                 WHERE f.id = :id
                 GROUP BY f.id
                 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $linha = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$linha) {
            return null;
        }
        return Filme::fromArray($linha);
    }

    // Lista filmes por categoria/gênero usando foreach
    public function listarPorCategoria(string $categoria): array
    {
        $sql  = "SELECT f.id, f.titulo, f.genero, f.descricao, f.ano,
                        COALESCE(AVG(a.nota), 0) AS nota_media
                 FROM filmes f
                 LEFT JOIN avaliacoes a ON a.filme_id = f.id
                 WHERE f.genero = :genero
                 GROUP BY f.id
                 ORDER BY f.titulo ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':genero', $categoria);
        $stmt->execute();

        $filmes = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $filmes[] = Filme::fromArray($linha);
        }
        return $filmes;
    }

    // Cria novo filme
    public function criar(string $titulo, string $genero, string $descricao, $ano): array
    {
        $titulo    = trim($titulo);
        $genero    = trim($genero);
        $descricao = trim($descricao);

        // Validação dos dados usando empty(), is_numeric()
        $erros = $this->validar($titulo, $genero, $descricao, $ano);

        if ($this->tituloExiste($titulo)) {
            $erros[] = 'Ja existe um filme com esse titulo.';
        }

        if (!empty($erros)) {
            return ['ok' => false, 'erros' => $erros];
        }

        $sql  = "INSERT INTO filmes (titulo, genero, descricao, ano) 
                 VALUES (:titulo, :genero, :descricao, :ano)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_INT);
        $stmt->execute();

        return ['ok' => true, 'erros' => [], 'id' => $this->pdo->lastInsertId()];
    }

    // Atualiza filme existente
    public function atualizar(int $id, string $titulo, string $genero, string $descricao, $ano): array
    {
        $titulo    = trim($titulo);
        $genero    = trim($genero);
        $descricao = trim($descricao);

        // Validação dos dados usando empty(), is_numeric()
        $erros = $this->validar($titulo, $genero, $descricao, $ano);

        if ($this->tituloExiste($titulo, $id)) {
            $erros[] = 'Ja existe outro filme com esse titulo.';
        }

        if (!empty($erros)) {
            return ['ok' => false, 'erros' => $erros];
        }

        $sql  = "UPDATE filmes SET titulo = :titulo, genero = :genero, 
                 descricao = :descricao, ano = :ano WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return ['ok' => true, 'erros' => []];
    }

    // Deleta filme
    public function deletar(int $id): bool
    {
        $sql  = "DELETE FROM filmes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Verifica se título já existe (ignora próprio ID na edição)
    private function tituloExiste(string $titulo, int $ignorarId = 0): bool
    {
        $sql  = "SELECT COUNT(*) FROM filmes WHERE titulo = :titulo AND id <> :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':id', $ignorarId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Validação dos dados usando empty(), is_numeric() etc.
    private function validar(string $titulo, string $genero, string $descricao, $ano): array
    {
        $erros = [];

        // Valida título - não pode estar vazio
        if (empty($titulo)) {
            $erros[] = 'O titulo do filme e obrigatorio.';
        } elseif (strlen($titulo) < 2) {
            $erros[] = 'O titulo deve ter ao menos 2 caracteres.';
        } elseif (strlen($titulo) > 150) {
            $erros[] = 'O titulo deve ter no maximo 150 caracteres.';
        }

        // Valida gênero - não pode estar vazio
        if (empty($genero)) {
            $erros[] = 'O genero do filme e obrigatorio.';
        } elseif (strlen($genero) < 2) {
            $erros[] = 'O genero deve ter ao menos 2 caracteres.';
        } elseif (strlen($genero) > 60) {
            $erros[] = 'O genero deve ter no maximo 60 caracteres.';
        }

        // Valida ano - deve ser numérico e válido
        if (empty($ano)) {
            $erros[] = 'O ano do filme e obrigatorio.';
        } elseif (!is_numeric($ano)) {
            $erros[] = 'O ano deve ser um numero valido.';
        } else {
            $anoInt = (int) $ano;
            $anoAtual = (int) date('Y');
            if ($anoInt < 1888) {
                $erros[] = 'O ano deve ser maior ou igual a 1888 (primeiro filme da historia).';
            } elseif ($anoInt > $anoAtual + 5) {
                $erros[] = 'O ano nao pode ser mais de 5 anos no futuro.';
            }
        }

        // Descrição é opcional, mas se preenchida, verifica tamanho
        if (!empty($descricao) && strlen($descricao) > 5000) {
            $erros[] = 'A descricao deve ter no maximo 5000 caracteres.';
        }

        return $erros;
    }

    // Retorna lista de gêneros disponíveis
    public function listarGeneros(): array
    {
        $sql  = "SELECT DISTINCT genero FROM filmes ORDER BY genero ASC";
        $stmt = $this->pdo->query($sql);
        
        $generos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $linha) {
            $generos[] = $linha['genero'];
        }
        return $generos;
    }
}

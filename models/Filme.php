<?php

// Classe que representa um filme do sistema
// CRUD de filmes com métodos POO

class Filme {
    public int $id;
    public string $titulo;
    public string $genero;
    public string $descricao;
    public int $ano;
    public float $nota_media;

    public function __construct(int $id, string $titulo, string $genero, string $descricao, int $ano, float $nota_media = 0.0) {
        $this->id         = $id;
        $this->titulo     = trim($titulo);
        $this->genero     = trim($genero);
        $this->descricao  = trim($descricao);
        $this->ano        = $ano;
        $this->nota_media = $nota_media;
    }

    // Cria objeto a partir de array do banco
    public static function fromArray(array $linha): self {
        return new self(
            (int)   ($linha['id'] ?? 0),
                    ($linha['titulo'] ?? ''),
                    ($linha['genero'] ?? ''),
                    ($linha['descricao'] ?? ''),
            (int)   ($linha['ano'] ?? 0),
            (float) ($linha['nota_media'] ?? 0)
        );
    }

    // Resumo da descrição com limite de caracteres
    public function resumo(int $limite = 100): string {
        if (strlen($this->descricao) <= $limite) {
            return $this->descricao;
        }
        return substr($this->descricao, 0, $limite) . '...';
    }

    // Formata o título com primeira letra maiúscula
    public function tituloFormatado(): string {
        return ucwords(strtolower($this->titulo));
    }

    // Verifica se o filme tem descrição
    public function temDescricao(): bool {
        return $this->descricao !== '';
    }

    // Retorna a nota formatada com uma casa decimal
    public function notaFormatada(): string {
        return number_format($this->nota_media, 1, ',', '');
    }

    // Verifica se é um filme recente (últimos 5 anos)
    public function ehRecente(): bool {
        return $this->ano >= (int) date('Y') - 5;
    }
}
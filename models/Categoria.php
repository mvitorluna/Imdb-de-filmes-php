<?php

// Classe que representa uma categoria/genero de filme
// Eduardo - tarefa Banco, PDO & Classes Model

class Categoria
{
    public int $id;
    public string $nome;
    public string $descricao;

    // descricao tem valor padrao vazio
    public function __construct(int $id, string $nome, string $descricao = '')
    {
        $this->id        = $id;
        $this->nome      = trim($nome);
        $this->descricao = trim($descricao);
    }

    // cria o objeto a partir de uma linha vinda do banco
    public static function fromArray(array $linha): self
    {
        $id        = (int) ($linha['id'] ?? 0);
        $nome      = $linha['nome'] ?? '';
        $descricao = $linha['descricao'] ?? '';
        return new Categoria($id, $nome, $descricao);
    }

    // corta a descricao se ela for muito grande (limite padrao 60)
    public function resumo(int $limite = 60): string
    {
        if (strlen($this->descricao) <= $limite) {
            return $this->descricao;
        }
        return substr($this->descricao, 0, $limite) . '...';
    }

    // primeira letra maiuscula
    public function nomeFormatado(): string
    {
        return ucfirst(strtolower($this->nome));
    }

    public function temDescricao(): bool
    {
        return $this->descricao != '';
    }
}

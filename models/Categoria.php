<?php

class Categoria {
    public int $id;
    public string $nome;
    public string $descricao;

    public function __construct(int $id, string $nome, string $descricao = '') {
        $this->id        = $id;
        $this->nome      = $nome;
        $this->descricao = $descricao;
    }

    public function resumo(int $limite = 60): string {
        if (strlen($this->descricao) <= $limite) {
            return $this->descricao;
        }
        return substr($this->descricao, 0, $limite) . '...';
    }
}

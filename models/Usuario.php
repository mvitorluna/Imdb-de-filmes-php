<?php

class Usuario {
    public int $id;
    public string $nome;
    public string $email;
    public string $cpf;
    public string $dataNascimento;
    public bool $isAdmin;

    public function __construct(
        int $id,
        string $nome,
        string $email,
        string $cpf,
        string $dataNascimento,
        bool $isAdmin = false
    ) {
        $this->id             = $id;
        $this->nome           = $nome;
        $this->email          = $email;
        $this->cpf            = $cpf;
        $this->dataNascimento = $dataNascimento;
        $this->isAdmin        = $isAdmin;
    }

    public function primeiroNome(): string {
        return explode(' ', trim($this->nome))[0];
    }
}

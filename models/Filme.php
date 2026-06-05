<?php

class Filme {
    public int $id;
    public string $titulo;
    public string $genero;
    public string $descricao;
    public int $ano;
    public float $nota_media;

    public function __construct(int $id, string $titulo, string $genero, string $descricao, int $ano, float $nota_media = 0.0) {
        $this->id         = $id;
        $this->titulo     = $titulo;
        $this->genero     = $genero;
        $this->descricao  = $descricao;
        $this->ano        = $ano;
        $this->nota_media = $nota_media;
    }
}
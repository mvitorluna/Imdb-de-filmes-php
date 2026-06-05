<?php

class Banco {
    private static $conexao = null;

    public static function conectar() {
        if (self::$conexao === null) {
            try {
                self::$conexao = new PDO(
                    "mysql:host=localhost;dbname=locadora;charset=utf8",
                    "root",
                    "",
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                die("Erro na conexão: " . $e->getMessage());
            }
        }
        return self::$conexao;
    }
}
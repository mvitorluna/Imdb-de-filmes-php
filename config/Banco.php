<?php

class Banco {
    private static ?PDO $conexao = null;

    public static function conectar(): PDO {
        if (self::$conexao === null) {
            try {
                self::$conexao = new PDO(
                    "mysql:host=localhost;dbname=imdb;charset=utf8mb4",
                    "root",
                    "",
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                die("Erro na conexao: " . $e->getMessage());
            }
        }
        return self::$conexao;
    }
}

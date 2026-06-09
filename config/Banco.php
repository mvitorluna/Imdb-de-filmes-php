<?php

// Classe de conexao com o banco usando PDO
// Eduardo - tarefa Banco, PDO & Classes Model

class Banco
{
    // dados de acesso (XAMPP: usuario root e senha vazia)
    private const HOST  = 'localhost';
    private const NOME  = 'imdb';
    private const USER  = 'root';
    private const SENHA = '';

    // guarda a conexao pra nao abrir uma nova toda hora
    private static ?PDO $conexao = null;

    public static function conectar(): PDO
    {
        if (self::$conexao != null) {
            return self::$conexao;
        }

        $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::NOME . ";charset=utf8mb4";

        $opcoes = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$conexao = new PDO($dsn, self::USER, self::SENHA, $opcoes);
        } catch (PDOException $e) {
            // guarda o erro real no log e mostra uma mensagem simples
            error_log("Erro ao conectar: " . $e->getMessage());
            exit("Nao foi possivel conectar ao banco de dados.");
        }

        return self::$conexao;
    }
}

-- Banco do projeto IMDB de Filmes
-- Eduardo - tarefa Banco, PDO & Classes Model
--
-- Acesso (XAMPP): host=localhost, banco=imdb, usuario=root, senha=(vazia)
-- Importar:  mysql -u root -p < database/imdb.sql   (ou pelo phpMyAdmin)
-- Depois rodar database/criar_admin.php pra criar o admin.

CREATE DATABASE IF NOT EXISTS imdb
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE imdb;

DROP TABLE IF EXISTS comentarios;
DROP TABLE IF EXISTS avaliacoes;
DROP TABLE IF EXISTS filmes;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nome            VARCHAR(120) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    cpf             VARCHAR(11)  NOT NULL UNIQUE,
    data_nascimento DATE         NOT NULL,
    senha           VARCHAR(255) NOT NULL,
    is_admin        TINYINT(1)   NOT NULL DEFAULT 0,
    remember_token  VARCHAR(64)  DEFAULT NULL,
    criado_em       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    nome      VARCHAR(60)  NOT NULL UNIQUE,
    descricao VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE filmes (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    titulo    VARCHAR(150) NOT NULL,
    genero    VARCHAR(60)  NOT NULL,
    descricao TEXT,
    ano       INT          NOT NULL
) ENGINE=InnoDB;

CREATE TABLE avaliacoes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    filme_id   INT NOT NULL,
    usuario_id INT DEFAULT NULL,
    nota       DECIMAL(3,1) NOT NULL,
    CONSTRAINT chk_nota CHECK (nota >= 0 AND nota <= 10),
    CONSTRAINT fk_aval_filme   FOREIGN KEY (filme_id)   REFERENCES filmes(id)   ON DELETE CASCADE,
    CONSTRAINT fk_aval_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE comentarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    filme_id   INT NOT NULL,
    usuario_id INT NOT NULL,
    texto      TEXT NOT NULL,
    criado_em  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_com_filme   FOREIGN KEY (filme_id)   REFERENCES filmes(id)   ON DELETE CASCADE,
    CONSTRAINT fk_com_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO categorias (nome, descricao) VALUES
('Acao',    'Filmes de acao e aventura'),
('Comedia', 'Filmes de comedia'),
('Drama',   'Filmes dramaticos'),
('Terror',  'Filmes de terror e suspense'),
('Ficcao',  'Ficcao cientifica');

INSERT INTO filmes (titulo, genero, descricao, ano) VALUES
('Matrix',              'Ficcao',  'Um hacker descobre a verdade sobre a realidade.', 1999),
('O Poderoso Chefao',   'Drama',   'A saga de uma familia da mafia italiana.',        1972),
('Toy Story',           'Comedia', 'Brinquedos ganham vida quando ninguem ve.',       1995),
('O Iluminado',         'Terror',  'Uma familia isolada em um hotel assombrado.',     1980),
('Mad Max: Estrada da Furia', 'Acao', 'Perseguicao em um mundo pos-apocaliptico.',    2015);

INSERT INTO avaliacoes (filme_id, usuario_id, nota) VALUES
(1, NULL, 9.0),
(1, NULL, 8.5),
(2, NULL, 9.5),
(3, NULL, 8.0),
(4, NULL, 7.5),
(5, NULL, 8.8);

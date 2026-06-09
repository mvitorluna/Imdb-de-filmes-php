# Banco de dados

Parte do Eduardo - tarefa Banco, PDO & Classes Model.

## Acesso (XAMPP)

- Host: localhost
- Banco: imdb
- Usuario: root
- Senha: (vazia)

Se a senha do seu MySQL nao for vazia, muda a constante SENHA em `config/Banco.php`.

## Como subir o banco

1. Importar o banco:

   mysql -u root -p < database/imdb.sql

   (ou importar o arquivo imdb.sql pelo phpMyAdmin)

2. Criar o usuario admin:

   php database/criar_admin.php

   Login: admin@imdb.com / senha: admin123

## Tabelas

- usuarios - contas dos usuarios
- categorias - generos dos filmes (CRUD dessa tarefa)
- filmes - os filmes
- avaliacoes - notas dos filmes (FK pra filmes e usuarios)
- comentarios - comentarios (FK pra filmes e usuarios)

As tabelas usam chaves estrangeiras pra ligar os dados (avaliacao/comentario
pertencem a um filme e a um usuario).

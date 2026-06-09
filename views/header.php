<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMDB de Filmes</title>
    <link rel="stylesheet" href="/projeto/assets/estilo.css">
</head>
<body>

<header>
    <nav>
        <a href="/projeto/index.php">Inicio</a>
        <a href="/projeto/views/filmes/listar.php">Filmes</a>

        <?php if (!empty($_SESSION['usuario_id'])): ?>
            <?php if (!empty($_SESSION['usuario_admin'])): ?>
                <a href="/projeto/views/categorias/listar.php">Categorias</a>
            <?php endif; ?>
            <span class="nav-user">Ola, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
            <a href="/projeto/views/auth/logout.php">Sair</a>
        <?php else: ?>
            <a href="/projeto/views/auth/login.php">Entrar</a>
            <a href="/projeto/views/auth/cadastro.php">Cadastrar</a>
        <?php endif; ?>
    </nav>
</header>

<main>

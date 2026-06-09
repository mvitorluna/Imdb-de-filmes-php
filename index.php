<?php
require_once __DIR__ . '/config/bootstrap.php';
require_once __DIR__ . '/views/header.php';
?>

<section>
    <h1>IMDB de Filmes</h1>
    <p>Avalie e comente os seus filmes favoritos.</p>

    <ul>
        <li><a href="/projeto/views/filmes/listar.php">Ver filmes</a></li>
        <?php if (empty($_SESSION['usuario_id'])): ?>
            <li><a href="/projeto/views/auth/login.php">Entrar</a></li>
            <li><a href="/projeto/views/auth/cadastro.php">Criar conta</a></li>
        <?php endif; ?>
    </ul>
</section>

<?php require_once __DIR__ . '/views/footer.php'; ?>

<?php

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../functions/filmes_functions.php';

$busca  = isset($_GET['busca'])  ? trim($_GET['busca'])  : '';
$genero = isset($_GET['genero']) ? trim($_GET['genero']) : '';

if ($busca !== '') {
    $filmes = buscarFilmesPorNome($busca);
} elseif ($genero !== '' && $genero !== 'todos') {
    $filmes = buscarFilmesPorGenero($genero);
} else {
    $filmes = buscarTodosFilmes();
}

$destaques = buscarFilmesMaisAvaliados(3);

require_once __DIR__ . '/../header.php';
?>

<section>
    <h2>Melhores Avaliados</h2>
    <div>
        <?php foreach ($destaques as $filme): ?>
            <div>
                <h3><?= htmlspecialchars($filme->titulo) ?></h3>
                <p>Gênero: <?= htmlspecialchars($filme->genero) ?></p>
                <p>Nota: <?= number_format($filme->nota_media, 1) ?> ⭐</p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section>
    <h2>Buscar Filmes</h2>
    <form method="GET" action="">
        <input type="text" name="busca" placeholder="Buscar por nome..." value="<?= htmlspecialchars($busca) ?>">
        <select name="genero">
            <option value="todos">Todos os gêneros</option>
            <option value="Ação"    <?= $genero === 'Ação'    ? 'selected' : '' ?>>Ação</option>
            <option value="Comédia" <?= $genero === 'Comédia' ? 'selected' : '' ?>>Comédia</option>
            <option value="Drama"   <?= $genero === 'Drama'   ? 'selected' : '' ?>>Drama</option>
            <option value="Terror"  <?= $genero === 'Terror'  ? 'selected' : '' ?>>Terror</option>
            <option value="Ficção"  <?= $genero === 'Ficção'  ? 'selected' : '' ?>>Ficção Científica</option>
        </select>
        <button type="submit">Buscar</button>
    </form>
</section>

<section>
    <h2>Todos os Filmes</h2>
    <?php if (empty($filmes)): ?>
        <p>Nenhum filme encontrado.</p>
    <?php else: ?>
        <div>
            <?php foreach ($filmes as $filme): ?>
                <div>
                    <h3><?= htmlspecialchars($filme->titulo) ?></h3>
                    <p>Gênero: <?= htmlspecialchars($filme->genero) ?></p>
                    <p>Ano: <?= $filme->ano ?></p>
                    <p><?= htmlspecialchars($filme->descricao) ?></p>
                    <p>Nota: <?= number_format($filme->nota_media, 1) ?> ⭐</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>
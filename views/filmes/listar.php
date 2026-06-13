<?php

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../functions/filmes_functions.php';
require_once __DIR__ . '/../../core/Csrf.php';

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

// Verifica se é admin para mostrar opções de CRUD
$isAdmin = !empty($_SESSION['usuario_admin']);

// Mensagens de sucesso
$msg = $_GET['msg'] ?? '';

require_once __DIR__ . '/../header.php';
?>

<?php if ($msg === 'criado'): ?>
    <p class="msg-sucesso">Filme cadastrado com sucesso!</p>
<?php elseif ($msg === 'editado'): ?>
    <p class="msg-sucesso">Filme atualizado com sucesso!</p>
<?php elseif ($msg === 'deletado'): ?>
    <p class="msg-sucesso">Filme removido com sucesso!</p>
<?php endif; ?>

<?php if ($isAdmin): ?>
<section>
    <a href="/projeto/views/filmes/criar.php" class="btn-admin">+ Cadastrar Novo Filme</a>
</section>
<?php endif; ?>

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
                    
                    <?php if ($isAdmin): ?>
                    <div class="admin-actions">
                        <a href="/projeto/views/filmes/editar.php?id=<?= $filme->id ?>">Editar</a>
                        <form method="POST" action="/projeto/views/filmes/deletar.php" 
                              style="display:inline;" 
                              onsubmit="return confirm('Tem certeza que deseja excluir este filme?');">
                            <?= Csrf::campo() ?>
                            <input type="hidden" name="id" value="<?= $filme->id ?>">
                            <button type="submit" class="btn-deletar">Excluir</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>
<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';

$auth = new AuthController();
$auth->exigirAdmin();

$controller = new CategoriaController();
$categorias = $controller->listar();

require_once __DIR__ . '/../header.php';
?>

<section>
    <h1>Categorias / Generos</h1>
    <p><a href="/projeto/views/categorias/criar.php">+ Nova categoria</a></p>

    <?php if (empty($categorias)): ?>
        <p>Nenhuma categoria cadastrada.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descricao</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= $categoria->id ?></td>
                        <td><?= htmlspecialchars($categoria->nome) ?></td>
                        <td><?= htmlspecialchars($categoria->resumo()) ?></td>
                        <td>
                            <a href="/projeto/views/categorias/editar.php?id=<?= $categoria->id ?>">Editar</a>
                            <form method="POST" action="/projeto/views/categorias/deletar.php" style="display:inline"
                                  onsubmit="return confirm('Excluir esta categoria?');">
                                <?= Csrf::campo() ?>
                                <input type="hidden" name="id" value="<?= $categoria->id ?>">
                                <button type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>

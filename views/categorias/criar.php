<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';

$auth = new AuthController();
$auth->exigirAdmin();

$controller = new CategoriaController();
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $erros[] = 'Token de seguranca invalido. Recarregue a pagina.';
    } else {
        $resultado = $controller->criar(
            trim($_POST['nome'] ?? ''),
            trim($_POST['descricao'] ?? '')
        );
        if ($resultado['ok']) {
            header('Location: /projeto/views/categorias/listar.php');
            exit;
        }
        $erros = $resultado['erros'];
    }
}

require_once __DIR__ . '/../header.php';
?>

<section class="form-box">
    <h1>Nova categoria</h1>

    <?php if (!empty($erros)): ?>
        <ul class="msg-erro">
            <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <?= Csrf::campo() ?>

        <label>Nome
            <input type="text" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
        </label>

        <label>Descricao
            <textarea name="descricao"><?= htmlspecialchars($_POST['descricao'] ?? '') ?></textarea>
        </label>

        <button type="submit">Salvar</button>
        <a href="/projeto/views/categorias/listar.php">Cancelar</a>
    </form>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>

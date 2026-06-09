<?php
require_once __DIR__ . '/../../config/bootstrap.php';

$auth = new AuthController();
if ($auth->estaLogado()) {
    header('Location: /projeto/index.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $erro = 'Token de seguranca invalido. Recarregue a pagina.';
    } else {
        $email   = trim($_POST['email'] ?? '');
        $senha   = $_POST['senha'] ?? '';
        $lembrar = isset($_POST['lembrar']);

        if ($auth->login($email, $senha, $lembrar)) {
            header('Location: /projeto/index.php');
            exit;
        } else {
            $erro = 'E-mail ou senha incorretos.';
        }
    }
}

require_once __DIR__ . '/../header.php';
?>

<section class="form-box">
    <h1>Entrar</h1>

    <?php if ($erro !== ''): ?>
        <p class="msg-erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <?= Csrf::campo() ?>

        <label>E-mail
            <input type="email" name="email" required>
        </label>

        <label>Senha
            <input type="password" name="senha" required>
        </label>

        <label class="check">
            <input type="checkbox" name="lembrar"> Lembrar de mim
        </label>

        <button type="submit">Entrar</button>
    </form>

    <p>
        <a href="/projeto/views/auth/cadastro.php">Criar conta</a> |
        <a href="/projeto/views/auth/recuperar.php">Esqueci minha senha</a>
    </p>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>

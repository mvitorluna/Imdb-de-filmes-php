<?php
require_once __DIR__ . '/../../config/bootstrap.php';

$auth = new AuthController();
if ($auth->estaLogado()) {
    header('Location: /projeto/index.php');
    exit;
}

$erros   = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $erros[] = 'Token de seguranca invalido. Recarregue a pagina.';
    } else {
        $resultado = $auth->cadastrar(
            trim($_POST['nome'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['cpf'] ?? ''),
            $_POST['data_nascimento'] ?? '',
            $_POST['senha'] ?? ''
        );
        if ($resultado['ok']) {
            $sucesso = true;
        } else {
            $erros = $resultado['erros'];
        }
    }
}

require_once __DIR__ . '/../header.php';
?>

<section class="form-box">
    <h1>Criar conta</h1>

    <?php if ($sucesso): ?>
        <p class="msg-ok">Conta criada com sucesso! Agora e so <a href="/projeto/views/auth/login.php">entrar</a>.</p>
    <?php else: ?>

        <?php if (!empty($erros)): ?>
            <ul class="msg-erro">
                <?php foreach ($erros as $erro): ?>
                    <li><?= htmlspecialchars($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="">
            <?= Csrf::campo() ?>

            <label>Nome completo
                <input type="text" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
            </label>

            <label>E-mail
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </label>

            <label>CPF
                <input type="text" name="cpf" placeholder="000.000.000-00" value="<?= htmlspecialchars($_POST['cpf'] ?? '') ?>" required>
            </label>

            <label>Data de nascimento
                <input type="date" name="data_nascimento" value="<?= htmlspecialchars($_POST['data_nascimento'] ?? '') ?>" required>
            </label>

            <label>Senha
                <input type="password" name="senha" required>
            </label>

            <button type="submit">Cadastrar</button>
        </form>

        <p><a href="/projeto/views/auth/login.php">Ja tenho conta</a></p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>

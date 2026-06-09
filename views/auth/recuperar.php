<?php
require_once __DIR__ . '/../../config/bootstrap.php';

$auth    = new AuthController();
$erros   = [];
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $erros[] = 'Token de seguranca invalido. Recarregue a pagina.';
    } else {
        $resultado = $auth->recuperarSenha(
            trim($_POST['cpf'] ?? ''),
            $_POST['data_nascimento'] ?? '',
            $_POST['nova_senha'] ?? ''
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
    <h1>Recuperar senha</h1>
    <p>Confirme seu CPF e data de nascimento para definir uma nova senha.</p>

    <?php if ($sucesso): ?>
        <p class="msg-ok">Senha alterada com sucesso! <a href="/projeto/views/auth/login.php">Entrar</a>.</p>
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

            <label>CPF
                <input type="text" name="cpf" placeholder="000.000.000-00" required>
            </label>

            <label>Data de nascimento
                <input type="date" name="data_nascimento" required>
            </label>

            <label>Nova senha
                <input type="password" name="nova_senha" required>
            </label>

            <button type="submit">Alterar senha</button>
        </form>

        <p><a href="/projeto/views/auth/login.php">Voltar ao login</a></p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>

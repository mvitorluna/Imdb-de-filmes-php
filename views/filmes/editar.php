<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/FilmeController.php';

// Somente administradores podem acessar
$auth = new AuthController();
$auth->exigirAdmin();

$controller = new FilmeController();
$erros = [];

// Pega o ID do filme (POST ou GET)
$id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

// Processa o formulário quando enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação CSRF
    if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
        $erros[] = 'Token de seguranca invalido. Recarregue a pagina.';
    } else {
        // Chama o método do controller para atualizar o filme
        $resultado = $controller->atualizar(
            $id,
            trim($_POST['titulo'] ?? ''),
            trim($_POST['genero'] ?? ''),
            trim($_POST['descricao'] ?? ''),
            $_POST['ano'] ?? ''
        );
        
        if ($resultado['ok']) {
            header('Location: /projeto/views/filmes/listar.php?msg=editado');
            exit;
        }
        $erros = $resultado['erros'];
    }
}

// Busca o filme pelo ID
$filme = $controller->buscarPorId($id);

// Se não encontrou o filme, exibe mensagem
if (!$filme) {
    require_once __DIR__ . '/../header.php';
    echo '<section><p>Filme nao encontrado.</p>';
    echo '<a href="/projeto/views/filmes/listar.php">Voltar</a></section>';
    require_once __DIR__ . '/../footer.php';
    exit;
}

// Lista de gêneros para o select
$generos = $controller->listarGeneros();

require_once __DIR__ . '/../header.php';
?>

<section class="form-box">
    <h1>Editar Filme</h1>

    <?php if (!empty($erros)): ?>
        <ul class="msg-erro">
            <?php foreach ($erros as $erro): ?>
                <li><?= htmlspecialchars($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" value="<?= $filme->id ?>">

        <label>Titulo *
            <input type="text" name="titulo" 
                   value="<?= htmlspecialchars($_POST['titulo'] ?? $filme->titulo) ?>" 
                   required maxlength="150">
        </label>

        <label>Genero *
            <input type="text" name="genero" 
                   value="<?= htmlspecialchars($_POST['genero'] ?? $filme->genero) ?>" 
                   required maxlength="60" 
                   list="generos-lista"
                   placeholder="Ex: Acao, Drama, Comedia...">
            <datalist id="generos-lista">
                <?php foreach ($generos as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>">
                <?php endforeach; ?>
            </datalist>
        </label>

        <label>Ano *
            <input type="number" name="ano" 
                   value="<?= htmlspecialchars($_POST['ano'] ?? $filme->ano) ?>" 
                   required min="1888" max="<?= date('Y') + 5 ?>">
        </label>

        <label>Descricao
            <textarea name="descricao" rows="5" 
                      maxlength="5000"><?= htmlspecialchars($_POST['descricao'] ?? $filme->descricao) ?></textarea>
        </label>

        <button type="submit">Atualizar Filme</button>
        <a href="/projeto/views/filmes/listar.php">Cancelar</a>
    </form>
</section>

<?php require_once __DIR__ . '/../footer.php'; ?>

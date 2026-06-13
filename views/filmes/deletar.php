<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/FilmeController.php';

// Somente administradores podem acessar
$auth = new AuthController();
$auth->exigirAdmin();

// Processa a deleção quando enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::validar($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        (new FilmeController())->deletar($id);
    }
}

// Redireciona para a listagem
header('Location: /projeto/views/filmes/listar.php?msg=deletado');
exit;

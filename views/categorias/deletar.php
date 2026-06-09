<?php
require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../controllers/CategoriaController.php';

$auth = new AuthController();
$auth->exigirAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Csrf::validar($_POST['csrf_token'] ?? null)) {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        (new CategoriaController())->deletar($id);
    }
}

header('Location: /projeto/views/categorias/listar.php');
exit;

<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Banco.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../controllers/AuthController.php';

if (empty($_SESSION['usuario_id']) && !empty($_COOKIE['lembrar_token'])) {
    $auth = new AuthController();
    $auth->loginPorCookie($_COOKIE['lembrar_token']);
}

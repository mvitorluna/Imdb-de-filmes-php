<?php
require_once __DIR__ . '/../../config/bootstrap.php';

(new AuthController())->logout();

header('Location: /projeto/index.php');
exit;

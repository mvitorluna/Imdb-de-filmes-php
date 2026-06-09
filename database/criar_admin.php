<?php
require_once __DIR__ . '/../config/Banco.php';

$pdo = Banco::conectar();

$email = 'admin@imdb.com';
$cpf   = '52998224725';
$nasc  = '2000-01-01';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();

if ((int) $stmt->fetchColumn() > 0) {
    echo "Admin ja existe. Use $email / admin123";
    exit;
}

$hash = password_hash('admin123', PASSWORD_DEFAULT);

$sql  = "INSERT INTO usuarios (nome, email, cpf, data_nascimento, senha, is_admin)
         VALUES (:nome, :email, :cpf, :nasc, :senha, 1)";
$stmt = $pdo->prepare($sql);
$nome = 'Administrador';
$stmt->bindParam(':nome',  $nome);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':cpf',   $cpf);
$stmt->bindParam(':nasc',  $nasc);
$stmt->bindParam(':senha', $hash);
$stmt->execute();

echo "Admin criado com sucesso!<br>";
echo "E-mail: $email<br>";
echo "Senha: admin123<br>";
echo "CPF: $cpf | Nascimento: $nasc";

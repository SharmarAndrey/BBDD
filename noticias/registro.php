<?php

require_once "conexion.php";

//creamos carpeta avatars si no existe
$avatarDir = "img/avatars/";
if (!is_dir($avatarDir)) {
    mkdir($avatarDir, 0755, true);
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$avatarPath = "img/default-avatar.png";

if (!$nombre || !$email || !$password) {
    header("Location: registro.html?error=" . urlencode("Todos los campos son obligatorios"));
    exit;
}

// Verificar si ya existe el email
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header("Location: registro.html?error=" . urlencode("El email ya está registrado"));
    exit;
}

// Subir avatar
$avatarPath = null;
if (!empty($_FILES['avatar']['tmp_name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
    $uploadsDir = 'img/avatars/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('avatar_', true) . "." . $ext;
    $targetPath = $uploadsDir . $filename;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $avatarPath = $targetPath;
    }
}
// Insertar nuevo usuario
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, avatar) VALUES (?, ?, ?, ?)");
$stmt->execute([$nombre, $email, $hash, $avatarPath]);

header("Location: login.html");
exit;

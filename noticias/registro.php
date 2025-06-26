<?php

require_once "conexion.php";

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

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

// Insertar nuevo usuario
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
$stmt->execute([$nombre, $email, $hash]);

header("Location: login.html");
exit;

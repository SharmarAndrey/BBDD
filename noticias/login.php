<?php

session_start();
require_once "conexion.php";

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    header("Location: login.html?error=" . urlencode("Todos los campos son obligatorios"));
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['nombre'];
    header("Location: index.php");
    exit;
} else {
    header("Location: login.html?error=" . urlencode("❌ Email o contraseña incorrectos"));
    exit;
}

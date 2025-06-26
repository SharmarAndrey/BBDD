<?php

session_start();
require_once "conexion.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Buscar usuario por email
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar existencia y contraseña
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['nombre']; // или $user['email']
    header("Location: index.php");
    exit;
} else {
    echo "❌ Email o contraseña incorrectos.";
    echo '<br><a href="login.html">← Volver</a>';
}

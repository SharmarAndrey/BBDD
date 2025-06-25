<?php

session_start();

$correct_email = "test@example.com";
$correct_password = "1234";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email === $correct_email && $password === $correct_password) {
    $_SESSION['user'] = $email;
    header("Location: index.php");
    exit;
} else {
    echo "❌ Email o contraseña incorrectos.";
    echo '<br><a href="login.html">← Volver</a>';
}

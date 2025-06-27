<?php

session_start();
require_once "conexion.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ?");
$stmt->execute([$_SESSION['user']]);
$user_id = $stmt->fetchColumn();

$noticia_id = $_POST['noticia_id'] ?? null;
$contenido = trim($_POST['contenido'] ?? '');

if ($noticia_id && $contenido && $user_id) {
    $stmt = $pdo->prepare("INSERT INTO comentarios (noticia_id, user_id, contenido) VALUES (?, ?, ?)");
    $stmt->execute([$noticia_id, $user_id, $contenido]);
}

header("Location: noticia.php?id=" . $noticia_id);
exit;

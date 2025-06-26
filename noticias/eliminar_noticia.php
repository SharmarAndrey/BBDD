<?php

session_start();
require_once "conexion.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID faltante");
}

$userName = $_SESSION['user'] ?? null;
$stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ?");
$stmtUser->execute([$userName]);
$currentUserId = $stmtUser->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    die("Noticia no encontrada");
}
if ($noticia['user_id'] != $currentUserId) {
    die("⛔ No tienes permiso para eliminar esta noticia.");
}

if ($noticia['imagen'] && $noticia['imagen'] !== "img/default.jpg" && file_exists($noticia['imagen'])) {
    unlink($noticia['imagen']);
}

$stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;

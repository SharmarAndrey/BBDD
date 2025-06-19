<?php

require_once "conexion.php";

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT imagen FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $imagen = $stmt->fetchColumn();
    if ($imagen && $imagen !== "img/default.jpg" && file_exists($imagen)) {
        unlink($imagen);
    }
    $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;

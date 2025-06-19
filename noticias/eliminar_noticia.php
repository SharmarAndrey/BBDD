<?php

require_once "conexion.php";

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT imagen FROM noticias WHERE id = ?");
    $stmt->execute([$id]);
    $imagen = $stmt->fetchColumn();

    $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->execute([$id]);

    if ($imagen && $imagen !== "img/default.jpg" && file_exists($imagen)) {
        unlink($imagen);
    }
}

header("Location: index.php");
exit;

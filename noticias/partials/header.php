<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Noticias</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<header>
  <nav>
    <ul class="nav-list">
      <li><a href="index.php">Inicio</a></li>
      <li><a href="anadir_noticia.php">Añadir Noticia</a></li>
      <li class="nav-user">
        <?php if (isset($_SESSION['user'])): ?>
          Bienvenido, <?= htmlspecialchars($_SESSION['user']) ?> |
          <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
          <a href="login.html">Iniciar sesión</a>
        <?php endif; ?>
      </li>
    </ul>
  </nav>
</header>
<main class="container">

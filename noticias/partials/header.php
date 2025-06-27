<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../conexion.php';

$avatarPath = 'img/default-avatar.png';
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT avatar FROM usuarios WHERE nombre = ?");
    $stmt->execute([$_SESSION['user']]);
    $userData = $stmt->fetch();
    if ($userData && !empty($userData['avatar']) && file_exists($userData['avatar'])) {
        $avatarPath = $userData['avatar'];
    }
}

$current = basename($_SERVER['PHP_SELF']);
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
  <nav class="nav-container">
    <ul class="nav-list">
      <li><a href="index.php" class="<?= $current === 'index.php' ? 'active' : '' ?>">Inicio</a></li>
      <li><a href="anadir_noticia.php" class="<?= $current === 'anadir_noticia.php' ? 'active' : '' ?>">Añadir Noticia</a></li>
    </ul>
    <div class="user-info">
      <?php if (isset($_SESSION['user'])): ?>
        <div class="avatar-menu-container">
          <img src="<?= htmlspecialchars($avatarPath) ?>" alt="avatar" class="avatar-header" onclick="toggleDropdown()" />
		  
          <div class="dropdown-menu" id="dropdownMenu">
            <a href="user.php">Mi perfil</a>
            <a href="mis_noticias.php">Mis noticias</a>
            <a href="logout.php">Cerrar sesión</a>
          </div>
        </div>
        <span class="welcome-text">Bienvenido, <?= htmlspecialchars($_SESSION['user']) ?></span>
      <?php else: ?>
        <a href="login.html" class="<?= $current === 'login.html' ? 'active' : '' ?>">Iniciar sesión</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
<main class="container">

<script>
  function toggleDropdown() {
    const menu = document.getElementById("dropdownMenu");
    menu.classList.toggle("show");
  }

  window.onclick = function(event) {
    if (!event.target.matches('.avatar-header')) {
      const dropdowns = document.getElementsByClassName("dropdown-menu");
      for (const dd of dropdowns) {
        dd.classList.remove("show");
      }
    }
  };
</script>

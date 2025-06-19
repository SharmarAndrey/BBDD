<?php
require_once "conexion.php";
require_once "partials/header.php";

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<?php if ($noticia): ?>
  <div class="details">
    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
    <small>Categoría: <?= htmlspecialchars($noticia['categoria']) ?> | Fecha: <?= $noticia['fecha'] ?></small>
    <?php if (!empty($noticia['imagen'])): ?>
      <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen">
    <?php endif; ?>
    <p><?= nl2br(string: htmlspecialchars($noticia['descripcion'])) ?></p>
    <small>Publicado por usuario #<?= $noticia['user_id'] ?></small>
    <div class="details-actions">
      <a href="index.php" class="btn">← Volver</a>
    </div>
  </div>
<?php else: ?>
  <p class="error">❌ Noticia no encontrada.</p>
  <a href="index.php" class="btn">← Volver</a>
<?php endif; ?>

<?php require_once "partials/footer.php"; ?>

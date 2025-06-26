<?php
require_once "conexion.php";
require_once "partials/header.php";

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("
    SELECT n.*, c.nombre AS categoria 
    FROM noticias n 
    JOIN categorias c ON n.categoria_id = c.id 
    WHERE n.id = ?
");
$stmt->execute([$id]);
$noticia = $stmt->fetch();
?>

<?php if ($noticia): ?>
  <div class="details">
    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
    <small>
      Categoría: <strong><?= htmlspecialchars($noticia['categoria']) ?></strong> |
      Fecha: <?= htmlspecialchars($noticia['fecha']) ?>
    </small>

    <?php if ($noticia['imagen'] && file_exists($noticia['imagen'])): ?>
      <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen de la noticia">
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($noticia['descripcion'])) ?></p>

    <div class="details-actions">
      <a href="index.php" class="btn">← Volver a noticias</a>
    </div>
  </div>
<?php else: ?>
  <p class="error">❌ Noticia no encontrada.</p>
<?php endif; ?>

<?php require_once "partials/footer.php"; ?>

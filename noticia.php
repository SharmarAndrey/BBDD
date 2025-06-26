<?php
// Conexión a la base de datos y encabezado HTML
require_once "conexion.php";
require_once "partials/header.php";

// Obtener ID de la noticia desde GET
$id = $_GET['id'] ?? null;

// Preparar consulta para obtener los datos de la noticia y su categoría
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
	<!-- Mostrar título de la noticia -->
    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
    <small>
      Categoría: <strong><?= htmlspecialchars($noticia['categoria']) ?></strong> |
      Fecha: <?= htmlspecialchars($noticia['fecha']) ?>
    </small>

	 <!-- Mostrar imagen si existe en disco -->
    <?php if ($noticia['imagen'] && file_exists($noticia['imagen'])): ?>
      <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen de la noticia">
    <?php endif; ?>

	<!-- Mostrar descripción de la noticia con saltos de línea -->
    <p><?= nl2br(htmlspecialchars($noticia['descripcion'])) ?></p>

	   <!-- Botón para volver al listado de noticias -->
    <div class="details-actions">
      <a href="index.php" class="btn">← Volver a noticias</a>
    </div>
  </div>
<?php else: ?>
	  <!-- Si no se encuentra la noticia -->
  <p class="error">❌ Noticia no encontrada.</p>
<?php endif; ?>

<?php require_once "partials/footer.php"; ?>

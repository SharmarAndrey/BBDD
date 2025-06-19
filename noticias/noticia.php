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
$n = $stmt->fetch();
?>

<?php if ($n): ?>
    <div class="details">
        <h1><?= htmlspecialchars($n['titulo']) ?></h1>
        <p>Categoría: <?= htmlspecialchars($n['categoria']) ?> | Fecha: <?= $n['fecha'] ?></p>

        <?php if ($n['imagen'] && file_exists($n['imagen'])): ?>
            <img src="<?= htmlspecialchars($n['imagen']) ?>" alt="Imagen" style="max-width: 400px;">
        <?php endif; ?>

        <p><?= nl2br(htmlspecialchars($n['descripcion'])) ?></p>

        <!-- Button with visible text-->
        <a href="index.php" class="btn">← Volver a noticias</a>
    </div>
<?php else: ?>
    <p>❌ Noticia no encontrada.</p>
<?php endif; ?>

<?php require_once "partials/footer.php"; ?>

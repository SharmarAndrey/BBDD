<?php
require_once "conexion.php";
$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

require_once "partials/header.php";
?>
<main>
<div class="tarjeta">
    <?php if ($noticia): ?>
        <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
        <div class="meta">
            <small><strong>Categoría:</strong> <?= htmlspecialchars($noticia['categoria']) ?> &nbsp; | &nbsp;
            <strong>Fecha:</strong> <?= $noticia['fecha'] ?></small>
        </div>
        <p class="contenido"><?= nl2br(htmlspecialchars($noticia['descripcion'])) ?></p>
        <div class="meta">
            <small><strong>Publicado por:</strong> <?= $noticia['user_id'] ?></small>
        </div>
        <a href="index.php" class="button">← Volver</a>
    <?php else: ?>
        <p class="error">❌ Noticia no encontrada.</p>
        <a href="index.php" class="button">← Volver</a>
    <?php endif; ?>
</div>
</main>
<?php require_once "partials/footer.php"; ?>

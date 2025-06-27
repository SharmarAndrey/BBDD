<?php
require_once "conexion.php";
require_once "partials/header.php";
session_start();

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("
    SELECT n.*, c.nombre AS categoria, u.nombre AS autor, u.avatar AS autor_avatar
    FROM noticias n
    JOIN categorias c ON n.categoria_id = c.id
    JOIN usuarios u ON n.user_id = u.id
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
      Fecha: <?= htmlspecialchars($noticia['fecha']) ?> |
      Autor:
      <?php if (!empty($noticia['autor_avatar']) && file_exists($noticia['autor_avatar'])): ?>
        <img src="<?= htmlspecialchars($noticia['autor_avatar']) ?>" alt="Avatar" style="width: 24px; height: 24px; object-fit: cover; border-radius: 50%; vertical-align: middle; margin-right: 4px;">
      <?php endif; ?>
      <strong><?= htmlspecialchars($noticia['autor']) ?></strong>
    </small>

    <?php if ($noticia['imagen'] && file_exists($noticia['imagen'])): ?>
      <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen de la noticia">
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($noticia['descripcion'])) ?></p>

    <div class="details-actions">
      <a href="index.php" class="btn">← Volver a noticias</a>
    </div>

    <hr>

    <?php
    // Comentarios
    $stmtComentarios = $pdo->prepare("
      SELECT c.contenido, c.fecha, u.nombre, u.avatar 
      FROM comentarios c
      JOIN usuarios u ON c.user_id = u.id
      WHERE c.noticia_id = ?
      ORDER BY c.fecha DESC
    ");
    $stmtComentarios->execute([$id]);
    $comentarios = $stmtComentarios->fetchAll();
    ?>

    <h3>💬 Comentarios</h3>

    <?php if ($comentarios): ?>
      <?php foreach ($comentarios as $coment): ?>
        <div class="comentario" style="margin-bottom:1em;">
          <strong>
            <?php if (!empty($coment['avatar']) && file_exists($coment['avatar'])): ?>
             <img src="<?= htmlspecialchars($coment['avatar']) ?>" alt="Avatar" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%; vertical-align: middle; margin-right: 8px;">
            <?php endif; ?>
            <?= htmlspecialchars($coment['nombre']) ?>
          </strong> 
          <small><?= $coment['fecha'] ?></small>
          <p><?= nl2br(htmlspecialchars($coment['contenido'])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No hay comentarios aún.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user'])): ?>
      <form action="comentar.php" method="post">
        <input type="hidden" name="noticia_id" value="<?= $id ?>">
        <textarea name="contenido" rows="3" required placeholder="Escribe tu comentario..." style="width:100%;"></textarea>
        <button type="submit" class="btn">Enviar comentario</button>
      </form>
    <?php else: ?>
      <p><a href="login.php">Inicia sesión</a> para comentar.</p>
    <?php endif; ?>

  </div>
<?php else: ?>
  <p class="error">❌ Noticia no encontrada.</p>
<?php endif; ?>

<?php require_once "partials/footer.php"; ?>


<?php
require_once "conexion.php";
$id = $_GET['id'] ?? null;

if (!$id) {
    die("❌ ID faltante");
}

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$noticia) {
    die("❌ Noticia no encontrada");
}

$titulo = $noticia['titulo'];
$categoria = $noticia['categoria'];
$descripcion = $noticia['descripcion'];
$updateSuccess = $updateErr = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);

    if ($titulo && $categoria && $descripcion) {
        try {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, descripcion=?, categoria=? WHERE id=?");
            $stmt->execute([$titulo, $descripcion, $categoria, $id]);

            // Si hay nueva imagen
            if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
                $uploadDir = "img/";
                $extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif'];
                if (in_array($extension, $allowed)) {
                    $fileName = uniqid() . '.' . $extension;
                    $newImagePath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $newImagePath)) {
                        if ($noticia['imagen'] !== "img/default.jpg" && file_exists($noticia['imagen'])) {
                            unlink($noticia['imagen']);
                        }
                        $stmt = $pdo->prepare("UPDATE noticias SET imagen=? WHERE id=?");
                        $stmt->execute([$newImagePath, $id]);
                    }
                }
            }

            $updateSuccess = "✅ Noticia actualizada";
        } catch (Exception $e) {
            $updateErr = "❌ Error: " . $e->getMessage();
        }
    }
}

require_once "partials/header.php";
?>

<main>
  <div id="main">
    <?php if ($updateSuccess): ?>
      <p class="success"><?= $updateSuccess ?></p>
    <?php endif; ?>
    <?php if ($updateErr): ?>
      <p class="error"><?= $updateErr ?></p>
    <?php endif; ?>

    <h1>Editar Noticia</h1>

    <form method="POST" enctype="multipart/form-data">
  <label for="titulo">Título:</label>
  <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo) ?>">

  <label for="categoria">Categoría:</label>
  <select id="categoria" name="categoria">
    <option value="Noticias" <?= $categoria == "Noticias" ? 'selected' : '' ?>>Noticias</option>
    <option value="Deportes" <?= $categoria == "Deportes" ? 'selected' : '' ?>>Deportes</option>
  </select>

  <label for="descripcion">Descripción:</label>
  <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>

  <label>Nueva Imagen:</label>
  <input type="file" name="file">

  <button type="submit" class="btn">Actualizar</button>
  <a href="index.php" class="btn">← Volver</a>
</form>

  </div>
</main>

<?php require_once "partials/footer.php"; ?>

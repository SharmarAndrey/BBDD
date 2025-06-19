<?php
require_once "conexion.php";
require_once "partials/header.php";

$tituloErr = $categoriaErr = $descripcionErr = $insertaErr = "";
$titulo = $categoria = $nuevaCategoria = $descripcion = $insertaSuccess = "";
$user_id = $_POST['user_id'] ?? 3;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST["titulo"]);
    $categoria = trim($_POST["categoria"]);
    $nuevaCategoria = trim($_POST["nueva_categoria"]);
    $descripcion = trim($_POST["descripcion"]);
    $imagen = "img/default.jpg";

    if ($nuevaCategoria) {
        $categoria = $nuevaCategoria;
    }

    if (!$titulo) {
        $tituloErr = "Introduce un título";
    }
    if (!$categoria) {
        $categoriaErr = "Selecciona o introduce categoría";
    }
    if (!$descripcion) {
        $descripcionErr = "Escribe descripción";
    }

    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $uploadDir = "img/";
        $extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($extension, $allowed)) {
            $fileName = uniqid() . '.' . $extension;
            $imagePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $imagePath)) {
                $imagen = $imagePath;
            } else {
                $insertaErr = "❌ Error subiendo imagen.";
            }
        } else {
            $insertaErr = "❌ Formato de imagen no permitido.";
        }
    }

    if (!$tituloErr && !$categoriaErr && !$descripcionErr && !$insertaErr) {
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, descripcion, categoria, user_id, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $categoria, $user_id, $imagen]);
        $insertaSuccess = "✅ Noticia añadida";
        $titulo = $categoria = $nuevaCategoria = $descripcion = "";
    }
}
?>

<h1>Añadir Noticia</h1>

<h3 class="success"><?= $insertaSuccess ?></h3>
<h3 class="error"><?= $insertaErr ?></h3>

<form method="POST" enctype="multipart/form-data">
  <label>Título:</label>
  <input type="text" name="titulo" value="<?= htmlspecialchars($titulo) ?>">
  <span class="error"><?= $tituloErr ?></span>

  <label>Categoría existente:</label>
  <select name="categoria">
    <option value="">-- Selecciona existente --</option>
    <option value="Noticias" <?= $categoria == "Noticias" ? 'selected' : '' ?>>Noticias</option>
    <option value="Deportes" <?= $categoria == "Deportes" ? 'selected' : '' ?>>Deportes</option>
  </select>

  <label>O nueva categoría:</label>
  <input type="text" name="nueva_categoria" value="<?= htmlspecialchars($nuevaCategoria) ?>">
  <span class="error"><?= $categoriaErr ?></span>

  <label>Descripción:</label>
  <textarea name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>
  <span class="error"><?= $descripcionErr ?></span>

  <label>Imagen:</label>
  <input type="file" name="file">

  <label>Usuario:</label>
  <select name="user_id">
    <option value="1" <?= $user_id == 1 ? 'selected' : '' ?>>Usuario 1</option>
    <option value="2" <?= $user_id == 2 ? 'selected' : '' ?>>Usuario 2</option>
    <option value="3" <?= $user_id == 3 ? 'selected' : '' ?>>Usuario 3</option>
  </select>

  <button type="submit" class="btn">Añadir</button>
  <a href="index.php" class="btn">← Volver</a>
</form>


<?php require_once "partials/footer.php"; ?>

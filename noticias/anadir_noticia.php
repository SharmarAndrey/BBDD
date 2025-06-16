<?php
require_once "conexion.php";

$tituloErr = $categoriaErr = $descripcionErr = $insertaErr = "";
$titulo = $categoria = $descripcion = $insertaSuccess = "";
$user_id = 3;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST["titulo"]);
    $categoria = trim($_POST["categoria"]);
    $descripcion = trim($_POST["descripcion"]);

    if (!$titulo) {
        $tituloErr = "Tienes que introducir un título";
    }
    if (!$categoria) {
        $categoriaErr = "Tienes que introducir una categoría";
    }
    if (!$descripcion) {
        $descripcionErr = "Tienes que introducir una descripción";
    }

    // Manejo de imagen
    $imagen = "img/default.jpg"; // valor por defecto
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
        $uploadDir = "img/";
        $fileName = basename($_FILES["file"]["name"]);
        $imagePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $imagePath)) {
            $imagen = $imagePath;
        } else {
            $insertaErr = "❌ Error subiendo la imagen.";
        }
    }

    if (!$tituloErr && !$categoriaErr && !$descripcionErr && !$insertaErr) {
        try {
            $stmt = $pdo->prepare("INSERT INTO noticias (titulo, descripcion, categoria, user_id, imagen) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $descripcion, $categoria, $user_id, $imagen]);
            $insertaSuccess = "✅ Noticia insertada con éxito";
            $titulo = $categoria = $descripcion = "";
        } catch (Exception $e) {
            $insertaErr = "❌ Error: " . $e->getMessage();
        }
    }
}

require_once "partials/header.php";
?>

<main>
<div id="main">
    <h1 class="success"><?= $insertaSuccess ?></h1>
    <h1 class="error"><?= $insertaErr ?></h1>
    <h1>Añadir Noticia</h1>
    <form method="POST" enctype="multipart/form-data">
        <label>Título:</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($titulo) ?>">
        <span class="error"><?= $tituloErr ?></span>

        <label>Categoría:</label>
        <select name="categoria">
            <option value="">-- Selecciona --</option>
            <option value="Noticias" <?= $categoria == "Noticias" ? 'selected' : '' ?>>Noticias</option>
            <option value="Deportes" <?= $categoria == "Deportes" ? 'selected' : '' ?>>Deportes</option>
        </select>
        <span class="error"><?= $categoriaErr ?></span>

        <label>Descripción:</label>
        <textarea name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>
        <span class="error"><?= $descripcionErr ?></span>

        <label>Imagen:</label>
        <input type="file" name="file">

        <input type="submit" value="Enviar">
    </form>
    <a href="index.php" class="button">← Volver</a>
</div>
</main>
<?php require_once "partials/footer.php"; ?>

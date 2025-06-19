<?php
require_once "conexion.php";
require_once "partials/header.php";

// Get all categories
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();

$titulo = $descripcion = $nuevaCategoria = "";
$categoria_id = "";
$user_id = $_POST['user_id'] ?? 3;
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria_id = $_POST['categoria_id'];
    $nuevaCategoria = trim($_POST['nueva_categoria']);
    $descripcion = trim($_POST['descripcion']);
    $imagen = "img/default.jpg";

    // If the new category of news — create it and receive ID
    if ($nuevaCategoria) {
        $stmt = $pdo->prepare("SELECT id FROM categorias WHERE nombre = ?");
        $stmt->execute([$nuevaCategoria]);
        $categoria_id = $stmt->fetchColumn();
        if (!$categoria_id) {
            $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)");
            $stmt->execute([$nuevaCategoria]);
            $categoria_id = $pdo->lastInsertId();
        }
    }

    if (!$titulo || !$categoria_id || !$descripcion) {
        $error = "❌ Todos los campos son obligatorios";
    } else {
        // Loading the image
        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $uploadDir = "img/";
            $ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $fileName = uniqid() . '.' . $ext;
                $path = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {
                    $imagen = $path;
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, descripcion, categoria_id, user_id, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $categoria_id, $user_id, $imagen]);

        $success = "✅ Noticia añadida";
        $titulo = $descripcion = $nuevaCategoria = "";
        $categoria_id = "";
    }
}
?>

<h1>Añadir Noticia</h1>
<p class="success"><?= $success ?></p>
<p class="error"><?= $error ?></p>

<form method="POST" enctype="multipart/form-data">
    <label>Título:</label>
    <input type="text" name="titulo" value="<?= htmlspecialchars($titulo) ?>">

    <label>Categoría existente:</label>
    <select name="categoria_id">
        <option value="">-- Selecciona existente --</option>
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $categoria_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>O nueva categoría:</label>
    <input type="text" name="nueva_categoria" value="<?= htmlspecialchars($nuevaCategoria) ?>">

    <label>Descripción:</label>
    <textarea name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>

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

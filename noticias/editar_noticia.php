<?php
require_once "conexion.php";

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID faltante");
}

$stmt = $pdo->prepare("
    SELECT n.*, c.nombre AS categoria 
    FROM noticias n 
    JOIN categorias c ON n.categoria_id = c.id 
    WHERE n.id = ?
");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$noticia) {
    die("Noticia no encontrada");
}

$titulo = $noticia['titulo'];
$categoria_id = $noticia['categoria_id'];
$descripcion = $noticia['descripcion'];
$success = $error = "";

// All categories
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria_id = $_POST['categoria_id'];
    $nuevaCategoria = trim($_POST['nueva_categoria']);
    $descripcion = trim($_POST['descripcion']);

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

    if ($titulo && $categoria_id && $descripcion) {
        $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, descripcion=?, categoria_id=? WHERE id=?");
        $stmt->execute([$titulo, $descripcion, $categoria_id, $id]);

        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $uploadDir = "img/";
            $ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                $fileName = uniqid() . '.' . $ext;
                $path = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {
                    if ($noticia['imagen'] !== "img/default.jpg" && file_exists($noticia['imagen'])) {
                        unlink($noticia['imagen']);
                    }
                    $stmt = $pdo->prepare("UPDATE noticias SET imagen=? WHERE id=?");
                    $stmt->execute([$path, $id]);
                }
            }
        }

        $success = "✅ Noticia actualizada";
    } else {
        $error = "❌ Todos los campos son obligatorios";
    }
}

require_once "partials/header.php";
?>

<h1>Editar Noticia</h1>
<p class="success"><?= $success ?></p>
<p class="error"><?= $error ?></p>

<form method="POST" enctype="multipart/form-data">
    <label>Título:</label>
    <input type="text" name="titulo" value="<?= htmlspecialchars($titulo) ?>">

    <label>Categoría existente:</label>
    <select name="categoria_id">
        <?php foreach ($categorias as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $categoria_id ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>O nueva categoría:</label>
    <input type="text" name="nueva_categoria">

    <label>Descripción:</label>
    <textarea name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>

    <label>Nueva Imagen:</label>
    <input type="file" name="file">

    <button type="submit" class="btn">Actualizar</button>
    <a href="index.php" class="btn">← Volver</a>
</form>

<?php require_once "partials/footer.php"; ?>

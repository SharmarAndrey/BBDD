<?php
session_start();
require_once "conexion.php";
require_once "partials/header.php";

// Получаем ID текущего пользователя
$userName = $_SESSION['user'] ?? null;
$stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ?");
$stmtUser->execute([$userName]);
$user_id = $stmtUser->fetchColumn();

// Получаем список категорий
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();

$titulo = $descripcion = $nuevaCategoria = "";
$categoria_id = "";
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria_id = $_POST['categoria_id'];
    $nuevaCategoria = trim($_POST['nueva_categoria']);
    $descripcion = trim($_POST['descripcion']);
    $imagen = "img/default.jpg";

    // Nueva categoría
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
        // Subida de imagen
        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $uploadDir = "img/";
            $ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'avif', 'webp', 'svg'])) {
                $fileName = uniqid() . '.' . $ext;
                $path = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {
                    $imagen = $path;
                }
            }
        }

        // Guardar en base de datos
        $stmt = $pdo->prepare("INSERT INTO noticias (titulo, descripcion, categoria_id, user_id, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titulo, $descripcion, $categoria_id, $user_id, $imagen]);

        $success = "✅ Noticia añadida correctamente";
        $titulo = $descripcion = $nuevaCategoria = "";
        $categoria_id = "";
    }
}
?>

<h1>Añadir Noticia</h1>

<?php if ($success): ?>
  <p class="success"><?= $success ?></p>
<?php endif; ?>

<?php if ($error): ?>
  <p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
  <label for="titulo">Título:</label>
  <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>">

  <label for="categoria_id">Categoría existente:</label>
  <select name="categoria_id" id="categoria_id">
    <option value="">-- Selecciona existente --</option>
    <?php foreach ($categorias as $cat): ?>
      <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $categoria_id ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['nombre']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="nueva_categoria">O nueva categoría:</label>
  <input type="text" name="nueva_categoria" id="nueva_categoria" value="<?= htmlspecialchars($nuevaCategoria) ?>">

  <label for="descripcion">Descripción:</label>
  <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>

  <label for="file">Imagen:</label>
  <input type="file" name="file" id="file">

  <label for="usuario">Usuario:</label>
  <input type="text" value="<?= htmlspecialchars($userName) ?>" disabled>
  <input type="hidden" name="user_id" value="<?= $user_id ?>">

  <button type="submit" class="btn btn-success">Añadir</button>
  <a href="index.php" class="btn">← Volver</a>
</form>

<?php require_once "partials/footer.php"; ?>

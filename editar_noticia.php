<?php
// Inicia la sesión para acceder al usuario actual
session_start();
require_once "conexion.php";

// Validar que se recibió un ID por GET
$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID faltante");
}

// Obtener ID del usuario actual logueado
$userName = $_SESSION['user'] ?? null;
$stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ?");
$stmtUser->execute([$userName]);
$currentUserId = $stmtUser->fetchColumn();

// Buscar la noticia por ID
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

// Validaciones: noticia existe y pertenece al usuario actual
if (!$noticia) {
    die("Noticia no encontrada");
}
if ($noticia['user_id'] != $currentUserId) {
    die("⛔ No tienes permiso para editar esta noticia.");
}

// Preparar datos para el formulario
$titulo = $noticia['titulo'];
$categoria_id = $noticia['categoria_id'];
$descripcion = $noticia['descripcion'];
$success = $error = "";

// Obtener todas las categorías para el select
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
$categorias = $stmt->fetchAll();

// Procesar formulario al enviar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria_id = $_POST['categoria_id'];
    $nuevaCategoria = trim($_POST['nueva_categoria']);
    $descripcion = trim($_POST['descripcion']);

    // Si se proporciona nueva categoría, buscar o insertar
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

    // Validar campos y actualizar noticia
    if ($titulo && $categoria_id && $descripcion) {
        $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, descripcion=?, categoria_id=? WHERE id=?");
        $stmt->execute([$titulo, $descripcion, $categoria_id, $id]);

        // Procesar imagen si se subió
        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == UPLOAD_ERR_OK) {
            $uploadDir = "img/";
            $ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'avif', 'webp', 'svg'])) {
                $fileName = uniqid() . '.' . $ext;
                $path = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $path)) {

                    // Eliminar imagen anterior si no es la por defecto
                    if ($noticia['imagen'] !== "img/default.jpg" && file_exists($noticia['imagen'])) {
                        unlink($noticia['imagen']);
                    }

                    // Guardar nueva ruta de imagen
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
<!-- Mostrar mensajes de error o éxito -->
<?php if ($success): ?>
  <p class="success"><?= $success ?></p>
<?php endif; ?>

<?php if ($error): ?>
  <p class="error"><?= $error ?></p>
<?php endif; ?>

<!-- Formulario para editar noticia -->
<form method="POST" enctype="multipart/form-data">
  <label for="titulo">Título:</label>
  <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>">

  <label for="categoria_id">Categoría existente:</label>
  <select name="categoria_id" id="categoria_id">

  <!-- Seleccionar categoria existentes -->
    <?php foreach ($categorias as $cat): ?>
      <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $categoria_id ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['nombre']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <!-- Seleccionar categoria nueva -->
  <label for="nueva_categoria">O nueva categoría:</label>
  <input type="text" name="nueva_categoria" id="nueva_categoria">

  <!-- Introducir descripcion -->
  <label for="descripcion">Descripción:</label>
  <textarea name="descripcion" id="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>

  <!-- Introducir imagen -->
  <label for="file">Nueva Imagen:</label>
  <input type="file" name="file" id="file">

  <!-- Botones para actualizar y volver -->
  <button type="submit" class="btn btn-success">Actualizar</button>
  <a href="index.php" class="btn">← Volver</a>
</form>

<!-- Pie de página -->
<?php require_once "partials/footer.php"; ?>

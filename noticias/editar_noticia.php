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
$user_id = $noticia['user_id'];
$updateSuccess = $updateErr = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $categoria = trim($_POST['categoria']);
    $descripcion = trim($_POST['descripcion']);

    if ($titulo && $categoria && $descripcion) {
        try {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, descripcion=?, categoria=? WHERE id=?");
            $stmt->execute([$titulo, $descripcion, $categoria, $id]);
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

    <form method="POST">
        <div class="form-group">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo) ?>">
        </div>

        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <select id="categoria" name="categoria">
                <option value="Noticias" <?= $categoria == "Noticias" ? 'selected' : '' ?>>Noticias</option>
                <option value="Deportes" <?= $categoria == "Deportes" ? 'selected' : '' ?>>Deportes</option>
            </select>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($descripcion) ?></textarea>
        </div>

        <input type="submit" value="Actualizar">
        <a href="index.php" class="button">← Volver</a>
    </form>
</div>
</main>

<?php require_once "partials/footer.php"; ?>

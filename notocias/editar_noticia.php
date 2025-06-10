<?php
require_once "conexion.php";

$tituloErr = $categoriaErr = $descripcionErr = $updateErr = "";
$titulo = $categoria = $descripcion = $updateSuccess = "";
$id = $_GET['id'] ?? null;

if (!$id) {
    die("❌ ID de noticia no proporcionado.");
}

// Obtener datos actuales de la noticia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    die("❌ Noticia no encontrada.");
}

$titulo = $noticia['titulo'];
$categoria = $noticia['categoria'];
$descripcion = $noticia['descripcion'];
$user_id = $noticia['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = trim($_POST["titulo"]);
    $categoria = trim($_POST["categoria"]);
    $descripcion = trim($_POST["descripcion"]);

    if (empty($titulo)) {
        $tituloErr = "Tienes que introducir un título";
    }
    if (empty($categoria)) {
        $categoriaErr = "Tienes que introducir una categoría";
    }
    if (empty($descripcion)) {
        $descripcionErr = "Tienes que introducir una descripción";
    }

    if (!$tituloErr && !$categoriaErr && !$descripcionErr) {
        try {
            $stmt = $pdo->prepare("UPDATE noticias SET titulo = ?, descripcion = ?, categoria = ? WHERE id = ?");
            $stmt->execute([$titulo, $descripcion, $categoria, $id]);
            $updateSuccess = "✅ Noticia actualizada con éxito";
        } catch (Exception $e) {
            $updateErr = "❌ Error al actualizar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar noticia</title>
    <style>
          body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f9f9f9;
            min-height: 100vh;
            margin: 0;
        }

        #main {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .error {
            color: darkred;
            font-size: 13px;
            margin-top: 3px;
            display: block;
        }

        .success {
            color: green;
            text-align: center;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        a.button {
            display: inline-block;
            margin-top: 15px;
            text-align: center;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }

        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div id="main">
    <h1 class="success"><?= $updateSuccess ?></h1>
    <h1 class="error"><?= $updateErr ?></h1>
    <h1>EDITAR NOTICIA</h1>

    <form action="editar_noticia.php?id=<?= $id ?>" method="POST">

        <label for="titulo">Título de la noticia:</label>
        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>">
        <?php if (!empty($tituloErr)): ?><span class="error">* <?= $tituloErr ?></span><?php endif; ?>

        <label for="user_id">Autor:</label>
        <input type="number" name="user_id" id="user_id" value="<?= htmlspecialchars($user_id) ?>" readonly>

        <label for="categoria">Categoría:</label>
        <select name="categoria" id="categoria">
            <option value="">-- Selecciona una categoría --</option>
            <option value="Noticias" <?= $categoria == "Noticias" ? 'selected' : '' ?>>Noticias</option>
            <option value="Curiosidades" <?= $categoria == "Curiosidades" ? 'selected' : '' ?>>Curiosidades</option>
            <option value="Deportes" <?= $categoria == "Deportes" ? 'selected' : '' ?>>Deportes</option>
        </select>
        <?php if (!empty($categoriaErr)): ?><span class="error">* <?= $categoriaErr ?></span><?php endif; ?>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" rows="5"><?= htmlspecialchars($descripcion) ?></textarea>
        <?php if (!empty($descripcionErr)): ?><span class="error">* <?= $descripcionErr ?></span><?php endif; ?>

        <input type="submit" value="Actualizar">
    </form>

    <a href="index.php" class="button">← Volver a las noticias</a>
</div>

</body>
</html>

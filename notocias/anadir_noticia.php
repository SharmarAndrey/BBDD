<?php
require_once "conexion.php";

$tituloErr = $user_idErr = $categoriaErr = $descripcionErr = $insertaErr = "";
$titulo = $user_id = $categoria = $descripcion = $insertaSuccess = "";
$otra = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST["titulo"])) {
        $tituloErr = "Tienes que introducir un nombre";
    } else {
        $titulo = test_input($_POST["titulo"]);
    }

    if (empty($_POST["user_id"])) {
        $user_idErr = "Tienes que introducir un user_id";
    } else {
        $user_id = test_input($_POST["user_id"]);
    }

    if (empty($_POST["categoria"])) {
        $categoriaErr = "Tienes que introducir una categoría";
    } else {
        $categoria = test_input($_POST["categoria"]);
    }

    if (empty($_POST["descripcion"])) {
        $descripcionErr = "Tienes que introducir algún texto";
    } else {
        $descripcion = test_input($_POST["descripcion"]);
    }


    if (empty($tituloErr) && empty($user_idErr) && empty($categoriaErr) && empty($descripcionErr)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO noticias (titulo, descripcion, categoria, user_id) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$titulo, $descripcion, $categoria, $user_id]);
            $insertaSuccess = "✅ Noticia insertada con éxito";
            $otra = "OTRA";
            $titulo = $user_id = $categoria = $descripcion = "";
        } catch (Exception $e) {
            $insertaErr = "❌ No se ha podido ingresar la noticia: " . $e->getMessage();
        }
    }
}

function test_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir noticia</title>
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
    <h1 class="success"><?= $insertaSuccess ?></h1>
    <h1 class="error"><?= $insertaErr ?></h1>
    <h1>AÑADIR <?= $otra ?> NOTICIA</h1>

    <form action="anadir_noticia.php" method="POST">

        <label for="titulo">Título de la noticia:</label>
        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($titulo) ?>">
        <?php if (!empty($tituloErr)): ?>
            <span class="error">* <?= $tituloErr ?></span>
        <?php endif; ?>

        <label for="user_id">Autor:</label>
        <input type="number" name="user_id" id="user_id" value="3" readonly>
        <?php if (!empty($user_idErr)): ?>
            <span class="error">* <?= $user_idErr ?></span>
        <?php endif; ?>

        <label for="categoria">Categoría:</label>
        <select name="categoria" id="categoria">
            <option value="">-- Selecciona una categoría --</option>
            <option value="Noticias" <?= $categoria == "Noticias" ? 'selected' : '' ?>>Noticias</option>
            <option value="Curiosidades" <?= $categoria == "Curiosidades" ? 'selected' : '' ?>>Curiosidades</option>
            <option value="Deportes" <?= $categoria == "Deportes" ? 'selected' : '' ?>>Deportes</option>
        </select>
        <?php if (!empty($categoriaErr)): ?>
            <span class="error">* <?= $categoriaErr ?></span>
        <?php endif; ?>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" rows="5"><?= htmlspecialchars($descripcion) ?></textarea>
        <?php if (!empty($descripcionErr)): ?>
            <span class="error">* <?= $descripcionErr ?></span>
        <?php endif; ?>

        <input type="submit" value="Enviar">
    </form>

    <a href="index.php" class="button">← Volver a las noticias</a>
</div>

</body>
</html>

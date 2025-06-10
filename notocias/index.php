<?php
require_once "conexion.php";

$stmt = $pdo->query("SELECT * FROM noticias ORDER BY fecha DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de noticias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-color: #f4f4f4;
        }

        a.button {
            display: inline-block;
            padding: 10px 15px;
            background: #4CAF50;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .noticia {
            background-color: white;
            margin-bottom: 20px;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .noticia h2 {
            margin: 0 0 5px 0;
        }

        .noticia small {
            color: #777;
        }

        .acciones {
            margin-top: 10px;
        }

        .acciones a {
            display: inline-block;
            margin-right: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        .acciones a:hover {
            text-decoration: underline;
        }

        .ver {
            color: #0077cc;
        }

        .editar {
            color: #e67e22;
        }

        .eliminar {
            color: red;
        }
    </style>
</head>
<body>

<a href="anadir_noticia.php" class="button">➕ Añadir Noticia</a>

<h1>Últimas noticias</h1>

<?php foreach ($noticias as $noticia): ?>
    <div class="noticia">
        <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
        <small>Categoría: <?= htmlspecialchars($noticia['categoria']) ?> | Fecha: <?= $noticia['fecha'] ?></small>

        <div class="acciones">
            <a href="noticia.php?id=<?= $noticia['id'] ?>" class="ver">🔎 Ver más</a>
            <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="editar">✏ Editar</a>
            <a href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" class="eliminar" onclick="return confirm('¿Estás seguro de que quieres eliminar esta noticia?');">🗑 Eliminar</a>
        </div>
    </div>
<?php endforeach; ?>

</body>
</html>

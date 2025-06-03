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
       }
       .noticia {
           margin-bottom: 20px;
           border-bottom: 1px solid #ccc;
           padding-bottom: 10px;
       }
       .noticia h2 {
           margin: 0;
       }
       .noticia small {
           color: #777;
       }
       .noticia a {
           display: inline-block;
           margin-top: 5px;
           text-decoration: none;
           color: #0077cc;
       }
   </style>
</head>
<body>
   <h1>Últimas noticias</h1>
   <?php foreach ($noticias as $noticia): ?>
       <div class="noticia">
           <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
           <small>Categoría: <?= htmlspecialchars($noticia['categoria']) ?> | Fecha: <?= $noticia['fecha'] ?></small><br>
           <a href="noticia.php?id=<?= $noticia['id'] ?>">Ver más</a>
       </div>
   <?php endforeach; ?>
</body>
</html>

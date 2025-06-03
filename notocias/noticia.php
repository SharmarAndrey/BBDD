
<?php
require_once "conexion.php";
$id = $_GET['id'] ?? null;
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = :id");
$stmt->execute(['id' => $id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <title><?php echo htmlspecialchars($noticia['titulo'] ?? 'Noticia no encontrada'); ?></title>
   <style>
       .tarjeta {
           max-width: 600px;
           margin: 40px auto;
           padding: 20px;
           border-radius: 10px;
           box-shadow: 0 0 10px #ccc;
           font-family: sans-serif;
       }
   </style>
</head>
<body>
   <div class="tarjeta">
       <?php if ($noticia): ?>
           <h1><?php echo htmlspecialchars($noticia['titulo']); ?></h1>
           <small>Categoría: <?php echo htmlspecialchars($noticia['categoria']); ?> - Fecha: <?php echo $noticia['fecha']; ?> </small>
           <p><?php echo nl2br(htmlspecialchars($noticia['descripcion'])); ?></p>
		    <small></small>Publicado por: </small>
		   <p><?php echo nl2br(htmlspecialchars($noticia['user_id'])); ?></p>
       <?php else: ?>
           <p>❌ Noticia no encontrada.</p>
       <?php endif; ?>
       <a href="index.php">← Volver</a>
   </div>
</body>
</html>

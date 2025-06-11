<?php
require_once "conexion.php";
require_once "partials/header.php";
$stmt = $pdo->query("SELECT * FROM noticias ORDER BY fecha DESC");
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<main><div class="contenedor-principal">


<h1>Últimas noticias</h1>

<?php foreach ($noticias as $noticia): ?>
    <div class="noticia">
        <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
        <small>Categoría: <?= htmlspecialchars($noticia['categoria']) ?> | Fecha: <?= $noticia['fecha'] ?></small>
        <div class="acciones">
            <a href="noticia.php?id=<?= $noticia['id'] ?>" class="ver">🔎 Ver más</a>
            <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="editar">✏ Editar</a>
            <a href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" class="eliminar" onclick="return confirm('¿Estás seguro?');">🗑 Eliminar</a>
        </div>
    </div>
<?php endforeach; ?>
<a href="anadir_noticia.php" class="button">➕ Añadir Noticia</a>
</div>

</main>
<?php require_once "partials/footer.php"; ?>

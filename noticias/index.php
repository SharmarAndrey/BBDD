<?php
require_once "conexion.php";
require_once "partials/header.php";

$num = 5;
$comienzo = isset($_GET['comienzo']) ? (int)$_GET['comienzo'] : 0;
$orden = $_GET['orden'] ?? 'fecha';
$ordenValido = in_array($orden, ['fecha', 'categoria', 'titulo']) ? $orden : 'fecha';
$categoria = $_GET['categoria'] ?? null;

if ($categoria) {
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM noticias WHERE categoria = ?");
    $stmtTotal->execute([$categoria]);
    $total = $stmtTotal->fetchColumn();
} else {
    $total = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
}

if ($categoria) {
    $stmt = $pdo->prepare("SELECT * FROM noticias WHERE categoria = ? ORDER BY $ordenValido DESC LIMIT ?, ?");
    $stmt->bindValue(1, $categoria);
    $stmt->bindValue(2, $comienzo, PDO::PARAM_INT);
    $stmt->bindValue(3, $num, PDO::PARAM_INT);
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("SELECT * FROM noticias ORDER BY $ordenValido DESC LIMIT ?, ?");
    $stmt->bindValue(1, $comienzo, PDO::PARAM_INT);
    $stmt->bindValue(2, $num, PDO::PARAM_INT);
    $stmt->execute();
}
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Últimas noticias</h1>

<div class="sort-links">
    <span>Ordenar por:</span>
    <a href="?<?= $categoria ? "categoria=$categoria&" : "" ?>orden=fecha">Fecha</a> |
    <a href="?<?= $categoria ? "categoria=$categoria&" : "" ?>orden=categoria">Categoría</a> |
    <a href="?<?= $categoria ? "categoria=$categoria&" : "" ?>orden=titulo">Título</a>
</div>

<?php if ($categoria): ?>
    <h2 class="filter-info">Filtrando por: <?= htmlspecialchars($categoria) ?></h2>
<?php endif; ?>

<div class="cards">
    <?php foreach ($noticias as $noticia): ?>
        <div class="card">
            <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
            <p class="meta">
                Categoría: 
                <a href="index.php?categoria=<?= urlencode($noticia['categoria']) ?>">
                    <?= htmlspecialchars($noticia['categoria']) ?>
                </a> | 
                Fecha: <?= htmlspecialchars($noticia['fecha']) ?>
            </p>
            <div class="actions">
                <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn">Ver más</a>
                <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-secondary btn-small">Editar</a>
                <a href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('¿Seguro que deseas eliminar esta noticia?');">Eliminar</a>
            </div>
        </div>
    <?php endforeach; ?>

    <a href="anadir_noticia.php" class="add-card">+ Añadir Noticia</a>
</div>

<div class="pagination">
    <?php if ($comienzo > 0): ?>
        <a href="?<?= $categoria ? "categoria=$categoria&" : "" ?>orden=<?= $orden ?>&comienzo=<?= max(0, $comienzo - $num) ?>" class="btn">← Anterior</a>
    <?php endif; ?>
    <?php if ($comienzo + $num < $total): ?>
        <a href="?<?= $categoria ? "categoria=$categoria&" : "" ?>orden=<?= $orden ?>&comienzo=<?= $comienzo + $num ?>" class="btn">Siguiente →</a>
    <?php endif; ?>
</div>

<?php require_once "partials/footer.php"; ?>

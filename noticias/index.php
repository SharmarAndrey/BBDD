<?php
require_once "conexion.php";
require_once "partials/header.php";

// Number per page
$num = 5;
$comienzo = isset($_GET['comienzo']) ? (int)$_GET['comienzo'] : 0;
$orden = $_GET['orden'] ?? 'fecha';
$ordenValido = in_array($orden, ['fecha', 'categoria', 'titulo']) ? $orden : 'fecha';
$categoria = $_GET['categoria'] ?? null;

// We get the total amount
if ($categoria) {
    $stmtTotal = $pdo->prepare("
        SELECT COUNT(*)
        FROM noticias n
        LEFT JOIN categorias c ON n.categoria_id = c.id
        WHERE c.nombre = ?
    ");
    $stmtTotal->execute([$categoria]);
    $total = $stmtTotal->fetchColumn();
} else {
    $total = $pdo->query("SELECT COUNT(*) FROM noticias")->fetchColumn();
}

// Prepare WHERE and parameters
$where = "";
$params = [];
if ($categoria) {
    $where = "WHERE c.nombre = ?";
    $params[] = $categoria;
}

// We prepare a request from Join
$sql = "
    SELECT n.*, c.nombre AS categoria
    FROM noticias n
    LEFT JOIN categorias c ON n.categoria_id = c.id
    $where
    ORDER BY $ordenValido DESC
    LIMIT ?, ?
";

$stmt = $pdo->prepare($sql);

// We tie the parameters
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}
$stmt->bindValue(count($params) + 1, $comienzo, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $num, PDO::PARAM_INT);

$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Últimas noticias</h1>

<div class="sort-links">
    <span>Ordenar por:</span>
    <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=fecha">Fecha</a> |
    <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=categoria">Categoría</a> |
    <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=titulo">Título</a>
</div>

<?php if ($categoria): ?>
    <h2 class="filter-info">Filtrando por: <?= htmlspecialchars($categoria) ?></h2>
<?php endif; ?>

<div class="cards">
    <?php if ($noticias): ?>
        <?php foreach ($noticias as $noticia): ?>
            <div class="card">
                <h2><?= htmlspecialchars($noticia['titulo']) ?></h2>
                <p class="meta">
                    Categoría:
                    <a href="?categoria=<?= urlencode($noticia['categoria']) ?>">
                        <?= htmlspecialchars($noticia['categoria']) ?>
                    </a> |
                    Fecha: <?= htmlspecialchars($noticia['fecha']) ?>
                </p>

                <?php if ($noticia['imagen'] && $noticia['imagen'] !== 'img/default.jpg'): ?>
                    <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen" style="max-width: 250px;">
                <?php endif; ?>

                <div class="actions">
                    <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn">Ver más</a>
                    <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-secondary btn-small">Editar</a>
                    <a href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('¿Seguro que deseas eliminar?');">Eliminar</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay noticias aún.</p>
    <?php endif; ?>

    <a href="anadir_noticia.php" class="add-card">+ Añadir Noticia</a>
</div>

<div class="pagination">
    <?php if ($comienzo > 0): ?>
        <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=<?= $orden ?>&comienzo=<?= max(0, $comienzo - $num) ?>" class="btn">← Anterior</a>
    <?php endif; ?>
    <?php if ($comienzo + $num < $total): ?>
        <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=<?= $orden ?>&comienzo=<?= $comienzo + $num ?>" class="btn">Siguiente →</a>
    <?php endif; ?>
</div>

<?php require_once "partials/footer.php"; ?>

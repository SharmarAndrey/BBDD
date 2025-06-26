<?php
// Iniciar sesión si aún no está iniciada
session_start();

// Conexión a la base de datos y encabezado HTML
require_once "conexion.php";
require_once "partials/header.php";

// Obtener el nombre del usuario desde la sesión
$userName = $_SESSION['user'] ?? null;
$currentUserId = null;

// Si hay usuario en sesión, obtenemos su ID
if ($userName) {
    $stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE nombre = ?");
    $stmtUser->execute([$userName]);
    $currentUserId = $stmtUser->fetchColumn();
}

// Paginación

// Número de noticias por página
$num = 5;
// Inicio
$comienzo = isset($_GET['comienzo']) ? (int)$_GET['comienzo'] : 0;
// Orden por defecto: fecha
$orden = $_GET['orden'] ?? 'fecha';
$ordenValido = in_array($orden, ['fecha', 'categoria', 'titulo']) ? $orden : 'fecha';
// Filtro por categoría
$categoria = $_GET['categoria'] ?? null;

// Construir cláusula WHERE si se ha filtrado por categoría
$where = "";
$params = [];
if ($categoria) {
    $where = "WHERE c.nombre = ?";
    $params[] = $categoria;
}

// Consulta SQL para obtener noticias con JOIN para obtener nombre de categoría
$sql = "
    SELECT n.*, c.nombre AS categoria
    FROM noticias n
    LEFT JOIN categorias c ON n.categoria_id = c.id
    $where
    ORDER BY $ordenValido DESC
    LIMIT ?, ?
";

// Preparar y ejecutar consulta
$stmt = $pdo->prepare($sql);
foreach ($params as $i => $param) {
    $stmt->bindValue($i + 1, $param);
}
$stmt->bindValue(count($params) + 1, $comienzo, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $num, PDO::PARAM_INT);
$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener el total de noticias para la paginación
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
?>

<main class="container">
  <h1>📰 Últimas noticias</h1>

    <!-- Enlaces para ordenar las noticias -->
  <div class="sort-links">
    <span>Ordenar por:</span>
    <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=fecha">Fecha</a>
    <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=categoria">Categoría</a>
    <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=titulo">Título</a>
  </div>

    <!-- Mensaje si se está filtrando por una categoría -->
  <?php if ($categoria): ?>
    <h2 class="filter-info">🔍 Filtrando por: <?= htmlspecialchars($categoria) ?></h2>
  <?php endif; ?>


   <!-- Mostrar las noticias -->
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

		   <!-- Mostrar imagen si existe -->
          <?php if ($noticia['imagen'] && $noticia['imagen'] !== 'img/default.jpg'): ?>
            <img src="<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen">
          <?php endif; ?>

		   <!-- Acciones disponibles (ver, editar, eliminar) -->
          <div class="actions">
            <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn">Ver más</a>
            <?php if ($currentUserId && $noticia['user_id'] == $currentUserId): ?>
              <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-secondary btn-small">Editar</a>
              <a href="eliminar_noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-danger btn-small" onclick="return confirm('¿Seguro que deseas eliminar?');">Eliminar</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No hay noticias aún.</p>
    <?php endif; ?>

	 <!-- Botón para añadir noticia si hay usuario autenticado -->
    <?php if ($currentUserId): ?>
      <a href="anadir_noticia.php" class="add-card">+ Añadir Noticia</a>
    <?php endif; ?>
  </div>

  <!-- Paginación -->
  <div class="pagination">
	<!-- Botones para navegar entre páginas -->
    <?php if ($comienzo > 0): ?>
      <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=<?= $orden ?>&comienzo=<?= max(0, $comienzo - $num) ?>" class="btn">← Anterior</a>
    <?php endif; ?>
	<!-- Botón para la siguiente página -->
    <?php if ($comienzo + $num < $total): ?>
      <a href="?<?= $categoria ? "categoria=" . urlencode($categoria) . "&" : "" ?>orden=<?= $orden ?>&comienzo=<?= $comienzo + $num ?>" class="btn">Siguiente →</a>
    <?php endif; ?>
  </div>
</main>

<!-- Pie de página -->
<?php require_once "partials/footer.php"; ?>

<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM productos WHERE activo = TRUE AND fecha_fin >= CURDATE() ORDER BY fecha_inicio DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(Exception $e) {
    $productos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="catalogo.php">Catálogo de Productos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link text-light">Hola, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Productos en Promoción</h1>
        
        <?php if(empty($productos)): ?>
            <div class="alert alert-info">
                No hay productos en promoción actualmente.
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($productos as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <div class="badge bg-danger mb-2">
                                    <?php 
                                    $descuento = round((($producto['precio_original'] - $producto['precio_promocion']) / $producto['precio_original']) * 100);
                                    echo $descuento . '% OFF';
                                    ?>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                <div class="mb-2">
                                    <span class="text-decoration-line-through text-muted">
                                        $<?php echo number_format($producto['precio_original'], 2); ?>
                                    </span>
                                    <span class="h4 text-success ms-2">
                                        $<?php echo number_format($producto['precio_promocion'], 2); ?>
                                    </span>
                                </div>
                                <small class="text-muted">
                                    Válido hasta: <?php echo date('d/m/Y', strtotime($producto['fecha_fin'])); ?>
                                </small>
                            </div>
                            <!-- Tarea 3 Corregida: Enlace funcional enviando el ID del producto -->
                            <div class="card-footer bg-white border-0">
                                <a href="detalle.php?id=<?php echo $producto['id']; ?>" class="btn btn-primary w-100">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
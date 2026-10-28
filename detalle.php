<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id_producto = $_GET['id'] ?? null;

if(!$id_producto) {
    header("Location: catalogo.php");
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM productos WHERE id = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id_producto);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$producto) {
        header("Location: catalogo.php");
        exit();
    }
} catch(Exception $e) {
    header("Location: catalogo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow-lg col-md-8 mx-auto">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0"><?php echo htmlspecialchars($producto['nombre']); ?></h3>
            </div>
            <div class="card-body">
                <p class="lead"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <hr>
                <p><strong>Precio Original:</strong> <span class="text-decoration-line-through">$<?php echo number_format($producto['precio_original'], 2); ?></span></p>
                <p><strong>Precio Promoción:</strong> <span class="h3 text-success">$<?php echo number_format($producto['precio_promocion'], 2); ?></span></p>
                <p><strong>Fecha Inicio:</strong> <?php echo date('d/m/Y', strtotime($producto['fecha_inicio'])); ?></p>
                <p><strong>Fecha Fin:</strong> <?php echo date('d/m/Y', strtotime($producto['fecha_fin'])); ?></p>
            </div>
            <div class="card-footer">
                <a href="catalogo.php" class="btn btn-secondary">Volver al Catálogo</a>
            </div>
        </div>
    </div>
</body>
</html>
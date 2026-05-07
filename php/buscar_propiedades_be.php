<?php
include 'conexion_be.php';

$where = ["estado = 'Disponible'"];
$params = [];

// Búsqueda general
if (!empty($_GET['q'])) {
    $where[] = "(titulo ILIKE :q OR zona ILIKE :q)";
    $params[':q'] = '%' . trim($_GET['q']) . '%';
}

// Filtros adicionales
if (!empty($_GET['zona'])) {
    $where[] = "zona ILIKE :zona";
    $params[':zona'] = '%' . trim($_GET['zona']) . '%';
}
if (!empty($_GET['tipo'])) {
    $where[] = "tipo = :tipo";
    $params[':tipo'] = $_GET['tipo'];
}
if (!empty($_GET['precio_min'])) {
    $where[] = "precio >= :min";
    $params[':min'] = (int)$_GET['precio_min'];
}
if (!empty($_GET['precio_max'])) {
    $where[] = "precio <= :max";
    $params[':max'] = (int)$_GET['precio_max'];
}

/* área mínima */
if (!empty($_GET['area_min'])) {
    $where[] = "area >= :area_min";
    $params[':area_min'] = (int)$_GET['area_min'];
}

/* área máxima */
if (!empty($_GET['area_max'])) {
    $where[] = "area <= :area_max";
    $params[':area_max'] = (int)$_GET['area_max'];
}


$sql = "SELECT * FROM propiedades WHERE " . implode(" AND ", $where) . " ORDER BY fecha_registro DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$props = $stmt;
?>

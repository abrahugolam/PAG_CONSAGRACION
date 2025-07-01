<?php
require 'conexion.php';

$q = $_GET['q'] ?? '';

if (!$q || strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT id, nombres, apellidos, localidad, escuela FROM misioneros WHERE nombres LIKE CONCAT('%', ?, '%') OR apellidos LIKE CONCAT('%', ?, '%') LIMIT 8");
$stmt->bind_param("ss", $q, $q);
$stmt->execute();
$res = $stmt->get_result();

$misioneros = [];
while ($row = $res->fetch_assoc()) {
    $misioneros[] = $row;
}
header('Content-Type: application/json');
echo json_encode($misioneros);

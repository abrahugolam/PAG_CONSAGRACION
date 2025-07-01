<?php
$host = "localhost";
$user = "lam";
$password = "L4m_T0Tu5_1607";
$db = "CONSAGRACION";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // errores claros

try {
    $conn = new mysqli($host, $user, $password, $db);
    $conn->set_charset('utf8mb4');                         // juego de caracteres
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());                           // registra el detalle
    die("No se pudo conectar a la base de datos.");
}
?>
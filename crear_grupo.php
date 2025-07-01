<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_grupo'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("INSERT INTO Grupos_Consagracion (nombre_grupo, descripcion) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $descripcion);
    $stmt->execute();
    $stmt->close();

    echo "Grupo creado correctamente.";
}
?>

<h2>Crear Grupo</h2>
<form method="POST">
    <label>Nombre del Grupo:</label><br>
    <input type="text" name="nombre_grupo" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion"></textarea><br><br>

    <input type="submit" value="Crear Grupo">
</form>

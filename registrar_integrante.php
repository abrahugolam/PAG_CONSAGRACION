<?php
require 'conexion.php';

$grupos = $conn->query("SELECT * FROM Grupos_Consagracion");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre_completo'];
    $grupo_id = $_POST['grupo_id'];

    $stmt = $conn->prepare("INSERT INTO Integrantes (nombre_completo, grupo_id) VALUES (?, ?)");
    $stmt->bind_param("si", $nombre, $grupo_id);
    $stmt->execute();
    $stmt->close();

    echo "Integrante registrado correctamente.";
}
?>

<h2>Registrar Integrante</h2>
<form method="POST">
    <label>Nombre completo:</label><br>
    <input type="text" name="nombre_completo" required><br><br>

    <label>Grupo:</label><br>
    <select name="grupo_id" required>
        <option value="">Selecciona un grupo</option>
        <?php while ($row = $grupos->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['nombre_grupo'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <input type="submit" value="Registrar Integrante">
</form>

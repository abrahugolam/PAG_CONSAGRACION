<?php
require 'conexion.php';

$grupos = $conn->query("SELECT * FROM Grupos_Consagracion");

if (isset($_POST['grupo_id'])) {
    $grupo_id = $_POST['grupo_id'];
    $integrantes = $conn->query("SELECT * FROM Integrantes WHERE grupo_id = $grupo_id");
}

if (isset($_POST['asistencias'])) {
    $fecha = date("Y-m-d");
    foreach ($_POST['asistencias'] as $id_integrante => $presente) {
        $stmt = $conn->prepare("INSERT INTO Asistencia (integrante_id, fecha, presente) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $id_integrante, $fecha, $presente);
        $stmt->execute();
    }
    echo "Asistencia registrada correctamente.";
}
?>

<h2>Tomar Asistencia</h2>

<form method="POST">
    <label>Selecciona un Grupo:</label>
    <select name="grupo_id" onchange="this.form.submit()">
        <option value="">-- Selecciona --</option>
        <?php while ($row = $grupos->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>" <?= isset($grupo_id) && $grupo_id == $row['id'] ? 'selected' : '' ?>>
                <?= $row['nombre_grupo'] ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<?php if (isset($integrantes) && $integrantes->num_rows > 0): ?>
    <form method="POST">
        <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">
        <table border="1">
            <tr><th>Nombre</th><th>Presente</th></tr>
            <?php while ($row = $integrantes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['nombre_completo'] ?></td>
                    <td><input type="checkbox" name="asistencias[<?= $row['id'] ?>]" value="1"></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <input type="submit" value="Guardar Asistencia">
    </form>
<?php endif; ?>

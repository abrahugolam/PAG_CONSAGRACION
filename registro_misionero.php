<?php
require 'conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Obtener roles de la tabla `roles`
$roles = $conn->query("SELECT id_rol, descripcion FROM roles");

// Obtener localidades de la tabla `localidades`
$localidades_result = $conn->query("SELECT id, nombre FROM localidades ORDER BY nombre");

// Insertar misionero y sus roles
$mensaje = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombres   = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $localidad_id = $_POST['localidad'];
    $escuela   = $_POST['escuela'];
    $rolesSel  = $_POST['roles'] ?? [];

    // Insertar misionero
    $stmt = $conn->prepare("INSERT INTO misioneros (nombres, apellidos, localidad, escuela) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $nombres, $apellidos, $localidad_id, $escuela);
    $stmt->execute();
    $misionero_id = $stmt->insert_id;
    $stmt->close();

    // Insertar roles seleccionados
    if (!empty($rolesSel)) {
        $stmtRoles = $conn->prepare("INSERT INTO misionero_roles (misionero_id, rol_id) VALUES (?, ?)");
        foreach ($rolesSel as $rol_id) {
            $stmtRoles->bind_param("ii", $misionero_id, $rol_id);
            $stmtRoles->execute();
        }
        $stmtRoles->close();
    }

    $mensaje = "Misionero registrado correctamente con roles asignados.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Misionero</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">Registro de Misionero</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-4">

        <!-- CONTENEDOR DATOS -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Datos del Misionero
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Localidad</label>
                        <select name="localidad" class="form-select" required>
                            <option value="">-- Selecciona --</option>
                            <?php while($loc = $localidades_result->fetch_assoc()): ?>
                                <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['nombre']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Escuela</label>
                        <select name="escuela" class="form-select" required>
                            <option value="">-- Selecciona --</option>
                            <option value="Lectio Divina">Lectio Divina</option>
                            <option value="San Lorenzo">San Lorenzo</option>
                            <option value="Escuela de Maria">Escuela de Maria</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR ACCESO -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    Roles de Acceso
                </div>
                <div class="card-body">
                    <p class="mb-2">Selecciona los roles que tendrá este misionero:</p>
                    <?php while ($rol = $roles->fetch_assoc()): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $rol['id_rol'] ?>" id="rol_<?= $rol['id_rol'] ?>">
                            <label class="form-check-label" for="rol_<?= $rol['id_rol'] ?>">
                                <?= htmlspecialchars($rol['descripcion']) ?>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- BOTÓN -->
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-success">Registrar Misionero</button>
        </div>
    </form>
</div>

<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

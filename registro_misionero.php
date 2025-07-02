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
    $documento = trim($_POST['documento']);
    $password  = $_POST['password'];
    $rolesSel  = $_POST['roles'] ?? [];

    // Validaciones básicas
    if (!$documento || !$password || empty($rolesSel)) {
        $mensaje = "<span class='text-danger'>Debe llenar Documento, Password y al menos un Rol.</span>";
    } else {
        // 1. Insertar en misioneros (incluye n_documento)
        $stmt = $conn->prepare("INSERT INTO misioneros (nombres, apellidos, localidad, escuela, n_documento) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $nombres, $apellidos, $localidad_id, $escuela, $documento);
        $stmt->execute();
        $misionero_id = $stmt->insert_id;
        $stmt->close();

        // 2. Insertar roles seleccionados en misionero_roles
        $stmtRoles = $conn->prepare("INSERT INTO misionero_roles (misionero_id, rol_id) VALUES (?, ?)");
        foreach ($rolesSel as $rol_id) {
            $stmtRoles->bind_param("ii", $misionero_id, $rol_id);
            $stmtRoles->execute();
        }
        $stmtRoles->close();

        // 3. Crear usuario
        $rol_usuario = intval($rolesSel[0]); // Toma el primer rol
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // El campo user es el documento, id_misionero es FK, rol_id FK a roles
        $stmtUser = $conn->prepare("INSERT INTO usuarios (user, password, rol_id, id_misionero, condicion) VALUES (?, ?, ?, ?, 'Activo')");
        $stmtUser->bind_param("ssii", $documento, $password_hash, $rol_usuario, $misionero_id);
        $stmtUser->execute();
        $stmtUser->close();

        $mensaje = "Misionero registrado correctamente con usuario de acceso.";
    }
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
 <?php session_start(); ?>

<!-- MUESTRA EL NOMBRE DEL USUARIO-->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
  <div class="container">
    <span class="navbar-text me-auto fw-bold">
      <?php
        echo "Bienvenido, " . htmlspecialchars($_SESSION['usuario_nombre'] ?? '') . " " . htmlspecialchars($_SESSION['usuario_apellidos'] ?? '');
        // Mostrar localidad si está en la sesión:
        if (!empty($_SESSION['usuario_localidad'])) {
          echo ' <span class="badge bg-primary ms-2">' . htmlspecialchars($_SESSION['usuario_localidad']) . '</span>';
        }
      ?>
    </span>



    <a href="logout.php" class="btn btn-outline-danger">Cerrar sesión <i class="bi bi-box-arrow-right"></i></a>
  </div>
</nav>

    

<div class="container py-5">
    <h2 class="text-center mb-4">Registro de Misionero</h2>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-4" autocomplete="off">

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
                    <div class="mb-3">
                        <label class="form-label">Documento (DNI o CE)</label>
                        <input type="text" name="documento" class="form-control" maxlength="20" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTENEDOR ACCESO -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    Acceso y Roles
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Password (para acceso al sistema)</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
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

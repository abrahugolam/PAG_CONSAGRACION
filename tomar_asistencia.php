<?php
require 'conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  // ayuda a depurar

// 1) Cargar grupos de la nueva tabla grupo_consagracion con detalles
$grupos = $conn->query("
    SELECT 
        gc.id,
        l.nombre AS localidad,
        gc.parroquia,
        gc.modalidad,
        gc.estado,
        gc.fecha_inicio,
        gc.dia,
        gc.hora,
        gc.cantidad_integrantes_inicio,
        (SELECT CONCAT(nombres, ' ', apellidos) FROM misioneros WHERE id = gc.encargado_id) AS nombre_encargado,
        (SELECT CONCAT(nombres, ' ', apellidos) FROM misioneros WHERE id = gc.apoyo1_id) AS nombre_apoyo1,
        (SELECT CONCAT(nombres, ' ', apellidos) FROM misioneros WHERE id = gc.apoyo2_id) AS nombre_apoyo2
    FROM grupo_consagracion gc
    LEFT JOIN localidades l ON gc.localidad_id = l.id
    ORDER BY l.nombre, gc.parroquia
");

// Guardar asistencia si corresponde
$mensaje_ok = null;
if (!empty($_POST['asistencias']) && !empty($_POST['grupo_id'])) {
    $grupo_id = $_POST['grupo_id'];
    $fecha = date("Y-m-d");
    $stmt  = $conn->prepare(
        "INSERT INTO Asistencia (integrante_id, fecha, presente) VALUES (?, ?, ?)"
    );
    foreach ($_POST['asistencias'] as $id_integrante => $presente) {
        $stmt->bind_param("isi", $id_integrante, $fecha, $presente);
        $stmt->execute();
    }
    $mensaje_ok = "Asistencia registrada correctamente.";
} else {
    $grupo_id = $_POST['grupo_id'] ?? null;
}

// SIEMPRE carga los integrantes si hay grupo seleccionado
$integrantes = [];
if ($grupo_id) {
    $stmt = $conn->prepare("SELECT * FROM Integrantes WHERE grupo_id = ?");
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $integrantes = $stmt->get_result();
}

// Para los tooltips
$grupos->data_seek(0);
$detalles_js = [];
while ($row = $grupos->fetch_assoc()) {
    $detalles_js[$row['id']] = [
        "localidad"  => $row['localidad'],
        "parroquia"  => $row['parroquia'] ?? 'Sin parroquia',
        "modalidad"  => $row['modalidad'],
        "estado"     => $row['estado'],
        "fecha_inicio" => $row['fecha_inicio'],
        "dia"        => $row['dia'],
        "hora"       => $row['hora'],
        "cantidad"   => $row['cantidad_integrantes_inicio'],
        "encargado"  => $row['nombre_encargado'] ?? 'No asignado',
        "apoyo1"     => $row['nombre_apoyo1'] ?? 'No asignado',
        "apoyo2"     => $row['nombre_apoyo2'] ?? 'No asignado'
    ];
}
$grupos->data_seek(0);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Tomar Asistencia</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .custom-tooltip {
            background: #3056d3;
            color: #fff;
            padding: 15px;
            border-radius: 10px;
            font-size: 1rem;
            max-width: 320px;
            box-shadow: 0 2px 18px 0 rgba(48,86,211,0.11);
            white-space: pre-line;
        }
        .custom-tooltip b { color: #b7e3ff;}
        .form-select option:hover { background: #dce8fc; }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="text-center mb-4">Tomar Asistencia</h2>

    <?php if ($mensaje_ok): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $mensaje_ok ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <!-- Selección de grupo -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="POST" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Selecciona un Grupo</label>
                    <select name="grupo_id" class="form-select" id="grupo_id_select" required onchange="this.form.submit()">
                        <option value="">-- Selecciona --</option>
                        <?php
                        $grupos->data_seek(0);
                        while ($row = $grupos->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"
                                <?= ($grupo_id == $row['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['localidad']) ?> - <?= htmlspecialchars($row['parroquia'] ?? 'Sin parroquia') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="grupo_detalle" class="custom-tooltip mt-2" style="display:none;"></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de integrantes y check de asistencia -->
    <?php if ($grupo_id && !empty($integrantes) && $integrantes->num_rows > 0): ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nombre</th>
                                    <th>F. Nacimiento</th>
                                    <th>Celular</th>
                                    <th>Correo</th>
                                    <th class="text-center">Presente</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $integrantes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($row['fecha_nacimiento'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['celular'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['correo'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   name="asistencias[<?= $row['id'] ?>]"
                                                   value="1">
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            Guardar Asistencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php elseif ($grupo_id): ?>
        <div class="alert alert-warning">Este grupo aún no tiene integrantes registrados.</div>
    <?php endif; ?>
</div>

<script>
const detallesGrupos = <?= json_encode($detalles_js) ?>;
document.addEventListener("DOMContentLoaded", function() {
    const grupoSelect = document.getElementById('grupo_id_select');
    const grupoDetalle = document.getElementById('grupo_detalle');
    function updateTooltip() {
        const gid = grupoSelect.value;
        if (gid && detallesGrupos[gid]) {
            const d = detallesGrupos[gid];
            grupoDetalle.innerHTML = `
                <div>
                    <div><b>Localidad:</b> ${d.localidad}</div>
                    <div><b>Parroquia:</b> ${d.parroquia}</div>
                    <div><b>Modalidad:</b> ${d.modalidad}</div>
                    <div><b>Estado:</b> ${d.estado}</div>
                    <div><b>Fecha de Inicio:</b> ${d.fecha_inicio}</div>
                    <div><b>Día:</b> ${d.dia}</div>
                    <div><b>Hora:</b> ${d.hora}</div>
                    <div><b>Integrantes Iniciales:</b> ${d.cantidad}</div>
                    <div><b>Encargado:</b> <span class="badge bg-primary"><i class="bi bi-person-badge"></i> ${d.encargado}</span></div>
                    <div><b>Apoyo 1:</b> <span class="badge bg-secondary"><i class="bi bi-person-check"></i> ${d.apoyo1}</span></div>
                    <div><b>Apoyo 2:</b> <span class="badge bg-info"><i class="bi bi-person-heart"></i> ${d.apoyo2}</span></div>
                </div>
            `;
            grupoDetalle.style.display = "block";
        } else {
            grupoDetalle.style.display = "none";
            grupoDetalle.innerHTML = "";
        }
    }
    grupoSelect.addEventListener('change', updateTooltip);
    // Si ya hay uno seleccionado al recargar
    if (grupoSelect.value) updateTooltip();
});
</script>
<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

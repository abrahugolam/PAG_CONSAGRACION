<?php
require 'conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);   // depuración clara

// 1) Obtener los grupos de consagración con detalle
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

// 2) Alta de integrante
$mensaje_ok = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST['nombre_completo']);
    $grupo_id = $_POST['grupo_id'];
    $fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : NULL;
    $celular = !empty($_POST['celular']) ? $_POST['celular'] : NULL;
    $correo = !empty($_POST['correo']) ? $_POST['correo'] : NULL;

    $stmt = $conn->prepare(
        "INSERT INTO Integrantes (nombre_completo, grupo_id, fecha_nacimiento, celular, correo) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sisss", $nombre, $grupo_id, $fecha_nacimiento, $celular, $correo);
    $stmt->execute();
    $stmt->close();

    $mensaje_ok = "Integrante registrado correctamente.";
}

// Para usar en JS
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
    <title>Registrar Integrante</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f6f8fc; }
        .custom-detail-card {
            border-radius: 14px;
            border-left: 5px solid #3056d3;
            background: #f4f8ff;
            margin-top: 14px;
            box-shadow: 0 2px 12px 0 rgba(48,86,211,0.10);
            padding: 1rem 1.2rem;
            font-size: 1.06rem;
        }
        .custom-detail-card b { color: #3056d3; }
        .badge {
            font-size: 1rem;
            vertical-align: middle;
            margin-left: 3px;
        }
        .badge.bg-primary { background: #3056d3!important;}
        .badge.bg-secondary { background: #7c88a1!important;}
        .badge.bg-info { color: #185074!important; background: #bae8fa!important;}
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="text-center mb-4">Registrar Integrante</h2>

    <?php if ($mensaje_ok): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $mensaje_ok ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="nombre_completo" class="form-control" placeholder="Juan Pérez" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Celular</label>
                    <input type="text" name="celular" class="form-control" maxlength="15" pattern="[0-9]+" title="Solo números">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Grupo</label>
                    <select name="grupo_id" class="form-select" required id="grupo_select">
                        <option value="">Selecciona un grupo...</option>
                        <?php
                        $grupos->data_seek(0);
                        while ($row = $grupos->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['localidad']) ?> - <?= htmlspecialchars($row['parroquia'] ?? 'Sin parroquia') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="grupo_detalle" class="custom-detail-card" style="display:none;"></div>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Registrar Integrante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const detallesGrupos = <?= json_encode($detalles_js) ?>;
document.addEventListener("DOMContentLoaded", function() {
    const grupoSelect = document.getElementById('grupo_select');
    const grupoDetalle = document.getElementById('grupo_detalle');
    grupoSelect.addEventListener('change', function() {
        const gid = this.value;
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
    });
});
</script>
<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
require 'conexion.php';

// Totales (ahora con grupo_consagracion)
$totalGrupos      = $conn->query("SELECT COUNT(*) FROM grupo_consagracion")->fetch_row()[0];
$totalIntegrantes = $conn->query("SELECT COUNT(*) FROM Integrantes")->fetch_row()[0];
$totalAsistencias = $conn->query("SELECT COUNT(*) FROM Asistencia")->fetch_row()[0];
$totalEncargados  = $conn->query("SELECT COUNT(*) FROM encargados_consagracion")->fetch_row()[0];

// Últimos 5 grupos de grupo_consagracion (usamos id DESC y localidad)
$ultimosGrupos = $conn->query("
    SELECT gc.id, l.nombre as localidad, gc.modalidad, gc.estado, gc.fecha_inicio
    FROM grupo_consagracion gc
    LEFT JOIN localidades l ON gc.localidad_id = l.id
    ORDER BY gc.id DESC
    LIMIT 5
");

// Últimos 5 encargados de grupo_consagracion (el encargado_id apunta a misioneros)
$ultimosEncargados = $conn->query("
    SELECT 
        CONCAT(m.nombres, ' ', m.apellidos) AS nombres_apellidos, 
        gc.modalidad, 
        gc.estado, 
        gc.fecha_inicio,
        l.nombre as localidad
    FROM grupo_consagracion gc
    LEFT JOIN misioneros m ON gc.encargado_id = m.id
    LEFT JOIN localidades l ON gc.localidad_id = l.id
    ORDER BY gc.id DESC
    LIMIT 5
");

// Asistencias por día (igual)
$asistenciasPorDia = $conn->query("
    SELECT fecha, COUNT(*) as total 
    FROM Asistencia 
    WHERE fecha >= CURDATE() - INTERVAL 6 DAY 
    GROUP BY fecha 
    ORDER BY fecha
");

$labels = [];
$data = [];
while ($row = $asistenciasPorDia->fetch_assoc()) {
    $labels[] = $row['fecha'];
    $data[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Consagración</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="text-center mb-5">📊 Dashboard - Sistema de Consagración</h1>

    <!-- Tarjetas resumen -->
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5>Grupos</h5>
                    <h2><?= $totalGrupos ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h5>Integrantes</h5>
                    <h2><?= $totalIntegrantes ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h5>Asistencias</h5>
                    <h2><?= $totalAsistencias ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-dark shadow-sm">
                <div class="card-body">
                    <h5>Encargados</h5>
                    <h2><?= $totalEncargados ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección rápida de accesos -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 border-primary shadow-sm">
                <div class="card-body text-center">
                    <h5>➕ Crear Grupo</h5>
                    <p>Crea nuevos grupos.</p>
                    <a href="crear_grupo.php" class="btn btn-outline-primary">Ir</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-success shadow-sm">
                <div class="card-body text-center">
                    <h5>👥 Registrar Integrante</h5>
                    <p>Agrega personas a los grupos.</p>
                    <a href="registrar_integrante.php" class="btn btn-outline-success">Ir</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-warning shadow-sm">
                <div class="card-body text-center">
                    <h5>📝 Tomar Asistencia</h5>
                    <p>Registra asistencias por grupo.</p>
                    <a href="tomar_asistencia.php" class="btn btn-outline-warning">Ir</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-secondary shadow-sm">
                <div class="card-body text-center">
                    <h5>📄 Reportes</h5>
                    <p>Consulta historial de asistencias.</p>
                    <a href="reporte_asistencias.php" class="btn btn-outline-secondary">Ver</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100 border-info shadow-sm">
                <div class="card-body text-center">
                    <h5>👨‍🏫 Encargados</h5>
                    <p>Registrar responsables de grupo.</p>
                    <a href="registro_encargado.php" class="btn btn-outline-info">Ir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos registros -->
    <div class="row mb-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light"><strong>📅 Últimos Grupos Creados</strong></div>
                <ul class="list-group list-group-flush">
                    <?php while ($g = $ultimosGrupos->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($g['localidad']) ?> – <?= htmlspecialchars($g['modalidad']) ?>
                            <br>
                            <small class="text-muted"><?= htmlspecialchars($g['estado']) ?>, inicio <?= htmlspecialchars($g['fecha_inicio']) ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light"><strong>👤 Últimos Encargados Registrados</strong></div>
                <ul class="list-group list-group-flush">
                    <?php while ($e = $ultimosEncargados->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($e['nombres_apellidos']) ?> – <?= htmlspecialchars($e['modalidad']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($e['estado']) ?>, <?= htmlspecialchars($e['localidad']) ?>, inicio <?= htmlspecialchars($e['fecha_inicio']) ?></small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Gráfico de asistencias -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h5 class="card-title">📈 Asistencias Registradas (últimos 7 días)</h5>
            <canvas id="asistenciaChart" height="100"></canvas>
        </div>
    </div>
</div>

<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<script>
const ctx = document.getElementById('asistenciaChart').getContext('2d');
const asistenciaChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Total asistencias',
            data: <?= json_encode($data) ?>,
            borderColor: 'rgba(54, 162, 235, 1)',
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            fill: true,
            tension: 0.3,
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
</body>
</html>

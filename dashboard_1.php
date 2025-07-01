<?php
require 'conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Conteos
$totalGrupos      = $conn->query("SELECT COUNT(*) FROM Grupos_Consagracion")->fetch_row()[0];
$totalIntegrantes = $conn->query("SELECT COUNT(*) FROM Integrantes")->fetch_row()[0];
$totalAsistencias = $conn->query("SELECT COUNT(*) FROM Asistencia")->fetch_row()[0];
$totalEncargados  = $conn->query("SELECT COUNT(*) FROM encargados_consagracion")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Consagración</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="text-center mb-5">📊 Dashboard - Sistema de Consagración</h1>

    <!-- Resumen numérico -->
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

    <!-- Accesos -->
    <div class="row g-4">

        <!-- Crear Grupo -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-primary">
                <div class="card-body text-center">
                    <h5 class="card-title">➕ Crear Grupo</h5>
                    <p class="card-text">Crea nuevos grupos de consagración.</p>
                    <a href="crear_grupo.php" class="btn btn-outline-primary">Ir a Crear Grupo</a>
                </div>
            </div>
        </div>

        <!-- Registrar Integrante -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-success">
                <div class="card-body text-center">
                    <h5 class="card-title">👥 Registrar Integrante</h5>
                    <p class="card-text">Agrega miembros a los grupos.</p>
                    <a href="registrar_integrante.php" class="btn btn-outline-success">Registrar Integrante</a>
                </div>
            </div>
        </div>

        <!-- Tomar Asistencia -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-warning">
                <div class="card-body text-center">
                    <h5 class="card-title">📝 Tomar Asistencia</h5>
                    <p class="card-text">Registra la asistencia de cada grupo.</p>
                    <a href="tomar_asistencia.php" class="btn btn-outline-warning">Tomar Asistencia</a>
                </div>
            </div>
        </div>

        <!-- Reporte de Asistencias -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-secondary">
                <div class="card-body text-center">
                    <h5 class="card-title">📄 Reporte de Asistencias</h5>
                    <p class="card-text">Consulta el historial por grupo o participante.</p>
                    <a href="reporte_asistencias.php" class="btn btn-outline-secondary">Ver Reportes</a>
                </div>
            </div>
        </div>

        <!-- Registrar Encargado -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100 border-info">
                <div class="card-body text-center">
                    <h5 class="card-title">👨‍🏫 Registrar Encargado</h5>
                    <p class="card-text">Registra a los responsables de cada grupo.</p>
                    <a href="registro_encargado.php" class="btn btn-outline-info">Registrar Encargado</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

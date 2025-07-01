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

    <div class="row g-4">

        <!-- Crear Grupo -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">➕ Crear Grupo de Consagración</h5>
                    <p class="card-text">Crea nuevos grupos de consagración.</p>
                    <a href="crear_grupo.php" class="btn btn-primary">Ir a Crear Grupo</a>
                </div>
            </div>
        </div>

        <!-- Registrar Integrante -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">👥 Registrar Participantes de Consagración</h5>
                    <p class="card-text">Agrega participantes de los grupos de consagración</p>
                    <a href="registrar_integrante.php" class="btn btn-primary">Registrar Integrante</a>
                </div>
            </div>
        </div>

        <!-- Tomar Asistencia -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">📝 Tomar Asistencia</h5>
                    <p class="card-text">Registra la asistencia de cada grupo.</p>
                    <a href="tomar_asistencia.php" class="btn btn-primary">Tomar Asistencia</a>
                </div>
            </div>
        </div>

        <!-- Reporte de Asistencias -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">📄 Reporte de Asistencias</h5>
                    <p class="card-text">Consulta el historial por grupo o participante.</p>
                    <a href="reporte_asistencias.php" class="btn btn-secondary">Ver Reportes</a>
                </div>
            </div>
        </div>

        <!-- Registrar Encargado -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">👨‍🏫 Registrar Encargado</h5>
                    <p class="card-text">Registra a los Encargados y apoyos de cada grupo.</p>
                    <a href="registro_encargado.php" class="btn btn-secondary">Registrar Encargado</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

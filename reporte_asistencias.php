<?php
require 'conexion.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 1) Listas para los selects
$grupos = $conn->query("
    SELECT 
        gc.id,
        l.nombre AS localidad,
        gc.parroquia
    FROM grupo_consagracion gc
    LEFT JOIN localidades l ON gc.localidad_id = l.id
    ORDER BY localidad, parroquia
");

$integrantes = [];
$grupo_id_for_participante = $_POST['grupo_id_for_participante'] ?? null;

if ($grupo_id_for_participante) {
    $stmt = $conn->prepare("SELECT * FROM Integrantes WHERE grupo_id = ?");
    $stmt->bind_param("i", $grupo_id_for_participante);
    $stmt->execute();
    $integrantes = $stmt->get_result();
}

// FUNCIONES DE REPORTE

function mostrarAsistenciaPorGrupo(mysqli $conn, int $grupo_id): void {
    $sql = "SELECT i.nombre_completo, a.fecha, a.presente
            FROM Asistencia a
            JOIN Integrantes i ON a.integrante_id = i.id
            WHERE i.grupo_id = ?
            ORDER BY a.fecha DESC, i.nombre_completo";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<h5 class="mb-3">Historial de Asistencia del Grupo</h5>';
    echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
    echo '<thead class="table-dark"><tr><th>Integrante</th><th>Fecha</th><th>Presente</th></tr></thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>'.htmlspecialchars($row['nombre_completo']).'</td>';
        echo '<td>'.$row['fecha'].'</td>';
        echo '<td>'.($row['presente'] ? '✔️' : '❌').'</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

function mostrarAsistenciaPorParticipante(mysqli $conn, int $participante_id): void {
    $sql = "SELECT a.fecha, a.presente
            FROM Asistencia a
            WHERE a.integrante_id = ?
            ORDER BY a.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $participante_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<h5 class="mb-3">Historial de Asistencia del Participante</h5>';
    echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
    echo '<thead class="table-dark"><tr><th>Fecha</th><th>Presente</th></tr></thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>'.$row['fecha'].'</td>';
        echo '<td>'.($row['presente'] ? '✔️' : '❌').'</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Asistencias</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-4">
    <h2 class="mb-4 text-center">Reporte de Asistencias</h2>

    <ul class="nav nav-pills mb-4 justify-content-center" id="reporte-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= isset($_POST['ver_reporte_grupo']) ? 'active' : '' ?>"
                    id="grupo-tab" data-bs-toggle="pill" data-bs-target="#por-grupo" type="button"
                    role="tab" aria-controls="por-grupo" aria-selected="<?= isset($_POST['ver_reporte_grupo']) ? 'true' : 'false' ?>">
                📋 Reporte por Grupo
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= isset($_POST['ver_reporte_participante']) ? 'active' : '' ?>"
                    id="participante-tab" data-bs-toggle="pill" data-bs-target="#por-participante"
                    type="button" role="tab" aria-controls="por-participante"
                    aria-selected="<?= isset($_POST['ver_reporte_participante']) ? 'true' : 'false' ?>">
                🙋 Reporte por Participante
            </button>
        </li>
    </ul>

    <div class="tab-content" id="reporte-tabs-content">
        <!-- ─── TAB REPORTE POR GRUPO ─── -->
        <div class="tab-pane fade <?= isset($_POST['ver_reporte_grupo']) ? 'show active' : '' ?>" id="por-grupo" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Selecciona un grupo</label>
                            <select name="grupo_id" class="form-select" required>
                                <option value="">-- Grupo --</option>
                                <?php
                                $grupos->data_seek(0);
                                while ($row = $grupos->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>" <?= (($_POST['grupo_id'] ?? '') == $row['id'] ? 'selected':'') ?>>
                                        <?= htmlspecialchars($row['localidad']) ?> - <?= htmlspecialchars($row['parroquia'] ?? 'Sin parroquia') ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="submit" name="ver_reporte_grupo" class="btn btn-primary">
                                Ver Reporte
                            </button>
                        </div>
                    </form>
                    <?php
                    if (isset($_POST['ver_reporte_grupo']) && !empty($_POST['grupo_id'])) {
                        echo '<hr class="my-4">';
                        mostrarAsistenciaPorGrupo($conn, (int)$_POST['grupo_id']);
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- ─── TAB REPORTE POR PARTICIPANTE ─── -->
        <div class="tab-pane fade <?= isset($_POST['ver_reporte_participante']) ? 'show active' : '' ?>" id="por-participante" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" class="row gy-3 gx-3 align-items-end">

                        <!-- Grupo para filtrar participantes -->
                        <div class="col-md-4">
                            <label class="form-label">Grupo</label>
                            <select name="grupo_id_for_participante" class="form-select" onchange="this.form.submit()" required>
                                <option value="">-- Grupo --</option>
                                <?php
                                $grupos2 = $conn->query("
                                    SELECT 
                                        gc.id,
                                        l.nombre AS localidad,
                                        gc.parroquia
                                    FROM grupo_consagracion gc
                                    LEFT JOIN localidades l ON gc.localidad_id = l.id
                                    ORDER BY localidad, parroquia
                                ");
                                while ($row = $grupos2->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>"
                                        <?= ($grupo_id_for_participante == $row['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['localidad']) ?> - <?= htmlspecialchars($row['parroquia'] ?? 'Sin parroquia') ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Participantes (si ya se eligió grupo) -->
                        <?php if (!empty($integrantes) && $integrantes->num_rows > 0): ?>
                            <div class="col-md-4">
                                <label class="form-label">Participante</label>
                                <select name="participante_id" class="form-select" required>
                                    <option value="">-- Participante --</option>
                                    <?php while ($row = $integrantes->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>"
                                            <?= (($_POST['participante_id'] ?? '') == $row['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($row['nombre_completo']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" name="ver_reporte_participante" class="btn btn-primary">
                                    Ver Reporte
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                    <?php
                    if (isset($_POST['ver_reporte_participante']) && !empty($_POST['participante_id'])) {
                        echo '<hr class="my-4">';
                        mostrarAsistenciaPorParticipante($conn, (int)$_POST['participante_id']);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div> <!-- /tab-content -->
</div><!-- /.container -->

<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

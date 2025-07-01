<?php
require 'conexion.php';

// Consultas para selects principales
$localidades = $conn->query("SELECT id, nombre FROM localidades ORDER BY nombre");
$misioneros = $conn->query("SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre FROM misioneros ORDER BY nombres, apellidos");
$departamentos = $conn->query("SELECT id, name FROM ubigeo_peru_departments ORDER BY name");

$mensaje = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recoge datos del formulario
    $localidad_id = $_POST['localidad_id'];
    $modalidad = $_POST['modalidad'];
    $estado = $_POST['estado'];
    $encargado_id = $_POST['encargado_id'];
    $apoyo1_id = !empty($_POST['apoyo1_id']) ? $_POST['apoyo1_id'] : NULL;
    $apoyo2_id = !empty($_POST['apoyo2_id']) ? $_POST['apoyo2_id'] : NULL;
    $fecha_inicio = $_POST['fecha_inicio'];
    $dia = $_POST['dia'];
    $hora = $_POST['hora'];
    $cantidad_integrantes = $_POST['cantidad_integrantes'];

    // Datos parroquia
    $parroquia = ($modalidad == 'Presencial') ? $_POST['parroquia'] : NULL;
    $departamento_id = ($modalidad == 'Presencial') ? $_POST['departamento_id'] : NULL;
    $provincia_id = ($modalidad == 'Presencial') ? $_POST['provincia_id'] : NULL;
    $distrito_id = ($modalidad == 'Presencial') ? $_POST['distrito_id'] : NULL;
    $direccion = ($modalidad == 'Presencial') ? $_POST['direccion'] : NULL;

    // Datos ceremonia
    $fecha_ceremonia = ($estado == 'Culminado') ? $_POST['fecha_ceremonia'] : NULL;
    $cantidad_consagrados = ($estado == 'Culminado') ? $_POST['cantidad_consagrados'] : NULL;

    // Datos cierre
    $fecha_cierre = ($estado == 'Cerrado') ? $_POST['fecha_cierre'] : NULL;
    $motivo_cierre = ($estado == 'Cerrado') ? $_POST['motivo_cierre'] : NULL;

    // Insertar datos
    $stmt = $conn->prepare("INSERT INTO grupo_consagracion (
    localidad_id, modalidad, estado, encargado_id, apoyo1_id, apoyo2_id, fecha_inicio, dia, hora, cantidad_integrantes_inicio,
    parroquia, departamento_id, provincia_id, distrito_id, direccion,
    fecha_ceremonia, cantidad_consagrados,
    fecha_cierre, motivo_cierre
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "issiiissssssssiisss",
    $localidad_id,     // i
    $modalidad,        // s
    $estado,           // s
    $encargado_id,     // i
    $apoyo1_id,        // i
    $apoyo2_id,        // i
    $fecha_inicio,     // s
    $dia,              // s
    $hora,             // s
    $cantidad_integrantes, // i
    $parroquia,        // s
    $departamento_id,  // s (porque es VARCHAR(2))
    $provincia_id,     // s (porque es VARCHAR(4))
    $distrito_id,      // s (porque es VARCHAR(6))
    $direccion,        // s
    $fecha_ceremonia,  // s
    $cantidad_consagrados, // i
    $fecha_cierre,     // s
    $motivo_cierre     // s
);
    if($stmt->execute()) {
        $mensaje = "Grupo de Consagración creado correctamente.";
    } else {
        $mensaje = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Grupo de Consagración</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <style>
        body {
            background: #e8f0fe;
        }
        .card {
            border-radius: 18px !important;
            border: none;
            box-shadow: 0 4px 22px 0 rgba(80,123,235,0.10);
        }
        h2 {
            color: #3056d3;
            font-weight: bold;
        }
        h5, h6 {
            color: #2852b8;
            font-weight: 600;
        }
        .section-divider {
            border-top: 2px dashed #b0c4de;
            margin: 30px 0 20px;
        }
        .form-label {
            font-weight: 500;
        }
        .form-select:focus, .form-control:focus {
            border-color: #3056d3;
            box-shadow: 0 0 0 0.10rem rgba(80,123,235,0.25);
        }
        .btn-success {
            font-size: 1.1rem;
            padding: 12px 30px;
            border-radius: 10px;
            background: linear-gradient(90deg,#2bc978 60%, #60e6ad 100%);
            border: none;
            font-weight: bold;
        }
        .btn-success:hover {
            background: linear-gradient(90deg,#1fb86a 50%, #48e7b6 100%);
            color: #fff;
        }
        .alert-info {
            font-size: 1.1rem;
            font-weight: 500;
            background: #e3f6ee;
            color: #219165;
            border-radius: 10px;
            border: none;
        }
        .d-none { display: none !important; }
        @media (max-width: 768px) {
            h2 { font-size: 1.4rem; }
            .card { padding: 15px !important;}
        }
    </style>
</head>
<body>
<div class="container my-4">
    <div class="mx-auto" style="max-width: 950px;">
        <div class="text-center mb-4">
            <h2>
                <svg width="40" height="40" fill="#3056d3" class="mb-1" viewBox="0 0 16 16"><path d="M8 1a5 5 0 0 1 5 5v2.5A1.5 1.5 0 0 0 14.5 10V15h-13V10A1.5 1.5 0 0 0 3 8.5V6a5 5 0 0 1 5-5z"/></svg>
                Crear Grupo de Consagración
            </h2>
        </div>
        <?php if($mensaje): ?>
            <div class="alert alert-info shadow-sm mb-4"><?= $mensaje ?></div>
        <?php endif; ?>

        <form method="POST" class="card shadow-lg p-4 px-4" autocomplete="off">
            <!-- Datos de Consagración -->
            <h5><i class="bi bi-people-fill"></i> Datos de Consagración</h5>

            <div class="alert alert-warning py-2 mb-3 small" style="font-size: 1.04rem;">
    <i class="bi bi-exclamation-triangle-fill"></i>
    Si no aparece el misionero en la lista de <strong>Encargado</strong> o <strong>Apoyo</strong>, es probable que no esté registrado.
    <a href="registro_misionero.php" class="alert-link ms-1" style="text-decoration:underline;">Haga click aquí para registrarlo</a>.
</div>



            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Localidad</label>
                    <select name="localidad_id" class="form-select" required>
                        <option value="">Seleccione localidad</option>
                        <?php while($row = $localidades->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Modalidad</label>
                    <select name="modalidad" class="form-select" id="modalidad" required>
                        <option value="">Seleccione modalidad</option>
                        <option value="Presencial">Presencial</option>
                        <option value="Virtual">Virtual</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select" id="estado" required>
                        <option value="">Seleccione estado</option>
                        <option value="Activo (Inscripciones abiertas)">Activo (Inscripciones abiertas)</option>
                        <option value="Activo (Inscripciones Cerradas)">Activo (Inscripciones Cerradas)</option>
                        <option value="Culminado">Culminado</option>
                        <option value="Cerrado">Cerrado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Encargado</label>
                    <select name="encargado_id" class="form-select" required>
                        <option value="">Seleccione misionero</option>
                        <?php while($row = $misioneros->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Apoyo 1</label>
                    <select name="apoyo1_id" class="form-select">
                        <option value="">(Opcional)</option>
                        <?php
                        $misioneros2 = $conn->query("SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre FROM misioneros ORDER BY nombres, apellidos");
                        while($row = $misioneros2->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Apoyo 2</label>
                    <select name="apoyo2_id" class="form-select">
                        <option value="">(Opcional)</option>
                        <?php
                        $misioneros3 = $conn->query("SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre FROM misioneros ORDER BY nombres, apellidos");
                        while($row = $misioneros3->fetch_assoc()): ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Inicio de Consagración</label>
                    <input type="date" name="fecha_inicio" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Día</label>
                    <select name="dia" class="form-select" required>
                        <option value="">Seleccione</option>
                        <?php foreach(['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'] as $d): ?>
                            <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
    <label class="form-label">Hora</label>
    <select name="hora" class="form-select" required>
        <option value="">Seleccione hora</option>
        <?php
        $start = strtotime('07:00');
        $end = strtotime('23:00');
        for ($i = $start; $i <= $end; $i += 30*60) {
            $value = date('H:i', $i);
            $label = date('h:i A', $i);
            echo "<option value=\"$value\">$label</option>";
        }
        ?>
    </select>
</div>
                <div class="col-md-3">
                    <label class="form-label">Integrantes (Inicio)</label>
                    <input type="number" name="cantidad_integrantes" class="form-control" min="1" required>
                </div>
            </div>
            <div class="section-divider"></div>
            <!-- Contenedor Parroquia -->
            <div id="contenedor_parroquia" class="d-none mb-3">
                <h6><i class="bi bi-geo-alt-fill"></i> Datos de Parroquia</h6>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Parroquia</label>
                        <input type="text" name="parroquia" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Departamento</label>
                        <select name="departamento_id" id="departamento_id" class="form-select">
                            <option value="">Seleccione departamento</option>
                            <?php while($row = $departamentos->fetch_assoc()): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Provincia</label>
                        <select name="provincia_id" id="provincia_id" class="form-select">
                            <option value="">Seleccione provincia</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Distrito</label>
                        <select name="distrito_id" id="distrito_id" class="form-select">
                            <option value="">Seleccione distrito</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control">
                </div>
            </div>
            <div class="section-divider"></div>
            <!-- Contenedor Ceremonia -->
            <div id="contenedor_ceremonia" class="d-none mb-3">
                <h6><i class="bi bi-award-fill"></i> Datos de Ceremonia de Consagración</h6>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Fecha de ceremonia</label>
                        <input type="date" name="fecha_ceremonia" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cantidad de Consagrados</label>
                        <input type="number" name="cantidad_consagrados" class="form-control" min="1">
                    </div>
                </div>
            </div>
            <div class="section-divider"></div>
            <!-- Contenedor Cierre -->
            <div id="contenedor_cierre" class="d-none mb-3">
                <h6><i class="bi bi-x-circle-fill"></i> Datos de Cierre de Consagración</h6>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Fecha de cierre</label>
                        <input type="date" name="fecha_cierre" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Motivo</label>
                        <input type="text" name="motivo_cierre" class="form-control">
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button class="btn btn-success shadow" type="submit">
                    <i class="bi bi-plus-circle"></i> Crear Grupo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap Icons CDN para los íconos decorativos -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
$(document).ready(function() {
    // Mostrar/ocultar secciones según Modalidad y Estado
    $('#modalidad').on('change', function() {
        $('#contenedor_parroquia').toggleClass('d-none', this.value !== 'Presencial');
    });
    $('#estado').on('change', function() {
        $('#contenedor_ceremonia').toggleClass('d-none', this.value !== 'Culminado');
        $('#contenedor_cierre').toggleClass('d-none', this.value !== 'Cerrado');
    });

    // Provincias según departamento (delegación de evento por si acaso)
    $(document).on('change', '#departamento_id', function() {
        let depto = $(this).val().trim();
        $('#provincia_id').html('<option value="">Cargando...</option>');
        $('#distrito_id').html('<option value="">Seleccione distrito</option>');
        if(depto) {
            $.get('ajax_provincias.php', {departamento_id: depto}, function(data) {
                let options = '<option value="">Seleccione provincia</option>' + data;
                $('#provincia_id').html(options);
                $('#distrito_id').html('<option value="">Seleccione distrito</option>');
            });
        } else {
            $('#provincia_id').html('<option value="">Seleccione provincia</option>');
            $('#distrito_id').html('<option value="">Seleccione distrito</option>');
        }
    });

    // Distritos según provincia (delegación de evento por si acaso)
    $(document).on('change', '#provincia_id', function() {
        let prov = $(this).val().trim();
        $('#distrito_id').html('<option value="">Cargando...</option>');
        if(prov) {
            $.get('ajax_distritos.php', {provincia_id: prov}, function(data) {
                $('#distrito_id').html(data);
            });
        } else {
            $('#distrito_id').html('<option value="">Seleccione distrito</option>');
        }
    });
});
</script>
<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

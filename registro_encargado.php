<?php
require 'conexion.php';

$mensaje_ok = null;

// Guardar encargado
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['misionero_id'])) {
    $misionero_id = $_POST['misionero_id'];
    $grupo_id = $_POST['grupo_id'];
    $funcion = $_POST['funcion'];

    $stmt = $conn->prepare("INSERT INTO encargados_consagracion (misionero_id, grupo_id, funcion) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $misionero_id, $grupo_id, $funcion);
    $stmt->execute();
    $stmt->close();

    $mensaje_ok = "Encargado registrado correctamente.";
}

// Grupos
$grupos = $conn->query("SELECT id, nombre_grupo FROM Grupos_Consagracion");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro Encargado</title>
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
        .autocomplete-items { border: 1px solid #ced4da; border-top: none; max-height: 180px; overflow-y: auto; background: #fff; }
        .autocomplete-items div { padding: 8px; cursor: pointer; }
        .autocomplete-items div:hover { background: #f1f1f1; }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <h2 class="mb-4 text-center">Registrar Encargado de Consagración</h2>

    <?php if ($mensaje_ok): ?>
        <div class="alert alert-success"><?= $mensaje_ok ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off" class="card shadow-sm p-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Buscar misionero</label>
                <input type="text" id="buscar_misionero" class="form-control" placeholder="Ingrese nombre o apellido" autocomplete="off">
                <div id="lista_sugerencias" class="autocomplete-items"></div>
            </div>
        </div>

        <!-- Hidden para ID misionero -->
        <input type="hidden" name="misionero_id" id="misionero_id" required>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Nombres</label>
                <input type="text" id="nombres" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" id="apellidos" class="form-control" readonly>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Localidad</label>
                <input type="text" id="localidad" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label">Escuela de formación</label>
                <input type="text" id="escuela" class="form-control" readonly>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Grupo de Consagración</label>
                <select name="grupo_id" class="form-select" required>
                    <option value="">Seleccione grupo</option>
                    <?php while ($g = $grupos->fetch_assoc()): ?>
                        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nombre_grupo']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Función</label>
                <select name="funcion" class="form-select" required>
                    <option value="">Seleccione función</option>
                    <option value="Encargado">Encargado</option>
                    <option value="Apoyo 1">Apoyo 1</option>
                    <option value="Apoyo 2">Apoyo 2</option>
                </select>
            </div>
        </div>
        <div class="text-end">
            <button class="btn btn-primary" type="submit">Registrar Encargado</button>
        </div>
    </form>
</div>

<script>
// Autocompletar búsqueda de misioneros
const buscar = document.getElementById('buscar_misionero');
const lista = document.getElementById('lista_sugerencias');
const id_mis = document.getElementById('misionero_id');
const nombres = document.getElementById('nombres');
const apellidos = document.getElementById('apellidos');
const localidad = document.getElementById('localidad');
const escuela = document.getElementById('escuela');

buscar.addEventListener('input', function() {
    const q = this.value;
    if (q.length < 2) { lista.innerHTML = ''; return; }
    fetch('buscar_misionero.php?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(datos => {
            lista.innerHTML = '';
            datos.forEach(m => {
                const div = document.createElement('div');
                div.textContent = m.nombres + ' ' + m.apellidos + ' (' + m.localidad + ')';
                div.onclick = () => {
                    id_mis.value = m.id;
                    nombres.value = m.nombres;
                    apellidos.value = m.apellidos;
                    localidad.value = m.localidad;
                    escuela.value = m.escuela;
                    buscar.value = m.nombres + ' ' + m.apellidos;
                    lista.innerHTML = '';
                };
                lista.appendChild(div);
            });
        });
});
document.addEventListener('click', function(e) {
    if (e.target !== buscar) lista.innerHTML = '';
});
</script>
<script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

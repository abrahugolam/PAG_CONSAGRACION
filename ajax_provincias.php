<?php
require 'conexion.php';
$depto = isset($_GET['departamento_id']) ? trim($_GET['departamento_id']) : '';
if($depto) {
    $res = $conn->query("SELECT id, name FROM ubigeo_peru_provinces WHERE department_id='$depto' ORDER BY name");
    if ($res && $res->num_rows > 0) {
        while($r = $res->fetch_assoc()){
            echo "<option value=\"{$r['id']}\">".htmlspecialchars($r['name'])."</option>";
        }
    } else {
        echo '<option value="">No hay provincias</option>';
    }
} else {
    echo '<option value="">Seleccione provincia</option>';
}

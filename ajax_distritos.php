<?php
require 'conexion.php';
$prov = isset($_GET['provincia_id']) ? trim($_GET['provincia_id']) : '';
if($prov) {
    $res = $conn->query("SELECT id, name FROM ubigeo_peru_districts WHERE TRIM(province_id) = '$prov' ORDER BY name");
    if ($res && $res->num_rows > 0) {
        while($r = $res->fetch_assoc()){
            echo "<option value=\"".trim($r['id'])."\">".htmlspecialchars($r['name'])."</option>";
        }
    } else {
        echo '<option value="">No hay distritos</option>';
    }
} else {
    echo '<option value="">Seleccione distrito</option>';
}

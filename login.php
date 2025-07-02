<?php
session_start();
require 'conexion.php';

// Si ya está logueado, redirige
if (isset($_SESSION['usuario_rol'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $usuario = trim($_POST['usuario']);
    $contrasena = trim($_POST['contrasena']);

    // Buscar usuario por campo 'user' y que esté Activo
    $stmt = $conn->prepare("
        SELECT 
            u.id_usuario, 
            u.user, 
            u.password, 
            u.rol_id, 
            u.id_misionero, 
            u.condicion,
            m.nombres,
            m.apellidos,
            l.nombre as localidad_nombre
        FROM usuarios u
        LEFT JOIN misioneros m ON u.id_misionero = m.id
        LEFT JOIN localidades l ON m.localidad = l.id
        WHERE u.user = ? AND u.condicion = 'Activo'
        LIMIT 1
    ");
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result(
            $id_usuario, $user, $password_hash, $rol_id, $id_misionero, $condicion,
            $nombre, $apellidos, $localidad_nombre
        );
        $stmt->fetch();
        if (password_verify($contrasena, $password_hash)) {
            // Aquí puedes obtener el nombre de rol si lo necesitas con otra consulta JOIN a la tabla roles
            $_SESSION['usuario_id'] = $id_usuario;
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_apellidos'] = $apellidos;
            $_SESSION['usuario_rol'] = $rol_id; // Puedes guardar el id_rol o hacer un JOIN y guardar el nombre del rol
            $_SESSION['id_misionero'] = $id_misionero;
            $_SESSION['usuario_localidad'] = $localidad_nombre;
            header('Location: dashboard.php');
            exit();
        }
    }
    $error = 'Usuario o contraseña incorrectos o usuario inactivo.';
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Lazos de Amor Mariano</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('img/fondo_login.jpeg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            z-index: 0;
        }
        .login-container {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(12px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .login-container h2 {
            color: white;
            margin-bottom: 20px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 18px;
        }
        .logo {
            width: 300px;
            display: block;
            margin: 0 auto 10px;
        }
        .titulo {
            text-align: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .subtitulo {
            text-align: center;
            color: white;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .form-label {
            color: #fff;
            font-weight: 500;
        }
        .form-control {
            background: rgba(255,255,255,0.93);
            border: none;
        }
        .form-control:focus {
            background: #fff;
        }
        .btn-primary {
            background: rgba(0, 123, 255, 0.8);
            border: none;
        }
        .btn-primary:hover {
            background: rgba(0, 123, 255, 1);
        }
        .alert {
            text-align: center;
        }
        @media (max-width: 576px) {
            .login-container {padding: 15px;}
            .logo {width: 100px;}
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="img/logo_LAM_oscuro.png" alt="Logo Lazos de Amor Mariano" class="logo">
            <div class="titulo">Equipo Espiritualidad</div>
            <div class="subtitulo">Provincia Santa Rosa de Lima</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="mb-3">
                <input type="text" name="usuario" placeholder="Documento (DNI o CE)" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <input type="password" name="contrasena" placeholder="Contraseña" class="form-control" required>
            </div>
            <div class="d-grid">
                <button class="btn btn-primary" name="login" type="submit">Ingresar</button>
            </div>
        </form>
    </div>
</body>
</html>

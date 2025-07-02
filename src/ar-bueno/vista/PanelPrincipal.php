<?php
// Iniciar sesión si no está iniciada
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    // Redirigir al login si no hay sesión
    header('Location: login.html');
    exit(); // Importante para evitar que el script continúe
}

// Opcional: Verificar tiempo de inactividad
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // 30 minutos de inactividad
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Actualizar tiempo de actividad
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel de Administración</title>
  <link rel="stylesheet" href="PanelPrincipal.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
  <header class="header">
    <h1>Panel de Administración</h1>
  </header>

  <main class="menu-container">
    <a href="Clientes.php" class="boton">
      
      <i class="fas fa-users"></i>
      <span>Clientes</span>
    </a>
    <a href="#propiedades" class="boton">
      <i class="fas fa-building"></i>
      <span>Propiedades</span>
    </a>
    <a href="#propietarios" class="boton">
      <i class="fas fa-user-tie"></i>
      <span>Propietarios</span>
    </a>
  </main>
</body>
</html>

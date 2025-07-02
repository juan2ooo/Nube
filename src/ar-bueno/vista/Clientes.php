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
    header('Location: login.html?timeout=1');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Actualizar tiempo de actividad
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda de Clientes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../../lib/sweetalert2/dist/sweetalert2.min.js">
    
    <link rel="stylesheet" href="Clientes.css">
</head>
<body>
    <div class="search-container">
        <h1><i class="fas fa-search"></i> Búsqueda de Clientes</h1>
        
        <div class="search-box">
            <div class="search-input">
                <input type="text" id="searchTerm" placeholder="Nombre">
                <input type="text" id="searchTerm1" placeholder="Teléfono">
                <button id="searchButton" class="btn-blue">
                    <i class="fas fa-search"></i> Buscar
                </button>

                <button id="aggBtn" class="btn-blue" onclick="abrirModal()">
                    <i class="fas fa-user-plus"></i>Agregar
                </button>
                <button class="btn-blue" onclick="eliminarElemento()">
                    <i class="fas fa-trash"></i> Eliminar
                </button>

            </div>
        </div>


        <!-- El modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <div class="firstRow">
            <h2 class="modal-title">Formulario</h2>
            <button class="save-btn save-btn-modificador" id="cerrar">X</button>
        </div>
        

            <input type="text" class="input-field" placeholder="Nombre" required id="nombreCliente">
            <input type="tel" class="input-field" placeholder="Teléfono" required id="telCliente">
            <select type="number" class="input-field" required id="horarioCliente">
                <option value="1">Mañana</option>
                <option value="2">Tarde</option>
                <option value="3">Noche</option>
            </select>
            <input type="text" class="input-field" placeholder="Observación" id="obsCliente">
            
            <button class="save-btn" id="saveClient">Guardar</button>

    </div>
</div>
        
        <div class="results-container">
            <table id="resultsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Horario</th>
                        <th>Observación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los resultados se cargarán aquí mediante JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="Clientes.js"></script>
</body>
</html>
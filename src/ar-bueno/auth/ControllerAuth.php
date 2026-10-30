<?php
require "../modelo/BD.php";

class ControllerAuth
{


    public function __construct()
    {
        // Ya no necesitamos inicializar vacíos si vamos a asignar valores desde el front
    }

    public function crearAdmin($nombre, $usuario, $contrasena)
    {
        $datosAdmin = array(
            "nombre" => $nombre,
            "usuario" => $usuario,
            "contrasena" => password_hash($contrasena, PASSWORD_DEFAULT)
        );

        $bd = new BD();
        return $bd->insert("administrador", $datosAdmin); // Cambia "administradores" por tu tabla real
    }


    /**
     * Verifica si la contraseña ingresada es correcta para un usuario dado
     *
     * @param string $usuario Nombre de usuario
     * @param string $contrasena Contraseña ingresada
     * @return bool Retorna true si la contraseña es correcta, false en caso contrario
     */
public function verificarCredenciales($usuario, $contrasena)
{
    try {
        $tabla = "administrador";
        $columnaClave = "usuario";

        $bd = new BD();
        $datos = $bd->selectByKey($tabla, $columnaClave, $usuario);

        if (isset($datos[0]['contrasena'])) {
            $hash = $datos[0]['contrasena'];

            $respuesta = password_verify($contrasena, $hash);

            return [
                "success" => $respuesta,
                "message" => $respuesta ? "Credenciales válidas" : "Contraseña incorrecta"
            ];
        }

        // Usuario no encontrado
        return [
            "success" => false,
            "message" => "Usuario no encontrado"
        ];

    } catch (Exception $e) {
        // Retornar el error al frontend en formato JSON
        return [
            "success" => false,
            "error"   => true,
            "message" => "Error en el servidor: " . $e->getMessage()
        ];
    }
}

}

// Manejo de la solicitud AJAX
if ($_POST['accion'] === 'registrarAdmin' & $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $auth = new ControllerAuth();

    $response = [
        'success' => false,
        'message' => ''
    ];

    try {

        if (!isset($_POST['nombre']) || !isset($_POST['usr']) || !isset($_POST['cont'])) {
            throw new Exception('Faltan datos requeridos');
        }

        $resultado = $auth->crearAdmin(
            $_POST['nombre'],
            $_POST['usr'],
            $_POST['cont']
        );

        $response['success'] = true;
        $response['message'] = 'Administrador creado exitosamente';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} elseif ($_POST['accion'] === 'login') {

    // Verifica que se hayan recibido los campos necesarios
    if (!isset($_POST['usr']) || !isset($_POST['cont'])) {
        throw new Exception('Faltan datos para iniciar sesión');
    }

    // Limpieza básica de entrada para prevenir ataques simples de inyección
    $usuario = trim($_POST['usr']);
    $contrasena = $_POST['cont'];
    $auth = new ControllerAuth();


    // Verifica credenciales usando el método que ya tienes
    if ($auth->verificarCredenciales($usuario, $contrasena)) {
        // Inicia sesión de manera segura
        session_start();

        // Regenera el ID de sesión para prevenir fijación de sesión
        session_regenerate_id(true);

        // Guarda el usuario en sesión
        $_SESSION['usuario'] = $usuario;

        // Guarda la hora del inicio de sesión
        $_SESSION['login_time'] = time();

        // Establece un tiempo de expiración de sesión (ej: 15 minutos)
        $_SESSION['expire_time'] = 180 * 60; // 15 minutos en segundos

        $response['success'] = true;
        $response['message'] = 'Inicio de sesión exitoso';

        $nuevaURL = "../vista/PanelPrincipal.html";

        echo ("Validado");
        exit(); // <-- Esto detiene la ejecución inmediatamente
    } else {
        throw new Exception('Credenciales incorrectas');
    }
}

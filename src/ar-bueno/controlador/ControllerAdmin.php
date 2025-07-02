<?php

require_once "../modelo/Administrador.php";
require_once "../modelo/Cliente.php";



// Manejo de la solicitud AJAX
if ($_POST['accion'] === 'saveClient' & $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $admin = new Administrador();

    $response = [
        'success' => false,
        'message' => ''
    ];

    try {

        $cliente = new Cliente();
        $cliente->nombre = $_POST['nombre'];
        $cliente->telefono = $_POST['telefono'];
        $cliente->horario = $_POST['horario'];
        $cliente->observacion =  $_POST['observacion'];


        $resultado = $admin->crearCliente($cliente);

        $response['success'] = true;
        $response['message'] = 'Cliente creado';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    //header('Content-Type: application/json');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}










// Manejo de la solicitud AJAX
if ($_POST['accion'] === 'buscarClientes' & $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    error_log($_POST['accion']);
    $admin = new Administrador();

    $response = [
        'success' => false,
        'message' => ''
    ];

    try {
        $nombre = $_POST['data']["nombre"];
        $telefono = $_POST['data']["telefono"];
        $admin = new Administrador();
        if($nombre != "" && $telefono != ""){
            //hay dos argumentos
            $nombreClaves = ["nombre","telefono"];
            $claves = [$nombre, $telefono];
            $datos = ["nombreClaves" => $nombreClaves, "claves" => $claves];
            $resultado = $admin-> buscarCliente($datos);
            var_dump($resultado);

        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    //header('Content-Type: application/json');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

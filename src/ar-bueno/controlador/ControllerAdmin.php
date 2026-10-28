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










if ($_POST['accion'] === 'buscarClientes' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Acción: " . $_POST['accion']);

    $response = [
        'success' => false,
        'message' => '',
        'data' => null
    ];

    try {


        
        $nombre = $_POST["nombre"];
        $telefono = $_POST["telefono"];
        $admin = new Administrador();


        if($_POST['all']){


            $response["data"] = $admin->buscarTodo();
            error_log("Respuesta all: " . print_r($response, true));
            //header('Content-Type: application/json');
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        if (!empty($nombre) && !empty($telefono)) {
            $nombreClaves = ["nombre", "telefono"];
            $claves = [$nombre, $telefono];
            $datos = ["nombreClaves" => $nombreClaves, "claves" => $claves];

            error_log("Resultado de buscarCliente por el if: " . print_r($datos, true));
            $resultado = $admin->buscarCliente($datos);
            error_log("Resultado de buscarCliente: " . print_r($resultado, true));

            if (!empty($resultado)) {
                error_log("Todo bien");
                $response["success"] = true;
                $response["message"] = "Todo Ok";
                $response["data"] = $resultado;
            } else {
                $response["message"] = "N";
            }
        } else {
            error_log($nombre);
            error_log($telefono);

            if($nombre !== ""){
                $nombreClave = "nombre";
                $clave = $nombre;
            }else{
                $nombreClave = "telefono";
                $clave = $telefono;
            }


            $datos = ["nombreClaves" => $nombreClave, "claves" => $clave];
            error_log("Resultado datos: " . print_r($datos, true));

            $resultado = $admin->buscarCliente($datos);
            error_log("Resultado de buscarCliente: " . print_r($resultado, true));

            if (!empty($resultado)) {
                error_log("Todo bien");
                $response["success"] = true;
                $response["message"] = "Todo Ok";
                $response["data"] = $resultado;
            } else {
                $response["message"] = "N";
            }
            $response["message"] = "Nombre o teléfono vacíos";
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    error_log("Respuesta final: " . print_r($response, true));
    //header('Content-Type: application/json');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}





if ($_POST['accion'] === 'desabilitarCliente' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = [
        'success' => false,
        'message' => '',
        'data' => null
    ];

    try {

        $telefonos = $_POST["telefonos"];
        //$telefono = $_POST["telefono"];
        $admin = new Administrador();
        
        $response["data"] = $admin->desabilitarCliete($telefonos);
        echo json_encode($response);


    }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }
}







if ($_POST['accion'] === 'desabilitarCliente' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = [
        'success' => false,
        'message' => '',
        'data' => null
    ];

    try {

        $telefonos = $_POST["telefonos"];
        //$telefono = $_POST["telefono"];
        $admin = new Administrador();
        
        $response["data"] = $admin->desabilitarCliete($telefonos);
        echo json_encode($response);


    }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }
}



if ($_POST['accion'] === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [
        'success' => false,
        'message' => '',
        'data' => null
    ];

    try {

        $datosEditar = $_POST["datos"];
        //$telefono = $_POST["telefono"];
        $admin = new Administrador();
        
        $response["data"] = $admin->editar("cliente",$_POST['clave'],$_POST['valor'], $datosEditar);
        echo json_encode($response);


    }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }
}
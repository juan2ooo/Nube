<?php
require_once "BD.php";
    class Administrador{
        public $nombre;
        public $usuario;

        public function crearCliente(Cliente $cliente){
            $datosCliente = array(
                "nombre" => $cliente->nombre,
                "telefono" => $cliente->telefono,
                "horario" => $cliente->horario,
                "observacion" => $cliente->observacion,
            );

        $bd = new BD();
            return $bd->insert("cliente", $datosCliente); 
        }


        public function buscarCliente($datos){
            $nombreTabla = "cliente";
            $nombresClaves = $datos["nombreClaves"];

            $claves = $datos["claves"];
            $bd = new BD();

            if (is_array($datos["claves"])){
                error_log("por el si");
                
                if (count($datos["claves"]) == 0){
                    error_log("por el si x 2");
                    error_log("por aqui!!!!");
                    return;
                }
                return $bd->selectByTwoKey($nombreTabla, $nombresClaves,$claves);
            }

            error_log("por el no");
            return $bd->selectByKeyLike($nombreTabla, $nombresClaves,$claves);
        }
    }
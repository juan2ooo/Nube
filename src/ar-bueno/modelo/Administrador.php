<?php
require_once "BD.php";
class Administrador
{
    public $nombre;
    public $usuario;

    public function crearCliente(Cliente $cliente)
    {
        $datosCliente = array(
            "nombre" => $cliente->nombre,
            "telefono" => $cliente->telefono,
            "horario" => $cliente->horario,
            "observacion" => $cliente->observacion,
        );

        $bd = new BD();
        return $bd->insert("cliente", $datosCliente);
    }


    public function buscarCliente($datos)
    {
        $nombreTabla = "cliente";
        $nombresClaves = $datos["nombreClaves"];

        $claves = $datos["claves"];
        $bd = new BD();

        if (is_array($datos["claves"])) {
            error_log("por el si");

            if (count($datos["claves"]) == 0) {

                return;
            }
            return $bd->selectByTwoKey($nombreTabla, $nombresClaves, $claves);
        }

        error_log("por el no");
        return $bd->selectByKeyLike($nombreTabla, $nombresClaves, $claves);
    }


    public function buscarTodo()
    {
        $bd = new BD();
        return $bd->selectAll("cliente");
    }


    public function desabilitarCliete($telefonos)
    {
        $bd = new BD();
        //$telefonos = ["3113311054"];
        return $bd->desabilitarCliente("cliente", "deleted", $telefonos);
    }


    public function editar($nombreTabla, $clave, $valor, $datos)
    {
        try {
            // Validación: asegurar que $datos tenga la clave que se va a actualizar
            if (!isset($datos[$clave])) {
                throw new Exception("El valor de la clave '{$clave}' no se encuentra en los datos proporcionados.");
            }

            // Crear instancia de la base de datos
            $bd = new BD();

            // Actualizar el registro
            $resultado = $bd->actualizarRegistro($nombreTabla, $clave, $valor, $datos);

            // Retornar el resultado
            return $resultado;
        } catch (Exception $e) {
            // Registrar el error en el log
            error_log("Error en editar(): " . $e->getMessage());

            // Retornar false o un mensaje de error
            return false;
        }
    }
    
}

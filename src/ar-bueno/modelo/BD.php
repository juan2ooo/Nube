<?php
// Clase Abstracta que nos permitirá conectarnos a MySQL
class BD
{
    // Atributos
    //private static $db_host = 'localhost';
    private static $db_host = 'dbinmobiliaria.ckmhm1qm7yyy.us-east-1.rds.amazonaws.com';
    //private static $db_user = 'root';
    private static $db_user = 'admin';
    private static $db_pass = 'Naccional10.';
    // private static $db_name = 'mexflix';
    //public $db_name = "bd_inmobiliaria";
    public $db_name = "inmobiliaria";
    private static $db_charset = 'utf8';
    private $conn;
    protected $query;
    protected $rows = array();

    private function db_open()
    {
        $this->conn = new mysqli(
            self::$db_host,
            self::$db_user,
            self::$db_pass,
            $this->db_name
        );
        $this->conn->set_charset(self::$db_charset);
    }

    private function db_close()
    {
        $this->conn->close();
    }





/**
 * Inserta un nuevo registro en una tabla de la base de datos.
 *
 * Este método construye dinámicamente una sentencia SQL `INSERT` usando 
 * parámetros preparados (`prepared statements`) para evitar inyección SQL.
 * Los valores son enlazados automáticamente como cadenas (`string`).
 *
 * Requisitos:
 * - La clase debe tener implementados los métodos `db_open()` y `db_close()`.
 * - La conexión debe estar definida en la propiedad `$this->conn` como instancia de `mysqli`.
 * - El archivo `../auth/cr/entrar.txt` debe contener la contraseña de la base de datos.
 *
 * @param string $table  Nombre de la tabla en la cual se insertará el registro.
 * @param array  $data   Arreglo asociativo con los campos y valores a insertar.
 *                       Ejemplo: ['nombre' => 'Ana', 'edad' => 22]
 *
 * @return void  No retorna valor. Si ocurre un error, lo registra en el log de PHP.
 *
 * @throws mysqli_sql_exception Si ocurre un error al preparar o ejecutar la consulta.
 *
 * @example
 * // Insertar un nuevo usuario en la tabla "usuarios"
 * $obj->insert(
 *     'usuarios',
 *     [
 *         'nombre' => 'Ana Gómez',
 *         'correo' => 'ana@example.com',
 *         'edad'   => 22
 *     ]
 * );
 */



public function insert($table, $data)
{
    // Hacer que mysqli lance excepciones en lugar de warnings
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // Leer contraseña desde archivo externo (ruta parametrizable)
        $file = '../auth/cr/entrar.txt'; // Cambia esta ruta
        self::$db_pass = file_get_contents($file);

        $this->db_open();

        // 1. Preparar la consulta SQL con ?
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        // 2. Crear y ejecutar el prepared statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($data)), ...array_values($data));
        $stmt->execute();

        $stmt->close();
        $this->db_close();

    } catch (mysqli_sql_exception $e) {
        // Registrar el error en el log de PHP
        error_log("Error en insert(): " . $e->getMessage());
    }
}



    /**
     * Realiza una consulta SELECT a una tabla filtrando por una clave específica
     * 
     * @param string $nombreTabla Nombre de la tabla a consultar
     * @param string $nombreColumnaClave Nombre de la columna por la que se filtra
     * @param mixed $clave Valor de la clave para filtrar
     * @return array|false Retorna un array asociativo con los resultados o false en caso de error
     */
    public function selectByKey($nombreTabla, $nombreColumnaClave, $clave)
    {
        // Leer contraseña desde archivo externo (igual que en insert)
        $file = '../auth/cr/entrar.txt';
        self::$db_pass = file_get_contents($file);
        
        $this->db_open();
        $result = false;

        try {
            // 1. Preparar la consulta SQL con parámetro
            $sql = "SELECT * FROM $nombreTabla WHERE  $nombreColumnaClave = ?";
            
            // 2. Crear y ejecutar el prepared statement
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('s', $clave);
            $stmt->execute();
            
            // 3. Obtener resultados
            $queryResult = $stmt->get_result();
            
            // 4. Convertir resultados a array asociativo
            if ($queryResult) {
                $result = $queryResult->fetch_all(MYSQLI_ASSOC);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            // Manejo de errores (opcional)
            error_log("Error en selectByKey: " . $e->getMessage());
            $result = false;
        }
        
        $this->db_close();
        return $result;
    }



public function selectByKeyLike($nombreTabla, $nombreColumnaClave, $clave)
{
    error_log($nombreTabla);
    error_log($nombreColumnaClave);
    error_log($clave);

    // Leer contraseña desde archivo externo (igual que en insert)
    $file = '../auth/cr/entrar.txt';
    self::$db_pass = file_get_contents($file);
    
    $this->db_open();
    $result = false;

    try {
        // 1. Preparar la consulta SQL con parámetro
        $sql = "SELECT * FROM $nombreTabla WHERE deleted = 0 AND $nombreColumnaClave LIKE ?";
        
        // 2. Crear y ejecutar el prepared statement
        $stmt = $this->conn->prepare($sql);
        $likeClave = "%{$clave}%"; // <-- mover esto a una variable
        $stmt->bind_param('s', $likeClave); // <-- pasar variable ya procesada
        $stmt->execute();
        
        // 3. Obtener resultados
        $queryResult = $stmt->get_result();
        
        // 4. Convertir resultados a array asociativo
        if ($queryResult) {
            $result = $queryResult->fetch_all(MYSQLI_ASSOC);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error en selectByKeyLike: " . $e->getMessage());
        $result = false;
    }
    
    $this->db_close();
    return $result;
}





        public function selectByTwoKey($nombreTabla, $nombreColumnaClave, $clave)
    {
        // Leer contraseña desde archivo externo (igual que en insert)
        $file = '../auth/cr/entrar.txt';
        self::$db_pass = file_get_contents($file);
        
        $this->db_open();
        $result = false;

        try {
            // 1. Preparar la consulta SQL con parámetro
            $sql = "SELECT * FROM $nombreTabla WHERE delete = 0 AND $nombreColumnaClave[0]  LIKE ? AND $nombreColumnaClave[1] LIKE ?";

            
            // 2. Crear y ejecutar el prepared statement
            $stmt = $this->conn->prepare($sql);
            $clave[0] = "%{$clave[0]}%";    // nombre
            $clave[1] = "{$clave[1]}%";     // teléfono

            
            $stmt->bind_param('ss', $clave[0], $clave[1]);
            $stmt->execute();
            
            // 3. Obtener resultados
            $queryResult = $stmt->get_result();
            
            // 4. Convertir resultados a array asociativo
            if ($queryResult) {
                $result = $queryResult->fetch_all(MYSQLI_ASSOC);
                //error_log($result);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            // Manejo de errores (opcional)
            error_log("Error en selectByKey: " . $e->getMessage());
            $result = false;
        }
        
        $this->db_close();
        return $result;
    }



    


        public function selectAll($nombreTabla)
    {
        // Leer contraseña desde archivo externo (igual que en insert)
        $file = '../auth/cr/entrar.txt';
        self::$db_pass = file_get_contents($file);
        
        $this->db_open();
        $result = false;

        try {
            // 1. Preparar la consulta SQL con parámetro
            $sql = "SELECT * FROM $nombreTabla WHERE deleted = 0";
            
            // 2. Crear y ejecutar el prepared statement
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            
            // 3. Obtener resultados
            $queryResult = $stmt->get_result();
            
            // 4. Convertir resultados a array asociativo
            if ($queryResult) {
                $result = $queryResult->fetch_all(MYSQLI_ASSOC);
            }
            
            $stmt->close();
        } catch (Exception $e) {
            // Manejo de errores (opcional)
            error_log("Error en selectByKey: " . $e->getMessage());
            $result = false;
        }
        
        $this->db_close();
        return $result;
    }


    /**
 * Deshabilita clientes en la base de datos actualizando una columna específica.
 *
 * Esta función recibe el nombre de la tabla, la columna a actualizar y
 * una lista de teléfonos. Usando prepared statements, actualiza los registros
 * cuyo campo "telefono" coincida con los números enviados.
 *
 * @param string $table          Nombre de la tabla en la BD.
 * @param string $nombreColumna  Columna que se actualizará (ejemplo: 'estado').
 * @param array  $telefonos      Lista de teléfonos que se desean actualizar.
 *
 * @return int|false  Número de filas afectadas si la operación fue exitosa, 
 *                    o false en caso de error.
 */

public function desabilitarCliente($table, $nombreColumna, $telefonos)
{
    // Leer contraseña desde archivo externo (ruta parametrizable)
    $file = '../auth/cr/entrar.txt'; // Cambia esta ruta
    self::$db_pass = file_get_contents($file);

    $this->db_open();

    try {
        // Construir placeholders para los teléfonos (?, ?, ? ...)
        $placeholders = implode(',', array_fill(0, count($telefonos), '?'));

        // Query con placeholders
        $sql = "UPDATE {$table} 
                SET {$nombreColumna} = 1
                WHERE telefono IN ($placeholders)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en prepare: " . $this->conn->error);
        }

        // Tipos de parámetros dinámicos (todos string para los teléfonos)
        $types = str_repeat('s', count($telefonos));

        // Vincular parámetros (spread operator con ... en PHP 5.6+)
        $stmt->bind_param($types, ...$telefonos);

        $stmt->execute();

        // Retornar filas afectadas
        return $stmt->affected_rows;

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        return false;
    } finally {
        $stmt->close();
        $this->db_close();
    }
}
 





/**
 * Actualiza un registro en la base de datos.
 *
 * Construye y ejecuta de forma dinámica una sentencia SQL `UPDATE` en la tabla
 * indicada, actualizando las columnas que se pasen en el arreglo de datos.
 * Solo se actualizan registros que no estén marcados como eliminados (`deleted = 0`).
 *
 * Requisitos:
 * - La clase debe tener un método `db_open()` que inicialice la conexión `$this->conn`.
 * - La conexión debe ser una instancia de `mysqli`.
 * - El archivo `../auth/cr/entrar.txt` debe contener la contraseña de la base de datos.
 *
 * @param string $table        Nombre de la tabla sobre la cual se realizará la actualización.
 * @param string $nombreClave  Nombre de la columna que actúa como clave primaria o única.
 * @param mixed  $clave        Valor de la clave para identificar el registro a actualizar.
 * @param array  $datos        Arreglo asociativo con los campos y valores a actualizar.
 *                             Ejemplo: ['nombre' => 'Juan', 'edad' => 25]
 *
 * @return bool  Devuelve `true` si la actualización se ejecutó correctamente,
 *               o `false` si ocurrió un error en la preparación o ejecución.
 *
 * @throws RuntimeException Si no se puede preparar la sentencia SQL.
 *
 * @example
 * // Ejemplo de uso:
 * $exito = $obj->actualizarRegistro(
 *     'usuarios',          // Tabla
 *     'id_usuario',        // Columna clave
 *     15,                  // Valor de clave
 *     [                    // Campos a actualizar
 *         'nombre' => 'Carlos Pérez',
 *         'correo' => 'carlos@example.com'
 *     ]
 * );
 *
 * if ($exito) {
 *     echo "Registro actualizado correctamente.";
 * } else {
 *     echo "No se pudo actualizar el registro.";
 * }
 */


public function actualizarRegistro($table, $nombreClave, $clave, $datos)
{
    // Log de los parámetros
    error_log("Tabla: $table");
    error_log("Nombre de clave: $nombreClave");
    error_log("Valor de clave: $clave");
    error_log("Datos a actualizar: " . print_r($datos, true));

    // Leer contraseña desde archivo externo
    $file = '../auth/cr/entrar.txt';
    self::$db_pass = trim(file_get_contents($file));

    // Abrir conexión si no está abierta
    $this->db_open();

    // Construir dinámicamente SET y valores
    $setPart = [];
    $valores = [];
    foreach ($datos as $columna => $valor) {
        $setPart[] = "`$columna` = ?";
        $valores[] = $valor;
    }

    // Agregar la clave del WHERE
    $valores[] = $clave;

    // Construir SQL
    $sql = "UPDATE `$table` SET " . implode(", ", $setPart) . " WHERE `$nombreClave` = ? AND deleted = 0";

    // Preparar y ejecutar
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) {
        error_log("Error en preparar statement: " . $this->conn->error);
        return false;
    }

    // Construir tipos para bind_param
    $tipos = str_repeat("s", count($valores)); // asumimos todos strings
    $stmt->bind_param($tipos, ...$valores);

    if ($stmt->execute()) {
        error_log("Registro actualizado correctamente.");
        $stmt->close();
        return true;
    } else {
        error_log("Error al actualizar registro: " . $stmt->error);
        $stmt->close();
        return false;
    }
}













}

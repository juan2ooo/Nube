<?php
// Clase Abstracta que nos permitirá conectarnos a MySQL
class BD
{
    // Atributos
    private static $db_host = 'localhost';
    private static $db_user = 'root';
    private static $db_pass = '';
    // private static $db_name = 'mexflix';
    public $db_name = "bd_inmobiliaria";
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



    // Método seguro para insertar (versión simple)
    public function insert($table, $data)
    {
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
            $sql = "SELECT * FROM $nombreTabla WHERE $nombreColumnaClave = ?";
            
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
        $sql = "SELECT * FROM $nombreTabla WHERE $nombreColumnaClave LIKE ?";
        
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
            $sql = "SELECT * FROM $nombreTabla WHERE $nombreColumnaClave[0] LIKE ? AND $nombreColumnaClave[1] LIKE ?";

            
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

}

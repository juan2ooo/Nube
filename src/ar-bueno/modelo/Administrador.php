<?php
require_once "BD.php";
//require __DIR__ . '/vendor/autoload.php';

//require '../../../vendor/autoload.php';
require '../../../vendor/autoload.php';

use Google\Client as Google_Client;
use Google\Service\Drive as Google_Service_Drive;
use Google\Service\Drive\DriveFile as Google_Service_Drive_DriveFile;
use Google\Service\Drive\Permission as Google_Service_Drive_Permission;



class Administrador
{
    public $nombre;
    public $usuario;
        // Ruta fija al JSON con credenciales de la cuenta de servicio
    private $serviceAccountJson = __DIR__ . "/../auth/cr/cuentaServicio.json";
    //private $serviceAccountJson = "../auth/cr/cuentaServicio.json";

    // ID fijo de la carpeta en Google Drive
    //private $folderId = "17R1z9C4zFBiT13Sb8kvDoBmmufTGd9RL";

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


    public function crearPropiedad(Propiedad $propiedad){
        
        $propiedad = new Propiedad();
        $propiedad->id = $_POST['id'];
        $propiedad->nombre = $_POST['nombre'];
        $propiedad->municipio = $_POST['municipio'];
        $propiedad->ubicacion = $_POST['ubicacion'];
        $propiedad->valor =  $_POST['valor'];
        $propiedad->metro =  $_POST['metro'];
        $propiedad->fotos =  $_POST['fotos'];
        $propiedad->videos =  $_POST['videos'];

        
    }







/**
 * Sube un archivo de video a Google Drive dentro de la carpeta fija (en un Shared Drive) y retorna su URL pública.
 *
 * @param array $file El archivo recibido desde un input file ($_FILES['video'])
 * @return string URL pública del archivo subido
 * @throws Exception Si ocurre un error en la subida
 */
public function subirVideoAGoogleDrive($file)
{
    try {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('Archivo no válido o no se pudo subir.');
        }

        $client = new Google_Client();
        $client->setAuthConfig(__DIR__.'/credentials.json');
        $client->addScope(Google_Service_Drive::DRIVE_FILE);
        $client->setAccessType('offline');

        $tokenPath = 'token.json';

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // Si el token no existe o expiró
        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Genera URL de autorización
                $authUrl = $client->createAuthUrl();
                echo "Abre esta URL en tu navegador y autoriza la app: $authUrl\n";
                echo "Ingresa el código de autorización: ";
                $authCode = trim(fgets(STDIN));
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }

        $service = new Google_Service_Drive($client);

        $fileMetadata = new Google_Service_Drive_DriveFile([
            'name' => $file['name'],
            'parents' => ['TU_SHARED_DRIVE_FOLDER_ID']
        ]);

        $content = file_get_contents($file['tmp_name']);
        if ($content === false) {
            throw new Exception('No se pudo leer el contenido del archivo.');
        }

        $uploadedFile = $service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file['type'],
            'uploadType' => 'multipart',
            'supportsAllDrives' => true
        ]);

        if (!$uploadedFile || !isset($uploadedFile->id)) {
            throw new Exception('Error al subir el archivo a Google Drive.');
        }

        $permission = new Google_Service_Drive_Permission([
            'type' => 'anyone',
            'role' => 'reader',
        ]);
        $service->permissions->create($uploadedFile->id, $permission, ['supportsAllDrives' => true]);

        return "https://drive.google.com/uc?id={$uploadedFile->id}&export=download";

    } catch (Exception $e) {
        throw new Exception("Error subiendo video a Google Drive: " . $e->getMessage());
    }
}



    




    
}

<?php
include("../vendor/autoload.php");
include("../functions.php");

/**
 * Clase Api
 * 
 * Esta clase se encarga de procesar las solicitudes HTTP entrantes y ejecutar las acciones correspondientes.
 * 
 * author: Yeisson Vélez
 * @version 1.0
 * @since 2025-01-15
 * 
 * Copyright (c) 2025 Yeisson Vélez Salazar
 * Todos los derechos reservados.
 *
 * Este código es propiedad exclusiva de Yeisson Vélez Salazar.
 * Está prohibida la reproducción, distribución o uso no autorizado.
 * Para obtener permisos, contacta con yeisson.velez@gmail.com.
 */
class Api {
    /**
     * Constructor de la clase Api
     * 
     * Detecta si se ha enviado una acción mediante POST y, si existe, la ejecuta.
     */
    public function __construct()
    {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            if (method_exists($this, $action)) {
                $this->$action();
            }
            exit;
        }
       
        header("location: /");
        exit;
    }

    /**
     * Ejecuta la acción para renovar un certificado.
     * 
     * Este método procesa los datos enviados por POST, actualiza el certificado en el servidor 
     * y retorna una respuesta en formato JSON.
     * 
     * @return void
     */
    private function renewCertificate(): void
    {
        // Datos que quieres retornar en formato JSON
        $data = [
            "status" => "error",
            "token" => "eXZz",
            "message" => "La actualización falló. Por favor, inténtelo de nuevo.",
            "code" => 500
        ];

        // Establecer el encabezado para indicar que el contenido es JSON
        header('Content-Type: application/json');

        $certificate = $_POST['certificate'] ?? "";

        if ($certificate !== "") {
            $_POST['id'] = 1;

            // Instancia de la clase Security (asumimos que está definida en otro archivo incluido)
            $security = new SecurityModel();
            
            // Guardar el certificado en un archivo y actualizar los permisos
            file_put_contents(LICENSE, $certificate);
            chmod(LICENSE, 0777);

            // Quitamos la clave para que no falle en los servidores que actualizan datos dinámicamente (save_edit)
            unset($_POST['certificate']);


            // Llamar al método para actualizar la clave
            $security->updateKey($_POST);

            // Actualizar la respuesta a éxito
            $data = [
                "status" => "success",
                "token" => "eXZz",
                "message" => "La operación fue exitosa.",
                "code" => 200
            ];
        }

        // Convertir el array a JSON y retornarlo
        echo json_encode($data);
    }
}

new Api();

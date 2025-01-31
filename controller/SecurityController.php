<?php
/**
 * Controlador para la gestión de licencias y seguridad en la aplicación.
 * 
 * author: Yeisson Vélez
 * @version 1.0
 * @since 2024-12-29
 * 
 * Copyright (c) 2025 Yeisson Vélez Salazar
 * Todos los derechos reservados.
 *
 * Este código es propiedad exclusiva de Yeisson Vélez Salazar.
 * Está prohibida la reproducción, distribución o uso no autorizado.
 * Para obtener permisos, contacta con yeisson.velez@gmail.com.
 */
class SecurityController extends BaseController {
    /**
     * Constructor de la clase LGController.
     * Inicializa el controlador sin realizar configuraciones adicionales.
     * 
     * @return void
     */
    function __construct()
    {
        // Constructor vacío
    }

    /**
     * Obtiene los datos de una clave existente.
     * 
     * Este método recupera información de una clave utilizando el componente de seguridad.
     * 
     * @return array Datos de la clave obtenida.
     */
    function getKey() {
        $security = new SecurityModel();
        return $security->getLicense(1); // Obtiene la clave con ID 1
    }

    /**
     * Valida la licencia y arranca la aplicación.
     * 
     * Este método verifica la existencia y validez del archivo de licencia. 
     * Descifra la información utilizando la clave y el IV almacenados, valida la fecha de expiración 
     * y autoriza el arranque de la aplicación si todo es válido.
     * 
     * @return void
     */
    function check() {
        $licenseInfo = (object)$this->getKey();
        $archivo_licencia = LICENSE;

        // Verificar si el archivo de licencia existe
        if (!file_exists($archivo_licencia)) {
            die(base64_decode(M_1));
        }

        // Recuperar y descifrar datos
        $cifrado_completo = base64_decode(file_get_contents($archivo_licencia));
        $iv_recibido = substr($cifrado_completo, 0, 16);  // IV de los primeros 16 bytes
        $cifrado_recibido = substr($cifrado_completo, 16); // Texto cifrado
        $clave_secreta = $licenseInfo->randomKey;

        $datos_licencia = openssl_decrypt($cifrado_recibido, 'aes-128-cbc', $clave_secreta, 0, $iv_recibido);
        if ($datos_licencia === false) {
            die(base64_decode(M_2));
        }

        $licencia = json_decode($datos_licencia, true);

        // Validar fecha de expiración
        if (strtotime($licencia['fecha_expiracion']) < time()) {
            die(base64_decode(M_3));
        }
    }
}

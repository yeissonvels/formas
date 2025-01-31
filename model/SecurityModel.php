<?php
/**
 * Modelo para la gestión de la seguridad.
 * 
 * 
 * @author Yeisson Vélez
 * @version 1.0
 * @since 2025-01-07
 * 
 * 
 * Copyright (c) 2025 Yeisson Vélez Salazar
 * Todos los derechos reservados.
 *
 * Este código es propiedad exclusiva de Yeisson Vélez Salazar.
 * Está prohibida la reproducción, distribución o uso no autorizado.
 * Para obtener permisos, contacta con yeisson.velez@gmail.com.
 */

class SecurityModel extends Base {
    protected $configTable;
    protected $wpdb;
    /**
     * Constructor de la clase User.
     * Inicializa la conexión a la base de datos.
     */
    public function __construct()
    {
        global $wpdb;
        $this->errors = [];
        $this->configTable = $wpdb->prefix . "config";
        $this->wpdb = $wpdb;
    }

     /**
     * Crea un key para la aplicación.
     * 
     * @param array $info datos de la licencia.
     * @return bool True si la inserción fue exitosa, false en caso contrario.
     */
    function createKey($info) {
        $_POST['randomKey'] = $info['randomKey'];
        $_POST['expiration'] = $info['expiration'];
        $_POST['customer'] = $info['customer'];
        $this->wpdb->save($this->configTable);
    }

    /**
     * Obtiene los datos de la licencia por su ID.
     * 
     * @param int $licenseId.
     * @return object|null Datos de la licencia o null si no existe.
     */
    function getLicense(int $licenseId): ?object {
        return $this->wpdb->getOneRow($this->configTable, $licenseId);
    }

    /**
     * Actualiza los datos del key para la aplicación.
     * 
     * @param array $info datos de la licencia.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    function updateKey($info) {
        $_POST['randomKey'] = $info['randomKey'];
        $_POST['expiration'] = $info['expiration'];
        $_POST['customer'] = $info['customer'];
        $_POST['id'] = $info['id'];
        
        if($this->wpdb->save_edit($this->configTable, false)) {
            return true;
        }

        return false;
    }
}
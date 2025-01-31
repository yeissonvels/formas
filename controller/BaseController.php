<?php
/**
 * 
 * Controlador Base
 * 
 * author: Yeisson Vélez
 * @version 1.0
 * @since 2025-01-06
 * 
 * Copyright (c) 2025 Yeisson Vélez Salazar
 * Todos los derechos reservados.
 *
 * Este código es propiedad exclusiva de Yeisson Vélez Salazar.
 * Está prohibida la reproducción, distribución o uso no autorizado.
 * Para obtener permisos, contacta con yeisson.velez@gmail.com.
 */
class BaseController {
    /**
     * Nivel de seguridad requerido para acceder a este controlador.
     * 
     * @var array
     */
    protected array $security;

    /**
     * Nivel de seguridad requerido para cada método.
     * 
     * @var array
     */
    protected array $permissions;

    /**
     * Idioma de la aplicación.
     * 
     * @var string
     */
    protected string $locale;

    function __construct()
    {
        // Core de la aplicación
        (new (base64_decode(constant(chr((6*10)+(42/3)/2).'_'.chr(((3120/5)/8))))))->{base64_decode(constant(chr(11*7).'_'.(chr((4*3+1)*6))))}();
        $this->setLocale();
    }

    /**
     * Obtiene el permiso requerido para poder usar el método
     * 
     * @param string $method nombre del método.
     * @return array
     */
    protected function getMethodPermission(string $method): array {
        $permissions = $this->permissions;
       
        if(array_key_exists($method, $permissions)) {
            return $permissions[$method];
        }

        return [];
    }

    /**
     * Configura la seguridad del controlador
     * 
     * @param array $method nombre del método.
     * @param string $controller nombre del método.
     * @return void
     */
    protected function setSecurity(array $security, string $controller): void {
        array_push($security, $controller);
        $this->security = $security;
    }

    /**
     * Configura el idioma de la aplicación
     * 
     * @return void
     */
    function setLocale(): void {
        $this->locale = isset($_GET['locale']) ? $_GET['locale']  : 'esES';
    }
}
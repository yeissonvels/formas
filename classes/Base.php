<?php
/**
 * Clase Base.
 * 
 * author: Yeisson Vélez
 * @version 1.0
 * @since 2024-12-05
 * 
 * Copyright (c) 2025 Yeisson Vélez Salazar
 * Todos los derechos reservados.
 *
 * Este código es propiedad exclusiva de Yeisson Vélez Salazar.
 * Está prohibida la reproducción, distribución o uso no autorizado.
 * Para obtener permisos, contacta con yeisson.velez@gmail.com.
 */
class Base {
    /**
     * @var array $errors Almacena los errores producidos durante las operaciones.
     */
    protected array $errors;

    /**
     * Devuelve los errores producidos
     * 
     * @return array errores producidos
     * 
     */
    public function getErrors(): array {
        return $this->errors;
    }
}
<?php
date_default_timezone_set('Europe/Madrid');

// Libreria de funciones
include('functions.php');

// Cargamos la cabecera
include(VIEWS_PATH_COMMON . 'header' . VIEW_EXT);

// Parte principal del programa
include('controller.php');

// Cargamos el pie de página
include(VIEWS_PATH_COMMON . 'footer' . VIEW_EXT);
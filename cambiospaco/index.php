<?php
include("../functions.php");

// Cargamos la cabecera
include(VIEWS_PATH_COMMON . 'header' . VIEW_EXT);

listDirectory(".");

// Cargamos el pie de página
include(VIEWS_PATH_COMMON . 'footer' . VIEW_EXT);


<?php
set_time_limit(240);
require('functions.php');

error_reporting(0);

if(!is_user_logged_in()) {
    header("location: index.php");
}

/**
 * Paramétros para generar el pdf
 * controller, opt, pdfName, pdfTitel, qrText, waterMark, logo, orientation: P [portrait] (por defecto) | L (landscape)
 * Ejemplo: pdf.php?controller=user&opt=getuserspdf&qrText=textoqr&pdfName=nombrepdf&waterMark=marca-de-agua&logo=url-img
 */

if (isset($_GET['controller'])) {
    $controller = ucfirst($_GET['controller']) . 'Controller';
    if (class_exists($controller)) {
        $controller = new $controller();
        if (isset($_GET['opt'])) {
            $method = $_GET['opt'];
            if (method_exists($controller, $method)) {
                $pdf = setParameters(new PDF());

                //pre($pdf);
                //$pdf->setPdfTitel(str_replace('_', ' ', getGetValue('pdfName')) . ' ' . getYearForTitle());
                //$pdf->setPdfTitel(str_replace('_', ' ', getGetValue('pdfName')));
                $pdf->setPdfContent($controller->$method());
                $pdf->show();
                //$pdf->Output(ABSOLUTE_PATH . "prueba.pdf", 'F');
                //$pdf2 = new PDF();

            } else {
                echo 'Error: no existe el metodo "' . $_GET['opt'] . '" ';
            }

        } else {
            echo 'Error: metodo no puede estar vacio';
        }
    } else {
        echo 'Error: no existe el controlador " ' . $controller . ' " ';
    }
}

/**
 * @param $object
 * @return mixed
 *
 * Setea dinamicamente propiedades a la clase PDF
 */
function setParameters($object) {
    foreach ($_GET as $key => $value) {
        if (property_exists($object, $key)) {
            call_user_func_array(array($object, 'set' . ucfirst($key)), array($value));
        }
    }

    return $object;
}

/**
 * @return int|string
 *
 * Genera un texto con el año al que pertenece la información
 */
function getYearForTitle() {
    $year = getGetValue('year');
    if ($year > 0 ) {
        $year = ' año ' . $year;
    } else {
        $year = ' año ' . date('Y');
    }

    return $year;
}

/**
 * @param $pdf
 *
 * Enable debug mode
 */
function debug($pdf) {
    $pdf->getPdf()->setModeDebug();
}
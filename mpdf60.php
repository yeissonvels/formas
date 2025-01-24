<?php
ini_set('max_execution_time', '300');
ini_set("pcre.backtrack_limit", "15000000");

require('functions.php');
require __DIR__ . '/vendor/autoload.php';

/*error_reporting(E_ALL);
ini_set("display_errors", 1);*/

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
                $html = "";
                $mpdf = new \Mpdf\Mpdf();
                $mpdf->mirrorMargins = 1;
                $mpdf->SetDisplayMode('fullpage','two');
                $html = createHeader();
                $htmlController = $controller->$method();
                $html .= $htmlController;

                $mpdf->setFooter(createFooter());
                $mpdf->setWatermarkText(getWaterMark(), 0.1);
                $mpdf->showWatermarkText = true;

                try {
                    $mpdf->WriteHTML($html);
                } catch (\Mpdf\MpdfException $e) {
                }
                try {
                    $mpdf->Output(getFileName(), 'I');
                } catch (\Mpdf\MpdfException $e) {
                }
                exit;
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

function createHeader() {
    $header = "";
    $newLogo = getLogo();

    //if (isset($_GET['logo']) && $_GET['logo'] != "") {
    if($newLogo !== "") {
        $monthLabel = '';
        $header .= '<div style="margin-bottom: 10px;">';
        $header .=      '<img src="' . $newLogo . '" style="width: 250px;">';
        if (isset($_GET['pdfTitel'])) {
            if (isset($_GET['month'])) {
                if ($_GET['month'] == 'all') {
                    $monthLabel = ' del año';
                } else if ($_GET['month'] > 0) {
                    $monthLabel = ' del mes de ' . getMonth($_GET['month']);
                }
            }
            $header .= '<h4>' . $_GET['pdfTitel'] . $monthLabel . '</h4>';
        }
        $header .= '</div>';
    }

    return $header;
}

function getFileName() {
    if (isset($_GET['pdfName']) && $_GET['pdfName']) {
        return str_ireplace(' ', '_', $_GET['pdfName']) . '.pdf';
    } else {
        return 'Datos.pdf';
    }
}

function createFooter() {
    $footer = '<div style="text-align: center;">Página {PAGENO}</div>';
    $footer .= '<div style="text-align: right;">documento generado el {DATE j-m-Y H:i:s}</div>';

    return $footer;
}

function getWaterMark() {
    if (isset($_GET['waterMark']) && $_GET['waterMark'] != "") {
        return $_GET['waterMark'];
    }
}

function getLogo() {
    return HTTP_HOST . '/images/logo-formas-naranja.png';
}
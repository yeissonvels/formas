<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 30/03/16
 * Time: 13:10
 */
// Permite configurar parámetros adicionales

$config = array(
    // Perfiles de usuario
    'roles' => array(
        1 => array('Standard', 'Estandar', 'Standard'),
        2 => array('Admin', 'Admin', 'Admin'),
        3 => array('SuperAdmin', 'SuperAdmin', 'SuperAdmin'),
    )
);

global $profileTypes;
$profileTypes = array(
    0 => array(0 => 'userstore', 1 => 'Tienda'),
    1 => array(0 => 'userrepository', 1 => 'Distribución'),
    2 => array(0 => 'usermanager', 1 => 'Jefe'),
    3 => array(0 => 'useraccounting',1 => 'Contabilidad' )
);

global $urlTypes;
$urlTypes = array(
    0 => 'show',
    1 => 'new',
    2 => 'delete',
    3 => 'new_item',
    4 => 'new_file',
    5 => 'edit_pdf',
    1000 => 'generic1',
    1001 => 'generic2',
);

global $status;
$status = array(
    0 => 'Pendiente de entrega',
    1 => 'Listo para entregar',
    2 => 'Entregado',
    3 => 'Entregado con incidencia'
);

global $pandingstatus;
$pandingstatus = array(
    0 => 'Pendiente de pago',
    1 => 'Pagado',
);

global $saletypes;
$saletypes = array(
    0 => 'Venta',
    1 => 'Variación',
    2 => 'Entrega a cuenta de venta',
);


global $incidencestatus;
$incidencestatus = array(
    0 => 'Pendiente',
    1 => 'Solucionado',
);

global $incidenceTypes;
$incidenceTypes = array(
    0 => 'Incidencia',
    1 => 'Entrega parcial'
);

global $deliveryRanges;
$deliveryRanges = array(
    0 => '1-15',
    1 => '16-30',
);

global $paymethods;
$paymethods = array(
    0 => 'Tarjeta',
    1 => 'Transferencia',
    2 => 'Metálico',
    3 => 'Crédito aprobado',
    4 => 'Cheque'
);

global $estimateOrigins;
$estimateOrigins = array(
    0 => 'Cliente anterior',
    1 => 'Cliente recomendado',
    2 => 'Cliente nuevo web',
    3 => 'Cliente nuevo publicidad',
    4 => 'Cliente nuevo proximidad'
);

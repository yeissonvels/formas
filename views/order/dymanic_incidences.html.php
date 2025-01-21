<?php if (count($incidences) > 0) {
    global $incidenceTypes;
    global $incidencestatus;
    $deliveryZones = getZones(true);

    $html = "";
    $th = "";
    $tr1 = "";
    $td1 = "";
    $tr2 = "";
    $td2 = "";

    $generatePdf = false;

    $html = '';
    if (isset($_REQUEST['generatepdf'])) {
        $css = getPdfTableStyle();
        $th = $css->th;
        $tr1 = $css->trmodo1;
        $td1 = $css->tdmodo1;
        $tr2 = $css->trmodo2;
        $td2 = $css->trmodo2;
        $generatePdf = true;
        $html = '<div style="font-size: 12px; margin-bottom: 10px; font-weight: bold;">';
        $html .= 'Incidencias ';

        if ($_GET['from'] != "" && $_GET['to'] != "") {
            $html .= ' desde el ' . $_GET['from'] .  ' hasta  el ' . $_GET['to'];
        } else if ($_GET['month'] == "all" && $_GET['year'] == 0) {
            $html .= ' del año ' . date('Y');
        } else if ($_GET['month'] > 0 && $_GET['year'] > 0) {
            $html .= ' de ' . getMonth($_GET['month']). ' del ' . $_GET['year'];
        } else {
            $html .= ' de ' . getMonth(date('m')). ' del ' . date('Y');
        }

        $html .= '</div>';
        $html .= '<table>';
    }
    $html .= '
    <thead>
    <tr>
        <th style="' . $th . '">Id</th>
        <th style="' . $th . '">Fecha</th>
        <th style="' . $th . '">Nº de pedido</th>
        <th style="' . $th . '">Tipo</th>
        <th style="' . $th . '">Descripción</th>
        <th style="' . $th . '">Tienda</th>
        <th style="' . $th . '">Cliente</th>
        <th style="' . $th . '">Pendiente de pago</th>
        <th style="' . $th . '">Estado</th>
        <th style="' . $th . '">Zona de entrega</th>';

    if (!isset($_REQUEST['generatepdf'])) {
        $html .= '<th>Editar</th>';
        $server = $_SERVER['SERVER_NAME'];
        $url = 'mpdf60.php?controller=order&opt=generatePdfIncidences&generatepdf=true' . $incidences[0]->pdfparameters;
        $url .= '&pdfName=Incidencias&logo=http://' . $server . '/images//' . LOGO_PDF . '&orientation=P';
        $url .= '&pdfTitel=&logowidth=150';
        $html .= '<th><a href="' . $url . '" target="_blank">' . icon('pdf', false) . '</a></th>';
    }

    $html .= '
    </tr>
    </thead>
    <tbody>';

    foreach ($incidences as $incidence) {
        $customer = "";
        $id = $incidence->id;
        $trClass = 'table-danger';
        $cancelled = false;

        if ($incidence->incidencetype == 1) {
            $trClass = 'table-info';
        }

        if ($incidence->orderid > 0) {
            $_REQUEST['id'] = $incidence->orderid;
            $orderData = $controller->getOrderData(true);
            $orderLabel = $orderData->getCode();
            $store = $orderData->getStorename();
            $customer = $orderData->getCustomer();
            $deliveryzone = $deliveryZones[$orderData->getDeliveryzone()];
        } else if ($incidence->code != "") {
            $orderData = $controller->getOrderData(true, $incidence->code);
            if ($orderData) {
                $orderLabel = $orderData->getCode();
                $store = $orderData->getStorename();
                $customer = $orderData->getCustomer();
                $deliveryzone = $deliveryZones[$orderData->getDeliveryzone()];
            } else {
                $orderLabel = $incidence->code;
                $store = getStoreName($incidence->store);
                $customer = $incidence->customer;
                $deliveryzone = 'Desconocida';
                if ($incidence->deliveryzone > 0) {
                    $deliveryzone = $deliveryZones[$incidence->deliveryzone];
                }
            }
        }
		
		
        $html .= '<tr class="' . $trClass . '">';
        $html .=    '<td style="' . $td1 . '">' . $id . '</td>';
        $html .=    '<td style="' . $td1 . '">' . americaDate($incidence->incidencedate, false) . '</td>';

        $html .=    '<td style="' . $td1 . '">' . $orderLabel . '</td>';
        $html .=    '<td style="' . $td1 . '">' . $incidenceTypes[$incidence->incidencetype] . '</td>';
        $html .=    '<td style="' . $td1 . '">' . $incidence->description . '</td>';
        $html .=    '<td style="' . $td1 . '">' . $store . '</td>';
        $html .=    '<td style="' . $td1 . '">' . $customer . '</td>';
        $html .=    '<td style="' . $td1 . '">' . numberFormat($incidence->pendingpay, true, 2) . ' &euro;</td>';
        $html .=    '<td style="' . $td1 . '">' . $incidencestatus[$incidence->status] . '</td>';
        $html .=    '<td style="' . $td1 . '">' . $deliveryzone . '</td>';

        if (!$cancelled && !isset($_REQUEST['generatepdf'])) {
            $html .=    '<td style="' . $td1 . '">';
            $html .=        '<a href="' . getUrl('edit_incidence', $controller->getUrls(), $id) . '" >' . icon('edit', false) . '</a>';
            $html .= '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    if (isset($_REQUEST['generatepdf'])) {
        $html .= '</table>';
    }
    if (isset($_REQUEST['generatepdf'])) {
        return $html;
    } else {
        echo $html;
    }

} else {
    errorMsg("No se han encontrado incidencias");
}
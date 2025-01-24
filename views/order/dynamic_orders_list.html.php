<?php
$html = "";
$th = "";
$tr1 = "";
$td1 = "";
$tr2 = "";
$td2 = "";

$generatePdf = false;

if (isset($_GET['generatepdf'])) {
    $css = getPdfTableStyle();
    $th = $css->th;
    $tr1 = $css->trmodo1;
    $td1 = $css->tdmodo1;
    $tr2 = $css->trmodo2;
    $td2 = $css->trmodo2;
    $generatePdf = true;
}


if ($data) {
    $server = $_SERVER['SERVER_NAME'];
    $url = 'mpdf60.php?controller=order&opt=dynamic_orders_list&generatepdf=true' . $data[0]->getPdfparameters();
    $url .= '&pdfName=Listado de pedidos&logo=http://' . $server . '/images//' . LOGO_PDF . '&orientation=P';
    $monthLabel = "";
    if (isset($_REQUEST['month']) && $_REQUEST['month'] > 0) {
        $monthLabel = ' de ' . getMonth($_REQUEST['month']);
    }

    $url .= '&pdfTitel=Listado de pedidos ' . $monthLabel . '&logowidth=150';

if (isset($_GET['generatepdf'])) {
    $html .= '<table>';
}

$html .= '<thead>';

$html .= '
<tr style="' . $tr1 . '">
    <th style="' . $th . '">Código</th>';

if (!$generatePdf) {
    $html .= '<th>' . icon('pdf', false) . '</th>';
}

$html .= '
    <th style="' . $th . '">Cliente</th>
    <th style="' . $th . '">Teléfono</th>
    <th style="' . $th . '">Tienda</th>
    <th style="' . $th . '">Fecha de compra</th>
    <th style="' . $th . '">Entrega</th>
    <th style="' . $th . '">Mes</th>
    <th style="' . $th . '">Estado</th>
    <th style="' . $th . '">Total</th>
     <th style="' . $th . '">Pendiente de pago</th>
    <th style="' . $th . '">' . ($generatePdf ? 'Estado de Pago' : icon('money', false)) . '</th>
    <th style="' . $th . '">Zona</th>';

if (!isset($_GET['generatepdf'])) {
    $html .= '<th style="' . $th . '">Gestionar</th>';
    $html .= '<th><a href="' . $url . '" target="_blank">' . icon('pdf') . '</a></th>';
}

$html .= '
</tr>';
$html .= '</thead>
            <tbody>';

    global $deliveryRanges;
    $zones = getZones(true);
    $total = 0;
    $i = 0;
    foreach ($data as $order) {
        $cancelled = false;
        $trClass = '';
        $tdStyle = '';
        if ($order->getCancelled() == 1) {
            $cancelled = true;
            $trClass = 'class="deleted"';
        }

        if ($generatePdf && $order->getCancelled() == 1) {
            $tdStyle = 'text-decoration: line-through;';
        }

        if (!$cancelled) {
            $total += $order->getTotal();
        }

        if ($order->getPdfname() != "") {
            $url = "/uploaded-files/pdfs/" . $order->getPdfid() . "/" . $order->getPdfname();
            $pdf =  '<a class="cursor-pointer" onclick="openUrlInWindow(\'' . $url . '\');">' . icon('pdf', false) . '</a>';
        }
        $html .= '
        <tr style="' . $tr1 . '" '. $trClass .'>
            <td style="' . $td1 . $tdStyle . '">' . $order->getCode() . '</td>';
        if (!$generatePdf) {
            $html .= '<td>' . $pdf . '</td>';
        }

        $html .= '<td style="' . $td1 . $tdStyle . '">' . $order->getCustomer() . '</td>
            <td style="' . $td1 . $tdStyle . '">' . $order->getTelephone() . '</td>
            <td style="' . $td1 . $tdStyle . '">' . $order->getStorename() . '</td>
            <td style="' . $td1 . $tdStyle . '">' . americaDate($order->getPurchasedate(), false) . '</td>
            <td style="' . $td1 . $tdStyle . '">' . $deliveryRanges[$order->getDeliveryrange()] . '</td>
            <td style="' . $td1 . $tdStyle . '">' . getMonth($order->getDeliverymonth()) . '</td>
            <td style="' . $td1 . '">';

            if (!$cancelled) {
                $status = $order->getStatus();
                $deliveryDate = americaDate($order->getDeliverydate(), false);
                if ($status == 0) {
                    $msg = "Pendiente de entrega " . (!isset($_GET['generatepdf']) ? icon('pending', false) : '');
                } else if ($status == 1) {
                    $msg = "Listo para entregar " . (!isset($_GET['generatepdf']) ? icon('truck', false) : '');
                } else if ($status == 2) {
                    $msg = "Entregado " . (!isset($_GET['generatepdf']) ? icon('delivered', false) . " " . $deliveryDate : '');
                } else if ($status == 3) {
                    $msg = "Entregado con incidencia " . (!isset($_GET['generatepdf']) ? icon('delivered', false) . " " . $deliveryDate : '');
                }
            } else {
                $msg = "Anulado";
            }

        $html .= $msg;
        $html .=    '</td>';
        $html .=    '<td style="' . $td1 . $tdStyle . '">' .  numberFormat($order->getTotal(), '.', 2) . ' &euro;</td>';
        $html .=    '<td style="' . $td1 . $tdStyle . '">' .  numberFormat($order->getPendingpay(), '.', 2) . ' &euro;</td>';
        $html .=    '<td style="' . $td1 . $tdStyle . '">' . ($order->getPendingstatus() == 0 ? 'No pagado' : 'Pagado') . '</td>';
        $html .=    '<td style="' . $td1 . $tdStyle . '">' . $zones[$order->getDeliveryzone()] . '</td>';
        if (!$generatePdf && !$cancelled) {
            $html .= '<td style="' . $td1 . '" class="text-center"><a href="?controller=order&opt=new_order&id=' . $order->getId() . '"' . icon('edit', false) . '</a></td>';
        }
        $html .= '</tr>';
        $i++;
    }

    $html .= '<tr style="' . $tr1 . '">';
    $html .=    '<td style="' . $td1 . '" colspan="' . (!$generatePdf ? 8 : 7) . '"><b>Total resultados: ' . $i . '</b></td>';
    $html .=    '<td style="' . $td1 . '"><b>TOTAL</b></td>';
    $html .=    '<td style="' . $td1 . '" colspan="' . (!$generatePdf ? 2 : 3) . '"><b>' . numberFormat($total, '.', 2) . ' &euro;</b></td>';
    $html .= '</tr>';

    $html .= '</tbody>';
    if (isset($_GET['generatepdf'])) {
        $html .= '</table>';
    }

} else {
    $html = errorMsg('Sin resultados', false);
}

return $html;

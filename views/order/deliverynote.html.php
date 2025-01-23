<?php
$html = '<div style="margin-bottom: 10px; font-size: 10px;">';
$html .=        '<div style="width: 49%; float: left;">';
$html .=            '&nbsp;';
$html .=        '</div>';
$html .=        '<div style="width: 49%; float: left; text-align: right;">';
$html .=         '<b>FORMAS ' . strtoupper($store->getStorename()) . '</b><br>';
$html .=          $store->getAddress() . '<br>';
$html .=          $store->getCp() . ' ' . $store->getCity() . '<br>';
$html .=          'Tel: ' . $store->getTelephone() . ($store->getFax() != "" ? ' Fax: ' . $store->getFax() : "") . '<br>';
$html .=          'CIF: ' . $store->getCif() . ' <br>';
$html .=           $store->getEmail() . '<br>';
$html .=           $store->getWeb() . '<br>';
$html .=        '</div>';
$html .= '</div>';

$html .=  '<div style="font-size: 12px; margin-bottom: 20px;">';
$html .=    '<div style="width: 60%; float: left;">';
$html .=        '<div style="margin-bottom: 10px;">';
$html .=            '<b>Nota de entrega</b>';
$html .=        '</div>';
$html .=        '<div>';
$html .=            '<div style="width: 30%; float: left;"><b>Nº de nota de entrega: </b></div>';

$incitype = $incidence->incidencetype == 0 ? '-INCIDENCIA' : '-PARCIAL';

if ($order) {
    $code = $order->getCode();
} else {
	if ($incidence->code != "") {
		$code = $incidence->code;
	} else {
		$code = $incidence->id;
	}
    
}

$html .=            '<div style="width: 69%; float: left;">' . strtoupper($code) . $incitype . '</div>';
$html .=        '</div>';
$html .=        '<div>';
$html .=            '<div style="width: 30%; float: left;"><b>Fecha: </b></div>';
$html .=            '<div style="width: 69%; float: left;">' . americaDate($incidence->deliverydate, false) . '</div>';
$html .=        '</div>';
$html .=        '<div>';
$html .=            '<div style="width: 30%; float: left;"><b>Le atendió: </b></div>';
$html .=            '<div style="width: 69%; float: left;">' . strtoupper(clearStringToUpper($incidence->seller)) . '</div>';
$html .=        '</div>';
$html .=        '<div>';
$html .=            '<div style="width: 30%; float: left;"><b>Montador: </b></div>';
$html .=            '<div style="width: 30%; float: left;">' . strtoupper(clearStringToUpper($incidence->assembler)) . '</div>';
$html .=        '</div>';
$html .=    '</div>';

if ($order) {
    $customer = $order->getCustomer();
    $tel = $order->getTelephone();
    $tel2 = $order->getTelephone2();
    $email = $order->getEmail();
} else {
    $customer = $incidence->customer;
    $tel = $incidence->telephone;
    $tel2 = $incidence->telephone2;
    $email = $incidence->email;
}

$html .=     '<div style="width: 30%; float: left;">';
$html .=        '<b>' . strtoupper(clearStringToUpper($customer)) . '</b><br>';
$html .=        'DNI: ' . strtoupper($incidence->dni) . '<br>';
$html .=        strtoupper(clearStringToUpper($incidence->address)) . '<br>';
$html .=        $incidence->cp . ' ' . strtoupper(clearStringToUpper($incidence->city)) . '<br>';
$html .=        strtoupper(getProvinceName($incidence->provinceid)) . '<br>';
$html .=        'Tel: ' . $tel . ($tel2 != "" ? " // " . $tel2 : '' ) . '<br>';
$html .=        $email != "" ? ('Email: ' . strtoupper($email) . '<br>') : "";
$html .=      '</div>';
$html .=  '</div>';

$html .=    '<table style="font-size: 12px; border-collapse: collapse; width: 100%;">';
$html .=        '<tr style="background: gainsboro;">';
$html .=            '<th style=" padding: 5px;">Ref.</th>';
$html .=            '<th>Descripción</th>';
$html .=            '<th>Cant.</th>';
$html .=            '<th>Precio</th>';
$html .=            '<th>Dto(%)</th>';
$html .=            '<th>Importe</th>';
$html .=        '</tr>';

$items = $incidence->items;
$total = 0;
$iva = 21;

foreach ($items as $item) {
    $rowTotal = $item->price * $item->quantity;
    $discount = $item->discount;
    if ($discount > 0) {
        $rowTotal = $rowTotal - (($rowTotal/100)*$discount);
    }

    $total += $rowTotal;

    $html .=        '<tr>';
    $html .=            '<td>' . $item->reference . '</td>';
    $html .=            '<td>' . $item->description . '</td>';
    $html .=            '<td>' . $item->quantity . '</td>';
    $html .=            '<td align="center">' . numberFormat($item->price, true, 2) . '</td>';
    $html .=            '<td align="center">' . $discount . '</td>';
    $html .=            '<td align="right">' . numberFormat($rowTotal, true, 2)  . '</td>';
    $html .=        '</tr>';

}

$valueIva = (($total/100)*21);

$totalWithIva = $total + $valueIva;

$html .=        '<tr>';
$html .=            '<td colspan="6"><hr></td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="3"></td>';
$html .=            '<td><b>Importe</b></td>';
$html .=            '<td></td>';
$html .=            '<td align="right">' . numberFormat($total, true, 2) . ' &euro;</td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="6"></td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="3"></td>';
$html .=            '<td><b>Base</b></td>';
$html .=            '<td></td>';
$html .=            '<td align="right">' . numberFormat($total, true, 2) . ' &euro;</td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="3"></td>';
$html .=            '<td><b>IVA</b></td>';
$html .=            '<td align="center">' . $iva . ' %</td>';
$html .=            '<td align="right">' . numberFormat($valueIva, true, 2) . ' &euro;</td>';
$html .=        '</tr>';
$html .=        '<tr>';
$html .=            '<td colspan="3"></td>';
$html .=            '<td style="background: gainsboro; border: 1px solid black; border-right: none; padding: 5px;"><b>Total pedido</b></td>';
$html .=            '<td style="background: gainsboro; border: 1px solid black; border-left: none; border-right: none;"></td>';
$html .=            '<td align="right" style="background: gainsboro; gainsboro; border: 1px solid black; border-left: none">' . numberFormat($totalWithIva, true, 2) . ' &euro;</td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="3"></td>';
$html .=            '<td></td>';
$html .=            '<td></td>';
$html .=            '<td align="right">Iva incluido</td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="6">&nbsp;</td>';
$html .=        '</tr>';
$html .=        '<tr>';
$html .=            '<td colspan="6">&nbsp;</td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="3"></td>';
$html .=            '<td style="border: 1px solid black; border-right: none; padding: 5px;"><b>Total entregado</b></td>';
$html .=            '<td align="center" style="border: 1px solid black; border-left: none; border-right: none;"></td>';
$html .=            '<td align="right" style="border: 1px solid black; border-left: none;">' . numberFormat(0) . ' &euro;</td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="3"></td>';
$html .=            '<td style="background: gainsboro; border: 1px solid black; border-right: none; padding: 5px;"><b>Pendiente de pago</b></td>';
$html .=            '<td style="background: gainsboro; border: 1px solid black; border-left: none; border-right: none;"></td>';
$html .=            '<td align="right" style="background: gainsboro; gainsboro; border: 1px solid black; border-left: none:">' .  numberFormat($totalWithIva, true, 2) . ' &euro;</td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="6">&nbsp;</td>';
$html .=        '</tr>';
$html .=        '<tr>';
$html .=            '<td colspan="6"><hr></td>';
$html .=        '</tr>';

$html .=        '<tr>';
$html .=            '<td colspan="6"><b>Observaciones:</b> ' . strtoupper(clearStringToUpper($incidence->observations)) . ' </td>';
$html .=        '</tr>';

$html .=    '</table>';

$html .= '<div style="margin-top: 20px; font-size: 12px;">';
$html .=    '<div style="float: left; width: 49%; text-align: center;">';
$html .=        '<div><b>MONTADOR</b></div>';
$html .=        '<div style="margin-top: 60px;">LA MERCANCIA ESTA ENTREGADA Y MONTADA<br>CORRECTAMENTE</div>';
$html .=    '</div>';
$html .=    '<div style="float: left; width: 49%; text-align: center;">';
$html .=        '<div><b>CONFORME</b></div>';
$html .=        '<div style="margin-top: 60px;">ACEPTO GARANTÍA Y CONDICIONES AL DORSO</div>';
$html .=    '</div>';
$html .= '</div>';

/*echo $html;
exit;*/

?>
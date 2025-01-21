<?php if (count($data) > 0) {
    global $user;
    //pre($data);

    $html = '<div style="font-size: 12px; margin-bottom: 10px;">';
    $html .= 'Ventas ';
	if ($_GET["commission"] == "yes") {
		$html .= ' con propuesta ';
	}
    if ($_GET['user'] != "") {
        $html .= 'del usuario ' . getUsername($_GET['user']);
    }

    if ($_GET['from'] != "" && $_GET['to'] != "") {
        $html .= ' desde el ' . $_GET['from'] .  ' hasta  el ' . $_GET['to'];
    } else if ($_GET['month'] == "all" && $_GET['year'] == 0) {
        $html .= ' del año ' . date('Y');
    } else if ($_GET['month'] > 0 && $_GET['year'] > 0) {
        $html .= ' de ' . getMonth($_GET['month']). ' del ' . $_GET['year'];
    } else {
        $html .= ' de ' . getMonth(date('m')). ' del ' . date('Y');
    }

    if ($_GET['store'] != "") {
        $html .= '<div>Tienda ' . getStoreName($_GET['store']) . ' </div>';
    }

    $html .= '</div>';
    $html .= '<table style="font-size: 10px; border-collapse: collapse;">
<thead>
<tr style="background: gainsboro;">
    <th>Tipo</th>
    <th>Fecha de venta</th>
    <th>Nº de pedido</th>
    <th>Titular del pedido</th>
    <th>Importe Total</th>
    <th>Abonado a cuenta</th>
    <th>Mediante</th>
    <th>Creado el</th>
    <th>Creado por</th>
    <th>Tienda</th>
</tr>
</thead>
<tbody>';

    $i = 0;
    $total = 0;
    $totalNegative = 0;
    $totalPositive = 0;
    $totalPays = 0;

    global $saletypes;
    global $paymethods;

    foreach ($data as $pdf) {
        $deleted = '';
        $cancelled = false;
        if ($pdf->cancelled == 1) {
            $cancelled = true;
        }
        $deleted = ($cancelled ? ' style="text-decoration: line-through;"' : '');

        $parent = array();
        if ($pdf->saletype > 0) {
            $parent = $this->getPdfData($pdf->parentcode);
        }

        $trClass = 'class="table-success"';
        if (!$cancelled) {
            if ($pdf->saletype == 0) {
                $total += $pdf->total;
            } else {
                if ($pdf->total < 0) {
                    $totalNegative += $pdf->total;
                } else {
                    $totalPositive += $pdf->total;
                }
            }

            $totalPays += $pdf->payed;
        }

        $html .=  $pdf->payed . "<br>";

        if ($pdf->saletype == 1 && $pdf->total > 0) {
            $trClass = 'class="table-success"';
        } else if ($pdf->saletype == 1 && $pdf->total < 0) {
            $trClass = 'class="table-danger"';
        } else if ($pdf->saletype == 2) {
            $trClass = 'class="table-info"';
        }

        $html .= '<tr ' . $trClass . '>
        <td ' . $deleted . '>';
        $icon = 'money';
        if ($pdf->saletype == 1) {
            $icon = 'exchange';
        }
        $html .= $saletypes[$pdf->saletype] . ' ' . icon($icon, false);

        $html .= '</td>';
        $html .= '<td ' . $deleted . '>' . americaDate($pdf->saledate, false) . '</td>';
        $html .= '<td ' . $deleted . '>';

        if ($pdf->saletype == 0) {
            $html .= $pdf->code;
        } else {
            $html .= $parent->code;
        }
        $html .= '
            
        </td>
        <td ' . $deleted . '>';
        if ($pdf->saletype == 0) {
            $html .= $pdf->customer;
        } else {
            $html .= $parent->customer;
        }

        $html .= '
        </td>
        <td ' . $deleted . '>';

        if ($pdf->saletype != 2) {
            $html .= numberFormat($pdf->total, true, 2) . " &euro;";
        }
        $html .= '          
        </td>
        <td ' . $deleted . '>' . numberFormat($pdf->payed, true, 2) . ' &euro;</td>
        <td ' . $deleted . '>';
        if ($pdf->saletype == 0) {
            $html .= $paymethods[$pdf->paymethod];
        } else {
            if ($pdf->payed > 0) {
                $html .= $paymethods[$pdf->paymethod];
            } else {
                $html .= "----------";
            }
        }

        $html .= '</td>';
        $html .= '<td ' . $deleted . '>' . americaDate($pdf->created_on) . '</td>';
        $html .= '<td ' . $deleted . '>';

        if (empty($_POST['code'])) {
            $html .=  $pdf->username;
        } else {
            $html .= getUsername($pdf->created_by);
        }

        $html .= '</td>';
        $html .= '<td class="text-center" ' . $deleted . '>' . getStoreName($pdf->storeid) . '</td>
        
    </tr>';
        $i++;
    }

    $totalReal = $total + $totalPositive + $totalNegative;

    $totalPending = $totalReal - $totalPays;
    if ($totalPending > 0) {
        $totalPending = numberFormat($totalReal - $totalPays, true, 2);
    } else {
        $totalPending = 0;
    }

    $colspan = 2;
    $html .= '
<tr>
    <td colspan="4"></td>
    <td class="bold">Abonos</td>
    <td class="bold">' . numberFormat($totalPays, true, 2) . ' &euro;</td>
</tr>


<tr>
    <td colspan="' . $colspan . '"><b>Total ventas (sin variaciones): </b></td>
    <td colspan="2" align="right"><b>' . numberFormat($total, true, 2) . ' &euro;</b></td>
    <td colspan="8">&nbsp;</td>
</tr>
<tr>
    <td colspan="' . $colspan . '"><b>Variaciones positivas: </b></td>
    <td colspan="2" align="right" class="green-color"><b>' . numberFormat($totalPositive, true, 2) . ' &euro;</b></td>
    <td colspan="8">&nbsp;</td> 
</tr>
<tr>
    <td colspan="' . $colspan . '"><b>Variaciones negativas</b></td>
    <td colspan="2" align="right" class="red-color"><b>' . numberFormat($totalNegative, true, 2) . ' &euro;</b></td>
    <td colspan="8">&nbsp;</td>
</tr>
<tr>
    <td colspan="' . $colspan . '"><b>TOTAL REAL</b></td>
    <td colspan="2" align="right"><b>' . numberFormat($totalReal, true, 2) . ' &euro;</b></td>
    <td colspan="8">&nbsp;</td>
</tr>

<tr>
    <td colspan="' . $colspan . '"><b>PENDIENTE DE PAGO</b></td>
    <td colspan="2" align="right"><b>' . $totalPending . ' &euro;</b></td>
    <td colspan="8">&nbsp;</td>
</tr>

<tr>
    <td colspan="9"><b>Resultados: ' . $i . '</b></td>
</tr>
</tbody>';
} else {
    $html .= '<tr><td>Sin resultados</td></tr>';
}
$html .= '</table>';

/*echo $html;
exit;*/
?>

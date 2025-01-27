<table class="table" id="myLastEstimates">
    <thead>
        <tr>
            <th>Nº presupuesto</th>
            <th>Fecha de presupuesto</th>
            <th>Cliente</th>
            <th>Teléfono</th>
            <th>Total</th>
        </tr>
    </thead>
   <tbody>
        <?php
            $html = ""; 
            if($estimates) {
                foreach($estimates as $estimate) {
                    // Convertir el objeto estimate a JSON
                    $code = json_encode($estimate);
                    // Escapar caracteres especiales
                    $code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
                    // Crear la función setEstimate con el código JSON
                    $fnc = "setEstimate($code);";
                    $html .= '<tr>';
                    //$html .=    '<td><a href="#" onclick=\''.$fnc.'\'>'.$estimate->code.'</a></td>';
                    $html .=    '<td>'.$estimate->code.'</a></td>';
                    $html .=    '<td>'.americaDate($estimate->saledate, false).'</td>';
                    $html .=    '<td>'.$estimate->customer.'</td>';
                    $html .=    '<td>'.$estimate->tel.'</td>';
                    $html .=    '<td>'.numberFormat($estimate->total, true, 2).' €</td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr class="table-warning"><td colspan="5">¡No se han encontrado presupuestos pendientes!</td></tr>';
            }
            echo $html;
            
        ?>
   </tbody>
</table>
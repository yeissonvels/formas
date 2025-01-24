<table class="table">
    <thead>
        <tr>
            <th>Nº presupuesto</th>
            <th>Fecha de presupuesto</th>
            <th>Cliente</th>
            <th>Total</th>
        </tr>
    </thead>
   <tbody>
        <?php
            $html = ""; 
            if($estimates) {
                foreach($estimates as $estimate) {
                    $code = $estimate->code;
                    $fnc = "setEstimate('$code');";
                    $html .= '<tr>';
                    $html .=    '<td><a href="#" onclick="'.$fnc.'">'.$estimate->code.'</a></td>';
                    $html .=    '<td>'.americaDate($estimate->saledate, false).'</td>';
                    $html .=    '<td>'.$estimate->customer.'</td>';
                    $html .=    '<td>'.numberFormat($estimate->total, true, 2).' €</td>';
                    $html .= '</tr>';
                }
            } else {

            }
            echo $html;
        ?>
   </tbody>
</table>
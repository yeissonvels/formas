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
                    $html .= '<tr>';
                    $html .=    '<td>'.$estimate->code.'</td>';
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
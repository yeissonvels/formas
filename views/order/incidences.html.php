<?php
if (isset($data['data']) && is_object($data['data'])){
    $incidences = $data['data']->getIncidences();
    $htmlincidences = '   <tr>';
    $htmlincidences .= '       <th>Fecha</th>';
    $htmlincidences .= '       <th>Tipo</th>';
    $htmlincidences .= '       <th>Descripción</th>';
    $htmlincidences .= '       <th>Pendiente de pago</th>';
    $htmlincidences .= '       <th>Creado por</th>';
    $htmlincidences .= '       <th>Estado</th>';
    $htmlincidences .= '       <th>Gestionar</th>';
    $htmlincidences .= '   </tr>';
    global $incidencestatus;
    foreach ($incidences as $incidence) {
        $htmlincidences .= '<tr>';
        $htmlincidences .= '<td>' . americaDate($incidence->incidencedate, false) . '</td>';
        $htmlincidences .= '<td>' . ($incidence->incidencetype == 0 ? 'Incidencia' : 'Entrega parcial') . '</td>';
        $htmlincidences .= '<td>' . $incidence->description . '</td>';
        $htmlincidences .= '<td>' . numberFormat($incidence->pendingpay, true, 2) . ' &euro;</td>';
        $htmlincidences .= '<td>' . $incidence->username . '</td>';
        $htmlincidences .= '<td>' . $incidencestatus[$incidence->status] . ' ' . americaDate($incidence->fixed_on, false) . '</td>';
        $htmlincidences .= '<td class="text-center">';
        $htmlincidences .= '<a class="cursor-pointer" onclick="editIncidence(' . $incidence->id . ', ' . $incidence->orderid . '); $(\'#editinci-lk\').trigger(\'click\');">' . icon('edit', false) . '</a>';
        $htmlincidences .= '</td>';
        $htmlincidences .= '</tr>';
    }

} else {
    $htmlincidences = "";
}

?>
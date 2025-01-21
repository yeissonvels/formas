<?php
if (count($data) > 0) {
    $html = '<thead>';
    $html .=    '<tr>';
    $html .=        '<th>E-mail</th>';
    $html .=        '<th>Producto</th>';
    $html .=        '<th>Fecha de compra</th>';
    $html .=        '<th><a href="/download.php?opt=cus_emails" target="_blank">' . icon('word', false) . '</a></th>';
    $html .=    '</tr>';
    $html .= '</thead>';

    $txt = "CORREOS DE CLIENTES QUE HAN COMPRADO " . $data[0]->category . PHP_EOL . PHP_EOL;
    foreach ($data as $email) {
        $cusEmail = $email->email;
        $txt .= $cusEmail . PHP_EOL;
        $html .= '<tr>';
        $html .=    '<td>' . $cusEmail . '</td>';
        $html .=    '<td>' . $email->category . '</td>';
        $html .=    '<td>' . americaDate($email->purchasedate, false) . '</td>';
        $html .= '</tr>';
    }

    file_put_contents(CUSTOMER_EMAILS, $txt);

} else {
    $html = errorMsg('Sin resultados', false);
}
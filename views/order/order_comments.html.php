<?php
if ($data['data'] && count($data['data']->getComments()['interns']) > 0) {
    $comments = $data['data']->getComments();;
    $interns = $comments['interns'];
    foreach ($interns as $intern) {
        $trclass = "";
        $extraIcon = "";
        if ($intern->readydelivery == 1) {
            $trclass = 'class="table-info"';
            $extraIcon = icon('truck', false);
        } else if ($intern->delivered == 1) {
            $trclass = 'class="table-success"';
            $extraIcon = icon('delivered', false);
        }

        $usercomment = $intern->username;
        $commentDate = americaDate($intern->created_on, true);
        $comment = $intern->comment;
        echo '<tr ' . $trclass .'>';
        echo    '<td>';
        echo        '<b>' . $usercomment . "</b> " . icon('calendar', false) . "(" . $commentDate . ") " . $extraIcon . ": $comment";
        echo    '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr>';
    echo    '<td>';
    warningMsg('No existen comentarios!', true, false);
    echo    '</td>';
    echo '</tr>';
}
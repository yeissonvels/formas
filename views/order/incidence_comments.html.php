<?php
if (isset($incidenceComments)) {
    foreach ($incidenceComments as $comment) {
        $trclass = "";
        $extraIcon = "";

        /*if ($comment->readydelivery == 1) {
            $trclass = 'class="table-info"';
            $extraIcon = icon('truck', false);
        } else if ($comment->delivered == 1) {
            $trclass = 'class="table-success"';
            $extraIcon = icon('delivered', false);
        }*/

        $usercomment = $comment->username;
        $commentDate = americaDate($comment->created_on, true);
        $comment = $comment->comment;
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
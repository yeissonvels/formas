<?php
if ($data['data'] && count($data['data']->getComments()['customer']) > 0) {
    $comments = $data['data']->getComments();;
    $interns = $comments['customer'];
    foreach ($interns as $intern) {
        $usercomment = $intern->username;
        $commentDate = americaDate($intern->created_on, true);
        $comment = $intern->comment;
        echo '<tr>';
        echo    '<td>';
        echo        '<b>' . $usercomment . "</b> " . icon('calendar', false) . "(" . $commentDate . "): $comment";
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
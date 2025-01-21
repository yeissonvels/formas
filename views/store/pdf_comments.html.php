<?php
if ($data && count($data->comments) > 0) {
    $comments = $data->comments;
    foreach ($comments as $intern) {
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
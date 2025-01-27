<?php
global $user;

$html = "";

if ($user->getUserRepository() == 1 || $user->getUsermanager() == 1 || isadmin()) {
    if (isset($_GET['opt']) && FORM_CONTROLLER  == "order" && $_GET['opt'] == "new_order") {
        $html .= '<div class="row text-center">' . PHP_EOL;
        $html .= '<div class="col-lg-1">' . PHP_EOL;
        $html .= '<a style="color: #000 !important;" data-bs-target="#customerComments" data-bs-toggle="modal" class="withqtip cursor-pointer" id="cu-com-lk">';
        $html .= icon('phone', false) . PHP_EOL;
        $html .= '</a>';
        $html .= '</div>' . PHP_EOL;
        $html .= '<div class="col-lg-1">' . PHP_EOL;
        $html .= '<a style="color: #000 !important;" data-bs-target="#ourComments" data-bs-toggle="modal" class="withqtip cursor-pointer" title="" id="our-com-lk">';
        $html .= icon('comments', false) . PHP_EOL;
        $html .= '</a>';
        $html .= '</div>' . PHP_EOL;
        $html .= '<div class="col-lg-1" id="lk-incidence">' . PHP_EOL;
        $html .= '<a style="color: #000 !important;" data-bs-target="#incidences" data-bs-toggle="modal" class="withqtip cursor-pointer" title="" id="incide-lk">';
        $html .= icon('incidence', false) . PHP_EOL;
        $html .= '</a>';
        $html .= '</div>' . PHP_EOL;
        $html .= '</div>' . PHP_EOL;
        return $html;
    }
}

return $html;

?>
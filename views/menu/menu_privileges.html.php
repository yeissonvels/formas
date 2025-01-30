<style>
    #menu2 {
        margin: 10px;
        border: 1px solid silver;
    }

    #menu2 ul {
        list-style-type: none;
        margin: 0px;
        padding: 0px;
        /*width: 200px;*/
        font-family: Arial, sans-serif;
        font-size: 11pt;
    }

    #menu2 ul li {
        color: black;
        text-decoration: none;
        text-transform: uppercase;
        display: block;
        padding: 5px 10px 0px 20px;
    }

    #menu2 ul li:first-child {
        /*font-weight: bold;*/
    }

</style>

<script>
    function savePermissions() {
        var data = [];
        var op = 'saveMenuPermissions';
        var controller = 'MenuController';

        $("#frm2").find(':checkbox:checked').each(function() {
            data.push($(this).val());
        });

        $.ajax({
            url: '/ajax.php',
            method: 'post',
            data: {
                ids: data,
                op: op,
                controller: controller
            }
        }).done(function(response) {
            alert('¡Permisos actualizados!');
            console.log(response);
        });

    }
</script>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Permisos de usuario (para el menú principal)</h4>
    </div>
    <div class="card-block">
    <?php

    global $profileTypes;
    $selectedLang = getLangGetLabelFunction();

    $html = '<div id="menu2">' . PHP_EOL;
    $html .= '<form method="POST" id="frm2">';
    $html .=    '<ul>' . PHP_EOL;
    foreach ($data["items"] as $it) {
       $html .= getRecursiveOptions($it, $selectedLang, $data["permissions"]);
    }
    $html .=    '</ul>' . PHP_EOL;
    $html .= '</form>';
    $html .= '</div>' . PHP_EOL;
    echo $html;

    function getRecursiveOptions($item, $selectedLang, $permissions) {
        global $profileTypes;
        $html = '';
        $checked = '';
        $id = $item->getId();

        if(count($item->getChilds()) > 0) {
            $label = $item->$selectedLang() != '' ? $item->$selectedLang() : $item->getLabel();
            $html .= '<li>';
            $html .=    "<b>$label</b>" . PHP_EOL;
            for ($i = 0; $i < count($profileTypes); $i++) {
                if (isset($permissions->$id)) {
                    if (in_array($profileTypes[$i][0], $permissions->$id)) {
                        $checked = 'checked="checked"';
                    }
                }
                $checkValue = $id . "#" . $profileTypes[$i][0];
                $html .= '&emsp;<input type="checkbox"  class="form-check-input" value="' . $checkValue . '" ' . $checked . '>';
                $html .= " " . $profileTypes[$i][1] . "&emsp;";
                $checked = "";
            }
            $html .= '<ul>';
            if (count($item->getChilds()) > 0) {
                foreach ($item->getChilds() as $subLev) {
                    $html .= getRecursiveOptions($subLev, $selectedLang, $permissions);
                }
            }
            $html .= '</ul>' . PHP_EOL;
            $html .= '</li>' . PHP_EOL;

        } else {
            $label = $item->$selectedLang() != '' ? $item->$selectedLang() : $item->getLabel();
            $html .= '<li>' . PHP_EOL;
            $html .=    (($item->label == "Inicio") ? "<b>$label</b>" : $label) . PHP_EOL;
            for ($i = 0; $i < count($profileTypes); $i++) {
                if (isset($permissions->$id)) {
                    if (in_array($profileTypes[$i][0], $permissions->$id)) {
                        $checked = 'checked="checked"';
                    }
                }
                $checkValue = $id . "#" . $profileTypes[$i][0];
                $html .= '&emsp;<input type="checkbox" class="form-check-input" value="' . $checkValue . '" ' . $checked . '>';
                $html .= " ". $profileTypes[$i][1] . "&emsp;";
                $checked = "";
            }
            $html .= '</li>' . PHP_EOL;
        }

        return $html;
    }
    ?>
    </div>
    <div class="card-footer text-muted">
        <button type="submit" class="btn btn-primary" onclick="savePermissions();">Guardar permisos</button>
    </div>
</div>

<script>
    function savePermissions() {
        var data = [];
        var userid = '<?php echo $_GET['id']; ?>';
        var createdby = '<?php global $user; echo $user->getId();?>';
        var op = 'saveUserPermissions';
        var controller = 'MenuController';

        $("#frm2").find(':checkbox:checked').each(function() {
            data.push($(this).val());
        });

        $.ajax({
            url: 'ajax.php',
            method: 'post',
            data: {
                ids: data,
                userid: userid,
                createdby: createdby,
                op: op,
                controller: controller
            }
        }).done(function(response) {
            alert('¡Permisos actualizados!');
            console.log(response);
        });

    }
</script>
<h3>Permisos de usuario (para el menú principal)</h3>

<div>
    <form method="POST" id="frm2">
    <?php
    //pre($items);
    //pre($permissions);
    // Usamos la variable $getLabelFunction que contiene el nombre de la función get que obtendrá el label del menú
    foreach ($items as $it) {
        $checked = "";
        if (in_array($it->getId(), $permissions)) {
            $checked = 'checked="checked"';
        }
        $check = '<span>';
        $check .= '<input type="checkbox" value="' . $it->getId() . '" '. $checked . '>';
        $check .= '<b>' . $it->$getLabelFunction() . '</b></span> &emsp;<span style="color: #2EA2CC;">(' . $it->getLink() . ')</span><br>';
        echo $check;
        getRecursiveOptions($it, $getLabelFunction, $permissions);
    }

    function getRecursiveOptions($items, $getLabelFunction, $permissions) {
        foreach ($items->getChilds() as $it) {
            $checked = "";
            if (in_array($it->getId(), $permissions)) {
                $checked = 'checked="checked"';
            }

            $check = '&emsp;<span title="' . $it->getLink() . '">';
            $check .= '<input type="checkbox" value="' . $it->getId() . '" '. $checked . '>';
            $check .= $it->$getLabelFunction() . '</span> &emsp;<span style="color: #2EA2CC;">(' . $it->getLink() . ')</span><br>';
            echo $check;

            getRecursiveOptions($it, $getLabelFunction, $permissions);
        }

    }
    ?>
    </form>
</div>

<div class="card-footer text-muted">
    <input type="button" class="btn btn-primary" value="Guardar permisos" onclick="savePermissions();">
</div>


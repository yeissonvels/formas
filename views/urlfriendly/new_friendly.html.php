<script>

    function check_new_url() {
        comprobate = Array('#urlname', '#description', '#urlfriendly', '#controllername', '#method');

        // Devuelve true si todos los campos han sido completados
        if (checkNoEmpty(comprobate)) {
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function getClassMethods() {
        var classname = $('#controllername').val();
        if (classname != "") {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'getClassMethods',
                    classname: classname,
                }
            }).done(function(Response) {
                var html = ' <option value="">Elija un método</option>';
                html += Response;
                $('#method').html(html);
            });
        }
    }
</script>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo $data["data"] ? "Editar url" : "Nueva url" ?></h4>
    </div>

    <form action="" method="POST" onsubmit="return check_new_url();">
        <div class="card-block">
            <div class="form-group row">
                <label for="urlname"
                       class="col-sm-2 col-form-label">Nombre</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="urlname" id="urlname" placeholder=""
                           value="<?php echo($data["data"] ? $data["data"]->getUrlname() : ''); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="description"
                       class="col-sm-2 col-form-label">Descripción</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="description" id="description" placeholder=""
                           value="<?php echo($data["data"] ? $data["data"]->getDescription() : ''); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="url"
                       class="col-sm-2 col-form-label">Url friendly</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="urlfriendly" id="urlfriendly" placeholder=""
                           value="<?php echo($data["data"] ? $data["data"]->getUrlfriendly() : ''); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="urledit"
                       class="col-sm-2 col-form-label">Url friendly Edit</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="urlfriendlyedit" id="urlfriendlyedit" placeholder=""
                           value="<?php echo($data["data"] ? $data["data"]->getUrlfriendlyEdit() : ''); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="controllername"
                       class="col-sm-2 col-form-label">Controller</label>
                <div class="col-sm-10">
                    <select name="controllername" id="controllername" class="form-select" onchange="getClassMethods()">
                        <option value="">Elija un controlador</option>
                        <?php
                            foreach ($data["controllers"] as $controller) {
                                $selected = "";
                                if($data["data"] && ($data["data"]->getControllername() == $controller)) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $controller . '" ' . $selected . '>' . $controller . '</option>';

                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="method"
                       class="col-sm-2 col-form-label">Método</label>
                <div class="col-sm-10">
                    <select name="method" id="method" class="form-select">
                        <option value="">Elija un método</option>
                    <?php
                        if ($data["data"]) {
                            $methods = $myController->getClassMethods(ucfirst($data["data"]->getControllername()) . "Controller");
                            foreach ($methods as $method) {
                                $selected = "";
                                if ($method == $data["data"]->getMethod()) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $method . '" ' . $selected . '>' . $method . '</option>';
                            }
                        }
                    ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="type"
                       class="col-sm-2 col-form-label">Tipo</label>
                <div class="col-sm-10">
                    <select name="type" id="type" class="form-select">
                <?php
                    global $urlTypes;
                    foreach ($urlTypes as $key => $value) {
                        $selected = "";
                        if ($data["data"] && $data["data"]->getType() == $key) {
                            $selected = 'selected="selected"';
                        }
                        echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                    }
                ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data["data"] ? 'save_edit_url' : 'save_url' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <?php
                if ($data["data"]) {
                    echo '<input type="hidden" name="id" value="' . $data["data"]->getId() . '">';
                }
                ?>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php save_update_btn($data["data"]); ?>
            <?php exit_btn(getUrl('show', $myController->getUrls())); ?>
        </div>
    </form>
</div>
<script>
    function check_new_zone() {
        comprobate = Array('#zone');

        // Devuelve true si todos los campos han sido completados
        if (checkNoEmpty(comprobate)) {
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }
</script>

<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo $data ? "Modificar datos" : "Nueva zona" ?></h4>
    </div>
    <form action="" method="POST" onsubmit="return check_new_zone();">
        <div class="card-block">
            <div class="form-group">
                <label for="zone">Zona</label>
                <input type="text" class="form-control" name="zone" id="zone"
                       placeholder=""
                       value="<?php echo($data ? $data->getZone() : ""); ?>">
            </div>

            <div class="form-group">
                <label for="active">Activo</label>
                <select class="form-control" name="active">
                    <option value="1" <?php echo($data && $data->getActive() == 1 ? 'selected="selected"' : ''); ?>>Si</option>
                    <option value="0" <?php echo($data && $data->getActive() == 0 ? 'selected="selected"' : ''); ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_zone' : 'save_zone' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_delivery_zones">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->getId() . '">';
                } else {
                    global $user;
                    echo '<input type="hidden" name="created_on" value="' . date('Y-m-d H:i:s') . '">';
                    echo '<input type="hidden" name="created_by" value="' . $user->getId() . '">';
                }
                ?>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php
            save_update_btn($data);
            exit_btn(getUrl('show', $myController->getUrls()));
            ?>
        </div>
    </form>
</div>

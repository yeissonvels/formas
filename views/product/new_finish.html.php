<script>
    function check_new_product() {
        comprobate = Array('#finishname');

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
        <h4 class="card-title"><?php echo $data ? "Modificar acabado" : "Nuevo acabado" ?></h4>
    </div>
    <form action="" method="POST" onsubmit="return check_new_product();">
        <div class="card-block">
            <div class="form-group">
                <label for="finishname">Nombre</label>
                <input type="text" class="form-control" name="finishname" id="finishname"
                       placeholder=""
                       value="<?php echo($data ? $data->finishname : ""); ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Descripción</label>
                <input type="text" class="form-control" name="description" id="description"
                       placeholder=""
                       value="<?php echo($data ? $data->description : ""); ?>">
            </div>
            
            <div class="form-group">
                <label for="points">Puntos</label>
                <input type="text" class="form-control" name="points" id="points"
                       placeholder=""
                       value="<?php echo($data ? $data->points : ""); ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Precio</label>
                <input type="text" class="form-control" name="price" id="price"
                       placeholder=""
                       value="<?php echo($data ? $data->price : ""); ?>">
            </div>

            <div class="form-group">
                <label for="active">Activo</label>
                <select class="form-control" name="active">
                    <option value="1" <?php echo($data && $data->active == 1 ? 'selected="selected"' : ''); ?>>Si</option>
                    <option value="0" <?php echo($data && $data->active == 0 ? 'selected="selected"' : ''); ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_finish' : 'save_finish' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_finishes">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->id . '">';
                }
                ?>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php
            save_update_btn($data);
            exit_btn(getUrl('show_finishes', $myController->getUrls()));
            ?>
        </div>
    </form>
</div>

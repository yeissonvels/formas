<div class="card">
    <div class="card-header">
        <h4 class="card-title">Configuración de urls amigables</h4>
        <?php echo $msg; ?>
    </div>

    <form action="" method="POST">
        <div class="card-block">
            <div class="form-group row">
                <label for="config"
                       class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10">
                    <select class="form-control" name="status" id="status">
                        <option value="OFF" <?php echo $data && $data->status == "OFF" ? 'selected="selected"' : '' ?>>OFF</option>
                        <option value="ON"  <?php echo $data && $data->status == "ON" ? 'selected="selected"' : '' ?>>ON</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="opt" value="set_friendly_urls_config">
                <input type="hidden" name="controller" value="urlFriendly">
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php save_update_btn((isset($data) ? $data : ""), "Configurar"); ?>
            <?php exit_btn('/'.GLOBAL_DIRECTORY); ?>
        </div>
    </form>
</div>
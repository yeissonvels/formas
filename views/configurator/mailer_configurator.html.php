<script>
   
    function check_configuratio() {
        comprobate = Array('#username', '#user_pass', '#repeat_user_pass', '#usertype');
        
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
        <h4 class="card-title"><?php echo $data ? 'Modificar configuración' : 'Crear configuración' ?></h4>
    </div>

    <form action="" method="POST" onsubmit="return check_configuration();">
        <div class="card-block">
            <div class="form-group row" id="div_host">
                <label for="host"
                       class="col-sm-2 col-form-label">Host</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="host" id="host" placeholder=""
                           value="<?php echo($data ? $data->host : ''); ?>">
                </div>
            </div>

            <div class="form-group row" id="div_user">
                <label for="user"
                       class="col-sm-2 col-form-label">User</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user" id="user" placeholder=""
                           value="<?php echo($data ? $data->user : ''); ?>">
                </div>
            </div>

            <div class="form-group row" id="div_password">
                <label for="password"
                       class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="password" id="password" placeholder=""
                           value="<?php echo($data ? $data->password : ''); ?>">
                </div>
            </div>

            <div class="form-group row" id="div_from">
                <label for="from"
                       class="col-sm-2 col-form-label">From</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="fromName" id="fromName" placeholder=""
                           value="<?php echo($data ? $data->fromName : ''); ?>">
                </div>
            </div>

            <div class="form-group row" id="div_to">
                <label for="to"
                       class="col-sm-2 col-form-label">To</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="_to" id="_to" placeholder=""
                           value="<?php echo($data ? $data->_to : ''); ?>">
                </div>
            </div>

            <div class="form-group row" id="div_cc">
                <label for="cc"
                       class="col-sm-2 col-form-label">CC</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="_cc" id="_cc" placeholder=""
                           value="<?php echo($data ? $data->_cc : ''); ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_config' : 'save_config' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="mailer_configurator">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->id . '">';
                }
                ?>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php save_update_btn($data); ?>
            <?php exit_btn(getUrl("show", $this->urls)); ?>
        </div>
    </form>
</div>
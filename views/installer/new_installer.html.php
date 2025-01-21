<script>
    function check_new_user() {
        if ($('#opt').val() == "save_installer") {
            comprobate = Array('#user_login', '#user_email', '#user_nicename', '#user_pass', '#repeat_user_pass');
        } else {
            if ($('#user_pass').val() != "") {
                comprobate = Array('#user_login', '#user_email', '#user_nicename', '#user_pass', '#repeat_user_pass');
            } else {
                comprobate = Array('#user_login', '#user_email', '#user_nicename');
            }

            if ($('#user_pass').val() != $('#repeat_user_pass').val()) {
                border_error('#user_pass');
                border_error('#repeat_user_pass');
                alert("Las contraseñas no coinciden");
                return false;
            }
        }

        // Devuelve true si todos los campos han sido completados
        if (checkNoEmpty(comprobate)) {
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function comprobateUsername() {
        var username = $("#user_login").val();
        $.ajax({
            type: "post",
            url: 'ajax.php',
            data: {
                username: username,
                op: 'comprobateUsername',
                controller: 'UserController'
            }
        }).done(function(data) {
            // Retorna "si" 0 "no" dependeiendo si el usuario existe
            if (data == "si") {
                $('#user_login').removeClass("form-control-success");
                $('#user_login').addClass("form-control-danger");
                //$('#user_login_response').html("El usuario ya existe");
            } else {
                $('#user_login').removeClass("form-control-danger");
                $('#user_login').addClass("form-control-success");
            }
        });
    }
</script>

<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo $data ? "Modificar instalador" : "Nuevo instalador" ?></h4>
    </div>

    <form action="" method="POST" onsubmit="return check_new_user();">
        <div class="card-block">
            <div class="form-group row">
                <label for="user_login"
                       class="col-sm-2 col-form-label"><?php echo trans('login_username') ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user_login" id="user_login" placeholder=""
                           value="<?php echo($data ? $data->getUserlogin() : ''); ?>" onkeyup="comprobateUsername();">
                    <span id="user_login_response"></span>
                </div>
            </div>
            <div class="form-group row">
                <label for="user_nicename" class="col-sm-2 col-form-label"><?php echo trans('full_name') ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user_nicename" id="user_nicename" placeholder=""
                           value="<?php echo($data ? $data->getUsernicename() : ''); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="user_email" class="col-sm-2 col-form-label"><?php echo trans('email') ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="user_email" name="user_email" placeholder=""
                           value="<?php echo($data ? $data->getUseremail() : ''); ?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="user_pass" class="col-sm-2 col-form-label"><?php echo trans('password') ?></label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="user_pass" name="user_pass" placeholder=""
                           value="">
                </div>
            </div>
            <div class="form-group row">
                <label for="repeat_user_pass"
                       class="col-sm-2 col-form-label"><?php echo trans('repeat_password') ?></label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="repeat_user_pass" placeholder="" value="">
                </div>
            </div>
            <div class="form-group row">
                <label for="active" class="col-sm-2 col-form-label"><?php echo trans('active_account') ?></label>
                <div class="col-sm-10">
                    <?php
                    $selected = $data ? $data->getActive() : 0;
                    generateYesNotSelect('active', $selected);
                    ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="deleted" class="col-sm-2 col-form-label"><?php echo trans('is_user_deleted') ?></label>
                <div class="col-sm-10">
                    <?php
                    $selected = $data ? $data->getDeleted() : 0;
                    generateYesNotSelect('deleted', $selected);
                    ?>
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_installer' : 'save_installer' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_installers">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->getId() . '">';
                } else {
                    echo '<input type="hidden" name="user_registered" value="' . (date('Y-m-d h:i:s')) . '">';
                }
                ?>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php save_update_btn($data); ?>
            <?php exit_btn(CONTROLLER . '&opt=show_installers'); ?>
        </div>
    </form>
</div>
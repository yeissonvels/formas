<script>
    comprobate = Array('#password','#repeat_password');

    function check_change_password() {
        // Devuelve true si todos los campos han sido completados
        if (checkNoEmpty(comprobate)) {
            if (jQuery('#password').val() == jQuery('#repeat_password').val()) {
                return true;
            } else {
                jQuery('#message').html('<?php errorMsg(trans('passwords_does_not_math')); ?>');
                return false;
            }

        } else {
            jQuery('#message').html('<?php errorMsg(trans('required_fields')) ?>');
            return false;
        }
    }

    $(document).ready(function() {
        $('input:password').click(function() {
            jQuery('#message').html('');
        });
    });

</script>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo trans('change_password') ?></h4>
        <p id="message">
            <?php echo $msg;?>
        </p>
    </div>

    <form action="" method="POST" onsubmit="return check_change_password();">
        <div class="card-block">
            <div class="form-group row" id="div_user_login">
                <label for="password"
                       class="col-sm-2 col-form-label"><?php echo trans('new_password') ?></label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="password" id="password">
                </div>
            </div>
            <div class="form-group row">
                <label for="repeat_password" class="col-sm-2 col-form-label"><?php echo trans('repeat_password') ?></label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="repeat_password" id="repeat_password">
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="opt" value="set_new_password">
                <input type="hidden" name="controller" value="user">
                <input type="hidden" name="show" value="none">
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php exit_btn('/'.GLOBAL_DIRECTORY); ?>
            <?php save_update_btn((isset($data) ? $data : ""), trans('change_password')); ?>
        </div>
    </form>
</div>
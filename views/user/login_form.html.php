<nav class="navbar navbar-light navbar-toggleable-md" style="height: 50px;">
    <a href="<?php echo HTTP_HOST; ?>">
        <img src="/images/logo-formas-naranja.png" class="img-fluid" alt="Responsive image" style="width: 90px;">
    </a>
</nav>

<div class="container-fluid">
    <div class="login-form">
        <input type="hidden" id="js_required_fields" value="<?php echo trans('required_fields')?>">
        <p>
            <?php echo $msg;?>
        </p>
        <form name="loginform" id="loginform" action="" method="post" onsubmit="return check_login();">
            <div class="form-group row">
                <label for="user_login" class="col-sm-2 col-form-label"><?php echo trans('login_username') ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="user_login" id="user_login">
                </div>
            </div>

            <div class="form-group row">
                <label for="user_pass" class="col-sm-2 col-form-label"><?php echo trans('login_password') ?></label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" name="user_pass" id="user_pass">
                </div>
            </div>

            <div class="">
                <button type="submit" class="btn btn-primary"><?php echo trans('login')?></button>
                <input type="hidden" name="opt" value="login">
            </div>

        </form>
    </div>
</div>
<script>
    var usernameValidated = <?php echo ($data && $data->getUsername() != "" ? 'true' : 'false' ); ?>;
    var usercodeValidated = <?php echo ($data && $data->getUsercode() != "" ? 'true' : 'false' ); ?>;

    $(document).ready(function () {
        $('#username').keyup(function () {
            var username = $("#username").val();
            if (username.length > 1) {
                $.ajax({
                    type: "post",
                    url: '/ajax.php',
                    data: {
                        username: username,
                        op: 'comprobateUsername',
                        controller: 'UserController'
                    }
                }).done(function (data) {
                    // Retorna "si" 0 "no" dependeiendo si el usuario existe
                    if (data == "si") {
                        addError('#div_user_login', '#username');
                        $('#user_login_response').html("El usuario ya existe");
                        usernameValidated = false;
                    } else {
                        addSuccess('#div_user_login', '#username');
                        $('#user_login_response').html("Usuario disponible");
                        usernameValidated = true;
                    }
                });
            }
        });

        $('#usercode').keyup(function () {
            var usercode = $("#usercode").val();
            if (usercode.length > 1) {
                $.ajax({
                    type: "post",
                    url: '/ajax.php',
                    data: {
                        usercode: usercode,
                        op: 'comprobateUsercode',
                        controller: 'UserController'
                    }
                }).done(function (data) {
                    // Retorna "si" 0 "no" dependeiendo si el usuario existe
                    if (data == "si") {
                        addError('#div_user_code', '#usercode');
                        $('#user_code_response').html("El código ya existe");
                        usercodeValidated = false;
                    } else {
                        addSuccess('#div_user_code', '#usercode');
                        $('#user_code_response').html("Código disponible");
                        usercodeValidated = true;
                    }
                });
            }
        });

        $('#user_email').keyup(function () {
            var email = $("#user_email").val();
            if (email.length > 1) {
                $.ajax({
                    type: "post",
                    url: '/ajax.php',
                    data: {
                        email: email,
                        op: 'comprobateEmail',
                        controller: 'UserController'
                    }
                }).done(function (data) {
                    // Retorna "si" 0 "no" dependeiendo si el usuario existe
                    if (data == "si") {
                        addError('#div_user_email', '#user_email');
                        $('#user_email_response').html("El email ya existe");
                        emailValidated = false;
                    } else {
                        addSuccess('#div_user_email', '#user_email');
                        $('#user_email_response').html("Email disponible");
                        emailValidated = true;
                    }
                });
            }
        });
    });

    function check_new_user() {
        var selecteduser = $('#usertype').val();
        if ($('#opt').val() == "save_user") {
            comprobate = Array('#username', '#usercode', '#user_pass', '#repeat_user_pass', '#usertype');
            if (selecteduser == 0) {
                comprobate.push('#storeid');
            }

        } else {
            comprobate = Array('#username', '#usercode', '#usertype');
            if ($('#user_pass').val() != "") {
                comprobate.push('#user_pass');
                comprobate.push('#repeat_user_pass');
            }

            if (selecteduser == 0) {
                comprobate.push('#storeid');
            }

            if ($('#user_pass').val() != $('#repeat_user_pass').val()) {
                border_error('#user_pass');
                border_error('#repeat_user_pass');
                alert("Las contraseñas no coinciden");
                return false;
            }
        }

        // Devuelve true si todos los campos han sido completados
        if (checkNoEmpty(comprobate) && usernameValidated && usercodeValidated) {
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function checkUserType() {
        var userType = $('#usertype').val();
        if (userType == 0) {
            $('#divstores').show('slow');
        } else {
            border_ok('#storeid');
            $('#divstores').hide('slow');
        }
    }

</script>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo $data ? trans('edit_user') : trans('new_user') ?></h4>
    </div>

    <form action="" method="POST" onsubmit="return check_new_user();">
        <div class="card-block">
            <div class="form-group row" id="div_user_login">
                <label for="user_login"
                       class="col-sm-2 col-form-label"><?php echo trans('login_username') ?></label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="username" id="username" placeholder=""
                           value="<?php echo($data ? $data->getUsername() : ''); ?>">
                    <div id="user_login_response" class="form-control-feedback"></div>
                </div>
            </div>
            <div class="form-group row" id="div_user_code">
                <label for="user_code"
                       class="col-sm-2 col-form-label"><?php echo trans('usercode') ?> (sólo números)</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="usercode" id="usercode" placeholder=""
                           value="<?php echo($data ? $data->getUsercode() : ''); ?>" onkeyup="allowOnlyNumbers(event);">
                    <div id="user_code_response" class="form-control-feedback"></div>
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
                    <select name="active" class="form-control">
                    	<option value="1" <?php echo $data && $data->active == 1 ? 'selected="selected"' :  ''; ?>>Si</option>
                    	<option value="0" <?php echo $data && $data->active == 0 ? 'selected="selected"' :  ''; ?>>No</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="deleted" class="col-sm-2 col-form-label"><?php echo trans('is_user_deleted') ?></label>
                <div class="col-sm-10">
                    <?php
                    $selected = $data ? $data->deleted : 0;
                    generateYesNotSelect('deleted', $selected);
                    ?>
                </div>
            </div>
            <?php
                global $user;
                $isAdmin = false;
                if ($user->getAdmin() == 1) {
                    $isAdmin = true;
                }
            ?>

            <?php
                if ($isAdmin) {
                    ?>
                    <div class="form-group row">
                        <label for="admin" class="col-sm-2 col-form-label">Admin</label>
                        <div class="col-sm-10">
                            <?php
                            $selected = $data ? $data->admin : 0;
                            generateYesNotSelect('admin', $selected);
                            ?>
                        </div>
                    </div>
                    <?php
                }
            ?>
            <?php
                $userStore = ($data && $data->getUserstore() == 1) ? true : false;
                $display = $userStore ? "" : "display: none;";
            ?>
            <div class="form-group row">
                <label for="gestor" class="col-sm-2 col-form-label">Tipo de usuario</label>
                <div class="col-sm-10">
                    <select name="usertype" id="usertype" class="form-control" onchange="checkUserType()">
                        <option value="">Seleccione un tipo</option>
                        <?php
                            global $profileTypes;
                            foreach ($profileTypes as $key => $value) {
                                $selected = "";
                                if ($data) {
                                    // Usuario de tienda
                                    if ($key == 0 && $userStore) {
                                        $selected = 'selected="selected"';
                                    } else if ($key == 1 && $data->getUserRepository() == 1) {
                                        // Usuario de almacén
                                        $selected = 'selected="selected"';
                                    } else if ($key == 2 && $data->getUsermanager() == 1) {
                                        // Usuario Jefe
                                        $selected = 'selected="selected"';
                                    } else if ($key == 3 && $data->getUseraccounting() == 1) {
                                        // Usuario Jefe
                                        $selected = 'selected="selected"';
                                    }
                                }

                                echo '<option value="' . $key . '" ' . $selected . '>' . $value[1] . '</option>';

                            }
                        ?>
                    </select>

                </div>
            </div>
            <div class="form-group row" id="divstores" style="<?php echo $display; ?>">
                <label for="storeid" class="col-sm-2 col-form-label">Tienda</label>
                <div class="col-sm-10">
                    <select name="storeid" id="storeid" class="form-control">
                        <option value="">Seleccione la tienda</option>
                        <?php
                            $stores = getStores(true);
                            foreach ($stores as $store) {
                                $selected = "";
                                if ($data) {
                                    // Usuario de tienda
                                    if ($data->getStoreid() == $store['id']) {
                                        $selected = 'selected="selected"';
                                    }
                                }
                                echo '<option value="' . $store['id'] . '" ' . $selected . '>' . $store['name'] . '</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_user' : 'save_user' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_users">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->id . '">';
                } else {
                    echo '<input type="hidden" name="user_registered" value="' . (date('Y-m-d h:i:s')) . '">';
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

<!-- Permisos -->
<div class="card" style="width: 50%; display: none; vertical-align: top;">
    <div class="card-header">
        <h4 class="card-title">Permisos de usuario</h4>
    </div>
    <div class="card-block" id="dynamic-menu">
        <?php
        /*if (isset($_GET['id'])) {
                $this->setUserMenuPermissions($_GET['id']);
            } else {
                echo "<h4>Aquí se asignarán los persmisos del usuario para cada opción del menú<br> Pero primero debe crearlo!</h4>";
            } */
        ?>
    </div>
</div>
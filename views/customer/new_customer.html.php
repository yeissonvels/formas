<script>
    function check_new_customer() {
        comprobate = Array('#nif', '#name', '#address', '#cp', '#city', '#provinceid');

        // Devuelve true si todos los campos han sido completados
        if (checkNoEmpty(comprobate)) {
            // Seteamos el user_nicename igual al user_login
            //$('#user_nicename').prop('value', $('#user_login').val());
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }
</script>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?php echo $data ? trans('edit_user') : trans('new_user') ?></h4>
        </div>
        <form action="" method="POST" onsubmit="return check_new_customer();">
            <div class="card-block">
                <div class="form-group">
                    <label for="exampleInputEmail1">NIF</label>
                    <input type="text" class="form-control" name="nif" id="nif" aria-describedby="emailHelp"
                           placeholder=""
                           value="<?php echo($data ? $data->getNif() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Nombre</label>
                    <input type="text" class="form-control" name="name" id="name" aria-describedby="emailHelp"
                           placeholder=""
                           value="<?php echo($data ? $data->getName() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="exampleTextarea">Dirección</label>
                    <textarea class="form-control" name="address" id="address"
                              rows="3"><?php echo($data ? $data->getAddress() : ""); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="cp">CP.</label>
                    <input type="text" class="form-control" id="cp" name="cp" aria-describedby="emailHelp"
                           placeholder="" value="<?php echo($data ? $data->getCp() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="city">Localidad</label>
                    <input type="text" class="form-control" id="city" name="city" aria-describedby="emailHelp"
                           placeholder="" value="<?php echo($data ? $data->getCity() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="province">Provincia</label>
                    <select name="provinceid" id="provinceid" class="form-select">
                        <option value="">Seleccione una provincia</option>
                        <?php
                        $provinces = getProvinces();
                        foreach ($provinces as $province) {
                            $selected = "";
                            if ($data && $data->getProvinceid() == $province->id) {
                                $selected = 'selected="selected"';
                            }
                            echo '<option value="' . $province->id . '" ' . $selected . '>' . $province->province . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Email</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                           placeholder=""
                           value="<?php echo($data ? $data->getEmail() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Teléfono</label>
                    <input type="text" class="form-control" id="telephone" name="telephone" aria-describedby="emailHelp"
                           placeholder="" value="<?php echo($data ? $data->getTelephone() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="fax">Fax</label>
                    <input type="text" class="form-control" id="fax" name="fax" aria-describedby="emailHelp"
                           placeholder="" value="<?php echo($data ? $data->getFax() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="cel">Móvil</label>
                    <input type="text" class="form-control" id="cel" name="cel" aria-describedby="emailHelp"
                           placeholder="" value="<?php echo($data ? $data->getCel() : ""); ?>">
                </div>
                <!--<div class="form-group">
                    <label for="exampleInputPassword1">Contraseña</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="">
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">Repetir contraseña</label>
                    <input type="password" class="form-control" id="repeat_password" placeholder="">
                </div>-->
                <div class="form-group">
                    <label for="contact_person">Persona de contacto</label>
                    <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder=""
                           value="<?php echo($data ? $data->getContactPerson() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="exampleSelect1">Activo</label>
                    <select class="form-select" id="active" name="active">
                        <option value="1">SI</option>
                        <option value="0">NO</option>
                    </select>
                </div>
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_customer' : 'save_customer' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_customers">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->getId() . '">';
                } else {
                    echo '<input type="hidden" name="created_on" value="' . (date('Y-m-d h:i:s')) . '">';
                }
                ?>

            </div>
            <div class="card-footer text-muted">
                <?php
                save_update_btn($data);
                exit_btn(getUrl('show', $myController->getUrls()));
                ?>
            </div>
        </form>
    </div>

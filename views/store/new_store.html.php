<script>
    function check_new_store() {
        comprobate = Array('#cif', '#storename', '#address', '#cp', '#city', '#provinceid');

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
            <h4 class="card-title"><?php echo $data ? "Modificar datos" : "Nueva tienda" ?></h4>
        </div>
        <form action="" method="POST" onsubmit="return check_new_store();">
            <div class="card-block">
                <div class="form-group">
                    <label for="cif">CIF</label>
                    <input type="text" class="form-control" name="cif" id="cif"
                           value="<?php echo($data ? $data->getCif() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="storename">Nombre</label>
                    <input type="text" class="form-control" name="storename" id="storename" aria-describedby="emailHelp"
                           placeholder=""
                           value="<?php echo($data ? $data->getStorename() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Dirección</label>
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
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                           placeholder=""
                           value="<?php echo($data ? $data->getEmail() : ""); ?>">
                </div>
                <div class="form-group">
                    <label for="web">Web</label>
                    <input type="web" class="form-control" id="web" name="web"
                           value="<?php echo($data ? $data->getWeb() : ""); ?>">
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
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_store' : 'save_store' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_stores">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->getId() . '">';
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

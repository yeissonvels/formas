<script>

    // Valores a comprobar
    comprobate = Array('#controller_name','#description');

    function check_new_menu() {

        // Devuelve true si todos los campos han sido completados
        if(checkNoEmpty(comprobate)){
            return true;

        }else{
            alert(completeRequiredFields);
            return false;
        }
    }

</script>
<h3><?php echo $data ? trans('edit_controller') : trans('new_controller') ?></h3>
<form action="" method="POST" onsubmit="return check_new_menu();">

    <table>
        <tr>
            <td><?php echo trans('menu_name') ?></td>
            <td>
                <input type="text" name="controller_name" id="controller_name" value="<?php echo ($data ? $data->getControllerName() : ''); ?>">
            </td>
        </tr>

        <tr>
            <td><?php echo trans('description') ?></td>
            <td>
                <textarea name="description" id="description"><?php echo ($data ? $data->getDescription() : ''); ?></textarea>
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>
                <!--<input type="hidden" name="opt" id="opt" value="< ?php echo $data ? 'save_edit_controller' : 'save_controller'?>"> -->
                <!-- Si guardamos o actualizamos lo controladmos desde createObject -->
                <input type="hidden" name="opt" id="opt" value="createObject">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_controllers">
                <?php
                if($data){
                    echo '<input type="hidden" name="old_controllername" value="' . $data->getControllerName() . '">';
                    echo '<input type="hidden" name="id" value="' . $data->getId() . '">';
                } else {
                    echo '<input type="hidden" name="created_by" value="' . $user->getId() . '">';
                }
                ?>
            </td>
        </tr>

    </table>
    <p class="submit">
        <?php exit_btn(CONTROLLER . '&opt=show_controllers'); ?>
        <?php save_update_btn($data); ?>
    </p>
</form>
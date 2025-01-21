<script>

    // Si no hay sub items comprobamos estos valores
    comprobate = Array('#menu_name','#description');

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

<div class="container">
    <div class="card">
        <div class="card-header">
            <?php echo $data ? trans('edit_menu') : trans('new_menu') ?>
        </div>

        <div class="card-block">
            <form action="" method="POST" onsubmit="return check_new_menu();">

                <table>
                    <tr>
                        <td><?php echo trans('menu_name') ?></td>
                        <td>
                            <input type="text" name="menu_name" id="menu_name" class="form-control" value="<?php echo ($data ? $data->menu_name : ''); ?>">
                        </td>
                    </tr>

                    <tr>
                        <td><?php echo trans('description') ?></td>
                        <td>
                            <textarea name="description" id="description" class="form-control"><?php echo ($data ? $data->description : ''); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td><?php echo trans('active')?>?</td>
                        <td>
                            <?php
                                $selected = $data ? $data->active : '';
                                generateYesNotSelect('active', $selected);
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <input type="hidden" name="opt" id="opt" value="<?php echo $data ? 'save_edit_menu' : 'save_menu'?>">
                            <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                            <input type="hidden" name="show" value="show_menus">
                            <?php
                            if($data){
                                echo '<input type="hidden" name="id" value="'.$data->getId().'">';
                            }
                            ?>
                        </td>
                    </tr>

                </table>
                <p class="submit">
                    <?php save_update_btn($data); ?>
                    <?php exit_btn(getUrl('show', $this->urls)); ?>
                </p>
            </form>
        </div>
    </div>
</div>
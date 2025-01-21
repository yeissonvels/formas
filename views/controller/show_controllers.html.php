<script>
    function delete_controller(id, controname){
        if (confirm('¿Esta seguro que desea borrar este controlador "' + controname + '"?')) {
            document.location.href = '<?php echo CONTROLLER?>&opt=delete_controller&id=' + id + '';
        }
    }
</script>
<table>
    <tr>
        <td><h3><?php echo trans('controllers') ?></h3></td>
        <?php
        if(isadmin() && $user->getId() == 1){
            ?>
            <td><span><a href="<?php echo CONTROLLER ?>&opt=new_controller"><?php echo trans('new') ?></a></span></td>
            <td><?php update_icon(CONTROLLER . '&opt=show_controllers');?></td>
            <?php
        }
        ?>
    </tr>
</table>

<table class="wp-list-table widefat fixed users" style="width: 90%;">
    <thead>
    <tr>
        <th><?php echo trans('id') ?></th>
        <th><?php echo trans('menu_name') ?></th>
        <th><?php echo trans('old_name') ?></th>
        <th><?php echo trans('description') ?></th>
        <th><?php echo trans('by') ?></th>
        <th><?php echo trans('created_on') ?></th>
        <th><?php echo trans('edit') ?></th>
    </tr>
    </thead>

    <?php
    foreach($controllers as $controller) { ?>
        <tr>
            <td><?php echo $controller->getId() ?></td>
            <td><?php echo $controller->getControllerName() ?></td>
            <td><?php echo $controller->getOldControllername() ?></td>
            <td><?php echo $controller->getDescription() ?></td>
            <td><?php echo $controller->getCreatedBy() ?></td>
            <td><?php echo americaDate($controller->getCreatedOn()); ?></td>
            <td><a href="<?php echo CONTROLLER ?>&opt=new_controller&id=<?php echo $controller->getId(); ?>"><?php edit_icon() ?></a></td>
        </tr>
        <?php
    }
    ?>

</table>
<table class="wp-list-table widefat fixed users">
    <thead>
    <tr>
        <th><?php echo trans('id') ?></th>
        <th><?php echo trans('menu_name') ?></th>
        <th><?php echo trans('description') ?></th>
        <th><?php echo trans('active') ?></th>
        <th><?php echo trans('edit') ?></th>
    </tr>
    </thead>
    <?php

    foreach ($menus as $menu) {
        ?>
        <tr>
            <td><?php echo $menu->getId() ?></td>
            <td><?php echo $menu->menu_name ?></td>
            <td><?php echo $menu->description ?></td>
            <td>
                <span style="color: <?php echo $menu->active > 0 ? 'green' : 'red' ?>"><?php echo $menu->active > 0 ? trans('yes') : trans('no') ?></span>
            </td>
            <td><a href="<?php echo CONTROLLER?>&opt=new_menu2&id=<?php echo $menu->getId()?>"><?php edit_icon()?></a></td>
        </tr>

    <?php
    }
    ?>

</table>
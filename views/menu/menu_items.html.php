<?php
    if (count($menuItems) > 0) {
        ?>
        <div class="card" style="margin-top: 10px;">
            <div class="card-header">
                <h4>Enlaces</h4>
            </div>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th><?php echo trans('id') ?></th>
                        <th><?php echo trans('label') ?></th>
                        <th><?php echo trans('label') ?> 2</th>
                        <th><?php echo trans('label') ?> 3</th>
                        <th><?php echo trans('show_label')?>?</th>
                        <th><?php echo trans('link') ?></th>
                        <th><?php echo trans('link_friendly') ?></th>
                        <th><?php echo trans('permision') ?></th>
                        <th><?php echo trans('active') ?></th>
                        <th><?php echo trans('icon') ?></th>
                        <th><?php echo trans('target') ?></th>
                        <th><?php echo trans('edit') ?></th>
                        <th><?php echo trans('delete') ?></th>
                    </tr>
                </thead>
                <?php
                    foreach ($menuItems as $item) {?>

                      <tr id="tr<?php echo $item->getId(); ?>">
                          <td class="parent-menu"><?php echo $item->position; ?></td>
                          <td class="parent-menu"><?php echo $item->label; ?></td>
                          <td class="parent-menu"><?php echo $item->label2; ?></td>
                          <td class="parent-menu"><?php echo $item->label3; ?></td>
                          <td class="parent-menu"><?php echo ($item->show_label > 0 ? trans('yes') : trans('no')); ?></td>
                          <td class="parent-menu"><?php echo $item->link; ?></td>
                          <td class="parent-menu"><?php echo $item->link_friendly; ?></td>
                          <td class="parent-menu"><?php echo $item->permision; ?></td>
                          <td class="parent-menu"><?php echo $item->active; ?></td>
                          <td>
                              <?php
                                    $iconPath = UPLOADED_MENU_ICONS_PATH . 'menu' . $_GET['id'] . '/' . $item->icon;
                                    echo $item->icon != "" ? '<img src="' . $iconPath . '">' : ''
                              ?>
                          </td>
                          <td class="parent-menu"><?php echo $item->target > 0 ? "_blank" : "" ?></td>
                          <td>
                              <a onclick="loadMenuItemData(<?php echo $item->getId(); ?>);scrollingTop();" style="cursor: pointer;">
                                <?php echo edit_icon();?>
                              </a>
                          </td>
                          <td>
                              <a onclick="deleteMenuItem(<?php echo $item->getId(); ?>, this.event)" style="cursor: pointer;" id="a<?php echo $item->getId()?>">
                                  <?php echo delete_icon();?>
                              </a>
                          </td>
                      </tr>
                  <?php
                        createRecursiveTable($item);
                  }
                  ?>
            </table>
        </div>
    <?php
    } else {
        echo errorMsg(trans('no_menu_items_found'));
    }

    function createRecursiveTable($item) {
        echo '<tr>';
        echo    '<td colspan="12">';
        echo        '<table>';
        foreach($item->getChilds() as $niv) {
            echo '<tr id="tr' . $niv->getId() . '">';
            echo    '<td><b>|</b></td>';
            echo    '<td>' . $niv->position . '</td>';
            echo    '<td>' . $niv->label . '</td>';
            echo    '<td>' . $niv->label2 . '</td>';
            echo    '<td>' . $niv->label3 . '</td>';
            echo    '<td>' . ($niv->show_label > 0 ? trans('yes') : trans('no')) . '</td>';
            echo    '<td>' . $niv->link . '</td>';
            echo    '<td>' . $niv->link_friendly . '</td>';
            echo    '<td>' . $niv->permision . '</td>';
            echo    '<td>' . $niv->active . '</td>';

            $iconPath = UPLOADED_MENU_ICONS . 'menu' . $_GET['id'] . '/' . $niv->icon;

            echo '<td>';
            echo    $niv->icon != "" ? '<img src="' . $iconPath . '">' : '';
            echo '</td>';

            echo    '<td>' . $niv->target . '</td>';
            ?>
            <td>
                <a onclick="loadMenuItemData(<?php echo $niv->getId(); ?>);scrollingTop();" style="cursor: pointer;">
                    <?php echo edit_icon();?>
                </a>
            </td>

            <td>
                <a onclick="deleteMenuItem(<?php echo $niv->getId(); ?>, this.event)" style="cursor: pointer;" id="a<?php echo $niv->getId()?>">
                    <?php echo delete_icon();?>
                </a>
            </td>
        <?php
            echo '</tr>';

            createRecursiveTable($niv);
        }
        echo        '</table>';
        echo    '</td>';
        echo '<tr>';
    }
?>

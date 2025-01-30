
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?php echo "Menús"; ?></h4>
            <span><a href="<?php echo getUrl('new', $this->urls) ?>"><?php icon('save', true); ?></a></span>
            <?php update_icon(getUrl('show', $urls)); ?>
        </div>
        <div class="card-block">
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th><?php echo trans('id'); ?></th>
                        <th><?php echo trans('menu_name'); ?></th>
                        <th><?php echo trans('description'); ?></th>
                        <th><?php echo trans('active'); ?></th>
                        <th><?php echo trans('edit'); ?></th>
                        <th><?php echo trans('create_items'); ?></th>
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
                        <td>
                            <a href="<?php echo getUrl('edit', $this->urls, $menu->getId()); ?>"><?php icon('edit', true); ?></a>
                        </td>
                        <td>
                            <a href="<?php echo getUrl('new_item', $this->urls, $menu->getId()); ?>"><?php icon('save', true); ?></a>
                        </td>
                    </tr>

                <?php
                }
                ?>

            </table>
        </div>
    </div>
<script>
    function delete_user(id, username) {
        if (confirm('¿Esta seguro que desea borrar este usuario "' + username + '"?')) {
            document.location.href = '<?php echo getUrl('delete', $this->urls); ?>' + id;
        }
    }
</script>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?php echo trans('users') ?></h4>
            <span><a href="<?php echo getUrl('new', $this->urls); ?>"><?php echo trans('new') ?></a></span>
            <?php update_icon(getUrl('show', $this->urls)); ?>
        </div>
        <div class="card-block">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th><?php echo trans('user') ?></th>
                    <th><?php echo trans('usercode') ?></th>
                    <th><?php echo trans('register_date') ?></th>
                    <th><?php echo trans('last_login') ?></th>
                    <th><?php echo trans('account_status') ?></th>
                    <th><?php echo trans('deleted') ?></th>
                    <?php
                    	if (isadmin()) {
                    		echo '<th>Admin</th>';
                    	}
                    ?>
                    <th>Perfil</th>
                    <th><?php echo trans('edit') ?></th>
                    <th><?php echo trans('delete') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($users as $dbuser) {
                    $trClass = '';
                    if ($dbuser->getUsermanager() == 1) {
                        $trClass = "table-success";
                    } else if ($dbuser->getUseraccounting() == 1) {
                        $trClass = "table-info";
                    } else if ($dbuser->getUserstore() == 1) {
                        $trClass = "table-active";
                    } else if ($dbuser->getUserrepository() == 1) {
                        $trClass = "table-warning";
                    }

                    global $translator;
                    $lenguages = $translator->languages;
                    $selectedLang = $lenguages[$_SESSION['lang']];
                    $dbuserId = $dbuser->getId();

                    $deleted = $dbuser->deleted == 1 ? true : false;
                    ?>
                    <tr class="<?php echo ($deleted ? 'deleted' : '') . " " . $trClass ?>">
                        <td><?php echo $dbuser->username ?></td>
                        <td><?php echo $dbuser->usercode ?? '<span style="color: red;">Sin asignar</span>' ?></td>
                        <td><?php echo americaDate($dbuser->user_registered); ?></td>
                        <td><?php echo americaDate($dbuser->last_login) ?></td>
                        <td>
                            <?php
                            echo $dbuser->active == 1 ? 'Activa' : 'Inactiva';
                            ?>
                        </td>
                        <td>
                            <?php
                                echo $dbuser->deleted == 1 ? 'Si' : 'No';
                            ?>
                        </td>
                        
                        <?php
                        	if (isadmin()) {
                        		echo '<td>';
                        		echo 	$dbuser->admin == 1 ? 'Si' : 'No';
								echo '</td>';
                        	}
                        ?>
                        
                        <td>
                            <?php
                                if ($dbuser->getUserstore() == 1) {
                                    $storename = getStoreName($dbuser->getStoreid());
                                    echo $storename;
                                } else if ($dbuser->getUserrepository() == 1) {
                                    echo "DISTRIBUCIÓN";
                                } else if ($dbuser->getUseraccounting() == 1) {
                                    echo "CONTABILIDAD";
                                } else if ($dbuser->getUsermanager() == 1) {
                                    echo "JEFE";
                                }

                            ?>
                        </td>
                        <td>
                            <a href="<?php echo getUrl('edit', $this->urls, $dbuserId); ?>"><?php icon('edit', true) ?></a>
                        </td>
                        <td>
                            <?php
                            if (!$deleted) { ?>
                                <a href="#"
                                   onclick="delete_user(<?php echo $dbuserId ?>, '<?php echo $dbuser->username ?>')"><?php icon('delete', true); ?></a>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
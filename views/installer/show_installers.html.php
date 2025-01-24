<script>
    function delete_user(id, username) {
        if (confirm('¿Esta seguro que desea borrar este usuario "' + username + '"?')) {
            document.location.href = '<?php echo CONTROLLER; ?>&opt=delete_user&id=' + id + '';
        }
    }
</script>

<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo trans('users') ?></h4>
        <span><a href="<?php echo CONTROLLER ?>&opt=new_installer"><?php echo trans('new') ?></a></span>
        <?php update_icon(CONTROLLER . '&opt=show_installers'); ?>
    </div>
    <div class="card-block">
        <table class="table table-responsive">
            <thead>
            <tr>
                <th><?php echo trans('id') ?></th>
                <th><?php echo trans('user') ?></th>
                <th><?php echo trans('full_name') ?></th>
                <th><?php echo trans('email') ?></th>
                <th><?php echo trans('register_date') ?></th>
                <th><?php echo trans('last_login') ?></th>
                <th><?php echo trans('account_status') ?></th>
                <th><?php echo trans('deleted') ?>?</th>
                <th><?php echo trans('edit') ?></th>
                <th><?php echo trans('delete') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($installers as $user) {
                global $translator;
                $lenguages = $translator->languages;
                $selectedLang = $lenguages[$_SESSION['lang']];
                $userId = $user->getId();

                $deleted = $user->getDeleted() == 1 ? true : false;
                ?>
                <tr <?php echo $deleted ? 'class="deleted"' : '' ?>>
                    <td><?php echo $userId ?></td>
                    <td><?php echo $user->getUserLogin() ?></td>
                    <td><?php echo $user->getUserNicename() ?></td>
                    <td><?php echo $user->getUserEmail() ?></td>
                    <td><?php echo americaDate($user->getUserRegistered()); ?></td>
                    <td><?php echo americaDate($user->getLastLogin()) ?></td>
                    <td>
                        <?php
                        echo $user->getActive() == 1 ? 'Activa' : 'Inactiva';
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $user->getDeleted() == 1 ? 'Si' : 'No';
                        ?>
                    </td>
                    <td>
                        <a href="<?php echo CONTROLLER ?>&opt=new_installer&id=<?php echo $userId; ?>"><?php edit_icon() ?></a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
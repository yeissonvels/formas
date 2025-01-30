<div class="card">
    <div class="card-header">
        <h4 class="card-title">Remitentes <?php icon('send_email', true);  ?></h4>
        <!--<span><a href="< ?php echo getUrl('new', $this->urls); ?>">< ?php icon('save', true); ?></a></span>
        < ?php update_icon(getUrl('show', $this->urls)); ?> -->
    </div>
    <div class="card-block">
        <table class="table mt-2">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>host</th>
                    <th>fromName</th>
                    <th>user</th>
                    <th>password</th>
                    <th>to</th>
                    <th>cc</th>
                    <th>status</th>
                    <th><?php icon('edit', true); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($configurations as $configurator) {
                        $id = $configurator->id;
                        echo "<tr>";
                        echo    "<td>".$id."</td>";
                        echo    "<td>".$configurator->host."</td>";
                        echo    "<td>".$configurator->fromName."</td>";
                        echo    "<td>".$configurator->user."</td>";
                        echo    "<td>**********</td>";
                        echo    "<td>".$configurator->_to."</td>";
                        echo    "<td>".$configurator->_cc."</td>";
                        echo    "<td>".((int)$configurator->status == 1 ? ("Activo <span style='color: #18c818;'>" . icon('on', false) . '</span>') : ("Inactivo <span style='color: red;'>" . icon('off', false) .'</span>')) ."</td>";
                        echo    "<td><a href='?controller=configurator&opt=mailer_configurator&id=$id'>".icon('edit', false)."</a></td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
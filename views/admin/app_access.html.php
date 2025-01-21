<div class="card">
    <div class="card-header">
        <h4 class="card-title">Accesos</h4>
    </div>
    <div class="card-block"> 
        <table class="table table-responsive">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Ip</th>
                    <th>Host</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($data as $access) {
                        $id = $access->id;
                        echo '<tr>';
                        echo    '<td>' . $id . '</td>';
                        echo    '<td>' . getUsername($access->user) . '</td>';
                        echo    '<td>' . americaDate($access->accessdate) . '</td>';
                        echo    '<td>' . $access->ip . '</td>';
						echo    '<td>' . $access->remotehost . '</td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

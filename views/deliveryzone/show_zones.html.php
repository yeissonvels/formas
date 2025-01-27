<div class="card">
    <div class="card-header">
        <h4 class="card-title">Zonas de entrega <?php icon('truck', true); ?></h4>
        <span><a href="<?php echo getUrl('new', $myController->getUrls()) ?>"><?php icon('save', true); ?></a></span>
        <?php update_icon(getUrl('show', $myController->getUrls())); ?>
    </div>
    <div class="card-block">
        <table class="table">
            <thead>
                <tr>
                    <th>Zona</th>
                    <th>Activa</th>
                    <th>Creada por</th>
                    <th>Creada el</th>
                    <th>Modificar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($data as $zone) {
                        $id = $zone->getId();
                        echo '<tr>';
                        echo    '<td>' . $zone->getZone() . '</td>';
                        echo    '<td>' . ($zone->getActive() == 1 ? 'Si' : 'No' ) . '</td>';
                        echo    '<td>' . getUsername($zone->getCreatedBy()) . '</td>';
                        echo    '<td>' . americaDate($zone->getCreatedOn()) . '</td>';
                        echo    '<td><a href="' . getUrl('edit', $myController->getUrls(), $id) . '" >' . icon('edit', false) . '</a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

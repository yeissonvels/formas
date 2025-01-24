<div class="card">
    <div class="card-header">
        <h4 class="card-title">Acabados</h4>
        <span><a href="<?php echo getUrl('new_finish', $myController->getUrls()) ?>"><?php echo "Nuevo"; ?></a></span>
        <?php update_icon(getUrl('show_finishes', $myController->getUrls())); ?>
    </div>
    <div class="card-block">
        <table class="table">
            <thead>
                <tr>
                    <th style="display: none;">Id</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Puntos</th>
                    <th>Precio</th>
                    <th>Activo</th>
                    <th>Creado el</th>
                    <th>Modificar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($data as $product) {
                        $id = $product->id;
                        echo '<tr>';
                        echo    '<td style="display: none;">' . $id . '</td>';
                        echo    '<td>' . $product->finishname . '</td>';
						echo    '<td>' . $product->description . '</td>';
						echo    '<td>' . $product->points . '</td>';
						echo    '<td>' . $product->price . '</td>';
                        echo    '<td>' . ($product->active == 1 ? 'Si' : 'No' ) . '</td>';
                        echo    '<td>' . $product->createdon . '</td>';
                        echo    '<td><a href="' . getUrl('edit_finish', $myController->getUrls(), $id) . '" >' . icon('edit', false) . '</a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

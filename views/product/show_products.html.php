<div class="card">
    <div class="card-header">
        <h4 class="card-title">Productos <?php icon('cart', true); ?></h4>
        <span><a href="<?php echo getUrl('new', $myController->getUrls()) ?>"><?php echo "Nuevo"; ?></a></span>
        <?php update_icon(getUrl('show', $myController->getUrls())); ?>
    </div>
    <div class="card-block">
        <table class="table">
            <thead>
                <tr>
                    <th style="display: none;">Id</th>
                    <th>Nombre</th>
                    <th>Activo</th>
                    <th>Modificar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($data as $product) {
                        $id = $product->getId();
                        echo '<tr>';
                        echo    '<td style="display: none;">' . $id . '</td>';
                        echo    '<td>' . $product->getProductname() . '</td>';
                        echo    '<td>' . ($product->getActive() == 1 ? 'Si' : 'No' ) . '</td>';
                        echo    '<td><a href="' . getUrl('edit', $myController->getUrls(), $id) . '" >' . icon('edit', false) . '</a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

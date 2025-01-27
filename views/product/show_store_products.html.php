<div class="card">
    <div class="card-header">
        <h4 class="card-title">Productos <?php icon('cart', true); ?></h4>
        <span><a href="<?php echo getUrl('new_store_product', $myController->getUrls()) ?>"><?php icon('save', true); ?></a></span>
        <?php update_icon(getUrl('show_store_products', $myController->getUrls())); ?>
    </div>
    <div class="card-block">
        <table class="table">
            <thead>
                <tr>
                    <th style="display: none;">Id</th>
                    <th>Referencia</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Acabado</th>
                    <th>Activo</th>
                    <th>Modificar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($data as $product) {
                        $id = $product->id;
						$finish = $myController->getFinishData($product->finishid);
                        echo '<tr>';
                        echo    '<td style="display: none;">' . $id . '</td>';
						echo    '<td>' . $product->reference . '</td>';
						echo    '<td>' . $product->productname . '</td>';
						echo    '<td>' . numberFormat($product->price, true, 2) . ' €</td>'; 
                        echo    '<td>' . $finish->finishname . ' €</td>';
                        echo    '<td>' . ($product->active == 1 ? 'Si' : 'No' ) . '</td>';
                        echo    '<td><a href="' . getUrl('edit_store_product', $myController->getUrls(), $id) . '" >' . icon('edit', false) . '</a></td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

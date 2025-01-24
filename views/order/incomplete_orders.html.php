<?php
if ($data) { ?>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?php icon('half', true); ?> Pedidos incompletos</h4>
        </div>

        <div class="card-block">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Fecha de compra</th>
                    <th>Tienda</th>
                    <th>Mes de entrega</th>
                    <th>Estado</th>
                    <th>Total artículos</th>
                    <th>Artículos disponibles</th>
                    <th>Gestionar</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 0;
                foreach ($data as $order) {
                    $cancelled = false;
                    $trClass = "";
                    if ($order->getCancelled() == 1) {
                        $cancelled = true;
                        $trClass = 'class="deleted"';
                    }

                    $reuploaded = $myController->orderReuploaded($order->getPdfid());
                    $reuploaded = $reuploaded[0];

                    ?>
                    <tr <?php echo $trClass; ?>>
                        <td><?php echo $order->getCode(); ?></td>
                        <td><?php echo $order->getCustomer(); ?></td>
                        <td><?php echo $order->getTelephone(); ?></td>
                        <td><?php echo americaDate($order->getPurchasedate(), false); ?></td>
                        <td><?php echo $order->getStorename(); ?></td>
                        <td><?php echo getMonth($order->getDeliverymonth()); ?></td>
                        <td>
                            <?php
                            if (!$cancelled) {
                                $deliveryDate = americaDate($order->getDeliverydate(), false);
                                $status = $order->getStatus();
                                if ($status == 0) {
                                    $msg = "Pendiente de entrega " . icon('pending', false);
                                } else if ($status == 1) {
                                    $msg = "Listo para entregar " . icon('truck', false);
                                } else if ($status == 2) {
                                    $msg = "Entregado " . icon('delivered', false) . " " . $deliveryDate;
                                } else if ($status == 3) {
                                    $msg = "Entregado con incidencia " . icon('delivered', false) . " " . $deliveryDate;
                                }
                            } else {
                                $msg = '<span class="red-color">Anulado</span>';
                            }
                            echo $msg;
                            ?>
                        </td>

                        <td class="text-center"><?php echo $order->getTotalitems(); ?></td>
                        <td class="text-center"><?php echo $order->getSaveditems(); ?></td>

                        <td class="text-center">
                            <?php if (!$cancelled) { ?>
                                <a href="<?php echo getUrl('edit', $myController->getUrls(), $order->getId()); ?>"><?php icon('edit', true); ?></a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
                <tr>
                    <td colspan="9"><b>Total resultados: <?php echo $i; ?></b></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} else {
    errorMsg('Sin resultados');
}

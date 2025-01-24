<?php
/*
 * NOTA
 * Algunos datos los obtenemos de la venta y otros del pedido.
 */
//pre($data);

$orderExist = false;
$cancelled = false;
$parent = $data->parent[0];
if ($parent->cancelled == 1) {
    $cancelled = true;
}

if (isset($data->order)) {
    $order = $data->order;
    $orderExist = true;
}

$total = 0;
$payed = 0;
foreach ($data->parent as $pdf) {
    $total += $pdf->total;
    $payed += $pdf->payed;
}

?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <?php icon('info', true); ?>Información del pedido
            <?php echo $parent->code; ?></h4>
    </div>
    <div class="card-block">
        <?php if (!$orderExist && !$cancelled) { errorMsg("El almacén aún no ha generado el pedido"); } ?>
        <?php
        if ($cancelled) {
            $cancellMsg = 'La venta fue anulada por ' . getUsername($parent->cancelled_by) .  ' el ';
            $cancellMsg .= americaDate($parent->cancelled_on) . '<br><br>';
            $cancellMsg .= '<b>Motivo</b> ' . $parent->cancell_reason;
            errorMsg($cancellMsg);
        }
        ?>
        <table class="table table-responsive">
            <thead>
            <tr <?php echo $cancelled ? 'class="deleted"' : ''?>>
                <th>Número de pedido</th>
                <th>Propuesta de pedido</th>
                <th>Imágenes</th>
                <th>Cliente</th>
                <th>Teléfono</th>
                <th>Atendido por</th>
                <th>Tienda</th>
                <th>Fecha de compra</th>
                <th>Fecha de entrega</th>
                <th>Nota de entrega</th>
                <th>Importe</th>
                <th>Estado de pago</th>
            </tr>
            </thead>
            <tbody>
            <tr <?php echo $cancelled ? 'class="deleted"' : ''?>>
                <td><?php echo $parent->code; ?></td>
                <td>
                    <?php
                    $uploadedPdf = 0;
                    if ($parent->pdfname != "") {
                        $uploadedPdf = 1;
                        $pdfTitle = '<b>Cargada el:</b> ' . americaDate($parent->pdf_uploaded_on);
                        $url = "/uploaded-files/pdfs/" . $parent->id . "/" . $parent->pdfname;
                        echo '<a href="' . $url . '" target="_blank" title="' . $pdfTitle . '" class="withqtip">' . icon('pdf', false) . '</a>';
                    } else if ($parent->saletype == 0) {
                        echo '<a class="cursor-pointer withqtip" title="No se cargado ningún pdf"><span class="red-color">' . icon('empty', false) . '</span></a>';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    listDirectory("uploaded-files/images/" . $parent->id . "/secondary/", false);
                    ?>
                </td>
                <td><?php echo $parent->customer; ?></td>
                <td><?php echo $orderExist ? $order->getTelephone() : ''; ?></td>
                <td><?php echo getUsername($parent->created_by); ?></td>
                <td><?php echo getStoreName($parent->storeid); ?></td>
                <td><?php echo americaDate($parent->saledate, false); ?></td>
                <td>
                    <?php
                    if ($orderExist) {
                        $oStatus = $order->getStatus();
                        global $status;
                        if ($oStatus > 2) {
                            echo americaDate($order->getDeliverydate(), false);
                        } else {
                            echo $status[$oStatus];
                        }
                    }

                    ?>
                </td>
                <td>
                    <?php
                    if ($orderExist && $order->getFinishdeliveryfile() != "") {
                        $fileUrl = DELIVERY_FILES_DIR . $order->getId() .  "/" . $order->getFinishdeliveryfile();
                        $finishDeliveryTitle = '<b>Subido por</b> ' . getUsername($order->getFinishdeliverycreatedby()) . ' el ' . americaDate($order->getFinishdeliverycreatedon());
                        ?>
                        <a class="cursor-pointer withqtip" onclick="openUrlInWindow('<?php echo $fileUrl; ?>');" target="_blank" title="<?php echo $finishDeliveryTitle; ?>">
                            <?php icon('word', true); ?>
                        </a>
                        <?php
                    } else {
                        echo '<a class="cursor-pointer withqtip" title="No se cargado ningún documento"><span class="red-color">' . icon('empty', false) . '</span></a>';
                    }
                    ?>
                </td>
                <td><?php echo numberFormat($total, true, 2); ?> &euro;</td>
                <td>
                    <?php
                    global $pandingstatus;
                    $pending = $total - $payed;
                    if ($pending > 0) {
                        $pending = numberFormat($pending, true, 2);
                    } else {
                        $pending = 0;
                    }

                    if ($orderExist) {
                        if ($order->getPendingstatus() == 0) {
                            echo $pandingstatus[$order->getPendingstatus()] . ' ' . $pending . ' &euro;';
                        } else {
                            echo $pandingstatus[$order->getPendingstatus()];
                        }
                    } else {
                        if ($pending == 0) {
                            echo "Pagado";
                        } else {
                            echo $pandingstatus[0] . ' ' . $pending . ' &euro;';
                        }

                    }

                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            Incidencias <?php icon('incidence', true); ?>
        </h4>
    </div>
    <div class="card-block">
        <?php
        $incidences = $orderExist ? $order->getIncidences() : array();
        global $incidenceTypes;
        if (count($incidences) > 0) {
            ?>
            <table class="table table-responsive">
                <thead>
                <tr <?php echo $cancelled ? 'class="deleted"' : ''?>>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Atendida por</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <?php
                global $incidencestatus;
                foreach ($incidences as $incidence) {
                    ?>
                    <tr <?php echo $cancelled ? 'class="deleted"' : ''?>>
                        <td><?php echo americaDate($incidence->incidencedate, false); ?></td>
                        <td><?php echo $incidenceTypes[$incidence->incidencetype]; ?></td>
                        <td><?php echo $incidence->description; ?></td>
                        <td><?php echo $incidence->username; ?></td>
                        <td>
                            <?php
                            $incistatus = $incidence->status;
                            $inciLabel = $incidencestatus[$incistatus];
                            if ($incistatus > 0) {
                                echo $inciLabel . " el " . americaDate($incidence->fixed_on, false);
                            } else {
                                echo $inciLabel;
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        } else {
            confirmationMessage("Sin incidencias");
        }
        ?>
    </div>
</div>
<?php if (count($data) > 0) {
    global $user;
    global $estimateOrigins;
    ?>
    <thead>
    <?php
    if (userWithPrivileges()) {
        $server = $_SERVER['SERVER_NAME'];

        $urlpdf = '/mpdf60.php?controller=store';
        if ($_POST['code'] != "") {
            $method = '&opt=getPdfChildrenSales';
        } else {
            $method = '&opt=getPdfEstimates';
        }

        $urlpdf .= $method;

        $urlpdf .= '&logowidth=150&pdfName=Listado de presupuestos';
        $urlpdf .= '&logo=http://' . $server . '/images//' . LOGO_PDF . '&orientation=P';

        $urlpdf .= $data[0]->pdfparameters . '&generatepdf';
        ?>
        <tr style="display: none;">
            <td colspan="12"></td>
            <td><a href="<?php echo $urlpdf; ?>" target="_blank"><?php icon('pdf', true); ?></a></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <!--<th>Id</th>-->
        <th>Código</th>
        <th>Venta creada</th>
        <th>Fecha de presupuesto</th>
        <!--< ?php if ($controller->canIseeTheInitialSale($data->storeid)) { ?>
            <th>Venta inicial creada</th>
        < ?php } ? >
        -->
        <th>Cliente</th>
        <th>Teléfono</th>
        <th>Importe Total</th>
        <th>Notas</th>
        <th>Archivos</th>
        <th>Creado el</th>
        <th>Creado por</th>
        <th>Tienda</th>
        <th>Origen del presupuesto</th>
        <th style="display: none;"><?php icon('pdf', true); ?></th>
        <?php
        if ($user->getUseraccounting() == 0 && $user->getUserrepository() == 0) {
            echo '<th>Editar</th>';
        }

        if (isadmin() || $user->getUsermanager() == 1) {
            echo '<th style="display: none;">Anular</th>';
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    $total = 0;
    $totalNegative = 0;
    $totalPositive = 0;
    $totalPays = 0;
    $totalSaless = 0;

    global $saletypes;
    global $paymethods;

    foreach ($data as $pdf) {
        // Nos permite controlar si debemos mostrar el botón de ajuste
        
        $comments = $pdf->comments;
        $cancelled = false;
        $cancelled_class = '';
        $cancelled_title = '';
        if ($pdf->cancelled == 1) {
            $cancelled = true;
            $cancelled_class = ' deleted cursor-pointer trwithqtip';
            $cancelled_title = 'Anulado por <b>' . getUsername($pdf->cancelled_by) . '</b> el ' . americaDate($pdf->cancelled_on);
            $cancelled_title .= '<br><b>Motivo: </b>' . $pdf->cancell_reason;
        }

        $trClass = 'class="table-active ' . $cancelled_class . ' "';
        $total += $pdf->total;

        $btnSetAsinitial = '<a class="btn btn-primary cursor-pointer withqtip" style="color: #fff;" onclick="setEstimateAsSaleInitial(' . $pdf->id . ', \''. $pdf->code .'\')" title="Crear venta inicial">>>venta inicial</a>';
        $linkToInitialSale = "";
        $linkToShowInitialSale = "";

        ?>
        <tr <?php echo $trClass ?> id="tr-<?php echo $pdf->id; ?>" <?php echo $cancelled ? 'title="' . $cancelled_title . '"' : ''; ?>>
            <!--<td>
                < ?php echo $pdf->id; ?>
            </td>-->
            <td>
                <?php echo $pdf->code; ?>
            </td>
            <td>
                <?php 
                    if((int)$pdf->status === 1) {
                        $totalSaless ++;
                        echo  ('<span class="text-success">' . icon('checked', false));
                    } else {
                        echo '<a href="'.getUrl('save_sale', $controller->getUrls(), $pdf->id).'" title="Registrar venta">'.icon('save', false).'</a>';
                    }
                ?>
            </td>
            <td><?php echo americaDate($pdf->saledate, false); ?></td>
            <td>
                <?php echo $pdf->customer; ?>
            </td>
            <td>
                <?php echo $pdf->tel; ?>
            </td>
            <td>
                <?php
                    echo numberFormat($pdf->total, true, 2) . "&euro;<br>";
                ?>
            </td>

            <td class="text-center">
                <?php
                    if (count($comments) > 0) {
                        $mycomments = '<table>';
                        foreach ($comments as $intern) {
                            $usercomment = $intern->username;
                            $commentDate = americaDate($intern->created_on, true);
                            $comment = $intern->comment;
                            $mycomments .=  '<tr><td><b>' . $usercomment . '</b> ' . icon('calendar', false, true) . '(' . $commentDate . '): ' . $comment . '</tr></td>';
                        }
                        $mycomments .= '</table>';
                    ?>
                        <a class="withqtip cursor-pointer custom-lk" title="<?php echo $mycomments; ?>">
                            <?php icon('comments', true); ?>
                        </a>
                <?php } ?>
            </td>
            <td>
                <?php
                    listDirectory("uploaded-files/estimates/" . $pdf->id . "/secondary", false);
                ?>
            </td>

            <td><?php echo americaDate($pdf->created_on); ?></td>
            <td>
                <?php
                if (empty($_POST['code'])) {
                    echo $pdf->username;
                } else {
                    echo getUsername($pdf->created_by);
                }
                ?>
            </td>
            <td class="text-center"><?php echo getStoreName($pdf->storeid); ?></td>
            <td class="text-center"><?php echo $estimateOrigins[$pdf->estimateorigin] ?? ''; ?></td>
            <td style="display: none;">
                <?php
                    $urlpdf = $controller->getEstimatePdfUrl($pdf->id, $pdf->code);
                ?>
                <a href="<?php echo $urlpdf; ?>" target="_blank"><?php icon('pdf', true); ?></a>
            </td>
            <?php
            if ($user->getUseraccounting() == 0 && $user->getUserrepository() == 0) {?>
                <td class="text-center" id="td-edit-<?php echo $pdf->id;?>">
                    <?php
                    if (!$cancelled) {
                        $getUrl = 'ajax_edit_estimate';

                        if (!isTimeOver($pdf->created_on)) {
                            ?>
                            <a href="<?php echo getUrl($getUrl, $controller->getUrls(), $pdf->id); ?>">
                                <?php icon('edit', true); ?>
                            </a>
                            <?php
                        } else {
                            $title = 'Sólo lectura.<br>Es posible que haya pasado más de una hora desde que creó la venta o ya existe un pedido.'
                            ?>
                            <a href="<?php echo getUrl($getUrl, $controller->getUrls(), $pdf->id); ?>"
                               title="<?php echo $title; ?>" class="withqtip">
                                <?php icon('view', true); ?>
                            </a>
                            <?php
                        }

                    }
                    ?>
                </td>
            <?php }
            if (isadmin() || $user->getUsermanager() == 1) {
                echo '<td id="td-delete-' . $pdf->id . '" style="display: none;">';
                if (!$cancelled) {
                    echo '<a class="cursor-pointer red-color" data-target="#cancelSale" data-toggle="modal" onclick="setSaleToCancelId(\'' . $pdf->code . '\', '. $pdf->id . ', 0);">' . icon('delete', false) . '</a>';
                }
                echo '</td>';
            }
            ?>
        </tr>
        <?php
        $i++;
    }

    ?>


    <tr>
        <td><b>Total presupuestos: </b></td>
        <td colspan="2" align="right"><b><?php echo numberFormat($total, true, 2); ?> &euro;</b></td>
        <td colspan="8">&nbsp;</td>
    </tr>


    <tr>
        <td colspan="9"><b>Resultados: <?php echo $i; ?></b></td>
    </tr>
    <?php if ($controller->canIseeTheInitialSale($pdf->storeid)) { ?>
        <tr>
            <td colspan="9"><b>Ventas: <?php echo $totalSaless; ?></b></td>
        </tr>
        <tr>
            <td colspan="9"><b>Sin ventas: <?php echo (int)($i - $totalSaless); ?></b></td>
        </tr>
    <?php } ?>
    </tbody>
<?php } else {
    errorMsg('Sin resultados');
}
?>

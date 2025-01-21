<?php if (count($data) > 0) {
    global $user;
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

        $urlpdf .= '&logowidth=150&pdfName=Listado de ventas';
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
        <th>Id</th>
        <th>Código</th>
        <th>Fecha de presupuesto</th>
        <!--< ?php if ($controller->getModel()->canIseeTheInitialSale($data->storeid)) { ?>
            <th>Venta inicial creada</th>
        < ?php } ? >
        -->
        <th>Cliente</th>
        <th>Importe Total</th>
        <th>Nota de venta</th>
        <th>Archivos</th>
        <th>Creado el</th>
        <th>Creado por</th>
        <th>Tienda</th>
        <th><?php icon('pdf', true); ?></th>
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
    $totalInitialSales = 0;

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


        if ($pdf->salegenerated == 1) {
            $totalInitialSales++;
            $trClass = 'class="table-success ' . $cancelled_class . '"';
        }

        $saleGenerated = "<b>No</b>";
        $btnSetAsinitial = '<a class="btn btn-primary cursor-pointer withqtip" style="color: #fff;" onclick="setEstimateAsSaleInitial(' . $pdf->id . ', \''. $pdf->code .'\')" title="Crear venta inicial">>>venta inicial</a>';
        $linkToInitialSale = "";
        $linkToShowInitialSale = "";

        if ($pdf->salegenerated == 1) {
            $btnSetAsinitial = "";
            $saleGenerated = "El " . americaDate($pdf->nextSaleDate, false);
            $linkToInitialSale = '<a href="' . $controller->getModel()->getInitialSalePdfUrl($pdf->id, $pdf->code) . '" target="_blank">' . icon('pdf', false) . '</a><br>';
            $linkToShowInitialSale = '<a href="' . getUrl('ajax_edit_initial_sale', $controller->getModel()->getUrls(), $pdf->id) . '" target="_blank">' . icon('view', false) . '</a>';

        }

        ?>
        <tr <?php echo $trClass ?> id="tr-<?php echo $pdf->id; ?>" <?php echo $cancelled ? 'title="' . $cancelled_title . '"' : ''; ?>>
            <td>
                <?php echo $pdf->id; ?>
            </td>
            <td>
                <?php echo $pdf->code; ?>
            </td>
            <td><?php echo americaDate($pdf->saledate, false); ?></td>
            <!--< ?php if ($controller->getModel()->canIseeTheInitialSale($pdf->storeid)) { ?>
                <td style="text-align: center;" id="initial-<?php echo $pdf->id; ?>">
                    <?php
                    echo $saleGenerated . '<br>';
                    if ($pdf->salegenerated == 1) {
                        echo $linkToShowInitialSale;
                        echo $linkToInitialSale;
                    }
                    echo $btnSetAsinitial;

                    $urlpdf = $controller->getModel()->getInitialSalePdfUrl($pdf->id, $pdf->code);
                    ?>
                </td>
            < ?php } ?>
            -->
            <td>
                <?php echo $pdf->customer; ?>
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
                    listDirectory("uploaded-files/estimates/" . $pdf->id . "/", false);
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
            <td>
                <?php
                    $urlpdf = $controller->getModel()->getEstimatePdfUrl($pdf->id, $pdf->code);
                ?>
                <a href="<?php echo $urlpdf; ?>" target="_blank"><?php icon('pdf', true); ?></a>
            </td>
            <?php
            if ($user->getUseraccounting() == 0 && $user->getUserrepository() == 0) {?>
                <td class="text-center" id="td-edit-<?php echo $pdf->id;?>">
                    <?php
                    if (!$cancelled) {
                        $getUrl = 'ajax_edit_estimate';

                        if (!isTimeOver($pdf->created_on) || $pdf->salegenerated == 0) {
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
    <?php if ($controller->getModel()->canIseeTheInitialSale($pdf->storeid)) { ?>
        <tr>
            <td colspan="9"><b>Sin ventas: <?php echo (int)($i - $totalInitialSales); ?></b></td>
        </tr>
        <tr>
            <td colspan="9"><b>Ventas iniciales: <?php echo $totalInitialSales; ?></b></td>
        </tr>
    <?php } ?>
    </tbody>
<?php } else {
    errorMsg('Sin resultados');
}
?>

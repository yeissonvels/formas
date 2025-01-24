<script>
    function confirmRestore(id) {
        if (confirm('Estás seguro de que deseas devolver el PDF a la tienda?')) {
            window.location.href = '<?php echo getUrl('restore_pdf', $myController->getUrls());?>' + id;
        }
    }

    function confirmValidation(id) {
        if (confirm('Confirmas que has impreso la propuesta de pedido?')) {
            //window.location.href = '< ?php echo getUrl('pdf_yet_printed', $myController->getUrls());?>' + id;
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'pdfYetPrinted',
                    id: id
                }
            }).done(function(Response) {
                var res = JSON.parse(Response);
                if (res['updated'] == 1) {
                    $('#id' + id).addClass('table-success');
                    $('#validLabel' + id).html('<?php icon('checked', true) ?>');
                }
            });
        }
    }
</script>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Entregas <?php icon('truck', true); ?></h4>
        <hr>
        <div class="form-group row">
            <label for="month" class="col-sm-1 col-form-label">Filtros <?php icon('calendar', true);?></label>
            <div class="col-sm-3 mb-1">
                <form action="" method="post" id="frm1">
                    <select id="filter" name="filter" class="form-select" onchange="if($(this).val() != '') {$('#frm1').submit();}">
                        <option value="">Seleccione una opción</option>
                        <option value="all" <?php echo isset($_POST['filter']) && $_POST['filter'] == "all" ? 'selected' : '' ?>>Todos</option>
                        <option value="onlywithpdf" <?php echo isset($_POST['filter']) && $_POST['filter'] == "onlywithpdf" ? 'selected' : '' ?>>Sólo con propuestas</option>
                        <option value="pdfyetprinted" <?php echo isset($_POST['filter']) && $_POST['filter'] == "pdfyetprinted" ? 'selected' : '' ?>>Propuestas ya impresas</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="card-block">
        <?php if (count($data) > 0) { ?>
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>Fecha de venta</th>
                    <th>Código</th>
                    <th>Cliente</th>
                    <th>Documentos adicionales</th>
                    <th>Propuesta de pedido</th>
                    <th>Subido por</th>
                    <th>Tienda</th>
                    <th>Notas <?php icon('comments', true); ?></th>
                    <th>Crear pedido</th>
                    <th>Modificar pedido</th>
                    <th>Devolver a la tienda</th>
                </tr>
                </thead>
                <tbody id="dynamic-cus">
                <?php
                $storeController = new StoreController();
                foreach ($data as $pdf) {
                    $checkExistOrder = $myController->isOrderCreated($pdf->id);
                    $orderExist = $checkExistOrder[0];
                    $comments = $storeController->getNewPdfComments($pdf->id);

                    $trClass = "";
                    if ($pdf->reuploaded == 1 && $pdf->pdfname == "") {
                        $trClass = "table-danger";
                    } else if ($pdf->pdf_yet_printed == 1) {
                        $trClass = "table-success";
                    }

                ?>
                <tr class="<?php echo $trClass; ?>" id="id<?php echo $pdf->id; ?>">
                    <td><?php echo americaDate($pdf->saledate, false); ?></td>
                    <td><?php echo $pdf->code; ?></td>
                    <td><?php echo $pdf->customer; ?></td>
                    <td>
                        <?php
                            listDirectory("uploaded-files/images/" . $pdf->id . "/secondary/", false);
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($pdf->pdfname != "") {
                            //$url = "/uploaded-files/pdfs/" . $pdf->id . "/" . $pdf->pdfname;
                            //echo '<a class="cursor-pointer" onclick="openUrlInWindow(\'' . $url . '\');">' . icon('pdf', false) . '</a>';
                            listDirectory("uploaded-files/pdfs/" . $pdf->id, false);
                            if ($pdf->pdf_yet_printed == 0) {
                                echo '<span id="validLabel' . $pdf->id . '">';
                                echo '<br><a onclick="confirmValidation(' . $pdf->id . ');" class="cursor-pointer" style="text-decoration: underline;">';
                                echo 'Validar como impresa</a></span>';
                            } else {
                                icon('checked', true);
                            }

                        } else {
                            echo '<span class="red-color cursor-pointer" title="No se cargado ningún pdf">' . icon('empty', false) . '</span>';
                        }
                        ?>
                    </td>
                    <td><?php echo $pdf->username; ?></td>
                    <td><?php echo $pdf->storename; ?></td>
                    <td class="text-center">
                        <?php if (count($comments) > 0) {
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
                    <td class="text-center">
                        <?php
                        if (!$orderExist && $pdf->pdfname != "") {
                            echo '<a href = "' . getUrl('new_frompdf', $myController->getUrls(), $pdf->id) . '" >' . icon('save', false) . '</a>';
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if ($orderExist) {
                            echo '<a href="' . getUrl('new', $myController->getUrls(), $checkExistOrder[1]) . '">' . edit_icon(false) . '</a>';
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php if ($pdf->pdfname != "") { ?>
                            <a class="cursor-pointer" onclick="confirmRestore(<?php echo $pdf->id; ?>)"><?php icon('restore', true); ?></a>
                        <?php } else if ($pdf->reuploaded == 1 && $pdf->pdfname == "") {
                                    echo "Devuelto el " . americaDate($pdf->returned_on);
                              }
                        ?>
                    </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        <?php
            } else {
                confirmationMessage('No existen órdenes pendientes.');
            }
        ?>
    </div>
</div>
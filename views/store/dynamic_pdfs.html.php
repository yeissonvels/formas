<?php if (count($data) > 0) {
    global $user;

    //pre($data);
    ?>
    <thead>
    <?php
    if (userWithPrivileges()) {
        $server = $_SERVER['SERVER_NAME'];

        $urlpdf = '/mpdf60.php?controller=store';
        if ($_POST['code'] != "") {
            $method = '&opt=getPdfChildrenSales';
        } else {
            $method = '&opt=getPdfSales';
        }

        $urlpdf .= $method;

        $urlpdf .= '&logowidth=150&pdfName=Listado de ventas';
        $urlpdf .= '&logo=http://' . $server . '/images//' . LOGO_PDF . '&orientation=P';

        $urlpdf .= $data[0]->pdfparameters . '&generatepdf';
        ?>
        <tr>
            <td colspan="12"></td>
            <td><a href="<?php echo $urlpdf; ?>" target="_blank"><?php icon('pdf', true); ?></a></td>
        </tr>
        <?php
    }
    ?>
    <tr>
        <th>Tipo</th>
        <th>Fecha de venta</th>
        <th>Nº de pedido</th>
        <th>Titular del pedido</th>
        <th>Importe Total</th>
        <th>Abonado a cuenta</th>
        <th>Mediante</th>
        <th>Nota de venta</th>
        <th>Otros documen.</th>
        <th>Propuesta de pedido</th>
        <th>Creado el</th>
        <th>Creado por</th>
        <th>Tienda</th>
        <?php
        if ($user->getUseraccounting() == 0 && $user->getUserrepository() == 0) {
            echo '<th>Editar</th>';
        }

        if (isadmin() || $user->getUsermanager() == 1) {
            echo '<th>Anular</th>';
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

    global $saletypes;
    global $paymethods;
    $existAdjust = false;

    foreach ($data as $pdf) {
        // Nos permite controlar si debemos mostrar el botón de ajuste
        if ($pdf->saletype == 3) {
            $existAdjust = true;
        }
        $comments = array();
        if ($pdf->saletype != 3) {
            $comments = $controller->getNewPdfComments($pdf->id);
        }

        $parent = array();
        $cancelled = false;
        $cancelled_class = '';
        $cancelled_title = '';
        if ($pdf->cancelled == 1) {
            $cancelled = true;
            $cancelled_class = ' deleted cursor-pointer trwithqtip';
            $cancelled_title = 'Anulado por <b>' . getUsername($pdf->cancelled_by) . '</b> el ' . americaDate($pdf->cancelled_on);
            $cancelled_title .= '<br><b>Motivo: </b>' . $pdf->cancell_reason;
        }

        if ($pdf->saletype > 0) {
            $parent = $controller->getPdfData($pdf->parentcode);
        }

        $trClass = 'class="table-success ' . $cancelled_class . ' "';

        // Totalizamos los pedidos que no hayan sido anulados
        if (!$cancelled) {
            if ($pdf->saletype == 0) {
                $total += $pdf->total;
            } else {
                if ($pdf->total < 0) {
                    $totalNegative += $pdf->total;
                } else {
                    $totalPositive += $pdf->total;
                }
            }

            $totalPays += $pdf->payed;
        }

        echo $pdf->payed . "<br>";

        if ($pdf->cancelled == 1) {
            $trClass = 'class="table-active ' . $cancelled_class . '"';
        } else if ($pdf->saletype == 1 && $pdf->total > 0) {
            $trClass = 'class="table-success ' . $cancelled_class . '"';
        } else if ($pdf->saletype == 1 && $pdf->total < 0) {
            $trClass = 'class="table-danger ' . $cancelled_class . '"';
        } else if ($pdf->saletype == 2) {
            $trClass = 'class="table-info ' . $cancelled_class . '"';
        } else if ($pdf->saletype == 3) {
            $trClass = 'class="table-active ' . $cancelled_class . '"';
        }

        ?>
        <tr <?php echo $trClass ?> id="tr-<?php echo $pdf->id; ?>" <?php echo $cancelled ? 'title="' . $cancelled_title . '"' : ''; ?>>
            <td>
                <?php
                $icon = 'money';
                if ($pdf->saletype == 1) {
                    $icon = 'exchange';
                } else if ($pdf->saletype == 3) {
                    $icon = 'calculator';
                }
                echo ($saletypes[$pdf->saletype] ?? '') . ' ' . icon($icon, false);
                ?>
            </td>
            <td><?php echo americaDate($pdf->saledate, false); ?></td>
            <td>
                <?php
                    if ($pdf->saletype == 0) {
                        $code =  $pdf->code;
                        $customer = $pdf->customer;
                        $jsId = $pdf->id;
                    } else {
                        $code = $parent->code;
                        $customer = $parent->customer;
                        $jsId = $pdf->parentcode;
                    }

                    $jsCode = $code . ' (' . americaDate($pdf->saledate, false) . ') ' . $customer;
                    ?>
                    <a class="cursor-pointer" onclick="searchByOrderCode('<?php echo $jsId; ?>', '<?php echo $jsCode; ?>');" style="color: #014c8c;"><?php echo $code; ?></a>

            </td>
            <td>
                <?php
                    if ($pdf->saletype != 3) {
                        echo $customer;
                    }
                ?>
            </td>
            <td>
                <?php
                if ($pdf->saletype < 2) {
                    echo numberFormat($pdf->total, true, 2) . "&euro;<br>";

                    if ((isadmin() || $user->getUseraccounting() == 1 || $user->getUsermanager() == 1 || $user->getUserrepository() == 1) && !$cancelled) {
                        if ($pdf->saletype == 0) {
                          $existVariation = false;
                          $variationValue = 0;
                          if (isset($pdf->variations)) {
                      		  //pre($pdf->variations);
                              $existVariation = true;
							  $varPositive = !empty($pdf->variations->positive) ? $pdf->variations->positive : 0;
							  $totalPositive += $varPositive;
							  $varNegative = !empty($pdf->variations->negative) ? $pdf->variations->negative : 0;
							  $totalNegative += $varNegative;
                              $variationValue = $varPositive + $varNegative;
                          }
                          echo icon('exchange', false) . ' ' . numberFormat($variationValue, true, 2) . ' &euro;<br>';
                          if ($variationValue != 0) {
                              echo 'Real: ' . numberFormat(($pdf->total + $variationValue), true, 2) . ' &euro;<br>';
                          }
                        }
                        echo "<div id='totalvalidation" . $pdf->id . "'>";
                        if ($pdf->total_checked_by != 0) {
                            $title = 'Validado por ' . getUsername($pdf->total_checked_by) . ' el ' . americaDate($pdf->total_checked_on, false) . '<br>';
                            $title .= '<b>Nota: </b>' . $pdf->total_checked_note;
                            $html = '<a class="withqtip cursor-pointer" title="' . $title . '">' . icon('checked') . ' ' . icon('comments') . '</a>';
                            if (isadmin() || !isTimeOver($pdf->total_checked_system_date) || $user->getUsermanager() == 1) {
                                $html .= '<br><a class="cursor-pointer red-color" onclick="deleteTotalValidation(' . $pdf->id . ');">Eliminar' . icon('delete') . '</a>';
                            }
                            echo $html;
                        } else {
                            echo '<span class="red-color">No validado<br>';
                            if ($user->getUserrepository() != 1) {
                                echo '<input type="button" value="Validar total" class="btn btn-success" style="font-size: 10px;" data-bs-target="#checkTotal" data-bs-toggle="modal" onclick="setTotalId(' . $pdf->id . ')">';
                            }

                        }
                        echo "</div>";
                    }
                }
                ?>
            </td>
            <td>
                <?php
                    echo numberFormat($pdf->payed, true, 2) . " &euro;";
                    if ($pdf->saletype != 3) {
                        if ((isadmin() || $user->getUseraccounting() == 1 || $user->getUsermanager() == 1 || $user->getUserrepository() == 1) && !$cancelled) {
                            echo "<div id='validation" . $pdf->id . "'>";
                            if ($pdf->accounting_checked_by != 0) {
                                $title = 'Validado por ' . getUsername($pdf->accounting_checked_by) . ' el ' . americaDate($pdf->accounting_checked_on, false) . '<br>';
                                $title .= '<b>Nota: </b>' . $pdf->accounting_checked_note;
                                $html = '<a class="withqtip cursor-pointer" title="' . $title . '">' . icon('checked') . ' ' . icon('comments') . '</a>';
                                if (!isTimeOver($pdf->accounting_checked_system_date) || $user->getUsermanager() == 1) {
                                    $html .= '<br><a class="cursor-pointer" data-bs-target="#checkPayment" data-bs-toggle="modal" onclick="changeValidate(' . $pdf->id . ');">Cambiar' . icon('edit') . '</a>';
                                    $html .= '<br><a class="cursor-pointer red-color" onclick="deleteValidation(' . $pdf->id . ');">Eliminar' . icon('delete') . '</a>';
                                }
                                echo $html;
                            } else {
                                echo '<span class="red-color">No validado</span><br>';
                                if ($user->getUserrepository() != 1) {
                                    echo '<input type="button" value="Validar pago" class="btn btn-success" style="font-size: 10px;" data-bs-target="#checkPayment" data-bs-toggle="modal" onclick="setPaymentId(' . $pdf->id . ')">';
                                }

                            }
                            echo "</div>";
                        } else {
                        	// Activamos las validaciones a las tiendas
                        	if ($user->getUserstore() == 1) {
                        		echo "<div id='validation" . $pdf->id . "'>";
		                            if ($pdf->accounting_checked_by != 0) {
		                                $title = 'Validado por ' . getUsername($pdf->accounting_checked_by) . ' el ' . americaDate($pdf->accounting_checked_on, false) . '<br>';
		                                $title .= '<b>Nota: </b>' . $pdf->accounting_checked_note;
		                                $html = '<a class="withqtip cursor-pointer" title="' . $title . '">' . icon('checked') . ' ' . icon('comments') . '</a>';
		                                echo $html;
		                            } else {
		                                echo '<span class="red-color">No validado</span>';
		                            }
                               echo "</div>";
                        	}
                        }
                    }
                ?>
            </td>
            <td>
                <?php
                if ($pdf->saletype == 0) {
                    echo $paymethods[$pdf->paymethod];
                } else {
                    if ($pdf->payed > 0) {
                        echo $paymethods[$pdf->paymethod];
                    } else {
                        echo "----------";
                    }
                }

                ?>
            </td>
            <td class="text-center">
                <?php
                    // Los comentarios se almacenan en otra tabla, pero si el saletype es de tipo 3 (ajuste de pendiente)
                    // Se guarda en el campo comment
                    if (count($comments) > 0 || $pdf->saletype == 3) {
                        $mycomments = '<table>';
                        if ($pdf->saletype == 3) {
                            $created_by = ($_POST['code'] == "") ? $pdf->username : getUsername($pdf->created_by);
                            $mycomments .=  '<tr><td><b>' . $created_by . '</b> ' . icon('calendar', false, true) . '(' . americaDate($pdf->created_on, false) . '): ' . $pdf->comment . '</tr></td>';
                        } else {
                            foreach ($comments as $intern) {
                                $usercomment = $intern->username;
                                $commentDate = americaDate($intern->created_on, true);
                                $comment = $intern->comment;
                                $mycomments .=  '<tr><td><b>' . $usercomment . '</b> ' . icon('calendar', false, true) . '(' . $commentDate . '): ' . $comment . '</tr></td>';
                            }
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
                if ($pdf->saletype < 2) {
                    listDirectory("uploaded-files/images/" . $pdf->id . "/secondary/", false);
                }

                ?>
            </td>
            <td>
                <?php
                    $uploadedPdf = 0;
                    if ($pdf->pdfname != "") {
                        $uploadedPdf = 1;
                        $pdfTitle = '<b>Cargada el:</b> ' . americaDate($pdf->pdf_uploaded_on);
                        $url = "/uploaded-files/pdfs/" . $pdf->id . "/" . $pdf->pdfname;
                        echo '<a href="' . $url . '" target="_blank" title="' . $pdfTitle . '" class="withqtip">' . icon('pdf', false) . '</a>';
                        listDirectory('uploaded-files/pdfs/' . $pdf->id, false, array('divresponse' => '', 'excludes' => array($pdf->pdfname)));
                        echo "<div id='commission" . $pdf->id . "'>";
                        if ($pdf->commissionpayed == 1) {
                            $commtitle = 'Por <b>' . getUsername($pdf->commission_validated_by) . '</b> el ' . americaDate($pdf->commission_payed_on, false) . '<br>';
                            $commhtml = icon('checked', false) . '<span class="green-color"> Propuesta validada</span> ';
                            $commhtml .= '<a class="withqtip cursor-pointer" title="' . $commtitle . '">' . icon('comments') . '</a>';
                            if (isadmin() || $user->getUsermanager() == 1 && !$cancelled) {
                                $commhtml .= '<br><a class="cursor-pointer red-color" onclick="deletecommission(' . $pdf->id . ');">Eliminar' . icon('delete') . '</a>';
                            }

                            echo $commhtml;
                        } else {
                            $commison =  '<br><span class="red-color">No validada</span>';
                            if (isadmin() || $user->getUsermanager() == 1 && !$cancelled) {
                                $commison .= '<input type="button" value="Validar propuesta" class="btn btn-success" style="font-size: 10px;" data-bs-target="#checkcommission" data-bs-toggle="modal" onclick="setcommissionId(' . $pdf->id . ')">';
                            }
                            echo $commison;
                        }
                        echo '</div>';
                    } else if ($pdf->saletype == 0) {
                        echo '<a class="cursor-pointer withqtip" title="No se cargado ningún pdf"><span class="red-color">' . icon('empty', false) . '</span></a>';
                    }

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
            <?php
            if ($user->getUseraccounting() == 0 && $user->getUserrepository() == 0) {?>
                <td class="text-center" id="td-edit-<?php echo $pdf->id;?>">
                    <?php
                    if (!$cancelled) {
                        if ($pdf->saletype != 2) {
                            $getUrl = 'ajax_edit_pdf';
                        } else {
                            $getUrl = 'add_pay';
                        }

                        if ($pdf->saletype != 3) {
                            if ($pdf->orderexist == 0 && !isTimeOver($pdf->created_on)) {
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
                        } else {
                            $myCode = $_POST['code'] != "" ? $_POST['code'] : $pdf->parentcode;
                            echo '<br><a href="#" onclick="setAdjustid(' . $myCode . ', \''. $pdf->code .'\', \'update\', ' . $pdf->id . ')" data-bs-toggle="modal" data-bs-target="#checkPendingPay" title="Ajustar pendiente de pago">' . icon('calculator') . '</a>';
                        }
                    }
                    ?>
                </td>
            <?php }
            if (isadmin() || $user->getUsermanager() == 1) {
                echo '<td id="td-delete-' . $pdf->id . '">';
                if (!$cancelled && $pdf->saletype != 3) {
                    echo '<a class="cursor-pointer red-color" data-bs-target="#cancelSale" data-bs-toggle="modal" onclick="setSaleToCancelId(\'' . $pdf->code . '\', '. $pdf->id . ', '. $pdf->saletype .');">' . icon('delete', false) . '</a>';
                }
                echo '</td>';
            }
            ?>
        </tr>
        <?php
        $i++;
    }

    $totalReal = $total + $totalPositive + $totalNegative;
    
    if (userWithPrivileges() || isset($_POST['code'])) {
         
    ?>

    <tr>
        <td colspan="4"></td>
        <td class="bold">Abonos</td>
        <td class="bold"><?php echo numberFormat($totalPays, true, 2); ?> &euro;</td>
    </tr>


    <tr>
        <td><b>Total ventas (sin variaciones): </b></td>
        <td colspan="2" align="right"><b><?php echo numberFormat($total, true, 2); ?> &euro;</b></td>
        <td colspan="8">&nbsp;</td>
    </tr>
    <tr>
        <td><b>Variaciones positivas: </b></td>
        <td colspan="2" align="right" class="green-color"><b><?php echo numberFormat($totalPositive, true, 2); ?> &euro;</b></td>
        <td colspan="8">&nbsp;</td>
    </tr>
    <tr>
        <td><b>Variaciones negativas</b></td>
        <td colspan="2" align="right" class="red-color"><b><?php echo numberFormat($totalNegative, true, 2); ?>&euro;</b></td>
        <td colspan="8">&nbsp;</td>
    </tr>
    <tr>
        <td><b>TOTAL REAL</b></td>
        <td colspan="2" align="right"><b><?php echo numberFormat($totalReal, true, 2); ?>&euro;</b></td>
        <td colspan="8">&nbsp;</td>
    </tr>

    <tr>
        <td><b>PENDIENTE DE PAGO</b></td>
        <?php
            $leyend = '';
            $totalPending = ($totalReal - $totalPays);
            $totalPending = numberFormat($totalPending, true, 2);
            if ($totalPending < 0 && $_POST['code'] != "") {
                $leyend = '<span class="red-color">¿Ha pagado el cliente de más?</span>';
            }

            if ($totalPending == '-0,00') {
                $totalPending = '0,00';
            }
        ?>
        <td colspan="2" align="right">
            <?php
                echo '<b>' . $totalPending . ' &euro;</b>';
                if (isadmin() || $user->getUsermanager() == 1 || $user->getUseraccounting() == 1) {
                    if ($_POST['code'] != "" && $totalPending != 0 && !$existAdjust) {
                        echo '<br><button onclick="setAdjustid(' . $_POST['code'] . ', \''. $data[0]->code .'\', \'save\', 0)" data-bs-toggle="modal" data-bs-target="#checkPendingPay" value="Ajustar" class="btn btn-primary" title="Ajustar pendiente de pago">' . icon('calculator') . '</button>';
                    }
                }
            ?>
        </td>
        <td colspan="4"><?php echo $leyend; ?></td>
        <td colspan="3">&nbsp;</td>
    </tr>
    
    <?php } ?>

    <tr>
        <td colspan="9"><b>Resultados: <?php echo $i; ?></b></td>
    </tr>
    </tbody>
<?php } else {
    errorMsg('Sin resultados');
}
?>

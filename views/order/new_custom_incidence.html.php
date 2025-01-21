<?php
    $editing = isset($ajaxincidence) ? true : false;
    $canEditIncidence = false;
    $incidenceDisabled = 'disabled="disabled"';

    if (!$editing) { // No hemos creado el pedido aún
        $canEditIncidence = true;
    } else if ($editing && $ajaxincidence->status == 0) {
        // Si el pedido no se ha entregado lo podemos modificar.
        $canEditIncidence = true;
    }

    if ($canEditIncidence) {
        $incidenceDisabled = '';
    }

    $totalItems = 2;

    $countProducts = 2;

    if ($editing) {
        if (count($ajaxincidence->items) > 0) {
            $totalItems = count($ajaxincidence->items) + 1;
        }

        if (count($ajaxincidence->internProducts) > 0) {
            $countProducts = count($ajaxincidence->internProducts) + 1;
        }
    }

    datePicker(array('incidencedate', 'fixed_on', 'inci-deliverydate'));

?>
<script>
    // Items internos
    var internProducts = <?php echo $countProducts; ?>;
    function moreInternProducts(target) {
        var html = '<tr id="inproduct' + internProducts + '" class="table-success">';
        html +=     '<td class="w-50">';
        html += '<select class="form-control products" id="inselect' + internProducts + '" name="products[]">';
        html +=         '<option value="">Seleccione un producto</option>';
        <?php
            if (isset($ajaxProducts)) {
                foreach ($ajaxProducts as $product) {
                    echo '   html += \'<option value="' . $product->id . '">' . $product->productname . '</option>\';' . PHP_EOL;
                }
            }
        ?>
        html +=         '</select>';
        html +=     '</td>';
        html +=     '<td class="w-50">';
        html += '<select class="form-control categories" id="incategory' + internProducts + '" name="categories[]">';
        html +=         '<option value="">Seleccione un producto</option>';
        <?php
        if (isset($ajaxCategories)) {
            foreach ($ajaxCategories as $category) {
                echo '   html += \'<option value="' . $category->id . '">' . $category->category . '</option>\';' . PHP_EOL;
            }
        }
        ?>
        html +=         '</select>';
        html +=     '</td>';
        html +=     '<td>';
        html +=     '   <a style="cursor: pointer;" onclick="deleteInternProduct(' + internProducts + ')"><?php icon('delete', true); ?></a>';
        html +=     '</td>';
        html += '</tr>';
        html += '<tr id="inproductdate' + internProducts + '">';
        html +=   '<td colspan="2">';
        html +=       '<table class="table-striped">';
        html +=            '<tr>';
        html +=                '<td>Fabricación <input type="text" class="form-control" id="inmanufacturing' + internProducts + '" name="manufacturings[]">';
        html +=                '</td>';
        html +=                '<td>Acabado <input type="text" class="form-control" id="infinish' + internProducts + '" name="finishes[]"></td>';
        html +=                '<td>Almacén <input type="text" class="form-control" id="instore' + internProducts + '" name="stores[]"></td>';
        html +=            '</tr>';
        html +=         '</table>';
        html +=    '</td>';
        html += '</tr>';

        $('#' + target).append(html);

        jQuery(function() {
            jQuery('#inmanufacturing' + internProducts).datepicker();
        });

        jQuery(function() {
            jQuery('#infinish' + internProducts).datepicker();
        });

        jQuery(function() {
            jQuery('#instore' + internProducts).datepicker();
        });

        //scrollingTo('#inproduct' + internProducts);
        var modalScroll = $('#inproduct' + internProducts).offset().top;
        $('.modal').animate({ scrollTop: modalScroll }, 'slow');
        internProducts++;
    }

    function deleteInternProduct(id) {
        $('#inproduct' + id).remove();
        $('#inproductdate' + id).remove();
    }


    // Items de la nota de entrega
    var totalitems = '<?php echo $totalItems; ?>';
    function addIncidenceItem() {
        var itemHtml = '';
        itemHtml += '<tr class="table-success" id="incidenceitem' + totalitems + '">';
        itemHtml += '        <td>Ref.</td>';
        itemHtml += '        <td>';
        itemHtml += '        <input type="text" name="reference[]" id="reference' + totalitems + '" class="form-control" value="">';
        itemHtml += '        </td>';
        itemHtml += '        <td>Descripción</td>';
        itemHtml += '        <td>';
        itemHtml += '        <textarea name="description[]" id="description' + totalitems + '" class="form-control" style="width: 250px; height: 90px; resize: horizontal;"></textarea>';
        itemHtml += '        </td>';
        itemHtml += '        <td>Cantidad</td>';
        itemHtml += '        <td>';
        itemHtml += '        <input type="text" name="quantity[]" id="quantity' + totalitems + '" class="form-control" value="">';
        itemHtml += '        </td>';
        itemHtml += '        <td>Precio</td>';
        itemHtml += '        <td>';
        itemHtml += '        <input type="text" name="price[]" id="price' + totalitems + '" class="form-control" value="" onkeyup="addCommas($(this).prop(\'id\'), $(this).val());">';
        itemHtml += '        </td>';
        itemHtml += '        <td>Dcto%</td>';
        itemHtml += '        <td>';
        itemHtml += '        <input type="text" name="discount[]" id="discount' + totalitems + '" class="form-control" value="">';
        itemHtml += '        </td>';
        itemHtml += '        <td><a class="cursor-pointer" onclick="deleteIncidenceItem(' + totalitems + ');"><?php icon('delete', true); ?></a></td>';
        itemHtml += '    </tr>';

        $('#incidence-order-items').append(itemHtml);
        totalitems++;
    }

    function deleteIncidenceItem(id) {
        $('#incidenceitem' + id).remove();
    }

    function editIncidence(inciId, orderid) {
        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: {
                id: inciId,
                orderid: orderid,
                op: 'getIncidenceData'
            }
        }).done(function(Response) {
            $('#tab-newincidence').html(Response);
            $('#opt_incidence').prop('save_edit_incidence');
        });
    }

    function checkChangeIncidenceStatus() {
        if ($('#incidencestatus').val() > 0) {
            $('#div_fixedon').show();
        } else {
            $('#div_fixedon').hide();
        }
    }

    function saveIncidence() {
        comprobate = Array('#code', '#incidencedate', '#store', '#deliveryzone', '#description', '#incipendingpay');
        if ($('#incidencestatus').val() > 0) {
            comprobate.push("#fixed_on");
        }
        if (checkNoEmpty(comprobate)) {
            $('#sp_incidence').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#frm-incidence').serialize(),
            }).done(function(Response){
                $('#sp_incidence').hide();
                res = JSON.parse(Response);

                // Si cambiamos el estado a solucionado quitamos los botones
                if ($('#incidencestatus').val() == 1) {
                    $('#btn_saveincidence').remove();
                    $('#btnSaveIncidenceComm').remove();
                    $('#btnSaveInternProducts').remove();
                    $('#btnSaveDeliveryNote').remove();
                }

                if (res['saved'] == 1) {
                    var lastId = res['lastid'];
                    $('#btn_saveincidence').prop('value', 'Modificar incidencia');
                    // Para guardar comentarios
                    $('#incidenceid').prop('value', lastId);

                    // Para los productos internos
                    $('#internInciId').prop('value', lastId);
                    $('#div-inci-interns').show();

                    // Para crear la nota de entrega
                    $('#inci-items-incitype').prop('value', $('#incidencetype').val()); // del tipo seteamos el nombre del pdf
                    $('#inci-orderid').prop('value', $('#orderid').val());
                    $('#inci-incidenceid').prop('value', res['lastid']);
                    $('#div-incidence-items').show();

                    // Actualiza el listado de incidencias
                    $('#dynamic-incidences').html(res['html']);
                    // Para los comentarios
                    $('#inci-comments').show();
                    alert('Incidencia guardada!');
                } else if (res['updated'] == 1) {
                    $('#dynamic-incidences').html(res['html']);
                    $('#inci-items-incitype').prop('value', $('#incidencetype').val()); // del tipo seteamos el nombre del pdf
                    alert('Incidencia actualizada!');
                } else if (res['duplicated'] == 1) {
                    alert('Nada para actualizar!');
                }
            });
        } else {
            alert(completeRequiredFields);
        }
    }

    function saveIncidenceComment() {
        var incidencecomment = $('#incidencecomment').val();
        var incidenceid = $('#incidenceid').val();
        if (incidencecomment != "") {
            $('#sp_inci_comm').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'saveIncidenceComment',
                    incidenceid: incidenceid,
                    comment: incidencecomment,
                }
            }).done(function(Response) {
                $('#sp_inci_comm').hide();
                $('#incidencecomment').prop('value', '');
                $('#dynamic-inci-comments').html(Response);
            });
            border_ok('#incidencecomment');
        } else {
            border_error('#incidencecomment');
        }
    }

    function saveInternProducts() {
        var interProductsControl = $('#frm-intern-products select');
        var cont = 0;
        var numproducts = interProductsControl.length;
        // Verificamos si todos los selects fueron elegidos y los inputs completados
        interProductsControl.each(function() {
            var seledId = '#' + $(this).prop('id');
            if ($(this).val() != "") {
                border_ok(seledId);
                cont++;
            } else {
                border_error(seledId);
            }
        });

        if (numproducts != cont) {
            alert('Por favor seleccione todos los productos!');
            return false;
        }
        // Spinner
        $('#sp_intern_products').show();
        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: $('#frm-intern-products').serialize(),
        }).done(function(Response) {
            var res = JSON.parse(Response);
            $('#intern-product-header').show();
            if(res['saved'] == 1) {
                alert('Productos guardados!');
            }
            $('#sp_intern_products').hide();
        });
    }

    function saveIncidenceItems() {
        //var items = $('#incidence-order-items :input, #incidence-order-items textarea');
        var items = $('#frm-incidence-items :input, #frm-incidence-items textarea');
        var cont = 0;
        var numproducts = items.length;
        // Verificamos si todos los selects fueron elegidos
        items.each(function() {
            var seledId = '#' + $(this).prop('id');
            if ($(this).val() != "") {
                border_ok(seledId);
                cont++;
            } else {
                // No son campos obligatorios
                if ($(this).prop('id') == "inci-tel" || $(this).prop('id') == "inci-tel2" || $(this).prop('id') == "inci-email") {
                    border_ok(seledId);
                    cont++;
                } else {
                    border_error(seledId);
                }

            }
        });

        if (numproducts != cont) {
            alert('Por favor complete todos los campos!');
            return false;
        }

        $('#sp_inci_items').show();

        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: $('#frm-incidence-items').serialize(),
        }).done(function(Response){
            $('#sp_inci_items').hide();
            var res = JSON.parse(Response);
            if(res['saved'] == 1) {
                $('#deliverynote-lk').html(res['link']);
                alert('Nota de entrega guardada correctamente!');
            }
        });
    }
</script>
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-lg-12 intern-product-header">
            <h4>Datos <?php icon('info', true); ?></h4>
        </div>
        <div class="col-lg-6">
            <form id="frm-incidence">
                <div class="form-group row">
                    <label for="code" class="col-sm-3 col-form-label">Nº de pedido</label>
                    <div class="col-sm-9">
                        <input type="text" name="code" id="code" class="form-control" value="<?php echo $editing ? $ajaxincidence->code : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="incidencedate" class="col-sm-3 col-form-label">Fecha</label>
                    <div class="col-sm-9">
                        <input type="text" name="incidencedate" id="incidencedate" class="form-control" value="<?php echo $editing ? americaDate($ajaxincidence->incidencedate, false) : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="incidencetype" class="col-sm-3 col-form-label">Tipo</label>
                    <div class="col-sm-9">
                        <select id="incidencetype" name="incidencetype" class="form-control" <?php echo $incidenceDisabled; ?>>
                            <?php
                            global $incidenceTypes;
                            foreach ($incidenceTypes as $key => $value) {
                                $selected = "";
                                if ($editing && $ajaxincidence->incidencetype == $key) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="store" class="col-sm-3 col-form-label">Tienda</label>
                    <div class="col-sm-9">
                        <select name="store" id="store" class="form-control">
                            <option value="">Selecciona</option>
                            <?php
                                $stores = getStores(true);
                                foreach ($stores as $store) {
                                    $selected = '';
                                    if ($editing && $store['id'] == $ajaxincidence->store) {
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $store['id'] . '" ' . $selected . '>' . $store['name'] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="store" class="col-sm-3 col-form-label">Zona de entrega</label>
                    <div class="col-sm-9">
                        <select name="deliveryzone" id="deliveryzone" class="form-control">
                            <option value="">Selecciona</option>
                            <?php
                            $zones = getZones(true);
                            foreach ($zones as $key => $zone) {
                                $selected = '';
                                if ($editing && $key == $ajaxincidence->deliveryzone) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $zone . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="description" class="col-sm-3 col-form-label">Motivo</label>
                    <div class="col-sm-9">
                        <textarea name="description" id="description" class="form-control" <?php echo $incidenceDisabled; ?>><?php echo $editing ? $ajaxincidence->description : ''; ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pendingpay" class="col-sm-3 col-form-label">Pendiente de pago</label>
                    <div class="col-sm-9">
                        <input type="text" name="pendingpay" id="incipendingpay" class="form-control" onkeyup="addCommas($(this).prop('id'), $(this).val());"
                               value="<?php echo $editing ? numberFormat($ajaxincidence->pendingpay, true, 2) : 0; ?>" <?php echo $incidenceDisabled; ?>>
                        <span class="red-color">(decimales separados por coma)</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="status" class="col-sm-3 col-form-label">Estado</label>
                    <div class="col-sm-9">
                        <select id="incidencestatus" name="status" class="form-control" onchange="checkChangeIncidenceStatus();" <?php echo $incidenceDisabled; ?>>
                            <?php
                            global $incidencestatus;
                            foreach ($incidencestatus as $key => $value) {
                                $selected = "";
                                if ($editing && $ajaxincidence->status == $key) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                    $fixedOnDisplay = 'display: none;';
                    if ($editing && $ajaxincidence->status > 0) {
                        $fixedOnDisplay = '';
                    }
                ?>
                <div class="form-group row" id="div_fixedon" style="<?php echo $fixedOnDisplay; ?>">
                    <label for="pendingpay" class="col-sm-3 col-form-label">Fecha de solución</label>
                    <div class="col-sm-9">
                        <input type="text" name="fixed_on" id="fixed_on" class="form-control"
                               value="<?php echo $editing ? americaDate($ajaxincidence->fixed_on, false) : ""; ?>" <?php echo $incidenceDisabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <?php
                            if (!$editing) {
                                //$orderid = $_GET['orderid'];
                                // Necesitamos este campo para guardar los comentarios y los items para la nota de entrega
                                echo '<input type="hidden" id="incidenceid" name="id" value="">';
                                echo '<input type="hidden" name="created_on" value="' . date('Y-m-d H:i:s') . '">';
                            } else {
                                $orderid = $ajaxincidence->orderid;
                                echo '<input type="hidden" id="incidenceid" name="id" value="' . $ajaxincidence->id . '">';
                            }
                        ?>
                        <input type="hidden" name="orderid" value="0">
                        <?php if ($canEditIncidence) { ?>
                            <input type="hidden" name="opt" value="<?php echo $editing ? 'save_incidence' : 'save_incidence'; ?>" id="opt_incidence">
                            <input type="button" class="btn btn-primary" value="<?php echo $editing ? 'Modificar datos' : 'Guardar datos'?>" onclick="saveIncidence();" id="btn_saveincidence">
                            <?php
                                if (!isset($_GET['id'])) {
                                    exit_btn(getUrl('show_incidences', $orderController->getUrls()));
                                }
                            ?>
                            <?php spinner_icon('spinner', 'sp_incidence', true); ?>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-6">
            <?php //if ($editing) { ?>
                <div id="inci-comments" style="display: <?php echo !$editing ? 'none' : ''; ?>;">
                    <div style="max-height: 260px; overflow: auto;">
                        <table id="dynamic-inci-comments" class="table table-striped">
                            <?php include(VIEWS_PATH . 'order/incidence_comments' . VIEW_EXT); ?>
                        </table>
                    </div>
                    <form id="frm-inci-comm">
                        <div class="form-group row">
                            <label for="incidencecomment" class="col-sm-3 col-form-label">Notas <?php icon('comments', true); ?></label>
                            <div class="col-sm-9">
                                <textarea name="incidencecomment" id="incidencecomment" class="form-control" <?php echo $incidenceDisabled; ?>></textarea>
                            </div>
                        </div>
                        <?php if ($canEditIncidence) { ?>
                            <div class="col-lg-5">
                                <input type="button" class="btn btn-primary" id="btnSaveIncidenceComm" value="Guardar nota" onclick="saveIncidenceComment()">
                                <?php spinner_icon('spinner', 'sp_inci_comm', true); ?>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            <?php //} ?>
        </div>
    </div>


    <div id="div-inci-interns" class="row" style="display: <?php echo !$editing ? 'none' : ''; ?>"> <!-- Productos internos -->
        <div class="col-lg-12 intern-product-header">
            <h4>Productos <?php icon('cart', true); ?></h4>
        </div>

        <form id="frm-intern-products">
            <div class="col-lg-12">
                <table class="table" id="dynamic-manufacturer-table">
                    <?php
                    // Si estamos modificando una incidencia
                    if ($editing && $ajaxincidence->internProducts) {
                        $i  = 1;

                        foreach ($ajaxincidence->internProducts as $item) {
                            echo '<tr id="product' . $i . '" class="table-success">';
                            echo    '<td class="w-50">';
                            echo        '<select class="form-control products" name="products[]" id="inselect' . $i . '"' . $incidenceDisabled . '>' . PHP_EOL;
                            echo            '<option value="">Seleccione un producto</option>' . PHP_EOL;
                            foreach ($ajaxProducts as $product) {
                                $selected = "";
                                if ($item->productid == $product->id) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $product->id . '" ' . $selected . '>' . $product->productname . '</option>' . PHP_EOL;
                            }
                            echo        '</select>' . PHP_EOL;
                            echo    '</td>' . PHP_EOL;
                            echo    '<td class="w-50">';
                            echo        '<select class="form-control categories" name="categories[]" id="incategory' . $i . '" ' . $incidenceDisabled . '>' . PHP_EOL;
                            echo            '<option value="">Seleccione una categoría</option>' . PHP_EOL;
                            foreach ($ajaxCategories as $category) {
                                $selected = "";
                                if ($item->categoryid == $category->id) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $category->id . '" ' . $selected . '>' . $category->category . '</option>' . PHP_EOL;
                            }
                            echo        '</select>' . PHP_EOL;
                            echo    '</td>' . PHP_EOL;
                            if ($i == 1) {
                                if ($ajaxincidence->status == 0) {
                                    echo '<td>';
                                    echo '<a style="cursor: pointer;" onclick="moreInternProducts(\'dynamic-manufacturer-table\')">' . icon('plus', false) . '</a>' . PHP_EOL;
                                    echo '</td>' . PHP_EOL;
                                }
                            } else {
                                if ($ajaxincidence->status == 0) {
                                    echo '<td>' . PHP_EOL;
                                    echo '<a onclick="deleteProduct(' . $i . ')" style="cursor: pointer;"><i class="fa fa-trash fa-fw"></i></a>' . PHP_EOL;
                                    echo '</td>' . PHP_EOL;
                                }
                            }
                            echo '</tr>' . PHP_EOL;
                            echo '<tr id="productdate' . $i . '">' . PHP_EOL;
                            echo    '<td colspan="2">' . PHP_EOL;
                            echo        '<table class="table-striped">' . PHP_EOL;
                            echo            '<tr>' . PHP_EOL;
                            echo                '<td>Fabricación <input type="text" class="form-control" id="inmanufacturing' . $i . '" name="manufacturings[]"  value="' . americaDate($item->manufacturing_in, false) . '" ' . $incidenceDisabled . '>' . PHP_EOL;
                            echo                '</td>' . PHP_EOL;
                            echo                '<td>Acabado <input type="text" class="form-control" id="infinish' . $i . '" name="finishes[]" value="' . americaDate($item->finish_in, false) . '" ' . $incidenceDisabled . '></td>' . PHP_EOL;
                            echo                '<td>Almacén <input type="text" class="form-control" id="instore' . $i . '" name="stores[]"  value="' . americaDate($item->store_in, false) . '" ' . $incidenceDisabled . '></td>' . PHP_EOL;
                            echo            '</tr>' . PHP_EOL;
                            echo         '</table>' . PHP_EOL;
                            echo    '</td>' . PHP_EOL;
                            echo '</tr>' . PHP_EOL;
                            datePicker(
                                array('inmanufacturing' . $i, 'infinish' . $i, 'instore' . $i), false);
                            $i++;
                        }
                    } else {
                        // Nuevo pedido
                        ?>
                        <tr class="table-success">
                            <td class="w-50">
                                <select class="form-control products" name="products[]" id="inselect1" <?php echo $incidenceDisabled; ?>>
                                    <option value="">Seleccione un producto</option>
                                    <?php
                                        if (isset($ajaxProducts)) {
                                            foreach ($ajaxProducts as $product) {
                                                echo '<option value="' . $product->id . '">' . $product->productname . '</option>';
                                            }
                                        }

                                    ?>
                                </select>
                            </td>
                            <td class="w-50">
                                <select class="form-control products" name="categories[]" id="incategorie1" <?php echo $incidenceDisabled; ?>>
                                    <option value="">Seleccione una categoria</option>
                                    <?php
                                        if (isset($ajaxCategories)) {
                                            foreach ($ajaxCategories as $category) {
                                                echo '<option value="' . $category->id . '">' . $category->category . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
                            <?php if ($canEditIncidence) { ?>
                                <td>
                                    <a style="cursor: pointer;" onclick="moreInternProducts('dynamic-manufacturer-table')"><?php icon('plus', true); ?></a>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr id="productdate1">
                            <td colspan="2">
                                <table class="table-striped" width="100%">
                                    <tr>
                                        <td>
                                            Fabricación <input type="text" class="form-control" id="inmanufacturing1" name="manufacturings[]" <?php echo $incidenceDisabled; ?>>
                                        </td>
                                        <td>
                                            Acabado <input type="text" class="form-control" id="infinish1" name="finishes[]" <?php echo $incidenceDisabled; ?>>
                                        </td>
                                        <td>
                                            Almacén <input type="text" class="form-control" id="instore1" name="stores[]" <?php echo $incidenceDisabled; ?>>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php
                            datePicker(array('inmanufacturing1', 'infinish1', 'instore1'), false);
                        }
                    ?>

                </table>
            </div>

            <div class="col-lg-12">
                <?php if ($canEditIncidence) { ?>
                    <div class="row">
                        <div class="col-lg-6" style="text-align: right;">
                            <a onclick="moreInternProducts('dynamic-manufacturer-table')" style="cursor: pointer;"><?php icon('plus', true); ?></a>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-lg-12">
                        <input type="hidden" name="incidenceid" id="internInciId" value="<?php echo $editing ? $ajaxincidence->id : ''; ?>">
                        <?php if ($canEditIncidence) { ?>
                            <input type="hidden" name="opt" value="saveInternProducts">
                            <input type="button" class="btn btn-primary" id="btnSaveInternProducts" value="<?php echo $editing ? 'Modificar productos' : 'Guardar productos' ?>" onclick="saveInternProducts();">
                            <?php spinner_icon('spinner', 'sp_intern_products', true); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <hr>

    <!-- Productos que componen la nueva nota de entrega de la incidencia -->
    <div id="div-incidence-items" class="row" style="display: <?php echo $editing ? 'block' : 'none'; ?>;">
        <div class="col-lg-12 intern-product-header">
            <h4>Nota de entrega <?php icon('edit', true); ?></h4>
        </div>
        <form id="frm-incidence-items">
            <table class="table" id="incidence-customer-data">
                <tr>
                    <td>Fecha de entrega</td>
                    <td colspan="5" class="w-50">
                        <input type="text" id="inci-deliverydate" name="deliverydate" class="form-control" value="<?php echo $editing ? americaDate($ajaxincidence->deliverydate, false) : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                </tr>
                <tr>
                    <td>Le atendió</td>
                    <td colspan="5" class="w-50">
                        <input type="text" id="seller" name="seller" class="form-control" value="<?php echo $editing ? $ajaxincidence->seller : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                </tr>
                <tr>
                    <td>Montador</td>
                    <td colspan="5" class="w-50">
                        <input type="text" id="assembler" name="assembler" class="form-control" value="<?php echo $editing ? $ajaxincidence->assembler : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                </tr>
                <tr>
                    <td>Cliente</td>
                    <td colspan="5" class="w-50">
                        <input type="text" id="inci-customer" name="customer" class="form-control" value="<?php echo $editing ? $ajaxincidence->customer : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                </tr>
                <tr>
                    <td>DNI</td>
                    <td colspan="5" class="w-50">
                        <input type="text" id="inci-dni" name="dni" class="form-control" value="<?php echo $editing ? $ajaxincidence->dni : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                </tr>
                <tr>
                    <td>Dirección</td>
                    <td colspan="5" class="w-75">
                        <input type="text" id="inci-address" name="address" class="form-control" value="<?php echo $editing ? $ajaxincidence->address : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                </tr>
                <tr>
                    <td>CP</td>
                    <td class="w-25">
                        <input type="text" id="inci-cp" name="cp" class="form-control" value="<?php echo $editing ? $ajaxincidence->cp : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                    <td>Localidad</td>
                    <td class="w-25">
                        <input type="text" id="inci-city" name="city" class="form-control" value="<?php echo $editing ? $ajaxincidence->city : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                    <td>Provincia</td>
                    <td class="w-50">
                        <select name="provinceid" id="inci-provinceid" class="form-control" <?php echo $incidenceDisabled; ?>>
                            <option value="">Provincia</option>
                            <?php
                                $provinces = getProvinces();
                                foreach ($provinces as $province) {
                                    $selected = "";
                                    if ($editing && $ajaxincidence->provinceid == $province->id) {
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $province->id . '" ' . $selected . '>' . $province->province . '</option>';
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Teléfono</td>
                    <td class="w-25">
                        <input type="text" id="inci-tel" name="telephone" class="form-control" value="<?php echo $editing ? $ajaxincidence->telephone : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                    <td>Teléfono 2</td>
                    <td class="w-25">
                        <input type="text" id="inci-tel2" name="telephone2" class="form-control" value="<?php echo $editing ? $ajaxincidence->telephone2 : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                    <td>E-mail</td>
                    <td class="w-50">
                        <input type="text" id="inci-email" name="email" class="form-control" value="<?php echo $editing ? $ajaxincidence->email : ''; ?>" <?php echo $incidenceDisabled; ?>>
                    </td>
                </tr>
            </table>
            <table class="table" id="incidence-order-items">
                <?php
                    if ($editing && count($ajaxincidence->items) > 0) {
                    ?>
                    <?php
                        $cont = 1;
                        foreach ($ajaxincidence->items as $inciItem) {
                    ?>
                            <tr class="table-success" id="incidenceitem<?php echo $cont; ?>">
                                <td>Ref.</td>
                                <td>
                                    <input type="text" name="reference[]" id="reference<?php echo $cont; ?>" class="form-control" value="<?php echo $inciItem->reference; ?>" <?php echo $incidenceDisabled; ?>>
                                </td>
                                <td>Descripción</td>
                                <td>
                                    <textarea name="description[]" id="description<?php echo $cont; ?>" class="form-control" style="width: 250px; height: 90px; resize: horizontal;" <?php echo $incidenceDisabled; ?>><?php echo $inciItem->description; ?></textarea>
                                </td>
                                <td>Cantidad</td>
                                <td class="col-xs-2">
                                    <input type="text" name="quantity[]" id="quantity<?php echo $cont; ?>" class="form-control" value="<?php echo $inciItem->quantity; ?>" <?php echo $incidenceDisabled; ?>>
                                </td>
                                <td>Precio</td>
                                <td>
                                    <input type="text" name="price[]" id="price<?php echo $cont; ?>" class="form-control" value="<?php echo numberFormat($inciItem->price, true, 2) ?>" onkeyup="addCommas($(this).prop('id'), $(this).val());" <?php echo $incidenceDisabled; ?>>
                                </td>
                                <td>Dcto%</td>
                                <td>
                                    <input type="text" name="discount[]" id="discount<?php echo $cont; ?>" class="form-control" value="<?php echo $inciItem->discount; ?>" <?php echo $incidenceDisabled; ?>>
                                </td>
                                <td>
                                    <?php
                                        if ($canEditIncidence) {
                                            if ($cont == 1) { ?>
                                                <a class="cursor-pointer"
                                                   onclick="addIncidenceItem();"><?php icon('plus', true); ?></a>
                                            <?php } else { ?>
                                                <a class="cursor-pointer"
                                                   onclick="deleteIncidenceItem(<?php echo $cont; ?>);"><?php icon('delete', true); ?></a>
                                            <?php }
                                        }
                                    ?>
                                </td>
                            </tr>
                    <?php
                            $cont++;
                        }
                  } else { ?>
                        <tr class="table-success" id="incidenceitem1">
                            <td>Ref.</td>
                            <td>
                                <input type="text" name="reference[]" id="reference1" class="form-control" value="">
                            </td>
                            <td>Descripción</td>
                            <td>
                                <textarea name="description[]" id="description1" class="form-control" style="width: 250px; height: 90px; resize: horizontal;"></textarea>
                            </td>
                            <td>Cantidad</td>
                            <td>
                                <input type="text" name="quantity[]" id="quantity1" class="form-control" value="">
                            </td>
                            <td>Precio</td>
                            <td>
                                <input type="text" name="price[]" id="price1" class="form-control" value="" onkeyup="addCommas($(this).prop('id'), $(this).val());">
                            </td>
                            <td>Dcto%</td>
                            <td>
                                <input type="text" name="discount[]" id="discount1" class="form-control" value="">
                            </td>
                            <td><a class="cursor-pointer" onclick="addIncidenceItem();"><?php icon('plus', true); ?></a></td>
                        </tr>
                <?php  } ?>
            </table>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group row">
                        <label for="notes" class="col-sm-2 col-form-label">Observaciones</label>
                        <div class="col-sm-10">
                            <textarea name="observations" id="observations" class="form-control" style="resize: both;" <?php echo $incidenceDisabled; ?>><?php echo $editing ? $ajaxincidence->observations : ""; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pull-right" style="margin-right: 10px;">
                <?php if ($canEditIncidence) { ?>
                    <input type="hidden" id="inci-items-incitype" name="incidencetype" value="<?php echo $editing ? $ajaxincidence->incidencetype : ''; ?>">
                    <input type="hidden" id="inci-orderid" name="orderid" value="<?php echo $editing ? $ajaxincidence->orderid : '0'; ?>">
                    <input type="hidden" id="inci-incidenceid" name="incidenceid" value="<?php echo $editing ? $ajaxincidence->id : ''; ?>">
                    <input type="hidden" name="opt" value="saveIncidenceItems">
                    <input type="button" class="btn btn-primary" id="btnSaveDeliveryNote" value="Guardar nota de entrega" onclick="saveIncidenceItems()">
                    <?php spinner_icon('spinner', 'sp_inci_items', true); ?>
                <?php } ?>
            </div>
            <div class="row" id="deliverynote-lk">
                <?php
                    if ($editing) {
                        $id = $ajaxincidence->id;
                        $pdfName = $ajaxincidence->incidencetype == 0 ? 'Incidencia_' . $id : 'Entrega_parcial_' . $id;
                        $url = '/mpdf60.php?controller=order&opt=generateDeliveryNote&order=' . $ajaxincidence->orderid;
                        $url .= '&incidenceid=' . $ajaxincidence->id . '&pdfName=' . $pdfName;

                        $htmlLk = '<a href="' . $url . '" class="cursor-pointer" disabled="disabled" target="_blank" style="font-size: 40px;">' . icon('pdf', false) . '</a>';
                        echo $htmlLk;
                    }
                ?>
            </div>
            <div class="card-footer text-muted mt-5">
                <?php
                    exit_btn(getUrl('show_incidences', $orderController->getUrls()));
                ?>
            </div>
        </form>
    </div>
</div>
<?php
// Creamos los calendarios
datePicker(array('purchasedate', 'deliverydate', 'paydate'));
?>
<script>
    var id = '<?php  echo $data['data'] ? $data['data']->getId() : 0; ?>';
    var pdfid = '<?php  echo $data['data'] ? $data['data']->getPdfid() : $_GET['pdfid']; ?>';
    // Necesitamos esta variable para crear una nueva incidencia, segundo prámetro de editIncidence
    var idnew = id;
    var codeValidated = '<?php  echo $data['data'] ? 'true' : 'false'; ?>';
    var orderStatus =  '<?php  echo $data['data'] ? $data['data']->getStatus() : ''; ?>';

    $(document).ready(function () {
        <?php
        if ($data['data']) {
            // Activamos el link de incidencias
            //echo "\$('#lk-incidence').show();";
        }

        if (isset($_GET['show']) && $_GET['show'] == "incidence") {
            echo "$('#incide-lk').trigger('click');";
        }
        ?>

        $('#cu-com-lk').click(function() {
            if (id == 0) {
                alert('Aún no has creado el pedido!');
                return false;
            }
        });

        $('#our-com-lk').click(function() {
            if (id == 0) {
                alert('Aún no has creado el pedido!');
                return false;
            }
        });

        $('#incide-lk').click(function() {
            if (id == 0) {
                alert('Aún no has creado el pedido!');
                return false;
            }
        });

    });

    function checkCode() {
        var code = $('#code').val();
        if (code.length > 1) {
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: {
                    code: code,
                    op: 'checkPdfCode',
                }
            }).done(function (data) {
                // Retorna "si" 0 "no" dependeiendo si el usuario existe
                if (data == "si") {
                    addError('#div_code', '#code');
                    $('#code_response').html("El código ya existe");
                    codeValidated = false;
                    $('#btnsave').prop('disabled', true);
                } else {
                    addSuccess('#div_code', '#code');
                    $('#code_response').html("Código disponible");
                    codeValidated = true;
                    $('#btnsave').prop('disabled', false);
                }
            });
        }
    }

    function addError(div, field) {
        $(div).removeClass("has-success");
        $(field).removeClass("form-control-success");
        $(div).addClass("has-danger");
        $(field).addClass("form-control-danger");
    }

    function addSuccess(div, field) {
        $(div).removeClass("has-danger");
        $(field).removeClass("form-control-danger");
        $(div).addClass("has-success");
        $(field).addClass("form-control-success");
    }

    function check_new_order() {
        comprobate = Array('#code', '#customer', '#telephone', '#purchasedate', '#deliveryrange', '#deliveryzone',
            '#deliverymonth', '#total', '#pendingpay', '#paymethod', "#status", "#totalitems");

        if ($('#pendingstatus').val() == 1) {
            comprobate.push('#paydate');
        }

        // Devuelve true si todos los campos han sido completados
        //if (checkNoEmpty(comprobate) && usernameValidated && emailValidated) {
        if (checkNoEmpty(comprobate) && codeValidated) {
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function sendAjaxForm() {
        if (check_new_order()) {

            $('#sp-order-info').show();
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: $('#principal').serialize(),
            }).done(function (Response) {
                res = JSON.parse(Response);
                $('#sp-order-info').hide();
                // Hemos guardado el pedido correctamente y si es la primera vez que pulsamos en enviar.
                if (res['saved'] == 1) {
                    alert('Datos del pedido guardados.\nAhora puede agregar los productos.');
                    id = res['lastid'];
                    // Seteamos el id para el guardado de productos
                    $('#orderid').prop('value', id);
                    $('#firstinciorderid').prop('value', id);
                    $('#products').show();
                    $('#divfinish_deliverynote').show();
                    // Cambiamos el opt de formulario para que actualice si es necesario
                    $('#opt').prop('value', 'save_edit_order');
                    $('#btnsaveorder').prop('value', 'Modificar datos');
                    // Creamos un campo id
                    $('#dynamicid').html('<input type="hidden" value="' + id + '" name="id" id="id">');
                    scrollingTo('#products');
                } else if (res['updated'] == 1) {
                    alert('Datos del pedido actualizados.');
                } else if (res['duplicated'] == 1) {
                    alert('Nada para actualizar');
                }
            });
        }
    }

    <?php
    $countProducts = 2;

    if ($data['data'] && count($data['data']->getItems())) {
        $countProducts = count($data['data']->getItems());
    }
    ?>

    var products = <?php echo $countProducts + 1; ?>;
    function moreProducts(target) {
        var html = '<tr id="product' + products + '" class="table-success">';
        html +=     '<td>';
        html += '<select class="form-control products" id="select' + products + '" name="products[]">';
        html +=         '<option value="">Seleccione un producto</option>';
        <?php
        foreach ($data['products'] as $product) {
            echo '   html += \'<option value="' . $product->id . '">' . $product->productname . '</option>\';' . PHP_EOL;
        }
        ?>
        html +=         '</select>';
        html +=     '</td>';
        html +=     '<td>';
        html += '<select class="form-control categories" id="category' + products + '" name="categories[]">';
        html +=         '<option value="">Seleccione un producto</option>';
        <?php
        foreach ($data['categories'] as $category) {
            echo '   html += \'<option value="' . $category->id . '">' . $category->category . '</option>\';' . PHP_EOL;
        }
        ?>
        html +=         '</select>';
        html +=     '</td>';
        html +=     '<td>';
        html +=     '   <a style="cursor: pointer;" onclick="deleteProduct(' + products + ')"><?php icon('delete', true); ?></a>';
        html +=     '</td>';
        html += '</tr>';
        html += '<tr id="productdate' + products + '">';
        html +=   '<td colspan="4">';
        html +=       '<table class="table-striped">';
        html +=            '<tr>';
        html +=                '<td>Fabricación <input type="text" class="form-control" id="manufacturing' + products + '" name="manufacturings[]">';
        html +=                '</td>';
        html +=                '<td>Acabado <input type="text" class="form-control" id="finish' + products + '" name="finishes[]"></td>';
        html +=                '<td>Almacén <input type="text" class="form-control" id="store' + products + '" name="stores[]"></td>';
        html +=            '</tr>';
        html +=         '</table>';
        html +=    '</td>';
        html += '</tr>';
        $('#' + target).append(html);

        jQuery(function() {
            jQuery('#manufacturing' + products).datepicker();
        });

        jQuery(function() {
            jQuery('#finish' + products).datepicker();
        });

        jQuery(function() {
            jQuery('#store' + products).datepicker();
        });

        scrollingTo('#product' + products);
        products++;
    }

    function deleteProduct(id) {
        $('#product' + id).remove();
        $('#productdate' + id).remove();
    }

    function saveProducts() {
        var orderid = $('#orderid').val();
        if (orderid != 0 && orderid != "") {
            //var selectproducts = $('#dynamic-table select'); // Todos los selects
            var selectproducts = $('#dynamic-table select.form-control.products'); // Sólo los de productos
            var cont = 0;
            var numproducts = selectproducts.length;
            // Verificamos si todos los selects fueron elegidos
            selectproducts.each(function() {
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
            $('#sp-products').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#form-products').serialize(),
            }).done(function(Response) {
                res = JSON.parse(Response);
                $('#sp-products').hide();
                if (res['saved'] == 1) {
                    alert('Productos guardados');
                }
            });
        } else {
            alert("Error con el identificador DB del pedido!");
        }

    }

    var validateCommentToChangeStatus = false;

    function saveComment(comtype) {
        var comment = "";
        var commentvalidated = false;
        var statuscomment = $('#statuscomment').val();
        var deliverydate = $('#deliverydate').val();
        var controlDeliveryDate = $('#controlDeliveryDate').val();

        // Si el estado es entregado es necesario poner la fecha de entrega
        if (controlDeliveryDate == 1) {
            if (deliverydate == "") {
                border_error('#deliverydate');
                return false;
            }  else {
                border_ok('#deliverydate');
            }
        }

        if (comtype == 0) {
            comment = $('#intercomment').val();
            if (comment == "") {
                border_error('#intercomment');
            } else {
                border_ok('#intercomment');
                if ($("#status").val() > 1) {

                }
                commentvalidated = true;
            }
        } else {
            comment = $('#customercomment').val();
            if (comment == "") {
                border_error('#customercomment');
            } else {
                border_ok('#customercomment');
                commentvalidated = true;
            }
        }

        if (commentvalidated) {
            if (comtype == 0) {
                $('#sp-in-comment').show();
                $('#dynamic-in-comments').html('');
            } else {
                $('#sp-cus-comment').show();
                $('#dynamic-cus-comments').html('');
            }

            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'saveComment',
                    comment: comment,
                    comment_type: comtype,
                    statuscomment: statuscomment,
                    orderid: id,
                    deliverydate: deliverydate,
                }

            }).done(function(Response) {
                // Seteamos el statuscomment a 0.
                // Con este parámetro podemos saber si el comentario es de tipo: listo para entrega o entregado.

                if (orderStatus != $('#status').val()) {
                    validateCommentToChangeStatus = true;
                    $('#btnsaveorder').trigger('click');
                }

                $('#statuscomment').prop('value', 0);
                // Si el estado es entregado quitamos los botones
                if (controlDeliveryDate == 1) {
                    $('#btnsaveorder').remove();
                    $('#btn-save-products').remove();
                }
                if (comtype == 0) {
                    $('#sp-in-comment').hide();
                    $('#intercomment').prop('value', '');
                    $('#dynamic-in-comments').html(Response);
                } else {
                    $('#sp-cus-comment').hide();
                    $('#customercomment').prop('value', '');
                    $('#dynamic-cus-comments').html(Response);
                }
            });
        }
    }

    /* Controlamos si seleccionamos un estado de pedido diferente se nos abre el modal para el comentario
     pero no guardamos ningún comentario, es decir le damos al borón de salir o cerrar.
     */
    function checkChangeStatusNoComment() {
        if (orderStatus != $('#status').val() && !validateCommentToChangeStatus) {
            $('#status > option[value="' + orderStatus + '"]').prop('selected', true);
        }
    }

    function checkChangeStatus() {
        var newstatus = $('#status').val();
        // Por si acaso seteamos a 0 el statuscomment. El usuario puede no darle a aceptar.
        $('#statuscomment').prop('value', 0);
        $('#div-deliverydate').hide();


        if (newstatus == 2) {
            if ($('#pendingstatus').val() == 0 && parseInt($('#pendingpay').val()) > 0) {
                $('#status > option[value="0"]').prop('selected', true);
                alert("No es posible cambiar el estado del pedido porque el pago aún está pendiente.\nCambia primero el estado a pagado e indica la fecha del pago");
                return false;
            }
        }

        // Listo para entregar o entregado con incidencia
        if (newstatus == 2) {
            if ($('#paydate').val() == "") {
                alert("Por favor indica la fecha de pago");
                $('#status > option[value="' + orderStatus + '"]').prop('selected', true);
                return false;
            }
        }

        if (newstatus == 1) {
            if (confirm('¿Desea cambiar el estado a listo para entregar?')) {
                // Desde este campo controlamos si el estado es entregado y habilitamos un campo fecha
                $('#controlDeliveryDate').prop('value', 0);
                $('#statuscomment').prop('value', newstatus);
                $('#ourComments').modal('show');
            } else {
                $('#status > option[value="' + orderStatus + '"]').prop('selected', true);
            }
        } else if (newstatus == 2) {
            if (confirm('¿Desea cambiar el estado a entregado?')) {
                $('#statuscomment').prop('value', newstatus);
                $('#div-deliverydate').show();
                // Desde este campo controlamos si el estado es entregado y habilitamos un campo fecha
                $('#controlDeliveryDate').prop('value', 1);
                $('#ourComments').modal('show');
            } else {
                $('#status > option[value="' + orderStatus + '"]').prop('selected', true);
            }
        } else if (newstatus == 3) {
            if (confirm('¿Desea cambiar el estado a entregado con incidencia?')) {
                $('#statuscomment').prop('value', newstatus);
                $('#div-deliverydate').show();
                // Desde este campo controlamos si el estado es entregado y habilitamos un campo fecha
                $('#controlDeliveryDate').prop('value', 1);
                $('#ourComments').modal('show');
            } else {
                $('#status > option[value="' + orderStatus + '"]').prop('selected', true);
            }
        }
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
            $('#firstinciorderid').prop('value', id);
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
        comprobate = Array('#incidencedate', '#description', '#incipendingpay');
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
                border_error(seledId);
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

    var arrayfiles = [];

    function addFile() {
        if ($('#file').val() != "") {
            jQuery.each(jQuery('#file')[0].files, function (i, file) {
                //data.append('file-'+i, file);
                arrayfiles.push(file);
            });
            //listFiles();
        } else {
            alert('Seleccione un archivo!');
        }
    }

    /**
     * Sube varios archivos al servidor
     */
    function uploadPdf() {
        /*var comment = $('#comment').val();
         if (reuploaded) {
         if (comment == "") {
         alert('Por favor indique por qué carga un nuevo PDF!');
         border_error('#comment');
         return false;
         }
         }*/
        if (arrayfiles.length > 0) {
            var data = new FormData();

            $('#sp-upload').show();
            $('#ajax-content').html('');

            jQuery.each(arrayfiles, function (i, file) {
                data.append('file-' + i, file);
            });

            data.append('op', 'uploadOrderPdf');
            data.append('orderid', id);
            data.append('comment', '');
            data.append('last', 'last');
            $.ajax({
                url: 'ajax.php',
                type: 'post',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
            }).done(function (response) {
                $('#sp-upload').hide();
                $('#btn-deliveryfile').prop('value', 'Modificar nota de entrega');
                $('#lastdeliverynote').html(response);
                //var dirPath = 'uploaded-files/order/35';
                // limpiamos el array de archivos
                arrayfiles = [];
                $('#file').prop('value', '');
                // recargamos los archivos mostrados
                //listDir(dirPath);
            });
        } else {
            alert('Seleccione un archivo!');
            border_error('#file');
        }
    }

</script>

<?php
$canEdit = false;
global $user;
if (!$data['data']) { // No hemos creado el pedido aún
    $canEdit = true;
} else if ($data['data'] && $data['data']->getStatus() != 2 || $user->getUsermanager() == 1 || isadmin()) {
    // Si el pedido no se ha entregado lo podemos modificar.
    $canEdit = true;
}
$disabled = 'disabled="disabled"';
if ($canEdit) {
    $disabled = '';
}
?>
<div class="row">
    <!-- Información del pedido -->
    <div class="card col-lg-6">
        <div class="card-header">
            <h4 class="card-title">
                <?php
                if ($canEdit) {
                    echo icon('info', true) . ($data['data'] ? "Modificar pedido " : "Nuevo pedido ");
                } else {
                    echo 'Pedido entregado ' . icon('truck', true);
                }

                $comments = $data['comments'];
                if (count($comments) > 0) {
                    $mycomments = '<table>';
                    foreach ($comments as $intern) {
                        $usercomment = $intern->username;
                        $commentDate = americaDate($intern->created_on, true);
                        $comment = $intern->comment;
                        $mycomments .= '<tr><td><b>' . $usercomment . '</b> ' . icon('calendar', false, true) . '(' . $commentDate . '): ' . $comment . '</tr></td>';
                    }
                    $mycomments .= '</table>';
                    ?>
                    <a class="withqtip cursor-pointer custom-lk" title="<?php echo $mycomments; ?>">
                        <?php icon('comments', true); ?>
                    </a>
                    <?php
                }

                if (isset($_GET['pdfid'])) {
                    $pdfid = $_GET['pdfid'];
                    $mycode = $data['pdfinfo']->code;
                    $myCustomer = $data['pdfinfo']->customer;
                    $purchaseDate = americaDate($data['pdfinfo']->saledate, false);
                    $myStore = $data['pdfinfo']->storeid;

                } else {
                    $mycode = $data['data']->getCode();
                    $myCustomer = $data['data']->getCustomer();
                    $purchaseDate = americaDate($data['data']->getPurchasedate(), false);
                    $myStore = $data['data']->getStore();
                    $pdfid = $data['data']->getPdfid();
                }
                ?>
            </h4>
        </div>

        <form action="" method="POST" onsubmit="return check_new_order();" id="principal">
            <div class="card-block">
                <div class="form-group row" id="div_code">
                    <label for="code"
                           class="col-sm-2 col-form-label">Nº de pedido</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="<?php echo $mycode; ?>" disabled="disabled">
                        <input type="hidden" class="form-control" name="code" id="code" placeholder="" onkeyup="checkCode();"
                               value="<?php echo $mycode; ?>">
                        <div id="code_response" class="form-control-feedback"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="purchasedate" class="col-sm-2 col-form-label">Fecha de compra</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?php echo $purchaseDate; ?>" disabled="disabled">
                        <input type="hidden" name="purchasedate" id="purchasedate" class="form-control" value="<?php echo $purchaseDate; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="customer" class="col-sm-2 col-form-label">Titular del pedido</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control"
                               value="<?php echo $myCustomer; ?>" disabled="disabled">
                        <input type="hidden" class="form-control" name="customer" id="customer" placeholder=""
                               value="<?php echo $myCustomer; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="telephone" class="col-sm-2 col-form-label">Teléfono</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="telephone" name="telephone" placeholder=""
                               value="<?php echo($data['data'] ? $data['data']->getTelephone() : ''); ?>" <?php echo $disabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="telephone2" class="col-sm-2 col-form-label">Teléfono 2</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="telephone2" name="telephone2" placeholder=""
                               value="<?php echo($data['data'] ? $data['data']->getTelephone2() : ''); ?>" <?php echo $disabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label"><?php echo trans('email') ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="email" name="email" placeholder=""
                               value="<?php echo($data['data'] ? $data['data']->getEmail() : ''); ?>" <?php echo $disabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="store" class="col-sm-2 col-form-label">Tienda</label>
                    <div class="col-sm-10">
                        <input type="text" value="<?php echo getStoreName($myStore); ?>" disabled="disabled" class="form-control" <?php echo $disabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliveryrange" class="col-sm-2 col-form-label">Fecha de entrega</label>
                    <div class="col-sm-10">
                        <select name="deliveryrange" id="deliveryrange" class="form-control" <?php echo $disabled; ?>>
                            <option value="">Seleccione un rango</option>
                            <?php
                            global $deliveryRanges;
                            foreach ($deliveryRanges as $key => $value) {
                                $selected = "";
                                if ($data['data'] && $data['data']->getDeliveryrange() == $key) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliverymonth" class="col-sm-2 col-form-label">Mes de entrega</label>
                    <div class="col-sm-10">
                        <select name="deliverymonth" id="deliverymonth" class="form-control" <?php echo $disabled; ?>>
                            <option value="">Seleccione un mes</option>
                            <?php
                            for ($i = 1; $i < 13; $i++) {
                                $selected = "";
                                if ($data['data'] && $data['data']->getDeliverymonth() == $i) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $i . '" ' . $selected . '>' . getMonth($i) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliveryzone" class="col-sm-2 col-form-label">Zona de entrega</label>
                    <div class="col-sm-10">
                        <select name="deliveryzone" id="deliveryzone" class="form-control" <?php echo $disabled; ?>>
                            <option value="">Seleccione una zona</option>
                            <?php
                            $zones = getZones();
                            foreach ($zones as $zone) {
                                $selected = "";
                                if ($data['data'] && $data['data']->getDeliveryzone() == $zone->id) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $zone->id . '" ' . $selected . ' >' . $zone->zone . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="total" class="col-sm-2 col-form-label">Importe total</label>
                    <div class="col-sm-10">
                        <input type="text" name="total" id="total" class="form-control"
                               value="<?php echo $data['data'] ? numberFormat($data['data']->getTotal(), true, 2) : ""; ?>"
                            <?php echo $disabled; ?> onkeyup="addCommas($(this).prop('id'), $(this).val());">
                        <?php if ($canEdit) { ?>
                            <span class="red-color">(decimales separados por coma)</span>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pendingpay" class="col-sm-2 col-form-label">Pendiente de pago</label>
                    <div class="col-sm-10">
                        <input type="text" name="pendingpay" id="pendingpay" class="form-control"
                               value="<?php echo $data['data'] ? numberFormat($data['data']->getPendingpay(), true, 2) : ""; ?>"
                            <?php echo $disabled; ?> onkeyup="addCommas($(this).prop('id'), $(this).val());">
                        <?php if ($canEdit) { ?>
                            <span class="red-color">(decimales separados por coma)</span>
                        <?php } ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliveryzone" class="col-sm-2 col-form-label">Estado del pago</label>
                    <div class="col-sm-10">
                        <select name="pendingstatus" id="pendingstatus" class="form-control" <?php echo $disabled; ?>>
                            <?php
                            global $pandingstatus;
                            foreach ($pandingstatus as $key => $value) {
                                $selected = "";
                                if ($data['data'] && $data['data']->getPendingstatus() == $key) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliveryzone" class="col-sm-2 col-form-label">Mediante</label>
                    <div class="col-sm-10">
                        <select name="paymethod" id="paymethod" class="form-control" <?php echo $disabled; ?>>
                            <option value="">Seleccione una forma de pago</option>
                            <?php
                            global $paymethods;
                            foreach ($paymethods as $key => $value) {
                                $selected = "";
                                if ($data['data'] && $data['data']->getPaymethod() == $key) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="paydate" class="col-sm-2 col-form-label">Fecha de pago</label>
                    <div class="col-sm-10">
                        <input type="text" name="paydate" id="paydate" class="form-control" value="<?php echo $data['data'] ? americaDate($data['data']->getPaydate(), false) : ''; ?>" <?php echo $disabled; ?>>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Estado de pedido</label>
                    <div class="col-sm-10">
                        <select id="status" name="status" class="form-control" onchange="checkChangeStatus();" <?php echo !$data['data'] ? 'disabled="disabled"' : $disabled; ?>>
                            <?php
                            global $status;
                            foreach ($status as $key => $value) {
                                $selected = "";
                                if ($data['data'] && $data['data']->getStatus() == $key) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Nº de productos</label>
                    <div class="col-sm-10">
                        <input type="text" name="totalitems" id="totalitems" class="form-control" value="<?php echo $data['data'] ? $data['data']->getTotalItems() : ''; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <input type="hidden" name="opt" id="opt"
                           value="<?php echo $data['data'] ? 'save_edit_order' : 'save_order' ?>">
                    <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                    <span id="dynamicid"></span>
                    <?php
                    if ($data['data']) {
                        echo '<input type="hidden" name="id" value="' . $data['data']->getId() . '">' . PHP_EOL;
                    } else {
                        global $user;
                        $id = $user->getId();
                        //echo '<input type="hidden" name="code" value="' . $data['pdfinfo']->code . '">' . PHP_EOL;
                        echo '<input type="hidden" name="store" value="' . $data['pdfinfo']->storeid . '">' . PHP_EOL;
                        echo '<input type="hidden" name="createdby" value="' . $id . '">' . PHP_EOL;
                        echo '<input type="hidden" name="createdon" value="' . (date('Y-m-d H:i:s')) . '">' . PHP_EOL;
                        echo '<input type="hidden" name="pdfid" value="' . $_GET['pdfid'] . '">' .PHP_EOL;
                    }
                    echo '<input type="hidden" name="pdfid" value="' . $pdfid . '">' .PHP_EOL;
                    ?>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-4">
                            <?php if ($canEdit) { ?>
                                <input type="button" value="<?php echo $data['data'] ? "Modificar datos" : "Guardar datos" ?>" id="btnsaveorder" onclick="sendAjaxForm()" class="btn btn-primary">
                            <?php } ?>
                        </div>
                        <div class="col-sm-3">
                            <?php exit_btn(getUrl("show", $myController->getUrls())); ?>
                        </div>
                        <div class="col-sm-2">
                            <?php echo spinner_icon('refresh', 'sp-order-info'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Productos  -->
    <?php include(VIEWS_PATH_CONTROLLER . 'order_products' . VIEW_EXT); ?>
    <!-- Fin de productos-->
</div>

<!-- Modals para comentarios-->
<?php include(VIEWS_PATH_CONTROLLER . 'modals' . VIEW_EXT); ?>
<!-- End modals -->

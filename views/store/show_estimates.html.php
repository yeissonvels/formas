<?php datePicker(array('from', 'to', 'total_checked_on', 'accounting_checked_on', 'commission_payed_on', 'pending_payed_on')); ?>
<script>
    $(document).ready(function(){
        $("#search-box").keyup(function(){
            if ($(this).val() != "" && $(this).val().length > 1) {
                $.ajax({
                    type: "POST",
                    url: "/ajax.php",
                    data: {
                        keyword: $(this).val(),
                        op: 'getAutocompleteCode',
                        estimate: 'yes',
                    },
                    beforeSend: function () {
                        //$("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
                    },
                    success: function (data) {
                        $("#suggesstion-box").show();
                        $("#suggesstion-box").html(data);
                        $("#search-box").css("background", "#FFF");

                    }
                });
            } else {
                $('#code').prop("value", "");
                $("#suggesstion-box").html("");
            }
        });
    });

    function selectCode(id, code) {
        $("#search-box").val(code);
        $("#code").val(id);
        $("#suggesstion-box").hide();
    }

    function searchEstimates() {
        $('#dynamic-cus').html("");
        $('#sp_sales').show();
        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: $('#frm1').serialize(),
        }).done(function(Response) {
            $('#sp_sales').hide();
            $('#dynamic-cus').html(Response);
            reloadQtip();
            scrollingTo('#dynamic-cus');
        });
    }

    function removeFilters() {
        $("option:selected").prop("selected", false);
        $('#code').prop("value", "");
        $(":text").prop("value", "");
        $("#suggesstion-box").html("");
    }

    function setTotalId(id) {
        $('#totalid').prop('value', id);
    }

    function setPaymentId(id) {
        $('#id').prop('value', id);
    }

    function checkTotal() {
        comprobate = Array('#total_checked_on', '#total_checked_note');

        if (checkNoEmpty(comprobate)) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#totalValidationForm').serialize()

            }).done(function(Response) {
                var res = JSON.parse(Response);
                if (res['updated'] == 1) {
                    var tdId = $('#totalid').val();
                    $('#totalvalidation' + tdId).html(res['html']);
                    reloadQtip();
                    $('#totalid').prop("value", "");
                    $('#total_checked_on').prop("value", "");
                    $('#total_checked_note').prop("value", "");
                    alert("Validación guardada!");
                }
            });
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function setAdjustid(parentid, code, op, id) {
        // Reset de formulario
        $('#pending_payed_on').prop('value', '');
        $('#pendingpay_method > option[value=""]').prop('selected', true);
        $('#pending_payed_amount').prop('value', '');
        $('#pending_adjust_note').prop('value', '');

        if (op == "save") {
            mylabel = 'adjustPendingPay';
            btnlabel = 'Guardar';
        } else {
            // Sólo si vamos a actualizar seteamos el id
            $('#updateid').prop('value', id);
            mylabel = 'updateAdjustPendingPay';
            btnlabel = 'Actualizar';
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'getAdjustPendingPayData',
                    parentid: parentid
                }
            }).done(function(Response) {
                var res = JSON.parse(Response);
                $('#pending_payed_on').prop('value', res['saledate']);
                $('#pending_payed_amount').prop('value', res['payed']);
                //$('#pendingpay_method').prop('value', res['paymethod']);
                $('#pendingpay_method > option[value="' + res['paymethod'] + '"]').prop('selected', true);
                $('#pending_adjust_note').prop('value', res['comment']);
            });
        }

        $('#adjustid').prop('value', parentid);
        $('#adjustcode').prop('value', code);
        $('#intern-opt').prop('value', mylabel);
        $('#adjust-lbl').html(btnlabel);
    }

    function checkPendingPay() {
        comprobate = Array('#pending_payed_on', '#pending_payed_amount', '#pendingpay_method', '#pending_adjust_note');
        if (checkNoEmpty(comprobate)) {
            $('#sp-in-adjust').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#pendingPayAdjustForm').serialize()

            }).done(function(Response) {
                $('#sp-in-adjust').hide();
                var res = JSON.parse(Response);
                if (res['saved'] == 1) {
                    $('#pending_payed_on').prop('value', '');
                    $('#pendingpay_method > option[value=""]').prop('selected', true);
                    $('#pending_payed_amount').prop('value', '');
                    $('#pending_adjust_note').prop('value', '');
                    $('#btn-search-sales').trigger('click');
                    alert("Pendiente de pago ajustado correctamente!");
                } else if (res['updated'] == 1) {
                    $('#pending_payed_on').prop('value', '');
                    $('#pendingpay_method > option[value=""]').prop('selected', true);
                    $('#pending_payed_amount').prop('value', '');
                    $('#pending_adjust_note').prop('value', '');
                    $('#btn-search-sales').trigger('click');
                    alert("Pendiente de pago actualizado correctamente!");
                } else if (res['duplicated'] == 1) {
                    alert('Nada para actualizar!');
                }
            });
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function checkPayment() {
        comprobate = Array('#accounting_checked_on', '#accounting_checked_note');

        if (checkNoEmpty(comprobate)) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#validationForm').serialize()

            }).done(function(Response) {
                var res = JSON.parse(Response);
                if (res['updated'] == 1) {
                    var tdId = $('#id').val();
                    $('#validation' + tdId).html(res['html']);
                    reloadQtip();
                    $('#id').prop("value", "");
                    $('#accounting_checked_on').prop("value", "");
                    $('#accounting_checked_note').prop("value", "");
                    alert("Validación guardada!");
                }
            });
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function checkPaymentcommission() {
        comprobate = Array('#commission_payed_on');

        if (checkNoEmpty(comprobate)) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#commissionForm').serialize()

            }).done(function(Response) {
                var res = JSON.parse(Response);
                if (res['updated'] == 1) {
                    var tdId = $('#commission-id').val();
                    $('#commission' +  tdId).html(res['html']);
                    $('#commission-id').prop("value", "");
                    $('#commission_payed_on').prop("value", "");
                    reloadQtip();
                    alert("Validación de propuesta guardada!");
                }
            });
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function changeValidate(id) {
        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: {
                id: id,
                op: 'getValidationPaymentData',
            }

        }).done(function(Response) {
            var res = JSON.parse(Response);
            $('#accounting_checked_on').prop("value", res['accounting_checked_on']);
            $('#accounting_checked_note').prop("value", res['accounting_checked_note']);
            $('#id').prop("value", id);
        });
    }

    function deleteTotalValidation(id) {
        if (confirm('¿Confirmas que deseas eliminar la validación?')) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'deleteTotalValidation',
                    id: id
                }
            }).done(function(Response) {
                var res = JSON.parse(Response);
                if (res['updated'] == 1) {
                    $('#totalvalidation' + id).html(res['html']);
                    alert("Validación eliminada correctamente!");
                }
            });
        }
    }

    function deleteValidation(id) {
        if (confirm('¿Confirmas que deseas eliminar la validación?')) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'deleteValidation',
                    id: id
                }
            }).done(function(Response) {
                var res = JSON.parse(Response);
                if (res['updated'] == 1) {
                    $('#validation' + id).html(res['html']);
                    alert("Validación eliminada correctamente!");
                }
            });
        }
    }

    /**
     * id realmente es el código de la venta.
     */
    function setSaleToCancelId(id, saleid, saletype) {
        $('#idsaledelete').prop('value', id);
        $('#saleid').prop('value', saleid);
        $('#mysaletype').prop('value', saletype);
        if (saletype == 0) {
            $('#btncancel-Label').html('venta');
            $('#typeLabel').html('venta');
        } else {
            $('#btncancel-Label').html('variación');
            $('#typeLabel').html('variación');
        }
    }

    function cancellSale() {
        if ($('#cancell_reason').val() != "") {
            border_ok('#cancell_reason');
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#cancelForm').serialize(),
            }).done(function(Response){
                var res = JSON.parse(Response);
                var trId = $('#idsaledelete').val();
                var succesLabel = 'Venta ';
                if (res['updated'] == 1){
                    $('#idsaledelete').prop('value', '');
                    $('#cancell_reason').prop('value', '');

                    /*$('#validation' + trId).html("");
                    $('#td-edit-' + trId).html("");
                    $('#td-delete-' + trId).html("");
                    $('#tr-' + trId).addClass('deleted');*/
                    $('#btn-search-sales').trigger('click');
                    if ($('#mysaletype').val() == 1) {
                        succesLabel = 'Variación ';
                    }
                    alert(succesLabel + "anulada correctamente!");
                    scrollingTo('#tr-' + trId);
                }
            });
        } else {
            border_error('#cancell_reason');
            alert('Por favor indica el motivo!');
        }
    }

    function setEstimateAsSaleInitial(id, code) {
        //  href="?controller=store&opt=setEstimateAsSaleInitial&id=' . $pdf->id . '"
        if (confirm('¿Confirmas que desear crear una venta inicial del presupuesto "' + code + '"?')) {
            changeTdStyle(id);
            window.open("?controller=store&opt=setEstimateAsSaleInitial&id=" + id, "_blank");
        }
    }


    function changeTdStyle(id) {
        var html = 'Si';
        $('#tr-' + id).addClass('table-success');
        $('#initial-' + id).html(html);
    }


</script>
<div>
    <?php
        if (isset($msg) && $msg != "") {
            confirmationMessage($msg);
        }
    ?>
</div>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Presupuestos</h4>
        <span><a href="<?php echo getUrl('new_estimate', $myController->getUrls()); ?>">Nuevo</a></span>
        <?php update_icon(getUrl('show_estimates', $myController->getUrls())); ?>
        <form action="<?php echo getUrl('show_estimates', $myController->getUrls()); ?>" method="post" id="frm1">
            <div class="form-group row">
                <label for="purchasedate" class="col-sm-1 col-form-label">Mes <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php
                        generateSelectMonth("", false);
                	?>
                </div>
                <?php if (userWithPrivileges()){ ?>
                <label for="year" class="col-sm-1 col-form-label">Año <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php generateSelectYear(2017, "", false); ?>
                </div>
                <?php } ?>

                <div class="col-sm-2">
                    <input type="hidden" name="estimates" value="1">
                    <input type="hidden" name="op" value="searchEstimates">
                    <input type="button" value="Buscar" class="btn btn-primary" onclick="searchEstimates();" id="btn-search-sales">
                    <?php spinner_icon('spinner', 'sp_sales', true); ?>
                </div>

                <div class="col-sm-2">
                    <input type="button" value="Quitar filtros" onclick="removeFilters();" class="btn btn-warning">
                </div>
            </div>

            <?php
            global $user;
            if (isadmin() || $user->getUsermanager() == 1 || $user->getUseraccounting() == 1 || $user->getUserrepository() == 1) { ?>
                <div class="form-group row">
                    <label for="purchasedate" class="col-sm-1 col-form-label">Usuario <?php icon('user', true);?></label>
                    <div class="col-sm-2">
                        <select name="user" class="form-control">
                            <option value="">Elije uno</option>
                            <?php
                                $users = getUsers();
                                foreach ($users as $us) {
                                    if ($us->userstore == 1) {
                                        echo '<option value="' . $us->id . '">' . $us->username . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <label for="purchasedate" class="col-sm-1 col-form-label">Tienda <?php icon('home', true);?></label>
                    <div class="col-sm-2">
                        <select name="store" class="form-control">
                            <option value="">Elija una tienda</option>
                            <option value="all" <?php echo isset($_POST['store']) && $_POST['store'] == "all" ? 'selected="selected"' : "" ?>>Todas</option>
                            <?php
                            $stores = getStores(true);
                            foreach ($stores as $store) {
                                $selected = "";
                                if (isset($_POST['store']) && $_POST['store'] == $store['id']) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $store['id'] . '" ' . $selected . '>' . $store['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
                <div class="form-group row">
                    <?php if (userWithPrivileges()) { ?>
                      <label for="from" class="col-sm-1 col-form-label">Desde <?php icon('calendar', true);?></label>
                      <div class="col-sm-2">
                          <input type="text" name="from" id="from" class="form-control">
                      </div>
                      <label for="to" class="col-sm-1 col-form-label">Hasta <?php icon('calendar', true);?></label>
                      <div class="col-sm-2">
                          <input type="text" name="to" id="to" class="form-control">
                      </div>
                    <?php } ?>

                </div>

                <div class="form-group row">
                    <label for="from" class="col-sm-1 col-form-label">Código <?php icon('barcode', true); ?></label>
                    <div class="col-sm-4">
                        <input type="text" id="search-box" class="form-control" placeholder="Código o nombre del cliente">
                        <input type="hidden" name="code" id="code" class="form-control" value="">
                        <div id="suggesstion-box" style="position: absolute; z-index: 10000;"></div>
                    </div>
                </div>
        </form>
    </div>

    <div class="card-block">
        <table class="table table-responsive" id="dynamic-cus">
        </table>
    </div>
</div>

<!-- Modal de cancelación -->
<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade show" id="cancelSale">
    <div role="document" class="modal-dialog">
        <form id="cancelForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLiveLabel" class="modal-title">Anular <span id="typeLabel">venta</span> <?php icon('delete', true); ?></h5>
                    <button aria-label="Close" data-dismiss="modal" class="close" type="button">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="accounting_checked_note" class="col-sm-4 col-form-label">Motivo</label>
                                <textarea class="form-control" name="cancell_reason" id="cancell_reason"></textarea>
                                <input type="hidden" id="idsaledelete" name="id" value="">
                                <input type="hidden" id="saleid" name="saleid" value="">
                                <input type="hidden" id="mysaletype" name="saletype" value="">
                                <input type="hidden" name="opt" value="cancelEstimate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="cancellSale();">Anular <span id="btncancel-Label">venta</span></button>
                    <?php spinner_icon('spinner', 'sp-in-comment', true); ?>
                    <button data-dismiss="modal" class="btn btn-secondary" type="button">Salir</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
    datePicker('saledate');
?>
<script>
    var id = "<?php echo $data ? $data->id : ''; ?>";
    var codeValidated = <?php  echo $data && $data->parentcode ? 'true' : 'false' ?>;

    $(document).ready(function(){
        $("#search-box").keyup(function(){
            if ($(this).val() != "") {
                $.ajax({
                    type: "POST",
                    url: "/ajax.php",
                    data: {
                        keyword: $(this).val(),
                        op: 'getAutocompleteCode',
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
                $("#suggesstion-box").html("");
            }
        });
    });

    function selectCode(id, code) {
        $("#search-box").val(code);
        $("#parentcode").val(id);
        $("#suggesstion-box").hide();
    }

    function saveOrderPay() {
        comprobate = Array('#saledate', '#parentcode', '#payed', '#search-box', '#paymethod');

        if (checkNoEmpty(comprobate)) {
            $('#sp-save-sale').show();
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: $('#frm-saledata').serialize(),
            }).done(function (data) {
                $('#sp-save-sale').hide();
                var res = JSON.parse(data);
                var salelabel = ' la entrega a cuenta';

                if (res['saved'] == 1) {
                    id = res['lastid'];
                    $('#commentsdiv').show();
                    alert('Datos de la ' + salelabel + ' registrados.');
                    $('#btn-save-sale').prop('value', 'Modificar entrega a cuenta');
                    $('#opt-save-sale').prop('value', 'updateOrderPay');
                    $('#id').prop('value', id);
                } else if (res['updated'] == 1) {
                    alert('Datos de ' + salelabel + ' actualizados correctamente!');
                } else if (res['duplicated'] == 1) {
                    alert('Nada para actualizar!');
                }
            });
        } else {
            alert(completeRequiredFields);
        }
    }

    function changeSaleType() {
        var defaultLabel = "Guardar ";
        if (id != "") {
            defaultLabel = "Modificar ";
        }

        if ($('#saletype').val() == 0) {
            $('#btn-save-sale').prop("value", defaultLabel + "venta");
            $('#div_code').show();
            $('#div_parentcode').hide();
            $('#div_customer').show();
            $('#div_payed').show();
            if (id != "") {
                $('#div_img2').show();
                $('#div_pdf').show();
            }

        } else {
            $('#btn-save-sale').prop("value", defaultLabel + "variación");
            $('#div_code').hide();
            $('#div_parentcode').show();
            $('#div_customer').hide();
            /*$('#div_payed').hide();
            $('#div_img2').hide();
            $('#div_pdf').hide();*/
        }

    }

    function saveComment() {
        var pdfid = id;
        var comment = $('#pdfcomment').val();
        comprobate = Array('#pdfcomment');

        if (checkNoEmpty(comprobate)) {
            $('#sp-cus-comment').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    pdfid: id,
                    comment: comment,
                    op: 'savePdfComment'
                }
            }).done(function(Response) {
                $('#pdfcomment').prop('value', '');
                $('#sp-cus-comment').hide();
                $('#dynamic-cus-comments').html(Response);
            });
        } else {
            alert(completeRequiredFields);
        }
    }
</script>

<?php
    $disabled = 'disabled="disabled"';
    $canEdit = false;
    global $user;
    if (!$data) {
        $canEdit = true;
        $disabled = '';
    } else if ($data && !isTimeOver($data->created_on) || $user->getUsermanager() == 1) {
        $canEdit = true;
        $disabled = '';
    }

?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo $data ? "Modificar entrega a cuenta " : "Nueva entrega a cuenta " ?><?php icon('money', true); ?></h4>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-sm-6">
                <form id="frm-saledata">
                    <div class="form-group row" id="div_saledate">
                        <label for="saledate"
                               class="col-sm-2 col-form-label">Fecha de la entrega</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="saledate" id="saledate"
                                   value="<?php echo $data ? americaDate($data->saledate, false) : ''; ?>" <?php echo $disabled; ?> autocomplete="off">
                        </div>
                    </div>
                    <?php
                        $parentValue = "";
                        if ($data) {
                            $parentValue = $data->parent->code . ' (' . americaDate($data->parent->saledate, false) . ') ' . $data->parent->customer;
                        }
                    ?>
                    <div class="form-group row" id="div_parentcode">
                        <label for="parentcode"
                               class="col-sm-2 col-form-label">Nº de pedido asociado</label>
                        <div class="col-sm-10">
                            <input type="text" id="search-box" placeholder="Código o nombre del cliente"  class="form-control" value="<?php echo $parentValue; ?>" autocomplete="off"/>
                            <input type="hidden" name="parentcode" id="parentcode" value="<?php echo $data ? $data->parentcode: ''; ?>">
                            <div id="suggesstion-box" style="position: absolute; z-index: 10000;"></div>
                        </div>
                    </div>

                    <div class="form-group row" id="div_payed">
                        <label for="payed"
                               class="col-sm-2 col-form-label">Importe de la entrega a cuenta</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="payed" id="payed" onkeyup="addCommas($(this).prop('id'), $(this).val());"
                                   value="<?php echo $data ? numberFormat($data->payed, true, 2) : ""; ?>" <?php echo $disabled; ?>>
                            <span class="red-color">(decimales separados por coma)</span>
                        </div>
                    </div>

                    <div class="form-group row" id="div_paymethod">
                        <label for="paymethod"
                               class="col-sm-2 col-form-label">Mediante</label>
                        <div class="col-sm-10">
                            <select name="paymethod" id="paymethod" class="form-select" <?php echo $disabled; ?>>
                                <option value="">Elija una opción</option>
                                <?php
                                    global $paymethods;
                                    foreach ($paymethods as $key => $value) {
                                        $selected = "";
                                        if ($data && $data->paymethod == $key) {
                                            $selected = 'selected="selected"';
                                        }
                                        echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php if ($canEdit) {
                            ?>
                            <div class="col-sm-4">
                                <input type="hidden" name="id" id="id" value="<?php echo $data ? $data->id : ''; ?>">
                                <input type="hidden" name="opt" value="<?php echo $data ? 'updateOrderPay' : 'saveOrderPay'; ?>"
                                       id="opt-save-sale">
                                <input type="button" class="btn btn-primary" id="btn-save-sale"
                                       value="<?php echo($data ? 'Modificar ' : 'Guardar ') . 'entrega a cuenta ' ?>" onclick="saveOrderPay();">
                                <?php spinner_icon('spinner', 'sp-save-sale', true); ?>
                            </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
            <!-- Columna derecha -->
            <?php
            $commentsDisplay = $data ? 'block' : 'none';
            ?>
            <div class="col-sm-6">
                <div class="modal-body" style="display: <?php echo $commentsDisplay?>;" id="commentsdiv">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12" style="max-height: 200px; overflow: auto;">
                                <table class="table table-striped" id="dynamic-cus-comments">
                                    <?php
                                    include (VIEWS_PATH_CONTROLLER . 'pdf_comments' . VIEW_EXT);
                                    ?>
                                </table>
                            </div>
                            <div class="col-lg-12">
                                <textarea class="form-control" id="pdfcomment"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button class="btn btn-primary" type="button" onclick="saveComment(1);">Nuevo comentario</button>
                        <?php spinner_icon('spinner', 'sp-cus-comment', true); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php
                exit_btn(getUrl('show_pdfs', $myController->getUrls()));
            ?>
        </div>
    </div>
</div>

<?php datePicker(array('from', 'to', 'total_checked_on', 'accounting_checked_on', 'commission_payed_on', 'pending_payed_on')); ?>
<script>
    $(document).ready(function(){
        $("#search-box").keyup(function(){
            if ($(this).val() != "" && $(this).val().length > 1) {
                let config = {
                    searchBox: '#search-box',
                    inputId: '#code',
                    suggestionBox: '#suggesstion-box'
                };
                $.ajax({
                    type: "POST",
                    url: "/ajax.php",
                    data: {
                        keyword: $(this).val(),
                        config: config,
                        op: 'getAutocompleteEstimateCode',
                        estimate: 'yes',
                        all: 'yes'
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

    function selectEstimateCode(code, fullCode, configString) {
        let config = configString.split(',');
        $(config[0]).val(fullCode);
        $(config[1]).val(code);
        $(config[2]).hide();
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

    /**
     * id realmente es el código del presupuesto.
     */
    function setSaleToCancelId(code, id, saletype) {
        $('#estimateCode').prop('value', code);
        $('#id').prop('value', id);
        
        $('#btncancel-Label').html('presupuesto');
        $('#typeLabel').html('presupuesto');
       
    }

    function cancellEstimate() {
        if ($('#cancell_reason').val() != "") {
            border_ok('#cancell_reason');
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#cancelForm').serialize(),
            }).done(function(Response){
                var res = JSON.parse(Response);
                var trId = $('#id').val();
                var succesLabel = 'Presupuesto ';
                if (res['updated'] == 1){
                    $('#estimateCode').prop('value', '');
                    $('#cancell_reason').prop('value', '');
                    $('#btn-search-sales').trigger('click');
                    $('#btn-close-modal').trigger('click');
                    alert(succesLabel + "anulado correctamente!");
                    //scrollingTo('#tr-' + trId);
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
        <h4 class="card-title">Presupuestos <?php icon('estimate', true); ?></h4>
        <span><a href="<?php echo getUrl('new_estimate', $myController->getUrls()); ?>"><?php icon('save', true); ?></a></span>
        <?php update_icon(getUrl('show_estimates', $myController->getUrls())); ?>
        <form action="<?php echo getUrl('show_estimates', $myController->getUrls()); ?>" method="post" id="frm1">
            <div class="form-group row">
                <label for="purchasedate" class="col-sm-1 col-form-label">
                    <h6 class="filter-label-icon">Mes <?php icon('calendar', true);?></h6>
                </label>
                <div class="col-sm-2">
                    <?php
                        //generateSelectMonth("", false);
                        generateEstimateSelectMonth();
                	?>
                </div>
                <?php if (userWithPrivileges()){ ?>
                <label for="year" class="col-sm-1 col-form-label">
                    <h6 class="filter-label-icon">Año <?php icon('calendar', true);?></h6>
                </label>
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

            <div class="form-group row">
                <label for="purchasedate" class="col-sm-1 col-form-label">
                    <h6 class="filter-label-icon">Criterio de ordenación <?php icon('sort', true);?></h6>
                </label>
                <div class="col-sm-2">
                   <select class="form-select" name="order" id="order">
                        <option value="DESC">Más reciente primero</option>
                        <option value="ASC">Más antiguo primero</option>
                   </select>
                </div>
            </div>

            <?php
            global $user;
            if (userWithPrivileges()) { ?>
                <div class="form-group row">
                    <label for="purchasedate" class="col-sm-1 col-form-label">
                        <h6 class="filter-label-icon">Usuario <?php icon('user', true);?></h6>
                    </label>
                    <div class="col-sm-2">
                        <select name="user" class="form-select">
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

                    <label for="purchasedate" class="col-sm-1 col-form-label">
                        <h6 class="filter-label-icon">Tienda <?php icon('home', true);?></h6>
                    </label>
                    <div class="col-sm-2">
                        <select name="store" class="form-select">
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
                      <label for="from" class="col-sm-1 col-form-label">
                        <h6 class="filter-label-icon">Desde <?php icon('calendar', true);?></h6>
                      </label>
                      <div class="col-sm-2">
                          <input type="text" name="from" id="from" class="form-control">
                      </div>
                      <label for="to" class="col-sm-1 col-form-label">
                        <h6 class="filter-label-icon">Hasta <?php icon('calendar', true);?></h6>
                      </label>
                      <div class="col-sm-2">
                          <input type="text" name="to" id="to" class="form-control">
                      </div>
                    <?php } ?>

                </div>

                <div class="form-group row">
                    <?php 
                        if (userWithPrivileges()) { ?>
                            <label for="from" class="col-sm-1 col-form-label">
                            <h6 class="filter-label-icon">Estado <?php icon('status', true);?></h6>
                            </label>
                            <div class="col-sm-2">
                                <select class="form-select" name="status" id="status">
                                    <option value="">Elije uno</option>
                                    <option value="no">Sin venta</option>
                                    <option value="yes">Convertido en venta</option>
                                </select>
                            </div>
                    <?php 
                        }
                    ?>
                    <label for="from" class="col-sm-1 col-form-label">
                        <h6 class="filter-label-icon">Código <?php icon('barcode', true); ?></h6>
                    </label>
                    <div class="col-sm-4">
                        <input type="text" id="search-box" class="form-control" placeholder="Código o nombre del cliente">
                        <input type="hidden" name="code" id="code" class="form-control" value="">
                        <div id="suggesstion-box" style="position: absolute; z-index: 10000;"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <?php if (userWithPrivileges()) { ?>
                      <label for="from" class="col-sm-1 col-form-label">
                        <h6 class="filter-label-icon">Teléfono <?php icon('phone', true);?></h6>
                      </label>
                      <div class="col-sm-2">
                          <input type="text" name="tel" id="tel" class="form-control" placeholder="Teléfono o teléfono 2">
                      </div>
                    <?php } ?>

                </div>
        </form>
    </div>

    <div class="card-block">
        <table class="table table-responsive" id="dynamic-cus">
        </table>
    </div>
</div>

<!-- Modal de cancelación -->
<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade" id="cancellSale">
    <div role="document" class="modal-dialog">
        <form id="cancelForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLiveLabel" class="modal-title">Anular <span id="typeLabel">presupuesto</span> <?php icon('delete', true); ?></h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="accounting_checked_note" class="col-sm-4 col-form-label">Motivo</label>
                                <textarea class="form-control" name="cancell_reason" id="cancell_reason"></textarea>
                                <input type="hidden" id="estimateCode" name="code" value="">
                                <input type="hidden" id="id" name="id" value="">
                                <input type="hidden" name="opt" value="cancellEstimate">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" onclick="cancellEstimate();">Anular <span id="btncancel-Label">presupuesro</span></button>
                    <?php spinner_icon('spinner', 'sp-in-comment', true); ?>
                    <button data-bs-dismiss="modal" class="btn btn-secondary" type="button" id="btn-close-modal">Salir</button>
                </div>
            </div>
        </form>
    </div>
</div>
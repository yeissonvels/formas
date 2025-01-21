<?php datePicker(array('from', 'to', 'accounting_checked_on')); ?>
<script>
    $(document).ready(function(){
        $("#search-box").keyup(function(){
            if ($(this).val() != "" && $(this).val().length > 1) {
                $.ajax({
                    type: "POST",
                    url: "/ajax.php",
                    data: {
                        keyword: $(this).val(),
                        op: 'getAutocompleteIncidenceCode',
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

    function searchIncidences() {
        $('#dynamic-cus').html("");
        $('#sp_sales').show();
        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: $('#frm1').serialize(),
        }).done(function(Response) {
            $('#sp_sales').hide();
            //$('#code').prop("value", "");
            $('#dynamic-cus').html(Response);
            reloadQtip();
        });
    }

    function removeFilters() {
        $("option:selected").prop("selected", false);
        $('#code').prop("value", "");
        $(":text").prop("value", "");
        $("#suggesstion-box").html("");
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
        <h4 class="card-title">Incidencias <?php icon('incidence', true); ?></h4>
        <span><a href="<?php echo getUrl('new_incidence', $myController->getUrls()); ?>">Nueva</a></span>
        <?php update_icon(getUrl('show_incidences', $myController->getUrls())); ?>
        <form action="" method="post" id="frm1">
            <div class="form-group row">
                <label for="purchasedate" class="col-sm-1 col-form-label">Mes <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php generateSelectMonth("", false); ?>
                </div>
                <label for="year" class="col-sm-1 col-form-label">Año <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php generateSelectYear(2017, "", false); ?>
                </div>

                <div class="col-sm-2">
                    <input type="hidden" name="op" value="searchIncidences">
                    <input type="button" value="Buscar" class="btn btn-primary" onclick="searchIncidences();">
                    <?php spinner_icon('spinner', 'sp_sales', true); ?>
                </div>

                <div class="col-sm-2">
                    <input type="button" value="Quitar filtros" onclick="removeFilters();" class="btn btn-warning">
                </div>
            </div>
            <div class="form-group row">
                <label for="from" class="col-sm-1 col-form-label">Desde <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <input type="text" name="from" id="from" class="form-control">
                </div>
                <label for="to" class="col-sm-1 col-form-label">Hasta <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <input type="text" name="to" id="to" class="form-control">
                </div>
                <label for="to" class="col-sm-1 col-form-label">Estado</label>
                <div class="col-sm-3">
                   <select name="status" id="status" class="form-control">
                       <option value="all">Todos</option>
                       <?php
                            global $incidencestatus;
                            foreach ($incidencestatus as $key => $value) {
                                echo '<option value="' . $key . '">' . $value . '</option>';
                            }
                       ?>
                   </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="from" class="col-sm-1 col-form-label">Nº de pedido <?php icon('barcode', true); ?></label>
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
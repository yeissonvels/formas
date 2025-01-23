<script>
    function searchOrder() {
        $('#dynamic-cus').html("");
        $('#sp-search').show();
        $.ajax({
            url: '/ajax.php',
            type: 'post',
            data: $('#frm').serialize(),

        }).done(function(Response) {
            $('#dynamic-cus').html(Response);
            $('#sp-search').hide();
        });
    }

    function removeFilters() {
        $("option:selected").prop("selected", false);
    }

    function confirmRestore(id) {
        if (confirm('Estás seguro de que deseas devolver el PDF a la tienda?')) {
            window.location.href = '<?php echo getUrl('restore_pdf', $myController->getUrls());?>' + id;
        }
    }

    $(document).ready(function() {
        $('#order').click(function() {
            border_ok(this);
        });
    });
</script>
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Listado de pedidos</h4>
        <hr>
        <form id="frm">
            <div class="form-group row">
                <label for="deliveryrange" class="col-sm-1 col-form-label">Quincena</label>
                <div class="col-sm-2">
                    <select name="deliveryrange" id="deliveryrange" class="form-select">
                        <option value="">Todas</option>
                        <?php
                            global $deliveryRanges;
                            foreach ($deliveryRanges as $key => $value) {
                                $selected = "";
                                if ($key == 0) {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <label for="month" class="col-sm-1 col-form-label">Mes
                    <a class="cursor-pointer withqtip" title="Es el mes de entrega del pedido<br><br>Si no se ha seleccionado uno se busca por el mes actual">
                        <?php icon('calendar', true);?></a>
                </label>
                <div class="col-sm-2">
                    <?php generateSelectMonth("", false); ?>
                </div>
                <label for="year" class="col-sm-1 col-form-label">Año <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php generateSelectYear(2017, "", false); ?>
                </div>
            </div>
            <div class="form-group row">
                <label for="store" class="col-sm-1 col-form-label">Tienda <?php icon('home', true);?></label>
                <div class="col-sm-3 mb-1">
                    <select name="store" id="store" class="form-select">
                        <option value="">Seleccione una tienda</option>
                        <?php
                            $stores = getStores(true);
                            foreach ($stores as $store) {
                                echo '<option value="' . $store['id'] . '"> ' . $store['name'] . ' </option>';
                            }
                        ?>
                    </select>
                </div>
                <label for="status" class="col-sm-1 col-form-label">Estado <?php icon('status', true); ?></label>
                <div class="col-sm-3">
                    <select name="status" id="status" class="form-select">
                        <option value="">Seleccione un estado</option>
                        <?php
                            global $status;
                            foreach ($status as $key => $value) {
                                echo '<option value="' . $key . '">' . $value . '</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="deliveryzone" class="col-sm-1 col-form-label">Zona <?php icon('map', true); ?></label>
                <div class="col-sm-3">
                    <select name="deliveryzone" id="deliveryzone" class="form-select">
                        <option value="">Seleccione una zona</option>
                        <?php
                        $zones = getZones();
                        foreach ($zones as $zone) {
                            echo '<option value="' . $zone->id . '">' . $zone->zone . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <input type="hidden" value="<?php echo FORM_CONTROLLER; ?>" name="controller">
                    <input type="hidden" name="opt" value="getOrdersList">
                    <input type="button" class="btn btn-primary" value="Buscar" onclick="searchOrder();">
                    <?php spinner_icon('spinner', 'sp-search', true); ?>
                </div>
                <div class="col-sm-2">
                    <input type="button" value="Quitar filtros" onclick="removeFilters();" class="btn btn-warning">
                </div>
            </div>
        </form>
    </div>
    <div class="card-block">
        <table class="table table-responsive" id="dynamic-cus">

        </table>
    </div>
</div>
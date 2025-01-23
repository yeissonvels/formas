<script>
    function searchOrder() {
        var order = $('#order').val();
        var store = $('#store').val();
        var month = $('#month').val();
        var year = $('#year').val();
        var validated = false;

        if (order.length > 0 || store != "" || month != 0 || year != 0) {
            border_ok('#order');
            validated = true;
        }
        if (validated) {
            $('#dynamic-cus').html("");
            $('#sp-search').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'getOrder',
                    criteria: order,
                    store: store,
                    month: month,
                    year: year,
                    controller: '<?php echo FORM_CONTROLLER; ?>',
                }
            }).done(function(Response) {
                $('#dynamic-cus').html(Response);
                $('#sp-search').hide();
            });
        } else {
            border_error('#order');
        }
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
        <h4 class="card-title">Buscar pedido</h4>
        <!--< ?php update_icon(getUrl('show', $myController->getUrls()));?> -->
        <hr>
        <div class="form-group row">
            <label for="month" class="col-sm-1 col-form-label">Mes <?php icon('calendar', true);?></label>
            <div class="col-sm-2">
                <?php generateSelectMonth("", false); ?>
            </div>
            <label for="month" class="col-sm-1 col-form-label">Año <?php icon('calendar', true);?></label>
            <div class="col-sm-2">
                <?php generateSelectYear(2017, "", false); ?>
            </div>
            <label for="store" class="col-sm-1 col-form-label">Tienda <?php icon('home', true);?></label>
            <div class="col-sm-3 mb-1">
                <select id="store" class="form-select">
                    <option value="">Seleccione una tienda</option>
                    <?php
                        $stores = getStores(true);
                        foreach ($stores as $store) {
                            echo '<option value="' . $store['id'] . '"> ' . $store['name'] . ' </option>';
                        }
                    ?>
                </select>
            </div>
            <div class="col-sm-2">
                <input type="button" value="Quitar filtros" onclick="removeFilters();" class="btn btn-warning">
            </div>
        </div>
        <div class="form-group row">
            <label for="order" class="col-sm-2 col-form-label">Código, Nombre del ciente ó Télefono: <a title="El código o teléfono debe ser completo" class="withqtip">
                    <?php icon('user', true);?></a></label>
            <div class="col-sm-5 mb-1">
                <input type="text" name="order" id="order" class="form-control">
            </div>
            <div class="col-sm-2">
                <input type="button" class="btn btn-primary" value="Buscar" onclick="searchOrder();">
                <?php spinner_icon('spinner', 'sp-search', true); ?>
            </div>
        </div>
    </div>
    <div class="card-block">
        <table class="table table-responsive" id="dynamic-cus">

        </table>
    </div>
</div>
<?php datePicker(array('from', 'to', 'accounting_checked_on')); ?>
<script>
    function searchEmails() {
        comprobate = Array("#product");

        if (checkNoEmpty(comprobate)) {
            $('#dynamic-cus').html("");
            $('#sp_sales').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#frm1').serialize(),
            }).done(function(Response) {
                $('#sp_sales').hide();
                $('#dynamic-cus').html(Response);
            });
        } else {
            alert(completeRequiredFields);
        }
    }

    function removeFilters() {
        $("option:selected").prop("selected", false);
        $('#code').prop("value", "");
        $(":text").prop("value", "");
        $("#suggesstion-box").html("");
    }

    function setPaymentId(id) {
        $('#id').prop('value', id);
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
        <h4 class="card-title">Correos de clientes <?php icon('email', true); ?></h4>
        <form action="<?php echo getUrl('show_pdfs', $myController->getUrls()); ?>" method="post" id="frm1">
            <div class="form-group row">
                <label for="product" class="col-sm-1 col-form-label">Producto</label>
                <div class="col-sm-2">
                    <select id="product" name="product" class="form-control">
                        <option value="">Seleccione</option>
                        <?php
                            $categories = getCategories(true);
                            foreach ($categories as $category) {
                                echo '<option value="' . $category['id'] . '">' . $category['category'] . '</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="month" class="col-sm-1 col-form-label">Mes <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php generateSelectMonth("", "", false); ?>
                </div>
                <label for="year" class="col-sm-1 col-form-label">Año <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php generateSelectYear(2017, "", false); ?>
                </div>

                <div class="col-sm-2">
                    <input type="hidden" name="op" value="searchEmails">
                    <input type="button" value="Buscar" class="btn btn-primary" onclick="searchEmails();">
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
            </div>
        </form>
    </div>

    <div class="card-block">
        <table class="table table-responsive" id="dynamic-cus">
        </table>
    </div>
</div>
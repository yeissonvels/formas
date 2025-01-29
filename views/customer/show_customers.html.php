<script>
    function searchCustomer() {
        var customer = $('#customer').val();
        if (customer.length > 0) {
            $('#dynamic-cus').html("");
            $('#loader').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    op: 'getCustomers',
                    criteria: customer,
                    controller: '<?php echo FORM_CONTROLLER; ?>',
                }
            }).done(function(Response) {
                $('#dynamic-cus').html(Response);
                $('#loader').hide();
            });
        } else {
            border_error('#customer');
        }
    }

    $(document).ready(function() {
        $('#customer').click(function() {
            border_ok('#customer');
        });
    });
</script>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo 'Clientes'; ?></h4>
        <span><a href="<?php echo getUrl('new', $myController->getUrls()) ?>"><?php icon('save', true); ?></a></span>
        <?php update_icon(getUrl('show', $myController->getUrls()));?>
        <hr>
        <table>
            <tr>
                <td>Nombre: </td>
                <td><input type="text" name="customer" id="customer" class="form-control"></td>
                <td><input type="button" class="btn btn-primary" value="Buscar" onclick="searchCustomer();"></td>
                <td><?php loader_icon(); ?></td>
            </tr>
        </table>
    </div>
    <div class="card-block">
        <table class="table table-responsive" id="dynamic-cus">

        </table>
    </div>
</div>
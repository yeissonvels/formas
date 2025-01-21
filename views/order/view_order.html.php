<div class="row">
    <div class="card col-lg-6">
        <div class="card-header">
            <h4 class="card-title"><?php echo "Datos de pedido"; icon('info', true); ?></h4>
        </div>

        <form action="" method="POST" onsubmit="return check_new_order();" id="principal">
            <div class="card-block">
                <div class="form-group row" id="div_code">
                    <label for="code"
                           class="col-sm-2 col-form-label">Código de entrega</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="code" id="code" placeholder=""
                               value="<?php echo($data['pdfinfo'] ? $data['pdfinfo']->code : ''); ?>" disabled="disabled">

                    </div>
                </div>
                <div class="form-group row">
                    <label for="customer" class="col-sm-2 col-form-label">Cliente</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="customer" id="customer" placeholder=""
                               value="<?php echo($data['data'] ? $data['data']->getCustomer() : ''); ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="telephone" class="col-sm-2 col-form-label">Teléfono</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="telephone" name="telephone" placeholder=""
                               value="<?php echo($data['data'] ? $data['data']->getTelephone() : ''); ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="telephone2" class="col-sm-2 col-form-label">Teléfono 2</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="telephone2" name="telephone2" placeholder=""
                               value="<?php echo($data['data'] ? $data['data']->getTelephone2() : ''); ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label"><?php echo trans('email') ?></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="email" name="email" placeholder=""
                               value="<?php echo($data['data'] ? $data['data']->getEmail() : ''); ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="store" class="col-sm-2 col-form-label">Tienda</label>
                    <div class="col-sm-10">
                        <input type="text" value="<?php echo getStoreName($data['pdfinfo']->storeid); ?>" disabled="disabled" class="form-control" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="purchasedate" class="col-sm-2 col-form-label">Fecha de compra</label>
                    <div class="col-sm-10">
                        <input type="text" name="purchasedate" id="purchasedate" class="form-control" value="<?php echo $data['data'] ? americaDate($data['data']->getPurchasedate(), false) : ''; ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliverydate" class="col-sm-2 col-form-label">Fecha de entrega</label>
                    <div class="col-sm-10">
                        <input type="text" name="deliverydate" id="deliverydate" class="form-control" value="<?php echo $data['data'] ? americaDate($data['data']->getDeliverydate(), false) : ''; ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliveryzone" class="col-sm-2 col-form-label">Zona de entrega</label>
                    <div class="col-sm-10">
                        <select name="deliveryzone" id="deliveryzone" class="form-control" disabled="disabled">
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
                        <input type="text" name="total" id="total" class="form-control" value="<?php echo $data['data'] ? $data['data']->getTotal() : 0; ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="pendingpay" class="col-sm-2 col-form-label">Pendiente de pago</label>
                    <div class="col-sm-10">
                        <input type="text" name="pendingpay" id="pendingpay" class="form-control" value="<?php echo $data['data'] ? $data['data']->getPendingpay() : 0; ?>" disabled="disabled">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="deliveryzone" class="col-sm-2 col-form-label">Forma de pago</label>
                    <div class="col-sm-10">
                        <select name="paymethod" id="paymethod" class="form-control" disabled="disabled">
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
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Estado</label>
                    <div class="col-sm-10">
                        <select name="status" id="status" class="form-control" disabled="disabled">
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
            </div>
            <div class="card-footer text-muted">
                <?php exit_btn(getUrl("show", $myController->getUrls())); ?>
            </div>
        </form>
    </div>

    <!-- Productos -->
    <div class="card col-lg-6">
        <div id="divfiles" style="margin-bottom: 10px;">
            <div class="card-header">
                <?php
                    $pdfUrl = PDF_DIR . $_GET['id'] . "/" . $data['pdfinfo']->pdfname;
                ?>
                <h4 class="card-title">PDF <a href="<?php echo $pdfUrl; ?>" target="_blank"><?php icon('pdf', true); ?></a></h4>
            </div>
        </div>
        <div id="products" style="<?php echo $data['data'] ? '' : 'display: none;' ?>">
            <div class="card-header">
                <h4 class="card-title">Productos <?php icon('cart', true); ?></h4>
            </div>
            <div class="card-block">
                <div id="dynamic-items">
                    <form id="form-products">
                        <table class="table" id="dynamic-table">
                            <?php
                                if ($data['data'] && $data['data']->getItems()) {
                                    foreach ($data['data']->getItems() as $item) {
                                        echo '<tr>';
                                        echo    '<td colspan="3">';
                                        echo        '<select class="form-control products" name="products[]" disabled="disabled">' . PHP_EOL;
                                        foreach ($data['products'] as $product) {
                                            $selected = "";
                                            if ($item->productid == $product->id) {
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option value="' . $product->id . '" ' . $selected . '>' . $product->productname . '</option>' . PHP_EOL;
                                        }
                                        echo        '</select>' . PHP_EOL;
                                        echo    '</td>' . PHP_EOL;
                                        echo '</tr>' . PHP_EOL;
                                        echo '<tr>';
                                        echo    '<td>';
                                        echo        'Fabricación ' . icon('calendar', false);
                                        echo    '</td>';
                                        echo    '<td>';
                                        echo        'Acabado' . icon('calendar', false);;
                                        echo    '</td>';
                                        echo    '<td>';
                                        echo       'Almacén' . icon('calendar', false);;
                                        echo    '</td>';
                                        echo '</tr>';
                                    }
                                }
                            ?>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card col-lg-6">
        <textarea class="form-control"></textarea>
    </div>
</div>
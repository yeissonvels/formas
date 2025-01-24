<!-- Productos -->
<div class="card col-lg-6">
    <div id="divfiles" style="margin-bottom: 10px;">
        <div class="card-header">
            <?php
                if (isset($_GET['pdfid'])) {
                    $pdfid = $_GET['pdfid'];
                } else if ($data['data']) {
                    $pdfid = $data['data']->getPdfid();
                }

                echo '<h4 class="card-title">Propuesta de pedido ';
                listDirectory("uploaded-files/pdfs/" . $pdfid, false);
                echo '</h4>';

                echo '<h4 class="card-title">Otros documentos ';
                listDirectory("uploaded-files/images/" . $pdfid . "/secondary/", false);
                echo '</h4>';
            ?>

            <div id="divfinish_deliverynote" style="<?php echo $data['data'] ? '' : 'display: none;' ?>">
                <h4 class="card-title">
                    Nota de entrega
                    <span id="lastdeliverynote">
                    <?php
                        if ($data['data'] && $data['data']->getFinishdeliveryfile() != "") {
                            $deliveryLabel = 'Modificar';
                            $fileUrl = DELIVERY_FILES_DIR . $data['data']->getId() .  "/" . $data['data']->getFinishdeliveryfile();
                    ?>
                            <a class="cursor-pointer" onclick="openUrlInWindow('<?php echo $fileUrl; ?>');" target="_blank">
                                <?php icon('word', true); ?>
                            </a>
                    <?php
                        } else {
                            $deliveryLabel = 'Subir';
                            icon('empty', true);
                        }
                    ?>
                    </span>
                </h4>

                <?php if (!$data['data'] || ($data['data'] && $data['data']->getStatus() < 2)) { ?>
                    <div class="form-group row">
                        <div class="col-sm-8">
                            <input type="file" id="file" class="form-control" multiple="multiple" onchange="addFile();"
                                   onclick="border_ok('#file')" <?php echo $disabled; ?>>
                        </div>
                        <div class="col-sm-2">
                            <input id="btn-deliveryfile" type="button" value="<?php echo $deliveryLabel; ?> nota de entrega" class="btn btn-primary" onclick="uploadPdf();" <?php echo $disabled; ?> style="font-size: 11px;">
                            <?php spinner_icon('spinner', 'sp-upload', true); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div id="products" style="<?php echo $data['data'] ? '' : 'display: none;' ?>">
        <div class="card-header">
            <h4 class="card-title">Items <?php icon('cart', true); ?></h4>
        </div>
        <div class="card-block">
            <?php
            $h4msg = "";
            if ($canEdit) {
                $h4msg = "Listado de productos. Pulse el botón (+) para agregar nuevos";
            }
            ?>
            <h4 id="h4"><?php echo $h4msg; ?></h4>
            <div id="dynamic-items">
                <form id="form-products">
                    <table class="table" id="dynamic-table">
                        <?php
                        // Si estamos modificando un pedido
                        if ($data['data'] && $data['data']->getItems()) {
                            $i  = 1;
                            foreach ($data['data']->getItems() as $item) {
                                echo '<tr id="product' . $i . '" class="table-success">';
                                echo    '<td>';
                                echo        '<select class="form-select products" name="products[]" id="select' . $i . '"' . $disabled . '>' . PHP_EOL;
                                echo            '<option value="">Seleccione un producto</option>' . PHP_EOL;
                                foreach ($data['products'] as $product) {
                                    $selected = "";
                                    if ($item->productid == $product->id) {
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $product->id . '" ' . $selected . '>' . $product->productname . '</option>' . PHP_EOL;
                                }
                                echo        '</select>' . PHP_EOL;
                                echo    '</td>' . PHP_EOL;
                                echo    '<td>';
                                echo        '<select class="form-select categories" name="categories[]" id="category' . $i . '" ' . $disabled . '>' . PHP_EOL;
                                echo            '<option value="">Seleccione una categoría</option>' . PHP_EOL;
                                foreach ($data['categories'] as $category) {
                                    $selected = "";
                                    if ($item->categoryid == $category->id) {
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $category->id . '" ' . $selected . '>' . $category->category . '</option>' . PHP_EOL;
                                }
                                echo        '</select>' . PHP_EOL;
                                echo    '</td>' . PHP_EOL;
                                if ($i == 1) {
                                    if ($canEdit) {
                                        echo '<td>';
                                        echo '<a style="cursor: pointer;" onclick="moreProducts(\'dynamic-table\')">' . icon('plus', false) . '</a>' . PHP_EOL;
                                        echo '</td>' . PHP_EOL;
                                    }
                                } else {
                                    if ($canEdit) {
                                        echo '<td>' . PHP_EOL;
                                        echo '<a onclick="deleteProduct(' . $i . ')" style="cursor: pointer;"><i class="fa fa-trash fa-fw"></i></a>' . PHP_EOL;
                                        echo '</td>' . PHP_EOL;
                                    }
                                }
                                echo '</tr>' . PHP_EOL;
                                echo '<tr id="productdate' . $i . '">' . PHP_EOL;
                                echo    '<td colspan="4">' . PHP_EOL;
                                echo        '<table class="table-striped">' . PHP_EOL;
                                echo            '<tr>' . PHP_EOL;
                                echo                '<td>Fabricación <input type="text" class="form-control" id="manufacturing' . $i . '" name="manufacturings[]"  value="' . americaDate($item->manufacturing_in, false) . '" ' . $disabled . '>' . PHP_EOL;
                                echo                '</td>' . PHP_EOL;
                                echo                '<td>Acabado <input type="text" class="form-control" id="finish' . $i . '" name="finishes[]" value="' . americaDate($item->finish_in, false) . '" ' . $disabled . '></td>' . PHP_EOL;
                                echo                '<td>Almacén <input type="text" class="form-control" id="store' . $i . '" name="stores[]"  value="' . americaDate($item->store_in, false) . '" ' . $disabled . '></td>' . PHP_EOL;
                                echo            '</tr>' . PHP_EOL;
                                echo         '</table>' . PHP_EOL;
                                echo    '</td>' . PHP_EOL;
                                echo '</tr>' . PHP_EOL;
                                datePicker(
                                    array('manufacturing' . $i, 'finish' . $i, 'store' . $i), false);
                                $i++;
                            }
                        } else {
                            // Nuevo pedido
                            ?>
                            <tr class="table-success">
                                <td>
                                    <select class="form-select products" name="products[]" id="select1" <?php echo $disabled; ?>>
                                        <option value="">Seleccione un producto</option>
                                        <?php
                                        foreach ($data['products'] as $product) {
                                            echo '<option value="' . $product->id . '">' . $product->productname . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select categories" name="categories[]" id="categorie1" <?php echo $disabled; ?>>
                                        <option value="">Seleccione una categoria</option>
                                        <?php
                                        foreach ($data['categories'] as $category) {
                                            echo '<option value="' . $category->id . '">' . $category->category . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <a style="cursor: pointer;" onclick="moreProducts('dynamic-table')"><?php icon('plus', true); ?></a>
                                </td>
                            </tr>
                            <tr id="productdate1">
                                <td colspan="4">
                                    <table class="table-striped">
                                        <tr>
                                            <td>
                                                Fabricación <input type="text" class="form-control" id="manufacturing1" name="manufacturings[]" <?php echo $disabled; ?>>
                                            </td>
                                            <td>
                                                Acabado <input type="text" class="form-control" id="finish1" name="finishes[]" <?php echo $disabled; ?>>
                                            </td>
                                            <td>
                                                Almacén <input type="text" class="form-control" id="store1" name="stores[]" <?php echo $disabled; ?>>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <?php
                            datePicker(array('manufacturing1', 'finish1', 'store1'), false);
                        }
                        ?>

                    </table>
                    <div class="container">
                        <?php if ($canEdit) { ?>
                            <div class="row">
                                <div class="col-lg-12" style="text-align: right;">
                                    <a onclick="moreProducts('dynamic-table')" style="cursor: pointer;"><?php icon('plus', true); ?></a>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <input type="hidden" name="id" value="<?php echo $data['data'] ? $data['data']->getId() : '' ; ?>" id="orderid">
                            <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                            <input type="hidden" name="opt" value="save_products" id="opt_products">
                            <input type="hidden" name="pdfid" value="<?php echo $pdfid; ?>">
                            <div class="col-sm-4">
                                <?php if ($canEdit) { ?>
                                    <input id="btn-save-products" type="button" class="btn btn-primary" onclick="saveProducts();"
                                           value="<?php echo $data['data'] ? 'Modificar productos' : 'Guardar productos'; ?>">
                                <?php } ?>
                            </div>
                            <div class="col-sm-2">
                                <?php echo spinner_icon('refresh', 'sp-products'); ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
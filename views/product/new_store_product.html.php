<script>
    function check_new_product() {
        comprobate = Array('#productname', '#finishid');

        // Devuelve true si todos los campos han sido completados
        if (checkNoEmpty(comprobate)) {
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }
</script>

<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?php echo $data ? "Modificar datos" : "Nuevo producto" ?></h4>
    </div>
    <form action="" method="POST" onsubmit="return check_new_product();">
        <div class="card-block">
        	<div class="form-group">
                <label for="productname">Referencia</label>
                <input type="text" class="form-control" name="reference" id="reference"
                       placeholder=""
                       value="<?php echo($data ? $data->reference : ""); ?>">
            </div>
            
            <div class="form-group">
                <label for="productname">Nombre</label>
                <input type="text" class="form-control" name="productname" id="productname"
                       placeholder=""
                       value="<?php echo($data ? $data->productname : ""); ?>">
            </div>
            
            <div class="form-group">
                <label for="productname">Precio</label>
                <input type="text" class="form-control" name="price" id="price"
                       placeholder=""
                       value="<?php echo($data ? $data->price : ""); ?>">
            </div>
            
            <div>
            	<div class="form-group">
	                <label for="productname">Acabado</label>
	                <select class="form-control" name="finishid" id="finishid">
	                	<option value="">Acabado</option>
	                	<?php
	                		$finishes = $myController->getJsonFinishes();
							
	                		foreach ($finishes as $finish) {
								$selected = "";
								if ($data && $data->finishid == $finish->id) {
									$selected = 'selected="selected"';
								}
								echo '<option value="' . $finish->id . '" ' . $selected . '>' . $finish->finishname . ' : ' . $finish->description . '</option>';
							}
	                	?>
	                </select>    
            	</div>
            </div>

            <div class="form-group">
                <label for="active">Activo</label>
                <select class="form-control" name="active">
                    <option value="1" <?php echo($data && $data->active == 1 ? 'selected="selected"' : ''); ?>>Si</option>
                    <option value="0" <?php echo($data && $data->active == 0 ? 'selected="selected"' : ''); ?>>No</option>
                </select>
            </div>

            <div class="form-group">
                <input type="hidden" name="opt" id="opt"
                       value="<?php echo $data ? 'save_edit_store_product' : 'save_store_product' ?>">
                <input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
                <input type="hidden" name="show" value="show_store_products">
                <?php
                if ($data) {
                    echo '<input type="hidden" name="id" value="' . $data->id . '">';
                }
                ?>
            </div>
        </div>
        <div class="card-footer text-muted">
            <?php
            save_update_btn($data);
            exit_btn(getUrl('show_store_products', $myController->getUrls()));
            ?>
        </div>
    </form>
</div>

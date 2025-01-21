<script>
    function confirmDelete() {
		if ($('.ckdel:checked').length > 0) {
			if (confirm('¿Confirmas que deseas eliminar las ventas seleccionadas?\n\n¡Este proceso no se puede deshacer!')) {
				$('#frmdelete').submit();
			}
		} else {
			alert('Selecciona al menos una venta');
		}
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
        <h4 class="card-title">Eliminar ventas</h4>
        <form action="<?php echo getUrl('show_pdfs', $this->getUrls()); ?>" method="post" id="frm1">
            <div class="form-group row" style="display: none;">
                <label for="purchasedate" class="col-sm-1 col-form-label">Mes <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php
                    	if (userWithPrivileges()) {
                    		generateSelectMonth("", false); 
                    	} else {
                    		generateSelectMonth("", false, '', date('m')); 
                    	}
                    	
                	?>
                </div>
                <?php if (userWithPrivileges()){ ?>
                <label for="year" class="col-sm-1 col-form-label">Año <?php icon('calendar', true);?></label>
                <div class="col-sm-2">
                    <?php generateSelectYear(2017, "", false); ?>
                </div>
                <?php } ?>

                <div class="col-sm-2">
                    <input type="hidden" name="op" value="searchSales">
                    <input type="button" value="Buscar" class="btn btn-primary" onclick="searchSales();" id="btn-search-sales">
                    <?php spinner_icon('spinner', 'sp_sales', true); ?>
                </div>

                <div class="col-sm-2">
                    <input type="button" value="Quitar filtros" onclick="removeFilters();" class="btn btn-warning">
                </div>
            </div>

            
			<div class="form-group row" style="display: none;">
				<label for="from" class="col-sm-1 col-form-label">Nº de pedido <?php icon('barcode', true); ?></label>
				<div class="col-sm-4">
					<input type="text" id="search-box" class="form-control" placeholder="Código o nombre del cliente">
					<input type="hidden" name="code" id="code" class="form-control" value="">
					<div id="suggesstion-box" style="position: absolute; z-index: 10000;"></div>
				</div>
				<?php if (userWithPrivileges()) { ?>
					<label for="commission" class="col-sm-1 col-form-label">Sólo con propuesta <?php icon('money', true); ?></label>
					<div class="col-sm-1">
						<select name="commission" class="form-control">
							<option value="no">No</option>
							<option value="yes">Si</option>
						</select>
					</div>
				<?php } ?>
			</div>
        </form>
    </div>

    <div class="card-block">
		<form id="frmdelete" method="post">
			<table class="table table-responsive" id="dynamic-cus">
				<thead>
					<tr>
						<th>Código</th>
						<th>Total</th>
						<th>Fecha</th>
						<th>Seleccionar</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="4">
							<input type="button" class="btn btn-danger" value="Borrar los seleccionados" onclick="confirmDelete();">
						</td>
					</tr>
					<?php
						foreach($orders as $order) { ?>
							<tr>
								<td><?php echo $order->code; ?></td>
								<td><?php echo numberFormat($order->total, true, 2); ?> € </td>
								<td><?php echo americaDate($order->created_on); ?></td>
								<td align="center">
									<input type="checkbox" name="orders[]" class="ckdel" value="<?php echo $order->id; ?>">
								</td>
							</tr>
						<?php
						}
					?>
					<tr>
						<td>
							<input type="hidden" name="controller" value="<?php echo FORM_CONTROLLER; ?>">
							<input type="hidden" name="opt" value="deleteOrderByAdmin">
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<input type="button" class="btn btn-danger" value="Borrar los seleccionados" onclick="confirmDelete();">
						</td>
					</tr>
					<tr>
						<td colspan="4"><b>Total registros: <?php echo count($orders); ?></b></td>
					</tr>
				</tbody>
			</table>
		</form>
    </div>
</div>
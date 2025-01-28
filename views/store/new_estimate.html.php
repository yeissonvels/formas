<?php
    datePicker(array('saledate', 'saleuntil'));
    global $user;
    $server = $_SERVER['SERVER_NAME'];
    $id = $data ? $data->id : '';
    $code = $data ? $data->code : $myController->getEstimateCode($user);
    $urlpdf = $myController->getEstimatePdfUrl($id, $code);

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
	$( function() {
		$( "#sortable" ).sortable();
		console.log("Sortable");
	});
</script>

<script>
    var id = "<?php echo $id; ?>";
    var code = "<?php echo $code; ?>";
    var calculate = <?php  echo $data && count($data->products) > 0 ? 'true' : 'false' ?>;
    var comprobate = [];
    var userWithPrivileges = <?php echo userWithPrivileges() ? 'true' : 'false'; ?>;

    $(document).ready(function(){
        if (calculate) {
            calculateTotal();
        }
        createComprobate();
        checkIfAnotherEstimateRegisteredWithTel();
    });

    function checkEstimateData() {
        saveEstimate();
    }

    function checkIfAnotherEstimateRegisteredWithTel() {
        $('#tel').keyup(function (event) {
            setTelValidations(event);
        });

        $('#tel2').keyup(function (event) {
            setTelValidations(event);
        });
    }

    function setTelValidations(event) {
        let inputId = event.target.id;
        let inputSelector = `#${inputId}`;
        resetValidations(inputSelector);
        validateTel(inputId, $(inputSelector).val());
    }

    function validateTel(selector, telValue) {
        if (telValue.length > 4) {
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: {
                    tel: telValue,
                    op: 'checkIfAnotherEstimateRegisteredWithTel',
                    controller: 'StoreController'
                }
            }).done(function (data) {
                // Retorna "si" 0 "no" dependeiendo si el usuario existe
                if (data == "si") {
                    $('#btn-save-sale').prop('disabled', true);
                    addError(`#div_${selector}`, `#${selector}`);
                    $('#modalAlert').modal('show');
                } else {
                    addSuccess(`#div_${selector}`, `#${selector}`);
                    $('#btn-save-sale').prop('disabled', false);
                    border_ok(`#${selector}`);
                }
            });
        }
    }

    function saveEstimate() {
        comprobate = Array('#saledate', '#customer', '#tel', '#total', '#estimateorigin');

        if (checkNoEmpty(comprobate)) {
            $('#sp-save-sale').show();
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: $('#frm-saledata').serialize(),
            }).done(function (data) {
                $('#sp-save-sale').hide();
                var res = JSON.parse(data);
                var salelabel = 'presupuesto';

                if (res['saved'] == 1) {
                    id = res['lastid'];
                    alert('Datos del ' + salelabel + ' registrados.');
                    $('#btn-save-sale').prop('value', 'Modificar presupuesto');
                    $('#opt-save-sale').prop('value', 'updateEstimate');
                    $('#id').prop('value', id);
                    $('#estimateId').prop('value', id);
                    //$('#div_img').show(); // Las imágenes se cargan ahora en la sección de documentos adicionales
                    $('#div_img2').show();
                    if ($('#saletype').val() == 0) {
                        $('#div_pdf').show();
                    }
                    updatePdfUrl(res['pdfurl']);
                    scrollingTo('#div_img2');
                    $('#products').show();
                    $('#commentsdiv').show();

                } else if (res['codeduplicated']) {
                    alert('Error al crear la venta. El código de pedido ya existe en otra venta!');
                } else if (res['updated'] == 1) {
                    updatePdfUrl(res['pdfurl']);
                    alert('Datos de ' + salelabel + ' actualizados correctamente!');
                } else if (res['duplicated'] == 1) {
                    alert('Nada para actualizar!');
                }
            });
        } else {
            alert(completeRequiredFields);
        }
    }

    function updatePdfUrl(url) {
        $('#pdf').prop('href', url);
        $('#urltitlepdf').prop('href', url);
    }

    var reuploaded = <?php echo($data && (isset($data->reuploaded) && $data->reuploaded == 1) ? 'true' : 'false'); ?>;
    var arrayImages = [];

    function addImage(imageType) {
        var image = "";
        var imageId = "";

        if (imageType == "primary") {
            image = $('#image').val();
            imageId = '#image';
        } else {
            image = $('#imag2').val();
            imageId = '#image2';
        }

        if (image != "") {
            jQuery.each(jQuery(imageId)[0].files, function (i, file) {
                arrayImages.push(file);
            });
        } else {
            alert('Seleccione un archivo!');
        }
    }

    /**
     * Sube varios archivos al servidor
     */
    function uploadImage(imageType) {
        if (arrayImages.length > 0) {
            var data = new FormData();

            if (imageType == "primary") {
                var divresponse = 'ajax-image-content';
                $('#sp-upload-image').show();
                $('#' + divresponse).html('');
            } else {
                var divresponse = 'ajax-extra-images-content';
                $('#sp-upload-image2').show();
                $('#' + divresponse).html('');
            }

            jQuery.each(arrayImages, function (i, file) {
                data.append('file-' + i, file);
            });

            data.append('op', 'uploadEstimateDocument');
            data.append('orderid', id);
            data.append('imageType', imageType);
            data.append('divresponse', divresponse);

            $.ajax({
                url: 'ajax.php',
                type: 'post',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
            }).done(function (response) {
                if (imageType == "primary") {
                    var res = JSON.parse(response);
                    // Sólo si el resultado es correcto
                    if (res['success'] == 1) {
                        $('#div_pdf').show();
                    }
                }

                if (imageType == "primary") {
                    $('#sp-upload-image').hide();
                    $('#image').prop("value", "");
                    $('#ajax-image-content').html(res['html']);
                } else {
                    $('#sp-upload-image2').hide();
                    $('#image2').prop("value", "");
                    $('#ajax-extra-images-content').html(response);
                    reloadQtip();
                }
                // limpiamos el array de archivos
                arrayImages = [];

            });
        } else {
            alert('Seleccione una imagen!');
            if (imageType == "primary") {
                border_error('#image');
            } else {
                border_error('#image2');
            }
        }
    }

    var arrayfiles = [];

    function addFile() {
        if ($('#file').val() != "") {
            jQuery.each(jQuery('#file')[0].files, function (i, file) {
                //data.append('file-'+i, file);
                arrayfiles.push(file);
            });
            //listFiles();
        } else {
            alert('Seleccione un archivo!');
        }
    }

    function listFiles() {
        var htmlpdf = '';
        for (i = 0; i < arrayfiles.length; i++) {
            htmlpdf += '<a href="#" onclick="deleteFile(' + i + ')">' + arrayfiles[i]['name'] + ' <img src="images/icons/Trash.png"></a><br>';
        }
        $('#ajax-content').html(htmlpdf);
        htmlpdf = "";
    }

    /**
     * Sube varios archivos al servidor
     */
    function uploadPdf() {
        if (arrayfiles.length > 0) {
            var data = new FormData();

            $('#sp-upload').show();
            $('#ajax-content').html('');

            jQuery.each(arrayfiles, function (i, file) {
                data.append('file-' + i, file);
            });

            data.append('op', 'uploadEstimatePdf');
            data.append('orderid', id);
            //data.append('comment', comment);
            $.ajax({
                url: 'ajax.php',
                type: 'post',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
            }).done(function (response) {
                if (reuploaded) {
                    alert('El nuevo PDF se ha cargado correctamente!');
                }
                $('#sp-upload').hide();
                $('#ajax-content').html(response);
                //var dirPath = 'uploaded-files/order/35';
                // limpiamos el array de archivos
                arrayfiles = [];
                $('#file').prop('value', '');
                // recargamos los archivos mostrados
                //listDir(dirPath);
            });
        } else {
            alert('Seleccione un archivo!');
            border_error('#file');
        }
    }

    function confirmSend(id) {
        if (confirm('Desea enviar el PDF al almacén?')) {
            window.location.href = '<?php echo getUrl('send_pdf', $myController->getUrls());?>' + id;
        }
    }

    function unlinkFile(file, path, fname, divresponse, toHide) {
        if (confirm('¿Deseas eliminar el archivo seleccionado? Ten en cuenta que no se podrá recuperar.')) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    file: file,
                    path: path,
                    fname: fname,
                    divresponse: divresponse,
                    op: 'unlinkFile',
                }
            }).done(function(Response) {
                var res = JSON.parse(Response);
                if (res['response'] == "Ok") {
                    $('#' + toHide).hide();
                    alert("Archivo eliminado correctamente!");
                }
                reloadQtip();
            });
        }

    }

    function saveComment() {
        var pdfid = id;
        var comment = $('#pdfcomment').val();
        comprobate = Array('#pdfcomment');

        if (checkNoEmpty(comprobate)) {
            $('#sp-cus-comment').show();
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    estimateid: id,
                    comment: comment,
                    op: 'saveEstimateComment'
                }
            }).done(function(Response) {
                $('#pdfcomment').prop('value', '');
                $('#sp-cus-comment').hide();
                $('#dynamic-cus-comments').html(Response);
            });
        } else {
            alert(completeRequiredFields);
        }
    }

    function createComprobate() {
        comprobate = [];
        for (i = 1; i < products; i++) {
            var desc = '#desc' + i;
            var quantity = '#quantity' + i;
            var price = '#price' + i;
            var dto = '#dto' + i;

            if (typeof $('#desc' + i).val() !== "undefined") {
                comprobate.push(desc, quantity, price, dto);
            }
        }
    }

	function saveProducts() {
        createComprobate();
        if (checkNoEmpty(comprobate)) {
            $.ajax({
                url: "/ajax.php",
                type: 'post',
                data: $('#frm-prod').serialize()
            }).done(function(response) {
                var res = JSON.parse(response);
                if (res['saved'] == 1) {
                    alert("Productos guardados correctamente!");
                } else {
                    alert("Error al guardar los productos!");
                }
            });
        } else {
            alert(completeRequiredFields);
        }
	}
	
	<?php
		$countProducts = 1;
	
	    if ($data && count($data->products) > 0) {
	        $countProducts = count($data->products);
	    }
    ?>

    var products = <?php echo $countProducts + 1; ?>;
    
    function moreProducts(target) {
        var html = '<tr id="product' + products + '" class="ui-state-default">';
        	html +=     '<td class="text-center">';
        	html +=     '<?php echo icon('sort'); ?>';
        	html +=     '</td>';
			html += 	'<td>';
			html += 	'	<textarea id="desc' + products + '" name="product[]" class="form-control" style="min-width: 500px"></textarea>';
			html += 	'	<div id="auto' + products + '"></div>';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="quantity' + products + '" name="quantity[]" class="form-control input-xxsmall text-center quantity" onkeyup="calculatePrice(' + products + ');$(this).prop(\'value\', validar(this.value));">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="price' + products + '" name="price[]" class="form-control input-xsmall text-center price" onkeyup="calculatePrice(' + products + ');$(this).prop(\'value\', validar(this.value));">';
			html += 	'   <input type="hidden" id="hideprice' + products + '" class="hideprice">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="dto' + products + '" name="dto[]" class="form-control input-xsmall text-center dto" onkeyup="calculatePrice(' + products + ');$(this).prop(\'value\', validar(this.value));" value="0">';
			html += 	'   <input type="hidden" id="hidedto' + products + '" class="hidedto">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="subtotal' + products + '" disabled="disabled" class="form-control input-xsmall subtotal">';
			html += 	'   <input type="hidden" id="hidesubtotal' + products + '">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<a style="cursor: pointer;" onclick="deleteProduct(' + products + ')"><?php icon('delete', true); ?></a>';
			html += 	'</td>';
			html += 	'</tr>';
      
        
        $('#' + target).append(html);
        $( function() {
			$( "#sortable" ).sortable();
			console.log("Sortable");
		});
        
        scrollingTo('#product' + products);
        products++;
        //createComprobate();
    }

    function deleteProduct(id) {
        $('#product' + id).remove();

        // Al borrar se devuelve el array modificado y debemos volver a obtener la posición
        var desc = comprobate.indexOf('#desc' + id);
        comprobate.splice(desc, 1);
        var quantity = comprobate.indexOf('#quantity' + id);
        comprobate.splice(quantity, 1);
        var dto = comprobate.indexOf('#dto' + id);
        comprobate.splice(dto, 1);
        comprobate = [];

        calculateTotal();
    }
    
    
    function calculatePrice(id) {
		var quantity = parseInt($('#quantity' + id).val());
		var price = parseFloat($('#price' + id).val());
		var result = price * quantity;
		var dto = parseFloat($('#dto' + id).val());
		// Seteamos el precio
		$('#hideprice' + id).prop("value", result);
		// Seteamos el subtotal teniendo en cuenta el descuento (si existe)
        console.log(result);
		if (!isNaN(dto)) {
			var auxDto = (result / 100) * dto;
			var difference = result - auxDto;
			$('#subtotal' + id).prop("value", Number(difference).toFixed(2));
			$('#hidedto' + id).prop("value", Number(auxDto).toFixed(2));
		} else {
			$('#subtotal' + id).prop("value", Number(result).toFixed(2));
		}
		
		calculateTotal();
    }
    
    function calculateTotal() {
    	console.log("Calculamos totales");
    	var prices = $('.hideprice');
    	var dtos = $('.hidedto');
    	var totDto = 0;
    	var totPrices = 0;
    	
    	dtos.each(function() {
    		var dto = this.value;
    		if (!isNaN(dto)) {
    			totDto += parseFloat(this.value);
    		} else {
    			totDto += 0;
    		}
    		
    	});
    	
    	
    	$('#dto').prop("value", Number(totDto).toFixed(2));
    	
    	
    	prices.each(function() {
    		var pr = this.value;
    		if (!isNaN(pr)) {
    			totPrices += parseFloat(this.value);
    		} else {
    			totPrices += 0;
    		}
    	});
    	
    	$('#subtotal').prop("value", Number(totPrices).toFixed(2));
    	
    	
    	var subtotales = $('.subtotal');
    	var totSub = 0;
    	subtotales.each(function() {
    		var st = this.value;
    		if (!isNaN(st)) {
    			totSub += parseFloat(st);
    		}
    	});
    	
    	$('#base').prop("value", Number(totSub).toFixed(2));
    	
    	var iva = parseFloat((totSub / 100) * <?php echo IVA; ?>);
    	iva = Number(iva).toFixed(2);
    	console.log( <?php echo IVA; ?>);
    	$('#iva').prop("value", iva);
    	
    	var lasttotal = parseFloat(totSub) + parseFloat(iva);
    	$('#lasttotal').prop("value", Number(lasttotal).toFixed(2));
        $('#total').prop("value", Number(lasttotal).toFixed(2));
    }

    function getManufacturerProducts() {
        if (id > 0) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: {
                    id: id,
                    opt: 'getManufacturerProducts'
                }
            }).done(function(response) {
                $('#dynamic-manufacturer').html(response);
            });
        }
    }

    function checkManufacturerInfo() {
        comprobate = [];

        if (checkNoEmpty(comprobate)) {
            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: $('#frm-manufacturer').serialize()
            }).done(function(response) {
                alert(response);
            });
        } else {
            alert(completeRequiredFields);
        }
    }

    /*var cont = < ?php echo $data && count(unserialize($data->concept)) > 0 ? count(unserialize($data->concept)) : 0 ?>;

    function addNewConcept() {
        cont++;
        var divId = 'div_concept_' + cont;
        var html = '<div class="form-group row" id="' + divId + '">';
        html += '<label for="concept" class="col-sm-2 col-form-label">Concepto ' + cont + '</label>';
        html +=     '<div class="col-sm-9">';
        html +=         '<textarea class="form-control" name="concept[]" id="concept' + cont + '"></textarea>';
        html +=     '</div>';
        html += '<a class="cursor-pointer red-color" onclick="deleteConcept(' + cont + ')">< ?php echo icon("delete", false); ?></a>';
        html +=  '</div>';
        $('#concepts').append(html);
        scrollingTo('#' + divId);
    }

    function deleteConcept(id) {
        $('#div_concept_' + id).remove();
    }*/

</script>

<?php
    $disabled = 'disabled="disabled"';
    $defaultLabel = 'presupuesto';

    $canEdit = false;
    if (!$data) {
        $canEdit = true;
        $disabled = '';
    } else if ($data && !isTimeOver($data->created_on) || $user->getUsermanager() == 1
        || isAdmin() || $data->status == 0 ) {
        $canEdit = true;
        $disabled = '';
    }
?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <span id="dynamictitle">
            	<?php echo $data ? "Modificar $defaultLabel" : "Nuevo $defaultLabel" ?> <?php icon('estimate', true); ?>
        	</span>
        	<?php
            if ($data) {?>
                <!-- <a href="< ?php echo $urlpdf; ?>" id="urltitlepdf" target="_blank" style="font-size: 20px;">< ?php icon('pdf', true); ?></a> -->
            <?php
            }
            ?>
        </h4>

    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-sm-6">
                <form id="frm-saledata">
                    <hr>
                    <h4 style="color: gray;">Datos del presupuesto</h4>
                    <div class="form-group row" id="div_saledate">
                        <label for="saledate"
                               class="col-sm-2 col-form-label">Fecha del presupuesto</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="saledate" id="saledate"
                                   value="<?php echo $data ? americaDate($data->saledate, false) : ''; ?>" <?php echo $disabled; ?> autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row" id="div_number">
                        <label for="code"
                               class="col-sm-2 col-form-label">Número de presupuesto</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control bg-warning text-white" name="code" id="code"
                                   value="<?php echo $code; ?>" autocomplete="off" disabled>
                        </div>
                    </div>

                    <div id="concepts" style="display: none;">
                        <hr>
                        <a onclick="addNewConcept();" class="cursor-pointer btn btn-warning text-white" >(+) añadir concepto</a>
                        <hr>
                        <?php if (!$data) { ?>
                            <div class="form-group row" id="div_concept">
                                <label for="concept"
                                       class="col-sm-2 col-form-label">Concepto</label>
                                <div class="col-sm-9">
                                    <!--<textarea class="form-control" name="concept[]" id="concept"></textarea>-->
                                </div>
                            </div>
                        <?php } else {
                            $i = 1;
                            if(isset($data->concept)) {
                                $data->concept = unserialize($data->concept);
                                foreach ($data->concept as $concept) {
                                ?>

                                    <div class="form-group row" id="div_concept_<?php echo $i; ?>">
                                        <label for="concept"
                                            class="col-sm-2 col-form-label">Concepto <?php echo ($i); ?></label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" name="concept[]" <?php echo $disabled; ?>><?php echo $concept; ?></textarea>
                                        </div>
                                        <?php if ($i > 1) { ?>
                                            <a class="cursor-pointer red-color" onclick="deleteConcept(<?php echo $i; ?>)"><?php icon('delete', true); ?></a>
                                        <?php } ?>
                                    </div>
                                <?php
                                    $i++;
                                }
                            }
                        }
                        ?>
                    </div>
                    <hr>
                    <h4 style="color: gray;">Datos cliente</h4>
                    <div class="form-group row" id="div_customer">
                        <label for="customer"
                               class="col-sm-2 col-form-label">Titular del presupuesto</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="customer" id="customer"
                                   value="<?php echo $data ? $data->customer : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
  
                    <div class="form-group row" id="div_tel">
                        <label for="tel"
                               class="col-sm-2 col-form-label">Teléfono</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tel" id="tel" 
                            	onkeyup="allowOnlyNumbers(event);" value="<?php echo $data ? $data->tel : ''; ?>" <?php echo $disabled; ?> >
                            <div class="invalid-feedback">
                                Telefóno encontrado en otro prespuesto.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="div_tel2">
                        <label for="tel2"
                               class="col-sm-2 col-form-label">Teléfono 2 (opcional)</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tel2" id="tel2" 
                            	onkeyup="allowOnlyNumbers(event);" value="<?php echo $data ? $data->tel2 : ''; ?>" <?php echo $disabled; ?> >
                            <div class="invalid-feedback">
                                Telefóno encontrado en otro prespuesto.
                            </div>
                        </div>
                    </div>
                   
                    <hr>
                    <div class="form-group row" id="div_total">
                        <label for="total"
                               class="col-sm-2 col-form-label"><span id="totallabel">Importe del presupuesto</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="total" id="total" onkeyup="addCommas($(this).prop('id'), $(this).val());"
                                   value="<?php echo $data ? numberFormat($data->total, true, 2) : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="deliveryrange" class="col-sm-2 col-form-label">Origen del presupuesto</label>
                        <div class="col-sm-10">
                            <select name="estimateorigin" id="estimateorigin" class="form-select" <?php echo $disabled; ?>>
                                <option value="">Seleccione un origen</option>
                                <?php
                                global $estimateOrigins;
                                foreach ($estimateOrigins as $key => $value) {
                                    $selected = "";
                                    if ($data && $data->estimateorigin == $key) {
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!--<div class="form-group row" style="display: none;">
                        <label for="paymethod"
                               class="col-sm-2 col-form-label">Observaciones</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="salecomment" name="salecomment" < ?php echo $disabled; ?>>< ?php echo $data ? $data->salecomment : ""; ?></textarea>
                        </div>
                    </div> -->

                    <div class="form-group row">
                        <?php if ($canEdit) {
                            $btnLabel = 'presupuesto';
                            ?>
                            <div class="col-sm-4">
                                <input type="hidden" name="id" id="id" value="<?php echo $data ? $data->id : ''; ?>">
                                <input type="hidden" name="code" id="code" value="<?php echo $code; ?>">
								<input type="hidden" name="opt" value="<?php echo $data ? 'updateEstimate' : 'saveEstimate'; ?>"
                                       id="opt-save-sale">
                                <input type="button" class="btn btn-primary" id="btn-save-sale"
                                       value="<?php echo($data ? 'Modificar ' : 'Guardar ') . $btnLabel ?>" onclick="checkEstimateData();">
                                <?php spinner_icon('spinner', 'sp-save-sale', true); ?>
                            </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
            <!-- Columna derecha -->
            <?php
                $commentsDisplay = $data ? 'block' : 'none';
				//$commentsDisplay = "";
            ?>
            <div class="col-sm-6">
                <div class="modal-body" style="display: <?php echo $commentsDisplay?>;" id="commentsdiv">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12" style="max-height: 200px; overflow: auto;">
                                <table class="table table-striped" id="dynamic-cus-comments">
                                    <?php
                                    include (VIEWS_PATH_CONTROLLER . 'pdf_comments' . VIEW_EXT);
                                    ?>
                                </table>
                            </div>
                            <div class="col-lg-12">
                                <textarea class="form-control" id="pdfcomment" <?php echo $disabled; ?>></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <?php if ($canEdit) { ?>
                            <button class="btn btn-primary" type="button" onclick="saveComment(1);">Nuevo comentario</button>
                            <?php spinner_icon('spinner', 'sp-cus-comment', true); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
            /*$estimateOrSaleIdName = "estimateId";
            $optValue = "save_estimate_products";
            include(VIEWS_PATH_CONTROLLER . 'estimate_and_sale_products' . VIEW_EXT);*/
        ?>
        
        <div id="div_img" style="display: none;">
            <hr>
            <div class="form-group row">
                <label for="image"
                       class="col-sm-1 col-form-label">Imagen de venta<?php icon('image', true); ?></label>
                <div class="col-sm-8">
                    <input type="file" id="image" class="form-control" multiple="multiple" onchange="addImage('primary');"
                           onclick="border_ok('#image')" <?php echo $disabled; ?>>
                </div>
                <?php if ($canEdit) { ?>
                    <div class="col-sm-2">
                        <input type="button" value="Subir imagen" class="btn btn-primary" onclick="uploadImage('primary');">
                        <?php spinner_icon('spinner', 'sp-upload-image', true); ?>
                    </div>
                <?php } ?>
            </div>

            <div id="ajax-image-content">
                <?php
                if ($data) {
                    if (isset($data->image) && $data->image != "") {
                        $url = "/uploaded-files/images/" . $data->id . "/" . $data->image;
                        confirmationMessage('Imágen actual <a href="' . $url . '" target="_blank">' . icon('image', false) . '</a>');
                    } else {
                        errorMsg('No se ha cargado ninguna imagen');
                    }
                }
                ?>
            </div>
        </div>

        <?php
            $displayDocuments = 'display: none;';
            if ($data) {
                $displayDocuments = '';
            }
        ?>


        <div id="div_img2" style="<?php echo $displayDocuments; ?>">
            <hr>
            <div class="form-group row">
                <label for="image"
                       class="col-sm-1 col-form-label">Imágenes o documentos<?php icon('image', true) . "" . icon('pdf', true) .  "" . icon('word', true); ?></label>
                <div class="col-sm-8">
                    <input type="file" id="image2" class="form-control" multiple="multiple" onchange="addImage('secondary');"
                           onclick="border_ok('#image2')">
                </div>

                <div class="col-sm-2">
                    <input type="button" value="Subir imagen o documento" class="btn btn-primary" onclick="uploadImage('secondary');">
                    <?php spinner_icon('spinner', 'sp-upload-image2', true); ?>
                </div>
            </div>

            <div id="ajax-extra-images-content">
                <?php
                    if ($data) {
                        $config = array(
                            'divresponse' => 'ajax-extra-images-content',
                            'excludes' => array()
                        );

                        listDirectoryTableFormat('uploaded-files/estimates/' . $data->id . '/secondary', true, $config);
                    }
                ?>
            </div>
        </div>

        <?php
            $pdfDisabled = 'disabled="disabled"';
            if ($data) {
                $pdfDisabled = "";
            } else if ($user->getUsermanager() == 1) {
                $pdfDisabled = "";
            }  else if (!$data) {
                $pdfDisabled = "";
            }

            $displayPdf = 'display: none;';
            if ($data) {
                $displayPdf = '';
            }

            // Ocultamos por ahora
            $displayPdf = "display: none";
        ?>

        <div class="card-footer text-muted">
            <?php
                exit_btn(getUrl('show_estimates', $myController->getUrls()));
            ?>
        </div>
    </div>
</div>

<!-- Modal de alerta para presupuestos encontrados con un mismo número de teléfono que no han finalizado en venta -->
<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade" id="modalAlert">
    <div role="document" class="modal-dialog">
        <form id="totalValidationForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLiveLabel" class="modal-title red-color">Atención <?php icon('attention2', true); ?></h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="total_checked_note" class="col-12 col-form-label">
                                    <?php
                                        $msg = "<h6>Este número de teléfono ya está asociado a otro presupuesto pendiente de conversión a venta.</h6>";
                                        warningMsg($msg, true); 
                                    ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bs-dismiss="modal" class="btn btn-secondary" type="button">Salir</button>
                </div>
            </div>
        </form>
    </div>
</div>
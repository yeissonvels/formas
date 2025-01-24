<?php
    datePicker('saledate');
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<!--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->

<script>
	$( function() {
		$( "#sortable" ).sortable();
		console.log("Sortable");
	});
</script>

<script>
    var id = "<?php echo $data ? $data->id : ''; ?>";
    var codeValidated = <?php  echo $data && $data->code ? 'true' : 'false' ?>;

    $(document).ready(function(){
        $("#search-box").keyup(function(){
            $('#parentcode').prop("value", "");
            if ($(this).val() != "") {
                $.ajax({
                    type: "POST",
                    url: "/ajax.php",
                    data: {
                        keyword: $(this).val(),
                        nocancelled: 'yes',
                        op: 'getAutocompleteCode',
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
                $("#suggesstion-box").html("");
            }
        });
    });

    function checkSaleData() {
        if ($('#saletype').val() == 1) {
            // Si es una variación y el valor de parentcode no es vacío validamos el codeValidated
            var parentCode = $('#parentcode').val();
            if (parentCode != "") {
                $.ajax({
                    type: "post",
                    url: '/ajax.php',
                    data: {
                        code: parentCode,
                        op: 'checkPdfCode',
                        fieldname: 'id'
                    }
                }).done(function (data) {
                    // Retorna "si" 0 "no" dependeiendo si el usuario existe
                    if (data == "si") {
                        border_ok('#search-box');
                        codeValidated = true;
                        saveSale();
                    } else {
                        border_error('#search-box');
                        codeValidated = false;
                        alert('Comprueba el Nº de pedido asociado!');
                        return false;
                    }
                });
            } else {
                alert('Comprueba el Nº de pedido asociado!');
                border_error('#search-box');
                return false;
            }
        } else {
            saveSale();
        }
    }

    function saveSale() {
        if ($('#saletype').val() == 0) {
            comprobate = Array('#saledate', '#code', '#customer', '#total', '#payed', '#paymethod');
        } else {
            border_ok('#payed');
            comprobate = Array('#saledate', '#parentcode', '#total');
            if ($('#payed').val() > 0) {
                comprobate.push('#paymethod');
            } else {
                border_ok('#paymethod');
            }
        }

        if (checkNoEmpty(comprobate) && codeValidated) {
            $('#sp-save-sale').show();
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: $('#frm-saledata').serialize(),
            }).done(function (data) {
                $('#sp-save-sale').hide();
                var res = JSON.parse(data);
                var salelabel = 'venta';
                if ($('#saletype').val() == 1) {
                    salelabel = 'variación';
                }
                if (res['saved'] == 1) {
                    id = res['lastid'];
                    alert('Datos de la ' + salelabel + ' registrados.');
                    $('#btn-save-sale').prop('value', 'Modificar venta');
                    $('#opt-save-sale').prop('value', 'updateSale');
                    $('#id').prop('value', id);
                    //$('#div_img').show(); // Las imágenes se cargan ahora en la sección de documentos adicionales
                    $('#div_img2').show();
                    if ($('#saletype').val() == 0) {
                        $('#div_pdf').show();
                    }
                    scrollingTo('#div_img2');
                    $('#commentsdiv').show();
                } else if (res['codeduplicated']) {
                    alert('Error al crear la venta. El código de pedido ya existe en otra venta!');
                } else if (res['updated'] == 1) {
                    alert('Datos de ' + salelabel + ' actualizados correctamente!');
                } else if (res['duplicated'] == 1) {
                    alert('Nada para actualizar!');
                }
            });
        } else {
            alert(completeRequiredFields);
        }
    }

    var reuploaded = <?php echo($data && $data->reuploaded == 1 ? 'true' : 'false'); ?>;

    function checkCode() {
        var code = $('#code').val();
        if (code.length > 1) {
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: {
                    code: code,
                    op: 'checkPdfCode',
                    fieldname: 'code'
                }
            }).done(function (data) {
                // Retorna "si" 0 "no" dependeiendo si el usuario existe
                if (data == "si") {
                    addError('#div_code', '#code');
                    $('#code_response').html("El código ya existe");
                    codeValidated = false;
                    $('#btnsave').prop('disabled', true);
                } else {
                    addSuccess('#div_code', '#code');
                    $('#code_response').html("Código disponible");
                    codeValidated = true;
                    $('#btnsave').prop('disabled', false);
                }
            });
        }
    }

    function save_code() {
        var code = $('#code').val();
        if (code.length > 1) {
            var opt = 'savePdfCode';
            if (id != "" && id > 0) {
                opt = 'updatePdfCode';
            }
            $.ajax({
                type: "post",
                url: '/ajax.php',
                data: {
                    code: code,
                    op: opt,
                    id: id, // Para la actualización es necesario el id. En el guardado la quitamos (unset)
                }
            }).done(function (data) {
                var res = JSON.parse(data);
                if (res['saved'] == 1) {
                    alert('Código del pedido guardado.\nAhora puede subir el PDF.');
                    $('#code_response').html("");
                    $('#btnsave').prop('value', 'Cambiar código');
                    id = res['lastid'];
                    // Seteamos el id para el guardado del pdf
                    $('#div_pdf').show();
                    scrollingTo('#div_pdf');
                } else if (res['updated'] == 1) {
                    alert('Código actualizado correctamente!');
                } else if (res['duplicated'] == 1) {
                    alert('El código antiguo y el nuevo son iguales!');
                }
            });
        }
    }

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

            data.append('op', 'uploadOrderImage');
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

            data.append('op', 'uploadOrderPdf');
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

    function changeSaleType() {
        var defaultLabel = "Guardar ";
        var defaultTitle = "Nueva ";
        if (id != "") {
            defaultLabel = "Modificar ";
            defaultTitle = "Modificar ";
        }

        if ($('#saletype').val() == 0) {
            $('#totallabel').html('Importe total');
            $('#dynamictitle').html(defaultTitle + "venta");
            $('#btn-save-sale').prop("value", defaultLabel + "venta");
            $('#div_code').show();
            $('#div_parentcode').hide();
            $('#div_customer').show();
            $('#div_payed').show();
            if (id != "") {
                $('#div_img2').show();
                $('#div_pdf').show();
            }

        } else {
            $('#dynamictitle').html(defaultTitle + "variación");
            $('#totallabel').html('Importe total de la <span class="red-color"><b>variación</b></span>');
            $('#btn-save-sale').prop("value", defaultLabel + "variación");
            $('#div_code').hide();
            $('#div_parentcode').show();
            $('#div_customer').hide();
            $('#div_pdf').hide();
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
                    pdfid: id,
                    comment: comment,
                    op: 'savePdfComment'
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
    
    function getAutocompleteProduct(id, criteria) {
    	if (criteria.length >= 2) {
    		$("#auto" + id).show();
    	
	    	$.ajax({
	    		url: "/ajax.php",
	    		type: "post",
	    		data: {
	    			id: id,
	    			keyword: criteria,
	    			opt: 'getAutocompleteProduct',
	    		}
	    	}).done(function(response) {
	    		var ele = '#auto' + id;
	    		$(ele).html(response);
	    	});
    	}
    }
    
    //function selectCode(autoid, id, code) {
	function selectCode(autoid, obj) {
	   	$("#auxref" + autoid).val(obj['reference']);
	    $("#desc" + autoid).val(obj['productname']);
	    $("#price" + autoid).val(obj['price']);
	    $("#id" + autoid).val(obj['id']);
	    $("#auto" + autoid).html("");
	    $("#auto" + autoid).hide();
	    
	    // Actualizamos los precios ante un posible cambio de producto, siempre que la cantidad se haya indicado
	    if ($('#quantity' + autoid).val() != "") {
	    	calculatePrice(autoid);
	    }
    }
	
	function saveProducts() {
		$.ajax({
			url: "/ajax.php",
			type: 'post',
			data: $('#frm-prod').serialize() 
		}).done(function(response) {
			
		});
	}
	
	<?php
		$countProducts = 1;
	
	    /*if ($data['data'] && count($data['data']->getItems())) {
	        $countProducts = count($data['data']->getItems());
	    }*/
    
    ?>

    var products = <?php echo $countProducts + 1; ?>;
    
    function moreProducts(target) {
        var html = '<tr id="product' + products + '" class="ui-state-default">';
        	html +=     '<td class="text-center">';
        	html +=     '<?php echo icon('sort'); ?>';
        	html +=     '</td>';
			html +=     '<td>';
		    html += 	'<input type="text" id="auxref' + products + '" readonly="" class="form-control input-small">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="desc' + products + '" class="form-control input-large" autocomplete="off" onkeyup="getAutocompleteProduct(' + products + ', this.value)">';
			html += 	'	<input type="hidden" id="id' + products + '" name="id[]">';
			html += 	'	<div id="auto' + products + '"></div>';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="quantity' + products + '" name="quantity[]" class="form-control input-xsmall text-center quantity" onkeyup="calculatePrice(' + products + ');">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="price' + products + '" disabled="disabled" class="form-control input-small text-center price">'; 
			html += 	'   <input type="hidden" id="hideprice' + products + '" class="hideprice">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="dto' + products + '" name="dto[]" class="form-control input-xsmall text-center dto" onkeyup="calculatePrice(' + products + ');" value="0">';
			html += 	'   <input type="hidden" id="hidedto' + products + '" class="hidedto">';
			html += 	'</td>';
			html += 	'<td>';
			html += 	'	<input type="text" id="subtotal' + products + '" disabled="disabled" class="form-control input-small subtotal">';
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
    }

    function deleteProduct(id) {
        $('#product' + id).remove();
        $('#productdate' + id).remove();
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
    
    /**
     * Actualizamos el subtotal teniendo en cuenta el descuento 
     */
    /*function calculatePrice(id) {
    	var quantity = parseInt($('#quantity' + id).val());
    	var dto = parseFloat($('#dto' + id).val());
    	var price = parseFloat($('#price' + id).val());
    	var result = price * quantity;
    	
    	if (!isNaN(dto)) {
			var auxDto = (price / 100) * dto;
			var difference = price - auxDto;
			$('#hidedto' + id).prop("value", Number(auxDto).toFixed(2));
			$('#subtotal' + id).prop("value", Number(difference).toFixed(2));
		} else {
			$('#subtotal' + id).prop("value", Number(result).toFixed(2));
			$('#hidedto' + id).prop("value", 0);
		}
		
		calculateTotal();
    }*/
    
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
    	
    	var iva = parseFloat((totSub / 100) * 21);
    	iva = Number(iva).toFixed(2);
    	$('#iva').prop("value", iva);
    	
    	var lasttotal = parseFloat(totSub) + parseFloat(iva);
    	$('#lasttotal').prop("value", Number(lasttotal).toFixed(2));
    	
    }

</script>

<?php
    $disabled = 'disabled="disabled"';
    $defaultLabel = 'pedido a fábrica';
    
    $canEdit = false;
    global $user;
    if (!$data) {
        $canEdit = true;
        $disabled = '';
    } else if ($data && $data->orderexist == 0 && !isTimeOver($data->created_on) || $user->getUsermanager() == 1 || isAdmin()) {
        $canEdit = true;
        $disabled = '';
    }

?>

<div class="card">
    <div class="card-header">
        <h4 class="card-title">
            <span id="dynamictitle">
            	<?php echo $data ? "Modificar $defaultLabel" : "Nuevo $defaultLabel" ?>
        	</span>
        </h4>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-sm-6">
                <form id="frm-saledata">
                    <hr>
                    <h4 style="color: gray;">Datos propuesta</h4>
                    
                    <div class="form-group row" id="div_saledate">
                        <label for="saledate"
                               class="col-sm-2 col-form-label">Fecha de venta</label> 
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="saledate" id="saledate"
                                   value="<?php echo $data ? americaDate($data->saledate, false) : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                   
                    <div class="form-group row" id="div_code">
                        <label for="code"
                               class="col-sm-2 col-form-label">Nº de pedido</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="code" id="code" onkeyup="checkCode();"
                                   value="<?php echo $data ? $data->code : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                    
                    <div class="form-group row" id="div_concept">
                        <label for="concept"
                               class="col-sm-2 col-form-label">Concepto</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="concept" id="concept"
                                   value="<?php echo $data ? $data->concept : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                   
                    <hr>
                    <h4 style="color: gray;">Datos cliente</h4>
                    <div class="form-group row" id="div_customer">
                        <label for="customer"
                               class="col-sm-2 col-form-label">Titular del pedido</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="customer" id="customer"
                                   value="<?php echo $data ? $data->customer : ''; ?>">
                        </div>
                    </div>
                    <div class="form-group row" id="div_dni">
                        <label for="dni"
                               class="col-sm-2 col-form-label">DNI</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="dni" id="dni"
                                   value="<?php echo $data ? $data->dni : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                    <div class="form-group row" id="div_address">
                        <label for="address"
                               class="col-sm-2 col-form-label">Dirección</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="address" id="address" <?php echo $disabled; ?>><?php echo $data ? $data->address : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row" id="div_tel">
                        <label for="tel"
                               class="col-sm-2 col-form-label">Teléfono</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tel" id="tel" 
                            	value="<?php echo $data ? $data->tel : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                    <div class="form-group row" id="div_tel">
                        <label for="email"
                               class="col-sm-2 col-form-label">E-mail</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="email" id="email" 
                            	value="<?php echo $data ? $data->email : ''; ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row" id="div_total">
                        <?php
                        $totalLabel = 'Importe total';
                        if ($data && $data->saletype > 0) {
                            $totalLabel = 'Importe total de la <span class="red-color">variación</span>';
                        }
                        ?>
                        <label for="total"
                               class="col-sm-2 col-form-label"><span id="totallabel"><?php echo $totalLabel; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="total" id="total" onkeyup="addCommas($(this).prop('id'), $(this).val());"
                                   value="<?php echo $data ? numberFormat($data->total, true, 2) : ""; ?>" <?php echo $disabled; ?>>
                            <span class="red-color">(decimales separados por coma)</span>
                        </div>
                    </div>

                    <div class="form-group row" id="div_payed">
                        <label for="payed"
                               class="col-sm-2 col-form-label">Abonado a cuenta</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="payed" id="payed" onkeyup="addCommas($(this).prop('id'), $(this).val());"
                                   value="<?php echo $data ? numberFormat($data->payed, true, 2) : ""; ?>" <?php echo $disabled; ?>>
                            <span class="red-color">(decimales separados por coma)</span>
                        </div>
                    </div>

                    <div class="form-group row" id="div_paymethod">
                        <label for="paymethod"
                               class="col-sm-2 col-form-label">Mediante</label>
                        <div class="col-sm-10">
                            <select name="paymethod" id="paymethod" class="form-select" <?php echo $disabled; ?>>
                                <option value="">Elija una opción</option>
                                <?php
                                global $paymethods;
                                foreach ($paymethods as $key => $value) {
                                    $selected = "";
                                    if ($data && $data->paymethod == $key && $data->total > 0) {
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <?php if ($canEdit) {
                            $btnLabel = 'venta';
                            if ($data && $data->saletype == 1) {
                                $btnLabel = 'variación';
                            }
                            ?>
                            <div class="col-sm-4">
                                <input type="hidden" name="id" id="id" value="<?php echo $data ? $data->id : ''; ?>">
                                <input type="hidden" name="opt" value="<?php echo $data ? 'updateSale' : 'saveSale'; ?>"
                                       id="opt-save-sale">
                                <input type="button" class="btn btn-primary" id="btn-save-sale"
                                       value="<?php echo($data ? 'Modificar ' : 'Guardar ') . $btnLabel ?>" onclick="checkSaleData();">
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
                                <textarea class="form-control" id="pdfcomment"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button class="btn btn-primary" type="button" onclick="saveComment(1);">Nuevo comentario</button>
                        <?php spinner_icon('spinner', 'sp-cus-comment', true); ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="products" style="margin-bottom: 20px;">
        	<hr>
        	<h4 style="color: gray;">Productos</h4>
        	<form id="frm-prod">
	        	<table>
	        		<thead>
	        			<tr>
	        				<th class="text-center" style="width: 80px;"><?php echo icon('sort'); ?></th>
	        				<th class="text-center">Ref.</th>
	        				<th class="text-center">Descripción</th>
	        				<th class="text-center">Cant.</th>
	        				<th class="text-center">Precio.</th>
	        				<th class="text-center">Dto(%).</th> 
	        				<th class="text-center">Importe</th>
	        				<th class="text-center"><?php echo icon('delete'); ?></th>
	        			</tr>
	        		</thead>
	        		<tbody id="sortable" class="ui-sortable">
	        			<tr id="product1" class="ui-state-default">
	        				<td class="text-center"> 
	        					<?php echo icon('sort'); ?>
	        				</td>
	        				<td>
	        					<input type="text" id="auxref1" readonly="" class="form-control input-small">
	        				</td>
	        				<td>
	        					<input type="text" id="desc1" class="form-control input-large" autocomplete="off" onkeyup="getAutocompleteProduct(1, this.value)"> 
	        					<input type="hidden" id="id1" name="id[]">
	        					<div id="auto1"></div>
	        				</td>
	        				<td>
	        					<input type="text" id="quantity1" name="quantity[]" class="form-control input-xsmall text-center quantity" onkeyup="calculatePrice(1)">
	        				</td>
	        				<td>
	        					<input type="text" id="price1" disabled="disabled" class="form-control input-small text-center price">
	        					<input type="hidden" id="hideprice1" class="hideprice">
	        				</td>
	        				<td>
	        					<input type="text" id="dto1" name="dto[]" class="form-control input-xsmall text-center dto" onkeyup="calculatePrice(1)" value="0">
	        					<input type="hidden" id="hidedto1" class="hidedto">
	        				</td>
	        				<td>
	        					<input type="text" id="subtotal1" disabled="disabled" class="form-control input-small subtotal" value="">
	        					<input type="hidden" id="hidesubtotal1">
	        				</td>
	        				<td>&nbsp;</td>
	        			</tr>
	        		</tbody>
	        		<tbody>
	        			<tr>
	        				<td colspan="4">&nbsp;</td>
	        				<td>
	        					<a onclick="moreProducts('sortable')" style="cursor: pointer;">(<?php icon('plus', true); ?>) Nuevo</a>
	        				</td>
	        			</tr>
	        			<tr>
	        				<td colspan="5">&nbsp;</td>
	        				<td class="table-total"><b>Importe</b></td>
	        				<td colspan="2"><input type="text" class="form-control" disabled="" id="subtotal"></td>
	        			</tr>
	        			<tr>
	        				<td colspan="5">&nbsp;</td>
	        				<td class="table-total"><b>Descuento</b></td>
	        				<td colspan="2"><input type="text" class="form-control" disabled="" id="dto"></td>
	        			</tr>
	        			<tr>
	        				<td colspan="5">&nbsp;</td>
	        				<td class="table-total"><b>Base</b></td>
	        				<td colspan="2"><input type="text" class="form-control" disabled="" id="base"></td>
	        			</tr>
	        			<tr>
	        				<td colspan="5">&nbsp;</td>
	        				<td class="table-total"><b>IVA (21%)</b></td>
	        				<td colspan="2"><input type="text" class="form-control" disabled="" id="iva"></td>
	        			</tr>
	        			<tr>
	        				<td colspan="5">&nbsp;</td>
	        				<td class="table-total"><b>Total</b></td>
	        				<td colspan="2"><input type="text" class="form-control" disabled="" id="lasttotal"></td>
	        			</tr>
	        		</tbody>
	        	</table>
        	</form>
        	
        	<div style="margin: 15px 0px;">
        		<input type="button" class="btn btn-primary" id="btn-save-products"
                                       value="<?php echo($data ? 'Modificar productos' : 'Guardar productos') ?>" onclick="saveProducts();">
            </div>
        </div>
        
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
                    if ($data->image != "") {
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
            if ($data && ($data->saletype == 0 || $data->saletype == 1)) {
                $displayDocuments = '';
            }
        ?>

        <div id="div_img2" style="<?php echo $displayDocuments; ?>">
            <hr>
            <div class="form-group row">
                <label for="image"
                       class="col-sm-1 col-form-label">Documentos adicionales<?php icon('image', true) . "" . icon('word', true); ?></label>
                <div class="col-sm-8">
                    <input type="file" id="image2" class="form-control" multiple="multiple" onchange="addImage('secondary');"
                           onclick="border_ok('#image2')">
                </div>

                    <div class="col-sm-2">
                        <input type="button" value="Subir documento" class="btn btn-primary" onclick="uploadImage('secondary');">
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

                        listDirectory('uploaded-files/images/' . $data->id . '/secondary/', true, $config);
                    }
                ?>
            </div>
        </div>

        <?php
            $pdfDisabled = 'disabled="disabled"';
            if ($data && $data->orderexist == 0) {
                $pdfDisabled = "";
            } else if ($user->getUsermanager() == 1) {
                $pdfDisabled = "";
            }  else if (!$data) {
                $pdfDisabled = "";
            }

            $displayPdf = 'display: none;';
            if ($data && $data->saletype == 0) {
                $displayPdf = '';
            }
        ?>

        <div id="div_pdf" style="<?php echo $displayPdf; ?>">
            <hr>
            <div class="form-group row">
                <label for="file"
                       class="col-sm-1 col-form-label">Propuesta de pedido<?php icon('pdf', true); ?></label>
                <div class="col-sm-8">
                    <input type="file" id="file" class="form-control" multiple="multiple" onchange="addFile();"
                           onclick="border_ok('#file')" <?php echo $data && $data->orderexist !=  0 ? $disabled : ""; ?>>
                </div>
                <div class="col-sm-2">
                    <input type="button" value="Subir propuesta de pedido" class="btn btn-primary" onclick="uploadPdf();" <?php echo $pdfDisabled; ?>>
                    <?php spinner_icon('spinner', 'sp-upload', true); ?>
                </div>

            </div>

            <div id="ajax-content">
                <?php
                if ($data) {
                    if ($data->pdfname != "") {
                        $deleteLk = $data->orderexist == 0  || $user->getUsermanager() == 1 || isadmin() ? true : false;
                        echo '<div>';
                        $config = array(
                            'divresponse' => 'ajax-content',
                            'excludes' => array()
                        );
                        listDirectory('uploaded-files/pdfs/' . $data->id, $deleteLk, $config);
                        echo '</div>';
                    } else {
                        errorMsg('No se ha cargado ninguna propuesta');
                    }
                }
                ?>
            </div>
        </div>

        <div class="card-footer text-muted">
            <?php
                exit_btn(getUrl('show_pdfs', $myController->getUrls()));
            ?>
        </div>
    </div>
</div>

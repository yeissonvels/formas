<?php
    datePicker('saledate');
    $estimateData = [];

    if(isset($data) && is_array($data) && isset($data['estimate'])) {
        $estimateData = $data['estimate'];
        $data = [];
    }
?>
<script>
    var id = "<?php echo $data ? $data->id : ''; ?>";
    var codeValidated = <?php  echo $data && $data->code ? 'true' : ($estimateData && $estimateData->code ? 'true' : 'false'); ?>;
    let modalValidated = <?php  echo $data && $data->code ? 'true' : 'false' ?>;

    $(document).ready(function(){
        //$('#modalAlert').modal('show');
        $("#search-box").keyup(function(){
            $('#parentcode').prop("value", "");
            if ($(this).val() != "") {
                let config = {
                    searchBox: '#search-box',
                    inputId: '#parentcode',
                    suggestionBox: '#suggesstion-box'
                };

                $.ajax({
                    type: "POST",
                    url: "/ajax.php",
                    data: {
                        keyword: $(this).val(),
                        config: config,
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

        $("#search-box-code").keyup(function () {
            $('#code').prop("value", "");
            codeValidated = false;
            modalValidated = false;
            if ($(this).val() != "") {
                let config = {
                    searchBox: '#search-box-code',
                    inputId: '#code',
                    suggestionBox: '#suggesstion-box-code'
                };
                // Retornar una promesa
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: "POST",
                        url: "/ajax.php",
                        data: {
                            keyword: $(this).val(),
                            config: config,
                            nocancelled: 'yes',
                            op: 'getAutocompleteEstimateCode',
                        },
                        beforeSend: function () {
                            // Opción para mostrar algo durante la carga (comentada en tu código original)
                            // $("#search-box-code").css("background", "#FFF url(LoaderIcon.gif) no-repeat 165px");
                        },
                        success: function (data) {
                            $("#suggesstion-box-code").show();
                            $("#suggesstion-box-code").html(data);
                            $("#search-box-code").css("background", "#FFF");
                            resolve(data); // Resolver con los datos recibidos
                        },
                        error: function (xhr, status, error) {
                            reject(error); // Rechazar en caso de error
                        }
                    });
                });
            } else {
                $("#suggesstion-box-code").html("");

                // Retornar una promesa ya resuelta si el input está vacío
                return Promise.resolve('empty');
            }
        });
    });

    function selectCode(id, code, configString) {
        let config = configString.split(',');
        $(config[0]).val(code);
        $(config[1]).val(id);
        $(config[2]).hide();
    }

    function setEstimate(estimate) {
        // Asegurarse de que estimate es un JSON válido
        if (typeof estimate === 'string') {
            estimate = JSON.parse(estimate);
        }
        console.log("Estimate ID:", estimate.id);
        console.log("Customer:", estimate.customer);
        // Resto de la lógica para manejar la estimación
        $('#code').prop('value', estimate.code);
        codeValidated = true;
        $('#search-box-code').prop('value', estimate.code);
        $('#customer').prop('value', estimate.customer);
        $('#tel').prop('value', estimate.tel);
        $('#tel2').prop('value', estimate.tel2);
        $('#total').prop('value', estimate.total);
        
    }

    function selectEstimateCode(code, fullCode, configString) {
        let config = configString.split(',');
        $(config[0]).val(fullCode);
        $(config[1]).val(code);
        $(config[2]).hide();

        $.ajax({
            type: "POST",
            url: "/ajax.php",
            data: {
                estimatecode: code,
                config: config,
                nocancelled: 'yes',
                op: 'getEstimateData',
            },
            beforeSend: function () {
                //$("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function (data) {
                let estimateData = JSON.parse(data);
                codeValidated = true;
                $('#customer').prop('value', estimateData['customer']);
                $('#tel').prop('value', estimateData['tel']);
                $('#tel2').prop('value', estimateData['tel2']);
                $('#total').prop('value', estimateData['total']);
                $('#payed').prop('value', '');
                $('#paymethod').prop('value', '');
                console.log(estimateData);
            }
        });
    }

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

    function modalConfirmated(){
        modalValidated = true;
        saveSale();
    }

    function cloningForm() {
        // Clonar el formulario
        var clonedForm = $('#frm-saledata').clone();
        // Asignar un ID único al formulario clonado
        clonedForm.attr('id', 'myClonedForm');

        // Deshabilitar todos los elementos del formulario clonado
        clonedForm.find('input[type="button"]').hide();
        clonedForm.find("input, textarea, button, select").prop("disabled", true);

        clonedForm.find(':input').each(function() {
            var original = $('#frm-saledata').find('[name="'+ $(this).attr('name') +'"]');
            if ($(this).is('select')) {
                $(this).val(original.val());
            } else if ($(this).is(':input')) {
                //$(this).val(original.val());
            }
        });

        // Agregar el formulario clonado al contenedor
        $("#cloned").html(clonedForm);
    }

    function saveSale() {
        if ($('#saletype').val() == 0) {
            //comprobate = Array('#saledate', '#code', '#search-box-code', '#customer', '#total', '#payed', '#paymethod');
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
            if(!modalValidated) {
                cloningForm();
                $('#modalAlert').modal('show');
            }
            
            if(modalValidated){
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
                console.log('Modal no validada');
            }
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

    /*function checkParentCode() {
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
                } else {
                    border_error('#search-box');
                    codeValidated = false;
                    return false;
                }
            });
        } else {
            border_error('#search-box');
            return false;
        }
    }*/

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
        /*var comment = $('#pdfcomment').val();
        if (reuploaded) {
            if (comment == "") {
                alert('Por favor indique por qué carga un nuevo PDF!');
                border_error('#pdfcomment');
                scrollingTo('#dynamic-cus-comments');
                return false;
            }
        }*/

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

</script>

<?php
    $disabled = 'disabled="disabled"';
    $defaultLabel = 'venta';
    if ($data && $data->saletype == 1) {
        $defaultLabel = 'variación';
    }
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
            <span id="dynamictitle"><?php echo $data ? "Modificar $defaultLabel" : "Nueva $defaultLabel" ?></span>
            <?php icon('money', true); ?></h4>
    </div>
    <div class="card-block mt-3">
        <div class="row">
            <div class="col-sm-6">
                <form id="frm-saledata">
                    <div class="form-group row">
                        <label for="saletype"
                               class="col-sm-2 col-form-label">Tipo</label>
                        <div class="col-sm-10">
                            <select name="saletype" id="saletype" class="form-select" <?php echo $disabled; ?> onchange="changeSaleType();">
                                <?php
                                    global $saletypes;

                                    if(isset($_GET['estimateId'])) {
                                        echo '<option value="0" selected>' . $saletypes[0] . '</option>';
                                    } else {
                                       
                                        foreach ($saletypes as $key => $value) {
                                            if ($key != 2) {
                                                $selected = '';
                                                if ($data && $data->saletype == $key) {
                                                    $selected = 'selected="selected"';
                                                }
                                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                                            }
                                        }
                                    }
                                    
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
                    $parentValue = "";
                    if ($data && $data->saletype == 1) {
                        $parentValue = $data->parent->code . ' (' . americaDate($data->parent->saledate, false) . ') ' . $data->parent->customer;
                    }

                    $displayParentCode = 'display: none;';
                    if ($data && $data->saletype == 1) {
                        $displayParentCode = '';
                    }
                    ?>
                    <div class="form-group row" id="div_parentcode" style="<?php echo $displayParentCode?>">
                        <label for="parentcode"
                               class="col-sm-2 col-form-label">Nº de pedido asociado</label>
                        <div class="col-sm-10">
                            <input type="text" id="search-box" placeholder="Código o nombre del cliente"  class="form-control" value="<?php echo $parentValue; ?>" autocomplete="off" <?php echo $disabled; ?>/>
                            <input type="hidden" name="parentcode" id="parentcode" value="<?php echo $data ? $data->parentcode: ''; ?>" autocomplete="off">
                            <div id="suggesstion-box" style="position: absolute; z-index: 10000;"></div>
                        </div>
                    </div>
                    <div class="form-group row" id="div_saledate">
                        <label for="saledate"
                               class="col-sm-2 col-form-label">Fecha de venta/ variación</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="saledate" id="saledate"
                                   value="<?php echo $data ? americaDate($data->saledate, false) : ''; ?>" <?php echo $disabled; ?> autocomplete="off">
                        </div>
                    </div>
                    <?php
                    $displayCode = '';
                    if ($data && $data->saletype == 1) {
                        $displayCode = 'display: none;';
                    }
                    ?>

                    <?php
                        $showedCode = "";
                        if ($data && $data->saletype == 0) {
                            //$codeValue = $data->code . ' (' . americaDate($data->saledate, false) . ') ' . $data->customer;
                            $showedCode = $data->code;
                            //$code = $data->code;
                        } else if($estimateData) {
                            //$code = $estimateData->id;
                            $showedCode = $estimateData->code;
                        }
                    ?>

                    <div class="form-group row" id="div_code" style="<?php echo $displayCode; ?>">
                        <label for="code"
                               class="col-sm-2 col-form-label">Nº de pedido</label>
                        <div class="col-sm-10">
                           <!-- <input type="text" id="search-box-code" placeholder="Código o nombre del cliente"  class="form-control" value="< ?php echo $codeValue; ?>" autocomplete="off" < ?php echo $disabled; ?>/>
                            <input type="hidden" name="code" id="code" value="< ?php echo $data ? $data->code: ''; ?>" autocomplete="off">
                            <div id="suggesstion-box-code" style="position: absolute; z-index: 10000;"></div>-->
                            <input type="text" class="form-control" value="<?php echo $showedCode; ?>" autocomplete="off" disabled/>
                            <input type="hidden" name="code" id="code" value="<?php echo $showedCode; ?>" autocomplete="off">
                        </div>
                    </div>
                    <?php
                    $displayCustomer = '';
                    if ($data && $data->saletype == 1) {
                        $displayCustomer = 'display: none;';
                    }
                    ?>
                    <div class="form-group row" id="div_customer" style="<?php echo $displayCustomer; ?>">
                        <label for="customer"
                               class="col-sm-2 col-form-label">Titular del pedido</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="customer" id="customer"
                                   value="<?php echo $data ? $data->customer : ($estimateData ? $estimateData->customer : ''); ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                    <div class="form-group row" id="div_telefono" style="<?php echo $displayCustomer; ?>">
                        <label for="tel"
                               class="col-sm-2 col-form-label">Teléfono</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tel" id="tel"
                                   value="<?php echo $data ? $data->tel : ($estimateData ? $estimateData->tel : ''); ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
                    <div class="form-group row" id="div_telefono2" style="<?php echo $displayCustomer; ?>">
                        <label for="tel2"
                               class="col-sm-2 col-form-label">Teléfono 2</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="tel2" id="tel2"
                                   value="<?php echo $data ? $data->tel2 : ($estimateData ? $estimateData->tel2 : ''); ?>" <?php echo $disabled; ?>>
                        </div>
                    </div>
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
                                   value="<?php echo $data ? numberFormat($data->total, true, 2) : ($estimateData ? numberFormat($estimateData->total, true, 2) : ''); ?>" <?php echo $disabled; ?>>
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
            <div class="col-sm-6">
                <?php
                    $commentsDisplay = $data ? 'block' : 'none';
                ?>
                <div class="modal-body">
                    <h6>Mis últimos presupuestos <?php icon('estimate', true); ?></h6>
                    <h6>
                        <a href="#" onclick="$('#myLastEstimates').slideToggle('slow');"><?php icon('view', true);?></a></h6>
                    <?php 
                        $myController->showMyLastEstimates();
                    ?>
                </div>
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
                           onclick="border_ok('#image2')" >
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
                            'excludes' => array(),
                            'disabled' => $disabled
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

<!-- Modal de alerta para pedir confirmación antes de guardar la venta -->
<div aria-labelledby="exampleModalLiveLabel" role="dialog" tabindex="-1" class="modal fade" id="modalAlert">
    <div role="document" class="modal-dialog modal-fullscreen">
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
                                        
                                        $msg = "<h5>¿Confirmas que los datos son correctos?</h5>";
                                        $msg .= "<div id='cloned'></div>";

                                        $msgRegister = "<h6>Una vez registrada la venta, esta quedará vinculada al presupuesto seleccionado.</h6>";
                                        $msgUpdate = "<h6>Si estás cambiando el número de presupuesto, el presupuesto anterior que estaba vinculado con la venta quedará liberado.</h6>";

                                        $complementMsg = $msgRegister;

                                        if($data) {
                                            //$complementMsg = $msgUpdate;
                                        }

                                        $msg .= $complementMsg;
                                        
                                        $msg .= "<h6 class='mt-3'>Pulsa el botón <span class='text-primary'>'Confirmar datos'</span> para continuar o <span style='color: red;'>'Salir'</span> para modificar los datos.</h6>";
                                        warningMsg($msg, true); 
                                    ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-bs-dismiss="modal" class="btn btn-primary" type="button" onclick="modalConfirmated();">Confirmar datos</button>
                    <button data-bs-dismiss="modal" class="btn btn-danger" type="button">Salir</button>
                </div>
            </div>
        </form>
    </div>
</div>

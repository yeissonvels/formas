<script>
    // Si no hay sub items comprobamos estos valores
    comprobate = Array('#label', '#label2', '#label3', '#link');

    function check_new_menu() {
        // Devuelve true si todos los campos han sido completados
        if(checkNoEmpty(comprobate)){
            return true;
        } else {
            alert(completeRequiredFields);
            return false;
        }
    }

    function scrollingTop() {
        $('html,body').animate({
                scrollTop: $("body").offset().top},
            'slow');
    }

    function saveMenuItem() {

        if (check_new_menu()) {
            $('#dynamic-menu').html('');
            $('#loader').show();

            var menuid = $('#menuid').val();
            var id = $('#id').val();
            var label = $('#label').val();
            var label2 = $('#label2').val();
            var label3 = $('#label3').val();
            var show_label = $('#show_label').val();
            var parent = $('#parent').val();
            var link = $('#link').val();
            var link_friendly = $('#link_friendly').val();
            var active = $('#active').val();
            var permision = $('#permision').val();
            var position = $('#position').val();
            var target = $('#target').val();
            var controller = '<?php echo FORM_CONTROLLER; ?>';
            var op = 'saveMenuItem';

            var data = new FormData();

            jQuery.each(jQuery('#icon')[0].files, function (i, file) {
                data.append('file-' + i, file);
            });

            data.append('menuid', menuid);
            data.append('id', id);
            data.append('label', label);
            data.append('label2', label2);
            data.append('label3', label3);
            data.append('show_label', show_label);
            data.append('link', link);
            data.append('link_friendly', link_friendly);
            data.append('parent', parent);
            data.append('active', active);
            data.append('permision', permision);
            data.append('position', position);
            data.append('target', target);
            data.append('controller', controller);
            data.append('op', op);

            $.ajax({
                url: '/ajax.php',
                type: 'post',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    updateMenuParents();

                    $('#loader').hide();
                    $('#message').html(data);
                    updateDynamicMenu();
                    // Reseteamos el formulario
                    $('#id').prop('value', 0);
                    $('#label').prop('value', '');
                    $('#label2').prop('value', '');
                    $('#label3').prop('value', '');
                    $('#link').prop('value', '');
                    $('#link_friendly > option[value="0"]').prop('selected', true);
                    $('#show_label > option[value="0"]').prop('selected', true);
                    $('#parent > option[value="0"]').prop('selected', true);
                    $('#active > option[value="0"]').prop('selected', true);
                    $('#target > option[value="0"]').prop('selected', true);
                    $('#permision > option[value="1"]').prop('selected', true);
                    $('#position > option[value=""]').prop('selected', true);
                    $('#position').prop('value', '');



                    // Ocultamos el div de confirmación de la carga del ícono
                    setTimeout(function() {
                        $("#message").fadeOut( "slow" );
                    }, 1500);

                    alert("Menú actualizado!");
                    var _scrollTo = $('#selecteditem').val();
                    if (_scrollTo != "") {
                        $('html,body').animate({
                                scrollTop: $("#tr" + _scrollTo).offset().top},
                            'slow');
                    }

                    $('#selecteditem').prop('value', '');
                }
            });
        }
    }

    function updateDynamicMenu() {
        var controller = '<?php echo FORM_CONTROLLER; ?>';

        $.ajax({
            url: '/ajax.php',
            type: 'get',
            data: {
                id: <?php echo $_GET['id'] ?>,
                op: 'updateDynamicMenu',
                controller : controller
            },
            success: function (data) {
                $('#dynamic-menu').html(data);
            }
        });
    }

    function loadMenuItemData(id) {
        var controller = '<?php echo FORM_CONTROLLER; ?>';
        $('#btn').prop('value', '<?php echo trans('btn_update')?>');
        $('#selecteditem').prop('value', id);
        $.ajax({
            url: '/ajax.php',
            type: 'get',
            data: {
                id: id,
                op: 'getMenuItemData',
                controller : controller
            },
            success: function (data) {
                $('#id').prop('value', id);
                $('#tricon').show();
                item = JSON.parse(data);

                var active = item['active'];
                var parent = item['parent'];
                var target = item['target'];
                var show_label = item['show_label'];
                var link_friendly = item['link_friendly'];
                var permision = item['permision'];
                var position = item['position'];

                $('#label').prop('value', item['label']);
                $('#label2').prop('value', item['label2']);
                $('#label3').prop('value', item['label3']);
                $('#link').prop('value', item['link']);
                $('#link_friendly > option[value="' + link_friendly + '"]').prop('selected', true);
                $('#show_label > option[value="' + show_label + '"]').prop('selected', true);
                $('#parent > option[value="' + parent + '"]').prop('selected', true);
                $('#target > option[value="' + target + '"]').prop('selected', true);
                $('#active > option[value="' + active + '"]').prop('selected', true);
                $('#permision > option[value="' + permision + '"]').prop('selected', true);
                $('#position > option[value="' + position + '"]').prop('selected', true);
            }
        });
    }

    function updateMenuParents() {
        var controller = '<?php echo FORM_CONTROLLER; ?>';
        var id = <?php echo $_GET['id'] ?>;
        $.ajax({
            url: '/ajax.php',
            type: 'get',
            data: {
                op: 'updateMenuParents',
                id: id,
                controller: controller
            },
            success: function (data) {
                $('#parent').html(data);
            }
        });
    }

    function uploadIcon() {
        if ($('#icon').val() != "") {
            if ($('#id').val() > 0) {
                $('#ajax-content').html('');
                $('#loader').show();

                var element = $('#id').val();
                var manager = '<?php echo FORM_CONTROLLER; ?>';
                var menuid = $('#menuid').val();
                var op = 'uploadIcon2';

                var data = new FormData();

                jQuery.each(jQuery('#icon')[0].files, function (i, file) {
                    data.append('file-' + i, file);
                });

                data.append('element', element);
                data.append('manager', manager);
                data.append('menuid', menuid);
                data.append('op', op);

                $.ajax({
                    url: '/ajax.php',
                    type: 'post',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        setTimeout(function () {
                            updateDynamicMenu();
                            $('#icon').prop('value', '');
                            $('#loader').hide();
                            $('#ajax-content').html(data);
                            // Ocultamos el div de confirmación de la carga del ícono
                            setTimeout(function() {
                                $("#ajax-content").fadeOut( "slow" );
                            }, 1500);
                        }, 1000);
                    }
                });
            } else {
                alert('<?php echo trans('no_menu_item_selected') ?>');
            }
        } else {
            alert('<?php echo trans('please_select_an_image') ?>');
        }
    }

    function deleteMenuItem(id) {

        if (confirm("Está seguro que desea eliminar el item seleccionado?")) {
            $('#dynamic-menu').html('<?php echo loader_icon_zindex(); ?>');
            $.ajax({
                url: '/ajax.php',
                type: 'get',
                data: {
                    id: id,
                    op: 'deleteMenuItem'
                },
                success: function() {
                    updateMenu();
                    setTimeout(function() {
                        updateDynamicMenu();
                        $('.overflow-layout').hide();
                        alert('Item eliminado!');
                    }, 1200);

                }
            })
        }
    }

</script>
    <?php
        $menuItems = $this->getMenuItems($_GET['id']);
    ?>
    <div class="card">
        <div class="card-header">
            <h4><?php echo trans('new_menu') ?></h4>
        </div>

        <div class="card-block">
            <div class="left-menu-item">
                <form>
                    <div class="form-group row">
                        <label for="label"
                               class="col-sm-2 col-form-label"><?php echo trans('label')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="label">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="label2"
                               class="col-sm-2 col-form-label"><?php echo trans('label')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="label2">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="label3"
                               class="col-sm-2 col-form-label"><?php echo trans('label')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="label3">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="show_label"
                               class="col-sm-2 col-form-label"><?php echo trans('show_label') ?>?</label>
                        <div class="col-sm-10">
                            <?php generateYesNotSelect('show_label')?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="link"
                               class="col-sm-2 col-form-label"><?php echo trans('link')?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="link">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="link_friendly"
                               class="col-sm-2 col-form-label"><?php echo trans('link_friendly')?></label>
                        <div class="col-sm-10">
                            <!--<input type="text" class="form-control" id="link_friendly"> -->
                            <select id="link_friendly" class="form-select">
                            <?php
                                echo '<option value="0">Ninguno</option>';
                                foreach ($friendlyUrls as $url) {
                                    echo '<option value="' . $url->id . '">' . $url->urlfriendly . '</option>';
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="parent"
                               class="col-sm-2 col-form-label"><?php echo trans('parent') ?></label>
                        <div class="col-sm-10">
                            <select id="parent" class="form-select">
                                <option value="0">Root</option>
                                <?php
                                foreach ($parents as $parent){
                                    ?>
                                    <option value="<?php echo $parent->id ?>"><?php echo $parent->label . ' (' . $parent->position . ') ' ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="active"
                               class="col-sm-2 col-form-label"><?php echo trans('active') ?></label>
                        <div class="col-sm-10">
                            <?php generateYesNotSelect('active')?>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="permision"
                               class="col-sm-2 col-form-label"><?php echo trans('privileges') ?></label>
                        <div class="col-sm-10">
                            <select name="permision" id="permision" class="form-select">
                                <option value="1" selected="selected">Con permisos</option>
                                <option value="0">Sin permisos</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="position"
                               class="col-sm-2 col-form-label"><?php echo trans('position') ?></label>
                        <div class="col-sm-10">
                            <!-- <input type="text" class="form-control" id="position"> -->
                            <select name="position" id="position" class="form-select">
                                <option value="">Posición</option>
                                <?php
                                    for ($i = 0; $i <= $totalItems; $i++ ) {
                                        echo '<option value="' . $i . '">' . $i .'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="target"
                               class="col-sm-2 col-form-label"><?php echo trans('target') ?></label>
                        <div class="col-sm-10">
                            <?php generateYesNotSelect('target')?>
                        </div>
                    </div>
                    <div class="form-group row" id="tricon" style="display: none;">
                        <label for="icon"
                               class="col-sm-2 col-form-label"><?php echo trans('icon') ?></label>
                        <div class="col-sm-10">
                            <input type="file" id="icon" class="form-control"><input type="button" value="<?php echo trans('upload_icon') ?>" onclick="uploadIcon();">
                            <img id="loader" style="vertical-align: top; display: none;" src="images/loader2.gif">
                            <br>
                            <span id="ajax-content"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-10">
                            <input type="hidden" id="selecteditem" value="">
                            <input type="hidden" id="menuid" value="<?php echo $_GET['id'] ?>">
                            <input type="button" value="<?php echo trans('btn_save') ?>" onclick="saveMenuItem();" id="btn" class="btn btn-primary">
                            <?php exit_btn(getUrl('show', $this->urls)); ?>
                            <?php loader_icon();?><br>
                            <span id="message"></span>
                            <input type="hidden" id="id" value="0"> <!-- Se usa cuando estamos editando y hace referencia al id del item de menu-->
                        </div>
                    </div>
                </form>
            </div>

            <div class="right-menu-item" id="dynamic-menu">
                <?php
                    require_once (VIEWS_PATH_CONTROLLER . 'menu_items.html.php');
                ?>
            </div>

            <div class="overflow-layout">
            </div>
        </div>
    </div>
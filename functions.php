<?php
if (isset($_GET['error'])) {
	echo "Activamos errores";
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}
require_once('constants.php');
require_once('conf/site_parameters.php');
include ('vendor/autoload.php');

// Creamos un objeto de tipo WPDB que se utilizará dentro de toda la página
global $wpdb;
global $user;
global $translator;

session_start();

function getDefaultLanguage() {
    $default = 'es';

    if (!isset($_SESSION['lang']) && !isset($_GET['lang'])) {
        $_SESSION['lang'] = $default;
    } else if (isset($_GET['lang'])){
        $_SESSION['lang'] = $_GET['lang'];
        // Go to the last visited page
        header('location: ' . $_SERVER['HTTP_REFERER'] . '');
    }
}

getDefaultLanguage();

$wpdb = new WPDB();
$translator = new Translator();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

/**
 * @param $label
 * @return mixed
 *
 * Obtiene la traducción de un texto
 */

function trans($label, $maxLength = 0) {
    global $translator;
	
    if ($translator->getTrans($label)) {
        return $translator->getTrans($label, $maxLength);
    } else {
        return $label;
    }

}

/**
 * @return mixed
 *
 * Obtiene el nombre de la función que obtendrá el label de un item del menu getLabel, getLabel2 ...
 */
function getLangGetLabelFunction() {
    global $translator;
    $langs = $translator->languages;
    $lang = $_SESSION['lang'];
    $langFunctions = array();
    $sufix = 1;

    foreach ($langs as $key => $value) {
        if ($sufix == 1) {
            $langFunctions[$key] = 'getLabel';
        } else {
            $langFunctions[$key] = 'getLabel' . $sufix;
        }
        $sufix++;
    }

    $labelFunction = method_exists('MenuItem', $langFunctions[$lang]) ? $langFunctions[$lang] : $langFunctions['en'];

    return $labelFunction;

}

function generateSelectMonth($selectedMonth = "", $onchange = true, $function = "", $start = 0) {
    $onchangeFunction = '';
    if ($onchange && $function == "") {
        $onchangeFunction = 'onchange="jQuery(\'#frm1\').submit();"';
    } else if ($function != "") {
        $onchangeFunction = 'onchange="' . $function . '"';
    }
    ?>
    <select id="month" name="month" <?php echo $onchangeFunction; ?> class="form-control">
    	<?php 
    		if ($start == 0) { 
        		echo '<option value="all">Todos</option>';
			}
            
            if (userWithPrivileges()) {
		?>
            <option value="0" <?php echo $selectedMonth == "" ? 'selected="selected"' : ''; ?>><?php echo trans('select_a_month')?></option>
        <?php
            }
            
    	$end = 12;
		
        if ($start > 0) {
        	if ($start > 1) {
        		$auxStart = $start;
        		$start = $start - 1;
				$end = $auxStart;
        	} else {
        		$end = $start;
        	}
        	
        } else {
        	$start = 1;
        }
		
		$resMonth = "";
		
        for($i = $start; $i <= $end; $i++) {
            if ($selectedMonth != "") {
                $resMonth .= '<option value="'.$i.'" '.($selectedMonth == $i ? 'selected="selected"' : '' ).' >'.getMonth($i).'</option>';
            } else {
                if (isset($_POST['month'])) {
                    $resMonth .= '<option value="'.$i.'" '.($_POST['month'] == $i ? 'selected="selected"' : '' ).' >'.getMonth($i).'</option>';
                } else { // Filtro ajax
                    $resMonth .= '<option value="'.$i.'" >'.getMonth($i).'</option>';
                }
            }
        }

		$extraMonths = "";
		
		if (!userWithPrivileges()) {
			$lastYear = date('Y') - 1;
			$auxDate = date('m');
			if ($auxDate < 3) {
				//if (isset($_GET['test'])) {
					if ($auxDate == 1) {
						$extraMonths .= '<option value="11" >'.getMonth(11).' (' . $lastYear . ')</option>';
						$extraMonths .= '<option value="12" >'.getMonth(12).' (' . $lastYear . ')</option>';
					} else {
						$extraMonths .= '<option value="12" >'.getMonth(12).' (' . $lastYear . ')</option>';
					}
				//}
			}
		}
		
		// Si es enero o febrero le concatenamos noviembre y/o diciembre según el caso
		
		$extraMonths .= $resMonth;
		
		echo $extraMonths;
		
        ?>
    </select>
<?php
}

function getProvinces() {
    $provinces = array();
    if (file_exists(JSON_PROVINCES)) {
        $json = file_get_contents(JSON_PROVINCES);
        $provinces = json_decode($json);
    }

    return $provinces;
}

function getProvinceName($id) {
    $provinces = getProvinces();
    foreach ($provinces as $province) {
        if ($province->id == $id) {
            return $province->province;
        }
    }
}



function getStores($onlyidname = false) {
    $stores = array();
    $aux = array();
    if (file_exists(JSON_STORES)) {
        $json = file_get_contents(JSON_STORES);
        $stores = json_decode($json);

        if ($onlyidname) {
            $i = 0;
            foreach ($stores as $store) {
                $aux[$i]['id'] = $store->id;
                $aux[$i]['name'] = $store->storename;
                $i++;
            }

            return $aux;
        }

    }

    return $stores;
}

function getUsers() {
    $aux = file_get_contents(JSON_USERS);
    $users = json_decode($aux);

    return $users;
}

function getCategories($onlyidname = false) {
    $categories = array();
    $aux = array();
    if (file_exists(JSON_CATEGORIES)) {
        $json = file_get_contents(JSON_CATEGORIES);
        $categories = json_decode($json);

        if ($onlyidname) {
            $i = 0;
            foreach ($categories as $category) {
                $aux[$i]['id'] = $category->id;
                $aux[$i]['category'] = $category->category;
                $i++;
            }

            return $aux;
        }

    }

    return $categories;
}

function getStoreName($id) {
    if (file_exists(JSON_STORES)) {
        $json = file_get_contents(JSON_STORES);
        $stores = json_decode($json);

        foreach ($stores as $store) {
            if ($store->id == $id) {
                return $store->storename;
            }
        }
    }
}

function





getZones($idName = false) {
    $zones = array();
    if (file_exists(JSON_ZONES)) {
        $json = file_get_contents(JSON_ZONES);
        $zones = json_decode($json);
    }

    if ($idName) {
        $aux = array();
        foreach ($zones as $zone) {
            $aux[$zone->id] = $zone->zone;
        }

        return $aux;
    }

    return $zones;
}

function loadTemplate($template, $data = array(), $msg = "", $myController = null) {
	if (file_exists($template)) {
		include ($template);
	} else {
		errorMsg("No se ha encontrado la plantilla: " . $template);
	}
}

/**
 * @param int $firstYear
 * @param string $selectedYear
 *
 * SelectedYear se utiliza en las edicciones para indicar el valor que debe de ser seleccionado.
 * Para las opciones de show se toma el valor de $_POST para el año que debe ser seleccionado
 */
function generateSelectYear($firstYear = 2014, $selectedYear = "", $onchange = true) {
    $onchangeFunction = '';
    if ($onchange) {
        $onchangeFunction = 'onchange="jQuery(\'#frm1\').submit();"';
    }
    ?>
    <select id="year" name="year" <?php echo $onchangeFunction; ?> class="form-control">
        <option value="0"><?php echo trans('select_a_year')?></option>
        <?php
        for($i= date('Y'); $i >= $firstYear; $i--) {
            if ($selectedYear != "") {
                echo '<option value="'.$i.'" '.($selectedYear == $i ? 'selected="selected"' : '' ).' >'.$i.'</option>';
            } else {
                if (isset($_POST['year'])) {
                    echo '<option value="' . $i . '" ' . ($_POST['year'] == $i ? 'selected="selected"' : '') . ' >' . $i . '</option>';
                } else { // Filtro ajax
                    echo '<option value="' . $i . '" >' . $i . '</option>';
                }
            }
        }
        ?>
    </select>
<?php
}

/**
 * @param $data
 * @param $name
 * @param $option
 * @param string $selected
 *
 * $data: es el array de objetos con la información
 * $name: nombre para el elemento y también se usa para el id
 * $option: lo que queremos que se muestre en el option
 * $selected: la opción que debe estar seleccionada cuando editamos
 */
function generateObjectSelect($data, $name, $option, $selected = "", $onchange = false, $showInmediatly = true) {
    $className = get_class($data[0]);
    $default = 'Seleccione una opción';
    $options = array('Truck', 'Apartment');
    $optionLabel = array(
        'Truck'     => trans('select_a_truck'),
        'Apartment' => trans('select_an_apartment')
    );

    $onchangeFunction = "";
    if ($onchange) {
        $onchangeFunction = 'onchange="jQuery(\'#frm1\').submit();"';
    }

    $html = '<select name="' . $name . '" id="' . $name . '" ' . $onchangeFunction . ' class="form-control">';
    $html .=    '<option value="0">' . (in_array($className, $options) ? $optionLabel[$className] : $default) . '</option>';

    foreach ($data as $d) {
        if ($d->getId() == $selected) {
            $html .= '<option value="' . $d->getId() . '" selected="selected"> ' . $d->$option . ' </option>';
        } else {
            $html .= '<option value="' . $d->getId() . '"> ' . $d->$option . ' </option>';
        }
    }

    $html .= '</select>';

    if ($showInmediatly) {
        echo $html;
    } else {
        return $html;
    }
}

/**
 * @param $name
 * @param string $selected
 *
 * Genera un select con las opciones No y Si
 */
function generateYesNotSelect($name, $selected = "", $showInmediatly = true) {
    $numbers = array('0' => trans('no_select'), '1' => trans('yes_select'));

    $html = '<select name="' . $name . '" id="' . $name . '" class="form-control">';

        foreach ($numbers as $key => $value) {
            if ($key == $selected && $selected != "") {
                $html .= '<option value="' . $key . '" selected="selected"> ' . $value . ' </option>';
            } else {
                $html .= '<option value="' . $key . '"> ' . $value . ' </option>';
            }
        }

    $html .= '</select>';

    if ($showInmediatly) {
        echo $html;
    } else {
        return $html;
    }
}

function forbidden() {
    ?>
    <div class="forbbiden-img">
        <img src="<?php echo IMAGES_PATH ?>prohibido.jpg">
    </div>
<?php
}

function confirmationMessage($message, $show = true) {
    $msg =   '<div id="message" class="alert alert-success"><p>'.$message.'</p></div>';
    if ($show) {
        echo $msg;
    } else {
        return $msg;
    }
}

function registerDuplicate() {
    echo '<div id="message" class="alert alert-danger">
            <p class="red-color">
                Se ha evitado el doble guardado de datos por una acción de la tecla F5, o la recarga de página o la ya existencia de esta información.
            </p>
            <p>
                Si desea actualizar la página por favor use este botón ' . update_icon2() . '
            </p>
         </div>';
}

/**
 * @param $msg
 * @param bool $show
 * @return string
 *
 * Muestra o retorna un mensaje de error
 */
function errorMsg($msg, $show = true) {
    $message = '<div id="message" class="container alert alert-danger"><p class="red-color">'.$msg.'</p></div>';
    if ($show) {
        echo $message;
    } else {
        return $message;
    }
}

function warningMsg($msg, $show = true, $container = true) {
    $class = "container";
    if (!$container) {
        $class = "";
    }
    $message = '<div id="message" class="' . $class . ' alert alert-warning"><p>'.$msg.'</p></div>';
    if ($show) {
        echo $message;
    } else {
        return $message;
    }
}

function exit_btn($url) {
    ?>
    <button class="btn btn-danger" type="button" onclick="redirect('<?php echo $url; ?>')" style="margin-right: 100px;" value="btn"><?php echo trans('btn_back'); ?></button>
<?php
}

/**
 * @param $data
 * @param string $label
 *
 * Por regla general los nombre del botón serán guardar o actualizar, pero si se pasa un label
 * como parámetro se usará este
 */
function save_update_btn($data, $label = "") {
    if ($label != "") {
        $value = $label;
    } else if($data){
        $value = trans('btn_update');
    } else {
        $value = trans('btn_save');
    }

    // onclick="$('form').submit();"
?>
    <button type="submit" class="btn btn-primary" id="submit"><?php echo $value; ?></button>
<?php
}

function edit_icon($show = true) {
    $icon = '<img src="' . ICONS_PATH . 'edit.png" >';
    if ($show) {
        echo $icon;
    } else {
        return $icon;
    }

}

/**
 * Genera un icono font Awesome
 */
function icon($name, $show = false, $scape = false) {
    $icons = array(
        'edit' => 'fa-pencil-square-o',
        'delete' => 'fa-trash',
        'save' => 'fa-floppy-o',
        'image' => 'fa-file-image-o',
        'user'	=> 'fa-user-circle',
        'logout' => 'fa-power-off',
        'superadmin' => 'fa-user',
        'pdf' => 'fa-file-pdf-o',
        'word' => 'fa-file-word-o',
        'plus' => 'fa-plus',
        'cart' => 'fa-shopping-cart',
        'database' => 'fa-database',
        'info' => 'fa-info',
        'question' => 'fa-question-circle-o',
        'empty' => 'fa-times',
        'send' => 'fa-paper-plane',
        'restore' => 'fa-reply',
        'calendar' => 'fa-calendar',
        'comments' => 'fa-comments',
        'view' => 'fa-eye',
        'home' => 'fa-home',
        'phone' => 'fa-phone',
        'truck' => 'fa-truck',
        'delivered' => 'fa-handshake-o',
        'pending' => 'fa-clock-o',
        'incidence' => 'fa-wrench', // incidencias
        'ok' => 'fa-thumbs-o-up',
        'money' => 'fa-money',
        'map' => 'fa-map-marker',
        'status' => 'fa-recycle',
        'bag' => 'fa-shopping-bag',
        'exchange' => 'fa-exchange',
        'barcode' => 'fa-barcode',
        'checked' => 'fa-check-square-o',
        'email' => 'fa-envelope',
        'search' => 'fa-search',
        'half' => 'fa-hourglass-half',
        'calculator' => 'fa-calculator',
        'sort' => 'fas fa-sort',
    );

    if ($scape) {
        $icon = '<i class=\'fa ' . $icons[$name] . ' fa-fw\'></i>';
    } else {
        $icon = '<i class="fa ' . $icons[$name] . ' fa-fw"></i>';
    }


    if ($show) {
        echo $icon;
    } else {
        return $icon;
    }
}

function clearStringToUpper($string) {
    $string = str_replace("á", "A", $string);
    $string = str_replace("é", "E", $string);
    $string = str_replace("í", "I", $string);
    $string = str_replace("ó", "O", $string);
    $string = str_replace("ò", "O", $string);
    $string = str_replace("ú", "U", $string);
    $string = str_replace("ñ", "Ñ", $string);

    return $string;
}

function spinner_icon($name, $id,  $show = false) {
    $icons = array(
        'refresh' => 'fa-refresh',
        'spinner' => 'fa-spinner',
        'circle' => 'fa-circle-o-notch',
        'cog' => 'fa-cog',
    );

    $icon =  '<span id="' . $id . '" style="display: none;">';
    $icon .=    '<i class="fa ' . $icons[$name] . ' fa-spin fa-3x fa-fw"></i>';
    $icon .=    '<span class="sr-only">Loading...</span>';
    $icon .=  '</span>';

    if ($show) {
        echo $icon;
    } else {
        return $icon;
    }
}

function getCommentsWidget() {
    return include (VIEWS_PATH . "order/comments-widget.html.php");
}

function loader_icon($show = true) {
    $icon = '<img src="' . ICONS_PATH . 'loader2.gif" id="loader" style="vertical-align: top; display: none;">';
    if ($show) {
        echo $icon;
    } else {
        return $icon;
    }
}

function loader_icon_zindex($show = true) {
    $icon = '<img src="' . ICONS_PATH . 'loader2.gif" id="overflow-loader" style="position: absolute; z-index: 3000;">';
    if ($show) {
        echo $icon;
    } else {
        return $icon;
    }
}


// Genera el icono de la opción cargar archivo
function upload_icon() {
    ?>
    <img src="<?php echo ICONS_PATH ?>upload.jpg">
<?php
}

// Genera el icono de la opción descargar archivo
function download_icon() {
    ?>
    <img src="<?php echo ICONS_PATH ?>download.png">
<?php
}

// Genera el icono de la opción pdf
function pdf_icon($show = TRUE) {
    $pdf = '<img src="' . ICONS_PATH . 'pdf.jpg" style="width: 20px;">';
    if ($show) {
        echo $pdf;
    } else {
        return $pdf;
    }
}

// Genera el icono de un archivo
function file_icon($show = TRUE) {
    $file = '<img src="' . ICONS_PATH . 'file_icon.jpeg" style="width: 20px;">';
    if ($show) {
        echo $file;
    } else {
        return $file;
    }
}

// Genera el icono de la opción borrar
function delete_icon() {
    ?>
    <img src="<?php echo ICONS_PATH ?>Trash.png">
<?php
}

// Genera el icono de la opción borrar pero sin visualizarlo inmediatamente (uso para Javascript)
function delete_icon_js() {
    return '<img src="'. ICONS_PATH . 'Trash.png">';
}

function update_icon($url) {
    ?>
    <a href="<?php echo $url; ?>" title="<?php echo trans('update_page'); ?>">
        <img src="<?php echo ICONS_PATH ?>update.jpg">
    </a>
<?php

}

function plus_icon($show = false) {
    $icon = '<img src="' . ICONS_PATH . 'plus-sign-6.png">';

    if ($show) {
        echo $icon;
    } else {
        return $icon;
    }

}

function minus_icon() {
    $icon = '<img src="' . ICONS_PATH . 'minussymbol.jpg">';
    echo $icon;
}

function update_icon2() {
    $icon = '<img src="'. ICONS_PATH . 'update.jpg">';
    return $icon;
}

function done_icon() {
    ?>
    <img src="<?php echo ICONS_PATH ?>done.png" title="Realizado">
<?php

}

function in_progress_icon() {
    ?>
    <img src="<?php echo ICONS_PATH ?>in_progress.gif" title="En progreso">
<?php
}

function cancelled_icon() {
    ?>
    <img src="<?php echo ICONS_PATH ?>cancelled.jpg" title="Cancelado">
<?php
}

function noRecords() {
    ?>
    <span style="padding: 5px;">No se ha encontrado ningún dato!</span><br>
    <img src="<?php echo IMAGES_PATH ?>Sad-face-8.jpg">
<?php

}

function notFound404() {
    ?>
    <div style="padding: 15px;">
        <center>
            <span style="padding: 5px;">Página no encontrada!</span><br>
            <img src="<?php echo IMAGES_PATH ?>Sad-face-8.jpg">
        </center>
    </div>
<?php
}

function getUsername($id) {
    global $wpdb;
    return $wpdb->getOneField($wpdb->prefix.'users', 'username',' WHERE id='. $id);
}

function upload_file($option) {
    // obtenemos los datos del archivo
    $id = $_POST['id'];
    global $wpdb;

    $size = $_FILES["document"]['size'];
    $type = $_FILES["document"]['type'];
    $file = $_FILES["document"]['name'];

    if ($option == 'paysheet') {
        $new_name = 'paysheet_signed_'.$id.'.pdf';
        $path =  "upload/paysheets/".$new_name;
        $query = 'UPDATE '.$wpdb->prefix.'paysheets SET signed_document=1,signed_document_name="'.$new_name.'" WHERE id='.$id;

        $wpdb->query($query);

    } else if($option == 'invoice') {

        $new_name = 'invoice_signed_'.$id.'.pdf';
        $path =  "upload/invoices/".$new_name;
        $query = 'UPDATE '.$wpdb->prefix.'invoices SET signed_document=1,signed_document_name="'.$new_name.'" WHERE id='.$id;

        $wpdb->query($query);

    } else if($option == 'makingpriceimage') {
        if ($_POST['position'] == 'front') {
            $new_name = 'front_making_price_image_'.$id.'.jpg';
            $path =  "<?php echo IMAGES_PATH ?>wear_making_prices_<?php echo IMAGES_PATH ?>".$new_name;
            $query = 'UPDATE '.$wpdb->prefix.'wear_making_prices SET front_image="'.$new_name.'" WHERE id='.$id;
        } else {
            $new_name = 'back_making_price_image_'.$id.'.jpg';
            $path =  "<?php echo IMAGES_PATH ?>wear_making_prices_<?php echo IMAGES_PATH ?>".$new_name;
            $query = 'UPDATE '.$wpdb->prefix.'wear_making_prices SET back_image="'.$new_name.'" WHERE id='.$id;
        }

        $wpdb->query($query);
    }

    if ($file != "") {
        // guardamos el archivo a la carpeta files
        if (copy($_FILES['document']['tmp_name'],$path)) {
            return TRUE;
        } else {
            return FALSE;
        }

    } else {
        return FALSE;
    }

}

function uploadPdf() {
    $allowedFiles = array('pdf');
    $file = $_FILES['file-0'];
    $size = $file['size'];
    $type = $file['type'];

    if ($file) {
        $year = getPostValue('year');
        $month = getPostValue('month');
        $truckId = getPostValue('truck');
        $code = getPostValue('code');
        $manager = getPostValue('manager');

        $fileName = 'extracto_de_' . getMonth($month) . '_del_' . $year . '_vehiculo_' . $code . '.pdf';

        $path = UPLOADED_FILES_PATH;

        if (!file_exists($path)) {
            mkdir($path);
            createProtectionIndexFile($path);
        }

        $path .= '/' . EXTRACTS_DIR;
        createProtectionIndexFile($path);

        if (!file_exists($path)) {
            mkdir($path);
            createProtectionIndexFile($path);
        }

        $path .= '/' . $year;

        if (!file_exists($path)) {
            mkdir($path);
            createProtectionIndexFile($path);
        }

        $path .= '/' . $code;

        if (!file_exists($path)) {
            mkdir($path);
            createProtectionIndexFile($path);
        }

        $path = $path . '/' . $fileName;

        // Obtenemos el tipo de archivo desde la variable type
        $fileType = explode('/', $type);
        // Hay archivos que vienen con la variable type vacía, por este motivo obtenemos la extensión
        $explodeName = explode('.', $file['name']);
        $typeFromName = $explodeName[count($explodeName) - 1];

        // Si el tipo de archivo es pdf o el nombre finaliza con pdf
        if (in_array($fileType[1], $allowedFiles) || in_array($typeFromName, $allowedFiles)) {
            // guardamos el archivo a la carpeta files
            if (copy($file['tmp_name'], $path)) {
                $manager = ucfirst($manager) . 'Controller';
                $manager = new $manager();
                $manager->updatePdfIncomeInDB($fileName, $year, $month, $truckId);

                $link = '<a href="' . $path . '" target="_blank">' . pdf_icon(false) . '<a/>';

                confirmationMessage(trans('file_uploaded_successfully') . $link);
            } else {
                errorMsg(trans('copy_folder_error'));
            }

        } else {
            errorMsg(trans('file_not_allow_only_pdf'));
        }
    } else {
        errorMsg(trans('file_not_uploaded'));
    }
}


function createProtectionIndexFile($path) {
    // Creamos el archivo index.html que protege el directorio de ser listado

    if (!file_exists($path . '/index.php')) {
        $fp = fopen($path . '/index.php', 'w');
        $text = '<?php' . PHP_EOL;
        $text .= '	header("location: ../index.php");' . PHP_EOL;
        $text .= '?>' . PHP_EOL;

        fwrite($fp, $text);
        fclose($fp);
    }

}

function isLocalHost() {
    if ($_SERVER['SERVER_NAME'] == "localhost") {
        return true;
    }
    
    return false;
}

function uploadIcon() {
    $allowedFiles = array('png', 'jpg', 'jpeg');
    $file = $_FILES['file-0'];
    $size = $file['size'];
    $type = $file['type'];

    if ($file) {
        $menuElement = getPostValue('element');
        $manager = getPostValue('manager');

        $path = UPLOADED_MENU_ICONS;

        if (!file_exists($path)) {
            mkdir($path);
        }

        // Obtenemos el tipo de archivo desde la variable type
        $fileType = explode('/', $type);
        // Hay archivos que vienen con la variable type vacía, por este motivo obtenemos la extensión
        $explodeName = explode('.', $file['name']);
        $typeFromName = $explodeName[count($explodeName) - 1];

        $fileName = 'icon_menu' . '_' . $menuElement . '.' . $fileType[1];
        $path = $path . $fileName;


        // Si el tipo de archivo es png o jpg o el nombre finaliza con png|jpg
        if (in_array($fileType[1], $allowedFiles) || in_array($typeFromName, $allowedFiles)) {
            // guardamos el archivo a la carpeta files
            if (copy($file['tmp_name'], $path)) {
                $manager = ucfirst($manager) . 'Controller';
                //$manager = new $manager();
                $menu = new MenuController();
                $menu->updateMenuIconInDB($menuElement, $fileName);

                confirmationMessage(trans('file_uploaded_successfully') . '<img src="' . UPLOADED_MENU_ICONS_PATH . $fileName . '">');
            } else {
                errorMsg(trans('copy_folder_error'));
            }

        } else {
            errorMsg(trans('file_not_allow_only_image'));
        }
    } else {
        errorMsg(trans('file_not_uploaded'));
    }
}

function uploadIcon2() {
    $allowedFiles = array('png', 'jpg', 'jpeg');
    $file = $_FILES['file-0'];
    $size = $file['size'];
    $type = $file['type'];

    if ($file) {
        $menuElement = getPostValue('element');
        $manager = getPostValue('manager');
        $menuId = getPostValue('menuid');

        $path = UPLOADED_MENU_ICONS;

        if (!file_exists($path)) {
            mkdir($path);
        }

        $path .= '/' . 'menu' . $menuId;
        if (!file_exists($path)) {
            mkdir($path);
        }

        $path .= '/';

        // Obtenemos el tipo de archivo desde la variable type
        $fileType = explode('/', $type);
        // Hay archivos que vienen con la variable type vacía, por este motivo obtenemos la extensión
        $explodeName = explode('.', $file['name']);
        $typeFromName = $explodeName[count($explodeName) - 1];

        $fileName = 'icon_menu_' . 'item_' . $menuElement . '.' . $fileType[1];
        $path = $path . $fileName;


        // Si el tipo de archivo es png o jpg o el nombre finaliza con png|jpg
        if (in_array($fileType[1], $allowedFiles) || in_array($typeFromName, $allowedFiles)) {
            // guardamos el archivo a la carpeta files
            if (copy($file['tmp_name'], $path)) {
                $manager = ucfirst($manager) . 'Controller';
                //$manager = new $manager();
                $menu = new MenuController();
                $menu->updateMenuIconInDB2($menuElement, $fileName);
                $pathIcon = UPLOADED_MENU_ICONS_PATH . 'menu' . $menuId . '/' . $fileName;
                confirmationMessage(trans('file_uploaded_successfully') . '<img src="' . $pathIcon . '">');
            } else {
                errorMsg(trans('copy_folder_error'));
            }

        } else {
            errorMsg(trans('file_not_allow_only_image'));
        }
    } else {
        errorMsg(trans('file_not_uploaded'));
    }
}

function is_user_logged_in() {
    if (isset($_SESSION['user']) && is_object($_SESSION['user'])) {
        return TRUE;
    }
    
    return false;
}

/**
 * Esta función sólo da acceso al usuario administrador y de id = 1
 */
function ismaster() {
    if ($_SESSION['user']->getId() == 1) {
        return TRUE;
    }

    return FALSE;
}

function sendMail($to, $subject, $message) {
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Cabeceras adicionales
    $headers .= $to . "\r\n";
    $headers .= 'From: Administrador <ssxgrnpq@pass65.dizinc.com>' . "\r\n";

    mail($to, $subject, $message, $headers);
}

function isadmin() {
   return isSuperAdmin();
}

/**
 * @return bool
 */
function isSuperAdmin() {
	global $user;
	if (!is_object($user)) {
		return false;	
	}
	
	if ($user->getAdmin() == 1) {
	    return true;
    }
    return false;

}

function is_user_deleted($user) {
    $isdeleted = FALSE;

    if ($user->deleted == 1) {
        $isdeleted = TRUE;
    }

    return $isdeleted;
}

/*
 * Genera los datos post básicos para restaurar información borrada
 */
function getRestorePostData($id = 0) {

    $_POST['id'] = ($id > 0 ? $id : $_GET['id']);
    $_POST['deleted'] = 0;
    $_POST['deleted_on'] = '0000-00-00 00:00:00';
    $_POST['deleted_by'] = 0;
    $_POST['deleted_reason'] = "";
}

/**
 * @param $text
 * @return mixed
 *
 * Aplica un estilo Bold a las fechas y color de texto azul
 */
function applyStyleToDateFormat($text) {
    $datePattern = '/(\d{1,2}\/)(\d{1,2}\/)(\d{2,4})/';

    return preg_replace($datePattern,
        "<b class='mark-found-date'>$1$2$3</b>", $text);
}

function searchDatePattern($date) {
    $isDate = false;
    $datePattern = '/(\d{1,2}\/)(\d{1,2}\/)(\d{2,4})/';
    $datePattern2 = '/(\d{2,4})-(\d{1,2})-(\d{1,2})/';
    $datePattern3 = '/(\d{1,2}).(\d{1,2}).(\d{2,4})/';

    if (preg_match($datePattern, $date) || preg_match($datePattern2, $date) || preg_match($datePattern3, $date)){
        $isDate = true;
    }

    return $isDate;
}

/**
 * @param $text
 * @param string $class
 * @param string $target
 * @return mixed
 *
 * Remarca un texto encontrado
 */
function markFoundTest($text, $target) {
    return str_ireplace($target,
        "<span class='remark-found-text'>" . $target . '</span>', $text);
}

/**
 * @param $text
 * @param string $class
 * @param string $target
 * @return mixed
 *
 * Busca dentro de un texto si existe algún link y lo muestra como tal
 */
function make_links($text, $class='', $target='_blank'){
    return preg_replace('!((http\:\/\/|ftp\:\/\/|https\:\/\/)|www\.)([-a-zA-Zа-яА-Я0-9\~\!\@\#\$\%\^\&\*\(\)_\-\=\+\\\/\?\.\:\;\'\,]*)?!ism',
        "<a class='".$class."' href='//$3' target='".$target."'>$1$3</a>", $text);
}


/**
 * @param $name
 * @return int
 *
 * devuelve el valor de POST indicado como nombre
 */
function getPostValue($name) {
    $value = 0;
    if (isset($_POST[$name])) {
        $value = $_POST[$name];
    }

    return $value;
}

/**
 * @param $name
 * @return int
 *
 */
function getGetValue($name) {
    $value = 0;
    if (isset($_GET[$name])) {
        $value = $_GET[$name];
    }

    return $value;
}

/**
 * @param $month
 * @param $year
 * @return string
 *
 * Retorna el filtro de búsqueda por mes y año
 * Si trueDate es TRUE el filtro de fecha tendrá el formato yyyy-mm-dd en caso contrario
 * será una cadena month='mm' AND year='yyyy'
 *
 */
function getFilterDate($month, $year, $trueDate = FALSE, $selector = "", $fieldname = "created_on", $allyear = true) {

    if ($trueDate) {
        if ($month > 0 && $year > 0) {
            $filterdate = ' AND  ' . $selector . '.' . $fieldname . ' BETWEEN "' . $year . '-' . $month . '-1" AND "' . $year . '-' . $month . '-31" ';
        } else if ($month > 0 && $year == 0) {
            $filterdate = ' AND  ' . $selector . '.' . $fieldname . ' BETWEEN "' . date('Y') . '-' .$month . '-1" AND "' . date('Y') . '-' . $month . '-31" ';
        } else if ($month == 0 && $year > 0) {
            $filterdate = ' AND ' . $selector . '.' . $fieldname . ' BETWEEN "' . $year . '-1-1" AND "' . $year . '-12-31" ';
        } else {
            if ($allyear) {
                $filterdate = ' AND  ' . $selector . '.' . $fieldname . ' BETWEEN "' . date('Y') . '-1-1" AND "' . date('Y') . '-12-31" ';
            } else {
                $filterdate = ' AND  ' . $selector . '.' . $fieldname . ' BETWEEN "' . date('Y') . '-' . date('m') . '-1" AND "' . date('Y') . '-' . date('m') . '-31" ';
            }
         }
    } else {
        if ($month > 0 && $year > 0) {
            $filterdate = ' AND month=' . $month . ' AND year=' . $year . ' ';
        } else if ($month > 0 && $year == 0) {
            $filterdate = ' AND month=' . $month . ' AND year=' . date('Y') . ' ';
        } else if ($month == 0 && $year > 0) {
            $filterdate = ' AND year=' . $year . ' ';
        } else {
            $filterdate = ' AND year=' . date('Y') . ' ';
        }
    }

    return $filterdate;
}

if (!function_exists('filterpost')) {
    function filterpost(){ // Filtra una variable $_POST, sacando los index como action
        $notarray = array('action', 'opt', 'id', 'op', 'controller', 'show'); // No se deben tomar como variables de la bbdd

        $new = array();

        foreach ($_POST as $key => $value){
            if(!in_array($key, $notarray)){
                $new[$key] = $value;
            }
        }

        return $new;
    }
}

function getDayWeek($id) {
    $days = array(
        1=> 'Lunes',
        2=> 'Martes',
        3=> 'Miércoles',
        4=> 'Jueves',
        5=> 'Viernes',
        6=> 'Sábado',
        7=> 'Domingo'
    );
    return $days[$id];
}

function redirectToIndex($msg = "") {
    echo '<script>';
    if ($msg != "") {
        echo 'alert("' . $msg . '");';
    }
    echo    'location.href="' . HTTP_HOST . '"';
    echo '</script>';
}

if (!function_exists('getMonth')) {
    function getMonth($id) {
        if ($id < 10 && strlen($id) == 2) { // Filtro para quitar el 0 de los números menores a 10 (cuando se obtiene de date('m'))
            $substr = substr($id, 1,2);
        } else {
            $substr = $id;
        }

        return trans(
            array(
                0 => 'months',
                1 => $substr
            )
        );
    }
}

if (!function_exists('numberFormat')) {
    function numberFormat($number, $milesSeparator = TRUE, $decimals = 0) {
        if ($milesSeparator) {
            return number_format($number, $decimals, ',', '.');
        } else {
            return number_format($number, $decimals, '', '');
        }
    }
}

function numberFormatDecimals($number) {
    return str_replace(',', '.', $number);
}

function formatNumberToDB($number) {
    $number = str_ireplace(".", "", $number);
    $number = str_ireplace(",", ".", $number);

    return $number;
}

if (!function_exists('pre')) {
    function pre($data){
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

/**
 * Función americaDate
 * Devuelve una fecha en el formato d/m/Y
 * Si el parámetro $time es TRUE, también se recibe la hora
 *
 */
if (!function_exists('americaDate')) {
    function americaDate($date, $time = TRUE) {
        $lang = $_SESSION['lang'];

        if (!$lang || $lang == 'es') {
            if ($date != '0000-00-00 00:00:00') {
                return date('d/m/Y' . ($time ? ' H:i:s' : '') . '', strtotime($date));
            } else {
                return '';
            }
        } else if ($lang == 'en') {
            if ($date != '0000-00-00 00:00:00') {
                return date('Y-m-d' . ($time ? ' H:i:s' : '') . '', strtotime($date));
            } else {
                return '';
            }
        } else if ($lang == 'de') {
            if ($date != '0000-00-00 00:00:00') {
                return date('d.m.Y' . ($time ? ' H:i:s' : '') . '', strtotime($date));
            } else {
                return '';
            }
        }
    }
}

/**
 * @param $date1
 * @param int $timeOut
 * @return bool
 *
 * 3600 una hora
 */
function isTimeOver($date1, $timeOut = 3600) {
    $now = strtotime(date('d-m-Y H:i:s'));
    $datePlusOneHour = strtotime($date1) + $timeOut;

    if ($now > $datePlusOneHour) {
        return true;
    }

    return false;
}

function unsetMenu() {
    unset($_SESSION['menu']);
}

/**
 * Función  from_calendar_to_date
 * convierte una fecha de calendario datepicker() a una fecha sql Y-m-d h:i:s
 *
 */
function from_calendar_to_date($calendar) {
    $lang = $_SESSION['lang'];

    if ($lang == 'es') {

        return implode('-', array_reverse(explode('/', $calendar))) . ' 00:00:00';
    } else if($lang == 'de') {

        return implode('-', array_reverse(explode('.', $calendar))) . ' 00:00:00';
    } else if ($lang == 'en'){

        return $calendar;
    }
}

function datePicker($ids, $libraries = true)
{
    if ($libraries) { ?>
        <link rel="stylesheet" href="/css/jquery-ui-datepicker.css"/>
        <script src="/js/jquery-ui-datepicker.js"></script>
        <?php
    }
    ?>
    <script>
        <?php
        if (is_array($ids)) {
            foreach ($ids as $id) {?>
                jQuery(function () {
                    jQuery("#<?php echo $id; ?>").datepicker();
                    jQuery.datepicker.regional['es'] = regionalDatePicker; // Variable en functions.js
                    jQuery.datepicker.setDefaults($.datepicker.regional['es']);
                });
            <?php
            }
        } else {
?>
        jQuery(function () {
            jQuery("#<?php echo $ids; ?>").datepicker();
            jQuery.datepicker.regional['es'] = regionalDatePicker; // Variable en functions.js
            jQuery.datepicker.setDefaults($.datepicker.regional['es']);
        });
<?php
        }
    ?>
    </script>
<?php

}

function truncateText($string, $limit, $break=".", $pad="...") {
    // return with no change if string is shorter than $limit
    if(strlen($string) <= $limit)
        return $string;
// is $break present between $limit and the end of the string?
    if(false !== ($breakpoint = strpos($string, $break, $limit))) {
        if($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }
    return $string;
}


function convert_post_to_data(){
    $std = new stdClass();

    foreach ($_POST as $key => $var) {
        $std->$key = $var;
    }

    return $std;
}

function getPdfTableStyle() {
    /*Estilos */
    //$th = 'background-color: #83AEC0;
    $th = 'background-color: #333333;
	    border-bottom: 1px solid #558FA6;
	    border-right: 1px solid #558FA6;
	    color: #FFFFFF;
	    font-size: 8px;
	    padding: 4px;
		text-align: center;';

    $trmodo1 = 'background-color: #F9F9F9;
	    color: #34484E';

    $tdmodo1 = 'border-bottom: 1px solid #A4C4D0;
	    border-right: 1px solid #A4C4D0;
	    padding: 4px; font-size: 10px;';

    $trmodo2 = 'background-color: #FDFDF1;
	    color: #34484E; font-size: 10px;';

    $tdmodo2 = ' border-bottom: 1px solid #EBE9BC;
	    border-right: 1px solid #EBE9BC;
	    padding: 4px;';

    $trtotal = 'background-color: gainsboro;
	    color: #34484E;
	    font-size: 12px;
	    font-weight: bold;
	    text-align: left;';

    $tdtotal = ' border-bottom: 1px solid #EBE9BC;
	    border-right: 1px solid #EBE9BC;
	    padding: 5px; text-align: right;';

    $tdtotalval = ' border-bottom: 1px solid #EBE9BC;
	    border-right: 1px solid #EBE9BC;
	    padding: 5px; text-align: center;';


    $styles = new stdClass();

    $styles->th = $th;
    $styles->trmodo1 = $trmodo1;
    $styles->tdmodo1 = $tdmodo1;
    $styles->trmodo2 = $trmodo2;
    $styles->tdmodo2 = $tdmodo2;

    return $styles;
}

/**
 * @param int $lenth
 * @return string
 *
 * Función que genera una contraseña segura. Por lo menos debe de contener un número y un caracter especial
 */
function securekeyGenerator($lenth = 10) {
    $array = array();
    // Se utilizará este array en caso de que en la generación aleatoria no se haya escogido un symbolo seguro
    $securedChars = array('!','&','@','#','(',')','[',']','-','_','.');
    // Se utilizará este array en caso de que en la generación aleatoria no se haya escogido un número
    $numbers = array('0','1','2','3','4','5','6','7','8','9','0');
    // Se utilizará este array en caso de que en la generación aleatoria no se haya escogido una letra mayúscula
    $caps = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    // Array principal que generará los valores aleatorios
    $symbols = array(
        'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
        '0','1','2','3','4','5','6','7','8','9','!','&','@','/','(',')','[',']','-','_','.','A','B','C','D',
        'E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    );

    // Generamos los primeros valores
    for ($i = 0; $i < $lenth; $i++) {
        $rand = rand(0,73);
        $previous = $symbols[$rand];
        $array[$i] = $previous;

    }

    $secured = true;

    // Recorremos el primer array en búsqueda del algún caracter especial
    for($i = 0; $i < count($array); $i++) {
        if (!in_array($array[$i], $securedChars)) {
            $secured = false;
        }
    }

    // Tamaño de array
    $nArray = count($array)-1;
    // Tamaño de caracteres seguros
    $nseCuredChars = count($securedChars)-1;

    // Index aleatorio que se sustituirá dentro del array
    $indexArray =  rand(0, $nArray);
    // Valor aleatorio que se dará al array
    $randSecureChar = rand(0, $nseCuredChars);

    // Si no se ha encontrado un caracter especial lo cambiamos
    if(!$secured) {
        $secChar = $securedChars[$randSecureChar];
        $array[$indexArray] = $secChar;
    }

    // Inicializamos $secured a false para comprobar si existe un número en el array
    $secured = false;

    for($i = 0; $i < count($array); $i++) {
        if (in_array($array[$i], $numbers)) {
            $secured = true;
        }
    }

    // Si no hay un número dentro del array
    if (!$secured) {
        // Si la posición donde queremos insertar el número es la misma a la del caracter especial debemos de buscar otra.
        do {
            $randPos =  rand(0, (count($array)-1));
        } while($randPos == $indexArray);

        $array[$randPos] = $numbers[rand(0, (count($numbers)-1))];
    }

    // Inicializamos $secured a false para comprobar si existe un caracter en Mayúscula
    $secured = false;

    for($i = 0; $i < count($array); $i++) {
        if (in_array($array[$i], $caps)) {
            $secured = true;
        }
    }

    // Si no hay una mayuscula dentro del array
    if (!$secured) {
        echo 'Debo de usar la generación de mayuscula<br>';
        // Si la posición donde queremos insertar el número es la misma a la del caracter especial debemos de buscar otra.
        do {
            $randPos2 =  rand(0, (count($array)-1));
        } while($randPos2 == $indexArray);

        $array[$randPos2] = $caps[rand(0, (count($numbers)-1))];
    }

    return implode('', $array);
}

/**
 * @param $data
 * @param $model
 * @return array
 *
 * Función que convierte un stdClass de la BBDD a un objeto indicado en el parámetro model.
 * Convierte tanto un stdClass simple como un array stdClass. Model se pasa como string sin los
 * paréntesis y la función se encargará de instanciar una clase del modelo. new $model()
 */
function persist($data, $model) {
    // Para un elemento y que sea stdClass
    //if ($data) { // NO usar esto porque da errores a la hora de generar el menu
        if (!is_array($data)) {
            $model = new $model();
            foreach ($data as $key => $value) {
                if (property_exists($model, $key)) {
                    $key = str_replace('_', '', $key);
                    $userArray = array($model, "set" . ucfirst($key));
                    $arrValue = array($value);
                    call_user_func_array($userArray, $arrValue);
                }
            }

            return $model;

        } else if (is_array($data)) {
            // Para array de 1 o más elementos
            $objects = array();
            foreach ($data as $da) {
                $model = new $model();
                foreach ($da as $key => $value) {
                    if (property_exists($model, $key)) {
                        $key = str_replace('_', '', $key);
                        $userArray = array($model, "set" . ucfirst($key));
                        $arrValue = array($value);
                        call_user_func_array($userArray, $arrValue);
                    }
                }
                array_push($objects, $model);
            }

            return $objects;
        }
    //}

    return false;
}

/**
 * @param $url
 * @return bool
 *
 *  Permite conocer si un usuario tiene acceso a una sección de la aplicación
 */
function canIUseTheController($url) {
    if (isSuperAdmin()) {
        $can = true;
    } else {
        $controller = new MenuController();
        $can = $controller->canIUseTheController($url);

        if (!$can) {
            echo errorMsg(trans('not_privileges'));
        }
    }

    return $can;
}

function userWithPrivileges() {
	global $user;
	if (isadmin() || $user->getUsermanager() == 1 || $user->getUseraccounting() == 1 || $user->getUserrepository() == 1) {
		return true;
	}
	
	return false;
}

function getPermissionsObject()
{
    $permissions = array();
    if (file_exists(PERMISSIONS_FILE)) {
        $json = file_get_contents(PERMISSIONS_FILE);
        $permissions = json_decode($json);
    }

    return $permissions;
}

function friendlyUrlsStatus() {
    if (file_exists(CONFIG_FRIENDLY_URLS)) {
        $json = json_decode(file_get_contents(CONFIG_FRIENDLY_URLS));
        return $json->status;
    }

    return "OFF";
}

function getUrlType($id) {
    global $urlTypes;

    return $urlTypes[$id];
}

/**
 * @param string $controller
 * @return array|mixed
 *
 * Obtenemos un array con todas las urls-friendly.
 * Si pasamos el parámetro controller sólo obtenemos las de ese controlador
 */
function getFriendlyUrls($controller = "") {
    $urls = array();
    if (file_exists(FRIENDLY_URLS)) {
        $json = file_get_contents(FRIENDLY_URLS);
        $jsonUrls = json_decode($json);

        if ($controller != "") {
            foreach ($jsonUrls as $url) {
                if ($url->controllername == $controller) {
                    $urls[] = $url;
                }
            }
        } else {
            $urls = $jsonUrls;
        }
    }

    return $urls;
}

/**
 * @param $type
 * @param $class
 * @return mixed
 *
 * Obtenemos la url-friendly por tipo.
 */
function getFriendlyByType($type, $class, $edit = false) {
    global $urlTypes;
    $idType = -1;
    $friendlyUrls = getFriendlyUrls($class);

    // Obtenemos el id del tipo (posición en el array). Se configura en site_parameters
    foreach ($urlTypes as $key => $value) {
        if ($type == $value) {
            $idType = $key;
            break;
        }
    }

    // Obtenemos la friendly a partir del tipo
    foreach ($friendlyUrls as $url) {
        if ($url->type == $idType) {
            if ($edit) {
                return $url->urlfriendlyedit;
            }

            return $url->urlfriendly;
        }
    }
}

/**
 * @param $name
 * @param $obj
 * @param int $i
 * @return mixed
 *
 * Devuelve la url que puede ser controlador o friendly + id (si es el caso)
 */
function getUrl($name, $obj, $id = 0) {
    $thisId = "";
    $msg = "No se ha configurado correctamente el index ($name) en el array de urls del controlador.";
    if (friendlyUrlsStatus() == "ON") {
        if (isset($obj[$name]['friendly']) && $obj[$name]['friendly'] != "") {
            if ($id != 0) {
                $thisId = $id . "/";
            }

            return $obj[$name]['friendly'] . $thisId;

        } else {
            if ($id != 0) {
                $thisId = $id;
            }
            if (isset($obj[$name]['controller'])) {
                return $obj[$name]['controller'] . $thisId;
            } else {
                return getJavascriptAlertMsg($msg);
            }
        }
    } else {
        if ($id != 0) {
            $thisId = $id;
        }

        if (isset($obj[$name]['controller'])) {
            return $obj[$name]['controller'] . $thisId;
        } else {
            return getJavascriptAlertMsg($msg);
        }
    }
}

function getJavascriptAlertMsg($msg) {
    return 'javascript:alert(\'' . $msg . '\');';
}

/**
 * @return int|mixed
 *
 * Nos permite obtener el id a partir de el REQUEST_URI (Cuando estamos usando urls amigables)
 */
function getIdFromRequestUri() {
    if (!isset($_GET['id'])) {
        $uri = explode("/", $_SERVER["REQUEST_URI"]);
        $aux = array();
        foreach ($uri as $ur) {
            if ($ur) {
                if ($ur != "") {
                    $aux[] = $ur;
                }
            }
        }

        if (isset($aux[1])) {
            $_GET['id'] = $aux[1];
        }
    }
}

function loadMenu() {
    $menu = new MenuController();
    // isset($_GET['lang']): necesario actualizar el menú si se cambia de idioma
    //$menu->createMenu();
    if (!isset($_SESSION['menu']) || isset($_GET['lang']) || isset($_SESSION["incompletemenu"])) {
        $menu->createMenu();
    } else {
        $menu->createMenu();
        //$menu->showSessionMenu();
    }

}

/**
 * $delete = si queremos mostrar un enlace para borrar los archivos
 * Funcion usada en varios contextos
 *
 * $config: configuraciones especiales
 * $config['divresponse'] div en el que debemos mostrar la respuesta
 * $config['excludes'] archivos que queremos excluir
 */
function listDirectory($path, $delete = false, $config = array('divresponse' => '', 'excludes' => array())) {
    $notAlloweds = array('.', '..', 'index.php');
    $icons = array(
        'rtf' => 'word',
        'doc' => 'word',
        'docx' => 'word',
        'pdf' => 'pdf',
        'png' => 'image'
    );

    $cont = 0;
	$excludes = 0;
    if (file_exists($path)) {
        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if (!in_array($file, $notAlloweds)) {
            	$fname = $file;

            	$ext =  getFileExtension($fname);
                $file = $path . '/' . $fname;

                // Es posible que queramos excluir archivos
                if (!in_array($fname, $config['excludes'])) {
                    if (array_key_exists($ext, $icons)) {
                        $qtip = false;
                        $type = $icons[$ext];
                    } else {
                        $qtip = true;
                        $type = 'image';
                    }

                    echo '<span id="' . $config['divresponse'] . $cont . '">';
                    if ($qtip) {
                        echo '<a href="' . $file . '" target="_blank" title="<img src=\'' . $file . '\'>" class="withqtip-no-close">' . icon($type, false) . '</a>';
                    } else {
                        echo '<a href="' . $file . '" target="_blank">' . icon($type, false) . '</a>';
                    }

                    if ($delete) {
                        $onclick = 'onclick="unlinkFile(\'' . $file . '\', \'' . $path . '\', \'' . $fname . '\',  \'' . $config['divresponse'] . '\', \'' . $config['divresponse'] . $cont . '\')"';
                        echo '<a  target="_blank" style="cursor: pointer;" ' . $onclick . ' title="Eliminar">' . delete_icon_js() . '</a><br>';
                    }
                    echo '</span>';

                    $cont++;
                } else {
                	$excludes++;
                }
            }
        }
    }

    if ($cont == 0 && $excludes == 0) {
        echo 'No se han encontrado archivos!';
    }
}

function getFileExtension($fname) {
    $aux = explode('.', $fname);
    $ext = count($aux) - 1;

    return $aux[$ext];

}

function getDirectoryImages($path) {
    $notAlloweds = array('.', '..', 'index.php');
	$images = [];

    if (file_exists($path)) {
        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if (!in_array($file, $notAlloweds)) {
            	/*$fname = $file;
                $file = $path . '/' . $fname;
                echo '<a href="' . $file . '" target="_blank" title="'. $fname .'">' . file_icon(false) . '</a>';
                if ($delete) {
                    $onclick = 'onclick="unlinkFile(\'' . $file .'\', \''. $path . '\', \''. $fname . '\' )"';
                    echo '<a  target="_blank" style="cursor: pointer;" '. $onclick .' title="Eliminar">' . delete_icon_js() . '</a><br>';
                }*/
                $images[] = $file;
            }
        }
    }
	
	return $images;
}

/**
 * @param $path
 * @return array
 *
 * Devuelve un array con todos los arvhivos .php de un directorio
 */
function getFilesFromDirectory($path) {
    $filesToFind = '.php';
    $files = [];

    if (file_exists($path)) {
        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if (strpos($file, $filesToFind) !== false) {
                $files[] = $file;
            }
        }
    }

    return $files;
}

/**
 * Control de inactividad. Si el usuario permanece más de 30 minutos inactivo la cuenta se cerrará automáticamente
 */
function inactiveTimeOutControl() {
if (isset($_SESSION['user'])) {
    ?>
<script src="/js/timeout.js"></script>
    <?php
	}
}

function friendlyUrlsJs() {
?>
<script>
    var urlfriendlystatus = '<?php echo friendlyUrlsStatus(); ?>';
</script>
<?php
}

function createCookie($cname, $cvalue, $exdays = 30) {
	//$cookieTime = time()+60*60*24*30;
?>

<script>
  var d = new Date();
  d.setTime(d.getTime() + (<?php echo $exdays; ?>*24*60*60*1000));
  var expires = "expires="+ d.toUTCString();
  document.cookie = "<?php echo $cname ?>" + "=" + <?php echo $cvalue; ?> + ";" + expires + ";path=/";
</script>

<?php
}

?>
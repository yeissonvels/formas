<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 4/04/16
 * Time: 13:23
 */

class Translator {
    public $languages;
    protected $lang;
	protected $dictionary;

    function __construct() {
        $this->lang = $_SESSION['lang'];
        // 0: english, 1: spanish, 2: german
        $this->languages = array(
            'en' => 0,
            'es' => 1,
            'de' => 2
        );
		
		// Seteamos el diccionario. Si no está en la sesión lo guardamos
		if (!isset($_SESSION['dictionary'])) {
			$dictionary = $this->generateDictionary();
			$_SESSION['dictionary'] = $dictionary;
			$this->dictionary = $dictionary;
		} else {
			$this->dictionary = $_SESSION['dictionary'];
		}
		
    }

    /**
     * @param $label
     * @param int $substrEnd (si queremos cortar la salida del texto) getTrans('product', 4) = prod
     * @return string
     */
    function getTrans($label, $substrEnd = 0) {
       	$dictionary = $this->dictionary;
        $lang = $this->languages[$this->lang];

        if (is_array($label)) {
            // En la posición 0 obtengo el primer indice, en 1 el segundo indice, y lang
            // me da la posición del último indice
            return $dictionary[$label[0]][$label[1]][$lang];

        } else {
            //echo $lang;
            if ($substrEnd > 0) {
                return substr($dictionary[$label][$lang], 0, $substrEnd) . '.';
            } else {
                return $dictionary[$label][$lang];
            }
        }
    }
	
	/**
	 * Devolvemos un array de traducciones
	 */
	function generateDictionary() {
		 // 0: english, 1: spanish, 2: german
        $dictionary = array(
            'method_doesnt_exist' => array(
                "Error: It doesn't exist the method ",
                "Error: no existe el método ",
                "Fehler: Das Metod nicht besteht "
            ),
            'not_privileges' => array(
                "You don't have enough access privileges!",
                'No tiene privilegios suficientes para realizar esta acción!',
                'Du hast nicht genügend rechte um diese Option auszuführen'
            ),
            'controller_doesnt_exist' => array(
                "Error: It doesn't exist the controller",
                "Error: no existe el controlador ",
                "Fehler: Treiber nicht gefunden ",
            ),
            'hello' => array('Hello', 'Hola', 'Hallo', 'Salut'),
            'search' => array('Search', 'Buscar', 'Suchen'),
            'input_search_label' => array('text to find', 'Búsqueda', 'Suche'),
            'search_field_required' => array(
                'Please write the text to find',
                'Por favor complete el campo de búsqueda',
                'Füllen Sie bitte das Suchfeld'
            ),
            'new' => array('New', 'Nuevo', 'Neu'),
            'new_f' => array('New', 'Nueva', 'Neue'),
            'edit' => array('Edit', 'Editar', 'Bearbeiten'),
            'delete' => array('Delete', 'Borrar', 'Löschen'),
            // Buttons
            'btn_save' => array('Save', 'Guardar', 'Speichern'),
            'btn_update' => array('Update', 'Actualizar', 'Aktualisieren'),
            'update_page' => array('Update page', 'Actualizar página', 'Seite aktualisieren'),
            'btn_back' => array('Exit', 'Salir', 'Zurück'),
            'login_error' => array(
                'User or password wrong!',
                'Usuario o contraseña incorrecta!',
                'Benutzer oder Passwort falsch!'
            ),
            'account_inactive' => array('Account inactive!', 'Cuenta de usuario inactiva!', 'Inaktives Konto!'),
            // Database
            'db_data_saved' => array('Data saved!', 'Datos guardados!', 'Die Daten wurden gespeichert!'),
            'db_data_updated' => array('Data updated!', 'Datos actualizados!', 'Die Daten wurden aktualisiert!'),
            'db_data_deleted' => array('Data deleted', 'Datos borrados!', 'Die Daten wurden gelöscht!'),
            'db_conection_failed' => array(
                'Conection failed: ',
                'No se pudo conectar: ',
                'Database Verbindung nicht möglich: '
            ),
            'db_select_db_failed' => array('Select DB failed: ', 'Error al seleccionar la base de datos: '),
            // Forms and list
            'required_fields' => array(
                'Please complete the required fields!',
                'Por favor complete todos los campos marcados en rojo!',
                'Bitte füllen Sie die Pflichtfeld!'
            ),
            'select_a_year' => array('Choice a year', 'Seleccione un año', 'Jahr auswählen'),
            'select_a_month' => array('Choice a month', 'Seleccione un mes', 'Monat auswählen'),
            'select_a_truck' => array('Choice a truck', 'Seleccione un camión', 'Lastwagen auswählen'),
            'yes' => array('Yes', 'Si', 'Ja'),
            'yes_select' => array('Yes', 'Si', 'Ja'),
            'no_select' => array('No', 'No', 'Nein'),
            'no' => array('No', 'No', 'Nein'),
            'upload_pdf' => array('Upload pdf', 'Cargar pdf', 'PDF hochladen'),
            'new_files' => array('New files', 'Nuevos archivos'),
            'upload_file' => array('Upload file (pdf or image)', 'Cargar archivo (pdf o imagen)', 'PDF hochladen (pdf o imagen)'),
            'income' => array('Income', 'Ingreso', 'Einkommen'),
            'incomes' => array('Incomes', 'Ingresos', 'Einkommen'),
            'truck_incomes' => array('Truck incomes', 'Extractos camión', 'Lastwagen Einkommen'),
            'new_truck_income' => array('New truck extract', 'Nuevo extracto camión', 'Neues Lastwagen Einkommen'),
            'edit_truck_income' => array('Edit truck extract', 'Editar extracto camión', 'Lastwagen Einkommen bearbeiten'),
            'year' => array('Year', 'Año', 'Jahr'),
            'month' => array('Month', 'Mes', 'Monat'),
            'truck' => array('Truck', 'Camión', 'Lastwagen'),
            'brutto_income' => array('Net Income', 'Producido', 'Einkommen'),
            'brutto_income_euros' => array('Inc. €', 'Prod. €', 'Ein. €'),
            'bonus_driver' => array('Bonus driver', 'Bonificación conductor', 'Fahrer Bonus'),
            'bonus_driver_euros' => array('Bon. driv €', 'Boni. cond €', 'Bon. Fahr. €'),
            'bonus_driver_advance' => array('Bonus driver advance', 'Adelanto bonificación conductor', 'Fahrer Bonus Avance'),
            'bonus_driver_advance_short' => array('Bonus advance', 'Adelanto conductor', 'Bonus Avance'),
            'bonus_driver_paid' => array('Bonus driver paid?', 'Bonificación del conductor pagada?', 'Bonus Fahrer bezahlt?'),
            'administration' => array('Administration', 'Administración', 'Verwaltung'),
            'administration_paid' => array('Administration paid?', 'Administración pagada?', 'Verwaltung bezahlt?'),
            'others' => array('Others', 'Otros', 'Sonstiges'),
            'comment' => array('Comment', 'Comentario', 'Kommentar'),
            'note' => array('Note', 'Nota', 'Note'),
            'check_received' => array('Check received?', 'Cheque recibido?', 'Scheck bekommen?'),
            'extract_in_pdf' => array('Extract (pdf)', 'Extracto físico (pdf)', 'Einkommen (pdf)'),
            'total_gross' => array('Total gross', 'Total bruto', 'Total Brutto'),
            'total_costs' => array('Total costs', 'Total gastos', 'Total Kosten'),
            'total_neto' => array('Total net', 'Total neto', 'Total Neto'),
            'check' => array('Check', 'Cheque', 'Scheck'),
            'by' => array('By', 'Por', 'Von'),
            'created_on' => array('Created on', 'Creado el', 'Erstellungsdatum'),
            'driver_comision_widget' => array(
                'Last paid driver comis.: ',
                'Última comisión cond. pagada:',
                'Letztes bezahltes Fahrer Bonus:'
            ),
            'admon_comision_widget' => array(
                'Last paid admon: ',
                'Última administración pagada: ',
                'Letztes bezahlte Verwaltung: '
            ),
            // Apartment
            'apartments' => array('Apartments', 'Apartamentos', 'Wohnungen'),
            'new_apartment' => array('New apartment', 'Nuevo apartamento', 'Neue Wohnung'),
            'new_apart_show_opt' => array('New', 'Nuevo', 'Neue'),
            'edit_apartment' => array('Edit apartment', 'Editar apartamento', 'Wohnung bearbeiten'),
            'apartment' => array('Apartment', 'Apartamento', 'Wohnung'),
            'code' => array('Code', 'Código', 'Code'),
            'aditional_information' => array(
                'Aditional information (floor, Tower, etc)',
                'Información adicional (Torre, Piso, etc)',
                'Begleitinformation'
            ),
            'address' => array('Address', 'Dirección', 'Adresse'),
            'residential_area' => array('Residential area', 'Urbanización', 'Siedlung'),
            'city' => array('City', 'Ciudad', 'Stadt'),
            'select_an_apartment' => array('Choice an apartment', 'Seleccione un apartamento', 'Wohnung auswählen'),
            'apartment_incomes' => array('Apartment incomes', 'Extractos apartamento', 'Wohnung Einkommen'),
            'new_apartment_income' => array('New income', 'Nuevo extracto', 'Neues Einkommen'),
            'edit_apartment_income' => array('Edit apartment income', 'Editar extracto', 'Einkommen bearbeiten'),
            'document' => array('Document', 'Documento', 'Unterlage'),
            'documents' => array('Documents', 'Documentos', 'Unterlagen'),
            // User
            'id' => array('Id', 'Id', 'Id'),
            'users' => array('Users', 'Usuarios', 'Benutzer'),
            'login' => array('Login', 'Acceder', 'Login'),
            'user' => array('User', 'Usuario', 'Benutzer'),
            'email' => array('Email', 'Email', 'Email'),
            'full_name' => array('Full name', 'Nombre mostrado', 'vollständiger Name'),
            'register_date' => array('Register date', 'Fecha de registro', 'Anmeldungsdatum'),
            'roles' => array('Roles', 'Roles', 'Rollen'),
            'logout' => array('Logout', 'Cerrar sesión', 'Abmelden'),
            'deleted' => array('Deleted', 'Eliminado', 'Gelöscht'),
            'last_login' => array('Last login', 'Última visita', 'Letztes Login'),
            'account_status' => array('Account status', 'Estado de cuenta', 'Konto Status'),
            'active_account' => array('Active account', 'Cuenta activa', 'Aktives Konto'),
            'is_user_deleted' => array('Deleted user', 'Usuario borrado', 'Gelöschter Benutzer'),
            'login_username' => array('Username', 'Nombre de usuario', 'Benutzername'),
            'login_password' => array('Password', 'Contraseña', 'Passwort'),
            'change_password' => array('Change password','Cambiar contraseña', 'Passwort ändern'),
            'passwords_does_not_math' => array(
                'Passwords does not match!',
                'Las contraseñas no coinciden!',
                'Passwörter stimmen nicht überein!'
            ),
            'password' => array('Password', 'Contraseña', 'Passwort'),
            'new_password' => array('New Password', 'Nueva contraseña', 'Neues Passwort'),
            'repeat_password' => array('Repeat password', 'Repita contraseña', 'Passwort wiederholen'),
            'new_user' => array('New user', 'Nuevo usuario', 'Neuer Benutzer'),
            'edit_user' => array('Edit user', 'Editar usuario', 'Benutzer bearbeiten'),
            'password_changed' => array('Password changed correctly!', 'Contraseña cambiada!', 'Das Passwort wurde geändert!'),
            // Menus
            'menu_name' => array('Menu name', 'Nombre', 'Menu Name'),
            'description' => array('Description', 'Descripción', 'Beschreibung'),
            'new_menu' => array('New menu', 'Nuevo menu', 'Neues Menu'),
            'edit_menu' => array('Edit menu', 'Editar menu', 'Menu bearbeiten'),
            'create_items' => array('Create items', 'Crear items', 'Items hinzufügen'),
            'label' => array('Label', 'Etiqueta', 'Label'),
            'show_label' => array('Show label', 'Mostrar etiqueta', 'Label zeigen'),
            'link' => array('Link', 'Enlace', 'Link'),
            'link_friendly' => array('Link friendly', 'Enlace amigable', 'Link friendly'),
            'position' => array('Position', 'Posición', 'Position'),
            'parent' => array('Parent', 'Elemento padre', 'Parent'),
            'privileges' => array('Privileges', 'Permisos', 'Rechte'),
            'permision' => array('Privileges', 'Permisos', 'Rechte'),
            'active' => array('Active', 'Activo', 'Aktiv'),
            'target' => array('Target', 'Target', 'Target'),
            'icon' => array('Icon', 'Icono', 'Icon'),
            'legend' => array('Legend: ', 'Leyenda: ', 'Hilfe: '),
            'more_than_one_profile' => array(
                'if more than one profile is selected they muss be separates with commas (1,2,3).',
                'si se asigna más de un permiso se debe separar por comas: (1,2,3).',
                'wenn mehr als ein Profil gewählt ist, die mussen mit Komma getrennt werden (1,2,3).'
            ),
            'submenus' => array('Submenus', 'Submenus', 'Submenus'),
            'please_select_an_image' => array(
                'Please select an image in png or jpg|jpeg format!',
                'Por favor seleccione una imagen en formato png o jpg|jpeg!',
                'Bitte wählen Sie ein Image in png oder jpg|jpeg Format!',
            ),
            'no_menu_item_selected' => array(
                'Please select a menu item!',
                'Por favor seleccione una opción del menú!',
                'Bitte wählen Sie ein Menu Option aus!'
            ),
            'actual' => array('actual', 'actual', 'aktuell'),
            'upload' => array('Upload', 'Cargar', 'Hochladen'),
            'upload_icon' => array('Upload icon', 'Cargar ícono', 'Icon Hochladen'),
            'no_menu_items_found' => array(
                'No items founds for this menu!',
                'No se han encontrado elementos para este menú!',
                'Keine Menu Items gefunden!'
            ),

            'months' => array(
                1 => array('January', 'Enero', 'Januar'),
                2 => array('February','Febrero', 'Februar'),
                3 => array('March','Marzo', 'März'),
                4 => array('April', 'Abril', 'April'),
                5 => array('May', 'Mayo', 'Mai'),
                6 => array('June', 'Junio', 'Juni'),
                7 => array('July', 'Julio', 'July'),
                8 => array('August', 'Agosto', 'August'),
                9 => array('September', 'Septiembre', 'September'),
                10 => array('October', 'Octubre', 'Oktober'),
                11 => array('November', 'Noviembre', 'November'),
                12 => array('December', 'Diciembre', 'Dezember')
            ),
            // Para el calendario Javascript
            'calendar_month_names' => array(
                0 => "'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'",
                1 => "'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'",
                2 => "'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'July', 'August', 'September', 'Oktober', 'November', 'Dezember'",
            ),
            'calendar_month_names_short' => array(
                0 => "'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'",
                1 => "'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'",
                2 => "'Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'",
            ),
            'calendar_day_names' => array(
                0 => "'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'",
                1 => "'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'",
                2 => "'Sontag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'",
            ),
            'calendar_day_names_short' => array(
                0 => "'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'",
                1 => "'Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'",
                2 => "'Son', 'Mon', 'Die', 'Mit', 'Don', 'Fre', 'Sam'",
            ),
            'calendar_day_names_min' => array(
                0 => "'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'",
                1 => "'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'",
                2 => "'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'",
            ),
            'calendar_date_format' => array(
                0 => 'yy-mm-dd',
                1 => 'dd/mm/yy',
                2 => 'dd.mm.yy',
            ),
            // Ajax
            'copy_folder_error' => array(
                'It happened an error when trying to copy the file',
                'Ocurrió un problema al copiar el archivo a la carpeta!',
                'Fehler: Die Datei wurde nicht kopiert'
            ),
            'file_not_allow_only_image' => array(
                'File not allowed (only png, jpeg or jpg format)',
                'Archivo no permitido (sólo formato png, jpeg o jpg)',
                'Format nicht erlaubt (nur png, jpeg oder jpg)'
            ),
            'file_not_allow_only_pdf' => array(
                'File not allowed (only pdf)',
                'Archivo no permitido (sólo formato pdf)',
                'Format nicht erlaubt (nur pdf)'
            ),

            'file_not_uploaded' => array(
                'Error: The file was not uploaded!',
                'Error: No se ha podido subir el archivo!',
                'Fehler: die Datei wurde nicht hochgeladen!'

            ),
            'file_uploaded_successfully' => array(
                'The file has been uploaded successfully!<br>',
                'Archivo cargado correctamente!<br> ',
                'Die Datei wurde erfolgreich hochgeladen!<br>'
            ),
            // Controladores
            'old_name' => array('Old name', 'Anterior nombre', 'Alter Name'),
            'controllers' => array('Controllers', 'Controladores', 'Kontrollers'),
            'new_controller' => array('New controller', 'Nuevo controlador', 'Neuer Kontroller'),
            'edit_controller' => array('Edit controller', 'Editar controlador', 'Kontroller bearbeiten'),
        );
		
		return $dictionary; 
	}


} 
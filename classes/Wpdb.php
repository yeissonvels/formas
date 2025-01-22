<?php
/**
 * Clase que gestiona la base de datos
 */

class WPDB {
    //private $host;
    //private $user;
    //private $password;
    private $dbname;
    public  $prefix;
    protected $linker;
    public $insert_id;
    protected $defaultTimeZone;
    private $where = array();

    function __construct() {
        //print_r($_SERVER);
        if (LOCALHOST) {
            $conf = new Config(CONFIG_PATH . 'config_localhost.php');
        } else {
            $conf = new Config(CONFIG_PATH . 'config_prod.php');
        }

        $this->dbname = $conf->getDbname();
        $this->prefix = $conf->getPrefix();
        $this->defaultTimeZone = $conf->getDefaultTimeZone();

        date_default_timezone_set($this->defaultTimeZone);

        $this->linker = mysqli_connect($conf->getHost(), $conf->getUser(), $conf->getPassword());

        if (!$this->linker) {
            die('No se pudo conectar: ' . mysqli_error($this->linker));
        } else {
            $bd_selected = mysqli_select_db($this->linker, $this->dbname);
            if (!$bd_selected) {
                die('Error al seleccionar la base de datos: ' . mysqli_error($this->linker));
            }
        }

        mysqli_set_charset($this->linker, "utf8");
    }

    function getDBName() {
        return $this->dbname;
    }

    /**
     * @param $query
     * @return array
     *
     * Obtiene todos los registros de una query
     */
    function get_results($query) {
        $array = array();
        if($result = (mysqli_query($this->linker,$query))
            or die("DB Error: " . mysqli_error($this->linker) . '<br>Query: ' . $query)){
            // Cycle through results
            while($row = mysqli_fetch_object($result)){
                array_push($array, $row);
            }
        }
        return $array;
    }

    /**
     * @param $query
     * @return null|object
     *
     *
     */
    function get_row($query) {
        $result = (mysqli_query($this->linker, $query))
        or die("DB Error: " . mysqli_error($this->linker) . '<br>Query: ' . $query);

        return mysqli_fetch_object($result);
    }

    /**
     * @param $query
     * @return mixed
     *
     * Obtiene un campo de una tabla
     */
    function get_var($query) {
        if($result = (mysqli_query($this->linker,$query))
            or die("DB Error: " . mysqli_error($this->linker) . '<br>Query: ' . $query)) {
            $row = mysqli_fetch_array($result);

            return $row[0];
        }
    }

    /**
     * @param $query
     *
     * Ejecuta una query
     */
    function query($query){
        mysqli_query($this->linker, $query) or die("DB Error: " . mysqli_error($this->linker) . '<br>Query: ' . $query);;

        $this->insert_id = mysqli_insert_id($this->linker);
    }

    /**
     * @param $table
     * @return bool
     *
     * Guarda registros en una tabla
     */
    function save($table, $showmsg = true) {
        $filter = filterpost();
        //$filter = $this->removeMilesSeparator($filter);
        $insert = $this->buildQueryInsert($table, $filter);
        // Para comprobar si el valor ya existe en la BBDD (evitar datos duplicados)
        $select  = $this->buildQuerySelect($table, $filter);

        //Comprobar si el dato ya existe en el sistema
        $comprobate = $this->get_row($select);
        $result = FALSE; // Se inicializa a False la variable que será devuelta si la operación es exitosa por que el dato no existe

        if ($comprobate === NULL) {
            $this->query($insert);
            if ($showmsg) {
                confirmationMessage(trans('db_data_saved'));
            } else {
                $reponse['msg'] = trans('db_data_saved');
                $response['saved'] = 1;
                $response['lastid'] = $this->insert_id;
                return $response;
            }
            $result = TRUE;
        } else {
            if ($showmsg) {
                registerDuplicate();
            } else {
                $response['msg'] = 'Registros duplicados!';
                $response['lastid'] = $this->insert_id;
                $response['saved'] = 0;
                $response['duplicated'] = 1;

                return $response;
            }
        }

        return $result;

    }

    /**
     * @param $table
     * @return bool
     *
     * Actualiza registros de una tabla
     */
    function save_edit($table, $showmsg = true, $where = 'id') {
        $filter = filterpost();
        //$filter = $this->removeMilesSeparator($filter);
        $update = $this->buildQueryUpdate($table, $filter) . ' WHERE ' . $where . ' = "' . $_POST["id"] . '"';
        $select  = $this->buildQuerySelect($table, $filter) . ' AND ' . $where . ' ="' . $_POST["id"] . '"';

        //Comprobar si el dato ya existe en el sistema
        $comprobate = $this->get_row($select);

        $result = FALSE; // Se inicializa a False la variable que será devuelta si la operación es exitosa por que el dato no existe
        if ($comprobate === NULL) {
            //echo $update;
            $this->query($update);
            if ($showmsg) {
                confirmationMessage(trans('db_data_updated'));
            } else {
                $response['updated'] = 1;
                return $response;
            }
            $result = TRUE;
        } else {
            if ($showmsg) {
                registerDuplicate();
            } else {
                $response['msg'] = 'Registros duplicados!';
                $response['duplicated'] = 1;

                return $response;
            }

        }
        return $result;
    }

    /**
     * @param $obj
     * @return string
     *
     * Construye una query para actualizar los datos en la DB
     */
    function buildQueryUpdate($table, $obj) {
        $filter = array('created_on', 'received_on');

        foreach ($obj as $key => $value) {
            if (in_array($key, $filter)) {
                if (searchDatePattern($value)) {
                    $obj[$key] = from_calendar_to_date($value);
                }
            }
        }

        $query  = 'UPDATE '.$table.' SET ';
        $query .= implode(', ', array_map(function ($v, $s) {return $s.'="'.$v.'"';}, $obj, array_keys($obj)));

        return $query;
    }

    /**
     * @param $obj
     * @return string
     *
     * Construye una query para actualizar los datos en la DB
     */
    function buildQuerySelect($table, $obj) {
        $filter = array('created_on', 'received_on');

        foreach ($obj as $key => $value) {
            if (in_array($key, $filter)) {
                if (searchDatePattern($value)) {
                    $obj[$key] = from_calendar_to_date($value);
                }
            }

            // Forzamos para que acepte el valor que viene con formato fecha desde el formulario en la base de datos
            if($key == 'created_on_2') {
                $obj['created_on'] = $obj['created_on_2'];
                unset($obj['created_on_2']);
            }
        }

        $query = 'SELECT * FROM ' . $table . ' WHERE ';
        $query .= implode(' AND ', array_map(function ($v, $s) {return $s.'="'.$v.'"';}, $obj, array_keys($obj)));

        return $query;
    }

    /**
     * @param $obj
     * @return mixed
     *
     * Devuelve un array con las claves (clave1, clave2 ...)
     * y los valores para insertar datos VALUES(valor1, valor2...)
     */
    function buildQueryInsert($table, $obj) {
        $filter = array('created_on', 'received_on');

        foreach ($obj as $key => $value) {
            if (in_array($key, $filter)) {
                if (searchDatePattern($value)) {
                    $obj[$key] = from_calendar_to_date($value);
                }
            }

            // Forzamos para que acepte el valor que viene con formato fecha desde el formulario en la base de datos
            if($key == 'created_on_2') {
                $obj['created_on'] = $obj['created_on_2'];
                unset($obj['created_on_2']);
            }
        }

        $query = 'INSERT INTO ' . $table;
        $query .= '('.implode(', ', array_map(function ($v, $s) {return '' . $s;}, $obj, array_keys($obj))) . ')';
        $query .= 'VALUES ("' . implode('", "', array_map(function ($v, $s) {return '' . $v;}, $obj, array_keys($obj))).'")';
        
        return $query;
    }

    /**
     * @param $table
     * @param $id
     * @return null|object
     *
     * Devuelve una fila
     */
    function getOneRow($table,$id) {
        $query = 'SELECT * FROM ' . $table . ' WHERE id=' . $id . ' LIMIT 1';
        return $this->get_row($query);
    }

    /**
     * @param $table
     * @param string $where
     * @param string $orderby
     * @return array
     *
     * Borra todos los registros de una tabla
     */
    function getAll($table, $where = "", $orderby = "") {
        $query = 'SELECT * FROM ' . $table . ' ' . $where . ' ' . $orderby;
        $data = $this->get_results($query);

        return $data;
    }

    /**
     * @param $table
     * @param $field
     * @param string $orderby
     * @return array
     *
     * Devuelve una columna
     */
    function getOneColumn($table, $field, $orderby = "") {

        $query = 'SELECT ' . $field . ' FROM ' . $table . ' ' . $orderby;
        $data = $this->get_results($query);

        return $data;
    }

    /**
     * @param $table
     * @param $field
     * @param string $where
     * @param string $orderby
     * @return mixed
     *
     * Devuelve un campo
     */
    function getOneField($table, $field, $where = "", $orderby = "") {
        $query = 'SELECT ' . $field . ' FROM ' . $table . ' ' . $where . ' ' . $orderby;
        $data = $this->get_var($query);
        return $data;
    }

    /**
     * @param $table
     * @param $id
     * @param string $reason
     *
     * Borra una fila
     */
    function deleteRow($table, $reason = "") {
        global $user;

        //$query = 'DELETE FROM '.$table.' WHERE id="'.$id.'" ';

        if(isset($_GET['id'])){
            $id = $_GET['id'];
        }else if(isset($_POST['id'])){
            $id = $_POST['id'];
        }

        $query = 'UPDATE '.$table.' SET deleted=1, deleted_on="'.date('Y-m-d h:i:s').'", deleted_by='.$user->getId().', deleted_reason="'.$reason.'" WHERE id='.$id;
        $this->query($query);

        confirmationMessage(trans('db_data_deleted'));
    }

    /**
     * @param $data
     * @return mixed
     *
     * Función que quita el separador de miles de los campos que no se encuentran
     * en el array de permitidos.
     */
    function removeMilesSeparator($data) { 
        // A estos indices no se le deben quitar los puntos
        $alloweds = array('comment', 'created_on', 'deleted_on', 'description', 'email', 'user_email', 'label', 'link', 'password', 'total', 'pendingpay', 'payed');
		
        // expresión regular $expr = '/\.00{1}$/';
        // Para controlar los valores decimales del tipo 5000000.00 y que no de problema a la hora de suprir los ceros
        // Ya que el valor 1000.00 (mil) me lo cambiaría por 100000 (cien mil)

        foreach ($data as $key => $value) {
            // Comrobamos que el campo sea numérico y que mínimo tenga una lóngitud de 5 caracteres 1.000
            if (strlen($value) > 4 && is_numeric(substr($value, 0, 5))) {
                //echo 'Es numerico: ' . $value . ' ' . substr($value, 0, 5) . ' <br>';

                if (!in_array($key, $alloweds)) {
                    // filtro por la expresión regular
                    // Si hay algun valor con decimales .00 los quito
                    if (preg_match('/\.00{1}$/', $value)) {
                        $n = strlen($value);
                        // extraigo desde el inicio del número cortando el punto y los 2 ceros (.00)
                        $value = substr($value, 0, $n - 3);
                    }
                    $data[$key] = str_replace('.', '', $value);
                }
            }
        }

        return $data;
    }

    /**
     * @param $field
     * @param $table
     * @param string $where
     * @param string $orderBy
     * @param string $limit
     * @return array
     *
     *
     */
    function criteria($field, $table, $where = '', $orderBy = '', $limit = '') {
        if (is_array($field)) {
            $field = implode(',', $field);
        }

        if (is_array($where)) {
            $wheQuery = ' WHERE ';
            $cont = 1;

            foreach ($where as $parameter) {
                if ($cont < count($where)) {
                    $wheQuery .= $parameter[0] . ' ' . $parameter[1] . ' ' . '"' . $parameter[2] . '" AND ';
                } else {
                    $wheQuery .= $parameter[0] . ' ' . $parameter[1] . ' ' . '"' . $parameter[2] . '"';
                }

                $cont++;
            }
        } else {
            $wheQuery = ' WHERE ' . $where;
        }

        if ($limit != "") {
            $limit = ' LIMIT ' . $limit;
        }

        $query = 'SELECT ' . $field . ' FROM ' . $table . $wheQuery . $orderBy . $limit;

        return $this->get_results($query);

    }

    /**
     * @param $name
     * @param $operator
     * @param $value
     *
     * Construye un array con 3 valores que se usara como where y que puede ser recuperado mediante el metodo getWhere
     *
     * $wpdb->buildWhere('user_nicename', '=', 'yeisson');
     * $wpdb->buildWhere('id', '>', 0);
     * $wpdb->buildWhere('user_email', 'LIKE', '%jeixuxspn%');
     *
     * $result = persist($wpdb->criteria('*', $wpdb->prefix . 'users', $wpdb->getWhere()), 'User');
     */
    function buildWhere($name, $operator, $value) {
        $new = array($name, $operator, $value);
        array_push($this->where, $new);
    }

    function getWhere() {
        return $this->where;
    }

    function getLinker() {
        return $this->linker;
    }
}

?>
<?php

/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 14/11/2016
 * Time: 14:42
 */
class ControllerController extends ControllerModel {
    protected $classesPath = CLASSES_PATH;
    protected $modelPath = MODELS_PATH;
    protected $controllerPath = CONTROLLERS_PATH;
    protected $fileName = '';
    protected $oldFilename = '';
    protected $object;

    function __construct() {
        parent::__construct();
    }

    function setOptions() {
        $options = array(
            'class' => array(
                'sufix' => '',
                'path' => $this->classesPath,
            ),
            'controller' => array(
                'sufix' => 'controller',
                'path' => $this->controllerPath
            ),
            'model' => array(
                'sufix' => 'model',
                'path' => $this->modelPath
            ),
            'view' => array(
                'sufix' => 'view',
                'path' => VIEWS_PATH
            )
        );

        $this->object = $options;
    }

    /**
     * Generamos el array de opciones
     */
    function createObject() {
        if (isset($_REQUEST['controller_name']) && !empty($_REQUEST['controller_name'])) {
            $this->fileName = $_REQUEST['controller_name'];
            if (isset($_REQUEST['old_controllername'])) {
                $this->oldFilename = $_REQUEST['old_controllername'];
            }

            $this->setOptions();
            $this->createController();
        } else {
            errorMsg("No se ha indicado el nombre del controlador");
        }
    }

    /**
     * Permite listar los controladores que hay en un directorio y los guarda en la DB
     */
    function saveAllControllersFromDir($dir = "") {
        global $user;
        $files = getFilesFromDirectory($dir . CONTROLLERS_PATH);
        $createdBy = 1;
        if (is_object($user)) {
            $createdBy = $user->getId();
        }

        foreach ($files as $file) {
            $name = str_replace('Controller.php', '', $file);
            $exists = $this->getControllerDataByName($name);

            if (count($exists) == 0) {
                $_POST['controller_name'] = $name;
                $_POST['description'] = $name;
                $_POST['created_by'] = $createdBy;
                $this->save_controller();
            }
        }

        unset($_POST);
    }

    /**
     * Creamos el controlador
     */
    function createController() {
        // Si recibimos el id es porque debemos renombrar los archivos
        if (isset($_POST['id'])) {
            if ($this->renameClass()) {
                $this->save_edit_controller();
            }
        } else {
            if ($this->createClass()) {
                $this->save_controller();
            }
        }
    }

    /**
     * Creamos el archivo fisico
     */
    function createClass() {
        $processOk = false;
        
        foreach ($this->object as $obj) {
            $filename = $obj['path'] . ucfirst($this->fileName) . ucfirst($obj['sufix']) . '.php';
            if ($obj['sufix'] == "view") {
                $filename = $obj['path'] . $this->fileName;
            }
            // Si no existe el archivo o directorio
            if (!file_exists($filename)) {
                if ($obj['sufix'] != "view") {
                    // Si no es una vista generamos el archivo
                    $content = $this->getFileHead($this->fileName, $obj['sufix']);
                    file_put_contents($filename, $content);
                    $processOk = true;
                } else {
                    // Para la vista creamos el directorio
                    mkdir($obj['path'] . strtolower($this->fileName));
                    $processOk = true;
                }
            } else {
                if ($obj['sufix'] != "view") {
                    errorMsg('Ya existe el controlador ' . $filename);
                } else {
                    errorMsg('Ya existe el directorio ' . $obj['path'] . $this->fileName);
                }
            }
        }

        if (!$processOk) {
            // Si llegamos a este punto es porque el archivo existe fisicamente, por ello comprobamos si existe en la BBDD
            // y en caso contrario decimos que processOk = true para que lo guarde mediante save_controller
            $exists = $this->getControllerDataByName($this->fileName);
            if (count($exists) == 0) {
                $processOk = true;
            }
        }

        return $processOk;
    }

    /**
     * @return bool
     *
     * Para renombrar los archivos
     */
    function renameClass() {
        $processOk = true;
        echo "Aquí habría que renombrar<br>";

        echo $this->fileName . " ";
        echo $this->oldFilename . " ";

        foreach ($this->object as $obj) {
            if ($obj['sufix'] != "view") {
                $oldFilename = $obj['path'] . ucfirst($this->oldFilename) . ucfirst($obj['sufix']) . '.php';
                $newFile = $obj['path'] . ucfirst($this->fileName) . ucfirst($obj['sufix']) . '.php';

                $code = file_get_contents($oldFilename);
                $aux = str_replace(ucfirst($this->oldFilename), ucfirst($this->fileName), $code);
                file_put_contents($oldFilename, $aux);
                //echo $filename . "<br>";
                //echo $aux;
                rename($oldFilename, $newFile);
            } else {
                // Renombramos el directorio
                rename($obj['path'] . strtolower($this->oldFilename), $obj['path'] . strtolower($this->fileName));
            }
        }

        return $processOk;
    }

    /**
     * @param $class
     * @param $complement
     * @return string
     *
     * Generamos el contenido que tendrá el archivo
     */
    function getFileHead($class, $complement) {
        $fileName = strtolower($this->fileName);
        $className = ucfirst($class);
        $aux = $complement;
        $complement = ucfirst($complement);

        $head = '<?php' . PHP_EOL;
        $head .= PHP_EOL;
        $head .= '/**' . PHP_EOL;
        $head .= ' * Class ' . $className . $complement . PHP_EOL;
        $head .= ' * Date: ' . date('d/m/Y') . PHP_EOL;
        $head .= ' * Time: ' . date('H:i:s') . PHP_EOL;
        $head .= ' */' . PHP_EOL;
        $head .= 'class ' . $className . $complement . ' {' . PHP_EOL;
        $head .= '    function __construct() {' . PHP_EOL;
        $head .= PHP_EOL;
        $head .= '    }';
        $head .= PHP_EOL . PHP_EOL;
        if ($aux == "controller") {
            $head .= '    function new_' . $fileName . '() {' . PHP_EOL;
            $head .= PHP_EOL;
            $head .= '    }';
            $head .= PHP_EOL;
            $head .= PHP_EOL;
            $head .= '    function show_' . $fileName . 's() {' . PHP_EOL;
            $head .= PHP_EOL;
            $head .= '    }';
            $head .= PHP_EOL;
            $head .= PHP_EOL;
        }

        $head .= '}';

        return $head;
    }

    function new_controller() {
        $data = false;
        global $user;

        if (isset($_GET['id'])) {
            $data = $this->getControllerData();
        }
        require_once(VIEWS_PATH_CONTROLLER . "new_controller.html.php");
    }

    function show_controllers() {
        global $user;
        $controllers = $this->getControllers();

        require_once(VIEWS_PATH_CONTROLLER . "show_controllers.html.php");
    }
    
    function protectDirectories($absolute = "") {
        $this->setOptions();
        $options = $this->object;

        foreach ($options as $option) {
            $path = $option['path'];
            $file = $absolute. $path . "index.php";

            if (!file_exists($file)) {
                file_put_contents($file, "");
                echo "Directorio protegido: " . $path . "<br>";
            }
        }


    }
    
}
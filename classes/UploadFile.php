<?php
/**
 * Created by PhpStorm.
 * User: yvelez
 * Date: 22/07/2016
 * Time: 10:26
 */
class LoadFile
{
    /**
     * @var
     *
     * Es un array en el que definimos las opciones que usara la clase
     * es necesario pasarle un indice dirs que contiene el arbol de directorios donde
     * se guardaran los archivos
     * $option = array (
     *      'dirs' => array (
     *          0 => dir1, 1 => dir2, ...
     *      )
     * );
     * que sera la estructura
     */
    protected $options;
    protected $alloweds;
	protected $absolutePath;

    function __construct($options) {
        $this->options = $options;
        $this->setAlloweds();
    }

    function uploadFiles($resize = false, $id, $pdfname = "", $showmsg = true) {
        $upload = false;
        $response = "Archivos cargados: <br>";
        $errors = "";

        $path = $this->createDirStructure();

        foreach ($_FILES as $file) {
            if ($file['error'] == 0 && $file['type'] != "" && $this->isAllowed($file)) {
                //copy($file['tmp_name'], $path . $file['name']);
                if ($pdfname == "") {
                    $pdfname = $file['name'];
                }

                copy($file['tmp_name'], $path . $pdfname);
                $response .= $file['name'] . '<br>';
                $upload = true;
            } else {
                $errors .= '<br>Archivos con errores: <br>';
                $errors .= $file['name'] . '<br>';
            }
        }
		
		//$this->absolutePath = $path;
        if ($showmsg) {
            echo $response . $errors;
        }

        return $upload;
		
    }

    function isAllowed($file) {
        $type = explode(".", $file['name']);
        $type = $type[count($type) - 1];

        if (in_array($type, $this->alloweds)) {
            return true;
        }

        return false;
    }

    function setAlloweds() {
        $alloweds = array(
            'png', 'JPG', 'jpg',
            'JPEG', 'jpeg', 'pdf',
            'doc', 'docx', 'xls', 'rtf'
        );

        $this->alloweds = $alloweds;
    }

    function existDir($dir) {
        if (file_exists($dir)) {
            return true;
        } else {
            if (mkdir($dir)) {
                $this->createDirProtection($dir);
                return true;
            } else {
                return false;
            }
        }
    }

    function createDirStructure() {
        $i = 0;
        $current = "";

        foreach ($this->options['dirs'] as $dir) {
            $current .= $dir . '/';
            $this->existDir($current);
        }

        return $current;
    }

    function createDirProtection($dir) {
        $file = $dir . 'index.php';
        $data = '<?php' . PHP_EOL;
        $data .=    '   echo "";' . PHP_EOL;
        $data .= '?>' . PHP_EOL;

        if (!file_exists($file)) {
            file_put_contents($file, $data);
        }
    }

	function resize_image($file, $w, $h, $crop = FALSE) {
		
	    list($width, $height) = getimagesize($file['tmp_name']);
		
		echo $width . ' x ' . $height;
		
	    $r = $width / $height;
	    if ($crop) {
	        if ($width > $height) {
	            $width = ceil($width-($width*abs($r-$w/$h)));
	        } else {
	            $height = ceil($height-($height*abs($r-$w/$h)));
	        }
	        $newwidth = $w;
	        $newheight = $h;
	    } else {
	        if ($w/$h > $r) {
	            $newwidth = $h*$r;
	            $newheight = $h;
	        } else {
	            $newheight = $w/$r;
	            $newwidth = $w;
	        }
	    }
	    $src = imagecreatefromjpeg($file['tmp_name']);
	    $dst = imagecreatetruecolor($newwidth, $newheight);
	    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	
	    return $dst;
	}

    function pre($obj) {
        echo '<pre>';
        print_r($obj);
        echo '</pre>';
    }

}
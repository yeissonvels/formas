<section class="section-controller">
<?php
    if (is_user_logged_in()) {
        $user = $_SESSION['user'];
        // Control de inactividad
        // $user->inactiveSessionControl();
        App::initGetController();
        App::initPostController();
    } else {
        App::login();
    }
?>
</section>
<?php
include ('../vendor/autoload.php');

class CronJob {
    function __construct() {
        echo "Cron ejecutado";
    }
}

$cron = new CronJob();
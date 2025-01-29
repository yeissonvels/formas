<?php
include ('../functions.php');

class CronJob {
    function __construct() {
        echo "Cron ejecutado";
    }

    function getEstimatesWithoutNotification() {
        
        $controller = new StoreController();
        $estimates = $controller->getEstimatesWithoutNotification();
        //(pre($estimates);
        foreach($estimates as $estimate) {
            $estimate->store = ucfirst(strtolower(getStoreName($estimate->storeid)));
            $estimate->user = getUsername($estimate->created_by);
            $estimate->saledate = americaDate($estimate->created_on);

            $estimate = json_decode(json_encode($estimate), true);
            $controller->notifyNewEstimate($estimate);
            //pre($estimate);
        }

        //pre($estimates);
    }
}

$cron = new CronJob();
$cron->getEstimatesWithoutNotification();
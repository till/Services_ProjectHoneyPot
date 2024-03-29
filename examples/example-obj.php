<?php
//error_reporting(E_ALL|E_STRICT);

set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

require_once 'Services/ProjectHoneyPot.php';

/**
 * config.php, includes:
 * <?php $access_key = '...'; ?>
 * 
 * ... is the access key
 */
include dirname(__FILE__) . '/config.php';

try {
    $sphp = new Services_ProjectHoneyPot($access_key);
    $sphp->setResponseFormat('object');

    $status = $sphp->query($harvester);

}
catch (Services_ProjectHoneyPot_Exception $e) {
    echo "\nMSG: " .$e->getMessage();
    echo "\nCODE: " . $e->getCode();
    exit;
}
if (count($status) == 0) {
    die("No results.");
}
foreach ($status as $res) {

    $ip  = key($res);
    $res = $res[$ip];

    if ($res === false) {
        echo 'Don\'t bother. Probably a regular user. ;-)' . "\n";
    } else {
        if ($res->isHarvester()) {
            echo '<h1>OMG, a harvester!!!</h1>';
            echo '<pre>'; var_dump($res); echo '</pre>';
        }
    }
}

?>

<?php
//error_reporting(E_ALL|E_STRICT);

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../');

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

    //$ip = $_SERVER['REMOTE_ADDR'];
    //$ip = '24.132.194.14';
    //$ip = '62.75.159.212';
    //$ip = 'heise.de';

    $ips = array('24.132.194.14', '62.75.159.212', 'heise.de', '81.169.145.28');

    $status = $sphp->query($ips);

}
catch (Services_ProjectHoneyPot_Exception $e) {
    echo '<br />MSG: ' .$e->getMessage();
    echo '<br />CODE: ' . $e->getCode();
    exit;
}
if (count($status) == 0) {
    die("No results.");
}

echo "<h1>COUNT: " . count($status) . "</h1>";

var_dump($status);

foreach ($status AS $res) {
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

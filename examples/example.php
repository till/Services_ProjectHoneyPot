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
    $sphp = Services_ProjectHoneyPot::factory($access_key);

    //$ip = $_SERVER['REMOTE_ADDR'];
    $ip = '24.132.194.14';
    //$ip = '62.75.159.212';
    //$ip = 'heise.de';

    $status = $sphp->query($ip);

}
catch (Services_ProjectHoneyPot_Exception $e) {
    echo '<br />MSG: ' .$e->getMessage();
    echo '<br />CODE: ' . $e->getCode();
    exit;
}
if ($status === false)
{
    echo 'Don\'t bother. Probably a regular user. ;-)';
    exit;
}

echo '<pre>'; var_dump($status); echo '</pre>';

if (is_null($status['search_engine']) === true) {
    echo $sphp->getHoneyPot();
}
?>
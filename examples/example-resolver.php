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
    /**
     * Use a custom Net_DNS_Resolver with Services_ProjectHoneyPot
     */
    $resolver = new Net_DNS_Resolver;
    $resolver->nameservers = array('66.114.197.251');

    /**
     * 66.114.197.251 is a authorative NS for dnsbl.httpbl.org
     * :-)
     */

    $sphp = Services_ProjectHoneyPot::factory($access_key, $resolver);
    $sphp->setResponseFormat('object');

    $status = $sphp->query($harvester);

}
catch (Services_ProjectHoneyPot_Exception $e) {
    echo "\nMSG: " .$e->getMessage();
    echo "\nCODE: " . $e->getCode();
    exit;
}
if ($status === false)
{
    echo 'Don\'t bother. Probably a regular user. ;-)';
    exit;
}

if ($status->isHarvester()) {
    echo '<h1>OMG, a harvester!!!</h1>';
    echo '<pre>'; var_dump($status->getAll()); echo '</pre>';
}
?>

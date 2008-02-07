--TEST--
Testing a regular hostname. We shouldn't find anything.
--FILE--
<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());
require_once 'Services/ProjectHoneyPot.php';
include dirname(__FILE__) . '/config.php';
$sphp   = Services_ProjectHoneyPot::factory($access_key);
$ip     = $valid_host;;
$status = $sphp->query($ip);
var_dump($status);
?>
--EXPECT--
bool(false)

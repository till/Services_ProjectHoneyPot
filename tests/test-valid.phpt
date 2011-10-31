--TEST--
Testing a regular IP. We shouldn't find anything.
--SKIPIF--
<?php
require dirname(__FILE__) . '/skip.inc';
--FILE--
<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());
require_once 'Services/ProjectHoneyPot.php';
include dirname(__FILE__) . '/config.php';
$sphp   = new Services_ProjectHoneyPot($access_key);
$ip     = $valid_ip;
$status = $sphp->query($ip);
$answer = $status->current();
var_dump($answer[$ip]);
?>
--EXPECT--
bool(false)

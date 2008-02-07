--TEST--
This is a known harvester!
--FILE--
<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());
require_once 'Services/ProjectHoneyPot.php';
include dirname(__FILE__) . '/config.php';
$sphp   = Services_ProjectHoneyPot::factory($access_key);
$ip     = $harvester;
$status = $sphp->query($ip);
var_dump($status);
?>
--EXPECT--
array(9) {
  ["suspicious"]=>
  int(1)
  ["harvester"]=>
  int(1)
  ["comment_spammer"]=>
  NULL
  ["search_engine"]=>
  NULL
  ["debug"]=>
  NULL
  ["last_activity"]=>
  string(1) "2"
  ["score"]=>
  string(2) "50"
  ["type"]=>
  string(1) "3"
  ["type_hr"]=>
  string(22) "Suspicious & Harvester"
}

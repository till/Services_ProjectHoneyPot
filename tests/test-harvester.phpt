--TEST--
This is a known harvester!
--SKIPIF--
<?php
require dirname(__FILE__) . '/skip.inc';
--FILE--
<?php
set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());
require_once 'Services/ProjectHoneyPot.php';
include dirname(__FILE__) . '/config.php';
$sphp   = new Services_ProjectHoneyPot($access_key);
$ip     = $harvester;
$result = $sphp->query($ip);

var_dump(count($result));
foreach ($result as $res) {
    var_dump($res[$ip]);
}
?>
--EXPECT--
int(1)
array(9) {
  ["suspicious"]=>
  NULL
  ["harvester"]=>
  int(1)
  ["comment_spammer"]=>
  NULL
  ["search_engine"]=>
  NULL
  ["last_activity"]=>
  string(1) "1"
  ["score"]=>
  string(1) "1"
  ["type"]=>
  string(1) "2"
  ["type_hr"]=>
  string(9) "Harvester"
  ["debug"]=>
  NULL
}

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
    $info = $res[$ip];
    var_dump($info['harvester']);
    var_dump($info['last_activity']);
    var_dump($info['score']);
    var_dump($info['type']);
    var_dump(get_class($info));
}
?>
--EXPECT--
int(1)
int(1)
string(1) "1"
string(1) "1"
string(1) "2"
string(40) "Services_ProjectHoneyPot_Response_Result"

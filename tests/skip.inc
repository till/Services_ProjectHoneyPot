<?php
if (!file_exists(dirname(__FILE__) . '/config.php')) {
    exit("skip No config.ini");
}
$deps = array('Net/DNS2.php', 'Net/CheckIP2.php');

foreach ($deps as $deb) {
    if (!@include_once $deb) {
        exit("skip Missing {$dep}");
    }
}

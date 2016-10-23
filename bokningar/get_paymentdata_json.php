<?php
require_once("db/auth/user.php");
$USER = new User();

$pm = $USER->get_get_value("pm"); //paymethod
// $bd ? send bookingsnr and load ? Wont work till a save
$res = $USER->apply_rules( $bd );

$json = '{ "aaData":'.json_encode($arr).' }';
print $json;
//print $all;
?>

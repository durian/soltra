<?php
require_once("auth/user.php");
$USER = new User();
$result = $USER->get_all_users();
//print_r($result);
$cols = array( "username" => "un", "email" => "em", "token" => "tk", "role" => "rl", "last" => "ls", "timeleft" => "tl" );
foreach ($result as $r) {
  $data = array();
  foreach( $cols as $col => $abbr ) {
    $data[$abbr] = $r[$col];
  }
  $arr[] = $data;
}
$json = '{ "aaData":'.json_encode($arr).' }';
print $json;
//print $all;
?>
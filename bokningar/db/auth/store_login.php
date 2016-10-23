<?php
include("user.php");
$USER = new User();

$json_str = stripslashes($_POST['json']);
$json_data = json_decode( $json_str );

//print_r($json_data);
$db = $USER->get_db();
$dbuser = $USER->get_user($db, $json_data->username);
if ( $dbuser == NULL ) {
  echo $USER->err(1); // no such username/passwd
  return;
}
//print_r($dbuser);
$db_passwd = $dbuser["password"];//seed+sha1(seed+sha1)
$seed = substr($db_passwd, 0, 16);
$db_passwd = substr($db_passwd, 16);
$sha1 = hash('sha1', $seed.$json_data->sha1);
//print $db_passwd."/".$seed."/".$sha1;
$timeout = intval($dbuser["timeout"]);
if ( $sha1 === $db_passwd ) {
  $token = $USER->random_hex_string(16);
  $USER->user_store_token($db, $json_data->username, $token);
  $USER->user_touch($db, $json_data->username); 
  setcookie( "todo_loggedin", $token, time()+$timeout, "/" );//must be done here

  echo $USER->err(0); // OK
  return;
}
echo $USER->err(1); // Wrong password, but give err 1
?>

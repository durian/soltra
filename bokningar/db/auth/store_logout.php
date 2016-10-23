<?php
include("user.php");
$USER = new User();

$json_str = stripslashes($_POST['json']);
$json_data = json_decode( $json_str );

//print_r($json_data);
$db = $USER->get_db();
$dbuser = $USER->get_user($db, $json_data->username);
if ( $dbuser == NULL ) {
  echo $USER->err(1); // no such username
  return;
}
$token = $USER->random_hex_string(16);
$USER->user_remove_token($db, $json_data->username);
setcookie( "todo_loggedin", "", 1, "/");
echo $USER->err(0); //ok, logged out
?>

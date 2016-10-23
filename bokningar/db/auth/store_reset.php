<?php
include("user.php");
$USER = new User();

$json_str = stripslashes($_POST['json']);
$json_data = json_decode( $json_str );
//874af7ed 888959a2
try {
  $db = $USER->get_db();
  $u = $USER->get_user($db, $json_data->username);
  if ( ! $u ) {
    echo $USER->err(-1); // user not found (we don't want to say this)
    return;
  }
  $randompass = $USER->random_hex_string(8);
  $sha1 = hash("sha1", $randompass);
  $salt = $USER->random_hex_string(16);
  $db_passwd = hash( 'sha1', $salt.$sha1);
  $db_passwd = $salt.$db_passwd;
  $data = array( 'username' => $json_data->username, 'password' => $db_passwd, 'last' => time() );
  $stmt = $db->prepare("UPDATE users SET password=:password,last=:last WHERE username=:username");
  $stmt->execute( $data );
  if ( $stmt === false ) {
    echo $USER->err(-1);
    return;
  } else {
    echo $USER->txt("Ditt nya lösenord är: ".$randompass);
    return;
  }
} catch (Exception $e) {
  echo "error";
  die ($e);
}
?>

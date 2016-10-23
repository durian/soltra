<?php
include("user.php");
$USER = new User();

$json_str = stripslashes($_POST['json']);
$json_data = json_decode( $json_str );

//print $json_data->sha1;
try {
  $db = $USER->get_db();
  // Check if name/email/... exists...
  $doubles = $USER->check_user_email($db, $json_data->username, $json_data->email);
  if ( $doubles > 0 ) {
    echo $USER->err(4);
    return;
  }
  $salt = $USER->random_hex_string(16);
  $db_passwd = hash( 'sha1', $salt.$json_data->sha1);
  $db_passwd = $salt.$db_passwd;
  $userid = $USER->random_hex_string(32);
  $data = array( 'username' => $json_data->username, 'password' => $db_passwd, 'email' => $json_data->email, 'token' => 123, 'role' => "user", 'userid' => $userid, 'active' => 1, 'last' => time() );
  $stmt = $db->prepare("INSERT INTO users (username, password, email, token, role, userid, active, last) VALUES (:username, :password, :email, :token, :role, :userid, :active, :last);");
  //
  $stmt->execute( $data );
  if ( $stmt === false ) {
    echo $USER->err(4);//"Username/password combination is not allowed."
  } else {
    echo $USER->err(0);
  }
  return;
} catch (Exception $e) {
  die ($e);
}
?>

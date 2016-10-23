<?php
include("user.php");
$USER = new User();

$json_str = stripslashes($_POST['json']);
$json_data = json_decode( $json_str );

if ( $USER->un == "guest" ) {
  echo $USER->err(6);
  return;
}

//print $json_data->sha1;
try {
  $db = $USER->get_db();
  //username TEXT UNIQUE, password TEXT, email TEXT UNIQUE, token TEXT, role TEXT, active TEXT, last TEXT
  // Check if old is correct
  $u = $USER->get_user($db, $USER->un);
  if ( ! $u ) {
    echo $USER->err(2);// wrong password
    return;
  }
  $db_passwd = $u["password"];//seed+sha1(seed+sha1)
  $seed = substr($db_passwd, 0, 16);
  $db_passwd = substr($db_passwd, 16);
  $sha1p0 = hash('sha1', $seed.$json_data->sha1p0);
  if ( $sha1p0 != $db_passwd ) {
    echo $USER->err(1);
    return;
  }
  $salt = $USER->random_hex_string(16);
  $db_passwd = hash( 'sha1', $salt.$json_data->sha1);
  $db_passwd = $salt.$db_passwd;
  $data = array( 'username' => $json_data->username, 'password' => $db_passwd, 'last' => time() );
  $stmt = $db->prepare("UPDATE users SET password=:password, last=:last WHERE username=:username;");
  $stmt->execute( $data );
  if ( $stmt === false ) {
    echo $USER->err(1);
  } else {
    echo $USER->err(0); // OK;
  }
} catch (Exception $e) {
  die ($e);
}
?>

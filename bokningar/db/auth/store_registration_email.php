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
  $data = array( 'username' => $json_data->username, 'password' => $db_passwd, 'email' => $json_data->email, 'token' => 123, 'role' => "user", 'userid' => $userid, 'active' => 0, 'last' => time() );
  $stmt = $db->prepare("INSERT INTO users (username, password, email, token, role, userid, active, last) VALUES (:username, :password, :email, :token, :role, :userid, :active, :last);");
  //
  // Send a mail, with a code for activation. When clicked, set active to 1.
  //
  $stmt->execute( $data );
  if ( $stmt === false ) {
    echo $USER->err(4);
  } else {
    // email activation.
    $token = $USER->random_hex_string(32);
    $data = array( 'username' => $json_data->username, 'token' => $token, 'exp' => time()+14400 );
    $stmt = $db->prepare("INSERT INTO activation (token, username, expire) VALUES(:token, :username, :exp);");
    $stmt->execute( $data );
    //
    // mail
    //
		$from = "contact@itmasala.se";
		$replyto = "contact@itmasala.se";
		$subject = "Registrering av ny användare i bokningsprogrammet för Solidarity Travels";
		$un = $json_data->username;
		$link = $USER->get_activation_link() . "?token=".$token;
		$body = <<<EOT
	Hej!

	Detta är ett automatiskt meddelande som skickats för att en ny användare registrerats med denna mailadressen.
	
	Användarnamn: $un

	Klicka på länken för att aktivera användaren: $link
	
EOT;
		$headers = "From: $from\r\n";
		$headers .= "Reply-To: $replyto\r\n";
		$headers .= "X-Mailer: PHP/" . phpversion();
		mail($json_data->email, $subject, $body, $headers);

    echo $USER->err(0);
    return;
  }
} catch (Exception $e) {
  die ($e);
}
?>

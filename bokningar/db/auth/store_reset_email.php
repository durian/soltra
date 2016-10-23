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
    echo $USER->err(5); // user not found (we don't want to say this)
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
    echo $randompass;
    return;
  } else {
  		// step 3: notify the user of the new password
			$from = "noreply@itmasala.se";
			$replyto = "noreply@itmasala.se";
			$subject = "Lösenord till bokningsprogram Solidarity Travels";
			$un = $u["username"];
			$body = <<<EOT
	Hej,

	Detta är ett automatisk meddelande som skickats för att ett nytt lösenord har begärts för användarnamn
	$un som är kopplat till denna mailadressen.

	Nytt lösenord:

	$randompass

EOT;
			$headers = "From: $from\r\n";
			$headers .= "Reply-To: $replyto\r\n";
			$headers .= "X-Mailer: PHP/" . phpversion();
			mail($u["email"], $subject, $body, $headers);

    echo $USER->err(5); ;
    return;
  }
} catch (Exception $e) {
  echo "error";
  die ($e);
}
?>

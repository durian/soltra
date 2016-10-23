<?php
// This will import GET and POST vars
// with an "f_" prefix

//import_request_variables("P", "f_");//DEPRECATED
//import_request_variables("gP", "postvar_");
//extract($_GET, EXTR_PREFIX_ALL, "postvar");
//extract($_POST, EXTR_PREFIX_ALL, "f_"); 

require 'PHPMailerAutoload.php';

function get_post_value($name) {
  if ( ($_POST[$name]) || (intval($_POST[$name] == 0)) ) {
    return $_POST[$name];
  }
  return "";
}

function email_validator($email) {
  if (eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$",$email))
    {
      $valid = "yes";
    }
  else
    {
      $valid = "no";
    }
  return $valid;
}

// PJB 2014-08-19
function DBG($s) {
  $myfile = "../bokningar/maillog.php"; // create empty file from Coda to start debugging.
  if ( is_writable($myfile) ) { // Header for login was taken from index.php in bokningar.
    $fh = fopen($myfile, 'a') or die("can't open file");
    fwrite($fh, $s."\n");
    fclose($fh);
  }
}
DBG( "<h2>".date("Y-m-d H:i:s")." ".$_SERVER['REMOTE_ADDR']."</h2>" );
// PJB 2014-08-19

$f_namn = get_post_value("namn");
$f_tel = get_post_value("tel");
$f_email = get_post_value("email");
$f_meddelande = get_post_value("meddelande");

if ( strlen( $f_namn ) < 1 ) {
	DBG( " No name supplied. ABORT" );
  die( "NONAME" );
  exit;
}

// The message
//
$message = "\n";

if ( strlen( $f_namn ) > 64 ) {
  $f_namn = substr( $f_namn, 0, 64 );
}
if ( strlen( $f_adress ) > 64 ) {
  $f_adress = substr( $f_adress, 0, 64 );
}
if ( strlen( $f_postadress ) > 64 ) {
  $f_postadress = substr( $f_postadress, 0, 64 );
}
if ( strlen( $f_email ) > 64 ) {
  $f_email = substr( $f_email, 0, 64 );
}

$spam = 0;
$num = 0;
$fields = array( $f_namm, $f_meddelande, $f_tel, $f_email );
foreach ($fields as $fld) {
  $pos = strpos($fld, "@");
  if ($pos !== false) {
    $spam++;
  }
  $pos = strpos($fld, "to:");
  if ($pos !== false) {
    $spam++;
  }
  $num = $num + preg_match_all( "/[A-Z]+/", $fld, $out ); // blocks with caps
}
if ( $num > 10 ) {
    $spam++;
}
//DBG( "Spam indicator:" . "$spam" );

$mail = new PHPMailer;
$mail->isSMTP(); 
$mail->Host = 'REMOVED:465';
$mail->SMTPAuth = true;
$mail->Username = 'REMOVED';
$mail->Password = 'REMOVED'; 
$mail->SMTPSecure = 'ssl';

$mail->From = 'REMOVED'; // OR f_email
$mail->FromName = 'REMOVED';
$mail->addAddress('REMOVED');
//$mail->addAddress('REMOVED');
if ( email_validator($f_email) == "no" ) {
  $mail->addReplyTo('REMOVED', 'Information');
} else {
  $mail->addReplyTo($f_email);
}

$mail->CharSet = 'UTF-8';

$mail->WordWrap = 70;    // Set word wrap to 50 characters
#$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
#$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(false);     // Set email format to HTML (hmmmm bleh)

$mail->Subject = 'Förfrågan (' . $f_namn . ')';

$message  = "Namn: " .  $f_namn . "\n";
$message .= "Telefonnr: " .  $f_tel . "\n";
$message .= "Email: " .  $f_email . "\n";
$message .= "Kommentar: " .  $f_meddelande . "\n";

DBG( "<pre>" );
DBG( $message );
DBG( "</pre>" );

$mail->Body    = $message; //PHP here has no no_magic_quotes?
//$mail->AltBody = $message; //mail looks better (linefeeds etc) without this AltBody

if(!$mail->send()) {
  echo 'Meddelande kunde inte skickas, ';
  DBG( '<p>Mailer Error: ' . $mail->ErrorInfo . "</p>" );
  exit;
}
DBG( "<p>Status: Meddelandet har skickats.</p>" );

#echo 'Message has been sent';
$tack = file_get_contents('tack_soltra.html');
echo $tack;
?> 

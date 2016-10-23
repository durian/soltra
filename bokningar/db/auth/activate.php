<?php
include("user.php");

if ( ! isset($_GET["token"]) ) {
  die("no token.");
}
$token = $_GET["token"];

$USER = new User();
//print $json_data->sha1;
$result = 0;
try {
  $db = $USER->get_db();
  $u = $USER->get_user_from_activation($db, $token);
  if ( ! $u ) {
    die("L&auml;nken &auml;r inte l&auml;ngre giltig.");
  }
  $data = array( 'username' => $u["username"] );
  $stmt = $db->prepare("UPDATE users SET active=1 WHERE username=:username;");
  $stmt->execute( $data );
  if ( $stmt === false ) {
    $result = 1;
  }
} catch (Exception $e) {
  die ($e);
}
?>
<html>
	<head>
		<title>Activation</title>
		<meta charset="utf-8"/>
		<link type="text/css" href="http://solidaritytravels.se/bokningar/stylesheet.css" rel="Stylesheet" />
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script> 
		<script type="text/javascript" src="js/sha1.js"></script>
		<script type="text/javascript" src="js/jquery.labs_json.js"></script>
	</head>
	<body>

<?php if ( $USER->un !== "guest" ) { ?>
	<div style="padding-bottom:0;background-color:#f4f4f4;width:100%;height:24px;margin-left:-30px;padding-left:30px;"><a style="float:right;font-size:12px;padding-right:50px;" href="/bokningar/db/auth/login.php">Logga ut / Administration</a></div>
<?php } else { ?>
	<div style="padding-bottom:0;background-color:#f4f4f4;width:100%;height:24px;margin-left:-30px;padding-left:30px;"><a style="float:right;font-size:12px;padding-right:50px;" href="/bokningar/db/auth/login.php">Logga in</a></div>
  
<?php } ?>


		<ul id="topmeny">
<li><img src="http://solidaritytravels.se/Bilder/logo/logo-transp-1000_128x128x32.png" height="80px" /></li>
<li style="font-size:36px;padding-top:25px;padding-bottom:25px;color:#0C6A12">Solidarity Travels</li>

<?php if ( $USER->un !== "guest" ) { ?>

<li style="float:right"><a href="/bokningar/alla_bokningar_std.php">Bokningshistorik</a></li>
<li style="float:right"><a href="/bokningar/kalender.php">Kalender</a></li>
<li style="float:right"><a href="/bokningar/nybokning.php?nr=<?=$last_nr?>">Bokningar</a></li>
<li style="float:right"><a href="/bokningar/index.php">Aktuellt</a></li>

<?php } else { ?>
  
<?php } ?>
		</ul>

<div class="contentwrapper">

	<h2>Aktivering av nytt konto</h2>
    <div style="margin-top:35px;color:#ffffff;font-size:14px"><?php echo $USER->msg(6);?>	</div>
	<div><a style="color:#f6f6f6;font-size:14px" href="/bokningar/db/auth/login.php">➭ Logga in här</a></div>

    
</div>	
  </body>
</html>

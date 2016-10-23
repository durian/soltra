<?php
include("user.php");
$USER = new User();
$token = 0;
if ( ! isset($_GET["token"]) ) {
  die("Invalid token.");
}
$token = $_GET["token"];
if ( $USER->is_valid(null, $token, "test") ) {
  print "valid";
} else {
  die("invalid");
}
$r = $USER->get_token(null, $token);
print_r($r);
$USER->use_token(null, $token);
for ( $i = 0; $i < 1; $i++ ) {
  $newt = $USER->create_token_use(null, "reg", 8);
  $newt = $USER->create_token_expire(null, "test", 3600);
}
$r = $USER->get_token(null, $newt);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Register</title>
		<meta charset="utf-8"/>
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script> 
		<script type="text/javascript" src="js/sha1.js"></script>
		<script type="text/javascript" src="js/jquery.labs_json.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
    } );
    </script>    
	</head>
	<body>
		<h1>Tokens</h1>
		<pre>
		<?php print_r($r);?>
		</pre/>
	</body>
</html>
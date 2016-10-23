<?php
include("user.php");
$USER = new User();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Change password</title>
		<link type="text/css" href="http://solidaritytravels.se/bokningar/stylesheet.css" rel="Stylesheet" />
		<meta charset="utf-8"/>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script> 
		<script type="text/javascript" src="js/sha1.js"></script>
		<script type="text/javascript" src="js/jquery.labs_json.js"></script>
		
    <script type="text/javascript" src="pnotify/jquery.pnotify.min.js"></script>
    <link type="text/css" href="jquery-ui/css/ui-lightness/jquery-ui-1.8.24.custom.css" rel="Stylesheet" />	
    <script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.24.custom.min.js"></script>
    <link href="pnotify/jquery.pnotify.default.css" media="all" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript" src="js/auth-plain.js"></script>
    <link type="text/css" href="auth.css" rel="Stylesheet" />	

    <script type="text/javascript">
    function process() {
      form = new Object();
      p0 = $('[name=password0]').val();
      p1 = $('[name=password1]').val();
      p2 = $('[name=password2]').val();
      form["username"] = "<?=$USER->un?>";
      form["sha1p0"] = Sha1.hash(p0);
      form["sha1"] = Sha1.hash(p1);
      if ( ((p0 == "") || (p1 == "") || (p2 == "")) || (p1 != p2) ) {
        show_error("<?php echo $USER->msg(2);?>");
        return false;
      }

			$.post("store_change_password.php", { 'json': $.json.encode(form) },
			   function(data) {
			      show_error( data.msg );
            if ( data.code == 0 ) {
              window.location.href="<?=$USER->get_start_link()?>";
            } else {
              //stay
            }
           },"json" );
      return false;
    }
    </script>
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
<li style="float:right"><a href="/bokningar/db/auth/login.php">Logga ut</a></li>
<li style="float:right"><a href="/bokningar/alla_bokningar_std.php">Bokningshistorik</a></li>
<li style="float:right"><a href="/bokningar/kalender.php">Kalender</a></li>
<li style="float:right"><a href="/bokningar/nybokning.php?nr=<?=$last_nr?>">Bokningar</a></li>
<li style="float:right"><a href="/bokningar/index.php">Aktuellt</a></li>
</ul>

<div class="contentwrapper">
		<h2>Ändra lösenord</h2>
<?php if ( $USER->un !== "guest" ) { ?>
			<form name="new user registration" id="registration" action="user.php" method="POST">
				<table>
 					<tr><td>Nuvarande lösenord </td><td><input type="password" name="password0" value="" /></td></tr>
					<tr><td>Nytt lösenord </td><td><input type="password" name="password1" value="" /></td></tr>
					<tr><td>Upprepa nytt lösenord </td><td><input type="password" name="password2" value="" /></td></tr>
				</table>
				<input style="width:120px;margin-top:25px;" type="button" value="Ändra lösenordet" onclick="process()" />
			</form>
<?php } else { ?>
  Du måste vara inloggad för att ändra lösenord.
<?php } ?>
		</div>
	</body>
	</body>
</html>
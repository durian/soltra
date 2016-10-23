<?php
include("user.php");
$USER = new User();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
		<meta charset="utf-8"/>
		<link type="text/css" href="http://solidaritytravels.se/bokningar/stylesheet.css" rel="Stylesheet" />
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
      var in_alert = 0;
      $(document).ready(function() { 
      $("#registration").keyup(function(event){
        if(event.keyCode == 13){
          if (in_alert == 0) {
            $("#login").click();
          }
        }
      });
     }); 
         
    function process() {
      form = new Object();
      p1 = $('[name=password1]').val();
      un = $('[name=username]').val();
      form["username"] = un;
      form["sha1"] = Sha1.hash(p1);
			$.post("store_login.php", { 'json': $.json.encode(form) },
           function(data) {
           //alert(data.msg);
            if ( data.code == 0 ) {
              window.location.href="<?=$USER->get_start_link()?>";
            } else {
              show_error(data.msg);
            }
           },"json" );
      return false;
    }
    function process_logout() {
      form = new Object();
      form["username"] = "<?=$USER->un?>";
			$.post("store_logout.php", { 'json': $.json.encode(form) },
           function(data) {
             window.location.href="<?=$USER->get_start_link()?>";
           },"json" );
      return false;
    }
    function process_reset() {
      form = new Object();
      form["username"] = $('[name=username]').val();
      if ( form["username"] == "" ) {
        show_error("<?php echo $USER->msg(1);?>");
        return false;
      }
      sure = confirm("Lösenordet kommer att ändras direkt! Är du säker på att du vill ändra lösenord?");
      if ( ! sure ) {
        return
      }
			$.post("<?=$USER->get_reset_link()?>", { 'json': $.json.encode(form) },
           function(data) {
            if (data.code == 0 ) {
              show_info(data.msg);
            } else {
              show_sticky(data.msg);
            }
           },"json" );
      return false;
    }
    </script>
	</head>
	<body>

	<div style="padding-bottom:0;background-color:#f4f4f4;width:100%;height:24px;margin-left:-30px;padding-left:30px;"> </div>
	
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

<?php if ( $USER->un === "guest" ) { ?>
			<!-- Allow a new user to register -->
			<form name="new user registration" id="registration" action="user.php" method="POST">
				<table>
					<tr><td>Användarnamn </td><td><input type="text"  name="username" value="" class="required"/></td></tr>
					<tr><td>Lösenord </td><td><input type="password" name="password1"  value="" /></td></tr>
				</table>
				<input style="width:100px;margin-top:25px;" id="login" type="button" value="Logga in" onclick="process()" />
				
				<div style="margin-top:35px;color:#a3a3a3;font-size:12px" onclick="process_reset()">Glömt lösenordet? Fyll i användarnamnet ovan och klicka sen här så skickas ett mail med nytt lösenord till registrerad mailadress.</div>
			</form>
<?php } else { ?>
    	<div style="margin-top:25px;color:#ffffff;font-size:12px" onclick="process_reset()">Du är inloggad som: <?=$USER->un?></div>
		<input style="width:100px;margin-top:25px;" type="button" value="Logga ut" onclick="process_logout()" />
		
		<h2 style="margin-top:60px;">Administration</h2>
		<div><a style="color:#f6f6f6;font-size:14px" href="<?=$USER->get_change_link()?>">➭ Ändra lösenord</a></div>
		
		<div><a style="color:#f6f6f6;font-size:14px" href="/bokningar/db/auth/register.php">➭ Registrera ny användare</a></div>

<?php } ?>

</div>
	</body>
</html>
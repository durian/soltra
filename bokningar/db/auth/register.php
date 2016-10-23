<?php
include("user.php");
$USER = new User();
$token = 0;
$r = $USER->get_token(null, $token);
$USER->use_token(null, $token);
//
// email link, and don't activate user before activation link is clicked.
//
$reg_link = $USER->get_registration_link(); // choose normal/via email 
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Register</title>
		<link type="text/css" href="http://solidaritytravels.se/bokningar/stylesheet.css" rel="Stylesheet" />
		<meta charset="utf-8"/>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script> 
		<script type="text/javascript" src="js/sha1.js"></script>
		<script type="text/javascript" src="js/jquery.labs_json.js"></script>

		<script type="text/javascript" src="pnotify/jquery.pnotify.min.js"></script>
		<link type="text/css" href="jquery-ui/css/ui-lightness/jquery-ui-1.8.24.custom.css" rel="Stylesheet" />	
		<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.24.custom.min.js"></script>
		<link href="pnotify/jquery.pnotify.default.css" media="all" rel="stylesheet" type="text/css" />

		<script type="text/javascript" src="js/jquery.validate.min.js"></script>
		<script type="text/javascript" src="js/additional-methods.min.js"></script>
		<script type="text/javascript" src="js/auth-plain.js"></script>
		<link type="text/css" href="auth.css" rel="Stylesheet" />	
		
		<style>
		/* Alternate stack initial positioning. */
		.ui-pnotify.stack-topleft {
			top: 25px;
			left: 240px;
			right: auto;
		}
		h4, h5, h6 { margin: 0;}
		.ui-widget {
      font-size: 80%;
    }
		</style>
		
		<style type="text/css">
    * { font-family: Verdana; font-size: 96%; }
    label { width: 10em; float: left; }
    label.error { float: none; color: red; padding-left: .5em; vertical-align: top;width:400px; }
    p { clear: both; }
    .submit { margin-top:25px; }
    em { font-weight: bold; padding-right: 1em; vertical-align: top; }
    </style>

    <script type="text/javascript">

      var stack_topleft = {"dir1": "down", "dir2": "right", "push": "top"};
  
      $(document).ready(function() { 
        /*$.pnotify({
        title: 'Hello <b>there</b>',
        text: 'Look <b>at</b> me',
        styling: 'jqueryui',
        type: 'error',
        before_open: function(pnotify) {
                // Position this notice in the center of the screen.
                pnotify.css({
                    "top": ($(window).height() / 2) - (pnotify.height() / 2),
                    "left": ($(window).width() / 2) - (pnotify.width() / 2)
                });
            }
          });*/
        /*$("#reg2").validate({
      		rules: {
      		  f1: {require_from_group: [1,".ff"] },
      		  f2: {require_from_group: [1,".ff"] }
      		}
        });*/

        $("#registration").validate({
      		rules: {
      			password2: {
      				required: true,
      				minlength: 5,
      				equalTo: "#password1"
      			}
      		}
        });
     }); 
     
    function process() {
      form = new Object();
      p1 = $('[name=password1]').val();
      p2 = $('[name=password2]').val();
      un = $('[name=username]').val();
      form["username"] = un;
      em = $('[name=email]').val();
      form["email"] = em;
      // Check
      /*
      if ( p1 != p2 ) {
        show_error("<?php echo $USER->msg(2);?>");
        return false;
      }
      if ( un == "" ) {
        show_error("<?php echo $USER->msg(3);?>");
        return false;      
      }
      if ( em == "" ) {
        show_error("<?php echo $USER->msg(4);?>");
        return false;      
      }
      if ( (p1 == "") || (p2 == "") ) {
        show_error("<?php echo $USER->msg(5);?>");
        return false;      
      }
      */
      if ( $("#registration").valid() == false ) {
        return false;
      }
      //
      form["sha1"] = Sha1.hash(p1);
			$.post("<?=$reg_link?>", { 'json': $.json.encode(form) },
			   function(data) {
            if ( data.code > 0 ) {
   			      show_error( data.msg );
            } else {
              window.location.href="<?=$USER->get_login_link()?>";
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

<?php if ( $USER->un !== "guest" ) { ?>

<li style="float:right"><a href="/bokningar/alla_bokningar_std.php">Bokningshistorik</a></li>
<li style="float:right"><a href="/bokningar/kalender.php">Kalender</a></li>
<li style="float:right"><a href="/bokningar/nybokning.php?nr=<?=$last_nr?>">Bokningar</a></li>
<li style="float:right"><a href="/bokningar/index.php">Aktuellt</a></li>

<?php } else { ?>
  
<?php } ?>
		</ul>

<div class="contentwrapper">

		<h2 style="padding-top:30px">Registrering av ny användare</h2>
<?php if ( $USER->un !== "guest" ) { ?>
		<table style="width: 100%; margin-top: 1em;"><tr><td style="width: 24em; padding-top:1em;">
			<!-- Allow a new user to register -->
			<form name="new user registration" id="registration" method="POST">

			 <fieldset>
         <p>
           <label for="username">Användarnamn <em>*</em></label>
           <input id="username" name="username" size="25" class="required" minlength="4" />
         </p>
         <p>
           <label for="email">Mailadress <em>*</em></label>
           <input id="email" name="email" size="25"  class="required email" />
         </p>
         <p>
           <label for="password1">Lösenord <em>*</em></label>
           <input type="password" id="password1" name="password1" size="25"  class="required password" value="" />
         </p>
         <p>
           <label for="password2">Upprepa lösenord <em>*</em></label>
           <input type="password" id="password2" name="password2" size="25"  class="required password"></input>
         </p>   
         <p>
           <input class="submit" type="submit" value="Skicka" onclick="process();return false;"/>
         </p>
        </fieldset>
			</form>
<?php } else { ?>
  		<div style="margin-top:35px;color:#ffffff;font-size:12px">Du måste vara inloggad för att registrera ny användare.</div>
  		<div style="margin-top:30px;"><a href="/bokningar/db/auth/login.php" style="color:#f2f2f2;font-weight:bold;font-size:14px;">Logga in</a></div>
<?php } ?>
		</div>
	</body>
</html>
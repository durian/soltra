<?php
require_once("db/auth/user.php");
$USER = new User();
if ( $USER->un === "guest" ) {
  // goto login
  header('Location: '.$USER->get_login_link());
}
//
$prev_id = $USER->get_previous_id($bd['id']);
$next_id = $USER->get_next_id($bd['id']);
$last_nr = $USER->get_last_schedule_nr();

//$tmp = $USER->get_schedule_cal();
//print_r($tmp);

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"  lang="sv" xml:lang="sv">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link href='db/js/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='db/js/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<!--<script src='db/js/jquery/jquery-1.9.1.min.js'></script>-->
<script src='db/js/jquery-1.7.2.min.js'></script>
<script src='db/js/fullcalendar/fullcalendar.min.js'></script>
<link type="text/css" href="stylesheet.css" rel="Stylesheet" />

  <script type='text/javascript'>
$(document).ready(function() {
  $('#calendar').fullCalendar({
    header: {
				left: 'prev,next today',
				center: 'title',
//				right: 'month,agendaWeek,agendaDay'
		},
    eventSources: ['p1_events.php'],
    eventColor: '#6CBC66',
    firstDay:1,
    weekNumbers:true,
    defaultView:'month',
    timeFormat: 'H(:mm)',
    height: 650,
    monthNames: ["Januari","Februari","Mars","April","Maj","Juni","Juli", "Augusti", "September", "Oktober", "November", "December" ],
    monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun','Jul','Aug','Sep','Okt','Nov','Dec'],
    dayNames: [ 'Söndag', 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag'],
    dayNamesShort: ['Sön','Mån','Tis','Ons','Tor','Fre','Lör'],
    buttonText: {
      today: 'Idag',
      month: 'Månad',
      week: 'Vecka',
      day: 'Dag'
    }
    //aspectRatio: 1
  });
});
</script>

<style type="text/css">

h2 {
	color: #35A760;
}

.contentwrapper {
	padding: 20px;
	height: auto;
}
.fc {
    background-color: #FFFFFF;
    padding: 10px;
}
.fc-event {
	line-height: 16px;
}


</style>


</head>
<body>

	<div style="padding-bottom:0;background-color:#f4f4f4;width:100%;height:24px;margin-left:-30px;padding-left:30px;"><a style="float:right;font-size:12px;padding-right:50px;" href="/bokningar/db/auth/login.php">Logga ut / Administration</a></div>

<ul id="topmeny">
<li><img src="http://pixelz.se/solidaritytravels/Bilder/logo/logo-transp-1000_128x128x32.png" height="80px" /></li>
<li style="font-size:36px;padding-top:25px;padding-bottom:25px;color:#0C6A12">Solidarity Travels</li>
<li style="float:right"><a href="alla_bokningar_std.php">Bokningshistorik</a></li>
<li style="float:right"><a href="kalender.php">Kalender</a></li>
<li style="float:right"><a href="nybokning.php?nr=<?=$last_nr?>">Bokningar</a></li>
<li style="float:right"><a href="index.php">Aktuellt</a></li>
</ul>

<div class="contentwrapper">


  <div id='calendar'></div>
  
</div>  
</body>
</html>

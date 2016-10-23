<?php
require_once("db/auth/user.php");
$USER = new User();
if ( $USER->un === "guest" ) {
  // goto login
  header('Location: '.$USER->get_login_link());
}
//
//
$last_nr = $USER->get_last_schedule_nr();
$type = $USER->get_get_value("type");
$param = "";
if ( $type === "future" ) {
  $param = "?type=future";
} else if ( $type === "past" ) {
  $param = "?type=past";
}
$param = "?type=future";
//
// From get_schedule_json.php
$cols = array( "nr" => array("NR", "25px", "table_right"),
  "grpid" => array("Gruppid", "80px", "table_left"), 
  "status" => array("Status", "30px", "table_left"), 
  "bookingdate" => array("Bokningsdatum", "80px", "table_left"), 
  "firstname" => array("Förnamn", "100px", "table_left"), 
  "lastname" => array("Efternamn", "100px", "table_left"),
//  "street" => array("Gatuadress", "150px", "table_left"), 
  "city" => array("Postadress", "100px", "table_left"), 
  "phone" => array("Tel", "100px", "table_right"), 
  "email" => array("e-post", "150px", "table_left"), 
  "project" => array("Projekt", "60px", "table_left"), 
  "startdate" => array("Startdatum", "80px", "table_right"), 
  "enddate" => array("Slutdatum", "80px", "table_right")
//  "comments" => array("Noteringar", "180px", "table_right")
);
/*
RULES: warning for delbetalning
select * from schedule where ffd1 - INTERVAL 14 DAY < CURDATE() and ffd1check !=1;
select * from schedule where ffd2 - INTERVAL 14 DAY < CURDATE() and ffd2check !=1;
select * from schedule where ffd3 - INTERVAL 14 DAY < CURDATE() and ffd3check !=1;

select * from schedule where info1 - INTERVAL 14 DAY < CURDATE() and info1check !=1;
select * from schedule where info2 - INTERVAL 14 DAY < CURDATE() and info2check !=1;
*/
/*
$db = $USER->get_db();
$stmt = $db->prepare('select *,ffd1 as ffd from schedule where ffd1 - INTERVAL 14 DAY < CURDATE() and ffd1check !=1;');
$stmt->execute(  );
$result = $stmt->fetchAll();
$stmt = $db->prepare('select *,ffd2 as ffd from schedule where ffd2 - INTERVAL 14 DAY < CURDATE() and ffd2check !=1');
$stmt->execute(  );
$result = array_merge($result, $stmt->fetchAll());
$stmt = $db->prepare('select *,ffd3 as ffd from schedule where ffd3 - INTERVAL 14 DAY < CURDATE() and ffd3check !=1');
$stmt->execute(  );
$result = array_merge($result, $stmt->fetchAll());
*/
$db = $USER->get_db();
// Customer did/not pay in time?
// dd is daydifference with today
$stmt = $db->prepare('select *,cffd1 as ffd,cffd1-CURDATE() as dd from schedule where cffd1 - INTERVAL 1 DAY < CURDATE() and cinvoicecheck1 !=1 and cffd1amount != "0.00" and status!="AV" union select *,cffd2 as ffd,cffd2-CURDATE() as dd from schedule where cffd2 - INTERVAL 1 DAY < CURDATE() and cinvoicecheck2 !=1 and cffd2amount != "0.00" and status!="AV" order by ffd');
$stmt->execute();
$resultc = $stmt->fetchAll();

// "INTERVAL 14 DAY < CURDATE()" is within a 14 day span coming up/in the future (grey font)
// (we don't want to check too much in the future)
$stmt = $db->prepare('select *,ffd1 as ffd,ffd1-CURDATE() as dd from schedule where ffd1 - INTERVAL 14 DAY < CURDATE() and ffd1check !=1 and ffd1amount != "0.00" and status!="AV" union select *,ffd2 as ffd ,ffd2-CURDATE() as dd from schedule where ffd2 - INTERVAL 14 DAY < CURDATE() and ffd2check !=1 and ffd2amount != "0" and status!="AV" union select *,ffd3 as ffd,ffd3-CURDATE() as dd from schedule where ffd3 - INTERVAL 14 DAY < CURDATE() and ffd3check !=1 and ffd3amount != "0.00" and status!="AV" order by ffd');
$stmt->execute();
$result = $stmt->fetchAll();


$stmt = $db->prepare('select *,info1 as info, info1-CURDATE() as dd from schedule where info1 - INTERVAL 14 DAY < CURDATE() and info1check !=1 and info1rec != "" and status!="AV" union select *,info2 as info, info2-CURDATE() as dd from schedule where info2 - INTERVAL 14 DAY < CURDATE() and info2check !=1 and info2rec != "" and status!="AV" order by info');
$stmt->execute(  );
$result4 = $stmt->fetchAll();
/*print "<pre>";
print_r( $result );
print "</pre>";*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="sv" xml:lang="sv">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resebokningar</title>

<style type="text/css">
<!--
#future {
	width: 900px;
	height: 500px;
	border: none;
}
.table_left {
  text-align: left;
  cursor: pointer;
}
.table_right {
  text-align: right;
  cursor: pointer;
}

.table_left:hover, .table_right:hover {
	text-decoration: underline;
}

.dataTables_wrapper {
	overflow-x: scroll;
}

-->
</style>
<link type="text/css" href="stylesheet.css" rel="Stylesheet" />

<style type="text/css" title="currentStyle">
			@import "db/js/DataTables-1.9.4/media/css/jquery.dataTables.css";
			/*@import "tables.css"; http://datatables.net/blog/Creating_beautiful_and_functional_tables_with_DataTables*/
</style>
<script type="text/javascript" src="db/js/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="db/js/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
<!-- -->
<script type="text/javascript">
$(document).ready(function() {
    $('#example').dataTable( {
        "bProcessing": true,
        "iDisplayLength": 25,
        "sAjaxSource": "get_schedule_json.php<?=$param?>",
        //"username" => "un", "email" => "em", "token" => "tk", "role" => "rl", "last" => "ls", "timeleft" => "tl"
        "aoColumns": [ // php returns named columns below:
        		<?php
          		foreach( $cols as $col => $info ) {
          		  //, \"sType\":\"date\"
          		  $type = "";
          		  if ( $col == "OFF_startdate" ) {
          		    $type = ", \"sType\":\"date\"";
          		  } else if ( $col == "nr" ) {
          		    $type = ", \"sType\":\"numeric\"";
          		  }
          		  $svensknamn = $info[0];
          		  $width = $info[1];
          		  $class = $info[2];
            		print "{ \"mData\": \"".$col."\", \"sWidth\":\"".$width."\"".$type.", \"sClass\":\"".$class."\" },\n";
              }
            ?>
        ]
    } );
    
    $('#example tbody tr').live('click', function () {
      var nTds = $('td', this);
      var sNr = $(nTds[0]).text(); // NB, 0 is column number!
      //alert( sNr );
      window.location="nybokning.php?nr="+sNr;
    } );

} );
</script>
<!-- -->
</head>


<!-- *****************************  B O D Y  ******************************* -->

<body>

<ul id="topmeny">
<li><img src="http://pixelz.se/solidaritytravels/Bilder/logo/logo-transp-1000_128x128x32.png" height="80px" /></li>
<li style="font-size:36px;padding-top:25px;padding-bottom:25px;color:#0C6A12">Solidarity Travels</li>
<li style="float:right"><a href="alla_bokningar_std.php">Bokningshistorik</a></li>
<li style="float:right"><a href="kalender.php">Kalender</a></li>
<li style="float:right"><a href="nybokning.php?nr=<?=$last_nr?>">Bokningar</a></li>
<li style="float:right"><a href="aktuellt.php">Aktuellt</a></li>
</ul>

<form id="nybokningform" action="store_nybokning.php" method="post">

<h2>Aktuellt</h2>

<div class="boxaktuellt duedates">

<h3>Att åtgärda</h3>
<p style="font-style:italic">Röd = förfallodag t o m idag<br />Orange = förfallodag inom tre dagar</p>

<?php
print "<h4>Förfallna kundbetalningar:</h4>\n";
foreach ( $resultc as $r ) {
  $cls = "normal";
  if ( intVal($r['dd']) <=0 ) {
    $cls = "past";
  } elseif ( intVal($r['dd']) <=3 ) {
    $cls = "urgent";
  }
  print "<div class=\"".$cls."\" dd=\"".$r['dd']."\"><a href=\"nybokning.php?nr=".$r['nr']."\"><span class=\"duedate\">".$r['ffd']."</span><span class=\"duebooknr\">".$r['nr']."</span><span class=\"volontar\">".$r['firstname']." ".$r['lastname']."</span> <span class=\"countryproj\">".$r['projectcountry']."/".$r['project']."</span></a></div>\n";
}

print "<h4>Förfallodagar för betalning:</h4>\n";
foreach ( $result as $r ) {
  $cls = "normal";
  if ( intVal($r['dd']) <=0 ) {
    $cls = "past";
  } elseif ( intVal($r['dd']) <=3 ) {
    $cls = "urgent";
  }
  print "<div class=\"".$cls."\" dd=\"".$r['dd']."\"><a href=\"nybokning.php?nr=".$r['nr']."\"><span class=\"duedate\">".$r['ffd']."</span><span class=\"duebooknr\">".$r['nr']."</span><span class=\"volontar\">".$r['firstname']." ".$r['lastname']."</span> <span class=\"countryproj\">".$r['projectcountry']."/".$r['project']."</span></a></div>\n";
}

print "<h4>Information om resedetaljer:</h4>\n";
foreach ( $result4 as $r ) {
  $cls = "normal";
  if ( intVal($r['dd']) <=0 ) {
    $cls = "past";
  } elseif ( intVal($r['dd']) <=3 ) {
    $cls = "urgent";
  }
  print "<div class=\"".$cls."\" dd=\"".$r['dd']."\"><a href=\"nybokning.php?nr=".$r['nr']."\"><span class=\"duedate\">".$r['info']."</span><span class=\"duebooknr\">".$r['nr']."</span><span class=\"volontar\">".$r['firstname']." ".$r['lastname']."</span> <span class=\"countryproj\">".$r['projectcountry']."/".$r['project']."</span></a></div>\n";
}
?>

</div>


<div class="boxaktuellt">

<h3 style="margin-bottom:30px;">Boknings&ouml;versikt (startdatum imorgon och framåt)</h3>

<div class="boxaktuellt">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
		<?php
		foreach( $cols as $col => $info) {
  		$svensknamn = $info[0];
		  print "<th>".$svensknamn."</th>\n";
		}
		?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="3" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
		<?php
		foreach( $cols as $col => $info) {
		  $svensknamn = $info[0];
		  print "<th>".$svensknamn."</th>\n";
		}
		?>
		</tr>
	</tfoot>
</table>
</body>    
</div>

</div>



<!-- Start of StatCounter Code for Default Guide -->
<script type="text/javascript">
var sc_project=8541017; 
var sc_invisible=1; 
var sc_security="0e49de4c"; 
var scJsHost = (("https:" == document.location.protocol) ?
"https://secure." : "http://www.");
document.write("<sc"+"ript type='text/javascript' src='" +
scJsHost +
"statcounter.com/counter/counter.js'></"+"script>");</script>
<noscript><div class="statcounter"><a title="web analytics"
href="http://statcounter.com/" target="_blank"><img
class="statcounter"
src="http://c.statcounter.com/8541017/0/0e49de4c/1/"
alt="web analytics"></a></div></noscript>
<!-- End of StatCounter Code for Default Guide -->


</body>
</html>
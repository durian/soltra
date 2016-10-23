<?php
/*
  Show all possible trailers, allow to add/subtract to stock?
  allow to add new trailers only, stock seperate?
*/
//$a = get_all(NULL);
//print_r(db_result_to_json($a));
require_once("db/auth/user.php");
$USER = new User();
if ( $USER->un === "guest" ) {
  // goto login
  header('Location: '.$USER->get_login_link());
}
//
$last_nr = $USER->get_last_schedule_nr();
$type = $USER->get_get_value("type");
$param = "";
if ( $type === "future" ) {
  $param = "?type=future";
} else if ( $type === "past" ) {
  $param = "?type=past";
}
//
// From get_schedule_json.php
$cols = array( "nr" => array("NR", "35px", "table_right"),
  "status" => array("Status", "35px", "table_left"), 
  "firstname" => array("Förnamn", "100px", "table_left"), 
  "lastname" => array("Efternamn", "100px", "table_left"),
  "street" => array("Gatuadress", "150px", "table_left"), 
  "city" => array("Postadress", "100px", "table_left"), 
  "phone" => array("Tel", "100px", "table_right"), 
  "email" => array("e-post", "150px", "table_left"), 
  "project" => array("Projekt", "100px", "table_left"), 
  "startdate" => array("Startdatum", "80px", "table_right"), 
  "enddate" => array("Slutdatum", "80px", "table_right")
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  lang="sv" xml:lang="sv">
<head>
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta name="author" content="Peter Berck" />
<meta name="copyright" content="Peter Berck" />
<meta name="description" content="Peter Berck" />
<meta name="keywords" content="Peter Berck" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Resebokningar</title>

<link type="text/css" href="stylesheet.css" rel="Stylesheet" />

<style type="text/css">
<!--
#future {
	width: 900px;
	height: 500px;
	border: none;
}
-->
</style>

<!-- -->
<style type="text/css">
body {
	font-size: 12px;
	font-family: "Lucida Grande", Verdana, Arial, sans-serif;
}
th { font-size: 11px; }
td { font-size: 11px; }
#logininfo {
  padding-top: 8px;
  padding-bottom: 12px;
}
.ten {
  margin-right: 10px;
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

</style> 
<style type="text/css" title="currentStyle">
			@import "db/js/DataTables-1.9.4/media/css/jquery.dataTables.css";
			/*@import "tables.css"; http://datatables.net/blog/Creating_beautiful_and_functional_tables_with_DataTables*/
</style>
<script type="text/javascript" src="db/js/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="db/js/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
<!-- -->
<script type="text/javascript">
$(document).ready(function() {
    var oTable = $('#example').dataTable( {
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
    
    oTable.fnFilter( 'AV', 1 );
    
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

	<div style="padding-bottom:0;background-color:#f4f4f4;width:100%;height:24px;margin-left:-30px;padding-left:30px;"><a style="float:right;font-size:12px;padding-right:50px;" href="/bokningar/db/auth/login.php">Logga ut / Administration</a></div>

<ul id="topmeny">
<li><img src="http://pixelz.se/solidaritytravels/Bilder/logo/logo-transp-1000_128x128x32.png" height="80px" /></li>
<li style="font-size:36px;padding-top:25px;padding-bottom:25px;color:#0C6A12">Solidarity Travels</li>
<li style="float:right"><a href="alla_bokningar.php">Bokningshistorik</a></li>
<li style="float:right"><a href="kalender.php">Kalender</a></li>
<li style="float:right"><a href="nybokning.php?nr=<?=$last_nr?>">Bokningar</a></li>
<li style="float:right"><a href="index.php">Aktuellt</a></li>
</ul>


<form id="nybokningform" action="store_nybokning.php" method="post">

<h2>Alla avbokningar</h2>

<p><a style="color: #00ac69;padding-right:30px;" href="alla_bokningar_std.php">Visa standardlista</a><a style="color: #00ac69;padding-right:30px;" href="alla_bokningar.php">Visa alla bokningar</a><a style="color: #00ac69;padding-right:30px;" href="alla_bokningar_ffd.php">Visa lista med förfallodagar</a></p>

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
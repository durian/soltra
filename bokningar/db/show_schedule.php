<?php
/*
  Show all possible trailers, allow to add/subtract to stock?
  allow to add new trailers only, stock seperate?
*/
//$a = get_all(NULL);
//print_r(db_result_to_json($a));
require_once("auth/user.php");
$USER = new User();
//
$type = $USER->get_get_value("type");
$param = "";
if ( $type === "future" ) {
  $param = "?type=future";
} else if ( $type === "past" ) {
  $param = "?type=past";
}
//
// From get_schedule_json.php
$cols = array( "nr", "firstname", "lastname", "street", "postcode", "city", "phone", "email", "project", "startdate", "enddate" );
?>
<html> 
<head>
<!-- -->
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta name="author" content="Peter Berck" />
<meta name="copyright" content="Peter Berck" />
<meta name="description" content="Peter Berck" />
<meta name="keywords" content="Peter Berck" />
<!-- -->
<title>All</title>
<!-- -->
<style type="text/css">
body {
  margin: 8px;
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
</style> 
<style type="text/css" title="currentStyle">
			@import "js/DataTables-1.9.4/media/css/jquery.dataTables.css";
			/*@import "tables.css"; http://datatables.net/blog/Creating_beautiful_and_functional_tables_with_DataTables*/
</style>
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="js/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
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
          		foreach( $cols as $col ) {
          		  //, \"sType\":\"date\"
          		  $type = "";
          		  if ( $col == "OFFstartdate" ) {
          		    $type = ", \"sType\":\"date\"";
          		  } else if ( $col == "OFFpostcode" ) {
          		    $type = ", \"sType\":\"numeric\"";
          		  }
            		print "{ \"mData\": \"".$col."\", \"sWidth\":\"100px\"".$type." },\n";
              }
            ?>
        ]
    } );
    
    $('#example tbody tr').live('click', function () {
      var nTds = $('td', this);
      var sTrailerid = $(nTds[0]).text(); // NB, 0 is column number!
      alert( sTrailerid );
      window.location="../nybokning.php?nr="+sTrailerid;
    } );

} );
</script>
</head>
<body>
<?php //$USER->header();?>
<br />
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
		<?php
		foreach( $cols as $col ) {
		  print "<th>".$col."</th>\n";
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
		foreach( $cols as $col ) {
		  print "<th>".$col."</th>\n";
		}
		?>
		</tr>
	</tfoot>
</table>
</body>    
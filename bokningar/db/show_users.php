<?php
/*
  Show all possible trailers, allow to add/subtract to stock?
  allow to add new trailers only, stock seperate?
*/
//$a = get_all(NULL);
//print_r(db_result_to_json($a));
require_once("auth/user.php");
$USER = new User();
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
th { font-size: 12px; }
td { font-size: 12px; }
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
</style>
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="js/DataTables-1.9.4/media/js/jquery.dataTables.min.js"></script>
<!-- -->
<script type="text/javascript">
$(document).ready(function() {
    $('#example').dataTable( {
        "bProcessing": true,
        "iDisplayLength": 25,
        "sAjaxSource": "get_users_json.php",
        //"username" => "un", "email" => "em", "token" => "tk", "role" => "rl", "last" => "ls", "timeleft" => "tl"
        "aoColumns": [ // php returns named columns below:
            { "mData": "un", "sWidth":"100px" },
            { "mData": "em", "bVisible":true, "sWidth":"100px" },
            { "mData": "tk", "sWidth":"100px" },
            { "mData": "rl", "sWidth": "50px" },
            { "mData": "ls", "sWidth": "50px" },
            { "mData": "tl", "sWidth": "50px" }
        ]
    } );
} );
</script>
</head>
<body>
<?php $USER->header();?>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
		  <th>username</th>
			<th>email</th>
			<th>token</th>
			<th>role</th>
			<th>last</th>
			<th>timeleft</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="3" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
		  <th>username</th>
			<th>email</th>
			<th>token</th>
			<th>role</th>
			<th>last</th>
			<th>timeleft</th>
		</tr>
	</tfoot>
</table>
</body>    
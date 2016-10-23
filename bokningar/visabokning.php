<?php
require_once("db/auth/user.php");
$USER = new User();
if ( $USER->un === "guest" ) {
  // goto login
  header('Location: '.$USER->get_login_link());
}

$id = $USER->get_get_value("id");
$sched = null;
if ( $id !== "" ) {
  $bd = $USER->get_schedule( $id );
  //print_r($sched);
} else {
  // ERROR
}

$sf = $USER->get_schedule_status_def();
$m_status = $USER->def_to_menu( $sf, "status_menu", $bd['status'], "status" );

// These can't be chanmged here anymore!! should not be a menu
$m_projectcountry = $USER->get_country_menu($bd['projectcountry']);
$m_project = $USER->get_project_menu( $bd['projectcountry'], $bd['project'] );
$pinfo = $USER->get_one_project($bd['projectcountry'], $bd['project']);
/*
$flags = $USER->get_flags( $id );
print "<pre>";
print_r( $flags );
$now = new DateTime();
$now = new DateTime($now->format('Y-m-d')); // to get without time
$USER->check_warnings( $bd, $flags, $now );
print "</pre>";
*/
$sf = $USER->get_schedule_fixflight_def();
$m_fixflight = $USER->def_to_menu( $sf, "fixflight_menu", $bd['fixflight'], "fixflight" );

$sf = $USER->get_schedule_fixtransport_def();
$m_fixtransport = $USER->def_to_menu( $sf, "fixtransport_menu", $bd['fixtransport'], "fixtransport" );

$sf = $USER->get_schedule_meddelaresa_def();
$m_meddelaresa = $USER->def_to_menu( $sf, "meddelaresa_menu", $bd['meddelaresa'], "meddelaresa" );

print "<pre>";
//print_r( $USER->get_rules( 102 ) );
print error_reporting()."<br/>";
$now = new DateTime();
$now = new DateTime($now->format('Y-m-d')); // to get without time
print_r( $USER->check_rules( $bd, $USER->get_rules( 103 ), $now ) );
print "</pre>";

?>
<html xmlns="http://www.w3.org/1999/xhtml"  lang="sv" xml:lang="sv">

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
<title>Resebokningar</title>

<style type="text/css">
<!--
body {
	font-family: Arial;
	font-size: 12px;
}

label {
	width: 300px;
	padding-top: 15px;
	display: block;
}

input {
	width: 300px;
}

textarea {
	width: 300px;
}

h2 {
	padding-top: 20px;
	margin-bottom: 0px;
}

#bookingheader {
	padding-bottom: 25px;
}

.box {
	float:left;
	margin-right:20px;
	border: 1px solid #f2f2f2;
	padding: 10px;
}

li {
	float: left;
	width: 100px;
	list-style: none;
}

.inputfield_date {
	width: 70px;
}

-->
</style>

<script type="text/javascript" src="db/js/jquery-1.7.2.min.js"></script> 
<link type="text/css" href="db/auth/jquery-ui/css/ui-lightness/jquery-ui-1.8.24.custom.css" rel="Stylesheet" />	
<script type="text/javascript" src="db/auth/jquery-ui/js/jquery-ui-1.8.24.custom.min.js"></script>
<script type="text/javascript">
var pmenu = new Array();
<?php
/*
  $flds = $USER->get_schedule_project_def();
  //print_r($flds);
  foreach( $flds as $idx => $opts ) {
    $i = 0;
    print utf8_encode(html_entity_decode("pmenu[$idx]={};\n"));
    foreach( $opts as $j => $opt ) {
      //print "// ".$j." - ".$opt."\n";
      print utf8_encode(html_entity_decode("pmenu[$idx]['$j']='$opt';\n"));
      $i++;
    }
  }
*/
?>
//var pmenu = '<?= json_encode($USER->get_schedule_project_def()); ?>';

$(document).ready(function() {
  $("#enddate").datepicker( { dateFormat: "yy-mm-dd", firstDay: 1 } );
  $('#enddate').datepicker('setDate', "<?=$bd['enddate']?>");
  $("#startdate").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#startdate").datepicker('setDate', "<?=$bd['startdate']?>");
  
  $("#traveltodate").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#traveltodate").datepicker('setDate', "<?=$bd['traveltodate']?>");
  $("#travelbackdate").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#travelbackdate").datepicker('setDate', "<?=$bd['travelbackdate']?>");
  
  $('#fixflight_menu').change(function() {
    //alert($('#fixflight_menu').prop("selectedIndex"));
  });
  $('#project_menu').change(function() {
    //alert( $('#project_menu').prop("selectedIndex")+"/"+$("#project_menu option:selected").attr("project") );
    paym = $("#project_menu option:selected").attr("paymethod");
    //alert(paym);
    // store this, or transfer when clicking submit.
  });
  $('#projectcountry_menu').change(function() {
    //alert($('#projectcountry_menu').prop("selectedIndex"));
    idx = $('#projectcountry_menu').prop("selectedIndex");
    cid = $("#projectcountry_menu option:selected").attr("projectcountry");
    //pid = $("#project_menu option:selected").attr("project");
    pid = "XXX"; //changing country invalidates chosen project
    //$('#project_menu').empty();
    $("#project_menu").load("get_project_menu.php",{'pid':pid, 'cid':cid});
  });
});
</script>
</head>

<!-- *****************************  B O D Y  ******************************* -->

<body>

<ul>
<li><a href="index.php">Aktuellt</a></li> <li><a href="nybokning.php">Ny bokning</a></li>  <li><a href="alla_bokningar.php">Bokningshistorik</a></li>
</ul>

Paymethod <?= $m_paymethods ?>

<h1 style="clear:both;padding-top:50px;">Solidarity Travels</h1>

<h2>Skapa/visa bokning</h2>

<div id="bookingheader">

<div id="bookid">Bokningsnummer: <?=$bd['nr']?></div>

<label for="paystat">Status</label>
<?= $m_status ?>

<label for="bookingdate">Bokningsdatum</label>
<input name="bookingdate" type="text" class="inputfield_date" id="bookingdate" tabindex=10 value="<?=$bd['bookingdate']?>" />

</div>

<div class="box">

<input type="checkbox" checked="checked" style="width:10px;"> Bekräftelse skickad till kund 
 
<h2>Personliga uppgifter</h2>
<label for="lastname">Efternamn</label>
<input name="lastname" type="text" id="lastname" class="inputfield" tabindex=1 value="<?=$bd['lastname']?>"/> 

<label for="firstname">F&ouml;rnamn</label>
<input name="firstname" type="text" id="firstname" class="inputfield" tabindex=2 value="<?=$bd['firstname']?>" />

<label for="company">F&ouml;retagsnamn/Skola</label>
<input name="company" type="text" id="company" class="inputfield" tabindex=3 value="<?=$bd['company']?>" />

<label for="email">Email</label>
<input name="email" type="text" id="email" class="inputfield" tabindex=4 value="<?=$bd['email']?>" />

<label for="tel">Telefon</label>
<input name="tel" type="text" id="tel" class="inputfield" tabindex=5 value="<?=$bd['phone']?>" />

<label for="address_street">Gatuadress</label>
<input name="address_street" type="text" id="address_street" class="inputfield" tabindex=6 value="<?=$bd['street']?>" />

<label for="address_city">Postadress</label>
<input name="address_city" type="text" id="address_city" class="inputfield" tabindex=7 value="<?=$bd['city']?>" />

<label for="address_country">Land</label>
<input name="address_country" type="text" id="address_country" class="inputfield" tabindex=8 value="<?=$bd['country']?>" />
</div>

<div class="box">
<h2>Bokat program</h2>
<label for="project">Volont&auml;rprogram</label>
<?= $pinfo['land'] ?> <?= $pinfo['project'] ?>


<label for="startdate">Startdatum</label>
<input name="startdate" type="text" class="inputfield_date" id="startdate" tabindex=10 value="<?=$bd['startdate']?>" />
 
<label for="enddate">Slutdatum</label>
<input name="enddate" type="text" class="inputfield_date" id="enddate" tabindex=12 value="<?=$bd['enddate']?>" />

<h2>Resedetaljer</h2>

<label for="project">Resen&auml;ren blir upph&auml;mtad på</label>
<?= $m_fixflight ?>

<label for="project">Status resedetaljer</label>
<?= $m_fixtransport ?>

<label for="project">Meddela resedetaljer till</label>
<?= $m_meddelaresa ?>


<label for="traveltodate">Utresedatum</label>
<input name="traveltodate" type="text" class="inputfield_date" id="traveltodate" tabindex=10 value="<?=$bd['traveltodate']?>" />

<label for="arrivaltime">Ankomsttid (lokal tid)</label>
<input name="arrivaltime" type="text" class="inputfield_date" id="arrivaltime" tabindex=10 value="<?=$bd['arrivaldate']?>" />

<!--
<label for="travelbackdate">Hemresedatum</label>
<input name="travelbackdate" type="text" class="inputfield_date" id="travelbackdate" tabindex=10 value="<?=$bd['travelbackdate']?>" />
-->

 
<label for="arrivaldate">Flightnummer</label>
<input name="arrivaldate" type="text" class="inputfield_date" id="arrivaldate" tabindex=10 value="<?=$bd['arrivaldate']?>" />
 

<label for="transport" style="height:auto">Övriga resedetaljer</label>
<textarea rows="4" name="transport" id="transport" tabindex=32 ><?=$bd['transport']?></textarea>
 
</div>




<div class="box">

<h2>Betalningar</h2>

<h3>Inbetalning</h3>

<input type="checkbox" style="width:10px;"> Kunden har betalt

<label for="pay">Betalningsid kund</label>
<input name="pay" type="text" id="pay" class="inputfield" tabindex=13 value="<?=$bd['invoice']?>" />

<br /><br />

<h3>Utbetalning</h3>

<input type="checkbox" style="width:10px;"> Betalning till samarbetspartner klar


<label for="ffd1">Förfallodag 1</label>
<input name="ffd1" type="text" id="ffd1" class="inputfield_date" tabindex=13 value="<?=$bd['ffd1']?>" />
<input type="checkbox" style="width:10px;"> Betald

<label for="pay">Betalningsid 1</label>
<input name="pay" type="text" id="pay" class="inputfield" tabindex=13 value="<?=$bd['pay']?>" />


<label for="ffd2">Förfallodag 2</label>
<input name="ffd2" type="text" id="ffd2" class="inputfield_date" tabindex=13 value="<?=$bd['ffd1']?>" />
<input type="checkbox" style="width:10px;"> Betald

<label for="pay">Betalningsid 2</label>
<input name="pay" type="text" id="pay" class="inputfield" tabindex=13 value="<?=$bd['pay']?>" />

</div>


<div style="clear:both">
<label for="comments" style="height:auto">Övrig information</label>
<textarea style="width:600px;" name="comments"  rows="6" name="comments" id="comments" tabindex=32 ><?=$bd['comments']?></textarea>
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
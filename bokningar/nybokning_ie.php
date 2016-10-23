<?php
require_once("db/auth/user.php");
$USER = new User();
//
$id = $USER->get_get_value("id");
$sched = null;
if ( $id !== "" ) {
  $bd = $USER->get_schedule( $id );
  //print_r($sched);
} else {
  //$id = $USER->get_next_schedule_id();
  $bd = $USER->new_schedule();
}

//print_r($bd);

$sf = $USER->get_schedule_status_def();
$m_status = $USER->def_to_menu( $sf, "status_menu", $bd['status'], "status" );

//$sf = $USER->get_schedule_projectcountry_def();
//$m_projectcountry = $USER->def_to_menu( $sf, "projectcountry_menu", $bd['projectcountry'], "projectcountry" );
$m_projectcountry = $USER->get_country_menu($bd['projectcountry']);

// project needs to be indexed via projectcountry
//$sf = $USER->get_schedule_project_def();
//$m_project = $USER->def_to_menu( $sf[$bd['projectcountry']], "project_menu", $bd['project'], "project" );
$m_project = $USER->get_project_menu( $bd['projectcountry'], $bd['project'] );

// For IE
$m_countryproject = $USER->get_countryproject_menu( $bd['projectcountry'], $bd['project'] );

$sf = $USER->get_schedule_fixflight_def();
$m_fixflight = $USER->def_to_menu( $sf, "fixflight_menu", $bd['fixflight'], "fixflight" );

$sf = $USER->get_schedule_fixtransport_def();
$m_fixtransport = $USER->def_to_menu( $sf, "fixtransport_menu", $bd['fixtransport'], "fixtransport" );

$sf = $USER->get_schedule_meddelaresa_def();
$m_meddelaresa = $USER->def_to_menu( $sf, "meddelaresa_menu", $bd['meddelaresa'], "meddelaresa" );

$cb_confirm = $USER->make_checkbox($bd['confirm'], "confirm", "Bokningsbekr&auml;ftelse skickad");
$cb_cinvoicecheck = $USER->make_checkbox($bd['cinvoicecheck'], "cinvoicecheck", "Betald");
$cb_info1check = $USER->make_checkbox($bd['info1check'], "info1check", "Klart");
$cb_info2check = $USER->make_checkbox($bd['info2check'], "info2check", "Klart");
$cb_ffd1check = $USER->make_checkbox($bd['ffd1check'], "ffd1check", "Betald");
$cb_ffd2check = $USER->make_checkbox($bd['ffd2check'], "ffd2check", "Betald");
$cb_ffd3check = $USER->make_checkbox($bd['ffd3check'], "ffd3check", "Betald");

/*
print "<pre>";
print_r($USER->get_paymethods());
print_r($USER->get_paymethod_warnings(100));
print $USER->warnings_to_flags($USER->get_paymethod_warnings(100));
print "</pre>";
*/
/*
  Array
(
    [0] => Array
        (
            [paymethodid] => 100
            [0] => 100
            [msg] => First one
            [1] => First one
        )

)
*/
$pms = $USER->get_paymethods();
$tmp = Array();
foreach ( $pms as $i => $pm ) {
  $tmp[$pm['paymethodid']] = $pm['msg'];
}
//print_r($tmp);
$m_paymethods = $USER->def_to_menu( $tmp, "paymethods_menu", $bd['paymethod'], "paymethod" );
/*
print "<pre>";
print_r($USER->get_all_projects());
print "--\n";
print_r($USER->projects_to_menu($USER->get_all_projects()));
print "</pre>";
*/
//print_r( $USER->get_country_menu("IND") );
//print_r( $USER->get_project_menu( "XXX", "XXX" ));
/*
print "<pre>";
print_r( $USER->get_warnings( 100 ) );
print_r( $USER->get_flags( 1000 ) );
print "</pre>";
*/
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

<link type="text/css" href="stylesheet.css" rel="Stylesheet" />

<script type="text/javascript" src="db/js/jquery-1.7.2.min.js"></script> 
<script type="text/javascript" src="db/js/jquery.form.js"></script>
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
  
  $("#arrivaldate").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#arrivaldate").datepicker('setDate', "<?=$bd['arrivaldate']?>");
  $("#info1").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#info1").datepicker('setDate', "<?=$bd['info1']?>");
  $("#info2").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#info2").datepicker('setDate', "<?=$bd['info2']?>");
  $("#ffd1").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#ffd1").datepicker('setDate', "<?=$bd['ffd1']?>");
  $("#ffd2").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#ffd2").datepicker('setDate', "<?=$bd['ffd2']?>");
  $("#ffd3").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#ffd3").datepicker('setDate', "<?=$bd['ffd3']?>");
  
  $('.nedtonadstart').each(function(i, obj) {
    deftext = $(obj).attr("deftext");
    clickrecall(obj, deftext);
 });

  $('#fixflight_menu').change(function() {
    //alert($('#fixflight_menu').prop("selectedIndex"));
  });
  $('#status_menu').change(function() {
    status = $("#status_menu option:selected").attr("status");
    $("#statusnr").val(status);
  });
  $('#project_menu').change(function() {
    // TODO: set hidden field with the IDs instead of the names
    // which end up in the menus. Or get it in beforesubmit?
    //alert( $('#project_menu').prop("selectedIndex")+"/"+$("#project_menu option:selected").attr("project") );
    paym = $("#project_menu option:selected").attr("paymethod");
    pid = $("#project_menu option:selected").attr("project");
    $("#pid").val(pid); // store in hidden field

    //alert(paym);
    // store this, or transfer when clicking submit.
  });
  $('#projectcountry_menu').change(function() {
    //alert($('#projectcountry_menu').prop("selectedIndex"));
    idx = $('#projectcountry_menu').prop("selectedIndex");
    cid = $("#projectcountry_menu option:selected").attr("projectcountry");
    //pid = $("#project_menu option:selected").attr("project");
    $("#cid").val(cid); //IND, etc
    pid = "XXX"; //changing country invalidates chosen project
    $("#pid").val(pid); // reset this also
    //$('#project_menu').empty();
    $.post("get_project_menu.php", {'pid':pid, 'cid':cid}).done(function(data) {
      //alert(data);
      //data = '"' + data + '"';    
      //$("#project_menu").html(data);
      //var newHTML = $('#project_menu').html();
      //$('#project_menu').html(newHTML.substr(1,newHTML.length-2));
	    $("#project_menu").html(data);
	    //$("#project_menu")[0].innerHTML = data;
	 });
//    $("#project_menu").load("get_project_menu.php",{'pid':pid, 'cid':cid});
  });
  
  // IE
  $('#countryproject_menu').change(function() {
    // TODO: set hidden field with the IDs instead of the names
    // which end up in the menus. Or get it in beforesubmit?
    //alert( $('#project_menu').prop("selectedIndex")+"/"+$("#project_menu option:selected").attr("project") );
    paym = $("#countryproject_menu option:selected").attr("paymethod");
    pid = $("#countryproject_menu option:selected").attr("project");
    cid = $("#countryproject_menu option:selected").attr("projectcountry");
    $("#pid").val(pid); // store in hidden field
    $("#cid").val(cid);
    //alert(paym);
    // store this, or transfer when clicking submit.
  });

  //$('#nybokningform').ajaxForm();
  //http://www.malsup.com/jquery/form/#validation
  var options = { 
        beforeSubmit: add_to_req  // pre-submit callback 
    }; 
 
  // bind form using 'ajaxForm' 
  $('#nybokningform').ajaxForm(options); 
    
  function add_to_req(formData, jqForm, options) { 
    pid = $('#project_menu').prop("selectedIndex");
    idx = $('#projectcountry_menu').prop("selectedIndex");
    cid = $("#projectcountry_menu option:selected").attr("projectcountry");
    //alert(pid+"/"+idx+"/"+cid);// 2/1/IND
    
    pid = $("#pid").val();
    if ( pid == "XXX" ) {
      alert("Choose project");
      return false;
    }
    
    //alert( formData[0].value );
    //var form = jqForm[0]; 
    //alert( form.bookingdate );
    
    //var queryString = $.param(formData); 
    //alert('About to submit: \n\n' + queryString); 
 
    // here we could return false to prevent the form from being submitted; 
    // returning anything other than false will allow the form submit to continue 
    return true; 
} 

});
</script>

	<script type="text/javascript">
	function clickclear(thisfield, defaulttext) {
	if (thisfield.value == defaulttext) {
	thisfield.value = "";
	thisfield.className="normaltext";
	}
	}
	
	function clickrecall(thisfield, defaulttext) {
	if ((thisfield.value == "") || (thisfield.value == deftext) ) {
	thisfield.value = defaulttext;
	thisfield.className="nedtonad";
	}
	}

	</script>


</head>

<!-- *****************************  B O D Y  ******************************* -->

<body>

<ul id="topmeny">
<li><img src="http://pixelz.se/solidaritytravels/Bilder/logo/logo-transp-1000_128x128x32.png" height="80px" /></li>
<li style="font-size:36px;padding-top:25px;padding-bottom:25px;color:#0C6A12">Solidarity Travels</li>
<li style="float:right"><a href="alla_bokningar.php">Bokningshistorik</a></li>
<li style="float:right"><a href="nybokning.php">Ny bokning</a></li>
<li style="float:right"><a href="index.php">Aktuellt</a></li>
</ul>


<form id="nybokningform" action="store_nybokning.php" method="post">

<h2>Skapa/visa bokning</h2>

<div id="bookingheader">

<div id="savebutton">
<input style="float:left;left:25px;" type="submit" value="Spara" /> 
</div>

<div name="bookid" id="bookid">Bokningsnummer: <?=$bd['nr']?></div>
<input type="hidden" id="bnr" name="bnr" value="<?=$bd['nr']?>" />
<input type="hidden" id="bid" name="bid" value="<?=$bd['id']?>" />

<label for="paystat">Status
<?= $m_status ?>
<input type="hidden" id="statusnr" name="statusnr" value="<?=$bd['status']?>" /></label>

<label for="bookingdate">Bokningsdatum
<input name="bookingdate" type="text" class="inputfield_short" id="bookingdate" tabindex=2 value="<?=$bd['bookingdate']?>" /></label>


<label for="project" style="height:55px">Volont&auml;rprogram
<?= $m_countryproject ?>
<input type="hidden" id="cid" name="cid" value="<?=$bd['projectcountry']?>" />
<input type="hidden" id="pid" name="pid" value="<?=$bd['project']?>" />
</label>

<label for="startdate" >Startdatum
<input name="startdate" type="text" class="inputfield_short" id="startdate" tabindex=13 value="<?=$bd['startdate']?>" /></label>
 
<label for="enddate" >Slutdatum
<input name="enddate" type="text" class="inputfield_short" id="enddate" tabindex=14 value="<?=$bd['enddate']?>" /></label>
</div>


<div class="box" style="clear:both;">

<!--Bekräftelse skickad till kund -->
<?= $cb_confirm ?>

<h3 style="clear:both;">Personliga uppgifter</h3>
<label for="lastname">Efternamn</label>
<input name="lastname" type="text" id="lastname" class="inputfield" tabindex=20 value="<?=$bd['lastname']?>"/> 

<label for="firstname">F&ouml;rnamn</label>
<input name="firstname" type="text" id="firstname" class="inputfield" tabindex=21 value="<?=$bd['firstname']?>" />

<label for="company">F&ouml;retagsnamn/Skola</label>
<input name="company" type="text" id="company" class="inputfield" tabindex=22 value="<?=$bd['company']?>" />

<label for="email">Email</label>
<input name="email" type="text" id="email" class="inputfield" tabindex=23 value="<?=$bd['email']?>" />

<label for="tel">Telefon</label>
<input name="tel" type="text" id="tel" class="inputfield" tabindex=24 value="<?=$bd['phone']?>" />

<label for="address_street">Gatuadress</label>
<input name="address_street" type="text" id="address_street" class="inputfield" tabindex=25 value="<?=$bd['street']?>" />

<label for="address_city">Postadress</label>
<input name="address_city" type="text" id="address_city" class="inputfield" tabindex=26 value="<?=$bd['city']?>" />

<label for="address_country">Land</label>
<input name="address_country" type="text" id="address_country" class="inputfield" tabindex=27 value="<?=$bd['country']?>" />
</div>

<div class="box">

<h3 style="clear:both">Inbetalning från kund</h3>


<label for="cinvoiceamount" class="labelfloat">Belopp
<input name="cinvoiceamount" type="text" id="cinvoiceamount" class="inputfield_short" tabindex=30 value="<?=$USER->fmt_money($bd['cinvoiceamount'])?>" /></label>
<!--Bekräftelse skickad till kund -->
<?= $cb_cinvoicecheck ?>


<label for="cinvoicedetails" style="clear:both;"></label>
<input name="cinvoicedetails" type="text" id="cinvoicedetails" class="inputfield nedtonadstart" deftext="Betalningsid/-datum, noteringar" onclick="clickclear(this, 'Betalningsid/-datum, noteringar')" onblur="clickrecall(this,'Betalningsid/-datum, noteringar')" tabindex=33 value="<?=$bd['cinvoicedetails']?>" />


<h3 style="clear:both;">Resedetaljer</h3>

<div>
<label for="arrivaldate" class="labelfloat">Ankomstdatum
<input name="arrivaldate" type="text" class="inputfield_short" id="arrivaldate" tabindex=40 value="<?=$bd['arrivaldate']?>" /></label>

<label for="arrivaltime" class="labelfloat">Ankomsttid
<input name="arrivaltime" type="text" class="inputfield_short" id="arrivaltime" tabindex=41 value="<?=$bd['arrivaltime']?>" /></label>
</div>

<label for="flight" class="labelfloat">Flightnummer
<input name="flight" type="text" class="inputfield_short" id="flight" tabindex=42 value="<?=$bd['flight']?>" /></label>

<br /> 
<textarea rows="4" name="transport" id="transport" class="nedtonadstart" deftext="&ouml;vriga resedetaljer" onclick="clickclear(this, '&ouml;vriga resedetaljer')" onblur="clickrecall(this,'&ouml;vriga resedetaljer')" tabindex=43 ><?=$bd['transport']?></textarea>


<h5>Mottagare av reseinformation</h5>
<label for="info1" class="labelfloat">Datum
<input name="info1" type="text" id="info1" class="inputfield_short" tabindex=50 value="<?=$bd['ffd1']?>" /></label>

<label for="info1rec" class="labelfloat">Mottagare
<input name="info1rec" type="text" id="info1rec" class="inputfield_short" tabindex=51 value="<?=$bd['info1rec']?>" /></label>

<!--Info skickad till mottagare 1 -->
<?= $cb_info1check ?>


<label for="info2" class="labelfloat" style="clear:both;">Datum
<input name="info2" type="text" id="info2" class="inputfield_short" tabindex=52 value="<?=$bd['info2']?>" /></label>

<label for="info2rec" class="labelfloat">Mottagare
<input name="info2rec" type="text" id="info2rec" class="inputfield_short" tabindex=53 value="<?=$bd['info2rec']?>" /></label>

<!--Info skickad till mottagare 2 -->
<?= $cb_info2check ?>
 
</div>




<div class="box">

<h3>Utbetalningar</h3>

<label for="totamount" class="labelfloat">Totalbelopp
<input name="totamount" type="text" id="totamount" class="inputfield_short" tabindex=60 value="<?=$USER->fmt_money($bd['totamount'])?>" /></label>
<label for="curr" class="labelfloat">Valuta
<input name="curr" type="text" id="curr" class="inputfield_short" tabindex=61 value="<?=$bd['curr']?>" /></label>

<div style="clear:both;padding-top:10px;"><input name="bankfee" type="text" readonly="readonly" id="bankfee" class="inputfield" value="<?=$bd['bankfee']?>" /></div>


<h5>Delbetalning 1</h5>
<label for="ffd1" class="labelfloat">F&ouml;rfallodag 1
<input name="ffd1" type="text" id="ffd1" class="inputfield_short" tabindex=63 value="<?=$bd['ffd1']?>" /></label>

<label for="ffd1amount" class="labelfloat">Belopp
<input name="ffd1amount" type="text" id="ffd1amount" class="inputfield_short" tabindex=64 value="<?=$USER->fmt_money($bd['ffd1amount'])?>" /></label>

<!-- Delbetalning 1 betald -->
<?= $cb_ffd1check ?>


<label for="ffd1notes" style="clear:both;padding-top:10px;">
<input name="ffd1notes" type="text" id="ffd1notes" class="inputfield nedtonadstart" deftext="Betalningsid/-datum, noteringar" onclick="clickclear(this, 'Betalningsid/-datum, noteringar')" onblur="clickrecall(this,'Betalningsid/-datum, noteringar')" tabindex=65 value="<?=$bd['ffd1notes']?>" /></label>

<h5>Delbetalning 2</h5>
<label for="ffd2" class="labelfloat">F&ouml;rfallodag 2
<input name="ffd2" type="text" id="ffd2" class="inputfield_short" tabindex=66 value="<?=$bd['ffd2']?>" /></label>

<label for="ffd2amount" class="labelfloat">Belopp
<input name="ffd2amount" type="text" id="ffd2amount" class="inputfield_short" tabindex=67 value="<?=$USER->fmt_money($bd['ffd2amount'])?>" /></label>

<!-- Delbetalning 2 betald -->
<?= $cb_ffd2check ?>


<label for="ffd2notes" style="clear:both;padding-top:10px;"></label>
<input name="ffd2notes" type="text" id="ffd2notes" class="inputfield nedtonadstart" deftext="Betalningsid/-datum, noteringar" onclick="clickclear(this, 'Betalningsid/-datum, noteringar')" onblur="clickrecall(this,'Betalningsid/-datum, noteringar')" tabindex=68 value="<?=$bd['ffd2notes']?>" />


<h5>Kostnader för transporter</h5>
<label for="ffd3" class="labelfloat">F&ouml;rfallodag 3
<input name="ffd3" type="text" id="ffd3" class="inputfield_short" tabindex=69 value="<?=$bd['ffd3']?>" /></label>

<label for="ffd3amount" class="labelfloat">Belopp
<input name="ffd3amount" type="text" id="ffd3amount" class="inputfield_short" tabindex=70 value="<?=$USER->fmt_money($bd['ffd3amount'])?>" /></label>

<!-- Delbetalning 3 betald -->
<?= $cb_ffd3check ?>


<label for="ffd3notes"  style="clear:both;padding-top:10px;">
<input name="ffd3notes" type="text" id="ffd3notes" class="inputfield nedtonadstart" deftext="Betalningsid/-datum, noteringar" onclick="clickclear(this, 'Betalningsid/-datum, noteringar')" onblur="clickrecall(this,'Betalningsid/-datum, noteringar')" tabindex=71 value="<?=$bd['ffd3notes']?>" /></label>

</div>


<div class="box" style="clear:both;width:982px;height:300px;margin-top:20px;margin-bottom:50px;">
<label for="comments" style="height:auto">Övrig information</label>
<textarea style="width:940px;" name="comments"  rows="15" name="comments" id="comments" tabindex=72 ><?=$bd['comments']?></textarea>
</div>

</form>

<footer>
	<p>Copyright © <a tabindex="99" href="http://itmasala.se">IT Masala HB</a></p>
</footer>


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
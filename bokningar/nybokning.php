<?php
require_once("db/auth/user.php");
$USER = new User();
if ( $USER->un === "guest" ) {
  // goto login
  header('Location: '.$USER->get_login_link());
}
//
$id = $USER->get_get_value("id");
$sched = null;
if ( $id !== "" ) {
  $bd = $USER->get_schedule( $id );
} else {
  // Do we have nr instead of id?
  $nr = $USER->get_get_value("nr");
  if ( $nr === "" ) { //no, new bokning
    //$id = $USER->get_next_schedule_id();
    $bd = $USER->new_schedule();
  } else { // no id, but new nr
    $bd = $USER->get_schedule_by_nr( $nr );
  }
}
//print_r($bd);
$prev_id = $USER->get_previous_id($bd['id']);
$next_id = $USER->get_next_id($bd['id']);
$last_nr = $USER->get_last_schedule_nr();

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

$ua = $_SERVER['HTTP_USER_AGENT'];
$ie = 0;
if ( preg_match("/Trident/", $ua)===1 ) {
  $ie = 1;
}
if ( preg_match("/OmniWeb/", $ua)===1 ) {
  $ie = 1;
}
$sf = $USER->get_schedule_fixflight_def();
$m_fixflight = $USER->def_to_menu( $sf, "fixflight_menu", $bd['fixflight'], "fixflight" );

$sf = $USER->get_schedule_fixtransport_def();
$m_fixtransport = $USER->def_to_menu( $sf, "fixtransport_menu", $bd['fixtransport'], "fixtransport" );

$sf = $USER->get_schedule_meddelaresa_def();
$m_meddelaresa = $USER->def_to_menu( $sf, "meddelaresa_menu", $bd['meddelaresa'], "meddelaresa" );

$cb_confirm = $USER->make_checkbox($bd['confirm'], "confirm", "Bokningsbekr&auml;ftelse skickad");
$cb_cinvoicecheck1 = $USER->make_checkbox($bd['cinvoicecheck1'], "cinvoicecheck1", "Betald");
$cb_cinvoicecheck2 = $USER->make_checkbox($bd['cinvoicecheck2'], "cinvoicecheck2", "");
$cb_info1check = $USER->make_checkbox($bd['info1check'], "info1check", "Klart");
$cb_info2check = $USER->make_checkbox($bd['info2check'], "info2check", "");
$cb_ffd1check = $USER->make_checkbox($bd['ffd1check'], "ffd1check", "Betald");
$cb_ffd2check = $USER->make_checkbox($bd['ffd2check'], "ffd2check", "");
$cb_ffd3check = $USER->make_checkbox($bd['ffd3check'], "ffd3check", "");

//$m_paymethods = $USER->def_to_menu( $tmp, "paymethods_menu", $bd['paymethod'], "paymethod" );

// Fill in the "rules" fields, iff they are empty, not if paymethod is 0 (default when empty)
if ( (intVal($bd['paymethod']) != 0) && ($bd['totamount'] != "0.00") ) {
  $res = $USER->apply_rules( $bd );
  foreach( $res as $r ) {
    $fld = $bd[ $r['df'] ];
    if ( $fld != "NF" ) { //NF=not fired
      $bd[ $r['df'] ] = $r['res'];
    }
    $fld = $bd[ $r['mf'] ];
    if ( $fld != "NF" ) { // NF=not fired
      $bd[ $r['mf'] ] = $r['r'];
    }
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"  lang="sv" xml:lang="sv">
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
$(document).ready(function() {
  $("#bookingdate").datepicker( { dateFormat: "yy-mm-dd", firstDay: 1 } );
  $("#enddate").datepicker( { dateFormat: "yy-mm-dd", firstDay: 1 } );
  $('#enddate').datepicker('setDate', "<?=$bd['enddate']?>");
  $("#startdate").datepicker( { dateFormat:"yy-mm-dd", firstDay:1, 
    onClose: function(dateText, inst) {
            $('#enddate').datepicker('setDate', dateText); //only if not bookingsdate?
        }
    }); 
  $("#startdate").datepicker('setDate', "<?=$bd['startdate']?>");
  
  $("#arrivaldate").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#arrivaldate").datepicker('setDate', "<?=$bd['arrivaldate']?>");
//  $("#info1").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
//  $("#info1").datepicker('setDate', "<?=$bd['info1']?>");
//  $("#info2").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
//  $("#info2").datepicker('setDate', "<?=$bd['info2']?>");
//  $("#ffd1").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
//  $("#ffd1").datepicker('setDate', "<?=$bd['ffd1']?>");
  $("#cffd1").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#cffd1").datepicker('setDate', "<?=$bd['cffd1']?>");
  $("#cffd2").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
  $("#cffd2").datepicker('setDate', "<?=$bd['cffd2']?>");
//  $("#ffd2").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
//  $("#ffd2").datepicker('setDate', "<?=$bd['ffd2']?>");
//  $("#ffd3").datepicker( { dateFormat:"yy-mm-dd", firstDay:1 } );
//  $("#ffd3").datepicker('setDate', "<?=$bd['ffd3']?>");
  
  $('.nedtonadstart').each(function(i, obj) {
    deftext = $(obj).attr("deftext");
    clickrecall(obj, deftext);
 });

  $('#fixflight_menu').change(function() {
    //alert($('#fixflight_menu').prop("selectedIndex"));
  });
  $('#status_menu').change(function() {
    newstatus = $("#status_menu option:selected").attr("status");
    $("#statusnr").val(newstatus);
  });
  $('#project_menu').change(function() {
    // TODO: set hidden field with the IDs instead of the names
    // which end up in the menus. Or get it in beforesubmit?
    //alert( $('#project_menu').prop("selectedIndex")+"/"+$("#project_menu option:selected").attr("project") );
    paym = $("#project_menu option:selected").attr("paymethod");
    $("#paymethod").val(paym); // store in hidden field
    pid = $("#project_menu option:selected").attr("project");
    $("#pid").val(pid); // store in hidden field

    bc = $("#project_menu option:selected").attr("bankcosts");
    if ( bc == "1" ) {
      $("#bankcosts").val("Alla bankavgifter");
    } else if ( bc == "2" ) {
      $("#bankcosts").val("bankcosts2");
    } else {
      $("#bankcosts").val("Egna bankavgifter");
    }

    v = $("#project_menu option:selected").attr("valuta");
    if ( v != "" ) {
      $("#curr").val(v);
    }
    i1r = $("#project_menu option:selected").attr("info1rec");
    if ( i1r != "" ) {
      $("#info1rec").val(i1r);
    }
    i2r = $("#project_menu option:selected").attr("info2rec");
    if ( i2r != "" ) {
      $("#info2rec").val(i2r);
    }

    i3r = $("#project_menu option:selected").attr("ffd1rec");
    if ( i3r != "" ) {
      $("#ffd1rec").val(i3r);
    }

    i4r = $("#project_menu option:selected").attr("ffd2rec");
    if ( i4r != "" ) {
      $("#ffd2rec").val(i4r);
    }

    i5r = $("#project_menu option:selected").attr("ffd3rec");
    if ( i5r != "" ) {
      $("#ffd3rec").val(i5r);
    }

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
    $("#paymethod").val(paym); // store in hidden field
    pid = $("#countryproject_menu option:selected").attr("project");
    cid = $("#countryproject_menu option:selected").attr("projectcountry");
    $("#pid").val(pid); // store in hidden field
    $("#cid").val(cid);
    
    bc = $("#countryproject_menu option:selected").attr("bankcosts");
    if ( bc == "1" ) {
      $("#bankcosts").val("Alla bankavgifter");
    } else if ( bc == "2" ) {
      $("#bankcosts").val("bankcosts2");
    } else {
      $("#bankcosts").val("Egna bankavgifter");
    }
    
    v = $("#countryproject_menu option:selected").attr("valuta");
    if ( v != "" ) {
      $("#curr").val(v);
    }
    i1r = $("#countryproject_menu option:selected").attr("info1rec");
    if ( i1r != "" ) {
      $("#info1rec").val(i1r);
    }
    i2r = $("#countryproject_menu option:selected").attr("info2rec");
    if ( i2r != "" ) {
      $("#info2rec").val(i2r);
    }

    i3r = $("#countryproject_menu option:selected").attr("ffd1rec");
    if ( i3r != "" ) {
      $("#ffd1rec").val(i3r);
    }

    i4r = $("#countryproject_menu option:selected").attr("ffd2rec");
    if ( i4r != "" ) {
      $("#ffd2rec").val(i4r);
    }

    i5r = $("#countryproject_menu option:selected").attr("ffd3rec");
    if ( i5r != "" ) {
      $("#ffd3rec").val(i5r);
    }


    //alert(paym);
    // store this, or transfer when clicking submit.
  });

  //$('#nybokningform').ajaxForm();
  //http://www.malsup.com/jquery/form/#validation
  var options = { 
        beforeSubmit: add_to_req,  // pre-submit callback 
        success: function() { 
          alert('Bokningen är sparad');
          window.location.replace("nybokning.php?nr=<?=$bd['nr']?>");
        }
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
      alert("Välj ett projekt");
      return false;
    }
    
    thestatus = $("#status_menu option:selected").attr("status");
    if ( thestatus == 0 ) {
      alert("Välj en status på registreringen");
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

<style type="text/css">
</style>

</head>

<!-- *****************************  B O D Y  ******************************* -->

<body>
<!--
<?php
print_r($res);
/*foreach( $res as $r ) {
  print_r($r);
  print "Result of rule: ".date_format($r['res'], 'Y-m-d')."\n";
  print "Databsefield: (".$bd[ $r['df'] ].")\n";
  print "databasefield: (".$bd[ $r['mf'] ].")\n";
}*/
?>
-->

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

<form id="nybokningform" action="store_nybokning.php" method="post">

<h2>Skapa/visa bokning</h2>

<div id="nextprev">

<a href="nybokning.php" style="margin-right:40px;">Skapa ny bokning</a>

<?php
if ( $prev_id != -1 ) {
?>
<a href="nybokning.php?id=<?=$prev_id?>">F&ouml;reg&aring;ende bokning</a>
<?php
}
?>
<?php
if ( ($next_id != -1) && ($prev_id != -1) ) {
?>
<span class="arrow">↔</span>
<?php
}
?>
<?php
if ( $next_id != -1 ) {
?>
<a href="nybokning.php?id=<?=$next_id?>">N&auml;sta bokning</a>
<?php
}
?>
</div>

<div id="bookingheader">

<div id="savebutton">
<input style="float:left;left:50px;" type="submit" value="Spara" /> 
</div>

<div name="bookid" id="bookid">Bokningsnummer: <?=$bd['nr']?></div>
<input type="hidden" id="bnr" name="bnr" value="<?=$bd['nr']?>" />
<input type="hidden" id="bid" name="bid" value="<?=$bd['id']?>" />
<input type="hidden" id="paymethod" name="paymethod" value="<?=$bd['paymethod']?>" />

<label for="grpid" >Gruppid
<input name="grpid" type="text" class="inputfield_short" id="grpid" tabindex=13 value="<?=$bd['grpid']?>" /></label>

<label for="paystat">Status
<?= $m_status ?>
<input type="hidden" id="statusnr" name="statusnr" value="<?=$bd['status']?>" /></label>

<label for="bookingdate">Bokningsdatum
<input name="bookingdate" type="text" class="inputfield_short" id="bookingdate" tabindex=2 value="<?=$bd['bookingdate']?>" /></label>

<label for="project" style="height:55px">Volont&auml;rprogram
<?php
if ( $ie===1 ) {
  print $m_countryproject;
} else {
  print $m_projectcountry."<br />".$m_project;  
}
?> 
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

<div class="box-wide" style="margin-top:0px;">

<h3 style="clear:both;">Inbetalning från kund</h3>

<label for="cinvoiceamount" class="labelfloat">Totalbelopp
<input name="cinvoiceamount" type="text" id="cinvoiceamount" class="inputfield_short" tabindex=30 value="<?=$USER->fmt_money($bd['cinvoiceamount'])?>" /></label>


<h5 style="clear:both;">Planerade och utförda betalningar</h5>

<label for="cffd1" class="labelfloat">F&ouml;rfallodag 1
<input name="cffd1" type="text" id="cffd1" class="inputfield_short" tabindex=31 value="<?=$bd['cffd1']?>" /></label>

<label for="cffd1amount" class="labelfloat">Belopp
<input name="cffd1amount" type="text" id="cffd1amount" class="inputfield_short" tabindex=32 value="<?=$USER->fmt_money($bd['cffd1amount'])?>" /></label>

<label for="cinvoicedetails1" style="float:left;width:315px;">Betalningsid/-datum, noteringar
<input name="cinvoicedetails1" type="text" id="cinvoicedetails1" class="inputfield" tabindex=33 value="<?=$bd['cinvoicedetails1']?>" /></label>
<!--Bekräftelse skickad till kund -->
<?= $cb_cinvoicecheck1 ?>

<label for="cffd2" class="labelfloat" style="clear:both;">
<input name="cffd2" type="text" id="cffd2" class="inputfield_short" tabindex=34 value="<?=$bd['cffd2']?>" /></label>

<label for="cffd2amount" class="labelfloat">
<input name="cffd2amount" type="text" id="cffd2amount" class="inputfield_short" tabindex=35 value="<?=$USER->fmt_money($bd['cffd2amount'])?>" /></label>

<label for="cinvoicedetails2" style="float:left;width:315px;">
<input name="cinvoicedetails2" type="text" id="cinvoicedetails2" class="inputfield" tabindex=36 value="<?=$bd['cinvoicedetails2']?>" /></label>
<!--Bekräftelse skickad till kund -->
<?= $cb_cinvoicecheck2 ?>
 
</div>

<div class="box-wide">
<label for="comments" style="height:auto">Övrig information</label>
<textarea style="width:620px;" name="comments"  rows="13" name="comments" id="comments" tabindex=100 ><?=$bd['comments']?></textarea>
</div>



<div class="box-full">

<h3>Utbetalningar</h3>

<label for="totamount" class="labelfloat">Totalbelopp
<input name="totamount" type="text" id="totamount" class="inputfield_short" tabindex=60 value="<?=$USER->fmt_money($bd['totamount'])?>" /></label>
<label for="curr" class="labelfloat">Valuta
<input name="curr" type="text" id="curr" class="inputfield_short autofill" tabindex=61 value="<?=$bd['curr']?>" /></label>
<label for="bankcosts" class="labelfloat">&nbsp;
<input name="bankcosts" type="text" readonly="readonly" id="bankcosts" class="inputfield" value="<?=$bd['bankcosts']?>" /></label>


<h5 style="clear:both">Planerade och utförda betalningar</h5>
<label for="ffd1" class="labelfloat">F&ouml;rfallodag
<input name="ffd1" readonly="readonly" type="text" id="ffd1" class="inputfield_short autofill" tabindex=63 value="<?=$bd['ffd1']?>" /></label>

<label for="ffd1amount" class="labelfloat">Belopp
<input name="ffd1amount" readonly="readonly" type="text" id="ffd1amount" class="inputfield_short autofill" tabindex=64 value="<?=$USER->fmt_money($bd['ffd1amount'])?>" /></label>

<label for="ffd1rec" style="float:left;width:315px;">Mottagare
<input name="ffd1rec" type="text" id="ffd1rec" class="inputfield autofill" tabindex=66 value="<?=$bd['ffd1rec']?>"/> </label>

<label for="ffd1notes" style="float:left;width:315px;">Betalningsid/-datum, noteringar
<input name="ffd1notes" type="text" id="ffd1notes" class="inputfield" tabindex=67 value="<?=$bd['ffd1notes']?>" /></label>

<!-- Delbetalning 1 betald -->
<?= $cb_ffd1check ?>

<label for="ffd2" class="labelfloat" style="clear:both;">
<input name="ffd2" readonly="readonly" type="text" id="ffd2" class="inputfield_short autofill" tabindex=68 value="<?=$bd['ffd2']?>" /></label>

<label for="ffd2amount" class="labelfloat">
<input name="ffd2amount" readonly="readonly" type="text" id="ffd2amount" class="inputfield_short autofill" tabindex=69 value="<?=$USER->fmt_money($bd['ffd2amount'])?>" /></label>

<label for="ffd2rec" style="float:left;width:315px;">
<input name="ffd2rec" type="text" id="ffd2rec" class="inputfield autofill" tabindex=70 value="<?=$bd['ffd2rec']?>"/> </label>

<label for="ffd2notes" style="float:left;width:315px;">
<input name="ffd2notes" type="text" id="ffd2notes" class="inputfield" tabindex=72 value="<?=$bd['ffd2notes']?>" /></label>

<!-- Delbetalning 2 betald -->
<?= $cb_ffd2check ?>


<h5 style="clear:both;">Kostnader för transporter</h5>
<label for="ffd3" class="labelfloat" style="clear:both;">
<input name="ffd3" readonly="readonly" type="text" id="ffd3" class="inputfield_short autofill" tabindex=73 value="<?=$bd['ffd3']?>" /></label>

<label for="ffd3amount" class="labelfloat">
<input name="ffd3amount" type="text" id="ffd3amount" class="inputfield_short" tabindex=74 value="<?=$USER->fmt_money($bd['ffd3amount'])?>" /></label>

<label for="ffd3rec" style="float:left;width:315px;">
<input name="ffd3rec" type="text" id="ffd3rec" class="inputfield autofill" tabindex=75 value="<?=$bd['ffd3rec']?>"/> </label>

<label for="ffd3notes" style="float:left;width:315px;">
<input name="ffd3notes" type="text" id="ffd3notes" class="inputfield" tabindex=77 value="<?=$bd['ffd3notes']?>" /></label>

<!-- Delbetalning 3 betald -->
<?= $cb_ffd3check ?>

</div>

<div id="otherinfo" class="box">

<h3>Reseinformation</h3>

<div id="ankomst" style="float:left;">
<h5 style="padding-top:0;">Resedetaljer</h5>

<label for="arrivaldate" class="labelfloat">Ankomstdatum
<input name="arrivaldate" type="text" class="inputfield_short" id="arrivaldate" tabindex=90 value="<?=$bd['arrivaldate']?>" /></label>

<label for="arrivaltime" class="labelfloat">Ankomsttid
<input name="arrivaltime" type="text" class="inputfield_short" id="arrivaltime" tabindex=91 value="<?=$bd['arrivaltime']?>" /></label>

<label for="flight" class="labelfloat">Flightnummer
<input name="flight" type="text" class="inputfield_short" id="flight" tabindex=92 value="<?=$bd['flight']?>" /></label>

<br /> 
<textarea style="width:440px;float:left" rows="8" name="transport" id="transport" class="nedtonadstart" deftext="&ouml;vriga resedetaljer" onclick="clickclear(this, '&ouml;vriga resedetaljer')" onblur="clickrecall(this,'&ouml;vriga resedetaljer')" tabindex=93 ><?=$bd['transport']?></textarea>
</div>


<div style="width:530px;float:left;padding-left:35px;">
<h5 style="padding-top:0;">Mottagare av reseinformation</h5>
<label for="info1" class="labelfloat">Datum
<input name="info1" readonly="readonly" type="text" id="info1" class="inputfield_short autofill" tabindex=94 value="<?=$bd['info1']?>" /></label>

<label for="info1rec" class="labelfloat" style="width:320px;">Mottagare
<input name="info1rec" type="text" id="info1rec" class="inputfield autofill" tabindex=95 value="<?=$bd['info1rec']?>" /></label>
<!--Info skickad till mottagare 1 -->
<?= $cb_info1check ?>


<label for="info2" class="labelfloat" style="clear:both;" >
<input name="info2" readonly="readonly" type="text" id="info2" class="inputfield_short autofill" tabindex=97 value="<?=$bd['info2']?>" /></label>

<label for="info2rec" class="labelfloat" style="width:320px;">
<input name="info2rec" type="text" id="info2rec" class="inputfield autofill" tabindex=98 value="<?=$bd['info2rec']?>" /></label>

<!--Info skickad till mottagare 2 -->
<?= $cb_info2check ?>

</div>

</div>



</form>

</div>

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
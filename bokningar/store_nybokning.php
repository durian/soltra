<?php
require_once("db/auth/user.php");
$USER = new User();

$json_str = stripslashes($_POST['json']);
$json_data = json_decode( $json_str );

$bid = $USER->get_post_value("bid");
// field in form, field in DB
// note that we have statusnr to status because the jquery.form
// code send the text in the menu as status_menu. The statusnr is a hidden
// field.
$fields = array(
    "bookingdate" => "bookingdate",
    "statusnr" => "status",
    "pid" => "project",
    "grpid" => "grpid",
    "cid" => "projectcountry",
    "paymethod" => "paymethod",
    "startdate" => "startdate", 
    "enddate" => "enddate", 
    "confirm" => "confirm",
    "lastname" => "lastname",
    "firstname" => "firstname",
    "company" => "company",
    "email" => "email",
    "tel" => "phone",
    "address_street" => "street",
    "address_city" => "city", 
    "address_country" => "country", 
    "cffd1" => "cffd1",    
    "cffd2" => "cffd2",    
    "cinvoiceamount" => "cinvoiceamount",
    "cffd1amount" => "cffd1amount",
    "cffd2amount" => "cffd2amount",
    "cinvoicecheck1" => "cinvoicecheck1",
    "cinvoicecheck2" => "cinvoicecheck2",
    "cinvoicedetails1" => "cinvoicedetails1",
    "cinvoicedetails2" => "cinvoicedetails2",
    "transport" => "transport",
    "arrivaldate" => "arrivaldate",
    "arrivaltime" => "arrivaltime",
    "flight" => "flight",
    "info1" => "info1",
    "info1rec" => "info1rec",
    "info1check" => "info1check",
    "info2" => "info2",
    "info2rec" => "info2rec",
    "info2check" => "info2check",
    "totamount" => "totamount",
    "curr" => "curr",
    "bankcosts" => "bankcosts",    
    "ffd1" => "ffd1",    
    "ffd1amount" => "ffd1amount",    
    "ffd1check" => "ffd1check",    
    "ffd1" => "ffd1",    
    "ffd1amount" => "ffd1amount",    
    "ffd1check" => "ffd1check",    
    "ffd1rec" => "ffd1rec",    
    "ffd1notes" => "ffd1notes",    
    "ffd2" => "ffd2",    
    "ffd2amount" => "ffd2amount",    
    "ffd2check" => "ffd2check",    
    "ffd2rec" => "ffd2rec",    
    "ffd2notes" => "ffd2notes",    
    "ffd3" => "ffd3",    
    "ffd3amount" => "ffd3amount",    
    "ffd3check" => "ffd3check",    
    "ffd3rec" => "ffd3rec",    
    "ffd3notes" => "ffd3notes",    
    "comments" => "comments"
);

// doubtful:
if ( $USER->get_post_value("confirm") === "on" ) {
  $_POST["confirm"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["confirm"] = 0;
}

if ( $USER->get_post_value("cinvoicecheck1") === "on" ) {
  $_POST["cinvoicecheck1"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["cinvoicecheck1"] = 0;
}

if ( $USER->get_post_value("cinvoicecheck2") === "on" ) {
  $_POST["cinvoicecheck2"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["cinvoicecheck2"] = 0;
}

if ( $USER->get_post_value("info1check") === "on" ) {
  $_POST["info1check"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["info1check"] = 0;
}

if ( $USER->get_post_value("info2check") === "on" ) {
  $_POST["info2check"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["info2check"] = 0;
}

if ( $USER->get_post_value("ffd1check") === "on" ) {
  $_POST["ffd1check"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["ffd1check"] = 0;
}
if ( $USER->get_post_value("ffd2check") === "on" ) {
  $_POST["ffd2check"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["ffd2check"] = 0;
}

if ( $USER->get_post_value("ffd3check") === "on" ) {
  $_POST["ffd3check"] = 1;
} else {
  // it's not in POST if not checked.
  $_POST["ffd3check"] = 0;
}

$m_flds = array( "cinvoiceamount", "cffd1amount", "cffd2amount", "totamount", "ffd1amount", "ffd2amount", "ffd3amount" );

$v = array();
$q = "update schedule set ";
foreach( $fields as $f => $dbf) {
  $val = $USER->get_post_value($f);
  if ( in_array($dbf, $m_flds) ) {
      $val = $USER->money_to_float($val);
  }
  if ( 1 || ($val !== "") ) { //how to empy a field?
    $q .= $dbf."=?,";
    $v[] = $val;
  }
}
$q = substr($q,0,-1);
$q .= " where id=".$bid;

//echo  $q;
$db = $USER->get_db();
$stmt = $db->prepare($q);
$stmt->execute($v);

//print_r($_POST);
?>

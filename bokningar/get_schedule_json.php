<?php
require_once("db/auth/user.php");
$USER = new User();

$type = $USER->get_get_value("type");
if ( $type === "future" ) {
  $result = $USER->get_future_schedule();
} else if ( $type === "past" ) {
  $result = $USER->get_past_schedule();
} else {
  $result = $USER->get_full_schedule();
}
//print_r($result);
$cols = array( "nr", "grpid", "bookingdate", "status", "firstname", "lastname", "street", "postcode", "city", "phone", "email", "startdate", "enddate", "cinvoiceamount", "cffd1", "cffd2", "totamount", "curr", "ffd1", "ffd2", "ffd3", "info1", "info2", "flightnrto", "traveltodate", "flightnrback", "travelbackdate", "transport", "comments" );
foreach ($result as $r) {
  $data = array();
  foreach( $cols as $col ) {
    $data[$col] = $r[$col];
  }
  $data["project"] = $r["projectcountry"]."/".$r["project"]; //tailor made
  $arr[] = $data;
}

$json = '{ "aaData":'.json_encode($arr).' }';
print $json;
//print $all;
?>

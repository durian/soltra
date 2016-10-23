<?php
require_once("auth/user.php");
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
$cols = array( "nr", "firstname", "lastname", "street", "postcode", "city", "phone", "email", "project", "startdate", "enddate", "flightnrto", "traveltodate", "flightnrback", "travelbackdate", "transport" );
foreach ($result as $r) {
  $data = array();
  foreach( $cols as $col ) {
    $data[$col] = $r[$col];
  }
  $arr[] = $data;
}
$json = '{ "aaData":'.json_encode($arr).' }';
print $json;
//print $all;
?>
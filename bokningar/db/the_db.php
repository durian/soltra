<?php

function get_db() {
  $DBNAME="sqlite:priser.sqll";
  $db = null;

  try {
    //create or open the database
    $db = new PDO($DBNAME);
  } catch(Exception $e) {
    die("error");
  }
  $db->setAttribute(PDO::ATTR_TIMEOUT, 10);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
  return $db;
}

// --

function get_userid( $db, $ui ) {
  if ( $db == NULL ) {
    $db = get_db();
  }
  $stmt = $db->prepare('select userid from users where userid = :userid');
  $stmt->execute( array('userid' => $ui) );
  $result = $stmt->fetchAll();
  return $result;
}

/*
sqlite> .schema points
*/

// http://kennyheer.blogspot.com/2010/10/geojson-in-php.html
// http://stackoverflow.com/questions/6452748/openlayers-parsed-geojson-points-always-display-at-coords0-0

function get_all($db) {
  if ( $db == NULL ) {
    $db = get_db();
  }
  $now = time();
  try {
    $stmt = $db->prepare("select * from trailers order by name");
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
}

//http://www.w3schools.com/sql/sql_join_inner.asp
function get_stock($db) {
  if ( $db == NULL ) {
    $db = get_db();
  }
  $now = time();
  try {
    $stmt = $db->prepare("SELECT * FROM stock INNER JOIN trailers ON stock.trailerid=trailers.trailerid");
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
}

// Get one trailer from stock with chassisnr=cnr
function get_stock_trailer($db, $cnr) {
  if ( $db == NULL ) {
    $db = get_db();
  }
  try {
    //$stmt = $db->prepare("SELECT * FROM stock INNER JOIN trailers ON stock.trailerid=trailers.trailerid WHERE stock.chassis=:cnr");
    $stmt = $db->prepare("SELECT * FROM stock WHERE chassis=:cnr");
    $stmt->execute( array('cnr' => $cnr) );
    $result = $stmt->fetchAll();
    return $result;
  } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
  }
}

/*
{
  "sEcho": 1,
  "iTotalRecords": "57",
  "iTotalDisplayRecords": "57",
  "aaData": [
    [
      "Gecko",
      "Firefox 1.0",
      "Win 98+ / OSX.2+",
      "1.7",
      "A"
    ],
    ...
*/
function _db_result_to_json( $res, $se ) {
  $i = 0;
  foreach ($res as $r) {
    $arr[] = array(
      $r['model'],
      $r['type'],
      $r['name']
    );
    // end of array
    $i++;
  }
  //$json = '{"sEcho":'.$se.', "iTotalRecords":"'.$i.'", "iTotalDisplayRecords":"'.$i.'", "aaData":'.json_encode($arr).'}';
  $json = '{"sEcho":'.$se.', "iTotalRecords":"'.$i.'", "iTotalDisplayRecords":"'.$i.'", "aaData":'.json_encode($res).'}';
  return $json;
}
// For the trailers table.
function db_result_to_json( $res ) {
  $cols = array( "trailerid" => "m", "type" => "t", "name" => "n", "length" => "l", "width" => "w", "totalweight" => "tw", "maxload" => "ml" );
  foreach ($res as $r) {
    $data = array();
    foreach( $cols as $col => $abbr ) {
      $data[$abbr] = $r[$col];
    }
    $arr[] = $data;
  }
  $json = '{ "aaData":'.json_encode($arr).' }';
  return $json;
}

?>
<?php
//include("the_db.php");
//include("util.php");

class User {

  public $un = "guest";
  public $role = "guest";
  public $tl = 0;
  public $uid = 0;
  
  public function check() {
    
    if ( isset($_COOKIE['todo_loggedin']) ) {
      $loggedin = $_COOKIE['todo_loggedin'];
      //print "cookie: ".$loggedin;
      $db = $this->get_db();
      $u = $this->get_user_from_token( $db, $loggedin );
      // should check if user, otherwise cookie can be copied?
      $this->user_touch($db, $u);
      //$u = get_user_from_token( $db, $loggedin );//to update timeleft?
      $this->un = $u["username"];
      $this->role = $u["role"];
      $this->tl = $u["timeleft"];//timeleft is from before touch
      $this->uid = $u["userid"];
    } else {
      //print "not logged in.";
    }
  }
  
  function __construct() {
    $this->check();
  }
  
  public function header() {
    //print loggedin/out/...";
    $root = "auth";
    print "<style>.auth_he { margin-bottom:8px; }.auth_un { padding-left:4px;padding-right:8px; }</style>\n"; 
    print "<!-- ".$this->tl." -->\n";
    if ( $this->un !== "guest" ) {
      print "<div class=\"auth_he\">Logged in as: ";
      print "<span class='auth_un'>".$this->un."</span>";
      print "<span class='auth_un'><a href=\"".$root."/login.php\">logout</a></span>";
      print "<span class='auth_un'><a href=\"".$root."/change_password.php\">change password</a></span>";
      print "</div>";
    } else {
      print "<div class=\"auth_he\">Logged in as: ";
      print "<span class='auth_un'>".$this->un."</span>";
      print "<span class='auth_un'><a href=\"".$root."/login.php\">login</a></span>";
      print "<span class='auth_un'><a href=\"".$root."/register.php\">register</a></span>";
      print "</div>";
    }
  }
  
  public function get_start_link() {
    return "http://www.solidaritytravels.se/bokningar/index.php";
  } 

  public function get_login_link() {
    return "http://www.solidaritytravels.se/bokningar/db/auth/login.php";
  }
  
  public function get_registration_link() {
    return "store_registration_email.php";
  } 

  public function get_reset_link() {
    return "store_reset_email.php";
  } 
  
  public function get_activation_link() {
    return "www.solidaritytravels.se/bokningar/db/auth/activate.php";
  }
  
  public function get_change_link() {
    return "http://www.solidaritytravels.se/bokningar/db/auth/change_password.php";
    }
  
  //json_encode(array("name"=>"John","time"=>"2pm")), js: log(data.name);
  public function err($e) {
    $msg = "Error";
    switch ($e) {
      case 0: $msg = "Klart.";break;
      case 1: $msg = "Fel användarnamn och/eller lösenord.";break;
      case 2: $msg = "Ogiltigt lösenord.";break;
      case 3: $msg = "Användarnamnet finns inte.";break;
      case 4: $msg = "Kombinationen användarnamn/lösenord är inte tillåten.";break;
      case 5: $msg = "Ett mail med återställning av lösenord har skickats till den registrerade adressen.";break;
      case 6: $msg = "Uppdateringen avbröts, du är troligen utloggad.";break;
      default: $msg = "Ett fel uppstod.";
    } 
    return json_encode(array("code"=>$e,"msg"=>$msg));
  }
  
  public function msg($m) {
    switch ($m) {
      case 0: return("OK");
      case 1: return("Fyll i ett användarnamn.");
      case 2: return("Lösenorden stämmer inte överens.");
      case 3: return("Fyll i ett användarnamn.");
      case 4: return("Fyll i en mailadress.");
      case 5: return("Fyll i lösenord.");
      case 6: return("Registreringen är klar.");
      default: return("?");
    } 
  }

  public function txt($msg) {
    return json_encode(array("code"=>1,"msg"=>$msg));
  }
  
  public function get_db_sqlite3() {
    $DATABASE_LOCATION = "/Applications/MAMP/htdocs/db/";//../../../db/";
    $DATABASE_NAME = "users_todo.db";
    if ( strpos($_SERVER['SERVER_NAME'], "bakombilen.se") !== false ) {
      $DATABASE_LOCATION = "/home/kalendas/db/";
      $DATABASE_NAME = "users_bb.db";
    }
    if ( strpos($_SERVER['SERVER_NAME'], "berck.se") !== false ) {
      $DATABASE_LOCATION = "/home/kalendas/db/";
    }
    $GUEST_USER  = "guest";
  
    // check if extsts.
    $init = false;
    if ( ! file_exists($DATABASE_LOCATION.$DATABASE_NAME) ) {
      $init = true;
    }
  			
    $DBNAME="sqlite:".$DATABASE_LOCATION.$DATABASE_NAME; //trips.sqll;
    $db = null;
  
    try {
      //create or open the database
      $db = new PDO($DBNAME);
    } catch(Exception $e) {
      print $e;
      die("error");
    }
    $db->setAttribute(PDO::ATTR_TIMEOUT, 10);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    if ( $init === true ) {
      $this->create_db($db);
    }
    return $db;
  }

  function get_db() {
    $DB_HOST = "localhost";
    $DB_NAME = "berck.se";
    $DB_USER = "root";
    $DB_PASS = "root";
    $dsn = "mysql:unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;dbname=" . $DB_NAME;
    
    if ( strpos($_SERVER['SERVER_NAME'], "solidaritytravels.se") !== false ) {
      $DB_HOST = "localhost";
      $DB_NAME = "solidari_soltravel";
      $DB_USER = "solidari_bp";
      $DB_PASS = "Js97iH14UB";
      $dsn = "mysql:host=" . $DB_HOST . ";dbname=" . $DB_NAME;
    }
    $GUEST_USER  = "guest";
  
    $db = null;
  
    try {
      $db = new PDO( 
        $dsn, 
        $DB_USER, 
        $DB_PASS, 
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") ); 
    } catch(Exception $e) {
      die("<pre>".$e."</pre>");
    }
    $db->setAttribute(PDO::ATTR_TIMEOUT, 10);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        
    // check if extsts.
    $init = false;
    // SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'berck.se' AND table_name = 'users';
    $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :ts AND table_name = :tn');
    $stmt->execute( array('ts' => $DB_NAME, 'tn' => 'users') );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( intval($result['COUNT(*)']) === 0 ) {
      $this->create_db($db);
    }
    // Other non-auth tables
    $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :ts AND table_name = :tn');
    $stmt->execute( array('ts' => $DB_NAME, 'tn' => 'schedule') );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( intval($result['COUNT(*)']) === 0 ) {
      $this->create_db_other($db);
    }

    return $db;
  }
  
  // -----------------------------------------------------------
  
  public function get_user( $db, $un ) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select *,(timeout - (:dt-last)) as timeleft from users where username = :username AND active=1');
    $stmt->execute( array('username' => $un, 'dt' => $dt) );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
  
  public function create_db_sqlite3( $db ) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $db->beginTransaction();
    $res = $db->exec('PRAGMA encoding = "UTF-8";'); 
    $res = $db->exec("DROP TABLE IF EXISTS users;");
    $res = $db->exec("CREATE TABLE users (username TEXT UNIQUE, password TEXT, email TEXT UNIQUE, token TEXT, role TEXT, active INTEGER, last INTEGER, userid INTEGER, timeout INTEGER DEFAULT 7200);");
    $res = $db->exec("DROP TABLE IF EXISTS tokens;");
    $res = $db->exec("CREATE TABLE tokens (token TEXT UNIQUE, used INTEGER, expire INTEGER, type TEXT);");
    $res = $db->exec("INSERT INTO tokens (token, used, expire, type) VALUES ('0', 1000000, 0, 'open');");
    $res = $db->exec("DROP TABLE IF EXISTS activation;");
    $res = $db->exec("CREATE TABLE activation (token TEXT UNIQUE, username TEXT, expire INTEGER);");
    $db->commit();
  }
  public function create_db( $db ) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $db->beginTransaction();
    $res = $db->exec("DROP TABLE IF EXISTS users;");
    $res = $db->exec("CREATE TABLE users (username VARCHAR(128), password VARCHAR(128), email VARCHAR(128), token VARCHAR(128), role VARCHAR(128), active INTEGER, last INTEGER, userid INTEGER, timeout INTEGER DEFAULT 7200, UNIQUE KEY username (username));");
    $res = $db->exec("DROP TABLE IF EXISTS tokens;");
    $res = $db->exec("CREATE TABLE tokens (token VARCHAR(128), used INTEGER, expire INTEGER, type VARCHAR(128), UNIQUE KEY token (token));");
    $res = $db->exec("INSERT INTO tokens (token, used, expire, type) VALUES ('0', 1000000, 0, 'open');");
    $res = $db->exec("DROP TABLE IF EXISTS activation;");
    $res = $db->exec("CREATE TABLE activation (token VARCHAR(128), username VARCHAR(128), expire INTEGER, UNIQUE KEY token (token));");
    $db->commit();
  }

  public function create_db_other( $db ) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    // load from file.
    $filename = 'dbnew.sql';

    if (file_exists($filename)) {
      echo "The file $filename exists";
      $sql = file_get_contents($filename);
      if (!$sql){
        die ('Error opening file');
      }
      echo 'processing file <br />';
      mysqli_multi_query($db,$sql);

    } else {
      echo "The file $filename does not exist";
    }
    
  }

  public function random_hex_string($len) {
    $string = "";
    $hex = "0123456789abcdef";
    $max = strlen($hex)-1;
    while($len-->0) { $string .= $hex[mt_rand(0, $max)]; }
    return $string;
  }
  		
  public function user_store_token($db, $un, $token) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare("UPDATE users SET token = :token WHERE username = :username;");
    $stmt->execute( array('token' => $token, 'username' => $un) );
  }
  		
  public function get_user_from_token( $db, $token ) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select *,(timeout - (:dt-last)) as timeleft from users where token = :token');
    $stmt->execute( array('token' => $token, 'dt' => $dt) );
    $result = $stmt->fetchAll();
    return $result[0];
  }
  
  public function get_user_from_activation($db, $token) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select *,(expire - :dt) as timeleft from activation where token = :token');
    $stmt->execute( array('token' => $token, 'dt' => $dt) );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $result ) {
      $dt = time();
      $stmt = $db->prepare('select *,(timeout - (:dt-last)) as timeleft from users where username = :username');
      $stmt->execute( array('username' => $result["username"], 'dt' => $dt) );
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt = $db->prepare('delete from activation where token = :token;');
      $stmt->execute( array('token' => $token) );
    }
    return $result;
  }
  
  public function user_remove_token($db, $un) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare("UPDATE users SET token = :token WHERE username = :username;");
    $stmt->execute( array('token' => "0", 'username' => $un) );
  }
  
  //update 'last' activity time
  // $u is the whole array from get_user_from_token()
  public function user_touch($db, $u) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare("UPDATE users SET last = :dt WHERE username = :username;");
    $stmt->execute( array('dt' => $dt, 'username' => $u["username"]) );
    //update cookie
    setcookie( "todo_loggedin", $u["token"], time()+$u["timeout"], "/" );
  }

  // Check if username/email combination already exists.
  public function check_user_email($db, $u, $e) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare("select * from users WHERE username = :u OR email = :e;");
    $stmt->execute( array('u' => $u, 'e' => $e) );
    $result = $stmt->fetchAll();
    return count($result);
  }
  
  // "user", or "|user||admin|"
  public function has_role($db, $r) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    
  }
  
  // -------------- TOKENS ------------------------
  
  public function get_token($db, $to) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select *,(expire - :dt) as timeleft from tokens where token = :token');
    $stmt->execute( array('token' => $to, 'dt' => $dt) );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  public function use_token($db, $t) {
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare("UPDATE tokens SET used = used - 1 WHERE token = :t;");
    $stmt->execute( array('t' => $t) );  
  }
  
  public function is_valid($db, $to, $ty) {
    $info = $this->get_token($db, $to);
    // wrong type is invalid.
    if ( $info['type'] != $ty ) {
      return false;
    }
    // Both params are 0, then we are invalid.
    if ( ($info['expire'] <= 0) && ($info['used'] == 0) ) {
      return false;
    }
    // If used > 0, or expire > time(), we are ok.
    if ( $info['used'] <= 0 ) {
      if ( $info['expire'] > time() ) {
        return true; //used<=0, but time left
      } else {
        return false; //used<=0, no time left
      }
    }
    return true;//used > 0, time irrelevant
  }
  
  public function create_token_use($db, $ty, $uses) {
    $token = $this->random_hex_string(8);
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    if ( $uses <= 1 ) {
      $uses = 1;
    }
    $stmt = $db->prepare("INSERT INTO tokens (token, used, expire, type) VALUES (:to, :u, 0, :ty)");
    $stmt->execute( array('ty' => $ty, 'to' => $token, 'u' => $uses) );
    return $token;
  }

  public function create_token_expire($db, $ty, $ex) {
    $token = $this->random_hex_string(8);
    if ( $db == NULL ) {
      $db = $this->get_db();
    }
    if ( $ex <= 60 ) {
      $ex = 60;
    }
    $dt = time() + $ex;
    $stmt = $db->prepare("INSERT INTO tokens (token, used, expire, type) VALUES (:to, 0, :ex, :ty)");
    $stmt->execute( array('ty' => $ty, 'to' => $token, 'ex' => $dt) );
    return $token;
  }

  // ---------------- DB defs for the web pages

  public function get_schedule_status_def() {
    $fields = array(
      "00" => "** V&auml;lj **",
      "FF" => "F&ouml;rfr&aring;gan",
      "BO" => "Bokning, under behandling",
      "BK" => "Bokning, klar",
      "AV" => "Avbokning"
    );
    return $fields;
  }
    
  public function get_schedule_invoicestatus_def() {
    $fields = array(
      0 => "Obetalt",
      1 => "Betalt"
    );
    return $fields;
  }

  public function get_schedule_fixflight_def() {
    $fields = array(
      0 => "** V&auml;lj **",
      1 => "Flygplats",
      2 => "Bussh&aring;llplats",
      3 => "T&aring;gstation",
      4 => "F&auml;rjeterminal"
    );
    return $fields;
  }

  public function get_schedule_fixtransport_def() {
    $fields = array(
      0 => "** V&auml;lj **",
      1 => "Avvakta besked fr&aring;n kunden",
      2 => "Meddela resedetaljer till samarbetspartner",
      3 => "Meddelat resedetaljer till samarbetspartner",
      4 => "Klart"
    );
    return $fields;
  }

  public function get_schedule_meddelaresa_def() {
    $fields = array(
      0 => "Samarbetspartner",
      1 => "Hotell",
      2 => "Taxibolag"
    );
    return $fields;
  }

  // PJB: defi for ints and defs for strings?
  
  // def_to_menu( fields, "status_menu", 1, "status" );
  // how to add onchange &c? add events in javascript on nybokning.php?
  public function def_to_menu( $def, $menu_id, $sel_idx, $db_field ) {
    $menu = "<select tabindex=\"1\" name=\"$menu_id\" id=\"$menu_id\">";
    //print "<pre>$menu_id";print_r($def);print "</pre>";
    foreach ( $def as $val => $label ) {
      $selected="";
      if ( $sel_idx == $val ) {
        $selected="selected=\"selected\"";
      }
      $menu .= "<option $db_field=\"$val\" $selected>$label</option>\n";
    }
    $menu .= "</select>";
    return $menu;
  }

  // ---------------- DB functions for the web pages
  
  public function get_get_value($name) {
    if (array_key_exists($name, $_GET)) {
      if ( ($_GET[$name]) || (intval($_GET[$name] == 0)) ) {
        return $_GET[$name];
      }
    }
    return "";
  }

  public function get_post_value($name) {
    if (array_key_exists($name, $_POST)) {
      if ( ($_POST[$name]) || (intval($_POST[$name] == 0)) ) {
        return $_POST[$name];
      }
    }
    return "";
  }

  public function get_all_users( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select *,(timeout - (:dt-last)) as timeleft from users');
    $stmt->execute( array('dt' => $dt) );
    $result = $stmt->fetchAll();//fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  public function get_full_schedule( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select * from schedule where status != "00"');
    $stmt->execute( array('dt' => $dt) );
    $result = $stmt->fetchAll();
    return $result;
  }

  public function get_future_schedule( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select * from schedule where startdate > CURDATE()');
    $stmt->execute( array('dt' => $dt) );
    $result = $stmt->fetchAll();
    return $result;
  }

  public function get_past_schedule( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $dt = time();
    $stmt = $db->prepare('select * from schedule where startdate <= CURDATE()');
    $stmt->execute( array('dt' => $dt) );
    $result = $stmt->fetchAll();
    return $result;
  } 

  //select * from schedule where nr > 3 order by nr ASC limit 1 ;
  // Get the previous booking from this one.
  public function get_previous_id( $id, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select id from schedule where id <:id order by id desc limit 1');
    $stmt->execute(array('id' => $id));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $result['id'] ) {
      return $result['id'];
    }
    return -1;
  }

  public function get_next_id( $id, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select id from schedule where id > :id order by id asc limit 1');
    $stmt->execute(array('id' => $id));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( $result['id'] ) {
      return $result['id'];
    }
    return -1;
  }

  public function get_next_schedule_id( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('SHOW TABLE STATUS LIKE "schedule"');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['Auto_increment'];
  }

  public function get_last_schedule_nr( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('SELECT nr FROM schedule ORDER BY nr DESC LIMIT 1');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['nr'];
  }

  // Uses the database field id, not nr.
  public function get_schedule( $id, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from schedule where id = :id');
    $stmt->execute( array('id' => $id) );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  // Uses the database field nr.
  public function get_schedule_by_nr( $nr, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from schedule where nr = :nr');
    $stmt->execute( array('nr' => $nr) );
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  // Return id, nr? year = bookingdate?
  public function get_schedule_by_year( $yr, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $y0 = $yr."-"."01-01";
    $y1 = $yr."-"."31-12";
    $stmt = $db->prepare('select * from schedule where bookingdate >=:y0 and bookingdate <=:y1');
    $stmt->execute( array('y0' => $y0, 'y1' => $y1) );
    $result = $stmt->fetchAll();
    return $result;
  }

  // Select around nr, +/- 10 or so
  public function get_schedule_around_nr( $nr, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    //$stmt = $db->prepare('select id,nr from schedule where nr >:nr limit 2')
    $stmt = $db->prepare('select id,nr from schedule where nr <:nr order by id desc limit 2');
    $stmt->execute( array('nr' => $nr) );
    $result = $stmt->fetchAll();
    $stmt = $db->prepare('select id,nr from schedule where nr >:nr order by id asc limit 2');
    $stmt->execute( array('nr' => $nr) );
    $result = array_merge($result, $stmt->fetchAll());
    return $result;
  }
  
  // New empty entry in schedule. Used in nybokning.php.
  public function new_schedule( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $today = date("Ymd");
    $last_nr = $this->get_last_schedule_nr( $db ); // the last real number
    $new_nr = $last_nr + 1;
    $stmt = $db->prepare("INSERT INTO schedule (id, nr, bookingdate) VALUES (:id, :nr, :dt)");
    $stmt->execute( array('id' => null, 'nr' => $new_nr, 'dt' => $today) );
    $id = $db->lastInsertId();
    $result = $this->get_schedule( $id, $db );
    return $result;
  }
  
  //SELECT * FROM stock INNER JOIN trailers ON stock.trailerid=trailers.trailerid"
  public function get_paymethods( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from paymethods;');
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  public function get_paymethod_warnings( $pid, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from paymethods INNER JOIN warnings on paymethods.paymethodid=warnings.paymethodsid where paymethodid=:pid');
    $stmt->execute( array('pid' => $pid) );
    $result = $stmt->fetchAll();
    return $result;
  }
  
  // Projects & countries
  
  public function get_all_projects( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from projects;');
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  
  public function get_countryids( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select landid from projects group by landid;');
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  // Takes array from get_all_projects(). Structure
  // to make the menu+submenus of.
  public function projects_to_menu( $all ) {
    $countries = Array();
    foreach( $all as $p ) { // p is a project
      $countries[$p['landid']] = Array();
    }
    $projects = Array();
    $tmp = Array();
    $curc = 1;
    foreach( $all as $p ) { // p is a project
      $countries[$p['landid']][$p['projectid']] = $p;
    }
    return $countries;
  }

  // Make a checkbox
  public function make_checkbox($val, $id, $lbl) {
    $c = "";
    if ( intVal($val) !== 0 ) {
      $c = "checked=\"checked\"";
    }
    //$cb = "<label><input type=\"checkbox\" id=\"".$id."\" name=\"".$id."\" ".$c." />".$lbl."</label>";
//    $cb = "<label for=\"".$id."\">".$lbl."</label><input type=\"checkbox\" id=\"".$id."\" name=\"".$id."\" ".$c." />";
//	$cb = "<label class=\"labelfloat\" for=\"".$id."\" id=\"".$id."\"><input tabindex=99 class=\"checkbox\" type=\"checkbox\" id=\"".$id."\" name=\"".$id."\" ".$c." />".$lbl."</label>";
	$cb = "<label class=\"labelfloat\" for=\"".$id."\" id=\"".$id."\">".$lbl."<input tabindex=99 class=\"checkbox\" type=\"checkbox\" id=\"".$id."\" name=\"".$id."\" ".$c." /></label>";
    return $cb;
  }

  // Make an HTML menu
  public function get_country_menu( $sel = "XXX", $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select landid,land from projects group by landid;');
    $stmt->execute();
    $result = $stmt->fetchAll();
    $m = "<select name=\"projectcountry_menu\" tabindex=3 id=\"projectcountry_menu\">";
    if ( $sel == "XXX" ) {
      $m .= "<option projectcountry=\"XXX\" selected=\"selected\">** V&auml;lj land **</option>";
    } else {
      $m .= "<option projectcountry=\"XXX\">** V&auml;lj land **</option>";
    }
    foreach( $result as $c ) {
      if ( $sel == $c['landid'] ) {
        $m .= "<option projectcountry=\"".$c['landid']."\" selected=\"selected\">".$c['land']."</option>";
      } else {
        $m .= "<option projectcountry=\"".$c['landid']."\">".$c['land']."</option>";
      }
    }
    $m .= "</select>";
    return $m;
  }

  // Make an HTML menu
  public function get_project_menu( $cid, $sel = "XXX", $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from projects where landid=:cid');
    $stmt->execute( array('cid' => $cid) );
    $result = $stmt->fetchAll();
    $m = "<select name=\"project_menu\" tabindex=4 id=\"project_menu\">";
    if ( $sel == "XXX" ) {
      $m .= "<option paymethod=\"0\" bankcost=\"0\" project=\"XXX\" selected=\"selected\">** V&auml;lj project **</option>";
    } else {
      $m .= "<option paymethod=\"0\" bankcost=\"0\" project=\"XXX\">** V&auml;lj project **</option>";
    }
    foreach( $result as $p ) {
      if ( $sel == $p['projectid'] ) {
        $m .= "<option paymethod=\"".$p['paymethod']."\" bankcosts=\"".$p['bankcosts']."\" project=\"".$p['projectid']."\" projectcountry=\"".$p['landid']."\" valuta=\"".$p['valuta']."\" info1rec=\"".$p['info1rec']."\" selected=\"selected\">".$p['project']."</option>";
      } else {
        $m .= "<option paymethod=\"".$p['paymethod']."\" bankcosts=\"".$p['bankcosts']."\" paymethod=\"0\" project=\"".$p['projectid']."\" projectcountry=\"".$p['landid']."\" valuta=\"".$p['valuta']."\" info1rec=\"".$p['info1rec']."\" info2rec=\"".$p['info2rec']."\" ffd1rec=\"".$p['ffd1rec']."\" ffd2rec=\"".$p['ffd2rec']."\" ffd3rec=\"".$p['ffd3rec']."\">".$p['project']."</option>";
      }
    }
    $m .= "</select>";
    return $m;
  }

  // For internet exploder
  public function get_countryproject_menu( $cid="XXX", $pid="XXX", $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from projects order by landid');
    $stmt->execute();
    $result = $stmt->fetchAll();
    $m = "<select name=\"countryproject_menu\" tabindex=4 id=\"countryproject_menu\">";
    if ( $pid == "XXX" ) {
      $m .= "<option paymethod=\"0\" country=\"XXX\" bankcost=\"0\" project=\"XXX\" selected=\"selected\">** V&auml;lj project **</option>";
    } else {
      $m .= "<option paymethod=\"0\" country=\"".$cid."\" bankcost=\"0\" project=\"".$pid."\">** V&auml;lj project **</option>";
    }
    foreach( $result as $p ) {
      if ( $pid == $p['projectid'] ) {
        $m .= "<option paymethod=\"".$p['paymethod']."\" project=\"".$p['projectid']."\" bankcost=\"".$p['bankcost']."\" projectcountry=\"".$p['landid']."\" valuta=\"".$p['valuta']."\" info1rec=\"".$p['info1rec']."\" selected=\"selected\">".$p['land'].":".$p['project']."</option>";
      } else {
        $m .= "<option paymethod=\"".$p['paymethod']."\" paymethod=\"0\" project=\"".$p['projectid']."\" bankcost=\"".$p['bankcost']."\" projectcountry=\"".$p['landid']."\" info1rec=\"".$p['info1rec']."\" info2rec=\"".$p['info2rec']."\" ffd1rec=\"".$p['ffd1rec']."\" ffd2rec=\"".$p['ffd2rec']."\" ffd3rec=\"".$p['ffd3rec']."\" valuta=\"".$p['valuta']."\">".$p['land'].":".$p['project']."</option>";
      }
    }
    $m .= "</select>";
    return $m;
  }

  public function get_one_project($cid, $pid, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from projects where landid=:cid and projectid=:pid');
    $stmt->execute(array('cid' => $cid, 'pid' => $pid));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
  }

  // AKTUELLT, warnings &c.
  
  public function get_rules( $pm, $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $stmt = $db->prepare('select * from rules where paymethod=:pm');
    $stmt->execute( array('pm' => $pm) );
    $result = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $result[ $row['id'] ] = $row;
    }
    return $result;
  }

  public function apply_rules( $bd ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    // get paymethod.
    $paymethod = $bd['paymethod'];
    // get the rules
    $rules = $this->get_rules($paymethod, $db);
    $result = array();
    $q = "update schedule set ";
  
    foreach( $rules as $rule ) {
      $result[$rule['id']]['id'] = $rule['id'];
      $result[$rule['id']]['res'] = "NF";
      $result[$rule['id']]['df'] = "NF";
      $result[$rule['id']]['cf'] = "NF";
      $result[$rule['id']]['dr'] = "NF";
      $result[$rule['id']]['r'] = "NF";
      $result[$rule['id']]['mf'] = "NF"; 
      $result[$rule['id']]['mr'] = "NF";
      
      $df = $rule['datefield']; // ffd1
      $cf = $rule['calcdate']; // startdate
      $dr = $rule['daterule'];  // +14 days
      
      //$dr = str_replace( array("MSD", "msd"), array($msd, $msd),$dr );
      
      if ( ($df != "" ) && ($cf != "") && ($dr != "") ) {
        $cf_d = date_create($bd[$cf]); //startdate as date
        // calc MSD, last day in month of  calcdate, replace in daterul
        $msd = date_format($cf_d,"Y-m-t");
        if ( strtolower($dr) == "msd" ) {
          $res = $msd; //date_create($msd); //NB no calculations with this
        } else {
          $res = date_add( $cf_d, date_interval_create_from_date_string( $dr ));// calculate it
          $res = date_format($res, "Y-m-d");
        }
        $result[$rule['id']]['res'] = $res;
        $result[$rule['id']]['df'] = $df; // Update DB
        $result[$rule['id']]['cf'] = $cf;
        $result[$rule['id']]['dr'] = $dr;
        $q .= "".$df."='".$res."',"; 
      }
      
      $mr = $rule['moneyrule'];  // totamount * 0.5
      $mf = $rule['moneyfield'];
      if ( ($mr != "") && ($mf != "") ) {
        $a = "\$r=".str_replace( array("totamount","AMT2"), array($bd['totamount'],$bd['amount2']),$mr).";";
        $er = error_reporting();
        error_reporting(0);
        $r=0;
        try { 
          eval($a);
        } catch(Exception $e) {
          $r = 0;
        }
        error_reporting($er);
        $result[$rule['id']]['r'] = $this->fmt_money($r);
        $result[$rule['id']]['mf'] = $mf; // Update DB 
        $result[$rule['id']]['mr'] = $a;
        $q .= "".$mf."='".$r."',";
      }
    } //foreach
    $q = substr($q,0,-1);
    $q .= " where id=".$bd['id'];
    $stmt = $db->prepare($q);
    $stmt->execute();
    $result['q']=$q;
    return $result; // maybe return full $bd, easier in nybokning...
  }

  // ---------- util
  
  // These can only be displayed! Input is a bcmath
  // number string from the DB.
  public function fmt_money($s) {
    if ( $s == "" ) {
      return $s;
    }
    $s = $this->money_to_float($s);
    $sign = "";
    if ( bccomp(0,$s) == 1 ) {
      $sign = "-";
      $s = substr($s, 1);
    }
    // round
    bcscale(2);
    $s = bcmul($s,100)+0.5;
    $s = bcdiv($s,100,2);
    $lr = explode( ".", $s );
    if ( count($lr) === 2 ) {
      $l = $lr[0];
      $r = $lr[1];
      return $sign.$this->_fmt_money($l,$r);
    }
    return $sign.$this->_fmt_money($s,"00");
  }

  // no negatives!
  public function _fmt_money($s,$r) {
    $tsep = " "; // "&thinsp;"
    $la = str_split($s);
    $l = "";
    $i = 3; // array(2,2,4,3) and pop for hindi etc
    foreach(array_reverse($la) as $d ) {
      if ( $i == 0 ) {
        $l = $d.$tsep.$l;
        $i = 3;
      } else {
        $l = $d.$l;
      }
      --$i;
    }
    return $neg.$l.",".$r;
  }

  // 1.000,50 => 1000.50
  public function money_to_float($m) {
    $tsep = " "; // " "
    // remove "."
    $m = str_replace($tsep, '', $m);
    //change "," to "."
    $m = str_replace(',', '.', $m);
    return $m;
  }
  
  // Calendar
  
  public function get_schedule_cal( $db = null ) {
    if ( $db == null ) {
      $db = $this->get_db();
    }
    $json = array();
    $stmt = $db->prepare('select * from schedule');
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $t = "Bnr ".$row['nr'].": ".$row['firstname']." ".$row['lastname']." ".$row['projectcountry']."/".$row['project'];
      $s = $row['startdate'];
      $e = $row['enddate'];
      $u = "nybokning.php?nr=".$row['nr'];
      $buildjson = array('title' => "$t", 'start' => "$s", 'end' => "$e", 'url' => "$u", 'allDay' => true);
      // Adds each array into the container array
      array_push($json, $buildjson);
    }
    return json_encode($json);
  }
  
}
?>

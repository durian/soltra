<?
require_once("db/auth/user.php");
$USER = new User();

$r = $USER->get_schedule_cal();
echo $r;
?>

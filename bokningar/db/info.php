<?php
require_once("auth/user.php");
$USER = new User();

print("<pre>");
print_r($USER->get_schedule_around_nr(40));

echo $USER->fmt_money("100000")."<br />";
echo $USER->fmt_money("1000")."<br />";
echo $USER->fmt_money("-1000.25")."<br />";
echo $USER->fmt_money("1000.25")."<br />";
echo $USER->fmt_money("10.123")."<br />";
echo $USER->fmt_money("11.999")."<br />";
echo $USER->fmt_money("10.129")."<br />";
echo $USER->fmt_money("-10.129")."<br />";
echo $USER->fmt_money("1000000000")."<br />";


$date = new DateTime('2000-01-01');
$date->add(new DateInterval('P10D'));
echo $date->format('Y-m-d') . "\n";

echo "<br/>";

$date = date_create('2000-01-01');
date_add($date, date_interval_create_from_date_string('10 days'));
echo date_format($date, 'Y-m-d');

echo "<br/>";

$date = new DateTime('2000-01-01');
$date->add(new DateInterval('PT10H30S'));
echo $date->format('Y-m-d H:i:s') . "\n";

echo "<br/>";

$date = new DateTime('2000-01-01');
$date->add(new DateInterval('P7Y5M4DT4H3M2S'));
echo $date->format('Y-m-d H:i:s') . "\n";

echo "<br/>";

$datetime1 = new DateTime('2009-10-11');
$datetime2 = new DateTime('2009-10-13');
$interval = $datetime1->diff($datetime2);
echo $interval->format('%R%a days');

echo "<br/>";

$today = new DateTime(date('2011-11-09'));
$appt  = new DateTime(date('2011-12-09'));
echo $days_until_appt = $appt->diff($today)->days;

echo "<br/>";

$now   = new DateTime;
$clone = clone $now;    
$clone->modify( '-1 day' );
echo $now->format( 'd-m-Y' ), "\n", $clone->format( 'd-m-Y' );
  
echo "<br/>";

$bdt = new DateTime("2013-03-31");
echo $bdt->format( 'd-m-Y' ),"<br/>";
$bdt->modify("+1 days");
echo $bdt->format( 'd-m-Y' ),"<br/>";

echo "<br/>";
echo bcmul("11111111111111111111","11111111111111111111");// must be strings!
echo "<br/>";
echo "<br/>";

$a = '100.40';
$b = '5';


echo bcadd($a, $b),"<br/>";     // 6
echo bcadd($a, $b, 4),"<br/>";   // 6.2340
echo bcdiv($a, $b, 4),"<br/>";   // 6.2340
echo bcdiv($a, "6", 2),"<br/>";   // 6.2340
echo bcdiv("190000000", "3", 2),"<br/>";   // 6.2340
echo bcdiv("19009643500", "3", 3),"<br/>";   // 6.2340
echo bcdiv("1900038467550", "1", 4),"<br/>";   // 6.2340
//$mul = gmp_mul("12345678", "2000");
//echo gmp_strval($mul) . "\n";

//phpinfo();
?>

<?php 
add_required_class( 'Calendar.Class.php', MODEL );

$calendar = new Calendar();
$calendar->buildCalendar();
echo $calendar->getOutput();
echo "<hr/>";
$calendar = new Calendar("2010-3-21");
$calendar->buildCalendar();
echo $calendar->getOutput();
echo "<hr/>";
$calendar = new Calendar("12-2-2009", "d-m-Y");
$calendar->buildCalendar();
echo $calendar->getOutput();
echo "<hr/>";
//$calendar = new Calendar(DateTime::createFromFormat("d-m-Y","29-05-1981"));
$calendar = new Calendar(date_create("29-05-1981"));
$calendar->buildCalendar();
echo $calendar->getOutput();
//DateTime::createFromFormat($this->format,"{$this->year}-{$this->mon}-01");
?>
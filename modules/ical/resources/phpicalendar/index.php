<?php

if (!isset($ALL_CALENDARS_COMBINED))  $ALL_CALENDARS_COMBINED = 'all_calendars_combined971';
include "./config.inc.php";
if (isset($_COOKIE['phpicalendar'])) {
	$phpicalendar 		= unserialize(stripslashes($_COOKIE['phpicalendar']));
	$default_view 		= $phpicalendar['cookie_view'];
}
if ($printview_default == 'yes') {
	$printview = $default_view;
	$default_view = "print.php";
} else {
	$check = array ('day', 'week', 'month');
	if (in_array($default_view, $check)) {
		$default_view = $default_view . '.php';
	} else {
		die('here');
	}
}
if(isset($_GET['cpath'])){
	$default_view .= '?cpath='.$_GET['cpath'];
}
header("Location: $default_view");

?>

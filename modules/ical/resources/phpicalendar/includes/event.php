<?php 
define('BASE', '../');
$getdate = $_POST['date'];
include_once(BASE.'functions/init.inc.php'); 
#include_once(BASE.'functions/ical_parser.php'); 
require_once(BASE.'functions/template.php');

function decode_popup ($item) {
	$item = stripslashes(rawurldecode($item));
	$item = str_replace('\\','',$item);
	return $item;
}



#$event 			= $master_array[$_POST['date']][$_POST['time']][decode_popup($_POST['uid'])];
$event 			= unserialize(stripslashes($_POST['event_data']));
$organizer 		= unserialize($event['organizer']);
$attendee 		= unserialize($event['attendee']);

// Format event time
// All day
if ($_POST['time'] == -1) {
	$event_times = $lang['l_all_day'];
} else {
	$event_times = date($timeFormat, $event['start_unixtime']) . ' - ' .  date($timeFormat, $event['end_unixtime']); 
}

$event['description'] 	= stripslashes(urldecode($event['description']));
$event['event_text'] 	= stripslashes(urldecode($event['event_text']));

if ($event['description']) $event['description'] = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",'<a target="_new" href="\0">\0</a>',$event['description']);

if (is_array($organizer)) {
	$i=0;
	$display .= $organizer_lang . ' - ';
	foreach ($organizer as $val) {	
		$organizers .= $organizer[$i]["name"] . ', ';
		$i++;
	}
	$organizer = substr($organizers,0,-2);
}
if (is_array($attendee)) {
	$i=0;
	$display .= $attendee_lang . ' - ';
	foreach ($attendee as $val) {	
		$attendees .= $attendee[$i]["name"] . ', ';
		$i++;
	}
	$attendee = substr($attendees,0,-2);
}

if ($event['location']) {
	if ($event['url'] != '') $event['location'] = '<a href="'.$event['url'].'" target="_blank">'.stripslashes($event['location']).'</a>';
}else{
	$event['location'] = stripslashes($event['location']);
}

if (!$event['location'] && $event['url']) {
	$event['location'] = '<a href="'.$event['url'].'" target="_blank">'.$event['url'].'</a>';
	$lang['l_location'] = 'URL';
}

if (sizeof($attendee) == 0) $attendee = '';
if (sizeof($organizer) == 0) $organizer = '';

switch ($event['status']){
	case 'CONFIRMED':
		$event['status'] =	$lang['l_status_confirmed'] ; 
		break;
	case 'CANCELLED':
		$event['status'] =	$lang['l_status_cancelled'] ; 
		break;
	case 'TENTATIVE':
		$event['status'] =	$lang['l_status_tentative'] ; 
		break;
}

$page = new Page(BASE.'templates/'.$template.'/event.tpl');

$page->replace_tags(array(
	'charset'			=> $charset,
	'cal' 				=> $event['calname'],
	'event_text' 		=> $event['event_text'],
	'event_times' 		=> $event_times,
	'description' 		=> $event['description'],
	'organizer' 		=> $organizer,
	'attendee'	 		=> $attendee,
	'status'	 		=> $event['status'],
	'location' 			=> stripslashes($event['location']),
	'cal_title_full'	=> $event['calname'].' '.$lang['l_calendar'],
	'template'			=> $template,
	'l_organizer'		=> $lang['l_organizer'],
	'l_attendee'		=> $lang['l_attendee'],
	'l_status'			=> $lang['l_status'],
	'l_location'		=> $lang['l_location']
		
	));

$page->output();

?>

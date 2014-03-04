<?php 

define('BASE', '../');
include_once(BASE.'functions/init.inc.php');
include_once(BASE.'functions/date_functions.php');
require_once(BASE.'functions/template.php');

$vtodo_array = unserialize(base64_decode($_GET['vtodo_array']));

// Set the variables from the array
$vtodo_text		= (isset($vtodo_array['vtodo_text'])) ? $vtodo_array['vtodo_text'] : ('');
$description	= (isset($vtodo_array['description'])) ? $vtodo_array['description'] : ('');
$completed_date	= (isset($vtodo_array['completed_date'])) ? localizeDate ($dateFormat_day, strtotime($vtodo_array['completed_date'])) : ('');
$status			= (isset($vtodo_array['status'])) ? $vtodo_array['status'] : ('');
$calendar_name  = (isset($vtodo_array['cal'])) ? $vtodo_array['cal'] : ('');
$start_date 	= (isset($vtodo_array['start_date'])) ? localizeDate ($dateFormat_day, strtotime($vtodo_array['start_date'])) : ('');
$due_date 		= (isset($vtodo_array['due_date'])) ? localizeDate ($dateFormat_day, strtotime($vtodo_array['due_date'])) : ('');
$priority 		= (isset($vtodo_array['priority'])) ? $vtodo_array['priority'] : ('');

$cal_title_full = $calendar_name.' '.$lang['l_calendar'];
$description	= ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", '<a target="_new" href="\0">\0</a>', $description);
$vtodo_text		= ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]",'<a target="_new" href="\0">\0</a>',$vtodo_text);


if ((!isset($status) || $status == "COMPLETED") && isset($completed_date)) {
	$status = $lang['l_completed_date'] . ' ' . $completed_date;
} elseif ($status == "COMPLETED") {
	$status = $completed_lang;
} else {
	$status = $unfinished_lang;
}

if ($priority >= 1 && $priority <= 4) {
	$priority = $lang['l_priority_high'];
} else if ($priority == 5) {
	$priority = $lang['l_priority_medium'];
} else if ($priority >= 6 && $priority <= 9) {
	$priority = $lang['l_priority_low'];
} else {
	$priority = $lang['l_priority_none'];
}

$page = new Page(BASE.'templates/'.$template.'/todo.tpl');

$page->replace_tags(array(
	'charset'			=> $charset,
	'cal' 				=> $cal_title_full,
	'vtodo_text' 		=> $vtodo_text,
	'description' 		=> $description,
	'priority'	 		=> $priority,
	'start_date' 		=> $start_date,
	'status'	 		=> $status,
	'due_date' 			=> $due_date,
	'cal_title_full'	=> $cal_title_full,
	'template'			=> $template,
	'l_created'			=> $lang['l_created'],
	'l_priority'		=> $lang['l_priority'],
	'l_status'			=> $lang['l_status'],
	'l_due'				=> $lang['l_due']
		
	));

$page->output();

?>
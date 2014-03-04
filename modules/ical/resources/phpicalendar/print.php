<?php
	
define('BASE', './');
require_once(BASE.'functions/date_functions.php');
require_once(BASE.'functions/init.inc.php');
$current_view 		='print';
$start_week_time 	= strtotime(dateOfWeek($getdate, $week_start_day));
$end_week_time 		= $start_week_time + (6 * 25 * 60 * 60);
$parse_month 		= date ("Ym", strtotime($getdate));
$events_week 		= 0;
$unix_time 			= strtotime($getdate);
$printview = 'month';
if (isset($_GET['printview']))	$printview = $_GET['printview'];

if ($printview == 'day') {
	$display_date 	= localizeDate ($dateFormat_day, strtotime($getdate));
	$next 			= date("Ymd", strtotime("+1 day", $unix_time));
	$prev 			= date("Ymd", strtotime("-1 day", $unix_time));
	$week_start		= '';
	$week_end		= '';
} elseif ($printview == 'week') {
	$start_week 	= localizeDate($dateFormat_week, $start_week_time);
	$end_week 		= localizeDate($dateFormat_week, $end_week_time);
	$display_date 	= "$start_week - $end_week";
	$week_start 	= date("Ymd", $start_week_time);
	$week_end 		= date("Ymd", $end_week_time);
	$next 			= date("Ymd", strtotime("+1 week", $unix_time));
	$prev 			= date("Ymd", strtotime("-1 week", $unix_time));
} elseif ($printview == 'month') {
	$display_date 	= localizeDate ($dateFormat_month, strtotime($getdate));
	$next 			= date("Ymd", strtotime("+1 month", $unix_time));
	$prev 			= date("Ymd", strtotime("-1 month", $unix_time));
	$week_start		= '';
	$week_end		= '';
} elseif ($printview == 'year') {
	$display_date 	= localizeDate ($dateFormat_year, strtotime($getdate));
	$next 			= date("Ymd", strtotime("+1 year", $unix_time));
	$prev 			= date("Ymd", strtotime("-1 year", $unix_time));
	$week_start		= '';
	$week_end		= '';
}
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/list_functions.php');
require_once(BASE.'functions/template.php');
header("Content-Type: text/html; charset=$charset");


$page = new Page(BASE.'templates/'.$template.'/print.tpl');

$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
	'sidebar'			=> BASE.'templates/'.$template.'/sidebar.tpl'
	));

$page->replace_tags(array(
	'version'			=> $phpicalendar_version,
	'event_js'			=> '',
	'charset'			=> $charset,
	'default_path'		=> '',
	'template'			=> $template,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'calendar_name'		=> $cal_displayname,
	'current_view'		=> $current_view,
        'printview'          => $printview,
	'display_date'		=> $display_date,
	'sidebar_date'		=> $sidebar_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_available' 	=> '',
	'rss_valid' 		=> '',
	'show_search' 		=> '',
	'next_day' 			=> $next_day,
	'prev_day'	 		=> $prev_day,
	'show_goto' 		=> '',
	'is_logged_in' 		=> '',
	'list_icals' 		=> $list_icals,
	'list_years' 		=> $list_years,
	'list_months' 		=> $list_months,
	'list_weeks' 		=> $list_weeks,
	'list_jumps' 		=> $list_jumps,
	'legend'	 		=> $list_calcolors,
	'style_select' 		=> $style_select,
	'l_time'			=> $lang['l_time'],
	'l_summary'			=> $lang['l_summary'],
	'l_description'		=> $lang['l_description'],
	'l_calendar'		=> $lang['l_calendar'],
	'l_day'				=> $lang['l_day'],
	'l_week'			=> $lang['l_week'],
	'l_month'			=> $lang['l_month'],
	'l_year'			=> $lang['l_year'],
	'l_location'			=> $lang['l_location'],	
	'l_subscribe'		=> $lang['l_subscribe'],
	'l_download'		=> $lang['l_download'],
	'l_no_results'		=> $lang['l_no_results'],
	'l_powered_by'		=> $lang['l_powered_by'],
	'l_this_site_is'	=> $lang['l_this_site_is']				
	));
	
$page->draw_print($page);

$page->output();

?>

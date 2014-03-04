<?php

define('BASE', './');
$current_view = 'year';
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/list_functions.php');
require_once(BASE.'functions/template.php');
header("Content-Type: text/html; charset=$charset");

ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);
$this_day 	= $day_array2[3]; 
$this_month = $day_array2[2];
$this_year 	= $day_array2[1];
$next_year 	= strtotime ("+1 year", strtotime($getdate));
$next_year 	= date ("Ymd", $next_year);
$prev_year 	= strtotime ("-1 year", strtotime($getdate));
$prev_year 	= date ("Ymd", $prev_year);

$sidebar_date 		= localizeDate($dateFormat_day, strtotime($getdate));

// For the side months
ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);
$this_day 	= $day_array2[3]; 
$this_month = $day_array2[2];
$this_year 	= $day_array2[1];

// select for calendars
$list_icals 	= display_ical_list(availableCalendars($username, $password, $ALL_CALENDARS_COMBINED));
$list_years 	= list_years();
$list_months 	= list_months();
$list_weeks 	= list_weeks();
$list_jumps 	= list_jumps();
$list_calcolors = '<img src="templates/'.$template.'/images/allday_dot.gif" alt=" " width="11" height="10" border="0" />'.$lang['l_all_day'].'<br>
<img src="templates/'.$template.'/images/event_dot.gif" alt=" " width="11" height="10" border="0" />'.$lang['l_event']."<br>"; 
;
$list_icals_pick = display_ical_list(availableCalendars($username, $password, $ALL_CALENDARS_COMBINED), TRUE);

// login/logout
$is_logged_in = ($username != '' && !$invalid_login) ? true : false;
$show_user_login = (!$is_logged_in && $allow_login == 'yes');
$login_querys = login_querys();
$logout_querys = logout_querys();



$page = new Page(BASE.'templates/'.$template.'/year.tpl');

$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
    'sidebar'           => BASE.'templates/'.$template.'/sidebar_year.tpl',
    'search_box'        => BASE.'templates/'.$template.'/search_box.tpl'
	));

$page->replace_tags(array(
	'version'			=> $phpicalendar_version,
	'event_js'			=> '',
	'current_view'		=> $current_view,
	'template'			=> $template,
	'charset'			=> $charset,
	'default_path'		=> '',
	'cal'				=> $cal,
	'getcpath'			=> "&cpath=$cpath",
    'cpath'             => $cpath,
	'getdate'			=> $getdate,
	'calendar_name'		=> $cal_displayname,
	'display_date'		=> $this_year,
	'sidebar_date'		=> $sidebar_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_available' 	=> '',
	'rss_valid' 		=> '',
	'todo_available' 	=> '',
	'event_js' 			=> '',
	'this_year'			=> $this_year,
	'next_day' 			=> $next_day,
	'next_week' 		=> $next_week,
	'prev_day'	 		=> $prev_day,
	'prev_week'	 		=> $prev_week,
	'next_year'			=> $next_year,
	'prev_year'			=> $prev_year,
	'show_goto' 		=> '',
	'show_user_login'	=> $show_user_login,
	'invalid_login'		=> $invalid_login,
	'login_querys'		=> $login_querys,
	'is_logged_in' 		=> $is_logged_in,
	'username'			=> $username,
	'logout_querys'		=> $logout_querys,
	'list_icals' 		=> $list_icals,
	'list_icals_pick' 		=> $list_icals_pick,
	'list_years' 		=> $list_years,
	'list_months' 		=> $list_months,
	'list_weeks' 		=> $list_weeks,
	'list_jumps' 		=> $list_jumps,
	'legend'	 		=> $list_calcolors,
	'style_select' 		=> $style_select,
	'l_goprint'			=> $lang['l_goprint'],
	'l_preferences'		=> $lang['l_preferences'],
	'l_calendar'		=> $lang['l_calendar'],
	'l_legend'			=> $lang['l_legend'],
	'l_tomorrows'		=> $lang['l_tomorrows'],
	'l_jump'			=> $lang['l_jump'],
	'l_todo'			=> $lang['l_todo'],
	'l_day'				=> $lang['l_day'],
	'l_week'			=> $lang['l_week'],
	'l_month'			=> $lang['l_month'],
	'l_year'			=> $lang['l_year'],
	'l_search'				=> $lang['l_search'],
	'l_subscribe'		=> $lang['l_subscribe'],
	'l_download'		=> $lang['l_download'],
	'l_pick_multiple'	=> $lang['l_pick_multiple'],
	'l_powered_by'		=> $lang['l_powered_by'],
	'l_this_site_is'	=> $lang['l_this_site_is']

	));

if ($allow_login == 'yes') {
	$page->replace_tags(array(
	'l_invalid_login'	=> $lang['l_invalid_login'],
	'l_password'		=> $lang['l_password'],
	'l_username'		=> $lang['l_username'],
	'l_login'			=> $lang['l_login'],
	'l_logout'			=> $lang['l_logout']
	));
}

$page->tomorrows_events($page);
$page->get_vtodo($page);
$page->draw_subscribe($page);	
$page->output();

?>

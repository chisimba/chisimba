<?php

/* Rewritten by J. Hu 4/2/06
*/

define('BASE','../');
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/calendar_functions.php');

if ($enable_rss != 'yes') {
	exit(error($lang['l_rss_notenabled'], $cal, '../'));
}

if (empty($default_path)) {
	if (isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'on' ) {
		$default_path = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'/rss/'));
	} else {
		$default_path = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'/rss/'));
	}
}

$current_view = "rssindex";
$display_date = "RSS Info";

$rss_list = "<table>\n";
$xml_icon ="<img src = 'xml.gif' alt='xml'>";

$filelist = availableCalendars($username, $password, $ALL_CALENDARS_COMBINED);
foreach ($filelist as $file) {
	// $cal_filename is the filename of the calendar without .ics
	// $cal is a urlencoded version of $cal_filename
	// $cal_displayname is $cal_filename with occurrences of "32" replaced with " "

	if (is_numeric(array_search($file, $cal_filelist))){
		$cal_displayname_tmp = $cal_displaynames[array_search($file,$cal_filelist)];
	}else{
		$cal_displayname_tmp = str_replace("32", " ", str_replace(".ics",'',basename($file)));
	}	
	$rss_list .= '<tr><td rowspan ="3"><font class="V12" color="blue"><b>'.$cal_displayname_tmp.' '. $lang['l_calendar'].'</b></font></td>';

/* Changed to show links without urlencode, but links valid urls */
	$rss_list .= "<td>".$lang['l_day']."</td>";
	$rss_list .= '<td><a href='.$default_path.'/rss/rss.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=day>'.$xml_icon.'</a> RSS 0.91</td>';
	$rss_list .= '<td><a href='.$default_path.'/rss/rss1.0.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=day>'.$xml_icon.'</a> RSS 1.0</td>';
	$rss_list .= '<td><a href='.$default_path.'/rss/rss2.0.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=day>'.$xml_icon.'</a> RSS 2.0</td></tr>';

	$rss_list .= "<td>".$lang['l_week']."</td>";
	$rss_list .= '<td><a href='.$default_path.'/rss/rss.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=week>'.$xml_icon.'</a> RSS 0.91</td>';
	$rss_list .= '<td><a href='.$default_path.'/rss/rss1.0.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=week>'.$xml_icon.'</a> RSS 1.0</td>';
	$rss_list .= '<td><a href='.$default_path.'/rss/rss2.0.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=week>'.$xml_icon.'</a> RSS 2.0</td></tr>';

	$rss_list .= "<td>".$lang['l_month']."</td>";
	$rss_list .= '<td><a href='.$default_path.'/rss/rss.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=month>'.$xml_icon.'</a> RSS 0.91</td>';
	$rss_list .= '<td><a href='.$default_path.'/rss/rss1.0.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=month>'.$xml_icon.'</a> RSS 1.0</td>';
	$rss_list .= '<td><a href='.$default_path.'/rss/rss2.0.php?cal='.rawurlencode($file).'&amp;cpath='.$cpath.'&amp;rssview=month>'.$xml_icon.'</a> RSS 2.0</td></tr>';

	$footer_check = $default_path.'/rss/rss.php?cal%3D'.rawurlencode($file.'&amp;cpath='.$cpath.'&amp;rssview='.$default_view);
	$validrss_check = str_replace('%', '%25', $footer_check);
	$rss_list .= "<tr><td>&nbsp;</td></tr>\n";

}
$rss_list .= "</table>\n";


/* End link modification */


$page = new Page(BASE.'templates/'.$template.'/rss_index.tpl');

$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
	'event_js'			=> ''
	));

$page->replace_tags(array(
	'version'			=> $phpicalendar_version,
	'default_path'		=> $default_path.'/',
	'template'			=> $template,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'calendar_name'		=> $calendar_name,
	'display_date'		=> $display_date,
	'current_view'		=> $current_view,
	'sidebar_date'		=> $sidebar_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_list'	 		=> $rss_list,
	'charset'	 		=> $charset,
	'rss_available' 	=> '',
	'rssdisable'	 	=> '',
	'rss_valid' 		=> '',
	'rss_docinfo'		=> "RSS feeds can also be set up for a specified number of days before or after a given date, or between two dates.  See the <a href='http://phpicalendar.net/documentation/index.php/RSS_feeds'>documentation</a> for how to set up the URLs",
/* Replaces footer.tpl {validrss_check} with $validrss_check */	
	'validrss_check'	=> $validrss_check,
	'show_search' 		=> $show_search,
	'l_rss_info'		=> $lang['l_rss_info'],
	'l_rss_subhead'		=> $lang['l_rss_subhead'],
	'l_day'				=> $lang['l_day'],
	'l_week'			=> $lang['l_week'],
	'l_month'			=> $lang['l_month'],
	'l_year'			=> $lang['l_year'],
	'l_subscribe'		=> $lang['l_subscribe'],
	'l_download'		=> $lang['l_download'],
	'l_this_months'		=> $lang['l_this_months'],
	'l_powered_by'		=> $lang['l_powered_by'],
	'l_this_site_is'	=> $lang['l_this_site_is']				
	));

$page->output();
								
								
?>

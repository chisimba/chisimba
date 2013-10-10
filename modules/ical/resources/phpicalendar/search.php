<?php

define('BASE','./');
$current_view = 'search';
$display_date = $lang['l_results'];
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/list_functions.php');
require_once(BASE.'functions/template.php');
header("Content-Type: text/html; charset=$charset");

if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != '') {
	$back_page = $_SERVER['HTTP_REFERER'];
} else {
	$back_page = BASE.$default_view.'.php?cal='.$cal.'&amp;getdate='.$getdate;
}

$search_valid = false;
if (isset($_GET['query']) && $_GET['query'] != '') {
	$query = $_GET['query'];
	$search_valid = true;
}

$search_box = '';
$search_box .= 
	'<form action="search.php" method="GET">'."\n".
        '<input type="hidden" name="cpath" value="'.$cpath.'">'."\n".
	'<input type="hidden" name="cal" value="'.$cal.'">'."\n".
	'<input type="hidden" name="getdate" value="'.$getdate.'">'."\n".
	'<input type="text" size="15" name="query" value="'.$query.'">'."\n".
        '<INPUT type="image" src="templates/'.$template.'/images/search.gif" border=0 height="19" width="18" name="submit" value="Search">'."\n".
	'</form>';

$search_started = getmicrotime();
if ($search_valid) {
	$format_search_arr = format_search($query);
	if (!$format_search_arr[0]) {
		$formatted_search = '<b>'.$no_query_lang.'</b>';
	} else {
		$formatted_search = $format_search_arr[0];
	}
	if (isset($master_array) && is_array($master_array)) {
		foreach($master_array as $date_key_tmp => $date_tmp) {
			if (is_array($date_tmp)) {
				foreach($date_tmp as $time_tmp) {
					if (is_array($time_tmp)) {
						foreach ($time_tmp as $uid_tmp => $event_tmp) {
							if (is_array($event_tmp)) {
								if (!isset($the_arr[$uid_tmp]) || isset($event_tmp['exception'])) {
                                                                        #print_r($format_search_arr);
                                                                        #echo "<br>this event:".$event_tmp['event_text']."<br>";
									$results1 = search_boolean($format_search_arr,$event_tmp['event_text']);
                                                                        
									if (!$results1) {
										$results2 = search_boolean($format_search_arr,$event_tmp['description']);
									}
									if ($results1 || $results2) {
										$event_tmp['date'] = $date_key_tmp;
										if (isset($event_tmp['recur'])) {
											$event_tmp['recur'] = format_recur($event_tmp['recur']);
										}
										if (isset($the_arr[$uid_tmp])) {
											$the_arr[$uid_tmp]['exceptions'][] = $event_tmp;
										} else {
											$the_arr[$uid_tmp] = $event_tmp;
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
} else {
	$formatted_search = '<b>'.$no_query_lang.'</b>';
}
$search_ended = getmicrotime();

$search_took = number_format(($search_ended-$search_started),3);

// takes a boolean search and formats it into an array
// use with sister function search_boolean()
function format_search($search_str) {
	// init arrays
	$and_arr = array();
	$or_arr = array();
	$not_arr = array();
	$or_str_arr = array();

	$search_str = strtolower($search_str);
	
	if ($search_str == ' ') return array(false,$and_arr,$or_arr,$not_arr);
	
	// clean up search string
	$search_str = trim($search_str);
	$search_str = str_replace(' and ', ' ', $search_str);
	$search_str = str_replace(' - ', ' ', $search_str);
	$search_str = ereg_replace('[[:space:]]+',' ', $search_str);
	$search_str = str_replace(' not ', ' -', $search_str);
	
	// start out with an AND array of all the items
	$and_arr = explode(' ', $search_str);
	$count = count($and_arr);
	$j = 0;
	
	// build an OR array from the items in AND
	for($i=0;$i<$count;$i++) {
		if ($i != 0 && $and_arr[$i] == 'or') {
			while ($and_arr[$i] == 'or') {
				$or_arr[$j][] = $and_arr[$i-1];
				unset($and_arr[$i], $and_arr[$i-1]);
				$i += 2;
			}
			if (isset($and_arr[$i-1])) {
				$or_arr[$j][] = $and_arr[$i-1];
				unset($and_arr[$i-1]);
			}
			$or_str_arr[$j] = implode('</b> OR <b>', $or_arr[$j]);
			$j++;
		}
	}

	// build a NOT array from the items in AND
	foreach($and_arr as $key => $val) {
		if (substr($val,0,1) == '-') {
			$not_arr[] = substr($val,1);
			unset($and_arr[$key]);
		} elseif(substr($val,0,1) == '+') {
			$and_arr[] = substr($val,1);
			unset($and_arr[$key]);
		}
	}
	
	// prepare our formatted search string
	if (count($and_arr) > 1) {
		$final_str_arr[] = implode('</b> AND <b>', $and_arr);
	} elseif (isset($and_arr[0]) && $and_arr[0] != '') {
		$final_str_arr[] = $and_arr[0];
	}
	
	if (count($or_str_arr) > 1) {
		$final_str_arr[] = implode('</b> AND <b>', $or_str_arr);
	} elseif (isset($or_str_arr[0]) && $or_str_arr[0] != '') {
		$final_str_arr[] = $or_str_arr[0];
	}
	
	if (count($not_arr) > 1) {
		$final_str_arr[] = '-'.implode('</b> AND <b>-', $not_arr);
	} elseif (isset($not_arr[0]) && $not_arr[0] != '') {
		$final_str_arr[] = '-'.$not_arr[0];
	}
	
	if (count($final_str_arr) > 1) {
		$formatted_search = '<b>'.implode('</b> AND <b>', $final_str_arr).'</b>';
	} else {
		$formatted_search = '<b>'.$final_str_arr[0].'</b>';
	}
	
	return array($formatted_search, $and_arr, $or_arr, $not_arr);
}

// takes an array made by format_search() and checks to see if it 
// it matches against a string
function search_boolean($needle_arr, $haystack) {
	// init arrays
	$and_arr = $needle_arr[1];
	$or_arr = $needle_arr[2];
	$not_arr = $needle_arr[3];
	
	if (!$needle_arr[0]) return false;
	if ((sizeof($and_arr) == 0) &&
		(sizeof($or_arr) == 0) &&
		(sizeof($not_arr) == 0)) return false;
	
	// compare lowercase versions of the strings
	$haystack = strtolower($haystack);

	// check against the NOT
	foreach($not_arr as $s) {
		if (is_string(strstr($haystack,$s)) == true) {
			return false;
		}
	}
	
	// check against the AND
	foreach($and_arr as $s) {
			#echo "haystack: $haystack<br>needle: $s<br>";
		if (is_string(strstr($haystack,$s)) == false) {
			return false;
		}
	}
	
	// check against the OR
	foreach($or_arr as $or) {
		$is_false = true;
		foreach($or as $s) {
			if (substr($s,0,1) == '-') {
				if (is_string(strstr($haystack,substr($s,1))) == false) {
					$is_false = false;
					break;
				}			
			} elseif (is_string(strstr($haystack,$s)) == true) {
				$is_false = false;
				break;
			}
		}
		if ($is_false) return false;	
	}
	// if we haven't returned false, then we return true
       # echo "return true<br>";
	return true;
}

function format_recur($arr) {
	global $format_recur_lang, $monthsofyear_lang, $daysofweek_lang;
	
	$d = $format_recur_lang['delimiter'];
	$int = $arr['INTERVAL'];
	$tmp = (($int == '1') ? 0 : 1);
	
	$freq = $arr['FREQ'];
	$freq = $format_recur_lang[$freq][$tmp];
	
	if		(isset($arr['COUNT']))	$for = str_replace('%int%',$arr['COUNT'],$format_recur_lang['count']);
	elseif	(isset($arr['UNTIL']))	$for = str_replace('%date%',$arr['UNTIL'], $format_recur_lang['until']);
	else							$for = '';
	
	$print = $format_recur_lang['start'];
	$print = str_replace('%int%', $int, $print);
	$print = str_replace('%freq%', $freq, $print);
	$print = str_replace('%for%', $for, $print);
	
	if (isset($arr['BYMONTH'])) {
		$list = '';
		$last = count($arr['BYMONTH']) - 1;
		foreach ($arr['BYMONTH'] as $key => $month) {
			if ($key == $last)	$list .= $monthsofyear_lang[($month-1)];
			else 				$list .= $monthsofyear_lang[($month-1)].$d;
		}
		$print .= '<br />'."\n";
		$print .= str_replace('%list%', $list, $format_recur_lang['bymonth']);
	}
	
	if (isset($arr['BYMONTHDAY'])) {
		$list = '';
		if ($arr['BYMONTHDAY'][(count($arr['BYMONTHDAY']) - 1)] == '0') unset($arr['BYMONTHDAY'][$last]);
		$last = count($arr['BYMONTHDAY']) - 1;
		foreach ($arr['BYMONTHDAY'] as $key => $day) {
			ereg('(-{0,1})([0-9]{1,2})',$day,$regs);
			list($junk,$sign,$day) = $regs;
			if ($sign != '')	$list .= $sign;
			if ($key == $last)	$list .= $day;
			else				$list .= $day.$d;
		}
		$print .= '<br />'."\n";
		$print .= str_replace('%list%', $list, $format_recur_lang['bymonthday']);
	}
	
	if (isset($arr['BYDAY'])) {
		$list = '';
		$last = count($arr['BYDAY']) - 1;
		foreach ($arr['BYDAY'] as $key => $day) {
			ereg('([-\+]{0,1})([0-9]{0,1})([A-Z]{2})',$day,$regs);
			list($junk,$sign,$day_num,$day_txt) = $regs;
			$num = two2threeCharDays($day_txt,false);
			if ($sign != '')	$list .= $sign;
			if ($day_num != '')	$list .= $day_num.' ';
			if ($key == $last)	$list .= $daysofweek_lang[$num];
			else				$list .= $daysofweek_lang[$num].$d;
		}
		$print .= '<br />'."\n";
		$print .= str_replace('%list%', $list, $format_recur_lang['byday']);
	}
	
	return $print;
}


$page = new Page(BASE.'templates/'.$template.'/search.tpl');

$page->draw_search($page);

	
$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
	'sidebar'			=> BASE.'templates/'.$template.'/sidebar.tpl',
	'event_js'			=> BASE.'functions/event.js',
	));


$page->replace_tags(array(
	'version'			=> $phpicalendar_version,
	'formatted_search'	=> $formatted_search,
	'l_results'			=> $lang['l_results'],
	'l_query'			=> $lang['l_query'],
	'l_time'			=> $lang['l_time'],
	'l_summary'			=> $lang['l_summary'],
	'l_location'		=> $lang['l_location'],
	'l_description'		=> $lang['l_description'],
	'l_recurring_event'	=> $lang['l_recurring_event'],
	'l_exception'		=> $lang['l_exception'],
	'l_no_results'		=> $lang['l_no_results'],
	'search_box'		=> $search_box,
	'charset'			=> $charset,
	'template'			=> $template,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'cpath'				=> $cpath,
	'calendar_name'		=> $cal_displayname,
	'display_date'		=> $display_date,
	'rss_powered'	 	=> $rss_powered,
	'default_path'		=> '',
	'rss_available' 	=> '',
	'rss_valid' 		=> '',
	'show_search' 		=> $show_search,
	'next_month' 		=> $next_month,
	'prev_month'	 	=> $prev_month,
	'show_goto' 		=> '',
	'is_logged_in' 		=> '',
	'list_jumps' 		=> $list_jumps,
	'list_icals' 		=> $list_icals,
	'list_years' 		=> $list_years,
	'list_months' 		=> $list_months,
	'list_weeks' 		=> $list_weeks,
	'legend'	 		=> $list_calcolors,
	'current_view'		=> $current_view,
	'style_select' 		=> $style_select,
        'sidebar_date'          => $sidebar_date,
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
	'l_subscribe'		=> $lang['l_subscribe'],
	'l_download'		=> $lang['l_download'],
	'l_this_months'		=> $lang['l_this_months'],
	'l_powered_by'		=> $lang['l_powered_by'],
	'l_this_site_is'	=> $lang['l_this_site_is']			
	));



$page->output();
#echo "<pre>";
#print_r($the_arr);
#echo "</pre>";
?>

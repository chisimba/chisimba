<?php

/********************************************************************************
*	Modified from phpicalendar 2.0a distribution by Jim Hu
*	philosophical changes 
*		- instead of having separate generators, use a date range for all views (done)
*		- change the rss generation method to conform to standards(not done)
*	PHP note: #@ is error control operator to suppress execution halt on error
*		- used below to deal with undef?
*
*	using rssview, RSS feeds can be specified to return events for a given day, week, month, or year 
*	feeds can be specified for a number of days to or from a given date
*	feeds can be specified for a range of dates
*
*********************************************************************************/

/* Modified from 2.21 by dyfrin 2006/03/08 19:09:28
   Changes:
   -RSS changed to 2.0, encoding removed, languages converted to ISO standard for feeds
   -RSS title changed to be set by config.inc.php.  Make sure that is added to it. 
   Lines modified: 135-165, 208-223
*/
   
define('BASE', '../');
require(BASE.'functions/init.inc.php');

if ($enable_rss != 'yes') {
	die ("RSS feeds are not enabled on this site.");
}

include_once(BASE.'functions/date_functions.php');


//set the range of days to return based on the view chosen
$rssview = $_GET['rssview'];
if (isset($_GET['getdate']) && $_GET['getdate'] !=''){
	$getdate = $_GET['getdate'];
}else{
	$getdate = date("Ymd");
}
# for all views, $fromdate is the first day to be shown and $todate should be the last day.
switch ($rssview){
	case 'day':
		$fromdate = $getdate;
		$todate = $getdate;
		$theview = $lang['l_day'];
		break;
	case 'week':
		$fromdate = dateOfWeek($getdate, 'Sunday');
		$todate = $fromdate + 6;
		$theview = $lang['l_week']." of ".date('n/d/Y',strtotime($fromdate));
		break;
	case 'month':
		$parse_month = date ("Ym", strtotime($getdate));
		$fromdate = ($parse_month *100) + 1;
		$todate = ($parse_month *100) + date("t",strtotime($getdate));
		$theview = date('M Y',strtotime($fromdate));
		break;
	case 'year':
		if(isset($_GET['year'])){
			$theyear = $_GET['year'];
		}else{
			$theyear = substr($getdate,0,4);
		}
		$fromdate = ($theyear*10000)+101;	
		$todate = date("Ymd", strtotime($theyear*10000+1231));
		$theview = $theyear;
		break;
	case 'daysfrom':
		$fromdate = $getdate;
		$todate = date("Ymd", strtotime($getdate) + $_GET['days']*60*60*24);
		$theview = $_GET['days']." days from ".date('n/d/Y',strtotime($fromdate));
		break;
	case 'daysto':
		$todate = $getdate;
		$fromdate = date("Ymd", strtotime($getdate) - $_GET['days']*60*60*24);
		$theview = $_GET['days']." days before ".date('n/d/Y',strtotime($todate));
		break;
	case 'range':
		if(isset($_GET['from'])){
		$fromdate = $_GET['from'];
		}else{
			$fromdate = $getdate;
		}
		$todate = $_GET['to'];
		$theview = date('n/d/Y',strtotime($fromdate)).'-'.date('n/d/Y',strtotime($todate));
		break;
	default:
		#default to week
		$fromdate = dateOfWeek($getdate, 'Sunday');
		$todate = $fromdate + 6;
		$theview = "";

}
#need to give ical_parser the most distant date to correctly set up master_array.
$getdate = $todate;
#echo "from:$fromdate to:$todate";

#Note that ical_parser supplies cal_displayname.
include(BASE.'functions/ical_parser.php');

$events_count = 0;

// calculate a value for Last Modified and ETag
$cal = implode(",",$cals);

//get filemtime from master array
$filemod = 0; #default to start of unix era, overwrite with most recent mtime from master array
foreach ($master_array['-4'] as $calinfo){
	if ($calinfo['mtime'] > $filemod) $filemod = $calinfo['mtime']; 
}	
$filemodtime = date("r", $filemod);

//send relevant headers
header ("Last-Modified: $filemodtime");
header ("ETag:\"$filemodtime\"");

// checks the user agents headers to see if they kept track of our
// stuff, if so be nice and send back a 304 and exit.

if ( ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $filemodtime) || ($_SERVER['HTTP_IF_NONE_MATCH'] == $filemodtime))
{
#	header ("HTTP/1.1 304 Not Modified");
#	exit;
}

/* Change languages to ISO 639-1 to validate RSS without changing long version in config.inc.php */
$user_language = array ("english", "polish", "german", "french", "dutch", "italian", "japanese", "norwegian", "spanish",  "swedish", "portuguese", "catalan", "traditional_chinese", "esperanto", "korean");
$iso_language = array ("en", "pl", "de", "fr", "nl", "da", "it", "ja", "no", "es", "sv", "pt", "ca", "zh-tw", "eo", "ko");
$rss_language = str_replace($user_language, $iso_language, $language);
/* End language modification */

$rss = 	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>"."\n";
#$rss .= '<!DOCTYPE rss PUBLIC "-//Netscape Communications//DTD RSS 2.01//EN" "http://my.netscape.com/publish/formats/rss-0.91.dtd">'."\n";

/* Use 2.0 and strip encoding, use rss_language */
$rss .= '<rss version="2.0"'."\n";
$rss .= 	'xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"  
	xmlns:ev="http://purl.org/rss/1.0/modules/event/" 
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	>'."\n";
$rss .= '<channel>'."\n";


$rss .= '<title>'.$cal_displayname;
if ($theview !=""){$rss .= ' - '.$theview;} 
$rss .= "</title>\n";

$rss .= '<link>'.$default_path.'/rss/rss2.0.php/';
if (isset($cpath) && $cpath !='') $rss_link.="?cpath=$cpath";
$rss .='</link>'."\n";
$rss .= '<description>'.$cal_displayname.' '.$lang['l_calendar'].' - '.$theview.'</description>'."\n";
$rss .= '<language>'.$rss_language.'</language>'."\n";


/* End rss 2.0 modification */


//generate the items
$numdays = round((strtotime($todate) - strtotime($fromdate))/(60*60*24))+1;
$thisdate = $fromdate; 	#	start at beginning of date range, 
						# 	note that usage of $thisdate is different from distribution
						# 	I use it as a date, dist uses it as a time
$i = 1;  #day counter

#$rss .= "<items>\n<rdf:Seq>\n";
$uid_arr = array();
	do {
	$thisdate=date('Ymd', strtotime($thisdate));
	#echo "Date: $thisdate<br>\n";
	$dayofweek = localizeDate ("%a %b %e %Y", strtotime($thisdate));
	if (isset($master_array[($thisdate)]) && sizeof($master_array[($thisdate)]) > 0) {
		foreach ($master_array[("$thisdate")] as $event_times) {
			foreach ($event_times as $uid=>$val) {
				#handle multiday all day events
				if(!$val["event_start"]){
					if (isset($uid_arr[$uid])){
						$uid_arr[$uid] .= "+$dayofweek" ;
						continue;
					}else{
						$uid_arr[$uid] = "$dayofweek" ;
					}
					$event_start = $lang['l_all_day'];
				}else{	
					$event_start 	= @$val["event_start"];	
					$event_start 	= date ($timeFormat, @strtotime ("$event_start"));
				}	
				$event_text 	= stripslashes(urldecode($val["event_text"]));
				$event_text 	= strip_tags($event_text, '<b><i><u>');
			#	$event_text		= urlencode($event_text);
			#uncomment for shorter event text with ...
			#	$event_text 	= word_wrap($event_text, 21, $tomorrows_events_lines); 		
				$description 	= stripslashes(urldecode($val["description"]));
				$description 	= strip_tags($description, '<b><i><u>');

				$rss_title		= htmlspecialchars ("$dayofweek: $event_text");
				/* original link, not valid url coding 
				$rss_link		= htmlspecialchars ("$default_path/day.php?getdate=$thisdate&cal=$cal&cpath=$cpath");
				*/
				/*  Add %20's for spaces for the calendar links to make them valid url's */
				$urlcal 		= rawurlencode ("$cal");
				$rss_link		=  ("$default_path/day.php?getdate=$thisdate&amp;cal=$urlcal");
				if (isset($cpath) && $cpath !=''){
					$rss_link .= "&amp;cpath=$cpath";
				}
				/* End link modification */
				$rss_description	= htmlspecialchars ("$dayofweek $event_start: $description");
				
				#$rss .= '<rdf:li rdf:resource="'.$rss_link.'&amp;uid='.$uid.'/" />'."\n";
				$rss .= '<item>'."\n";

				/* Create guid, and use uid to make link unique */
				$rss .= '<guid isPermaLink="false">'.$rss_link.'&amp;uid='.$uid.'</guid>'."\n";
				/* End guid modification */
				$rss .= '<title>'.$rss_title.'</title>'."\n";
				$rss .= '<ev:startdate>'.date("Y-m-d", strtotime($thisdate)).'T'.date("h:m:s",$val["event_start"]).'</ev:startdate>'."\n";
				$rss .= '<ev:enddate>'.date("Y-m-d", strtotime($thisdate)).'T'.date("h:m:s",$val["event_start"]).'</ev:enddate>'."\n";

				$rss .= '<link>'.$rss_link.'</link>'."\n";
				$rss .= '<description>'.$rss_description.'</description>'."\n";
				if (isset($val['location']) && $val['location'] !=''){
					$rss .= '<ev:location>'.$val['location']."</ev:location>\n";
				}	
				$rss .= '</item>'."\n";
				$events_count++;
			}
		}
	}
	if (($events_count < 1) && ($i == $numdays)) {
		$rss .= '<item>'."\n";
		$rss .= '<title>No events found</title>'."\n";
		$rss .= '<link>'.htmlspecialchars ("$default_path").'</link>'."\n";
		$rss .= '</item>'."\n";
	}
	$thisdate++;
	$i++;	
	} while ($i <= $numdays);

#$rss .= "</rdf:Seq>\n</items>\n";
$rss .= "\n</channel>\n</rss>\n";

foreach ($uid_arr as $uid=>$date_range){
	#echo "date_range:$date_range<br>";

	if(strpos($date_range,"+")>0){
		#echo "+ in date_range<br>";
		$temp = explode("+",$date_range);
		$date_range = $temp[0].'-'.array_pop($temp);
	}
	$rss = str_replace("<uid>$uid</uid>\n<event_start>".$lang['l_all_day']."</event_start>","<uid>$uid</uid>\n<event_start>$date_range</event_start>", $rss);

}
header ("Content-Type: text/xml");

echo "$rss";

?>

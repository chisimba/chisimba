<?php
/**
 * A class that implements and writes some microformats
 * 
 * This project works with PHP 4.x and PHP 5
 * 
 * Related projects: 
 * 	- hkit ( http://allinthehead.com/hkit ) - requires PHP5
 * 
 * Related websites:
 *  - microformats.org ( http://microformats.org/ ) - information about microformats 
 * 
 * @author Tobias Kluge, enarion.net
 * @version 0.1 from 2006-12-02
 * @license http://www.gnu.org/copyleft/gpl.html GPL v2
 * 
 * 
 */
class phpMicroformats {

	/**
	 * initialization
	 */
    function phpMicroformats() {
    }
    
    /**
     * creates and returns a valid hCalendar entry for given data
     * 
     * 
     * @param array $data contains the data of the calendar entry
     * @return string contains the hCalendar entry
     */
    /* static */ function createHCalendar($data) {
    	// prepare data
    	$data['dtstart'] = phpMicroformats::getTimestampISO8601($data['begin']);
    	$data['dtend'] = phpMicroformats::getTimestampISO8601($data['end']);
    	
    	$data['dtstart_h'] = phpMicroformats::getTimestampHuman($data['begin']);
    	$data['dtend_h'] = phpMicroformats::getTimestampHuman($data['end']);
    	// write result
    	$result = '';
    	
    	$result .= '<div class="vevent">'."\n";
    	
    	if (isset($data['url']) && strlen($data['url'])>0) {
    		$result .= '<a class="url" href="'.$data['url'].'" title="'.$data['name'].'">';
    	}
    	$result .= '<div><span class="summary">'.$data['name'].'</span></div>'."\n";
    	if (isset($data['url']) && strlen($data['url'])>0) {
    		$result .= '</a>';
    	}
    	
    	$result .= '<div>Begin: <span class="dtstart" title="'.$data['dtstart'].'">'.$data['dtstart_h'].'</span></div>'."\n";
    	$result .= '<div>End: <span class="dtend" title="'.$data['dtend'].'">'.$data['dtend_h'].'</span></div>'."\n";


		if (isset($data['location'])) {
			// TODO handle type of location: dom, intl, postal, parcel, home, work, pref
			$result .= phpMicroformats::formatLocation($data['location']);
		}
    	
    	$result .= '</div>'."\n";
    	
    	return $result;
    }


    /**
     * creates and returns a valid hCard entry for given data
     * 
     * 
     * @param array $data contains the data of the card entry
     * @return string contains the hCard entry
     */
    /* static */ function createHCard($data) {
    	$result = '';

	// prepare data
		$data['fn'] = $data['name']; // TODO parse name, apply vcard scheme (?)

	// encode vcard
    	$result .= '<div class="vcard">'."\n";
    	
		// name
		$result .= '<span class="fn">'.$data['fn'].'</span>'."\n";

		// email
		$result .= '<span>Email: <a class="fn email" href="mailto:'.$data['email'].'">'.$data['email'].'</a></span>'."\n";
	
		// company / organization
		if (isset($data['org'])) {
			if (isset($data['org']['name'])&& strlen($data['org']['name'])>0) $result .= '<span class="org">'.$data['org']['name'].'</span>'."\n";
			if (isset($data['org']['title']) && strlen($data['org']['title'])>0) $result .= '<span class="title">'.$data['org']['title'].'</span>'."\n";
		}
		
		// location
		if (isset($data['location'])) {
			// TODO handle type of location: dom, intl, postal, parcel, home, work, pref
			$result .= phpMicroformats::formatLocation($data['location']);
		}

		// phone
		if (isset($data['phone'])) {
			// TODO parse phone number!
			if (is_string($data['phone'])) {
				$result .= '<div class="tel">'."\n";
				$result .= '<span>'.$data['phone'].'</span>'."\n";
				$result .='</div>'."\n";
			}
			
			// handle phone types: msg, home, work, pref, voice, fax, cell, video, pager, bbs, car, isdn, pcs
			$phoneTypes = array('msg', 'home', 'work', 'pref', 'voice', 'fax', 'cell', 'video', 'pager', 'bbs', 'car', 'isdn', 'pcs');
			if (is_array($data['phone'])) {
				foreach($data['phone'] as $type => $number) {
					if (in_array($type, $phoneTypes)) {
						$result .= '<div class="tel">'."\n";
						$result .= '<span class="type">'.$type.'</span>'."\n";
        				$result .= '<span class="value">'.$number.'</span>'."\n";
        				$result .= '</div>'."\n";
					} // else: not valid type, ignore it
				}
			}
		}		
				
		// photo
		if (isset($data['photo']) && strlen($data['photo'])>0) {
			// local files can be encoded and added directly to the returned data
			if (is_file($data['photo']) && file_exists($data['photo'])) {
				// extract image type
				$imageTypes = array('png' => array('png', 'PNG'), 'jpeg' => array('jpg', 'jpeg', 'JPG', 'JPEG'), 'gif' => array('gif', 'GIF'));
				$imageType = '';
				foreach ($imageTypes as $type => $extensions) {
					foreach($extensions as $tmpId => $imageExtension) {
						// check filename extension - if ok, use this image type for the encoding
						if (substr($data['photo'],-strlen('.'.$imageExtension)) == '.'.$imageExtension) {
							$imageType = $type;
							break;
						}
					}
					if ($imageType != '') break;
				}
				
				// TODO maybe check size of image, do not encode large images
				$result .= '<img class="photo" src="data:image/'.$imageType.';base64,'.base64_encode(implode('',file($data['photo']))).'" alt="'.$data['fn'].'" />'."\n";
			} else { // TODO check if valid url with existing file at the end!
				// use url of image
				$result .= '<img class="photo" src="'.$data['photo'].'" alt="'.$data['fn'].'" />'."\n";
			}
		}
		
		// IM support: AIM, MSN, Skype, Yahooo
		if (isset($data['im'])) {
			$result .= '<div class="im">'."\n"; // class "im" is not supported by microformats.org, but might be useful
			if (isset($data['im']['aim'])) { 
				$result .= '<a class="url" href="aim:goim?screenname='.$data['im']['aim'].'">AIM chat with '.$data['fn'].'</a>'."\n";
			}
	
			if (isset($data['im']['msn'])) {
				$result .= '<a class="url" href="msnim:chat?contact='.$data['im']['msn'].'@hotmail.com">MSN chat with '.$data['fn'].'</a>'."\n";
			}
	
			if (isset($data['im']['skype'])) { 
				$result .= '<a class="url" href="skype:'.$data['im']['skype'].'?call">Skype call to '.$data['fn'].'</a>'."\n";
				$result .= '<a class="url" href="skype:'.$data['im']['skype'].'?chat">Skype chat with '.$data['fn'].'</a>'."\n";
			}
		
			if (isset($data['im']['xmpp'])) {
				$result .= '<a class="url" href="xmpp:'.$data['im']['xmpp'].'">XMPP chat with '.$data['fn'].'</a>'."\n";
			}
		
			if (isset($data['im']['yahoo'])) {
				$result .= '<a class="url" href="ymsgr:sendIM?'.$data['im']['yahoo'].'">Yahoo chat with '.$data['fn'].'</a>'."\n";
			}
			$result .= '</div>'."\n";
		}    	
    	
    	$result .= '</div>'."\n";
    	
    	return $result;
    }

	/**
	 * encodes a given location
	 * 
	 * @param array $location contains the data of the location
	 * @return string containing the formated location
	 */
	function formatLocation($location) {
		$result = '';

		$result .= '<p class="adr">'."\n";
		$result .= '<span style="font-weight:bold;">Location</span><br />'."\n";
		
		if (isset($location['street']) && strlen($location['street'])>0) {
			$result .= '<span class="street-address">'.$location['street'].'</span><br />'."\n";
		}	

		if (isset($location['town']) && strlen($location['town'])>0) {
			$result .= '<span class="locality">'.$location['town'].'</span>'."\n";
		}

		if (isset($location['zip']) && strlen($location['zip'])>0) {
			$result .= '<span class="postal-code">'.$location['zip'].'</span><br />'."\n";
		}

		if (isset($location['state']) && strlen($location['state'])>0) {
			$result .= '<span class="region">'.$location['state'].'</span><br />'."\n";
		}

		if (isset($location['country']) && strlen($location['country'])>0) {
			$result .= '<span class="country-name">'.$location['country'].'</span>'."\n";
		}
	
		$result .= '</p>'."\n";

		return $result;		
	}

	/**
	 * this function generates a timestamp in format ISO-8601 for given unixtimestamp
	 * 
	 * @param int $timestamp the unix timestamp
	 * @return string the ISO-8601 timestap
	 */
	function getTimestampISO8601($timestamp) {
		return is_int($timestamp) ?
			date('Y-m-d\TH:i:s', $timestamp).substr(date("O", $timestamp),0,3) . ":" .substr(date("O",$timestamp),3)
			: $timestamp;
	}

	/**
	 * this function generates a human readable timestamp for given unixtimestamp
	 * 
	 * @param int $timestamp the unix timestamp
	 * @return string the human readable timestap
	 */
	function getTimestampHuman($timestamp) {
		return is_int($timestamp) ?
			date('Y-m-d g:i A', $timestamp).' '.substr(date("O", $timestamp),0,3) . ":" .substr(date("O",$timestamp),3)
			: $timestamp;
	}

}
?>
<?php
/**
*
*
*/


/**
* WWW Functions
*
* Purpose
*     Any Functions for dealing with the web, it's paths etc
*
* @author  Blair Robertson <blair@squiz.net>
* @author prince mbekwa
* @version $Revision$
* @package 5ive
* @subpackage Installer
*/


/**
* Takes a two URLs and returns the relative HREF required to get
* you from the first URL to the second.
*
* @param string	$from	the absolute URL to come from, in the form protocol://domain/dir/dir2/file
* @param string	$to		the absolute URL to get to, in the form protocol://domain/dir/dir2/file
*
* @return string
* @access public
*/
function relative_href($from,$to)
{
	$from_bits = preg_split('/\\/+/', trim($from));
	$to_bits   = preg_split('/\\/+/', trim($to));

	// Ensure the first element is the protocol
	if (!preg_match('/^[A-Za-z]+:$/', $from_bits[0])) { array_unshift($from_bits, 'http:'); }
	if (!preg_match('/^[A-Za-z]+:$/', $to_bits[0]  )) { array_unshift($to_bits,   'http:'); }
	// Different protocols or domains? ABSOLUTE HREF!
	if (strtolower($from_bits[0]) != strtolower($to_bits[0]) || strtolower($from_bits[1]) != strtolower($to_bits[1])) {
		$to_bits[0] .= '/';
		return implode('/', $to_bits);
	}

	// Different first directories? Root path!
	if (empty($from_bits[2]) || empty($to_bits[2]) || $from_bits[2] != $to_bits[2]) {
		// Shift off protocol and domain
		array_splice($to_bits,   0, 2);
		return '/'.implode('/', $to_bits);
	}

	// Start from the second directory and find the place where the urls start to vary
	$split_point = 3;
	while (!empty($from_bits[$split_point])
			&& !empty($to_bits[$split_point])
			&& $from_bits[$split_point] == $to_bits[$split_point]) {
		$split_point++;
	}

	$href = str_repeat('../', count($from_bits) - $split_point);
	// Forward to the destination
	for ($i = $split_point - 1, $max = count($to_bits); $i < $max; $i++) {
		$href .= $to_bits[$i].'/';
	}

	// if they wanted a trailing slash make sure there is one, else remove it
	if (substr($to, -1) == '/') { $href = preg_replace('/\\/+$/', '/', $href); }
	else { $href = preg_replace('/\\/$/', '', $href); }

	// No remove any bit where we go back a directory then forward again to the
	// same last common directory
	return str_replace('../'.$from_bits[$split_point - 1].'/', '', $href);

}//end relative_href()


/**
* Checks an email address for validity
*
* @param string	$e	the email address to check
*
* @return boolean
* @access public
*/
function valid_email($e)
{
	return preg_match("|^[A-Za-z0-9](([_\.\-\']?[A-Za-z0-9]+)*)@([0-9a-zA-Z]([0-9A-Za-z-]*)+\.)+[a-zA-Z]{2,}$|",$e);

}//end valid_email()


/*
* Convert POST or GET array into raw string
*
* @param array $data	post or get array
*
* @return string
* @access public
**/
function make_raw_post_data($data)
{
	$val_array = null;

	if (!empty($data) && is_array($data)) {
		foreach ($data as $name => $value) {
			$val_array[] = recursive_make_raw_post_data($name, $value);
		}
	}

	if (!empty($val_array)) {
		$result = implode('&', $val_array);
	} else {
		$result = '';
	}

	return $result;

}//end make_raw_post_data()


/**
* Convert POST or GET array into raw string recursive
*
* @param string $name	name of the variable
* @param mixed $data	value
*
* @return string
* @access public
**/
function recursive_make_raw_post_data($name, $data)
{
	if (!is_array($data)) {
		return $name.'='.urlencode($data);
	} else {
		$val_array=Array();
		foreach ($data as $key => $value) {
			$val_array[] = recursive_make_raw_post_data($name.'['.$key.']', $value);
		}
		return implode('&', $val_array);
	}

}//end recursive_make_raw_post_data()


/**
* Undo the effect of htmlspecialchars
*
* @param string $string	string containing html special characters
*
* @access public
* @return string
*/
function undo_htmlspecialchars($string)
{
	$string = str_replace('&amp;','&',$string);
	$string = str_replace('&#039;',"'",$string);
	$string = str_replace('&quot;','"',$string);
	$string = str_replace('&lt;','<',$string);
	$string = str_replace('&gt;','>',$string);

	return $string;

}//end undo_htmlspecialchars()


?>
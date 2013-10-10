<?php
/**
* List of functions used to evaluate the FILTER statements in
* SPARQL queries.
*
* @package sparql
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id$
* @license http://www.gnu.org/licenses/lgpl.html LGPL
*
*/

 


/**
* Evaluates the regex() function. Returns true if the regex is matched false if not.
*
* @param String  $string   the string which has to be evaluated
* @param String  $pattern  the regex pattern
* @param String  $flags    additional flags like "i"
* @return boolean
*/
function regex($string,$pattern,$flags = ''){
	$string = trim($string);
	$pattern = trim($pattern);
	if(strpos($string,"str_")===0){
		$string = substr($string,4);
		$pattern = substr($pattern,4);
		$flags = substr($flags,4);
	}else{
		return false;
	}
	if(preg_match('/'.$pattern.'/'.$flags,$string))
	return true;
	else
	return false;
}

/**
* Evaluates the dateTime() function.Tries to convert a date string into
* a unix timestamp.
*
* @param  String $string the date string
* @return integer        the corresponding unix timestamp
*/
function dateTime($string){
	$string = trim($string);
	if(strpos($string,"str_")===0)
	$string = substr($string,4);

	$time = strtotime($string);
	if($time == -1)
	return $string;
	else
	return $time;
}


/**
* Evaluates the langMatches() function. Return true if the lang tag matches false if not.
*
* @param String $lang_range the string.
* @param String $lang_tag the regex pattern
* @return boolean
*/
function langMatches($lang_range,$lang_tag){

	if($lang_range == null)
	return false;

	if(strpos($lang_range,"str_")===0)
	$lang_range = substr($lang_range,4);
	if(strpos($lang_tag,"str_")===0)
	$lang_tag = substr($lang_tag,4);

	if(strtolower($lang_range) == strtolower($lang_tag))
	return true;
	$tags =  preg_match_all("/[^\-\s].[^\-\s]*/",$lang_range,$hits);
	if($tags){
		if($lang_tag == '*')
		return true;
		foreach($hits[0] as $tag){
			if(strtolower($tag) == strtolower($lang_tag))
			return true;
		}
		return false;
	}else{
		return false;
	}
}

/**
* Evaluates the str() function. Returns the string representation of a 
* variable or RDF term.
*
* @param  String $string the string
* @return boolean
*/
function str($string){
	$str = preg_match("/\".[^\"]*\"|\'.[^\']*\'/",$string,$hits);
	if($str != 0){
		return "str_".$hits[0];
	}else{
		if(strpos($string,"str_")===0){
			return $string;
		}else{
			if(strpos($string,"uri_")===0)
				return "str_".substr($string,4);
			if(strpos($string,'<')==0)
				return "str_".substr($string,1,-1);
		}
	}
	return false;
}

/**
* Evaluates the lang() function. Returns lang tag of a Literal.
*
* @param  String $string the string.
* @return String the lang tag or false if there is no language tag.
*/
function lang($string){
	$str = preg_match("/\".[^\"]*\"@(.[^\s]*)|\'.[^\']*\'@(.[^\s]*)/",$string,$hits);
	if($str){
		if($hits[1] != null)
		return $hits[1];
		else
		return $hits[2];
	}else{
		return false;
	}
}



?>
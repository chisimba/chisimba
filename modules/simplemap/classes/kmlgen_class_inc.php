<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to generate KML placemarkers with a simple overlay
 * 
 * This class (as with most of my GIS related classes) is dedicated to Laika (http://en.wikipedia.org/wiki/Laika)
 * without whom most of the cool things that we do with remote sensing would not be possible today.
 * 
 * @access public
 * @package simplemap
 * @author Paul Scott
 * @copyright AVOIR GNU/GPL
 * @filesource
 */

class kmlgen extends object {
	
	/**
	 * Class to generate KML from arbitrary latlong coords
	 * 
	 * @example $kml = $this->getObject('kmlgen','simplemap');
	 * 			$doc = $kml->overlay('my map','a test map')->generateSimplePlacemarker('place1', 'a place', '18.629057','-33.932922',0)
	 * 			->generateSimplePlacemarker('place2', 'another place', '32.56667','0.33333',0)->simplePlaceSuffix();
	 * 		 	
	 * @author Paul Scott
	 * @access public
	 */
	
	/**
	 * Language object
	 *
	 * @var object
	 */
	public $objLanguage;
	
	/**
	 * The KML string (document)
	 *
	 * @var string
	 */
	public $kml;
	
	/**
	 * Standard init function (pseudo constructor)
	 * 
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		
	}
	
	/**
	 * Create the initial overlay
	 *
	 * @param string $name
	 * @param string $description
	 * @return object
	 */
	public function overlay($name, $description)
	{
		$str = NULL;
		$str .= '<?xml version="1.0" encoding="UTF-8"?>';
		$str .= '<kml xmlns="http://earth.google.com/kml/2.0">';
		$str .= '<Document>';
		$str .= '<name>'.$name.'</name>';
		$str .= '<description>'.$description.'</description>';
		
		return $str;
		//return $this;
	}
	
	/**
	 * Method to  generate a simple placemarker
	 *
	 * @param string $name
	 * @param string $desc
	 * @param float $longitude
	 * @param float $latitude
	 * @param integer $altitude
	 * @return object
	 */
	public function generateSimplePlacemarker($name, $desc, $longitude, $latitude, $altitude = 0)
	{
		$spm = NULL;
		$spm .= '<Placemark>';
		$spm .= '<name>'.$name.'</name>';
    	$spm .= '<description><![CDATA['.$desc.']]></description>';
    	$spm .= '<Point>';
    	$spm .= '<coordinates>'.$longitude.','.$latitude.','.$altitude.'</coordinates>';
    	$spm .= '</Point>';
    	$spm .= '</Placemark>';
    	
    	//$this->kml .= $spm;
    	return $spm;
		
	}
	
	/**
	 * Method to generate the suffix to the doc
	 *
	 * @param void
	 * @return string
	 */
	public function simplePlaceSuffix()
	{
		$suffix = NULL;
		$suffix .= '</Document>';
		$suffix .= '</kml>';
		
		//$this->kml .= $suffix;
		return $suffix;
	}
	
	/**
	 * @todo this class so far does a really simple placemarker
	 * 		 It needs to be XML_Beautified and then extended!
	 */
	
	
}
?>
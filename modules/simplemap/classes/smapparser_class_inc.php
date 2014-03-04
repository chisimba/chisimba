<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* This is a parser for including SMAPs into content
*
* @author Derek Keats
* @category Chisimba
* @package timeline
* @copyright AVOIR and UWC
* @licence GNU/GPL
*
*/
class smapparser extends object
{

    /**
    *
    * @var string $demoMap Holds the value of the demo timeline
    *
    */
    public $demoMap;

    /**
    *
    * @var string $url Holds the value of the timeline to display
    *
    */
    public $url;

	/*
	* @var string $timeLineModuleLink Holds the link for the timeline module
	*/
    public $sMapModuleLink;

    /**
    *
    * Standard init method
    *
    * @access Public
    *
    */
    public function init()
    {
        $this->sMapModuleLink = $this->Uri(array(), "simplemap");
    }

    /*
     *
     * Method to set the uri parameter for the timeline to be
     * parsed.
     *
     * @access public
     * @return string The URI for the timeline to be parsed
     *
     */
	public function setMapUri($uri)
	{
	    $this->url = $uri;
	    return TRUE;
	}

	/*
	 *
	 * Method to get the timeline Uri as stored
	 *
	 * @access public
	 * @return string The URI or Null if not set
	 *
	 */
	public function getMapUri()
	{
	    if (isset($this->uri)) {
	        return $this->uri;
	    } else {
	        return NULL;
	    }
	}

	/**
	 *
	 * Show method do display the map
	 * @access public
	 * @return strng The map in an iframe
	 *
	 */
    public function show()
    {
        unset($objIframe);
    	$objIframe = $this->getObject('iframe', 'htmlelements');
        $ret = $this->sMapModuleLink;
        $url = $this->uri(array(
          "action" => "viewmap",
          "mode" => "plain"), "simplemap");
        //cant use the uri method because it urlencodes it and messes it up
        $url .= "&smap=" . $this->url;
		$objIframe->src=$url;
		$objIframe->width="800";
		$objIframe->height="600";
        return $objIframe->show();
    }

    /**
     *
     * Method ot use in a parser to return the map using
     * the show method
     * @access public
     * @return string The map formatted in an Iframe
     *
     */
    public function getRemote($smapFile) {
    	$this->setMapUri($smapFile);
        return $this->show();
    }

    /**
     *
     * Method to use in a parser to return the map based on
     * its id field in tbl_simplemap_maps table
     *
     * @access public
     * @aparam string $id The id of the map in the database
     * @return string The map in an iframe or a text error message if the id is not found
     *
     */
    public function getLocal($id){
    	$objDb = $this->getObject("dbmaps", "simplemap");
    	$ar = $objDb->getRow("id", $id);
    	if (isset($ar)) {
    	    $title = $ar['title'];
	    	$description = $ar['description'];
	    	$url = $ar['url'];
	    	$glat = $ar['glat'];
	    	$glong = $ar['glong'];
	    	$magnify = $ar['magnify'];
    		$width = $ar['width'];
    		$height = $ar['height'];
	    	$maptype = $ar['maptype'];
			$frSrc = $this->uri(array(
			  "mode" => "plain",
	          "action" => "viewmap",
	          "glat" => $glat,
	          "glong" => $glong,
	          "magnify" => $magnify,
	          "height" => $height,
	          "width" => $width,
	          "maptype" => $maptype,
			  "smap" => $url), "simplemap");
	    	$objIframe = $this->getObject('iframe', 'htmlelements');
	    	$objIframe->width = $width;
	    	$objIframe->height=$height;
	    	$objIframe->src = $frSrc;
	    	$objIframe->scrolling = "no";
	    	//Add the title to the map
			$objH = $this->getObject('htmlheading', 'htmlelements');
			//Heading H3 tag
			$objH->type=3;
			$objH->str = $title;
			$ret = $objH->show();
			$ret .= $objIframe->show();
			$ret .= "<br />" . $description;
    	} else {
		    //Give some results when the map is not found" .
		    $objLanguage = $this->getObject('language', 'language');
		    $ret = "<span class=\"error\"><h1>"
		      . $objLanguage->languageText("mod_simplemap_error_mapnotfound", "simplemap")
		      . ": " . $id ."</h1></span>";
		}
        return $ret;
    }

}
?>
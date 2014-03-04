<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* This is a simple class to build the basic Google Maps API
* as required to do a simple map.
*
* @author Derek Keats
* @category Chisimba
* @package simplemap
* @copyright AVOIR & UEC
* @licence GNU/GPL
*
*/
class simplebuildmap extends object
{
    /**
    *
    * @var $objConfig String object property for holding the
    * configuration object
    * @access public
    *
    */
    public $objConfig;
    /**
    *
    * @var $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;
    /**
    *
    * @var $width String property for holding the width of the map
    * @access public
    *
    */
    public $width;
    /**
    *
    * @var $height String property for holding the height of the map
    * @access public
    *
    */
	public $height;
    /**
    *
    * @var $gLat String property for holding the latitude X-coordinate of
    * the centre of the map
    * @access public
    *
    */
    public $gLat;
    /**
    *
    * @var $gLong String property for holding the latitude Y-coordinate of
    * the centre of the map
    * @access public
    *
    */
    public $gLong;
    /**
    *
    * @var $magnify String property for holding the magnification factor
    * for the map
    * @access public
    *
    */
    public $magnify;
    /**
    *
    * @var $smap String property to hold the fully qualified URL pointing
    * to the map file
    * @access public
    *
    */
    public $smap;

    /**
    *
    * Standard init method. This grabs the parameters from the querystring
    * and assigns default values if they are absent.
    *
    * @access public
    *
    */
    public function init()
    {
        //Create the configuration object
        $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->width = $this->getParam('width', '800');
        $this->height = $this->getParam('height', '600');
        $this->gLat = $this->getParam('glat', '-33.799669');
        $this->gLong = $this->getParam('glong', '18.364472');
        $this->magnify = $this->getParam('magnify', '6');
        $this->smap = $this->getParam('smap', $this->getDemoFile());
        //Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     *
    * A method to return the map Javascript as a string
    * It also caters for different file types. Currently SMAP
    * and KML.
    * @return string The map Javascript
    * @access public
    *
    */
    public function show()
    {

		$fileType = $this->getFileType();
		switch ($fileType) {
		    case "smap":
		    	$str = $this->insertMapLayer();
		    	$str .= $this->setupScript();
		    	$myMap = $this->getMapContents();
				return str_replace ("[[SMAP_INSERT_HERE]]", $myMap, $str);
		    	break;

		    case "kml":
		    	$str = $this->insertMapLayer();
		    	$str .= $this->setupKmlScript();
		    	$this->setVar('bodyParams', 'onLoad = "load()";');
		    	return $str;
		     	break;

		    default:
		    	return "<div class=\"error\">" . $this->objLanguage->languageText("mod_simplemap_error_novalidmapfile", "simplemap")
		    	  . ": " . $this->smap . "</div>";
		    	break;

		}


    }

    /**
    * Method to get the file type and see if its a valid file type
    * @return boolean TRUE|FALSE TRUE if valid file type FALSE if not
     */
    function getFileType()
    {
        $validFiles=array("smap", "kml");
		if (isset($this->smap) && $this->smap !== "") {
		    $ftArr = explode(".", strtolower($this->smap));
		    if (count($ftArr) > 0) {
			    $fileType = end($ftArr);
				if (in_array($fileType, $validFiles)) {
					return $fileType;
				}
		    }
		}
		//No valid file type found
		return NULL;
    }

    /**
    * Method to insert the javascript to process the KML
    * @return string The formatted javascript for the KML
    */
    function insertKml()
    {
    	$ret = "var kml = new GGeoXml(\"" . $this->smap . "\");\n"
    	  . "map.addOverlay(kml);";
        return $ret;
    }

    /**
    *
    * Method to return the google maps API key for the current site.
    * The API key is specific to site and directory, so if you change the
    * directory your Chisimba installation is working from, then you
    * need to obtain a new key.
    *
    * @access public
    * @return String The google API key
    *
    */
    public function getApiKey()
    {
		return $this->objConfig->getValue('mod_simplemap_apikey', 'simplemap');
    }

    /**
    *
    * Method to return the map DIV tag that is the place in the page
    * where the map apperas. It also set the height and width in pixels
    * from the height and width retrieved from the query string or
    * set in calling the class.
    *
    * @access public
    * @return string The DIV for the map
    *
    */
    public function insertMapLayer()
    {
        return "<div id=\"map\" style=\"width: " . $this->width
          . "px; height: " . $this->height . "px\"></div>";
    }

    /*
    *
    * Method to return the filename of the demo map
    *
    * @access public
    * @return string The filename of the demo file
    *
    */
    public function getDemoFile()
    {
    	// Create the configuration object
        $objRsConfig = $this->getObject('altconfig', 'config');
        //Set the file type and get the file into a string
		$this->fileType = "smap";
        $filename =  $objRsConfig->getModuleURI() . "simplemap/resources/jsmaps/madiba.smap";
        //Set the file type to large so that overlay control displays
        $this->mapControlType="large";
        return $filename;
    }

    /*
    *
    * Method to open the map file and read the contents into a string
    *
    * @access public
    * @return string The contents of the map file
    *
    */
    function getMapContents()
    {
    	$filename = $this->smap;
        $handle = fopen($filename, "r");
		$contents = stream_get_contents($handle);
		fclose($handle);
        return $contents;
    }

    /*
    *
    * Method to return the NOSCRIPT tags for inlusion for display when the
    * browser has Javascript disabled
    *
    * @access public
    * @return string The NOSCRIPT content.
    *
    */
    function getNoScript()
    {
    	return "<noscript>"
    	  . $this->objLanguage->languageText("mod_simplemap_noscript", "simplemap")
    	  .  "</noscript>\n\n";

    }

    /*
    *
    * Method to return the script for inclusion in the browser, including
    * the scripts read fromt the SMAP file.
    *
    * @access public
    * @return srting The Javascript for generating the map
    *
    */
    function setupScript()
    {
    	$ret = $this->getNoScript();
    	$lat = $this->gLat;
    	$long = $this->gLong;
    	$mag = $this->magnify;
    	$incompat = $this->objLanguage->languageText("mod_simplemap_incompatible", "simplemap");
		//Valid types are G_NORMAL_MAP, G_SATELLITE_MAP, G_HYBRID_MAP
		$gMapType = $this->getParam("maptype", NULL);
		if ($gMapType !== "" && $gMapType !==NULL) {
			$gMapType = $gMapType;
            //die($gMapType);
		} else {
		    $gMapType = "G_NORMAL_MAP";
		}
    	$ret .="
            <script type=\"text/javascript\">\n
    		//<![CDATA[\n
		    if (GBrowserIsCompatible()) {\n
		      // A function to create the marker and set up the event window\n
		      // Dont try to modify this function. It has to be here for the map to display\n
		      // Each instance of the function preserves the contents of a different instance\n
		      // of the \"marker\" and \"html\" variables which will be needed later when the event triggers. \n
		      function createMarker(point,html) {\n
		        var marker = new GMarker(point);\n
		        GEvent.addListener(marker, \"click\", function() {\n
		          marker.openInfoWindowHtml(html);\n
		        });\n
		        return marker;\n
		      }
		      // Display the map, with some controls and set the initial location
		      var map = new GMap2(document.getElementById(\"map\"));
		      {$this->getZoomControl()}
		      map.addControl(new GMapTypeControl());
		      map.setCenter(new GLatLng($lat,$long),$mag,$gMapType);

			  [[SMAP_INSERT_HERE]]

            }

		    // display a warning if the browser was not compatible
		    else {
		      alert(\"$incompat\");
		    }
		    //]]>
		    </script>";
        return $ret;
    }

        /*
    *
    * Method to return the script for inclusion in the browser for
    * displaying a KML file
    *
    * @access public
    * @return srting The Javascript for generating the map
    *
    */
    function setupKmlScript()
    {
    	$ret = $this->getNoScript();
    	$lat = $this->gLat;
    	$long = $this->gLong;
    	$mag = $this->magnify;
    	$incompat = $this->objLanguage->languageText("mod_simplemap_incompatible", "simplemap");
		//Valid types are G_NORMAL_MAP, G_SATELLITE_MAP, G_HYBRID_MAP
		$gMapType = $this->getParam("maptype", NULL);
		if ($gMapType !== "" && $gMapType !==NULL) {
			$gMapType = $gMapType;
        } else {
            $gMapType = "G_NORMAL_MAP";
        }
    	$ret .="
            <script type=\"text/javascript\">
    		//<![CDATA[
			// The GGeoXml constructor takes a URL pointing to a KML or GeoRSS file.
			// You add the GGeoXml object to the map as an overlay, and remove it as an overlay as well.
			// The Maps API determines implicitly whether the file is a KML or GeoRSS file.

			var map;
			var geoXml = new GGeoXml(\"$this->smap\");
			function load() {
  			    if (GBrowserIsCompatible()) {
				    map = new GMap2(document.getElementById(\"map\"));
				    //{$this->getZoomControl()}
	    		 	map.setCenter(new GLatLng($lat,$long),$mag,$gMapType);
				    map.addControl(new GLargeMapControl());
				    map.addOverlay(geoXml);
			    } else {
			        // display a warning if the browser was not compatible
					alert(\"$incompat\");
		        }
            }
		    //]]>
		    </script>";
        return $ret;
    }

	/**
	 *
	 * Method to create a map from an XML file with XML
	 * data corresponding to the pins on the map.
	 *
	 */
    function createSmapFromXml($fullFilePath)
    {
    	simplexml_load_file($fullFilePath);
    	$strRet = "";
        foreach($xml->pin as $pin) {
        	$strRet .= "var point = new GLatLng($pin->glat,$pin->glong);\n";
        	$strRet .= "var marker = createMarker($pin->markertype,'" . $pin->popup . "')\n";
        	$strRet .= " map.addOverlay($pin->overlay);\n\n";
        }
        return $strRet;
    }

    /**
    *
    * A method to control whether or not the large map zoom control shows
    * or no, or what type of control displays
    *
    */
    function getZoomControl()
    {
    	if (!isset($this->mapControlType)) {
            $this->mapControlType = '';
        }
        switch ($this->mapControlType) {
    		case "large":
    			$ret = "map.addControl(new GLargeMapControl());";
    			break;
    		default:
    			$ret = NULL;
                break;
    	}
    	return $ret;
    }
}
?>
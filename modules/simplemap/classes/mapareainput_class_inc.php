<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* This is a class to use the Google Maps API as an input tool. 
* Wherever the user clicks, it will return
* the latitude and longitude co-ordinates of that position,
* as well as the co-ordinates and zoom level of the current view.
*
* @author Tohir Solomons
* @category Chisimba
* @package simplemap
* @copyright AVOIR & UEC
* @licence GNU/GPL
*
*/
class mapareainput extends object 
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
    public $width='600px';
    /**
    * 
    * @var $height String property for holding the height of the map
    * @access public
    * 
    */
	public $height='300px';
    /**
    * 
    * @var $gLat String property for holding the default latitude X-coordinate of
    * the centre of the map
    * @access public
    * 
    */
    public $gLat;
    /**
    * 
    * @var $gLong String property for holding the default latitude Y-coordinate of
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
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
    }
    
    /**
     * 
    * A method to return the map Javascript as a string
    * @return string The map Javascript 
    * @access public
    * 
    */
    public function show()
    {
    	$this->setVar('pageSuppressXML', TRUE);
        $this->getGoogleMapJS();
        
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        
        $table->addCell($this->getMapLayer().$this->getMapScript());
        $table->addCell($this->getRightSideContent(), 100);

        $table->endRow();
        
        return $table->show();
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
    * Method to get the Right Hand Side Content - text boxes with inputs for latitude and longitude
    * @return string
    */
    private function getRightSideContent()
    {
        $latitude = new textinput('latitude');
        $latitude->cssId = 'latitude';
        $latitude->extra = ' readonly="readonly"';
        $latitudeLabel = new label ($this->objLanguage->languageText('word_latitude'), 'latitude');
        
        $longitude = new textinput('longitude');
        $longitude->cssId = 'longitude';
        $longitude->extra = ' readonly="readonly"';
        $longitudeLabel = new label ($this->objLanguage->languageText('word_longitude'), 'latitude');
        
        $clearLatLongButton = new button('clearlatlong', $this->objLanguage->languageText('mod_simplemap_fieldname_clearlatlong', 'simplemap', 'Clear Lat /Long'));
        $clearLatLongButton->setOnClick('clearLatLong();');
        
        $zoomlevel = new textinput('zoomlevel');
        $zoomlevel->cssId = 'zoomlevel';
        $zoomlevel->extra = ' readonly="readonly"';
        $zoomlevelLabel = new label ($this->objLanguage->languageText('mod_simplemap_fieldname_zoomlevel', 'simplemap', 'Zoom Level'), 'zoomlevel');
        
        $viewbounds = new textinput('viewbounds');
        $viewbounds->cssId = 'viewbounds';
        $viewbounds->extra = ' readonly="readonly"';
        $viewboundsLabel = new label ($this->objLanguage->languageText('mod_simplemap_fieldname_currentviewboundary', 'simplemap', 'Current View Boundary'), 'viewbounds');
        
        $currentcenter = new textinput('currentcenter');
        $currentcenter->cssId = 'currentcenter';
        $currentcenter->extra = ' readonly="readonly"';
        $currentcenterLabel = new label ($this->objLanguage->languageText('mod_simplemap_fieldname_currentcenter', 'simplemap', 'Current Center'), 'currentcenter');
        
        $heading = new htmlheading();
        $heading->str = $this->objLanguage->languageText('word_coordinates');
        $heading->type = 2;
        
        $right = $heading->show();
        
        $right .= '<p>'.$latitudeLabel->show().'<br />'.$latitude->show().'</p>';
        $right .= '<p>'.$longitudeLabel->show().'<br />'.$longitude->show().'</p>';
        $right .= '<p style="text-align:center">'.$clearLatLongButton->show().'</p>';
        $right .= '<p>'.$zoomlevelLabel->show().'<br />'.$zoomlevel->show().'</p>';
        $right .= '<p>'.$viewboundsLabel->show().'<br />'.$viewbounds->show().'</p>';
        $right .= '<p>'.$currentcenterLabel->show().'<br />'.$currentcenter->show().'</p>';
        
        return $right;
    }
    
    /**
    * Method to load the Google Map JavaScript into the Header
    */
    public function getGoogleMapJS()
    {
        $this->appendArrayVar('headerParams', '<script src="http://maps.google.com/maps?file=api&amp;v=1&amp;key='.$this->getApiKey().'" type="text/javascript"></script>');
    }
    
    /**
    * Method to get the Layer in which the map is to be displayed
    * Width and height is configurable through class properties
    * @return string
    */
    private function getMapLayer()
    {
        return '<div id="map" style="width: '.$this->width.'; height: '.$this->height.'"></div>';
    }
    
    /**
    * Method to Get the Script that replaces the layer with the Google Map
    * @return string
    * @todo Make default map configurable. Currently Set to Cape Town
    */
    private function getMapScript()
    {
        return '
<script type="text/javascript">
//<![CDATA[
    var map = new GMap(document.getElementById("map"));
    map.centerAndZoom(new GPoint(18.4954833984375, -33.95247360616281), 7);
    map.addControl(new GLargeMapControl());
    //map.setMapType(G_SATELLITE_MAP);
    map.addControl(new GMapTypeControl());

    GEvent.addListener(map, \'moveend\', function() {
            document.getElementById("viewbounds").value=map.getBounds();
            document.getElementById("currentcenter").value=map.getCenter();
            document.getElementById("zoomlevel").value=map.getZoomLevel();
    });


    // Recenter Map and add Coords by clicking the map
    GEvent.addListener(map, \'zoomend\', function() {
            document.getElementById("zoomlevel").value=map.getZoomLevel();
            document.getElementById("viewbounds").value=map.getBounds();
            document.getElementById("currentcenter").value=map.getCenter();
    });

    GEvent.addListener(map, \'click\', function(overlay, point) {

        map.clearOverlays();
        if (overlay) {
            map.removeOverlay(overlay);
        } else if (point) {
            var marker = new GMarker(point);
            map.addOverlay(marker);
        }
        document.getElementById("latitude").value=point.y;
        document.getElementById("longitude").value=point.x;
        document.getElementById("zoomlevel").value=map.getZoomLevel();
        document.getElementById("viewbounds").value=map.getBounds();
        document.getElementById("currentcenter").value=map.getCenter();
    });

    function clearLatLong()
    {
        map.clearOverlays();
        document.getElementById("latitude").value=\'\';
        document.getElementById("longitude").value=\'\';
    }


//]]>
</script>';
    }

}
?>
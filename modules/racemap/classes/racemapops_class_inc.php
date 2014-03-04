<?php
ini_set('memory_limit', -1);
/**
 *
 * racemap helper class
 *
 * PHP version 5.1.0+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   racemap
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * racemap helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package racemap
 *
 */
class racemapops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * @var string $objCurl String object property for holding the cURL object
     *
     * @access public
     */
    public $objCurl;
    

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        $this->objCurl       = $this->getObject('curl', 'utilities');
        $this->objCookie     = $this->getObject('cookie', 'utilities');
        $this->objDbRace     = $this->getObject('dbracemap');
        $this->objCC         = $this->getObject('displaylicense', 'creativecommons');
        
        // htmlelements
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
    }
    
 
    
    /**
     * Form used to invite friends to the site via mail invite
     *
     * @access public
     * @return string
     */
    public function showInviteForm() { 
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'qrcreator', 'Required').'</span>';
        $headerinv = new htmlheading();
        $headerinv->type = 1;
        $headerinv->str = $this->objLanguage->languageText('phrase_invitemate', 'userregistration').' '.$this->objConfig->getSitename();
        $ret = NULL;
        $ret .= $headerinv->show();
        // start the form
        $form = new form ('invite', $this->uri(array('action'=>'sendinvite'), 'userregistration'));
        // add some rules
        $form->addRule('friend_firstname', $this->objLanguage->languageText("mod_userregistration_needfriendname", "userregistration"), 'required');
        $form->addRule('friend_email', $this->objLanguage->languageText("mod_userregistration_needfriendemail", "userregistration"), 'email');
        // friend name
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $friendname = new textinput('friend_firstname');
        $friendnameLabel = new label($this->objLanguage->languageText('friendname', 'userregistration').'&nbsp;', 'input_friendname');
        $table->addCell($friendnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendname->show().$required);
        $table->endRow();
        // surname
        $table->startRow();
        $friendsurname = new textinput('friend_surname');
        $friendsurnameLabel = new label($this->objLanguage->languageText('friendsurname', 'userregistration').'&nbsp;', 'input_friendsurname');
        $table->addCell($friendsurnameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendsurname->show());
        $table->endRow();
        // email
        $table->startRow();
        $friendemail = new textinput('friend_email');
        $friendemailLabel = new label($this->objLanguage->languageText('friendemail', 'userregistration').'&nbsp;', 'input_friendemail');
        $table->addCell($friendemailLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($friendemail->show().$required);
        $table->endRow();
        // message to include to mate
        $defmsg = $this->objLanguage->languageText("mod_userregistration_wordhi", "userregistration").", <br /><br /> ".$this->objUser->fullname()." (".$this->objUser->username().") ".$this->objLanguage->languageText("mod_userregistration_hasinvited", "userregistration")." ".$this->objConfig->getSiteName()."! <br /><br /> ".$this->objLanguage->languageText("mod_userregistration_pleaseclick", "userregistration")."<br />";
        $table->startRow();
        $friendmsg = $this->newObject('htmlarea', 'htmlelements');
        $friendmsg->name = 'friend_msg';
        $friendmsg->value = $defmsg;
        $friendmsg->width ='50%';
        $friendmsgLabel = new label($this->objLanguage->languageText('friendmessage', 'userregistration').'&nbsp;', 'input_friendmsg');
        $table->addCell($friendmsgLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $friendmsg->toolbarSet = 'simple';
        $table->addCell($friendmsg->show());
        $table->endRow();
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("mod_userregistration_completeinvite", "userregistration"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();

        return $ret;
    }
    
    /**
     * Method to do an elevation profile across a bunch of points
     * 
     * @access public
     * @return header params
     */
     public function profileMap() {
         $gmapsrc = '<script src="http://maps.google.com/maps/api/js?sensor=false"></script><script type="text/javascript" src="http://www.google.com/jsapi"></script> ';
         $css = '<style type="text/css">
            #map {
                width: 100%;
                height: 350px;
                border: 1px solid black;
                background-color: white;
            }
        </style>';
        $prfmap = $this->getJavascriptFile('profilemap.js','racemap');
        
        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $css.$gmapsrc.$prfmap);
        $this->appendArrayVar('bodyOnLoad', "initialize();");
     }
     
     /**
      * Method to provide a map to digitise points along a line
      * 
      * @access public
      * @return string $ret return form
      */
     public function digiPtMap() {
        // Put the map together
        $olsrc = NULL;
        $css = NULL;
        $gmapsrc = NULL;
        $gmapsrc .= "<script src='http://maps.google.com/maps/api/js?sensor=false'>";
        $gmapsrc .= '</script><script type="text/javascript" src="http://www.google.com/jsapi"></script>';
        $olsrc .= $this->getJavascriptFile('lib/OpenLayers.js','georss');
        $css .= '<link rel="stylesheet" href="'.$this->getResourceUri('theme/default/style.css', 'georss').'" type="text/css" />';
        $css .= '<style type="text/css">
             #map {
                 width: 100%;
                 height: 500px;
                 border: 1px solid black;
                 background-color: white;
             }
             </style>';
        
        $gmapsrc .= $this->getJavascriptFile('digitisepoint.js','racemap');;
        
        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $olsrc.$css.$gmapsrc);
        $this->appendArrayVar('bodyOnLoad', "init();");
        
        // the form
        $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'qrcreator', 'Required').'</span>';
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('create_wapoints', 'racemap');
        $ret = NULL;
        $ret .= $header->show();
        // start the form
        $form = new form ('savepoints', $this->uri(array('action'=>'savepoints')));
        // add some rules
        //$form->addRule('friend_firstname', $this->objLanguage->languageText("mod_userregistration_needfriendname", "userregistration"), 'required');
        //$form->addRule('friend_email', $this->objLanguage->languageText("mod_userregistration_needfriendemail", "userregistration"), 'email');
        // map viewport
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $table->addCell('<div id="map"></div>');
        $table->endRow();
        
        // add in the press to digitise button
        $digi = new button('type', $this->objLanguage->languageText("save_waypoint", "racemap"), "toggleControl(this);"); //new radio('type'); // new button('type', $this->objLanguage->languageText("save_waypoint", "racemap"), "toggleControl(this);");
        //$digi->addOption('point','Digitise');
        $digi->setId("point");
        //$digi->addOption('none','Navigate');
        // $digi->setId("noneToggle");
        //$digi->extra = 'onClick="toggleControl(this);"';
        $table->startRow();
        $table->addCell($digi->show());
        $table->endRow();
        
        // geojson field
        //$table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $geojson = new textarea('geojson');
        $geojson->setId('geojson');
        $geojson->extra = "hidden";
        $table->addCell($geojson->show());
        $table->endRow();
        
        // fieldset and end off
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = '';
        $fieldset->contents = $table->show();
        // add the form to the fieldset
        $form->addToForm($fieldset->show());
        $button = new button ('submitform', $this->objLanguage->languageText("save_waypoints", "racemap"));
        $button->setToSubmit();
        $form->addToForm('<p align="center"><br />'.$button->show().'</p>');
        $ret .= $form->show();
        
        return $ret;
     }
     
     /**
      * Method to provide a map to digitise points along a line
      * 
      * @access public
      * @return string $ret return form
      */
     public function digiLineMap() {
        $olsrc = NULL;
        $css = NULL;
        $gmapsrc = NULL;
        
        $gmapsrc .= "<script src='http://maps.google.com/maps/api/js?sensor=false'>";
        $gmapsrc .= '</script><script type="text/javascript" src="http://www.google.com/jsapi"></script>';
        $olsrc .= $this->getJavascriptFile('lib/OpenLayers.js','georss');
        $css .= '<link rel="stylesheet" href="'.$this->getResourceUri('theme/default/style.css', 'georss').'" type="text/css" />';
        $css .= '<style type="text/css">
            #map {
                width: 100%;
                height: 500px;
                border: 1px solid black;
                background-color: white;
            }
            #controlToggle li {
                list-style: none;
            }
            #options {
                position: relative;
                width: 512px;
            }
            #output {
                float: right;
            }

            /* avoid pink tiles */
            .olImageLoadError {
                background-color: transparent !important;
            }
         </style>';
        
        $gmapsrc .= $this->getJavascriptFile('digitiseline.js','racemap');;
        
        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $olsrc.$css.$gmapsrc);
        $this->appendArrayVar('bodyOnLoad', "init();");
     }
     
     /**
      * Method to provide a map to display a full profile
      * 
      * @access public
      * @return string $ret header params
      */
     public function profileMapFull($metaid, $lat, $lon) {   
         $olsrc = NULL;
         $css = NULL;
         $cpath = $this->objConfig->getContentPath().'/graphs/';
         $gmapsrc = NULL;
         $gmapsrc .= "<script src='http://maps.google.com/maps/api/js?sensor=false'>";
         $gmapsrc .= '</script><script type="text/javascript" src="http://www.google.com/jsapi"></script>';
         $olsrc .= $this->getJavascriptFile('lib/OpenLayers.js','georss');
         $css .= '<link rel="stylesheet" href="'.$this->getResourceUri('theme/default/style.css', 'georss').'" type="text/css" />';
         $css = '<style type="text/css">
            #map {
                width: 100%;
                height: 350px;
                border: 1px solid black;
                background-color: white;
            }
        </style>';
        $olsrc .= '<script type="text/javascript">
        var lon = '.$lon.';
        var lat = '.$lat.';
        var zoom = 12;
        var map, layer;

        function init(){
            OpenLayers.Layer.WMS.prototype.getFullRequestString = function(newParams,altUrl)
                {
                    try{
                        var projectionCode=typeof this.options.projection == \'undefined\' ? this.map.getProjection() : this.options.projection;
                    }catch(err){
                        var projectionCode=this.map.getProjection();
                    }
                    this.params.SRS = projectionCode=="none" ? null : projectionCode;
                    return OpenLayers.Layer.Grid.prototype.getFullRequestString.apply(this,arguments);
                }
                
                var mapOptions = {
		            projection: new OpenLayers.Projection("EPSG:900913"),
		            displayProjection: new OpenLayers.Projection("EPSG:4326"),
		            units: "m",
		            numZoomLevels: 18,
		            maxResolution: 156543.0339,
		            maxExtent: new OpenLayers.Bounds(-20037508, -20037508, 20037508, 20037508.34),
		            controls: [new OpenLayers.Control.MouseDefaults()],
		            fallThrough: false
                };
                var defaultMapExtent = new OpenLayers.Bounds(-9024575.71780708, 4163697.68596957, -8966390.6897618, 4234743.17441856);
                
                map = new OpenLayers.Map(\'map\');
             
                // OpenStreetMap Base Layer
                osmarender = new OpenLayers.Layer.OSM(
	                "OpenStreetMap (Tiles@Home)",
	                "http://tah.openstreetmap.org/Tiles/tile/${z}/${x}/${y}.png"
                );
                
                var gphy = new OpenLayers.Layer.Google(
                    "Google Physical",
                    {type: google.maps.MapTypeId.TERRAIN}
                );
                var gmap = new OpenLayers.Layer.Google(
                    "Google Streets", // the default
                    {numZoomLevels: 20}
                );
                var ghyb = new OpenLayers.Layer.Google(
                    "Google Hybrid",
                    {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
                );
                var gsat = new OpenLayers.Layer.Google(
                    "Google Satellite",
                    {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
                );
                    
                map.addLayers([osmarender, gmap, gphy, ghyb, gsat]);
                map.addControl(new OpenLayers.Control.LayerSwitcher());
                map.addControl(new OpenLayers.Control.MousePosition());
                map.addControl( new OpenLayers.Control.PanZoomBar() );
            
               map.addLayer(new OpenLayers.Layer.GML("KML", "'.$cpath.$metaid.'.kml", 
               {
                format: OpenLayers.Format.KML, 
                formatOptions: {
                  extractStyles: true, 
                  extractAttributes: true,
                  maxDepth: 2,
                  projection: new OpenLayers.Projection("EPSG:4326"), 
                  displayProjection: new OpenLayers.Projection("EPSG:4326")
                }
               }));
            
            // map.setCenter(new OpenLayers.LonLat(lon,lat), zoom);
            map.setCenter(new OpenLayers.LonLat(lon, lat).transform(
                new OpenLayers.Projection("EPSG:4326"),
                map.getProjectionObject()
                ), zoom);
        }
         </script>';
              
         // add the lot to the headerparams...
         $this->appendArrayVar('headerParams', $olsrc.$css.$gmapsrc);
         $this->appendArrayVar('bodyOnLoad', "init();");     

     }
    
    /**
     * Method used to set geolocation coordinates
     *
     * Users are able to set geographic coordinates by either completing a text input or clicking on a map
     *
     * @param array $editparams
     * @param boolean $eventform
     * @return string
     */
    public function geoLocationForm($editparams = NULL, $eventform = FALSE) {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $ret = NULL;
        $lat = 0;
        $lon = 0;
        $zoom = 2;
        $currLocation = $this->objCookie->get('qrcreator_latlon');
        $currloc = explode("|", $currLocation);
        if(!empty($currloc) && isset($currloc[0]) && isset($currloc[1])) {
            $lat = $currloc[0];
            $lon = $currloc[1];
            $zoom = 10;
        } 
        if($this->objModules->checkIfRegistered('simplemap') && $this->objModules->checkIfRegistered('georss'))
        {
            $form = new form ('geoloc', $this->uri(array('action'=>'setlocation')));
            $this->loadClass('label', 'htmlelements');
            $this->objHead = $this->getObject('htmlheading', 'htmlelements');
            $this->objHead->type = 3;
            $this->objHead->str = $this->objLanguage->languageText("mod_qrcreator_geoposition", "qrcreator");
            $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
            $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
        }
    </style>';

            $google = "<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=".$gmapsapikey."' type=\"text/javascript\"></script>";
            $olsrc = $this->getJavascriptFile('lib/OpenLayers.js','georss');
            $js = "<script type=\"text/javascript\">
        var lon = 5;
        var lat = 40;
        var zoom = 17;
        var map, layer, drawControl, g;

        OpenLayers.ProxyHost = \"/proxy/?url=\";
        function init(){
            g = new OpenLayers.Format.GeoRSS();
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20, projection: new OpenLayers.Projection(\"EPSG:900913\"), displayProjection: new OpenLayers.Projection(\"EPSG:4326\") });
            var normal = new OpenLayers.Layer.Google( \"Google Map\" , {type: G_NORMAL_MAP, 'maxZoomLevel':18} );
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18} );
            
            map.addLayers([normal, hybrid]);

            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );

            map.setCenter(new OpenLayers.LonLat($lon,$lat), $zoom);

            map.events.register(\"click\", map, function(e) {
                var lonlat = map.getLonLatFromViewPortPx(e.xy);
                OpenLayers.Util.getElement(\"input_geotag\").value = lonlat.lat + \",  \" +
                                          + lonlat.lon
            });

        }
    </script>";

            // add the lot to the headerparams...
            $this->appendArrayVar('headerParams', $css.$google.$olsrc.$js);
            $this->appendArrayVar('bodyOnLoad', "init();");
            // add the table row with the map in it.
            $ptable = $this->newObject('htmltable', 'htmlelements');
            $ptable->cellpadding = 3;
            // a heading
            $ptable->startRow();
            //$ptable->addCell('');
            $ptable->addCell($this->objHead->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
            $ptable->endRow();
            // and now the map
            $ptable->startRow();
            $gtlabel = new label($this->objLanguage->languageText("mod_qrcreator_geoposition", "qrcreator") . ':', 'input_geotags');
            $gtags = '<div id="map"></div>';
            $geotags = new textinput('geotag', NULL, NULL, '100%');
            if (isset($editparams['geolat']) && isset($editparams['geolon'])) {
                $geotags->setValue($editparams['geolat'].", ".$editparams['geolon']);
            }
            //$ptable->addCell($gtlabel->show());
            $ptable->addCell($gtags.$geotags->show());
            $ptable->endRow();

            $fieldset = $this->newObject('fieldset', 'htmlelements');
            $fieldset->legend = '';
            $fieldset->contents = $ptable->show();
            $button = new button ('submitform', $this->objLanguage->languageText("mod_qrcreator_setlocation", "qrcreator"));
            $button->setToSubmit();
            $form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
            $ret .= $form->show();
        }
        else {
            $ret .= "Map cannot be shown";
        }

        return $ret;
    }
    
    /**
     * Sign in block
     *
     * Used in conjunction with the welcome block as a alertbox link. The sign in simply displays the block to sign in to Chisimba
     *
     * @return string
     */
    public function showSignInBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        return $objBlocks->showBlock('login', 'security', 'none');
    }

    /**
     * Sign up block
     *
     * Method to generate a sign up (register) block for the module. It uses a linked alertbox to format the response
     *
     * @return string
     */
    public function showSignUpBox() {
        $objBlocks = $this->getObject('blocks', 'blocks');
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        return $objFeatureBox->show($this->objLanguage->languageText("mod_events_signup", "events"), $objBlocks->showBlock('register', 'security', 'none'));
    }
    
    /**
     * Method to parse and insert a GPX file from a Nokia Sports Tracker device to the database
     *
     * @param $file file to parse
     * @return void
     */
    public function gpxParserNST($file) {
        $userid = $this->objUser->userId();
        $gpx = simplexml_load_file($file);
        $metadata = $gpx->metadata; 
        $mname = $metadata->name;
        $mdesc = $metadata->desc;
        $mauth = $metadata->author->name;
        $mtime = $metadata->time;

        // insert metadata to a meta table for ref
        $metains = array('userid' => $userid, 'name' => $mname, 'description' => $mdesc, 'author' => $mauth, 'creationtime' => strtotime($mtime));
        $metaid = $this->objDbRace->insertMetaRecord($metains);

        $track = $gpx->trk;
        $tname = $track->name;
        $tseg = $track->trkseg;
        $tpoints = $tseg->trkpt;

        foreach( $tpoints as $tpoi ) {
            foreach($tpoi->attributes() as $attr => $value) {
                $latlon[$attr] = $value;
            }
            $ele = $tpoi->ele;
            $speed = $tpoi->speed;
            $course = $tpoi->course;
            $desc = $tpoi->desc;
            $time = $tpoi->time;
            $segname = $tpoi->name;
    
            // insert to db as array
            $insarr = array('userid' => $userid, 'metaid' => $metaid, 'lat' => $latlon['lat'], 'lon' => $latlon['lon'], 
                            'elevation' => $ele, 'speed' => $speed, 'course' => $course, 
                            'description' => $desc, 'creationtime' => strtotime($time), 'segname' => $segname);
            $this->objDbRace->insertTrkPoints($insarr);
        }
        
        return TRUE;
    }
    
    /**
     * Method to parse and insert a GPX file from a Android MyTracks device to the database
     *
     * @param $file file to parse
     * @return void
     */
    public function gpxParserAMT($file) {
        $userid = $this->objUser->userId();
        $gpx = simplexml_load_file($file);
        $metadata = $gpx->trk; 
        $mname = $metadata->name;
        $mdesc = $metadata->desc;
        $mauth = $metadata->number;
        $mtime = time();

        // insert metadata to a meta table for ref
        $metains = array('userid' => $userid, 'name' => $mname, 'description' => $mdesc, 'author' => $this->objUser->userName(), 'creationtime' => $mtime);
        $metaid = $this->objDbRace->insertMetaRecord($metains);
        
        $track = $gpx->trk;
        // $tname = $track->name;
        $tseg = $track->trkseg;
        foreach($tseg as $seg) {
            $tpoints = $seg->trkpt;

            foreach( $tpoints as $tpoi ) {
                foreach($tpoi->attributes() as $attr => $value) {
                    $latlon[$attr] = $value;
                }
                $ele = $tpoi->ele;
                $time = $tpoi->time;
                // insert to db as array
                $insarr = array('userid' => $userid, 'metaid' => $metaid, 'lat' => $latlon['lat'], 'lon' => $latlon['lon'], 
                                'elevation' => $ele, 'speed' => 0, 'course' => 0, 
                                'description' => 'none', 'creationtime' => strtotime($time), 'segname' => 'none');
                $this->objDbRace->insertTrkPoints($insarr);
            }
        }
        
        return TRUE;
    }
    
    /**
     * Method to parse and insert a GPX file from a generic GPX capable device to the database
     *
     * @param $file file to parse
     * @return void
     */
    public function gpxParseMeta($file) {
        $userid = $this->objUser->userId();
        // set up all the vars first
        $mname = NULL;
        $mdesc = NULL;
        $mauth = NULL;
        $mcopy = NULL;
        $mlink = NULL;
        $mtime = NULL;
        $mkey  = NULL;
        $mbounds = NULL;
        $mextensions = NULL;
        
        $gpx = simplexml_load_file($file);
        
        // check for metadata
        if(!empty($gpx->metadata)) {
            $metadata = $gpx->metadata; 
        
            if(!empty($metadata->name)) {
                $mname = $metadata->name;
            }
            if(!empty($metadata->desc)) {
                $mdesc = $metadata->desc;
            }
            if(!empty($metadata->author->name)) {
                $mauth = $metadata->author->name;
            }
            if(!empty($metadata->copyright)) {
                $mcopy = $metadata->copyright;
            }
            if(!empty($metadata->link)) {
                $mlink = $metadata->link;
            }
            if(!empty($metadata->time)) {
                $mtime = $metadata->time;
            }
            if(!empty($metadata->keywords)) {
                $mkey = $metadata->keywords;
            }
            //foreach($metadata->bounds->attributes() as $attr => $value) {
            //        $mbounds .= $attr."=".$value.",";
            //}
            if(!empty($metadata->extensions)) {
                $mextensions = $metadata->extensions;
            }
        }
        else {
            // check for metadata in non-metadata tags :/
            if(!empty($gpx->name)) {
                $mname = $gpx->name;
            }
            if(!empty($gpx->desc)) {
                $mdesc = $gpx->desc;
            }
            if(!empty($gpx->author->name)) {
                $mauth = $gpx->author->name;
            }
            if(!empty($gpx->copyright)) {
                $mcopy = $gpx->copyright;
            }
            if(!empty($gpx->link)) {
                $mlink = $gpx->link;
            }
            if(!empty($gpx->time)) {
                $mtime = $gpx->time;
            }
            if(!empty($gpx->keywords)) {
                $mkey = $gpx->keywords;
            }
            //foreach($gpx->bounds->attributes() as $attr => $value) {
            //    $mbounds .= $attr."=".$value.",";
            
            //}
            if(!empty($gpx->extensions)) {
                $mextensions = $gpx->extensions;
            }
            
        }
        
        // insert metadata to a meta table for ref
        $metains = array('userid' => $userid, 'name' => $mname, 'description' => $mdesc, 'author' => $mauth, 
                         'copyright' => $mcopy, 'link' => $mlink, 'creationtime' => $mtime, 'keywords' => $mkey, 
                         'bounds' => $mbounds, 'extensions' => $mextensions);
        
        //var_dump($metains); //die();
        $metaid = $this->objDbRace->insertMetaRecord($metains);
        $this->gpxParseTrk($file, $metaid);
        return $metaid;
    }
    
    /**
     * Method to parse and insert a GPX file from a generic device to the database
     *
     * @param $file file to parse
     * @return void
     */
    public function gpxParseTrk($file, $metaid) {
        $userid = $this->objUser->userId();
        $gpx = simplexml_load_file($file);
        if(isset($gpx->trk)) {
            $track = $gpx->trk;
            $tname = $track->name;
            $tseg = $track->trkseg;
            foreach($tseg as $seg) {
                $tpoints = $seg->trkpt;
                
                foreach( $tpoints as $tpoi ) {
                    foreach($tpoi->attributes() as $attr => $value) {
                        $latlon[$attr] = $value;
                    }
                    $ele = $tpoi->ele;
                    $speed = $tpoi->speed;
                    $course = $tpoi->course;
                    $desc = $tpoi->desc;
                    $time = $tpoi->time;
                    $segname = $tpoi->name;
    
                    // insert to db as array
                    $insarr = array('userid' => $userid, 'metaid' => $metaid, 'lat' => $latlon['lat'], 'lon' => $latlon['lon'], 
                                    'elevation' => $ele, 'speed' => $speed, 'course' => $course, 
                                    'description' => $desc, 'creationtime' => strtotime($time), 'segname' => $segname);
                    $this->objDbRace->insertTrkPoints($insarr);
                }
            }    
        }
        elseif(isset($gpx->wpt)) {
            $track = $gpx->wpt;
            foreach($track->attributes() as $attr => $value) {
                    $latlon[$attr] = $value;
            }
            $segname = $track->name;
            $desc = $track->desc;
            $tsym  = $track->sym;
            $ele  = $track->ele;
            
            $speed = $track->speed;
            $course = $track->course;
            $time = $track->time;
    
            // insert to db as array
            $insarr = array('userid' => $userid, 'metaid' => $metaid, 'lat' => $latlon['lat'], 'lon' => $latlon['lon'], 
                            'elevation' => $ele, 'speed' => $speed, 'course' => $course, 
                            'description' => $desc, 'creationtime' => strtotime($time), 'segname' => $segname);
            $this->objDbRace->insertTrkPoints($insarr);

            return TRUE;
        }
    }
    
    /**
     * Method to draw a full map profile
     *
     * @param $metaid - metadata id
     * @param $width - width in px
     * @param $height - height in px
     * @param $title - title of graph
     * @param $subtitle - subtitle of graph
     * @param $xtitle - x axis title
     * @param $ytitle - y axis title
     * @return $file - filename of graph
     */
    public function drawProfile($metaid, $width = 550, $height = 350, $title = 'Untitled', $subtitle = '(subtitle)', $xtitle = 'time in seconds', $ytitle = 'altitude') {
        $stop = $this->objDbRace->countPoints($metaid);
        $ptids = NULL;
        $x = 0;
        $ptsarr = $this->objDbRace->getPoints($metaid, $x, $stop);
        foreach($ptsarr as $pts) {
            if(!isset($pts['elevation'])) {
                    $pts['elevation'] = 0;
            }
            $ptids[] = $pts['elevation'];
                
        }
        $this->objGraphOps = $this->getObject('graphops', 'jpgraph');
        $graph = $this->objGraphOps->linePlot($ptids, $width, $height, $title, $subtitle, $xtitle, $ytitle);
        $file = $this->objGraphOps->drawGraph($graph, $metaid.'_ele.png');
        
        return $file;
    }
    
    /**
     * Method to draw a full speed profile
     *
     * @param $metaid - metadata id
     * @param $width - width in px
     * @param $height - height in px
     * @param $title - title of graph
     * @param $subtitle - subtitle of graph
     * @param $xtitle - x axis title
     * @param $ytitle - y axis title
     * @return $file - filename of graph
     */
    public function drawSpeed($metaid, $width = 550, $height = 350, $title = 'Untitled', $subtitle = '(subtitle)', $xtitle = 'time in seconds', $ytitle = 'speed') {
        $stop = $this->objDbRace->countPoints($metaid);
        $ptids = NULL;
        $x = 0;
        $ptsarr = $this->objDbRace->getPoints($metaid, $x, $stop);
        foreach($ptsarr as $pts) {
            if(!isset($pts['speed'])) {
                $pts['speed'] = 0;
            }
            $speed[] = $pts['speed'];
        }
        $this->objGraphOps = $this->getObject('graphops', 'jpgraph');
        $graph = $this->objGraphOps->linePlot($speed, $width, $height, $title, $subtitle, $xtitle, $ytitle);
        $file = $this->objGraphOps->drawGraph($graph, $metaid.'_speed.png');
        
        return $file;
    }
    
    /**
     * Method to output a simple menu
     * 
     * @access public
     * @return string
     */
    public function showMenu() {
        $menu = NULL;
        $this->loadClass('href', 'htmlelements');
        $objFeaturebox = $this->newObject('featurebox', 'navigation');
        $menu .= '<ul>';
        $menu .= '<li>';
        $uploadlink = new href($this->uri(array('action' => 'uploaddatafile'), 'racemap'), $this->objLanguage->languageText('mod_racemap_uploadgpx', 'racemap'), NULL);
        $menu .= $uploadlink->show();
        $menu .= '</li>';
        $menu .= '</ul>';
        $menu = $objFeaturebox->showContent($this->objLanguage->languageText('mod_racemap_menuheader', 'racemap'), $menu);
        return $menu;
    }
    
    /**
     * Method to output an upload form
     * 
     * @access public
     * @return string
     */ 
    public function uploadForm() {
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
        $objSelectFile->name = 'file';
        $objSelectFile->restrictFileList = array('gpx', 'tcx');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $form = new form ('upload', $this->uri(array('action'=>'processfile'), 'racemap'));
        
        $this->loadClass('label', 'htmlelements');
        $this->objHead = $this->getObject('htmlheading', 'htmlelements');
        $this->objHead->type = 3;
        $this->objHead->str = $this->objLanguage->languageText("mod_racemap_uploadfile", "racemap");
        
        $uptable = $this->newObject('htmltable', 'htmlelements');
        $uptable->cellpadding = 3;
        // a heading
        $uptable->startRow();
        //$uptable->addCell('');
        $uptable->addCell($this->objHead->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
        $uptable->endRow();
        
        // the file uploader thing
        $fileLabel = new label($this->objLanguage->languageText('mod_racemap_file', 'racemap').'&nbsp;', 'input_file');
        $uptable->startRow();
        $uptable->addCell($fileLabel->show());
        $uptable->addCell($objSelectFile->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
        $uptable->endRow();
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = '';
        $fieldset->contents = $uptable->show();
        $button = new button ('submitform', $this->objLanguage->languageText("mod_racemap_uploadfile", "racemap"));
        $button->setToSubmit();
        $form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
        return $form->show();
    }
    
    /**
     * Method to output a form to edit the metadata
     * 
     * @access public
     * @param array $metainfo metadata info
     * @return string
     */
    public function editMeta($metainfo) {
        // form for editing metadata on gps tracks
        $metainfo = $metainfo[0];
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('htmlarea', 'htmlelements');
        $form = new form ('updatemeta', $this->uri(array('action' => 'updatemeta', 'id' => $metainfo['id']), 'racemap'));
        
        $this->loadClass('label', 'htmlelements');
        $this->objHead = $this->getObject('htmlheading', 'htmlelements');
        $this->objHead->type = 3;
        $this->objHead->str = $this->objLanguage->languageText("mod_racemap_updatemeta", "racemap");
        
        $mtable = $this->newObject('htmltable', 'htmlelements');
        $mtable->cellpadding = 3;
        // a heading
        $mtable->startRow();
        //$mtable->addCell('');
        $mtable->addCell($this->objHead->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
        $mtable->endRow();
        
        // name
        $nlabel = new label($this->objLanguage->languageText('mod_racemap_trkname', 'racemap') . ':', 'input_trkname');
        $name = new textinput('trkname');
        $name->size = 60;
        if (isset($metainfo['name'])) {
            $name->setValue(stripslashes($metainfo['name']));
        }
        $mtable->startRow();
        $mtable->addCell($nlabel->show());
        $mtable->addCell($name->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
        $mtable->endRow();
        
        // description
        $dlabel = new label($this->objLanguage->languageText('mod_racemap_description', 'racemap') . ':', 'input_description');
        $d = $this->newObject('htmlarea', 'htmlelements');
        $d->setName('description');
        $d->height = 400;
        $d->width = '100%';
        $d->setDefaultToolbarSet();
        if (isset($metainfo['description'])) {
            $d->setcontent((stripslashes(($metainfo['description']))));
        }
        $mtable->startRow();
        $mtable->addCell($dlabel->show());
        $mtable->addCell($d->show());
        $mtable->endRow();
        
        // copyright
        $lic = $this->getObject('licensechooser', 'creativecommons');
        $mtable->startRow();
        $pcclabel = new label($this->objLanguage->languageText('mod_racemap_cclic', 'racemap') . ':', 'input_cclic');
        $mtable->addCell($pcclabel->show());
        if (isset($metainfo['copyright'])) {
            $lic->defaultValue = $metainfo['copyright'];
        }
        $mtable->addCell($lic->show());
        $mtable->endRow();
        
        // keywords
        $klabel = new label($this->objLanguage->languageText('mod_racemap_keywords', 'racemap') . ':', 'input_keywords');
        $keys = new textinput('keywords');
        $keys->size = 60;
        if (isset($metainfo['keywords'])) {
            $keys->setValue(stripslashes($metainfo['keywords']));
        }
        $mtable->startRow();
        $mtable->addCell($klabel->show());
        $mtable->addCell($keys->show()); // , '100%', $valign="top", 'center', null, 'colspan=2','0');
        $mtable->endRow();
        
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->legend = '';
        $fieldset->contents = $mtable->show();
        $button = new button ('submitform', $this->objLanguage->languageText("mod_racemap_updateinfo", "racemap"));
        $button->setToSubmit();
        $form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
        
        return $form->show();
    }
    
}
?>

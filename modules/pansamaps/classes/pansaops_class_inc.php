<?php
/**
 *
 * General ops class for the PANSA Maps module
 *
 * PHP version 5
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
 * @package   pansamaps
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
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
 * Ops class for PANSA Maps
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 * @package pansamaps
 *
 */
class pansaops extends object {

    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    public $objSysConfig;

    /**
     *
     * Constructor
     *
     * @access public
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
    }
    
    
    /**
	 * Method to render a search form
	 */
	public function searchBox() {
	    $css = '<style type="text/css">  
            html, body {
                font: 100%/1.5 Arial; 
            }
        </style>';
        $this->appendArrayVar('headerParams', $css);
        $this->loadClass('textinput', 'htmlelements');
        $qseekform = new form('qseek', $this->uri(array(
            'action' => 'searchvenues',
        )));
        $qseekform->addRule('keyword', $this->objLanguage->languageText("mod_pansamaps_phrase_searchtermreq", "pansamaps") , 'required');
        $qseekterm = new textinput('keyword');
        $qseekterm->size = 15;
        $qseekform->addToForm($qseekterm->show());
        $this->objsTButton = &new button($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setValue($this->objLanguage->languageText('word_search', 'system'));
        $this->objsTButton->setToSubmit();
        $qseekform->addToForm($this->objsTButton->show());
        $qseekform = $qseekform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_pansamaps_qseek", "pansamaps") , $this->objLanguage->languageText("mod_pansamaps_qseekinstructions", "pansamaps") . "<br />" . $qseekform);

        return $ret;
    }
    
    /**
	 * Method to render an input form
	 */
	public function inputForm($editparams = NULL) {
	    $this->loadClass('label', 'htmlelements');
	    $this->loadClass('textarea','htmlelements');
	    $ret = NULL;
        $lat = 0;
        $lon = 0;
        $zoom = 2;
        $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        $css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: white;
        }
        html, body {
            font: 100%/1.5 Arial;
        
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
        
	    $required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'pansamaps', 'Required').'</span>';
        $this->loadClass('textinput', 'htmlelements');
        $iform = new form('iform', $this->uri(array(
            'action' => 'adddata',
        )));
        if (isset($editparams)) {
            $iform = new form('iform', $this->uri(array(
                'action' => 'updatedata', 'id' => $editparams['id'],
            )));
        }
        
        $iform->addRule('venuename', $this->objLanguage->languageText("mod_pansamaps_phrase_vnamereq", "pansamaps") , 'required');
        $iform->addRule('city', $this->objLanguage->languageText("mod_pansamaps_phrase_vcityreq", "pansamaps") , 'required');
        $iform->addRule('geotag', $this->objLanguage->languageText("mod_pansamaps_phrase_georeq", "pansamaps") , 'required');
        $iform->addRule('venueaddress1', $this->objLanguage->languageText("mod_pansamaps_phrase_vaddreq", "pansamaps") , 'required');
        //$iform->addRule('phonecode', $this->objLanguage->languageText("mod_pansamaps_phrase_numeric", "pansamaps") , 'numeric');
        //$iform->addRule('faxcode', $this->objLanguage->languageText("mod_pansamaps_phrase_numeric", "pansamaps") , 'numeric');
        //$iform->addRule('phone', $this->objLanguage->languageText("mod_pansamaps_phrase_numeric", "pansamaps") , 'numeric');
        //$iform->addRule('fax', $this->objLanguage->languageText("mod_pansamaps_phrase_numeric", "pansamaps") , 'numeric');
        $table = $this->newObject('htmltable', 'htmlelements');
        $mtable = $this->newObject('htmltable', 'htmlelements');
        
        // and now the map
        $mtable->startRow();
        $gtlabel = new label($this->objLanguage->languageText("mod_pansamaps_geoposition", "pansamaps") . ':', 'input_geotags');
        $gtags = '<div id="map"></div>';
        $geotags = new textinput('geotag', NULL, NULL, '100%');
        if (isset($editparams['geolat']) && isset($editparams['geolon'])) {
            $geotags->setValue($editparams['geolat'].", ".$editparams['geolon']);
        }
        //$ptable->addCell($gtlabel->show());
        $mtable->addCell($gtags.$geotags->show());
        $mtable->endRow();
        
        $table->startRow();
        $venuename = new textinput('venuename');
        $venuename->size = 15;
        if (isset($editparams['venuename'])) {
            $venuename->setValue($editparams['venuename']);
        }
        $venuenameLabel = new label($this->objLanguage->languageText('venuename', 'pansamaps').'&nbsp;', 'input_venuename');
        $table->addCell($venuenameLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($venuename->show().$required);
        $table->endRow();
        
        $table->startRow();
        $venueaddress1 = new textinput('venueaddress1');
        $venueaddress1->size = 15;
        if (isset($editparams['venueaddress1'])) {
            $venueaddress1->setValue($editparams['venueaddress1']);
        }
        $venueaddress1Label = new label($this->objLanguage->languageText('venueaddress1', 'pansamaps').'&nbsp;', 'input_venueaddress1');
        $table->addCell($venueaddress1Label->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($venueaddress1->show().$required);
        $table->endRow();
        
        $table->startRow();
        $venueaddress2 = new textinput('venueaddress2');
        $venueaddress2->size = 15;
        if (isset($editparams['venueaddress2'])) {
            $venueaddress2->setValue($editparams['venueaddress2']);
        }
        $venueaddress2Label = new label($this->objLanguage->languageText('venueaddress2', 'pansamaps').'&nbsp;', 'input_venueaddress2');
        $table->addCell($venueaddress2Label->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($venueaddress2->show());
        $table->endRow();
        
        $table->startRow();
        $city = new textinput('city');
        $city->size = 15;
        if (isset($editparams['city'])) {
            $city->setValue($editparams['city']);
        }
        $cityLabel = new label($this->objLanguage->languageText('city', 'pansamaps').'&nbsp;', 'input_city');
        $table->addCell($cityLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($city->show().$required);
        $table->endRow();
        
        $table->startRow();
        $zip = new textinput('zip');
        $zip->size = 15;
        if (isset($editparams['zip'])) {
            $zip->setValue($editparams['zip']);
        }
        $zipLabel = new label($this->objLanguage->languageText('zip', 'pansamaps').'&nbsp;', 'input_zip');
        $table->addCell($zipLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($zip->show());
        $table->endRow();
        
        $table->startRow();
        $phonecode = new textinput('phonecode');
        $phonecode->size = 3;
        $phonecode->extra = 'maxlength = "3"';
        $phone = new textinput('phone');
        $phone->size = 15;
        if (isset($editparams['phonecode'])) {
            $phonecode->setValue($editparams['phonecode']);
        }
        if (isset($editparams['phone'])) {
            $phone->setValue($editparams['phone']);
        }
        $phonecodeLabel = new label($this->objLanguage->languageText('phone', 'pansamaps').'&nbsp;', 'input_phonecode');
        $table->addCell($phonecodeLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell("(".$phonecode->show().") ".$phone->show());
        $table->endRow();
        
        $table->startRow();
        $faxcode = new textinput('faxcode');
        $faxcode->size = 3;
        $faxcode->extra = 'maxlength = "3"';
        $fax = new textinput('fax');
        $fax->size = 15;
        if (isset($editparams['faxcode'])) {
            $faxcode->setValue($editparams['faxcode']);
        }
        if (isset($editparams['fax'])) {
            $fax->setValue($editparams['fax']);
        }
        $faxcodeLabel = new label($this->objLanguage->languageText('fax', 'pansamaps').'&nbsp;', 'input_faxcode');
        $table->addCell($faxcodeLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell("(".$faxcode->show().") ".$fax->show());
        $table->endRow();
        
        $table->startRow();
        $email = new textinput('email');
        $email->size = 15;
        if (isset($editparams['email'])) {
            $email->setValue($editparams['email']);
        }
        $emailLabel = new label($this->objLanguage->languageText('emailaddr', 'pansamaps').'&nbsp;', 'input_email');
        $table->addCell($emailLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($email->show());
        $table->endRow();
        
        $table->startRow();
        $url = new textinput('url');
        $url->size = 15;
        if (isset($editparams['url'])) {
            $url->setValue($editparams['url']);
        }
        $urlLabel = new label($this->objLanguage->languageText('url', 'pansamaps').'&nbsp;', 'input_url');
        $table->addCell($urlLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell("http://".$url->show());
        $table->endRow();
        
        $table->startRow();
        $contactperson = new textinput('contactperson');
        $contactperson->size = 15;
        if (isset($editparams['contactperson'])) {
            $contactperson->setValue($editparams['contactperson']);
        }
        $contactpersonLabel = new label($this->objLanguage->languageText('contactperson', 'pansamaps').'&nbsp;', 'input_contactperson');
        $table->addCell($contactpersonLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($contactperson->show());
        $table->endRow();
        
        $table->startRow();
        $otherinfo = new textinput('otherinfo');
        $otherinfo->size = 15;
        if (isset($editparams['otherinfo'])) {
            $otherinfo->setValue($editparams['otherinfo']);
        }
        $otherinfoLabel = new label($this->objLanguage->languageText('otherinfo', 'pansamaps').'&nbsp;', 'input_otherinfo');
        $table->addCell($otherinfoLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($otherinfo->show());
        $table->endRow();
        
        $table->startRow();
        $venuedescription = new textarea('venuedescription');
        $venuedescription->size = 15;
        if (isset($editparams['venuedescription'])) {
            $venuedescription->setValue($editparams['venuedescription']);
        }
        $venuedescriptionLabel = new label($this->objLanguage->languageText('venuedescription', 'pansamaps').'&nbsp;', 'input_venuedescription');
        $table->addCell($venuedescriptionLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($venuedescription->show());
        $table->endRow();
        
        /*$table->startRow();
        $geolat = new textinput('geolat');
        $geolat->size = 15;
        $geolatLabel = new label($this->objLanguage->languageText('geolat', 'pansamaps').'&nbsp;', 'input_geolat');
        $table->addCell($geolatLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($geolat->show());
        $table->endRow();
        
        $table->startRow();
        $geolon = new textinput('geolon');
        $geolon->size = 15;
        $geolonLabel = new label($this->objLanguage->languageText('geolon', 'pansamaps').'&nbsp;', 'input_geolon');
        $table->addCell($geolonLabel->show(), 150, NULL, 'right');
        $table->addCell('&nbsp;', 5);
        $table->addCell($geolon->show());
        $table->endRow();*/
        
        $t = $mtable->show().$table->show();
        $iform->addToForm($t);
        if (isset($editparams)) {
            $this->objsTButton = new button($this->objLanguage->languageText('word_update', 'system'));
            $this->objsTButton->setValue($this->objLanguage->languageText('word_update', 'system'));
        }
        else {
            $this->objsTButton = new button($this->objLanguage->languageText('word_add', 'system'));
            $this->objsTButton->setValue($this->objLanguage->languageText('word_add', 'system'));
        }
        $this->objsTButton->setToSubmit();
        $iform->addToForm($this->objsTButton->show());
        $iform = $iform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->show($this->objLanguage->languageText("mod_pansamaps_inputvenue", "pansamaps") , $this->objLanguage->languageText("mod_pansamaps_inputinstructions", "pansamaps") . "<br />" . $iform);

        return $ret;
    }
    
    
    public function viewLocMap($lat, $lon, $zoom = 15) {
        $gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        $css = '<link href="http://www.google.com/apis/maps/base.css" rel="stylesheet" type="text/css"></link>';
        $css .= '<style type="text/css">  
            html, body {
                font: 100%/1.5 Arial; 
            }
        </style>';
        $this->appendArrayVar('headerParams', $css);
        $google = "<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=$gmapsapikey\"
            type=\"text/javascript\"></script>
    <script type=\"text/javascript\">
    //<![CDATA[
    
    function createMarker(point,html) {
        var marker = new GMarker(point);
        GEvent.addListener(marker, \"click\", function() {
          marker.openInfoWindowHtml(html);
        });
        return marker;
      }

    function refresh(){
          var map = new GMap2(document.getElementById(\"map\"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng(-30, 25), 6);

          GDownloadUrl(\"http://pansamaps.chisimba.com/index.php?module=pansamaps&action=getmapdata\", function(data, responseCode) {
          // To ensure against HTTP errors that result in null or bad data,
          // always check status code is equal to 200 before processing the data
          if(responseCode == 200) {
            var xml = GXml.parse(data);
            var markers = xml.documentElement.getElementsByTagName(\"marker\");
            for (var i = 0; i < markers.length; i++) {
              var info = markers[i].getAttribute(\"info\");
              var point = new GLatLng(parseFloat(markers[i].getAttribute(\"lat\")), parseFloat(markers[i].getAttribute(\"lng\")));
              var marker = createMarker(point, info)
              map.addOverlay(marker);
            }
            
          } else if(responseCode == -1) {
            alert(\"Data request timed out. Please try later.\");
          } else { 
            alert(\"Request resulted in error. Check XML file is retrievable.\");
          }
        });
          window.setTimeout(\"refresh()\", 30000);

    }

    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById(\"map\"));
        map.addControl(new GSmallMapControl());
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng(-30, 25), 6);
        GDownloadUrl(\"http://pansamaps.chisimba.com/index.php?module=pansamaps&action=getmapdata\", function(data, responseCode) {
          // To ensure against HTTP errors that result in null or bad data,
          // always check status code is equal to 200 before processing the data
          if(responseCode == 200) {
            var xml = GXml.parse(data);
            var markers = xml.documentElement.getElementsByTagName(\"marker\");
            for (var i = 0; i < markers.length; i++) {
              var info = markers[i].getAttribute(\"info\");
              var point = new GLatLng(parseFloat(markers[i].getAttribute(\"lat\")), parseFloat(markers[i].getAttribute(\"lng\")));
              var marker = createMarker(point, info)
              map.addOverlay(marker);
              map.addOverlay(marker);
	    }
          } else if(responseCode == -1) {
	    alert(\"Data request timed out. Please try later.\");
          } else { 
            alert(\"Request resulted in error. Check XML file is retrievable.\");
          }
        });

        // window.setTimeout(\"refresh()\", 30000);

      }
    }

    //]]>
    </script>";
    
        // add the lot to the headerparams...
        $this->appendArrayVar('headerParams', $css.$google);
        $this->appendArrayVar('bodyOnLoad', "load();");
        
        $gtags = '<div id="map" style="width: 768px; height: 768px"></div>';
        return $gtags;
    }
    
    public function emailNotify($dataArray) {
        $res = $dataArray;
        $bodyText = NULL;
        $bodyText .= $this->objLanguage->languageText("mod_pansamaps_newnotification", "pansamaps");
        $bodyText .= "<br /><br />";
        // create a table to show the details nicely
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $table->addCell($res['venuename']);
        $table->addCell($res['city']);
        $table->addCell($res['phonecode']." ".$res['phone']);
        $table->addCell($res['contactperson']);
        $table->addCell($res['venuedescription']);
        $table->endRow();
        $bodyText .= $table->show();
    
    
        $addys = $this->objSysConfig->getValue('emailnotify', 'pansamaps');
        $addys = explode(',', $addys);
        foreach($addys as $emailadd) {
            $objMailer = $this->newObject('email', 'mail');
            $objMailer->clearAddresses();
            
            $objMailer->setValue('IsHTML', TRUE);
            $objMailer->setValue('to', $emailadd);
            $objMailer->setValue('from', 'noreply@pansa.org');
            $objMailer->setValue('fromName', $this->objLanguage->languageText("mod_pansamaps_fromname", "pansamaps"));
            $objMailer->setValue('subject', $this->objLanguage->languageText("mod_blog_emailsub", "pansamaps"));
            $objMailer->setValue('body', $bodyText);
            $objMailer->send(TRUE);
            
        }
    }
    
    private function widgetize($action, $width, $height, $scroll){
        $widget = '<iframe style="border: medium none ; overflow: hidden; width: '.$width.'px; height: '.$height.'px; font: 100%/1.5 Arial;" 
                  src="'.$this->uri()."index.php?module=pansamaps&action=".$action.'" id="widgetframe" frameborder="0" scrolling="'.$scroll.'">
                  </iframe>';
        
        return $widget;
    }
    
}
?>

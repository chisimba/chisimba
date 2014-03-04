<?php

/**
 * Geo Helper Class
 *
 * Convenience class for interacting with MongoDB
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
 * @package   geo
 * @author    Paul Scott <pscott209@gmail.com>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mongoops_class_inc.php 19535 2010-10-28 18:22:39Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://www.mongodb.org/
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check

/**
 * Geo Helper Class
 *
 * Convenience class for interacting with MongoDB.
 *
 * @category  Chisimba
 * @package   geo
 * @author    Paul Scott <pscott209@gmail.com>
 * @copyright 2011 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: mongoops_class_inc.php 19535 2010-10-28 18:22:39Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @seealso   http://www.mongodb.org/
 */
class geoops extends object
{

	/**
	 * Instance of the dbsysconfig class of the sysconfig module.
	 *
	 * @access private
	 * @var    object
	 */
	private $objSysConfig;

	private $geowikiBase;

	private $flickrBase;

	/*
	 * Initialises some of the object's properties.
	 *
	 * @access public
	 */
	public function init()
	{
		// Objects
		$this->objSysConfig    = $this->getObject('dbsysconfig', 'sysconfig');
		$this->geowikiBase     = "http://api.wikilocation.org/articles?";
		$this->flickrBase      = "http://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=db8df9a1b93963aed7a5fdea50c718b0&license=cc+by+sa&accuracy=16&";
		$this->objProxy        = $this->getObject('proxyparser', 'utilities');
		$this->objLanguage     = $this->getObject ( 'language', 'language' );
		$this->objConfig       = $this->getObject('altconfig', 'config');
		$this->loadClass('form', 'htmlelements');
		$objSelectFile         = $this->newObject('selectfile', 'filemanager');
		$this->objUser         = $this->getObject('user', 'security');
		$this->objMongo        = $this->getObject('geomongo', 'mongo');
		$this->objCookie        = $this->getObject('cookie', 'utilities');
	}

	public function getWikipedia($lon, $lat, $radius=1500) {
		$url = $this->geowikiBase."lat=".$lat."&lng=".$lon."&radius=".$radius;
		$proxyArr = $this->objProxy->getProxy();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
			curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
		}
		$articlesjson = curl_exec($ch);
		curl_close($ch);

		return json_decode($articlesjson);
	}

	public function getFlickr($lon, $lat, $fradius=0.5) {
		$url = $this->flickrBase."lat=".$lat."&lon=".$lon."&radius=".$fradius."&radius_units=km&format=json&nojsoncallback=1";
		$proxyArr = $this->objProxy->getProxy();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
			curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
		}
		$flickrjson = curl_exec($ch);
		curl_close($ch);

		return json_decode($flickrjson);
	}

	public function picUploadForm() {
		$ret = NULL;
		$objSelectFile->restrictFileList = array('jpg', 'jpeg', 'png', 'gif');
		$objSelectFile->name = 'pic';
		$form = new form ('uploadpic', $this->uri(array('action'=>'uploadpic'), 'events'));
		$form->addToForm($objSelectFile->show());
		$button = new button ('submitform', $this->objLanguage->languageText("mod_userregistration_uploadpic", "userregistration"));
		$button->setToSubmit();
		$form->addToForm('<p align="center"><br />'.$button->show().'</p>');
		$ret .= $form->show();
		return $ret;
	}

	public function geoLocationForm($editparams = NULL, $eventform = FALSE) {
		$this->objModules = $this->getObject('modules', 'modulecatalogue');
		$ret = NULL;
		$lat = 0;
		$lon = 0;
		$zoom = 2;
		$currLocation = $this->objCookie->get('geo_latlon');
		$currloc = explode("|", $currLocation);
		if(!empty($currloc) && isset($currloc[0]) && isset($currloc[1])) {
			$lat = $currloc[0];
			$lon = $currloc[1];
			$zoom = 10;
		}
		if($this->objModules->checkIfRegistered('simplemap') && $this->objModules->checkIfRegistered('georss'))
		{
			$form = new form ('geoloc', $this->uri(array('action'=>'addplacedetails')));
			$this->loadClass('label', 'htmlelements');
			$this->objHead = $this->getObject('htmlheading', 'htmlelements');
			$this->objHead->type = 3;
			$this->objHead->str = $this->objLanguage->languageText("mod_geo_addplace", "geo");
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
                OpenLayers.Util.getElement(\"input_geotag\").value = lonlat.lat + \",  \" + lonlat.lon;
                OpenLayers.Util.getElement(\"input_lat\").value = lonlat.lat; 
                OpenLayers.Util.getElement(\"input_lon\").value = lonlat.lon; 
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
			$gtlabel = new label($this->objLanguage->languageText("mod_geo_geoposition", "geo") . ':', 'input_geotags');
			$gtags = '<div id="map"></div>';
			$geotags = new textinput('geotag', NULL, NULL, '100%');
			if (isset($editparams['geolat']) && isset($editparams['geolon'])) {
				$geotags->setValue($editparams['geolat'].", ".$editparams['geolon']);
			}
			//$ptable->addCell($gtlabel->show());
			$ptable->addCell($gtags.$geotags->show());
			$ptable->endRow();
			$patable = $this->newObject('htmltable', 'htmlelements');
			$patable->cellpadding = 3;
			// place name
			$patable->startRow();
			$namelabel = new label($this->objLanguage->languageText("mod_geo_placename", "geo") . ':', 'input_name');
			$name = new textinput('name', NULL, NULL, '100%');
			$patable->addCell($namelabel->show());
			$patable->addCell($name->show());
			$patable->endRow();
			// longitude
			$patable->startRow();
			$latlabel = new label($this->objLanguage->languageText("mod_geo_latitude", "geo") . ':', 'input_lat');
			$lat = new textinput('lat', NULL, NULL, '100%');
			$patable->addCell($latlabel->show());
			$patable->addCell($lat->show());
			$patable->endRow();
			// latitude
			$patable->startRow();
			$lonlabel = new label($this->objLanguage->languageText("mod_geo_longitude", "geo") . ':', 'input_lon');
			$lon = new textinput('lon', NULL, NULL, '100%');
			$patable->addCell($lonlabel->show());
			$patable->addCell($lon->show());
			$patable->endRow();
			// type
			$patable->startRow();
			$typelabel = new label($this->objLanguage->languageText("mod_geo_type", "geo") . ':', 'input_type');
			$type = new dropdown('type', NULL, NULL, '100%');
			$types = $this->objMongo->getDistinct("type");
			$types = $types['values'];
			foreach($types as $ptype) {
				if($ptype == "null" || $ptype == "undefined" || $ptype == "") {
					continue;
				}
				else {
			        $type->addOption($ptype, ucwords($ptype));
				}
			}
			$patable->addCell($typelabel->show());
			$patable->addCell($type->show());
			$patable->endRow();
			// alternate names
			$patable->startRow();
			$altlabel = new label($this->objLanguage->languageText("mod_geo_altnames", "geo") . ':', 'input_altnames');
			$alt = new textinput('altnames', NULL, NULL, '100%');
			$patable->addCell($altlabel->show());
			$patable->addCell($alt->show());
			$patable->endRow();

			$fieldset = $this->newObject('fieldset', 'htmlelements');
			$fieldset->legend = '';
			$fieldset->contents = $ptable->show()."<br />".$patable->show();
			$button = new button ('submitform', $this->objLanguage->languageText("mod_geo_addplace", "geo"));
			$button->setToSubmit();
			$form->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
			$ret .= $form->show();
		}
		else {
			$ret .= "Map cannot be shown";
		}

		return $ret;
	}

	public function getHTML5Loc() {
		$form = new form ('geoloc', $this->uri(array('action'=>'setlocation')));
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('textinput', 'htmlelements');
		$url = $this->uri(array('action' => 'setloc'), 'geo');
		$url = str_replace('&amp;', '&', $url);
		$js = '<script type="text/javascript">
               if (navigator.geolocation) {
                   navigator.geolocation.getCurrentPosition(function(position) {  
                   var url="'.$url.'&lat=" + position.coords.latitude + "&lon=" + position.coords.longitude;
                   document.location.href = url;
                   });
               }
               </script>';
		// add the lot to the headerparams...
		$this->appendArrayVar('headerParams', $js);
		return '<div id="pos"></div>';
	}

	public function makeMapMarkers($dataObj, $lat, $lon) {
		// build up a set of markers for a google map
		$head = "<markers>";
		$body = NULL;
		foreach($dataObj as $data) {
			if($data->latitude == "" || $data->longitude == "") {
				continue;
			}
			else {
				$body .= '<marker lat="'.$data->latitude.'" lng="'.$data->longitude.'" info="'.htmlspecialchars($data->name).'" />';
			}
		}
		$body .= '<marker lat="'.$lat.'" lng="'.$lon.'" info="'.htmlentities("You are here!").'" />';
		$tail = "</markers>";
		$data = $head.$body.$tail;
		$filename = microtime(TRUE)."markers.xml";
		$path = $this->objConfig->getModulePath()."geo/".$filename;
		if(!file_exists($path)) {
			touch($path);
			chmod($path, 0777);
		}
		else {
			unlink($path);
			touch($path);
			chmod($path, 0777);
		}
		file_put_contents($path, $data);

		return $filename;
	}

	public function makeGMap($lat, $lon, $path, $zoom = 14) {
		$gmapsapikey = $this->objSysConfig->getValue('mod_simplemap_apikey', 'simplemap');
		$uri = $this->uri('');
		//$css = '<link href="http://www.google.com/apis/maps/base.css" rel="stylesheet" type="text/css"></link>';
		$css = '<style type="text/css">
            html, body {
                font: 100%/1.5 Arial; 
            }
        </style>';
		$this->appendArrayVar('headerParams', $css);
		$google = "<script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=$gmapsapikey\"
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

    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById(\"map\"));
        map.addControl(new GLargeMapControl3D());
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng($lat, $lon), $zoom);
        map.setUIToDefault();
        GDownloadUrl(\"packages/geo/$path\", function(data, responseCode) {
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

	public function placeSearchForm() {
		$sform = new form ('placesearch', $this->uri(array('action'=>'placesearch')));
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('textinput', 'htmlelements');
		$pstable = $this->newObject('htmltable', 'htmlelements');
		$pstable->cellpadding = 3;
		$pstable->startRow();
		$llabel = new label($this->objLanguage->languageText("mod_geo_limit", "geo") . ':', 'input_limit');
		$limit = new textinput('limit', NULL, NULL, '10%');
		$limit->setValue(10);
		$slabel = new label($this->objLanguage->languageText("mod_geo_placename", "geo") . ':', 'input_place');
		$place = new textinput('placename', NULL, NULL, '50%');
		$pstable->addCell($slabel->show());
		$pstable->addCell($place->show());
		$pstable->addCell($llabel->show());
		$pstable->addCell($limit->show());
		$pstable->endRow();

		$fieldset = $this->newObject('fieldset', 'htmlelements');
		$fieldset->legend = $this->objLanguage->languageText("mod_geo_placesearch", "geo");
		$fieldset->contents = $pstable->show();
		$button = new button ('submitform', $this->objLanguage->languageText("mod_geo_searchplaces", "geo"));
		$button->setToSubmit();
		$sform->addToForm($fieldset->show().'<p align="center"><br />'.$button->show().'</p>');
		return $sform->show();
	}



	/**
	 * Welcome block
	 *
	 * Block to display welcome messages when logged in, or a sign in link if not
	 *
	 * @return string
	 */
	public function showWelcomeBox() {
		$objFeaturebox = $this->getObject('featurebox', 'navigation');
		$linklist = NULL;
		if($this->objUser->isLoggedIn() == FALSE) {
			$signinlink = $this->newObject('alertbox', 'htmlelements');
			$signuplink = $this->newObject('alertbox', 'htmlelements');
			$registerlink = $this->newObject('alertbox', 'htmlelements');
			// Make sure to show the sign up link only if registrations are allowed!
			if(strtolower($this->objConfig->getallowSelfRegister()) == 'true') {
				$signuplink = $signuplink->show($this->objLanguage->languageText("mod_geo_signup", "geo"), $this->uri(array('action' => 'showregister'), 'userregistration'));
			}
			else {
				$signuplink = NULL;
			}
			$signinlink1 = $signinlink->show($this->objLanguage->languageText("mod_geo_signin", "geo"), $this->uri(array('action' => 'showsignin')));
			$signinlink1 .= " ".$this->objLanguage->languageText("mod_geo_toaddplaces", "geo").", ";
			$linklist .= $signinlink1;
			$linklist .= $this->objLanguage->languageText("mod_geo_orifyoudonthaveacc", "geo").", ".$signuplink;
		}
		else {
			//user is logged in
			$invitelink = $this->newObject('alertbox', 'htmlelements');
			$invitelink = $invitelink->show($this->objLanguage->languageText("mod_geo_invitefriends", "geo"), $this->uri(array('action' => 'invitefriend')));

			$linklist .= $invitelink;
		}
		// location link is always visible
		$changeloclink = $this->newObject('link', 'htmlelements');
		$changeloclink->href = $this->uri(array('action' => 'changelocation'));
		$changeloclink->link = $this->objLanguage->languageText("mod_geo_changeloc", "geo");

		// add location link is always visible
		$addloclink = $this->newObject('link', 'htmlelements');
		$addloclink->href = $this->uri(array('action' => 'addplace'));
		$addloclink->link = $this->objLanguage->languageText("mod_geo_addplace", "geo");


		$linklist .= "<br /><ul>";
		$linklist .= "<li>".$changeloclink->show()."</li>";
		if($this->objUser->isLoggedIn()) {
		    $linklist .= "<li>".$addloclink->show()."</li>";
		}
		else {
			$signinlink2 = $signinlink->show($this->objLanguage->languageText("mod_geo_signin", "geo"), $this->uri(array('action' => 'showsignin')));
			$linklist .= "<li>".$signinlink2." ".$this->objLanguage->languageText("mod_geo_toadd", "geo")."</li>";
		}
		$linklist .= "</ul>";
		$linklist .= "<br />".$this->objLanguage->languageText("mod_geo_numrecs", "geo").": ".number_format($this->objMongo->getRecordCount());
		return $objFeaturebox->show($this->objLanguage->languageText("mod_geo_welcome", "geo"),$linklist);
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
		$objFeatureBox = $this->getObject('featurebox', 'navigation');
		return $objFeatureBox->show($this->objLanguage->languageText("mod_geo_signin", "geo"), $objBlocks->showBlock('login', 'security', 'none'));
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
		return $objFeatureBox->show($this->objLanguage->languageText("mod_geo_signup", "geo"), $objBlocks->showBlock('register', 'security', 'none'));
	}

	public function postgisKludge() {
		$dbconn = pg_connect("host=localhost dbname=gis user=postgres password=postgres")
		or die('Could not connect: ' . pg_last_error());

		// Performing SQL query
		$query = 'select *,point(the_geom) from south_africa_location';
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			// var_dump($line);
			$line['point'] = str_replace("(", "", $line['point']);
			$line['point'] = str_replace(")", "", $line['point']);
			$pt = explode(",", $line['point']);
			$lon = floatval($pt[0]);
			$lat = floatval($pt[1]);
			$insarr = array('name' => $line['NAME'], 'latitude' => $lat, 'longitude' => $lon, 'type' => $line['PLACE'], 'alternatenames' => "");
			
			$this->objMongo->upsertRecord($insarr, "noupdate");
		}

		// Free resultset
		pg_free_result($result);
		
		// Performing SQL query
		$query = 'select *,point(the_geom) from south_africa_poi';
		$result = pg_query($query) or die('Query failed: ' . pg_last_error());

		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			// var_dump($line);
			$line['point'] = str_replace("(", "", $line['point']);
			$line['point'] = str_replace(")", "", $line['point']);
			$pt = explode(",", $line['point']);
			$lon = floatval($pt[0]);
			$lat = floatval($pt[1]);
			$types = explode(":", $line['NAME']);
			
			if(count($types) == 2) {
				$name = $types[1];
				$subtype = $types[0];
			}
			else {
				$name = $types[0];
				$subtype = "";
			}
			$insarr = array('name' => $name, 'latitude' => $lat, 'longitude' => $lon, 'type' => $line['CATEGORY'], 'subtype' => $subtype, 'alternatenames' => "");
			$this->objMongo->upsertRecord($insarr, "noupdate");
		}

		// Free resultset
		pg_free_result($result);

		// Closing connection
		pg_close($dbconn);
		
		return;
	}
}
?>
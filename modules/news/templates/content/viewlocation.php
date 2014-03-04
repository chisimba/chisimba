<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_news_viewlocation', 'news', 'View Location').': '.$location['location'];
echo $header->show();

$latLong = str_replace('(', '', $location['latlongcenter']);
$latLong = str_replace(')', '', $latLong);
$latLong = explode(',', $latLong);

$latitude = trim($latLong[0]);
$longitude = trim($latLong[1]);


$objBuildMap = $this->getObject('simplebuildmap', 'simplemap');
$objBuildMap->gLat = $location['latitude'];
$objBuildMap->gLong = $location['longitude'];
$objBuildMap->magnify = $location['zoomlevel'];

//echo $objBuildMap->show();

$this->setVar('pageSuppressXML', TRUE);

$headerScripts = '
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAS23_q9_7KmHQLx7xfrAE_BT2yXp_ZAY8_ufC3CFXhHIE1NvwkxTiEOAf1SGLFhbVHleOuAmH2KeaYQ"
      type="text/javascript"></script>
    <script type="text/javascript">

    //<![CDATA[

    function load() {
      if (GBrowserIsCompatible()) {
        var map = new GMap(document.getElementById("map"));
        map.setCenter(new GLatLng('.$latitude.', '.$longitude.'), '.(17-$location['zoomlevel']).');
		map.addControl(new GMapTypeControl());
		map.addControl(new GLargeMapControl());
      }
    }
//]]>
    </script>
';

$this->appendArrayVar('headerParams', $headerScripts);
$this->setVar('bodyParams', 'onload="load()" onunload="GUnload()"');

?>
<div id="map" style="width: 600px; height: 400px"></div>
<?php

		// geoTagging map part
        // only show this is simplemap module is installed - we need the gmaps api key stored there
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        if($this->objModules->checkIfRegistered('simplemap') && $this->objModules->checkIfRegistered('georss'))
        {
        	$this->objHead = $this->getObject('htmlheading', 'htmlelements');
        	$this->objHead->type = 3;
        	$this->objHead->str = $this->objLanguage->languageText("mod_blog_geotagposts", "blog");
        	$gmapsapikey = $this->sysConfig->getValue('mod_simplemap_apikey', 'simplemap');
        	$css = '<style type="text/css">
        #map {
            width: 100%;
            height: 350px;
            border: 1px solid black;
            background-color: grey;
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
            map = new OpenLayers.Map( 'map' , { controls: [] , 'numZoomLevels':20 });
            var hybrid = new OpenLayers.Layer.Google( \"Google Hybrid Map\" , {type: G_HYBRID_MAP, 'maxZoomLevel':18} );
            var wmsLayer = new OpenLayers.Layer.WMS( \"Public WMS\", 
                \"http://labs.metacarta.com/wms/vmap0?\", {layers: 'basic'}); 
            
            map.addLayers([wmsLayer, hybrid]);
      
            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl( new OpenLayers.Control.MouseDefaults() );
            map.addControl( new OpenLayers.Control.LayerSwitcher() );
            map.addControl( new OpenLayers.Control.PanZoomBar() );
            
            map.setCenter(new OpenLayers.LonLat(0,0), 2);
            
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
       } 

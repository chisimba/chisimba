
        var map, drawControl, g;
        
        function serialize(feature) {
            feature.attributes = {};
            
            var title = prompt("Name for feature?");
            feature.attributes['title'] = title;
            
            var description = prompt("Describe this place");
            feature.attributes['description'] = description;
            
            var data = g.write(feature.layer.features);
            OpenLayers.Util.getElement("geojson").value = data // write the data to a textarea or something (called geojson)...
            toggleControl("nontoggle");
        }
        
        function init(){
            
            OpenLayers.Layer.WMS.prototype.getFullRequestString = function(newParams,altUrl)
            {
                try{
                    var projectionCode=typeof this.options.projection == 'undefined' ? this.map.getProjection() : this.options.projection;
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
        
            g = new OpenLayers.Format.GeoJSON();
            map = new OpenLayers.Map('map');
            
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

            var pointLayer = new OpenLayers.Layer.Vector("Point Layer");
            pointLayer.onFeatureInsert = serialize;

            map.addLayers([gmap, gphy, ghyb, gsat, osmarender, pointLayer]);
            map.addControl(new OpenLayers.Control.LayerSwitcher());
            map.addControl(new OpenLayers.Control.MousePosition());
            
            drawControls = {
                point: new OpenLayers.Control.DrawFeature(pointLayer,
                            OpenLayers.Handler.Point)
            };
            
            for(var key in drawControls) {
                map.addControl(drawControls[key]);
            }
            
            map.setCenter(new OpenLayers.LonLat(0, 0), 3);
        }

        function toggleControl(element) {
            for(key in drawControls) {
                var control = drawControls[key];
                //alert(key);
                //alert(element.id);
                
                if(element.id == key) { //&& element.checked) {
                    control.activate();
                } else {
                    control.deactivate();
                }
            }
        }



            var map, measureControls;
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
                    
                map.addLayers([gmap, gphy, ghyb, gsat, osmarender]);
                map.addControl(new OpenLayers.Control.LayerSwitcher());
                map.addControl(new OpenLayers.Control.MousePosition());
            
                // style the sketch fancy
                var sketchSymbolizers = {
                    "Point": {
                        pointRadius: 4,
                        graphicName: "square",
                        fillColor: "white",
                        fillOpacity: 1,
                        strokeWidth: 1,
                        strokeOpacity: 1,
                        strokeColor: "#333333"
                    },
                    "Line": {
                        strokeWidth: 3,
                        strokeOpacity: 1,
                        strokeColor: "#666666",
                        strokeDashstyle: "dash"
                    },
                    "Polygon": {
                        strokeWidth: 2,
                        strokeOpacity: 1,
                        strokeColor: "#666666",
                        fillColor: "white",
                        fillOpacity: 0.3
                    }
                };
                var style = new OpenLayers.Style();
                style.addRules([
                     new OpenLayers.Rule({symbolizer: sketchSymbolizers})
                ]);
                var styleMap = new OpenLayers.StyleMap({"default": style});
            
                measureControls = {
                    line: new OpenLayers.Control.Measure(
                        OpenLayers.Handler.Path, {
                            persist: true,
                            handlerOptions: {
                                layerOptions: {styleMap: styleMap}
                            }
                        }
                    ),
                
                    polygon: new OpenLayers.Control.Measure(
                        OpenLayers.Handler.Polygon, {
                            persist: true,
                            handlerOptions: {
                                layerOptions: {styleMap: styleMap}
                            }
                        }
                    )
                };
            
                var control;
                for(var key in measureControls) {
                    control = measureControls[key];
                    control.events.on({
                        "measure": handleMeasurements,
                        "measurepartial": handleMeasurements
                    });
                    map.addControl(control);
                }
            
                map.setCenter(new OpenLayers.LonLat(0, 0), 3);
            
                document.getElementById('noneToggle').checked = true;
            }
             
            function handleMeasurements(event) {
                var geometry = event.geometry;
	            var units = event.units;
	            var order = event.order;
	            var measure = event.measure;
	            var element = document.getElementById('output');
	            var out = "";
	            if(order == 1) {
		            out += "Measure: " + measure.toFixed(3) + " " + units;
	            } else {
		            out += "Measure: " + measure.toFixed(3) + " " + units + "<sup>2</" + "sup>";
	            }
                // alert(geometry);
                //alert(map.getProjection());
                element.innerHTML = out+" "+geometry;
            }

            function toggleControl(element) {
                for(key in measureControls) {
                    var control = measureControls[key];
                    if(element.value == key && element.checked) {
                        control.activate();
                    } else {
                        control.deactivate();
                    }
                }
            }
        
            function toggleGeodesic(element) {
                for(key in measureControls) {
                    var control = measureControls[key];
                    control.geodesic = element.checked;
                }
            }


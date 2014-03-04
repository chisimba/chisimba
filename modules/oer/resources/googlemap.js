//Useful links:
// http://code.google.com/apis/maps/documentation/javascript/reference.html#Marker
// http://code.google.com/apis/maps/documentation/javascript/services.html#Geocoding
// http://jqueryui.com/demos/autocomplete/#remote-with-cache
      
     
var geocoder;
var map;
var marker;
    
function initialize(loclat,loclong){
  
    var latlng = new google.maps.LatLng(loclat,loclong);
    
    var options = {
        zoom: 16,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
       
    map = new google.maps.Map(document.getElementById("map_canvas"), options);
       
    //GEOCODER
    geocoder = new google.maps.Geocoder();
        
    marker = new google.maps.Marker({
        map: map,
        draggable: true
    });
  

  
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        alert('geolocation not supported. Please enter address manually');
    }

    function success(position) {
 
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
       
   
        map.setCenter(new google.maps.LatLng(position.coords.latitude,position.coords.longitude));
        var location = new google.maps.LatLng(lat,lng);
   
        marker.setPosition(location);
  
        geocoder.geocode({
            'latLng': marker.getPosition()
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    jQuery('#address').val(results[0].formatted_address);
                    jQuery('#input_loclat').val(marker.getPosition().lat());
                    jQuery('#input_loclong').val(marker.getPosition().lng());
                }
            }
        });

  
    }

    function error(msg) {
        alert('error: ' + msg);
    }
 
				
}
		
jQuery(document).ready(function() { 
       
    initialize(43.5,2.3);
				  
    jQuery(function() {
        jQuery("#address").autocomplete({
            //This bit uses the geocoder to fetch address values
            source: function(request, response) {
                geocoder.geocode( {
                    'address': request.term
                }, function(results, status) {
                    response(jQuery.map(results, function(item) {
                        return {
                            label:  item.formatted_address,
                            value: item.formatted_address,
                            latitude: item.geometry.location.lat(),
                            longitude: item.geometry.location.lng()
                        }
                    }));
                })
            },
            //This bit is executed upon selection of an address
            select: function(event, ui) {
                jQuery("#input_loclat").val(ui.item.latitude);
                jQuery("#input_loclong").val(ui.item.longitude);
                var location = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
                marker.setPosition(location);
                map.setCenter(location);
            }
        });
    });
	
    //Add listener to marker for reverse geocoding
    google.maps.event.addListener(marker, 'drag', function() {
        geocoder.geocode({
            'latLng': marker.getPosition()
        }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    jQuery('#address').val(results[0].formatted_address);
                    jQuery('#input_loclat').val(marker.getPosition().lat());
                    jQuery('#input_loclong').val(marker.getPosition().lng());
                }
            }
        });
    });
  
});
  
  

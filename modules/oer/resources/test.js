var marker = new Array();
jQuery(document).ready(function(){ 
    myLatlng = [           
    new google.maps.LatLng(-26.19298,28.03152  ),           
    new google.maps.LatLng(-33.9248685,18.424055299999964  ),           
    new google.maps.LatLng(0.5701440999999999,34.55966539999997  ),        
    ];
    title = [ "The department of computer science", "The Java User Group", "C++ group",
    ];

    var myOptions = {
        zoom: 0,
        center: new google.maps.LatLng(0, 0),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    var oldAction = document.forms["maps"].action;

    for(i=0;i<myLatlng.length;i++)
    {
        marker[i] = new google.maps.Marker(
        {
            position: myLatlng[i],
            title: title[i]

        } );

        var pos = marker[i].getPosition();
        google.maps.event.addListener(marker[i], "click",
            (function(pos)
            {
                return function()

                {
                    //alert(i);
                    document.forms["maps"].action = oldAction + "&lat=" + pos.lat() + "&Lng=" + pos.lng();
                    document.forms["maps"].submit();
                };
            }
            )(pos)
            );

        marker[i].setMap(map);

    }


});
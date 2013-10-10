<?php

/**
 * contains utility methods for generating the maps
 *
 * @author davidwaf
 */
class mapfactory extends object {

    function init() {
        
        $this->appendArrayVar('headerParams', '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>');
        $this->appendArrayVar('headerParams', '<script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAA-O3c-Om9OcvXMOJXreXHAxQGj0PqsCtxKvarsoS-iqLdqZSKfxS27kJqGZajBjvuzOBLizi931BUow"></script>');
    }

    /**
     * generate a map showing group locations as they adapted products
     * @return type 
     */
    function getBrowseByMap() {
        $dbGroups = $this->getObject("dbgroups", "oer");
        $dbContext = $this->getObject("dbcontext", "context");
        
        $coords = $dbGroups->getGroupsThatHaveAdapatations();
        $titles = $dbGroups->getGroupsThatHaveAdapatations();
        $str='';
        $str .= ' <script type="text/javascript">
                    var marker = new Array();
                    jQuery(document).ready(function(){ 
                        myLatlng = [';

        foreach ($coords as $coord) {
            $str.='           new google.maps.LatLng(';
            $str.= $coord['loclat'] . ',' . $coord['loclong'];
            $str.='  ),';
        }

        $str.='        ];
        title = [';
        foreach ($titles as $title) {

            $context = $dbContext->getContext($title['contextcode']);
            $str.=' "';
            $str.=$context['title'];
            $str.='",';
        }
        $str.='
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
            { position: myLatlng[i],
                title: title[i]

            } );

            var pos = marker[i].getPosition();
            google.maps.event.addListener(marker[i], "click",
            (function(pos)
            { return function()
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

                </script>';

        $str.='  <br/>';
        $str.='  <div id="map_canvas" style="width:190; height:110"></div>';

        $form = new form('maps', $this->uri(array("action" => 'viewadaptationbymap')));

        $str.=' '. $form->show();
        return $str;
    }

}


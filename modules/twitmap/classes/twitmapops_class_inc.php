<?php
// security check - must be included in all scripts
if (! $GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}

/**
* Display class for tutorials module
* @author Paul Scott <pscott@uwc.ac.za>
*/

class twitmapops extends object
{
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    public function grabXML()
    {
        // grab the latest crap off twitter and save locally
        // load up the cURL class to get around any proxy issues
        $objCurl = $this->getObject('curl', 'utilities');
        // Some housekeeping
        $cacheDir = $this->objConfig->getContentBasePath().'twitmap/';
        if(!file_exists($cacheDir))
        {
             mkdir($cacheDir, 0777);
        }
        // Check if there is a cahce file, and get from last_id else get new
        if(file_exists($cacheDir."twitcache.xml"))
        {
            // parse the file and get the last id to get the deltas rather.
            $data = simplexml_load_file($cacheDir."twitcache.xml");
            $lastid = $data->status->id;
            // Now do the call to the twitter api to get the updates
            $result = $objCurl->exec('http://twitter.com/statuses/public_timeline.xml?last_id='.$lastid);
         }
         else {
             $result = $objCurl->exec('http://twitter.com/statuses/public_timeline.xml');
             $filename = "twitcache.xml";
             // write down the cache file for subsequent requests
             file_put_contents($cacheDir."twitcache.xml", $result);
         }
         $xml = simplexml_load_string($result);
         // OK so data is now an object lets do some magic.
         // lets get the users' locations
         foreach($xml->status as $udata)
         {
              $location = $udata->user->location;
              $text =  $udata->text;
              $image = $udata->user->profile_image_url;
              $sname = $udata->user->screen_name;
              // now check for coords from geonames db
              $loc = explode(',', $location);
              if(count($loc) == 2)
              {
                  // dude lets not even try with just a single param...some people are just jokers (location=earth etc);
                  $loc[1] = trim($loc[1]);
                  $query = $loc[0];
                  // Look up the location in geonames...
                  $query_escaped = urlencode(utf8_encode($query));
                  $placeresult = $objCurl->exec('http://ws.geonames.org/search?q='.$query_escaped.'&isNameRequired=true&maxRows=1');
                  // parse the place result now and get the coords ready for the map.
                  $placdata = simplexml_load_string($placeresult);
                  $lat = $placdata->geoname->lat;
                  $lon = $placdata->geoname->lng;
              }
              else {
                  $lat = NULL;
                  $lon = NULL;
              }

              $info[] = array('lat' => $lat, 'lon' =>$lon, 'text' => $text, 'image' => $image, 'sname' => $sname);
         }
         return $info;
    }

    public function makeMap($data)
    {
        $kml = $this->getObject('kmlgen','simplemap');
        $doc = $kml->overlay('Twitter Map','Map Twitter ecosystem');

        foreach($data as $part)
        {
            if($part['lat'] != '' && $part['lon'] != '')
            {
                // build up something nice to look at for the info data (entry)
                $geoadd = $this->newObject('htmltable', 'htmlelements');
        		$geoadd->cellpadding = 3;
        		$geoadd->startRow();
        		$geoadd->addCell('<img src="'.$part['image'].'">');
        		$geoadd->addCell($part['text']);
        		$geoadd->endRow();
                $info = $geoadd->show();

                $doc .= $kml->generateSimplePlacemarker($part['sname'], $info, $part['lon'], $part['lat'], 0);
            }
        }
        $doc .= $kml->simplePlaceSuffix();

        return $doc;
    }


}
?>
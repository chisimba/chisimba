<?php

echo $this->objGeoOps->searchForm();

$location = $this->getParam('location');

if ($location != '') {
    
    echo '<h2>'.$this->objLanguage->languageText('phrase_searchresultsfor', 'geonames', 'Search Results for').': <strong>'.$location.'</strong></h2>';
    $places = $this->objDbGeo->getLocation($location);
    
    if (count($places) == 0) {
        
        
        $objCurl = $this->getObject('curl', 'utilities');
        $data = $objCurl->exec('http://ws.geonames.org/search?name_equals='.urlencode($location).'&style=full&maxRows=100&fclass=P');



        $xml = simplexml_load_string($data);
        
        if (!$xml) {
            echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_geonames_noresultsnoconnection', 'geonames', 'No Results found. Also could not connect to the Geonames Webservice').'</div>';
        } else {
            
            echo '<p><span class="confirm">'.$this->objLanguage->languageText('mod_geonames_file', 'geonames', 'Results from Webservice').'</span></p>';
            
            if (isset($xml->geoname)) {
                
                $table = $this->newObject('htmltable', 'htmlelements');
                $table->startHeaderRow();
                $table->addHeaderCell($this->objLanguage->languageText('mod_geonames_geonameid', 'geonames', 'Geoname ID'));
                $table->addHeaderCell($this->objLanguage->languageText('word_name', 'geonames', 'Name'));
                $table->addHeaderCell($this->objLanguage->languageText('word_latitude', 'geonames', 'Latitude'));
                $table->addHeaderCell($this->objLanguage->languageText('word_longitude', 'geonames', 'Longitude'));
                $table->addHeaderCell($this->objLanguage->languageText('word_country', 'geonames', 'Country'));
                $table->endHeaderRow();
                
                foreach ($xml->geoname as $geoname)
                {
                    $this->objDbGeo->insertFromXML($geoname);
                    
                    $table->startRow();
                    $table->addCell($geoname->geonameId);
                    $table->addCell($geoname->name);
                    $table->addCell($geoname->lat);
                    $table->addCell($geoname->lng);
                    $table->addCell($geoname->countryName);
                    $table->endRow();
                }
                
                echo $table->show();
            } else {
                echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_geonames_noresultsfromwebservice', 'geonames', 'No Results from the Geonames Webservice').'<br />'.$this->objLanguage->languageText('mod_geonames_possiblespellingerror', 'geonames', 'Possibly a spelling error. Please try again').'</div>';
            }
        }
        
    } else {
        
        echo '<p><span class="confirm">'.$this->objLanguage->languageText('mod_geonames_resultsfromdatabase', 'geonames', 'Results from Database').'</span></p>';
        
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell($this->objLanguage->languageText('mod_geonames_geonameid', 'geonames', 'Geoname ID'));
        $table->addHeaderCell($this->objLanguage->languageText('word_name', 'geonames', 'Name'));
        $table->addHeaderCell($this->objLanguage->languageText('word_latitude', 'geonames', 'Latitude'));
        $table->addHeaderCell($this->objLanguage->languageText('word_longitude', 'geonames', 'Longitude'));
        $table->addHeaderCell($this->objLanguage->languageText('word_country', 'geonames', 'Country'));
        $table->endHeaderRow();
        
        foreach ($places as $place)
        {
            $table->startRow();
            $table->addCell($place['geonameid']);
            $table->addCell($place['name']);
            $table->addCell($place['latitude']);
            $table->addCell($place['longitude']);
            $table->addCell($place['countrycode']);
            $table->endRow();
        }
        
        echo $table->show();
    }
    
}


?>
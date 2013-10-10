<?php

$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('link','htmlelements');

//Add the templage heading to the main layer
$objH = $this->getObject('htmlheading', 'htmlelements');
//Heading H3 tag
$objH->type=3; 
$objH->str = $this->objLanguage->languageText("mod_travel_hotelresults","travel")." ".ucwords(strtolower($cityString));

if (isset($countryString)) {
    $hotelCount =  $this->objHotels->hotelCount($cityString,$countryString);
    $results = $this->objHotels->citySearch($cityString,$page,$countryString);
    $country = $this->objCountryCodes->getRow('code',$countryString);
    $objH->str .= ", {$country['name']}";
} else {
    $hotelCount =  $this->objHotels->hotelCount($cityString);
    $results = $this->objHotels->citySearch($cityString,$page);
}

$cIn = $this->getParam('checkin');
$cOut = $this->getParam('checkout');

list($iYear,$iMonth,$iDay) = split('-',$cIn);
list($oYear,$oMonth,$oDay) = split('-',$cOut);
$interval = mktime(0,0,0,$oMonth,$oDay,$oYear) - mktime(0,0,0,$iMonth,$iDay,$iYear);

$nights = $interval/86400;
if ($nights == 1) {
    $nights .= " ".$this->objLanguage->languageText('word_night');
} else {
    $nights .= " ".$this->objLanguage->languageText('word_nights');
}

$rooms = $this->getParam('searchRooms');

$adults = $kids = 0;
for ($i=0;$i<$rooms;$i++) {
    $adults += $this->getParam("searchAdults_$i");
    $kids += $this->getParam("searchChildren_$i");
}


if ($rooms == 1) {
    $rooms .= " ".$this->objLanguage->languageText('word_room');
} else {
    $rooms .= " ".$this->objLanguage->languageText('word_rooms');
}

if ($adults == 1) {
    $adults .= " ".$this->objLanguage->languageText('word_adult');
} else {
    $adults .= " ".$this->objLanguage->languageText('word_adults');
}

switch ($kids) {
    case 0:
        $kids ='';
        break;
    case 1:
        $kids = ", $kids ".$this->objLanguage->languageText('word_child');
        break;
    default:
        $kids = ", $kids ".$this->objLanguage->languageText('word_children');
        break;
}

$change = $this->getObject('link','htmlelements');
$change->link($this->uri(array('action'=>'search hotels')));
$change->link = $this->objLanguage->languageText('word_change');

$recapStr = "<span class='minute'>".$this->objLanguage->languageText('mod_travel_checkin','travel').': '.str_replace('-','/',$cIn).
            ", ".$this->objLanguage->languageText('mod_travel_checkout','travel').": ".str_replace('-','/',$cOut).
            ", $rooms, $nights, $adults$kids | ".$change->show()."</span>";

$startNo = ($page-1)*25+1;
if (($endNo = $startNo+24) > $hotelCount) {
    $endNo = $hotelCount;
}

$summary = "<span class='minute'>".str_replace("[TOTAL]",$hotelCount,str_replace("[END]",$endNo,str_replace("[START]",$startNo,$this->objLanguage->languageText("mod_travel_displaying","travel"))))."</span>";

$pages = "<span class='minute'>";

for ($i=1; $i<=ceil($hotelCount/25);$i++) {
    if ($page == $i) { 
        $pages .= "$i|";
    } else {
        $change->link($this->uri(array('action'=>'hotel results','searchStr'=>$this->getParam('searchStr'),'page'=>$i,'checkin'=>$this->getParam('checkin'),'checkout'=>$this->getParam('checkout'),'searchRooms'=>$this->getParam('searchRooms'),'searchAdults'=>$this->getParam('searchAdults'),'searchChildren'=>$this->getParam('searchChildren'))));
        $change->link = $i;
        $pages .= $change->show()."|";
    }
}
$pages = substr($pages,0,strlen($pages)-1)."</span>";

$summaryTable = $this->getObject('htmltable','htmlelements');
$summaryTable->width="51%";
$summaryTable->startrow();
$summaryTable->addCell($summary);
$summaryTable->addCell($pages,null,null,'right');
$summaryTable->endRow();

$list = "<div id='hotel_results'>".$summaryTable->show();
$count = 0;
foreach ($results as $hotel) {
    $hotel_location = '';
    if ($hotel['address3']) {
        $hotel_location = "{$hotel['address3']}, ";
    }
    $hotel_location .= $hotel['city'];
    $hotel_description = (strlen($hotel['propertydescription']) > 103)? substr($hotel['propertydescription'],0,100)."..." : $hotel['propertydescription']; 
    $name = htmlentities(ucwords(strtolower(strip_tags($hotel['name']))));
    $image = $this->objHotelImages->getImage($hotel['id']);
    $uri = $this->uri(array('action'=>'view hotel','id'=>$hotel['id']));
    if ($count != 0) { 
        $list .= "<br />";
    }
    $list .= "<div class='hotel_match'>
                    <div class='star_rating'></div>
                    <div class='hotel_name'><a href='$uri'>{$name}</a></div>
                    <hr />
                    <div class='hotel_thumb'><a href='$uri'><img src='{$image['thumbnail']}' alt='{$hotel['name']} - {$image['caption']}' /></a></div>
                    <div class='hotel_info'>
                        <strong>$hotel_location</strong><br />
                        $hotel_description
                    </div>
              </div>";
    $count++;
}
$pageTable = $this->newObject('htmltable','htmlelements');
$pageTable->width = "51%";
$pageTable->startRow();
$pageTable->addCell($pages,null,null,'right');
$pageTable->endRow();

$list .= $pageTable->show()."</div>";

$link = new link($this->uri(array('action'=>'search hotels')));
$link->link = $this->objLanguage->languageText('word_back');

$content = $objH->show().$recapStr.$list.$link->show();

echo $content;
?>
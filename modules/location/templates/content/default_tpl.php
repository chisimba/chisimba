<?php

// Load the necessary HTML helper classes
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');

// Load the language class
$objLanguage = $this->getObject('language', 'language');

// Set up the CSS layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// Set up the left sidebar
$userMenu = $this->newObject('usermenu', 'toolbar');
$leftColumn = $userMenu->show();
$cssLayout->setLeftColumnContent($leftColumn);

// Set up the heading
$header = new htmlHeading();
$header->str = $objLanguage->languageText('mod_location_currentlocation', 'location');
$header->type = 1;
$middleColumn = $header->show();

$objGMapApi = $this->getVar('objGMapApi');

if (is_object($objGMapApi)) {
    $middleColumn .= $objGMapApi->getMapJS();
    $middleColumn .= $objGMapApi->getMap();
    $middleColumn .= $objGMapApi->getSidebar();
}

// Get user location language strings
$nameLabel = $objLanguage->languageText('mod_location_locationname', 'location');
$longitudeLabel = $objLanguage->languageText('mod_location_longitude', 'location');
$latitudeLabel = $objLanguage->languageText('mod_location_latitude', 'location');

// Get user location variable values
$nameValue = $this->getVar('locationName');
$longitudeValue = $this->getVar('locationLongitude');
$latitudeValue = $this->getVar('locationLatitude');

// Add location information to middle column
$middleColumn .= '<ul>';
$middleColumn .= "<li>$nameLabel: $nameValue</li>";
$middleColumn .= "<li>$longitudeLabel: $longitudeValue</li>";
$middleColumn .= "<li>$latitudeLabel: $latitudeValue</li>";
$middleColumn .= '</ul>';

// Add update link to the middle column
$linkUri = $this->uri(array('module'=>'location','action'=>'update'));
$linkText = $objLanguage->languageText('mod_location_update', 'location');
$linkHref = new href($linkUri, $linkText);
$link = $linkHref->show();
$middleColumn .= "<p>$link</p>";

// Add twitter information and controls to middle column
if ($this->getVar('locationTwitter')) {
    $linkUri = $this->uri(array('module'=>'location', 'action'=>'disabletwitter'));
    $linkText = $objLanguage->languageText('mod_location_disable', 'location');
    $linkHref = new href($linkUri, $linkText);
    $link = $linkHref->show();
    $text = $objLanguage->languageText('mod_location_twitterenabled', 'location');
    $middleColumn .= "<p>$text $link</p>";
} else {
    $linkUri = $this->uri(array('module'=>'location', 'action'=>'enabletwitter'));
    $linkText = $objLanguage->languageText('mod_location_enable', 'location');
    $linkHref = new href($linkUri, $linkText);
    $link = $linkHref->show();
    $text = $objLanguage->languageText('mod_location_twitterdisabled', 'location');
    $middleColumn .= "<p>$text $link</p>";
}

// Finally add the middle column to the CSS layout
$cssLayout->setMiddleColumnContent($middleColumn);

// And output the whole lot!
echo $cssLayout->show();

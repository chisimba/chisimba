<?php
/**
 * @category  Chisimba
 * @package   conversions
 * @author    Nonhlanhla Gangeni <2539399@uwc.ac.za>
 * @author    Nazheera Khan <2524939@uwc.ac.za>
 * @author    Faizel Lodewyk <2528194@uwc.ac.za>
 * @author    Hendry Thobela <2649282@uwc.ac.za>
 * @author    Ebrahim Vasta <2623441@uwc.ac.za>
 * @author    Keanon Wagner <2456923@uwc.ac.za>
 * @author    Raymond Williams <2541826@uwc.ac.za>
 * @copyright 2007 UWC
 */
// Create an instance of the css layout class
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
// Set columns to 3
$cssLayout->setNumColumns(3);
// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
// Initialize left column
$leftSideColumn = $this->leftMenu->show();
$rightSideColumn = NULL;
$middleColumn = NULL;
$this->objUser = $this->getObject('user', 'security');
$this->objNav = $this->getObject('navigate');
//define $value
if (!isset($value)) {
    $value = NULL;
}
//define $from
if (!isset($from)) {
    $from = NULL;
}
//define $to
if (!isset($to)) {
    $to = NULL;
}
//define $action
if (!isset($action)) {
    $action = NULL;
}
//the main page
if ($goTo == NULL) {
    $description = wordwrap($this->objLanguage->languageText("mod_conversions_description", "conversions") , 100, "<br />\n");
    $objFeatureBox = $this->getObject('featurebox', 'navigation');
    $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_mainPage", "conversions") , $description);
    $middleColumn = $ret;
}
//the distance page
elseif ($goTo == "dist") {
    $middleColumn = $this->objNav->dist();
    $middleColumn.= $this->objNav->answer($value, $from, $to, $action);
}
//the temperature page
elseif ($goTo == "temp") {
    $middleColumn = $this->objNav->temp();
    $middleColumn.= $this->objNav->answer($value, $from, $to, $action);
}
//the volume page
elseif ($goTo == "vol") {
    $middleColumn = $this->objNav->vol();
    $middleColumn.= $this->objNav->answer($value, $from, $to, $action);
}
//the weight page
elseif ($goTo == "weight") {
    $middleColumn = $this->objNav->weight();
    $middleColumn.= $this->objNav->answer($value, $from, $to, $action);
}
//adding the navigation menu
$rightSideColumn = $this->objNav->conversionsFormNav();
//add left column
$cssLayout->setLeftColumnContent($leftSideColumn);
$cssLayout->setRightColumnContent($rightSideColumn);
//add middle column
$cssLayout->setMiddleColumnContent($middleColumn);
echo $cssLayout->show();
?>

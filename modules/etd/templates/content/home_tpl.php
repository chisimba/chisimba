<?php
/**
* Template to display the prelogin page for the ETD repository
* @access public
*/
//if($this->objUser->isLoggedIn()){
    $this->setLayoutTemplate('etd_layout_tpl.php');
/*}else{
    $this->setLayoutTemplate('etd3col_layout_tpl.php');
}
*/

$this->loadClass('htmlheading', 'htmlelements');

$head = $objLanguage->languageText('mod_etd_welcometoetd', 'etd');

//$institution = $this->objConfig->getinstitutionName();
//$shortname = $this->objConfig->getinstitutionShortName();
//$txtIntro = $objLanguage->code2Txt('mod_etd_welcomeintro', 'etd', array('institution' => $institution, 'shortname' => $shortname));

$objHead = new htmlheading();
$objHead->str = $head;
$objHead->type = 1;
$str = $objHead->show();

$str .= '<p>'.$txtIntro.'</p>';

$str .= '<br /><p>'.$this->etdResource->getRecentResources().'</p>';

echo $str;
?>
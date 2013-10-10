<div style="padding:5px">
<?php

/**
* @package popupcalendar
* Template to display the date picker
*/

//$this->objScriptaculous =& $this->getObject('scriptaculous', 'ajaxwrapper');
//$this->objScriptaculous->show();

$headerParams = $this->getJavascriptFile('jsFunctions.js', 'popupcalendar');
$this->appendArrayVar('headerParams', $headerParams);

// Suppress page elements
$this->setVar('pageSuppressXML', TRUE);
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('pageSuppressIM', TRUE);
$this->setVar('suppressFooter', TRUE);

// Set up html elements
$objIcon = &$this->newObject('geticon', 'htmlelements');
$this->loadClass('layer', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

// Set up heading
$heading = $this->objLanguage->languageText('phrase_selectdate');
$objHead = &new htmlHeading();
$objHead->str = $heading;
$objHead->type = 1;
echo $objHead->show();

// Calendar
$objLayer = &new layer();
$objLayer->str = $str;
$objLayer->id = 'calDiv';
$objLayer->align = 'center';
$objLayer->padding = '5px';
$objLayer->width = '280px';
echo $objLayer->show();

// Clock
$objLayer = &new layer();
$objLayer->str = $timeStr;
$objLayer->id = 'timeDiv';
$objLayer->align = 'center';
$objLayer->padding = '5px';
$objLayer->width = '280px';
echo $objLayer->show();

// Hidden form elements
$objLayer = &new layer();
$objLayer->str = $formStr;
$objLayer->id = 'formDiv';
$objLayer->align = 'center';
echo $objLayer->show();
?>
</div>
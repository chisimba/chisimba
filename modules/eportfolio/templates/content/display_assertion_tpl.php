<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objWashout = $this->getObject('washout', 'utilities');
$objTable = new htmltable();
$objTable->width = '100%';
$objTable->attributes = " align='left' border='0'";
$objTable->cellspacing = '12';
$objHeading->type = 1;
$objHeading->str = $objUser->getSurname() . $objLanguage->languageText("mod_eportfolio_assertionDisplay", 'eportfolio');
echo "<div align ='center'>" . $objHeading->show() . "</div>";
//display user's names
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display user's names
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $objLanguage->languageText("mod_eportfolio_lecturer", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($objUser->fullName($myinstructor)) , null, "top", "left", null, null, null);
$objTable->endRow();
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
//display Language
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_wordlanguage", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($mylanguage) , null, "top", "left", null, null, null);
$objTable->endRow();
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display user's names
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_rationaleTitle", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($myrationale) , null, "top", "left", null, null, null);
$objTable->endRow();
//display date calendar
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display user's names
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_creationDate", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($this->objDate->formatDate($mycreation_date)) , null, "top", "left", null, null, null);
$objTable->endRow();
//display short description
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display user's names
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($myshortdescription) , null, "top", "left", null, null, null);
$objTable->endRow();
//display Full description
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display user's names
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($mylongdescription) , null, "top", "left", null, null, null);
$objTable->endRow();
//Select Owner Home
$iconSelect = $this->getObject('geticon', 'htmlelements');
$iconSelect->setIcon('home');
$iconSelect->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . ' ' . $ownersurname . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
$mnglink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_assertion'
)));
$mnglink->link = $iconSelect->show();
$linkManage = $mnglink->show();
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell($linkManage, null, "top", "left", null, null, null);
$objTable->endRow();
echo $objTable->show();
?>

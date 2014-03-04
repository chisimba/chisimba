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
$ownersurname = $objUser->getSurname($ownerId);
$objHeading->str = $ownersurname . $objLanguage->languageText("mod_eportfolio_es", 'eportfolio') . ' ' . $objLanguage->languageText("mod_eportfolio_wordActivity", 'eportfolio');
echo "<div align ='center'>" . $objHeading->show() . "</div>";
//display user's names
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display context
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $objLanguage->languageText("mod_eportfolio_contexttitle", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($contexttitle) , null, "top", "left", null, null, null);
$objTable->endRow();
//Display Types
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display context
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($activityType) , null, "top", "left", null, null, null);
$objTable->endRow();
//display date
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display context
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($this->objDate->formatDate($activityStart)) , null, "top", "left", null, null, null);
$objTable->endRow();
//display date
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display context
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($this->objDate->formatDateOnly($activityFinish)) , null, "top", "left", null, null, null);
$objTable->endRow();
//display short description
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display context
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($shortdescription) , null, "top", "left", null, null, null);
$objTable->endRow();
//display Full description
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display context
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($longdescription) , null, "top", "left", null, null, null);
$objTable->endRow();
//Select Owner Home
$iconSelect = $this->getObject('geticon', 'htmlelements');
$iconSelect->setIcon('home');
$iconSelect->alt = $objLanguage->languageText("mod_eportfolio_view", 'eportfolio') . ' ' . $ownersurname . $objLanguage->languageText("mod_eportfolio_viewEportfolio", 'eportfolio');
$mnglink = new link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_others_eportfolio',
    'id' => $groupId
)));
$mnglink->link = $iconSelect->show();
$linkManage = $mnglink->show();
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell($linkManage, null, "top", "left", null, null, null);
$objTable->endRow();
echo $objTable->show();
?>

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
$objTable->attributes = " align='center' border='0'";
$objTable->cellspacing = '12';
$objHeading->type = 1;
$ownersurname = $objUser->getSurname($ownerId);
$objHeading->str = $ownersurname . $objLanguage->languageText("mod_eportfolio_es", 'eportfolio') . ' ' . $objLanguage->languageText("mod_eportfolio_wordtranscript", 'eportfolio');
echo "<div align ='center'>" . $objHeading->show() . "</div>";
//display type
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display user's names
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_contypes", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($type) , null, "top", "left", null, null, null);
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
$objTable->addCell($objWashout->parseText($shortdescription) , null, "top", "left", null, null, null);
$objTable->endRow();
/*
echo $objWashout->parseText("<b>".$label = $objLanguage->languageText("mod_eportfolio_shortdescription",'eportfolio').":</b>");
echo '<br></br>';
echo '<br></br>';
echo $objWashout->parseText($shortdescription);
echo '<br></br>';
echo '<br></br>';
*/
//display Full description
// Spacer
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
// display user's names
$objTable->startRow();
$objTable->addCell($objWashout->parseText("<b>" . $label = $objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>") , null, "top", "right", null, null, null);
$objTable->addCell($objWashout->parseText($longdescription) , null, "top", "left", null, null, null);
$objTable->endRow();
/*
echo $objWashout->parseText("<b>".$label = $objLanguage->languageText("mod_eportfolio_longdescription",'eportfolio').":</b>");
echo '<br></br>';
echo '<br></br>';

echo $objWashout->parseText($longdescription);
echo '<br></br>';
echo '<br></br>';
*/
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

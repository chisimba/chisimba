<?php
// Add in a heading
$this->loadClass('htmlheading', 'htmlelements');
$headern = new htmlHeading();
$headern->str = $this->objLanguage->languageText('mod_qrreview_reviewprod', 'qrreview');
$headern->type = 2;

$middleColumn = NULL;

$middleColumn .= $headern->show();
$middleColumn .= $this->objReviewOps->showReviewFormMobi($row);

echo $middleColumn;

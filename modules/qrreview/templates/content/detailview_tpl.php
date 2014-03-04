<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$objFeatureBox = $this->getObject('featurebox', 'navigation');
 
$pdfurl = $this->uri(array(
                    'action' => 'makepdf',
                    'module' => 'qrreview',
                    'id' => $row['id']
                ));      

$leftColumn = NULL;
$middleColumn = NULL;
$rightColumn = NULL;

if($this->objUser->isloggedIn()) {
    // get the sidebar object
    $this->leftMenu = $this->newObject('usermenu', 'toolbar');
    $leftColumn .= $this->leftMenu->show();
}
else {
    $leftColumn .= $this->objReviewOps->showSignInBox();
    $leftColumn .= $this->objReviewOps->showSignUpBox();
}

// Add in a heading
$headern = new htmlHeading();
$headern->str = $row['prodname'];
$headern->type = 1;

$middleColumn .= $headern->show();

$pdficon = $this->newObject('geticon', 'htmlelements');
$pdficon->setIcon('filetypes/pdf');
$lblView = $this->objLanguage->languageText("mod_qrreview_saveaspdf", "qrreview");
$pdficon->alt = $lblView;
$pdficon->align = false;
$pdfimg = $pdficon->show();
$pdflink = new href($pdfurl, $pdfimg, NULL);

$middleColumn .= $pdflink->show();

$ratelink = new href($this->uri(array('action' => 'review', 'id' => $row['id']), 'qrreview'),$this->objLanguage->languageText("mod_qrreview_ratethis", "qrreview"));

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell($row['longdesc']);
$table->addCell('<img src="'.$row['qr'].'">'."<br />".$ratelink->show(), 50, NULL, 'right');
$table->endRow();

$middleColumn .= $table->show(); // $row['longdesc'];

// $rightColumn .= $objFeatureBox->show($this->objLanguage->languageText("mod_qrreview_qrcode", "qrreview"), '<img src="'.$row['qr'].'">');

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
// $cssLayout->setRightColumnContent($rightColumn);
echo $cssLayout->show();

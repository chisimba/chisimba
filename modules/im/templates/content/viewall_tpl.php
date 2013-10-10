<?php
header("Content-Type: text/html;charset=utf-8");
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$refreshLink = $this->getObject('link', 'htmlelements');
$refreshIcon = $this->getObject('geticon', 'htmlelements');
$this->objFeatureBox = $this->getObject('featurebox', 'navigation');
$objWashout = $this->getObject('washout', 'utilities');
$this->objImOps = $this->getObject('imops');

$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_im_recentmessages', 'im');
$header->type = 1;

$refreshLink->href = $this->uri(null, 'im');
$refreshIcon->setIcon('refresh');
$refreshLink->link = $refreshIcon->show();



$objPagination = $this->newObject('pagination', 'navigation');
$objPagination->module = 'im';
$objPagination->action = 'viewallajax';
$objPagination->id = 'im';
$objPagination->numPageLinks = $pages;
$objPagination->currentPage = $pages - 1;


$middleColumn .= $header->show().'<br/>'.$refreshLink->show().'<br/>'.$objPagination->show();
//$middleColumn .= $objImView->renderOutputForBrowser($msgs);


if (!$this->objUser->isLoggedIn()) {
    $leftColumn .= $this->objImOps->loginBox(TRUE);
} else {
    $leftColumn .= $this->leftMenu->show();
    if($this->objUser->inAdminGroup($this->objUser->userId()))
    {
       $leftColumn .= $this->objImOps->showMassMessageBox(TRUE, TRUE);
    }
}

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();

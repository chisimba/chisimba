<?php
//$objBlocks = $this->getObject('blocks', 'blocks');
$this->loadClass('link', 'htmlelements');

//link to Landing Page
$objLandingPage = new link($this->uri(array('action'=>'Home')));
$objLandingPage->link='Home';
$landinging = $objLandingPage->show();

//link to Staff Member Registration From
$objRegStaff = new link($this->uri(array('action'=>'Staff Member Registarion')));
$objRegStaff->link='Staff Member Registarion';
$h = $objRegStaff->show();

//link to view registered Staff Members
$objRegStaff = new link($this->uri(array('action'=>'Registered Staff Member')));
$objRegStaff->link='Registered Staff Members';
$a = $objRegStaff->show();

//link to DOE Accredited Journal Articles data entry page
/*
$objAccrJournal = new link($this->uri(array('action'=>'DOE Accredoted Journal Articles')));
$objAccrJournal->link='Accredited Journal';
$b = $objAccrJournal->show();
*/
//View Accredited Journal Article
$objViewJournalf = new link($this->uri(array('action'=>'Accredted Journal Articles Info')));
$objViewJournalf->link='Accredited Journal Articles';
$c = $objViewJournalf->show();

/*//link to Entire Book data entry page
$objEntireBook = new link($this->uri(array('action'=>'Entire Book/Monogragh')));
$objEntireBook->link='Entire Book';
$d = $objEntireBook->show();
*/
//link to Entire Book data entry page
$objEntireBookDetail = new link($this->uri(array('action'=>'Entire Book/Monogragh Details')));
$objEntireBookDetail->link='Entire Book';
$dd = $objEntireBookDetail->show();

/*//link to Chapter in a Book data entry page
$objChaptersInBook = new link($this->uri(array('action'=>'Chapter In a Book')));
$objChaptersInBook->link='Chapter In a Book';
$e = $objChaptersInBook->show();
*/
//link to Chapter in a Book Information Dispaly
$objChaptersInBookInfo = new link($this->uri(array('action'=>'Chapter In a Book Details')));
$objChaptersInBookInfo->link='Chapter In a Book';
$ee = $objChaptersInBookInfo->show();

/*//link to Graduating Doctoral Students data entry page
$objGradDocStud = new link($this->uri(array('action'=>'Graduating Doctoral Student')));
$objGradDocStud->link='Doctoral Student';
$f = $objGradDocStud->show();
*/
//link to Graduating Doctoral Students Information Display
$objGradDocStudInfo = new link($this->uri(array('action'=>'Graduating Doctoral Student Info')));
$objGradDocStudInfo->link='Doctoral Students';
$ff = $objGradDocStudInfo->show();

/*//link to Graduating Doctoral Students Summary Page
$objGradDocStudSummar = new link($this->uri(array('action'=>'Graduating Doctoral Students Summary')));
$objGradDocStudSummar->link='Doctoral Students Summary';
$fff = $objGradDocStudSummar->show();
*/
/*//link to Graduating Masters Students data entry page
$objGradMasterStud = new link($this->uri(array('action'=>'Graduating Masters Student')));
$objGradMasterStud->link='Masters Student';
$g = $objGradMasterStud->show();
*/
//link to Graduating Masters Students Information Display
$objGradMasterStudInfo = new link($this->uri(array('action'=>'Graduating Masters Student Info')));
$objGradMasterStudInfo->link='Masters Students';
$gg = $objGradMasterStudInfo->show();

//link to Graduating Masters Students Summary Page
/*
$objMastersStudSummary = new link($this->uri(array('action'=>'Graduating Masters Students Summary')));
$objMastersStudSummary->link='Masters Students Summary';
$ggg = $objMastersStudSummary->show();
*/

//link to UniversitySummary Page
$objUniSummary = new link($this->uri(array('action'=>'General Summary')));
$objUniSummary->link='University Summary';
$sumarry = $objUniSummary->show();


$cssLayout = $this->getObject('csslayout', 'htmlelements');

//$leftColumn =$objRegStaff->show();// $this->getVar('leftContent');
//$middleColumn = $this->getVar('middleContent');

$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent('<br />'.$landinging.'<br />'.$h.'<br />'.$a.'<br />'.$c.'<br />'.$dd.'<br />'.$ee.'<br />'.$ff.'<br />'.$gg.'<br />'.$sumarry);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>

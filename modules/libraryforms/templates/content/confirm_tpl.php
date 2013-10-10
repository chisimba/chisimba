<?php
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');
$objIcon =  $this->newObject('geticon', 'htmlelements');
//$h3->str = $objIcon->show().'&nbsp;'. $this->objLanguage->languageText('category_resource_six','libraryforms','');

$objLayer->str = $h3->show();
$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$header = $objLayer->show();
$this->loadClass('link', 'htmlelements');
$cssLayout = $this->getObject('csslayout', 'htmlelements');

//link to Landing Page
$objLandingPage = new link($this->uri(array('action'=>'Back to Forms')));
$objLandingPage->link='Back to Forms';
$landinging = $objLandingPage->show();
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($landinging);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();


$display = '<p>'.$header.'</p><hr />';

//Show Header
echo $display;
echo '<div class="noRecordsMessage">'. $this->objLanguage->languageText('category_resource_six','libraryforms','');
echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_libraryforms_distanceconfirmsent', 'libraryforms', '').'</div>';
$objBlocks = $this->getObject('blocks', 'blocks');
$this->loadClass('link', 'htmlelements');



/*link to Landing Page
$objLandingPage = new link($this->uri(array('action'=>'Home')));
$objLandingPage->link='Home';
$landinging = $objLandingPage->show();

//link to Staff Member Registration From
$objdistance = new link($this->uri(array('action'=>'')));
$objdistance->link='Distance user Form';
$d = $objdistance->show();

//link to view registered Staff Members
$objthesis = new link($this->uri(array('action'=>'')));
$objthesis->link='Thesis Books only';
$t = $objthesis->show();


//View Accredited Journal Article
$objperiod = new link($this->uri(array('action'=>'')));
$objperiod->link='Periodical Books Only';
$p = $objperiod->show();


//link to Entire Book data entry page
$objfdb = new link($this->uri(array('action'=>'')));
$objfbd->link='Feed back Form';
$f = $objfdb->show();


$cssLayout = $this->getObject('csslayout', 'htmlelements');

//$leftColumn =$objRegStaff->show();// $this->getVar('leftContent');
//$middleColumn = $this->getVar('middleContent');

$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent('<br />'.$landinging.'<br />'.$d.'<br />'.$t.'<br />'.$p.'<br />'.$f.'<br />'.$sumarry);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();*/


?>

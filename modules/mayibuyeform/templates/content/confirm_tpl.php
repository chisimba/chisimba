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
$objLandingPage->link='Back to Research Form';
$landinging = $objLandingPage->show();
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($landinging);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();


$display = '<p>'.$header.'</p><hr />';

//Show Header
echo $display;
//echo '<div class="noRecordsMessage">'. $this->objLanguage->languageText('category_resource_six','libraryforms','');
echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_mayibuyeform_commentsent', 'mayibuyeform', '').'</div>';
$objBlocks = $this->getObject('blocks', 'blocks');
$this->loadClass('link', 'htmlelements');

?>

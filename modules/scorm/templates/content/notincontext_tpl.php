<?php
/* 
 * This class displays the error message informing thr user of attempting
 * to use the module without joining the a context first
 */
$this->loadclass('link','htmlelements');
$this->loadclass('htmlheading','htmlelements');
$objIcon= $this->newObject('geticon','htmlelements');
// Create an instance of the css layout class
$cssLayout = &$this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);



$heading = new htmlheading($this->objLanguage->languageText('mod_scorm_notincontext_title','scorm'),1);
$body    = $this->objLanguage->languageText('mod_scorm_notincontext_body','scorm');
$linktext    = $this->objLanguage->languageText('mod_scorm_notincontext_link','scorm');
$link = new link($this->uri(array('action'=>'home'),'postlogin'));
$link->link=$linktext;
// links are displayed on the left
$leftSideColumn = $body;
$cssLayout->setLeftColumnContent($leftSideColumn);
$rightSideColumn.=$heading->show().$body.'<br/>'.$link->show();
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

// Output the content to the page
echo $cssLayout->show();
?>

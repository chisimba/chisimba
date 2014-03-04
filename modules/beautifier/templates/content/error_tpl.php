<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('dropdown','htmlelements');
$userMenu  = &$this->newObject('usermenu','toolbar');

// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

// Add Post login menu to left column
$leftSideColumn ='';
$leftSideColumn = $userMenu->show();

$middleColumn = NULL;


$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_beautifier_heading', 'beautifier');

$instructions = new htmlheading();
$instructions->type = 4;
$instructions->str = $this->objLanguage->languageText('mod_beautifier_instructions', 'beautifier');

$middleColumn = $header->show();

//create the form
$objForm = new form('beautifier',$this->uri(array('action'=>'')));
$objForm->displayType = 4;
$objForm->addToFormEx($objLanguage->languageText('mod_beautifier_submitmod', 'beautifier'));


$modarr = array();
$this->objConfig = $this->getObject('altconfig', 'config');
if ($handle = opendir($this->objConfig->getModulePath())) {
   while (false !== ($file = readdir($handle))) {
       if ($file != "." 
           && $file != ".." 
           && $file != "CVS" 
           && $file != "CVSROOT"
           && $file != ".svn"
           && $file != "COPYING") {
           $modarr[] = $file;
       }
   }
   closedir($handle);
}

$dd = new dropdown('mod');
sort($modarr);
foreach($modarr as $options)
{
	$dd->addOption($options, $options);
}

$objForm->addToFormEx($objLanguage->languageText('mod_beautifier_ddlabel', 'beautifier'),$dd->show());

$this->objButton = new button($objLanguage->languageText('mod_beautifier_beautify', 'beautifier'));
$this->objButton->setValue($objLanguage->languageText('mod_beautifier_beautify', 'beautifier'));
//$this->objButton->setOnClick('alert(\'Processing\')');
$this->objButton->setToSubmit();



$objForm->addToFormEx($this->objButton->show());

$objFeatureBox = $this->newObject('featurebox', 'navigation');

$middleColumn .= $objFeatureBox->show($this->objLanguage->languageText("mod_beautifier_ddlabel", "beautifier"),$objForm->show()); //$instructions->show();
//$middleColumn .= $dd->show();


//add left column
$cssLayout->setLeftColumnContent($leftSideColumn);
//add middle column
$cssLayout->setMiddleColumnContent($middleColumn);
echo $cssLayout->show();
?>
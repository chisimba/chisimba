<?php
//Set up the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("mod_sysconfig_edtxt",'sysconfig');

$cssLayout->setLeftColumnContent($this->objLanguage->languageText("mod_sysconfig_edlabel",'sysconfig')
  . "<br />&nbsp;<br />");
$cssLayout->setMiddleColumnContent($header->show() . $str);
echo $cssLayout->show();
?>
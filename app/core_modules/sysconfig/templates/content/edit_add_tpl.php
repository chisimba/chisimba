<?php
//Set up the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("mod_sysconfig_edtxt",'sysconfig');
$left = "<div class='sysconfig_left'>"
  . $this->objLanguage->languageText("mod_sysconfig_edlabel",'sysconfig')
  . "</div><br />&nbsp;<br />";
$cssLayout->setLeftColumnContent($left);
$middle = $header->show() . $str;
$middle = "<div class='sysconfig_main'>$middle</div>";
$cssLayout->setMiddleColumnContent($middle);
echo $cssLayout->show();
?>
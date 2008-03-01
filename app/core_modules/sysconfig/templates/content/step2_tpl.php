<?php
//Set up the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

//Set up the title depending on system or module params
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 1;
$pgHd = $pmodule;
$objIcon = $this->newObject('geticon', 'htmlelements');
if($pmodule == '_site_'){
    $pgHd = $leftText = $this->objLanguage->languageText("mod_sysconfig_editsys",'sysconfig');
    $leftText = $this->objLanguage->languageText("mod_sysconfig_step2s",'sysconfig');
    //Create the table for the output
    $objTable = $this->newObject('htmltable', 'htmlelements');
    $objConfig = $this->newObject('altconfig','config');
    $objTable->cellpadding = 5;
    //Get the special delete icon to work with confirm
    $objDelIcon = $this->newObject('geticon', 'htmlelements');
    $objDelIcon->setIcon("delete");
    $objDelIcon->alt=$this->objLanguage->languageText("word_delete");

    //Add a row
    $objTable->startRow();
    $objTable->addCell("<b>".$this->objLanguage->languageText("mod_sysconfig_paramname",'sysconfig')."</b>", "30%", NULL, "LEFT", "heading");
    $objTable->addCell("<b>".$this->objLanguage->languageText("mod_sysconfig_paramvalue",'sysconfig')."</b>", "60%", NULL, "LEFT", "heading");
    $objTable->addCell("<b>".$this->objLanguage->languageText("mod_sysconfig_action",'sysconfig')."</b>", "10%", NULL, "RIGHT", "heading");
    $objTable->endRow();
    $xml = $objConfig->readConfig('','XML');
    $xml = $xml->toArray();
    $xml = $xml['root']['Settings'];

    if (isset($xml)) {
        //Initialize row counter for odd or even
        $rowcount=0;
        foreach ($xml as $line => $value) {
            //Bitwise determination of odd or even
            $oddOrEven=($rowcount==0) ? "odd" : "even";// $objAddLink =& $this->getObject('link', 'htmlelements');
            $edLink=$this->uri(array('action' => 'edit',
              'pmodule' => '_site_',
              'id' => $line,
              'value' =>$value));
            $delLink=$this->uri(array('action' => 'delete',
              'pmodule' => '_site_',
              'confirm' => 'yes',
              'id' => $line));
            //Get a confirm delete object
            $objConfirm = $this->newObject('confirm','utilities');
            $objConfirm->setConfirm($objDelIcon->show(), $delLink, $this->objLanguage->languageText("phrase_confirmdelete"));

            $objTable->startRow();
            $objTable->addCell($line, "40%", NULL, NULL, $oddOrEven);
            $objTable->addCell(htmlentities($value), "30%", NULL, NULL, $oddOrEven);
            $objTable->addCell($objIcon->getEditIcon($edLink), "60%", NULL, "RIGHT", $oddOrEven);
            $objTable->endRow();
            //Set rowcount for bitwise determination of odd or even
            $rowcount=($rowcount==0) ? 1 : 0;
        }
    }
//Module parameters
} else {
    $pgHd = $this->objLanguage->languageText("mod_sysconfig_editparm",'sysconfig')
      . ": " . $pmodule;
    $leftText = $this->objLanguage->languageText("mod_sysconfig_step2m",'sysconfig');
    //Create the table for the output
    $objTable = $this->newObject('htmltable', 'htmlelements');
    $objTable->cellpadding = 5;
    //Get the special delete icon to work with confirm
    $objDelIcon = $this->newObject('geticon', 'htmlelements');
    $objDelIcon->setIcon("delete");
    $objDelIcon->alt=$this->objLanguage->languageText("word_delete");


    //Add a row
    $objTable->startRow();
    $objTable->addCell("<b>".$this->objLanguage->languageText("mod_sysconfig_paramname",'sysconfig')."</b>", "30%", NULL, "LEFT", "heading");
    $objTable->addCell("<b>".$this->objLanguage->languageText("mod_sysconfig_paramdesc",'sysconfig')."</b>", "60%", NULL, "LEFT", "heading");
    $objTable->addCell("<b>".$this->objLanguage->languageText("mod_sysconfig_paramvalue",'sysconfig')."</b>", "60%", NULL, "LEFT", "heading");
    $objTable->addCell("<b>".$this->objLanguage->languageText("mod_sysconfig_action",'sysconfig')."</b>", "10%", NULL, "RIGHT", "heading");
    $objTable->endRow();
    if (isset($ary)) {
        //Initialize row counter for odd or even
        $rowcount=0;
        foreach ($ary as $line) {
            //Bitwise determination of odd or even
            $oddOrEven=($rowcount==0) ? "odd" : "even";
            $edLink=$this->uri(array('action' => 'edit',
              'pmodule' => $pmodule,
              'id' => $line['id']));
            $delLink=$this->uri(array('action' => 'delete',
              'pmodule' => $pmodule,
              'confirm' => 'yes',
              'id' => $line['id']));

            //Get a confirm delete object
            $objConfirm = $this->newObject('confirm','utilities');
            $objConfirm->setConfirm($objDelIcon->show(), $delLink, $this->objLanguage->languageText("phrase_confirmdelete"));

            $objTable->startRow();
            // addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null)
            $objTable->addCell($line['pname'], "20%", NULL, NULL, $oddOrEven);
            $objTable->addCell($this->objLanguage->languageText($line['pdesc'],$line['pmodule']), "60%", NULL, NULL, $oddOrEven);
            $objTable->addCell(htmlentities($line['pvalue']), "20%", NULL, NULL, $oddOrEven);
            $objTable->addCell($objIcon->getEditIcon($edLink), "60%", NULL, "RIGHT", $oddOrEven);
            $objTable->endRow();
            //Set rowcount for bitwise determination of odd or even
            $rowcount=($rowcount==0) ? 1 : 0;
        }
    }
}
$header->str = $pgHd;

$cssLayout->setLeftColumnContent($leftText . "<br />&nbsp;<br />");
$cssLayout->setMiddleColumnContent($header->show() . $objTable->show());
echo $cssLayout->show();
?>
<?php
//Get the icon class
$objIcon = $this->newObject('geticon', 'htmlelements');
//The URL for the add link
$addLink=$this->uri(array('action' => 'add',
          'pmodule' => $pmodule));
//Create text add link
// $objAddLink =& $this->getObject('link', 'htmlelements');
// $objAddLink->link($addLink);
// $objAddLink->link = 'add';                           
//The add Icon linked
// if ($disableadd==TRUE) {
    // $objIcon->setIcon("add_grey");
    // $objIcon->alt=$this->objLanguage->languageText("mod_sysconfig_addiconalt",'sysconfig');
    // $addIcon = $objIcon->show();
// } else {
    // $addIcon = $objIcon->getAddIcon($addLink);
// }
$pgHd = $pmodule;
if($pgHd == '_site_'){
$pgHd = 'Site Parameters';
$pgTitle =& $this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
$pgTitle->str = $this->objLanguage->languageText("mod_sysconfig_secondstep",'sysconfig')."&nbsp;"."&nbsp;"."&nbsp;"."&nbsp;"."&nbsp;"."&nbsp;".$pgHd;
?>
<style type="text/css">
<!--
.steplayout {
	font-family: Arial, Helvetica, sans-serif;
	font-style: normal;
	line-height: normal;
	background-color: #FFFFCC;
	color: #666666;
	border: thin dotted #FF6600;
}
.infocell {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	background-color: #F2EBD2;
}
-->
</style>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="3" class="steplayout">
  <tr>
    <td><h3><?php echo $pgTitle->show() ?></h3></td><td width="32%" rowspan="2" valign="top" class="infocell"><?php echo $step2 ?></td>
  </tr>
  <tr>
    <td width="68%" height="300" valign="top">
	
	<?php
//Create the table for the output
    $objTable = &$this->newObject('htmltable', 'htmlelements');
    $objConfig = &$this->newObject('altconfig','config');
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
            $oddOrEven=($rowcount==0) ? "odd" : "even";
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
            // addCell($str, $width=null, $valign="top", $align=null, $class=null, $attrib=Null)
            $objTable->addCell($line, "40%", NULL, NULL, $oddOrEven);
            $objTable->addCell($value, "30%", NULL, NULL, $oddOrEven);
            $objTable->addCell($objIcon->getEditIcon($edLink), "60%", NULL, "RIGHT", $oddOrEven);
            $objTable->endRow();
            //Set rowcount for bitwise determination of odd or even
            $rowcount=($rowcount==0) ? 1 : 0;
        }
         echo $objTable->show();
    }

}else{
//Create page header
$pgTitle =& $this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
$pgTitle->str = $this->objLanguage->languageText("mod_sysconfig_secondstep",'sysconfig')."&nbsp;"."&nbsp;"."&nbsp;"."&nbsp;"."&nbsp;"."&nbsp;".$pgHd;

?>
<style type="text/css">
<!--
.steplayout {
	font-family: Arial, Helvetica, sans-serif;
	font-style: normal;
	line-height: normal;
	background-color: #FFFFCC;
	color: #666666;
	border: thin dotted #FF6600;
}
.infocell {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	background-color: #F2EBD2;
}
-->
</style>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="3" class="steplayout">
  <tr>
    <td><h3><?php echo $pgTitle->show() ?></h3></td><td width="32%" rowspan="2" valign="top" class="infocell"><?php echo $step2 ?></td>
  </tr>
  <tr>
    <td width="68%" height="300" valign="top">
	
	<?php
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
            $objTable->addCell($line['pvalue'], "20%", NULL, NULL, $oddOrEven);
            $objTable->addCell($objIcon->getEditIcon($edLink), "60%", NULL, "RIGHT", $oddOrEven);
            $objTable->endRow();
            //Set rowcount for bitwise determination of odd or even
            $rowcount=($rowcount==0) ? 1 : 0;
        }
    }
    echo $objTable->show();
    if (isset($str)) {
      echo $str;
    }
}
	?>
	
	</td>
  </tr>
</table>

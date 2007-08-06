<?php
/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 * @author Wesley Nitsckie
 **/

echo $this->getJavascriptFile('TreeMenu.js','tree');
echo $this->getJavascriptFile('domLib.js','htmlelements');
echo $this->getJavascriptFile('domTT.js','htmlelements');
echo $this->getJavascriptFile('domTT_drag.js','htmlelements');
echo $this->getJavascriptFile('windowpopup.js','htmlelements');


$objTree =  $this->newObject('contenttree','tree');
$layer =  $this->newObject('layer', 'htmlelements');
$layer->width = '200';
$layer->overflow = 'auto';
$layer->str = $objTree->show();

$myTable=$this->newObject('htmltable','htmlelements');
$myTable->width="100%";
       $myTable->border="0";
      $myTable->cellpadding="0";      
	  $myTable->class="funnytable";
	  $myTable->startRow();
	  $myTable->addCell($layer->show(), '200', "top", "left", null, Null);	 
	  $myTable->addCell($page, null, "top", null, null, Null);
	  $myTable->endRow();	
      echo $myTable->show();
 
?>
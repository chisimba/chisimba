<?php


//Template to view the different admin functions
$tbl = $this->newObject('htmltable', 'htmlelements');
$tbl->cellpadding = 3;
$tbl->width = "100%";
$tbl->align = "left";

$link = & $this->newObject('link', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objH = &$this->newObject('htmlheading', 'htmlelements');
$objRound =&$this->newObject('roundcorners','htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$objDBContext = $this->getObject('dbcontext','context');

$objIcon->setIcon('control_panel','png', 'icons/cms/');
$objIcon->title = $this->objLanguage->languageText('mod_cmsadmin_cpanel','cmsadmin');
$objH->str = $objIcon->show().'&nbsp;'.$objDBContext->getTitle().'&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_cpanel','cmsadmin');

$objLayer->str = $objH->show();
//$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_left';
$header = $objLayer->show();

$objLayer->str = $this->_objUtils->topNav();
//$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_right';
$header .= $objLayer->show();

$objLayer->str = '';
//$objLayer->border = '; clear:both; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_clear';
$objLayer->cssClass = 'clearboth';
$headShow = $objLayer->show();

echo $objRound->show($header.$headShow);//$tbl->show());

//echo  $cpanel;

//print 'context view';

//list context that user has access to

//heading for the content

// control .. add pages .. import content ... page organizer  .... exporter ... 
//print $this->_objUtils->topNav();
print $this->_objUtils->getContextControlPanel();
?>

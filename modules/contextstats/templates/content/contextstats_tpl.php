<?php
  // Set up html elements
  $this->loadClass('htmltable', 'htmlelements');
  $this->loadClass('htmlheading', 'htmlelements');
  $this->loadClass('link', 'htmlelements');
  $objIcon = $this->newObject('geticon', 'htmlelements');
  $this->objImView = $this->getObject('viewer');
  
  $objHead = new htmlheading();
  $objHead->str = ucwords($this->objLanguage->code2Txt('mod_contextstats_name', 'contextstats', null, '[-context-] Statistics'));
  $objHead->type = 1;
  echo $objHead->show();
  
  $objTable = $this->newObject('htmltable', 'htmlelements');
  
  $contextcode = ucwords($this->objLanguage->code2Txt('mod_contextstats_phrasecontextcode', 'contextstats'));
  $contexttitle = ucwords($this->objLanguage->code2Txt('mod_contextstats_phrasecontexttitle', 'contextstats'));
  $department = ucwords($this->objLanguage->code2Txt('mod_contextstats_phrasedept', 'contextstats'));
  $etools = ucwords($this->objLanguage->code2Txt('mod_contextstats_phraseetools', 'contextstats'));
  
  $objTable->startHeaderRow();
  $objTable->addHeaderCell($contextcode, '10%');
  $objTable->addHeaderCell($contexttitle, '25%');
  
  $wsdl = $this->objSysConfig->getValue('WSDL', 'contextstats');
  if ($wsdl != 'FALSE') {
      $objTable->addHeaderCell($department, '25%');
  }
  $objTable->addHeaderCell($etools, '40%');
  
  $objTable->endHeaderRow() . '<br/>';
  
  $objPagination = $this->newObject('pagination', 'navigation');
  $objPagination->module = 'contextstats';
  $objPagination->action = 'viewallajax';
  $objPagination->id = 'contextstats';
  $objPagination->numPageLinks = $pages;
  $objPagination->currentPage = $pages - $pages;
  
  echo $objTable->show() . '<br/>' . $objPagination->show() . '<br/>';
?>

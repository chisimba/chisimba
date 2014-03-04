<?php
/**
* @package etd
*/

/**
* Template for displaying a printer friendly version.
* @param string $search The search results to print.
*/

//$this->setLayoutTemplate('etdsearch_layout_tpl.php');

$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);

$objLayer =& $this->newObject('layer', 'htmlelements');

$objLayer->str = $search;
$objLayer->padding = '10px';
$str = $objLayer->show();

echo $str;
?>
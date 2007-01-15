<?php
//call on tidy to clean up...
// Specify tidy configuration
//$config = array(
//         'indent'        => true,
//         'output-xhtml'  => true,
//         'wrap'          => 200);

// Tidy
//$tidy = new tidy;

if(!$this->getParam('query') == '')
{
	$objLucene = & $this->newObject('results', 'lucene');
	$searchResults = $objLucene->show($this->getParam('query'));
	
} else {
	$searchResults = '';
}
$output = $this->getContent().$searchResults;
//$tidy->parseString($output, $config, 'utf8');
//$tidy->cleanRepair();

echo $output;

?>
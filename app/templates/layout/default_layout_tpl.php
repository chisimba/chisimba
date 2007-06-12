<?php
//call on tidy to clean up...
// Specify tidy configuration
//$config = array(
//         'indent'        => true,
//         'output-xhtml'  => true,
//         'wrap'          => 200);

// Tidy
//$tidy = new tidy;
if($this->getParam('query') != '')
{
	$objLucene = & $this->newObject('results', 'lucene');
	$searchResults = $objLucene->show($this->getParam('query'));
	// echo $searchResults; die();
	$searchResults = str_replace('&','&amp;', $searchResults);
	$this->setVarByRef('searchresults', $searchResults);
	$output = $searchResults;
	
	

} else {
	$searchResults = '';
	$output = $this->getContent();
}
//.$searchResults;
//$tidy->parseString($output, $config, 'utf8');
//$tidy->cleanRepair();

echo $output;

?>
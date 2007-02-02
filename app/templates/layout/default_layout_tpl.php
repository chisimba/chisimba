<?php
//call on tidy to clean up...
// Specify tidy configuration
//$config = array(
//         'indent'        => true,
//         'output-xhtml'  => true,
//         'wrap'          => 200);

// Tidy
//$tidy = new tidy;
if(isset($this->getParam('query')))
{
	$objLucene = $this->newObject('results', 'lucene');
	$searchResults = $objLucene->show($this->getParam('query'));
	echo $searchResults;

} else {
	$searchResults = NULL;
	$output = $this->getContent();
	echo $output;
}

//$tidy->parseString($output, $config, 'utf8');
//$tidy->cleanRepair();


?>
<?php
//call on tidy to clean up...
// Specify tidy configuration
//$config = array(
//         'indent'        => true,
//         'output-xhtml'  => true,
//         'wrap'          => 200);

// Tidy
//$tidy = new tidy;
//$searchResults = '<hr/>This is the search results';
$output = $this->getContent().$searchResults.$this->footerStr;
//$tidy->parseString($output, $config, 'utf8');
//$tidy->cleanRepair();

echo $output;

?>
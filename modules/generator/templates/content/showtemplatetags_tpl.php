<?php
//Load the XML template tagnames 
$xml = simplexml_load_file("modules/generator/resources/template-tagnames.xml");

//Create an instance of the table object
$objTable = $this->newObject('htmltable', 'htmlelements');
//Turn on active rows
$objTable->active_rows=TRUE;
//Turn the array into a table
$objTable->startRow();
$objTable->addHeaderCell('tagname');
$objTable->addHeaderCell('tagtext');
$objTable->addHeaderCell('description');
$objTable->endRow();

foreach($xml->tag as $tag) {
	$objTable->startRow();
	$objTable->addCell($tag->tagname);
	$objTable->addCell($tag->tagtext);
	$objTable->addCell($tag->description);
	$objTable->endRow();
}

//Show the table
echo $objTable->show();
?>
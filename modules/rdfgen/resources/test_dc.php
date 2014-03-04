<?php

// DC example

//change the RDFAPI_INCLUDE_DIR to your local settings
define("RDFAPI_INCLUDE_DIR", "/var/www/rdfapi-php/api/"); 
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

$someDoc = new Resource ("http://www.example.org/someDocument.html");
$creator = new Resource ("http://www.purl.org/dc/elements/1.1/creator"); 

$statement1 = new Statement ($someDoc, $creator, new Literal ("Paul Scott")); 

$model1 = ModelFactory::getDefaultModel();
$model1->add($statement1); 

// Output $model1 as HTML table
echo "<b>Output the MemModel as HTML table: </b><p>";
$model1->writeAsHtmlTable();

// Output the string serialization of $model1
echo "<b>Output the plain text serialization of the MemModel: </b><p>";
echo $model1->toStringIncludingTriples();

// Output the RDF/XML serialization of $model1
echo "<b>Output the RDF/XML serialization of the MemModel: </b><p>";
echo $model1->writeAsHtml(); 
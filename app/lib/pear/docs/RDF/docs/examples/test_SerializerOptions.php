<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Test Serializer Options</title>
</head>
<body>

<?php
include 'RDF.php';
include 'RDF/Model/Memory.php';
// Filename of an RDf document
$base = "Example1.rdf";
// Create a new Model_Memory
$model =& new RDF_Model_Memory();
// Load and parse document
$model->load($base);
// Output model as HTML table
$model->writeAsHtmlTable();
echo "<P>"; 
// Create Serializer and serialize model to RDF with default configuration
$ser =& new RDF_Serializer();
$rdf = &$ser->serialize($model);
echo "<p><textarea cols='110' rows='20'>" . $rdf . "</textarea>";
// Serialize model to RDF using attributes
$ser->configUseAttributes(true);
$rdf = &$ser->serialize($model);
echo "<p><textarea cols='110' rows='20'>" . $rdf . "</textarea>";
$ser->configUseAttributes(false); 
// Serialize model to RDF using entities
$ser->configUseEntities(true);
$rdf = &$ser->serialize($model);
echo "<p><textarea cols='110' rows='30'>" . $rdf . "</textarea>";
$ser->configUseEntities(false);

?>

</body>
</html>

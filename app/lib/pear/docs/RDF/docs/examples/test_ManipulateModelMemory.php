<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Test Manipulate and Search RDF Model</title>
</head>
<body>

<?php 
// Include RAP
include 'RDF.php';
include 'RDF/Model/Memory.php';
// Filename of an RDF document
$base = "example1.rdf";
// Create a new Model_Memory
$model =& new RDF_Model_Memory();
// Load and parse document
$model->load($base);
// Output model as HTML table
$model->writeAsHtmlTable();
echo "<P>";
// Ceate new statements and add them to the model
$statement1 =& RDF_Statement::factory(
    RDF_Resource::factory('http://www.w3.org/Home/Lassila'),
    RDF_Resource::factory('http://description.org/schema/Description'),
    RDF_Literal::factory('Lassila\'s personal Homepage', 'en')
);

$statement2 =& RDF_Statement::factory(
    RDF_Resource::factory('http://www.w3.org/Home/Lassila'),
    RDF_Resource::factory('http://description.org/schema/Description'),
    RDF_Literal::factory('Lassilas persönliche Homepage', 'de')
);

$model->add($statement1);
$model->add($statement2);

$model->writeAsHtmlTable();
echo "<P>";
// Build search index to speed up searches.
$model->index();
// Search model 1
$homepage =& RDF_Resource::factory("http://www.w3.org/Home/Lassila");
$res = $model->find($homepage, null, null);

$res->writeAsHtmlTable();
echo "<P>";
// Search model 2
$description =& RDF_Resource::factory("http://description.org/schema/Description");
$statement = $model->findFirstMatchingStatement($homepage, $description, null);
// Check if something was found and output result
if ($statement) {
    echo $statement->toString();
} else {
    echo "Sorry, I didn't find anything.";
} 
echo "<P>";
// Search model 3
$res3 = $model->findVocabulary("http://example.org/stuff/1.0/");
$res3->writeAsHtmlTable();
echo "<P>"; 
// Write model as RDF
$model->writeAsHtml();
// Save model to file
$model->saveAs("Output.rdf");

?>
</body>
</html>

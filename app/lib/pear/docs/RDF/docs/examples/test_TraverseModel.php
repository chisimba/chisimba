<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Test Traverse Model_Memory</title>
</head>
<body>
<?php

include 'RDF.php';
include 'RDF/Model/Memory.php';

// Filename of an RDF document
$base = "example1.rdf";
// Create a new Model_Memory
$model =& new RDF_Model_Memory();
// Load and parse document
$model->load($base);
// Get Iterator from model
$it = $model->getStatementIterator();
// Traverse model and output statements
while ($it->hasNext()) {
    $statement = $it->next();
    echo "Statement number: " . $it->getCurrentPosition() . "<BR>";
    echo "Subject: " . $statement->getLabelSubject() . "<BR>";
    echo "Predicate: " . $statement->getLabelPredicate() . "<BR>";
    echo "Object: " . $statement->getLabelObject() . "<P>";
} 
// Move to the last statement and print it
$it->moveLast();
$statement = $it->current();
// Traverse model backward and print statements
echo $statement->toString() . "<BR>";
while ($it->hasPrevious()) {
    $statement = $it->previous();
    echo $statement->toString() . "<BR>";
} 
// Jump to statement 2 and print it
$it->moveTo(2);
$statement = $it->current();
echo $statement->toString() . "<BR>";
?>
</body>
</html>

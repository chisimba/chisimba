<?php 
// ----------------------------------------------------------------------------------
// PHP Script: test_ManipulatingModel_MDB.php
// ----------------------------------------------------------------------------------
/*
 * This is an online demo of RAP's database backend.
 * This script demonstrates some methods to manipulate a Model_MDB.
 *
 * @author Radoslaw Oldakowski <radol@gmx.de>
 */
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <title>Test Store Models in Database</title>
</head>
<body>

<?php 
// Include RAP
include 'RDF.php';
include 'RDF/Store/MDB.php';
include 'RDF/Model/MDB.php';
include 'RDF/Model/Memory.php';
// # 1. Connect to MsAccess database (via ODBC)
// # ------------------------------------------
// Connect to MsAccess (rdf_db DSN) database using connection settings
// defined in constants.php :
$rdf_database =& new RDF_Store_MDB(
    array(
        'phptype' => 'mysql',
        'username' => 'metapear',
        'password' => 'funky',
        'hostspec' => 'localhost',
        'database' => 'rdf'
    ), array());
// # 2. Load a Model_MDB
// # -----------------
$Model_MDB = $rdf_database->getModel("Example1.rdf");
// Output the model as HTML table
$Model_MDB->writeAsHtmlTable();

echo "<br><br>";
// # 3. Add a statement tho the Model_MDB
// # ----------------------------------
// Ceate a new statement
$statement =& RDF_Statement::factory(
    RDF_Resource::factory('http://www.w3.org/Home/Lassila'),
    RDF_Resource::factory('http://description.org/schema/Description'),
    RDF_Literal::factory('Lassilas persönliche Homepage', 'de')
);

// Add the statement to the Model_MDB
$Model_MDB->add($statement);
// Output the string serialization of the Model_MDB
echo $Model_MDB->toStringIncludingTriples();

echo "<br><br>";
// # 4. Search statements
// # ---------------------
// Search for statements having object $literal
$literal =& RDF_Literal::factory('Lassilas persönliche Homepage', 'de');
$res = $Model_MDB->find(null, null, $literal);
// Output the result
$res->writeAsHtmlTable();

echo "<br>";
// # 5. 5. Replace nodes and serialize the Model_MDB to XML/RDF
// # --------------------------------------------------------
// replace a literal
$Model_MDB->replace(null, null,
    RDF_Literal::factory("Lassilas persönliche Homepage", "de"),
    RDF_Literal::factory ("Lassila's personal Homepage", "en")
);
// Serialize to RDF
$Model_MDB->writeAsHtml();

echo "<br><br>";
// # 6. Remove a statement
// # ---------------------
$Model_MDB->remove(
    RDF_Statement::factory(
        RDF_Resource::factory("http://www.w3.org/Home/Lassila"),
        RDF_Resource::factory("http://description.org/schema/Description"),
        RDF_Literal::factory("Lassila's personal Homepage", "en")
    )
);
// Output the Model_MDB
$Model_MDB->writeAsHtmlTable();

echo "<br>";
// # 7. Generate a Model_Memory and compare both models
// # ----------------------------------------------
// Generate a Model_Memory
$Model_Memory = $Model_MDB->getMemModel();
// Compare this Model_MDB withe the generated Model_Memory
$res = $Model_MDB->equals($Model_Memory);

if ($res)
    echo "models are equal";
else
    echo "models are different";

echo "<br>";
// # 8. Save Model_MDB to file
// # ----------------------------------------------
// Save Model_MDB to file (XML/RDF)
$Model_MDB->saveAs("Output.rdf");
// Save Model_MDB to file (N3)
$Model_MDB->saveAs("Output.n3");
// close the Model_MDB
$Model_MDB->close();

?>

</body>
</html>

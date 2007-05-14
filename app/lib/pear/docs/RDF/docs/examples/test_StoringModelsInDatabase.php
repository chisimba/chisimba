<?php 
// ----------------------------------------------------------------------------------
// PHP Script: test_StoringModelsInDatabase.php
// ----------------------------------------------------------------------------------
/*
 * This is an online demo of RAP's database backend.
 * It shows how to peristently store rdf models in a database.
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
// # 2. Store a memory model in database.
// # ------------------------------------
// Load an RDF-Documtent into a memory model
// Filename of an RDF document
$base = "Example1.rdf";
// Create a new memory model
$Model_Memory =& new RDF_Model_Memory();
// Load and parse document
$Model_Memory->load($base);
// Now store the model in database
// An unique modelURI will be generated
$rdf_database->putModel($Model_Memory);
// You can also provide an URI for the model to be stored
$modelURI = "Example1.rdf";
// But then you must check if there already is a model with the same modelURI
// otherwise the method putModel() will return FALSE
if ($rdf_database->modelExists($modelURI))
    echo "Model with the same URI: '$modelURI' already exists";
else
    $rdf_database->putModel($Model_Memory, $modelURI);
// # 3. Create a new database model
// # ------------------------------
$modelURI = "newModel_MDB";
// Base URI of the new model (optional)
$baseURI = "baseURIofMyNewModel_MDB#";
// get a new Model_MDB
if ($rdf_database->modelExists($modelURI)) {
    echo "Model with the same URI: '$modelURI' already exists";
} else {
    $Model_MDB = $rdf_database->getNewModel($modelURI, $baseURI);
}
// # 4. List all models stored in database
// # -------------------------------------
// Get an array with modelURI and baseURI of all models stored in rdf database
$list = $rdf_database->listModels();
// Show the database contents
foreach ($list as $model) {
    echo "modelURI: " . $model['modelURI'] . "<br>";
    echo "baseURI : " . $model['baseURI'] . "<br><br>";
} 

?>
</body>
</html>

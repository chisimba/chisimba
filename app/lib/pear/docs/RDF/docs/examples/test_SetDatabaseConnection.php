<?php 
// ----------------------------------------------------------------------------------
// PHP Script: test_SetDatabaseConnection.php
// ----------------------------------------------------------------------------------
/*
 * This is an online demo of RAP's database backend.
 * Tutorial how to connect to different databases and how to create tables.
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
// # 1. Connect to MsAccess database (via ODBC) and create tables.
// Connect to MsAccess (rdf_db DSN) database using connection settings
// defined in constants.php :
// ----------------------------------------------------------------------------------
// Database
// ----------------------------------------------------------------------------------

// Connect to MySQL database with user defined connection settings
$rdf_database =& new RDF_Store_MDB(
    array(
        'phptype' => 'mysql',
        'username' => 'root',
        'password' => '',
        'hostspec' => 'localhost',
        'database' => 'rdf2'
    ), array());

// Create tables for MySQL
var_dump($rdf_database->createTables());

// Close the connection
$rdf_database->close();

?>
</body>
</html>

<?php

// ----------------------------------------------------------------------------------
// RDFDBUtils : Create database tables
// ----------------------------------------------------------------------------------

/** 
 * This lets you setup a database for use with RAP
 * 
 * @version $Id: createTables.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/


$needDB=true;

include("config.inc.php"); 

include("utils.php");

include("setup.php");

$title="RAP DB &raquo; Create tables";

include("header.php");

include("menu.php");

createDB();

if (isset($_REQUEST["create"]) && $_REQUEST["create"]) {

  $db->createTables("MySql"); 

    ?>

<h1>Database <?php print dbLink($activeDB)?></h1>

<h2>Tables created</h2>

<?php } else { ?>

<h1>Database <?php print dbLink($activeDB)?></h1>

   <?php if ($db->isSetup($_DB[$activeDB]["type"])) { ?>

<p>This database is already setup for RAP.</p>
<p><a href="createTables.php?create=true">Create tables again</a> - This wont destroy your data.</p>


<?php } else { ?>

<p>This database is not setup for RAP.</p>
<p><a href="createTables.php?create=true">Create tables now</a></p>
   
    

<?php 
}
}
include("footer.php");

?>


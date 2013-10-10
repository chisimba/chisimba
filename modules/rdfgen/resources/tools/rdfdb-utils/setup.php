<?php

// ----------------------------------------------------------------------------------
// RDFDBUtils : Setup
// ----------------------------------------------------------------------------------

/** 
 * This contains setup functions for DB connections etc.
 * 
 * @version $Id: setup.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/

if (isset($_SESSION["non_config"])) {
  $_DB["non_config"]=$_SESSION["non_config"];
}

if (isset($needDB) && $needDB) { 
    
  if (!isset($_SESSION["activeDB"]) || !isset($_DB[$_SESSION["activeDB"]])) { 
    if (!isset($activeDB)) {
      session_write_close();
      header("Location: choseDB.php");
    }
  } else { 
    $activeDB=$_SESSION["activeDB"];
  }

}

if (isset($needTables) && $needTables) {

  createDB();
  if (!$db->isSetup($_DB[$activeDB]["type"])) { 
    session_write_close();
    header("Location: createTables.php");
  }
}

if (isset($needModel) && $needModel) { 

  if (!isset($_REQUEST["modelURI"]) && !isset($_SESSION["modelURI"])) { 
    header("Location: listModels.php");
    die();
  } else { 
    if (isset($_REQUEST["modelURI"])) {
      $muri=$_REQUEST["modelURI"];
      $_SESSION["modelURI"]=$muri;
    } else { 
      $muri=$_SESSION["modelURI"];
    }
  }

  
}




?>

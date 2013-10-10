<?php 

// ----------------------------------------------------------------------------------
// RDFDBUtils : Add namespace to Model
// ----------------------------------------------------------------------------------

/** 
 * This lets you add namespaces and prefixes to a model
 * 
 * @version $Id: addnamesp.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Tobias Gauß <tobias.gauss@web.de>
 *
 **/


$needDB=true;
$needTables=true;
$needModel=true;

include("config.inc.php"); 
include("utils.php");

include("setup.php");

$title="RAP DB &raquo; Add namespace";
include("header.php");
include("menu.php");

if ($db->modelExists($muri)) { 
  $m=$db->getModel($muri);
 

  if ( isset($_REQUEST["namespaceeadd"])) { 

    print "<h1>Adding...</h1>\n";

    if (isset($_REQUEST["namespace"])){ 
    	$nmsp=$_REQUEST["namespace"];
    }else{
    	$nmsp = null;
    }

    if (isset($_REQUEST["namespaceprefix"])){	
    	$prefix=$_REQUEST["namespaceprefix"];
    }else{
    	$prefix = null;
    }
    
    if ($nmsp==null || $prefix==null) { 
      print "<div span='warning'>You must specify values for namespace and namespaceprefix!</div>\n";
    } else { 
      $m->addNamespace($prefix,$nmsp); 
      print "<p>Added (".$prefix.", ".$nmsp.").</p>\n";
      print "<hr/>\n";
    }
  }


?>

   <h1>Adding namespaces to <?php print $muri; ?></h1>

   <h2>Namespace, Prefix:<h2>
   <form name="dumbadd" method="get">
       <input type="hidden" name="namespaceeadd" value="true" />

   <span class="tableheader">Namespace:</span><br/>
   <input type="text" name="namespace" size="120" /><br/>
   
   <span class="tableheader">Prefix:</span><br/>
   <input type="text" name="namespaceprefix" size="120" /><br/>
   
   <input type="submit" value="Ok" class="okbutton"/>
   </form>



<?php } else {   //model doesn't exist! ?>

  <div class="message">No such model! <?php print $m; ?></div>

<?php } ?>

<?php include("footer.php"); ?> 

<?php 

// ----------------------------------------------------------------------------------
// RDFDBUtils : Model
// ----------------------------------------------------------------------------------

/** 
 * Shows model data and lets you perform various options
 * 
 * @version $Id: model.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/


$needDB=true;
$needTables=true;
$needModel=true;

include("config.inc.php"); 
include("utils.php");

include("setup.php");

# createDB();

$script= <<<JS
function doNada() {}
JS;

$title="RAP DB &raquo; Model";

include("header.php");
include("menu.php");

if ($db->modelExists($muri)) { 
  $m=$db->getModel($muri);

  if (isset($_REQUEST["delete"])) {
    $d=$_REQUEST["delete"];
    if ($d=="model") {
      $m->delete();
      $msg="Model deleted.";
    } 
    if ($d=="triples") { 
      $r=$m->find(null,null,null);
      foreach($r->triples as $t) $m->remove($t);
      $msg="Model emptied.";
    }
}


?>

<h1>Database <?php print dbLink($activeDB)?></h1>

<h1>Model: <?php print $muri?></h1>

<?php if (isset($msg)) print "<span class=\"warning\">$msg</span>\n"; ?>

<table>
   <tr><td class="tableheader">Base:</td><td><?php if(isset($m->baseuri)) print $m->baseuri;?></td></tr>
   <tr><td class="tableheader">Size:</td><td><?php print $m->size()?></td></tr>
</table>
<br/>
      <form action="model.php" method="post" id="deleteform">
      <input type="hidden" name="delete" id="delete" value="false" />
      <input type="button" onClick="if (confirm('Are you sure you want to delete this model and all it\'s triples?')) { document.getElementById('delete').value='model'; document.getElementById('deleteform').submit(); }" value="Delete this model" class="okbutton"/> 

   <input type="button" onClick="document.location='query.php?edit=true&dumbsearch=true&subject=&predicate=&object=&limit=100';" value="Edit" class="okbutton" />
   <input type="button" onClick="document.location='query.php';" value="Query" class="okbutton"/>       
   <input type="button" onClick="document.location='add.php';" value="Add" class="okbutton"/> 
   <input type="button" onClick="if (confirm('Are you sure you want to delete all triples in this model?')) { document.getElementById('delete').value='triples'; document.getElementById('deleteform').submit(); }" value="Empty" class="okbutton"/>     
   <input type="button" onClick="document.location='namesp.php?dumb=true';" value="Namespaces" class="okbutton"/> 
   <input type="button" onClick="document.location='addnamesp.php';" value="AddNamesp" class="okbutton"/> 
   <input type="button" onClick="document.location='rdf.php';" value="RDF/XML" class="okbutton"/> 
   <input type="button" onClick="document.location='n3.php';" value="N3" class="okbutton"/> 



      </form>  




<?php } else {   //model doesn't exist! ?>

  <div class="message">No such model! <?php print $m?></div>

<?php 
} 

include("footer.php"); ?> 

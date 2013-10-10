<?php 

// ----------------------------------------------------------------------------------
// RDFDBUtils : List Models
// ----------------------------------------------------------------------------------

/** 
 * This lets you select one of the models that exist in a database. 
 * 
 * @version $Id: listModels.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/



$needDB=true;
$needTables=true;

include("config.inc.php"); 
include("utils.php");

include("setup.php");

#print $_SESSION["activeDB"]."kake";

$title="RAP DB &raquo; List models";

include("header.php");
include("menu.php");

if (isset($_REQUEST["modelURI"])) { 
  $m=new MemModel($_REQUEST["base"]);
  $db->putModel($m,$_REQUEST["modelURI"]);
}

?>

<h1>Database <?php print dbLink($activeDB)?></h1>

<h1>Chose a model:</h1>

<ul>

<?php foreach($db->listModels() as $mo) { 
  $m=$mo["modelURI"];
?>
  <li><a href="model.php?modelURI=<?php print urlencode($m)?>"><?php print $m?></a></li>

<?php } 
if (count($db->listModels())==0) print "<li>No models found.</li>\n";
?>
</ul>


<h2>Create new model</h2>
<form name="new" method="post"> 
<table>
<tr><td class="tableheader">Model URI:</td>
<td><input type="text" name="modelURI" /></td></tr>
<tr><td class="tableheader">Base:</td>
<td><input type="text" name="base" /></td></tr>
</table>
<input type="submit" class="okbutton" value="Ok" />
</form>




<?php include("footer.php"); ?> 


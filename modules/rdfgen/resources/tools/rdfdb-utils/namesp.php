<?php 

// ----------------------------------------------------------------------------------
// RDFDBUtils : Display namespaces
// ----------------------------------------------------------------------------------

/**
* This displays the models namespaces
* 
* @version $Id: namesp.php 313 2006-08-03 07:45:38Z cyganiak $
* @author   Tobias Gauß <tobias.gauss@web.de>
*
**/

$needDB=true;
$needTables=true;
$needModel=true;

include("config.inc.php");
include("utils.php");

include("setup.php");

$script= <<<JS
function doNada() {}

  function delname(nmsp,prefix) { 
    if (confirm("Are you sure you want to delete the namespace ("+nmsp+")?")) {
      document.getElementById("op").value="delete";
      document.getElementById("namespaceuri").value=nmsp;
      document.getElementById("namespaceprefix").value=prefix;
      document.getElementById("namespops").submit();
    }
  }

  function editname(nmsp,prefix) {   
      document.getElementById("op").value="edit";
      document.getElementById("namespaceuri").value=nmsp;
      document.getElementById("namespaceprefix").value=prefix;
      document.getElementById("namespops").submit();
  }
  

JS;


$title="RAP DB &raquo; Namespaces";

include("header.php");
include("menu.php");
if ($db->modelExists($muri)) {
	$m=$db->getModel($muri);


	if(isset($_REQUEST["dumb"])){
		$dumb=$_REQUEST["dumb"];
	}else{
		$dumb=false;
	}

    if ($dumb) {
    	
    $nmsp = $_REQUEST['namespaceuri'];
    $prefix = $_REQUEST['namespaceprefix'];	 



	$nmsp=$m->getParsedNamespaces();

	
	 print "<form id='namespops'>\n";
	 print "<input type='hidden' id='namespaceprefix' name='namespaceprefix' />\n";
     print "<input type='hidden' id='namespaceuri' name='namespaceuri' />\n";
     print "<input type='hidden' id='op' name='op'/>\n";
   

	print "<br/>\n";
	print "<table class='resulttable'>\n<tr><td style='width: 64px'>------------</td><td class='tableheader'>Prefix</td><td class='tableheader'>Namespace</td></tr>\n";
	$i=0;
	if($nmsp != false){
	foreach($nmsp as $key => $value) {


		print "<tr class='restablerow$i'>";
		print "<td style='width: 64px'>";
		print "<img class='opicon' src='delete_icon.png' alt='delete' onClick='delname(\"".addslashes($key)."\",\"".addslashes($value)."\");'/>";
		print "<img class='opicon' src='edit_icon.png' alt='edit' onClick='editname(\"".addslashes($key)."\",\"".addslashes($value)."\");'/></td>";
		
		print "<td>".$value."</td>".
		"<td>".$key."</td>".
		"</tr>\n";
		$i++;
		$i%=2;
		
	}
	}else{
		print "No namespaces used.";
	}
	
	print "</table>\n";






	} else if (isset($_REQUEST["op"])) {

		$op=$_REQUEST["op"];
	
		if ($op=="delete") {
			print "<h1>Deleting...</h1>\n";

			$nmsp=$_REQUEST["namespaceuri"];
			$prefix=$_REQUEST["namespaceprefix"];


			print "\n<!--\n";
			var_dump($nmsp);
			var_dump($prefix);
			print "\n-->\n";

			$m->removeNamespace($nmsp);

			//invalidate triples cached in session:
			$_SESSION["triples"]=FALSE;

			print "<p>Deleted namespace (".$nmsp.").</p>\n";
			print "<hr/>\n";
		}

		if ($op=="edit") {

			$nmsp=$_REQUEST["namespaceuri"];
			$prefix=$_REQUEST["namespaceprefix"];


			print "<h1>Edit namespace:</h1>\n";
?>

      <form name="editform">
      <input name="onamespace" type="hidden" value="<?php print $nmsp?>"/>
	  <input name="oprefix" type="hidden" value="<?php print $prefix?>"/>

      
	  <span class="tableheader">Prefix:</span><br/> 
	  <input name="newprefix" type="text" size="120" value="<?php print $prefix ?>"/><br/>
	  <span class="tableheader">Namespace:</span><br/>
	  <input name="newnamespace" type="text" size="120" value="<?php print $nmsp?>"/><br/>
	  <input name="op" value="editsave" type="hidden"/>

	  <input type="submit" value="ok" class="okbutton" />
</form>
	
<?php }
	if ($op=="editsave") { 
  //Delete old.

  $nmsp = $_REQUEST["onamespace"];
  $prefix = $_REQUEST["oprefix"];
 
  
  print "<!--";
  var_dump($_REQUEST["onamespace"]);
  var_dump($_REQUEST["oprefix"]);
  print "-->";
  
  $m->removeNamespace($nmsp);

  $m->addNamespace($_REQUEST['newprefix'],$_REQUEST['newnamespace']);
  
  print "<h1>Updated namespaceprefix</h1>\n";
  print "<p>(".htmlspecialchars($_REQUEST['newprefix']).", ".htmlspecialchars($_REQUEST['newnamespace']).")</p>\n";

  print "<hr/>\n";
      
}
	

	
	}








}else{
	 //model doesn't exist! ?>

  <div class="message">No such model! <?php print $m?></div>

<?php ;}
include("footer.php"); ?> 

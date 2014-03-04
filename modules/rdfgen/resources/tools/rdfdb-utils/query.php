<?php 

// ----------------------------------------------------------------------------------
// RDFDBUtils : Query Model
// ----------------------------------------------------------------------------------

/** 
 * This lets you query a model and inspect the query results
 * 
 * @version $Id: query.php 313 2006-08-03 07:45:38Z cyganiak $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
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

  function del(s,p,o) {  
    if (confirm("Are you sure you want to delete the triple ("+s+", "+p+", "+o+")?")) { 
      document.getElementById("op").value="delete";
      document.getElementById("tripleSubject").value=s;
      document.getElementById("triplePredicate").value=p;
      document.getElementById("tripleObject").value=o;
      document.getElementById("tripleops").submit();
    }
  }

  function edit(s,p,o) {   
      document.getElementById("op").value="edit";
      document.getElementById("tripleSubject").value=s;
      document.getElementById("triplePredicate").value=p;
      document.getElementById("tripleObject").value=o;
      document.getElementById("tripleops").submit();
  }
   


JS;


$title="RAP DB &raquo; Query";

include("header.php");
include("menu.php");


function search($m, $s, $p, $o) {
  $r=$m->find($s,$p,$o); 

  //Can't put Objects in session, so preproc.
  $triples=array();
  foreach($r->triples as $t) { 
    $s=$t->getSubject();
    $p=$t->getPredicate();
    $o=$t->getObject();
	  
    $triples[]=array($s->toString(), $p->toString(), $o->toString());
  }
  return $triples;
}

if ($db->modelExists($muri)) { 
  $m=$db->getModel($muri);

?>
	



   <?php   if(isset($_REQUEST["rdql"])){
   			$rdql=$_REQUEST["rdql"];
   		}else{
   			$rdql=false;
   		}
   		if(isset($_REQUEST["dumbsearch"])){
   			$dumbsearch=$_REQUEST["dumbsearch"];
   		}else{
   			$dumbsearch=false;
   		}
   		
   
   
   		if ($rdql ||$dumbsearch) { 

    if (!isset($_REQUEST["edit"])) print "<h1>Querying $muri</h1>\n";
	else print "<h1>Editing $muri</h1>\n";


    if ($rdql) { 

      // RDQL query

      $rdql=$_REQUEST["rdql"];
      $r=$m->rdqlQuery($rdql);
      
      print "<h2>Results for ".htmlspecialchars($rdql)."</h2>\n";
      print "<table class='resulttable'>\n";
      
      $first=true;
      
      $i=1;
      foreach($r as $t) {
	if ($first) {
	  print "<tr>";
	  foreach ($t as $v=>$value) print "<td class='tableheader'>$v</td>";
	  print "</tr>\n";
	  $first=false;
	}
	print "<tr class='restablerow$i'>";
	foreach ($t as $v) {
	  print "<td>".($v==NULL?"null":$v->toString())."</td>";
	}
	print "</tr>\n";
	$i++; 
	$i%=2;
      }

      print "</table>\n";
      
    } else {
      //Dumb query

      if (!isset($previous)) {
	if ($_REQUEST["bnode"]) { 
	  $s=$_REQUEST["subject"]!=""?new BlankNode($_REQUEST["subject"]):null;
	} else { 
	  $s=$_REQUEST["subject"]!=""?new Resource($_REQUEST["subject"]):null;
	}
      
	//$s=$_REQUEST["subject"]!=""?new Resource($_REQUEST["subject"]):null;
	$p=$_REQUEST["predicate"]!=""?new Resource($_REQUEST["predicate"]):null;
	if (isset($_REQUEST["literal"])) 
	  $o=$_REQUEST["object"]!=""?new Literal($_REQUEST["object"]):null;
	else 
	  $o=$_REQUEST["object"]!=""?new Resource($_REQUEST["object"]):null;
	

	if (!isset($edit)) 
	  print "<h2>Results for (".($s!=null?$s->getLabel():"null").", ".($p!=null?$p->getLabel():"null").", ".($o!=null?$o->getLabel():"null").")</h2>\n";

	$triples=search($m,$s,$p,$o);
	
	$_SESSION["triples"]=$triples;
	$_SESSION["qsubject"]=$s==null?null:$s->toString();
	$_SESSION["qpredicate"]=$p==null?null:$p->toString();
	$_SESSION["qobject"]=$o==null?null:$o->toString();
      } else { 	
	$triples=$_SESSION["triples"];
	if ($triples===FALSE) { 
	  //redo query
	  $s=nodeFromString($_SESSION["qsubject"]);
	  $p=nodeFromString($_SESSION["qpredicate"]);
	  $o=nodeFromString($_SESSION["qobject"]);
	  $triples=search($m,$s,$p,$o);
	  $_SESSION["triples"]=$triples;

	}
      }
      
      print "<form id='tripleops'>\n";
      print "<input type='hidden' id='tripleSubject' name='tripleSubject' />\n";
      print "<input type='hidden' id='triplePredicate' name='triplePredicate' />\n";
      print "<input type='hidden' id='tripleObject' name='tripleObject' />\n";
      print "<input type='hidden' id='op' name='op'/>\n";
      
      $total=count($triples);
      if (!isset($edit)) print "<p>Found ".$total." triples.</p>\n";

      if ($_REQUEST["limit"]) {
	$limit=$_REQUEST["limit"];
	if (isset($_REQUEST["offset"])) $offset=$_REQUEST["offset"]; else $offset=0;
	if ($offset<0) $offset=0;
	if ($offset>$total) $offset-=1+$offset-$total;
	if ($offset+$limit>$total) $limit-=($limit+$offset)-$total;
	$atriples=array_slice($triples, $offset, $limit);

	print "Showing ".($offset?"$offset-":"").($limit+$offset)." of $total triples.<br/>\n";
	print "<table>\n";
	print "<tr><td align='right'>";
	if ($offset>0) print "<a href='query.php?dumbsearch=true&previous=true&limit=$limit&offset=0'>&lt;&lt;-- First </a>\n";
	print "</td><td>|</td><td>";
	if ($offset+$limit<$total) print "<a href='query.php?dumbsearch=true&previous=true&limit=$limit&offset=".($total-$limit)."'>Last --&gt;&gt;</a>\n";
	print "</td></tr>\n";
	print "<tr><td align='right'>";
	if ($offset>0) print "<a href='query.php?dumbsearch=true&previous=true&limit=$limit&offset=".($offset-$limit)."'>&lt;-- previous</a>\n";
	print "</td><td>|</td><td>";
	if ($offset+$limit<$total) print "<a href='query.php?dumbsearch=true&previous=true&limit=$limit&offset=".($offset+$limit)."'>next --&gt;</a>\n";
	print "</td></tr>";
	print "</table>\n";
	

      } else { 
	$atriples=$triples; 
      }

      print "<br/>\n";
      

      print "<table class='resulttable'>\n<tr><td style='width: 64px'>------------</td><td class='tableheader'>Subject</td><td class='tableheader'>Predicate</td><td class='tableheader'>Object</td></tr>\n";


      $i=0;
      foreach($atriples as $t) { 
	$s=$t[0];
	$p=$t[1];
	$o=$t[2];

	print "<tr class='restablerow$i'>";
	print "<td style='width: 64px'>";
	print "<input type='checkbox' name='selected[]' />\n";
	print "<img class='opicon' src='delete_icon.png' alt='delete' onClick='del(\"".addslashes($s)."\",\"".addslashes($p)."\",\"".addslashes($o)."\");'/>";
	print "<img class='opicon' src='edit_icon.png' alt='edit' onClick='edit(\"".addslashes($s)."\",\"".addslashes($p)."\",\"".addslashes($o)."\");'/></td>";
	print "<td>".$s."</td>".
	  "<td>".$p."</td>".
	  "<td>".$o."</td></tr>\n";
	  
	$i++; 
	$i%=2;
	
      }
      print "</table>\n";
    }


  } else if (isset($_REQUEST["op"])) { 


    
    $op=$_REQUEST["op"];
    
    if ($op=="delete") { 
      print "<h1>Deleting...</h1>\n";      
      
      $s=nodeFromString($_REQUEST["tripleSubject"]);
      $p=nodeFromString($_REQUEST["triplePredicate"]);
      $o=nodeFromString($_REQUEST["tripleObject"]);

      print "\n<!--\n";
      var_dump($s);
      var_dump($p);
      var_dump($o);
      print "\n-->\n";

      $m->remove(new Statement($s, $p, $o));
      
      //invalidate triples cached in session:
      $_SESSION["triples"]=FALSE;

      print "<p>Deleted statement (".$s->getLabel().", ".$p->getLabel().", ".$o->getLabel().").</p>\n";

      print "<hr/>\n";
    }

    if ($op=="edit") { 

      $os=htmlspecialchars($_REQUEST["tripleSubject"]);
      $op=htmlspecialchars($_REQUEST["triplePredicate"]);
      $oo=htmlspecialchars($_REQUEST["tripleObject"]);

      $s=htmlspecialchars(preg_replace("/^[^(]*\(\"|\"\)/","", $_REQUEST["tripleSubject"]));
      $p=htmlspecialchars(preg_replace("/^[^(]*\(\"|\"\)/","", $_REQUEST["triplePredicate"]));
      $o=htmlspecialchars(preg_replace("/^[^(]*\(\"|\"\)/","", $_REQUEST["tripleObject"]));


      print "<h1>Edit statement:</h1>\n";
?>

       <form name="editform">
	  <input name="otripleSubject" type="hidden" value="<?php print $os?>"/>
	  <input name="otriplePredicate" type="hidden" value="<?php print $op?>"/>
	  <input name="otripleObject" type="hidden" value="<?php print $oo?>"/>

	  <span class="tableheader">Subject:</span><br/> 
	  <input name="tripleSubject" type="text" size="120" value="<?php print $s?>"/><br/>
	  <span class="tableheader">Predicate:</span><br/>
	  <input name="triplePredicate" type="text" size="120" value="<?php print $p?>"/><br/>
	  <span class="tableheader">Object:</span><br/>
	  <input name="tripleObject" type="text" size="120" value="<?php print $o?>"/><br/>
	  <span class="tableheader">Literal:</span>
	  <input type="checkbox" name="literal" <?php print strpos($oo,"Literal")!==FALSE?"checked":""?>"/><br/>
	  

	  <input name="op" value="editsave" type="hidden"/>

	  <input type="submit" value="ok" class="okbutton" />
</form>
<?php
//"	  
    }

if ($op=="editsave") { 
  //Delete old.

  $s=nodeFromString($_REQUEST["otripleSubject"]);
  $p=nodeFromString($_REQUEST["otriplePredicate"]);
  $o=nodeFromString($_REQUEST["otripleObject"]);
  
  print "<!--";
  var_dump($_REQUEST["otripleSubject"]);
  var_dump($_REQUEST["otriplePredicate"]);
  var_dump($_REQUEST["otripleObject"]);
  print "-->";
  
  $os=new Statement($s,$p,$o);
  $m->remove($os);



  $s=new Resource($_REQUEST["tripleSubject"]);
  $p=new Resource($_REQUEST["triplePredicate"]);
  if ($_REQUEST["literal"]) 
    $o=new Literal($_REQUEST["tripleObject"]);
  else 
    $o=new Resource($_REQUEST["tripleObject"]);

  $m->add(new Statement($s,$p,$o));

  //invalidate triples cached in session:
  $_SESSION["triples"]=FALSE;

  
  print "<h1>Updated statement</h1>\n";
  print "<p>(".htmlspecialchars($s->getLabel()).", ".htmlspecialchars($p->getLabel()).", ".htmlspecialchars($o->getLabel()).")</p>\n";

  print "<hr/>\n";
      
}
?>

<?php } else { ?>


<h1>Query <?php print $muri?></h1>
   
   <h2>RDQL:</h2>
   <form name="rdqlquery" method="get">
     <input type="text" name="rdql" size="120" /><br/>
     <input type="submit" value="Ok" class="okbutton" />
   </form>
<hr/>
   <h2>Pred, Obj, Subj:<h2>
<p>Leave blank to list all</p>
   <form name="dumbquery" method="get">
   <input type="hidden" name="dumbsearch" value="true" />
   <span class="tableheader">Subject:</span><br/>
   <input type="text" name="subject" size="120" />
   <span class="tableheader">BNode:</span>
   <input type="checkbox" name="bnode" /><br/>


   <span class="tableheader">Predicate:</span><br/>
   <input type="text" name="predicate" size="120" /><br/>

   <span class="tableheader">Object:</span><br/>
   <input type="text" name="object" size="120" />
   <span class="tableheader">Literal:</span>
   <input type="checkbox" name="literal" /><br/>
   <span class="tableheader">Triple-Limit:</span><br/>
   <input name="limit" type="text" size="4" /><br/>
   
   <input type="submit" value="Ok" class="okbutton"/>
   </form>
   



<?php } } else {   //model doesn't exist! ?>

  <div class="message">No such model! <?php print $m?></div>

<?php } ?>

<?php include("footer.php"); ?> 

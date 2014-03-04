<?php 

// ----------------------------------------------------------------------------------
// RDFDBUtils : Add to Model
// ----------------------------------------------------------------------------------

/** 
 * This lets you add statements or file to a model
 * 
 * @version $Id: add.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/


$needDB=true;
$needTables=true;
$needModel=true;

include("config.inc.php"); 
include("utils.php");

include("setup.php");

$title="RAP DB &raquo; Add";
include("header.php");
include("menu.php");

if ($db->modelExists($muri)) { 
  $m=$db->getModel($muri);

  if ( isset($_FILES["rdffile"])) { 
    print "<h1>Uploading file to $muri</h1>\n";
    print "<p>Got ".$_FILES["rdffile"]["name"]."<br/>\n";
    if ( preg_match("/n3$/",$_FILES["rdffile"]["name"])) {
      // N3
      print "<p>Detected format N3.</p>\n";
      $n3p=new N3Parser(); 
      $m2=$n3p->generateModel($_FILES["rdffile"]["tmp_name"]);
    } else {
      // default to XML
      $r=new RdfParser();
      $m2=$r->generateModel($_FILES["rdffile"]["tmp_name"]);
    }
    $m->addModel($m2);
    print "Read ".$_FILES["rdffile"]["size"]." bytes and ".count($m2->triples)." triples.<br/></p>\n";
    
        
  } 

  if ( isset($_REQUEST["rdfuri"])) { 
    $u=$_REQUEST["rdfuri"];
    print "<h1>Downloading $u</h1>\n";

    if (isset($PROXYHOST)) {
      $a=getthroughproxy($u,$PROXYHOST,$PROXYPORT);
      $f=tempnam();
      
      $fp=fopen($f);
      fputs($fp,$a["content"]);
      fclose($fp);
      
    } else { 
      $f=$u;
    }

    $ri=new RdfParser(); 
    $m2=$ri->generateModel($f);
    $m->addModel($m2);
    print "Read ".count($m2->triples)." triples.</br></p>\n";

  }

  if ( isset($_REQUEST["tripleadd"])) { 

    print "<h1>Adding...</h1>\n";

    if (isset($_REQUEST["bnode"])) { 
      $s=$_REQUEST["subject"]!=""?new BlankNode($_REQUEST["subject"]):null;
    } else { 
      $s=$_REQUEST["subject"]!=""?new Resource($_REQUEST["subject"]):null;
    }
    $p=$_REQUEST["predicate"]!=""?new Resource($_REQUEST["predicate"]):null;

    if (isset($_REQUEST["literal"])) 
      $o=$_REQUEST["object"]!=""?new Literal($_REQUEST["object"]):null;
    else 
      $o=$_REQUEST["object"]!=""?new Resource($_REQUEST["object"]):null;

    if ($s==null || $o==null || $p==null) { 
      print "<div span='warning'>You must specify values for subject, object and predicate!</div>\n";
    } else { 
      $m->add(new Statement($s,$p,$o)); 
      print "<p>Added (".($s!=null?$s->toString():"null").", ".($p!=null?$p->toString():"null").", ".($o!=null?$o->toString():"null").").</p>\n";
      print "<hr/>\n";
    }
  }


?>

    <h1>Adding triples to <?php print $muri; ?></h1>

   <h2>Pred, Obj, Subj:<h2>
   <form name="dumbadd" method="get">
       <input type="hidden" name="tripleadd" value="true" />

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
   
   <input type="submit" value="Ok" class="okbutton"/>
   </form>

<h2>Upload RDF</h2>
<form method="post" enctype="multipart/form-data" > 
	  
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
<span class="tableheader">File:</span><br/> <input type="file" name="rdffile" class="okbutton"/><br/>
<span class="tableheader">Base:</span><br/> <input type="text" name="base" class="okbutton"/><br/>
<input type="submit" class="okbutton" value="Ok"/> 

</form>

<h2>Download RDF</h2>

<form method="post" action="add.php">

<span class="tableheader">URI:</span><br/><input name="rdfuri" class="okbutton" type="text" size="100" /><br/>
<span class="tableheader">Base:</span><br/> <input type="text" name="base" class="okbutton"/><br/>

<input type="submit" class="okbutton" value="Ok" />
</form>







<?php } else {   //model doesn't exist! ?>

  <div class="message">No such model! <?php print $m; ?></div>

<?php } ?>

<?php include("footer.php"); ?> 

<?php

// ----------------------------------------------------------------------------------
// RDFDBUtils : Utils
// ----------------------------------------------------------------------------------

/** 
 * This contains utility functions used by other pages
 *
 * @version $Id: utils.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/


function createDB() { 
  global $db, $_DB, $activeDB; 

  $hostname=$_DB[$activeDB]["host"].($_DB[$activeDB]["port"]!=""?":".$_DB[$activeDB]["port"]:"");

  if($_DB[$activeDB]["type"]=='MsAccess'){
    $type='odbc';
  }else{
    $type='MySQL';
  }
  

  $db=new DbStore($type, $hostname, 
		  $_DB[$activeDB]["dbName"], 
		  $_DB[$activeDB]["username"],
		  $_DB[$activeDB]["password"]);
  
}


// Assuming db is created...
function dbInfo() { 
  global $db, $_DB, $activeDB;

  print "<table>\n";
  print "<tr><td><b>Hostname:</b></td><td>".$_DB[$activeDB]["host"]."</td></tr>\n";
  print "<tr><td><b>Port:</b></td><td>".$_DB[$activeDB]["port"]."</td></tr>\n";
  print "<tr><td><b>Database:</b></td><td>".$_DB[$activeDB]["dbName"]."</td></tr>\n";
  print "<tr><td><b>Username:</b></td><td>".$_DB[$activeDB]["username"]."</td></tr>\n";
  print "<tr><td><b>Password:</b></td><td>".$_DB[$activeDB]["password"]."</td></tr>\n";
  print "<tr><td><b># Modesl:</b></td><td>".count($db->listModels())."</td></tr>\n";
  print "</table>\n";

}


function dbLink($i) { 
  global $_DB;
  return $_DB[$i]["type"]."://".$_DB[$i]["username"].":".$_DB[$i]["password"]."@".$_DB[$i]["host"].($_DB[$i]["port"]!=""?":".$_DB[$i]["port"]:"")."/".$_DB[$i]["dbName"];
}



function nodeFromString($s) { 

  $s=stripslashes($s);

  if (preg_match("/Resource\(\"(.*)\"\)/", $s,$m)) 
    return new Resource($m[1]);

  if (preg_match("/Literal\(\"(.*)\"\)/", $s,$m))
    return new Literal($m[1]);

  if (preg_match("/bNode\(\"(.*)\"\)/", $s,$m))
    return new BlankNode($m[1]);

  
}


/** 
 This is written by someone else, although I can no longer remember who. 
 I belive it came from the php.net manual page comments.
**/

function getthroughproxy($myfiles,$proxyhost, $proxyport) {
  $errno="";
  $errstr="";
  $datei = fsockopen($proxyhost, $proxyport, &$errno, &$errstr,30); 
  if( !$datei ) {
    fclose($resultfile); 
    return array('headers'=>false,
		 'content'=>false,
		 'errno'=>$errno,
		 'errstr'=>$errstr);
    // ^^^ proxy not available
    // You'll probably want to change this with return false;
    // to use in an 
    // if($file=getthroughproxy){} manner.
    // Well, it's up to You
  } else { 
    fputs($datei,"GET $myfiles HTTP/1.0\n\n"); 
    $zeile="";
    while (!feof($datei)) 
      {
	$zeile.=fread($datei,4096);
      }
  }
  fclose($datei);
  return array('headers'=>substr($zeile,0,strpos($zeile,"\r\n\r\n")),
	       'content'=>substr($zeile,strpos($zeile,"\r\n\r\n")+4),
	       'errno'=>$errno,
	       'errstr'=>$errstr);
}


?>
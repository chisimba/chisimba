<?php

// ----------------------------------------------------------------------------------
// RAP NetAPI
// ----------------------------------------------------------------------------------

/**
 * The RAP NetAPI provides an partly implementation of the W3C member submission "RDF Net API".
 * See http://www.w3.org/Submission/2003/SUBM-rdf-netapi-20031002/
 * It allows to use RAP as RDF server providing similar functionality as the Joseki server.
 * See http://www.joseki.org/
 *
 * Installation information is found in the NetAPI tutorial in the RAP documentation.
 *
 * @version  $Id: netapi.php 341 2007-01-23 14:53:56Z cweiske $
 * @author Phil Dawes <pdawes@users.sf.net>
 * @author Chris Bizer <chris@bizer.de>
 *
 * @package netapi
 * @todo nothing
 * @access public
 */

include_once 'config.inc.php';
include_once 'fetch.php';
include_once 'rdql.php';
include_once 'spo.php';
include_once 'remove.php';
include_once 'add.php';
include_once 'sparql.php';

// getallheaders replacement
if(!function_exists('getallheaders'))
{
	function getallheaders()
	{
		if(isset($_SERVER['ALL_HTTP']))
		{
			if( $pos = strpos($this->incoming_payload,"\r\n") ){
				$lb = "\r\n";
			} elseif( $pos = strpos($this->incoming_payload,"\n\n") ){
				$lb = "\n";
			}
			// detected
			if( isset($lb) ){
				$header_array = explode($lb,$_SERVER['ALL_HTTP']);
				// clean headers
				if(is_array($header_array)){
					foreach($header_array as $header_line){
						$arr = explode(':',$header_line);
						if(count($arr) >= 2){
							$headers[trim($arr[0])] = trim($arr[1]);
						}
					}
				}
			}
		}
		// grab what headers we can from $_SERVER
		else
		{
			foreach($_SERVER as $k => $v)
			{
				if(strstr($k,'HTTP_'))
				{
					$key = str_replace('_','-',str_replace('HTTP_','',$k));
					$key = ucfirst(strtolower($key));
					for($i = 0; $i < strlen($key); $i++)
					{
						if($dash)
						{
							$key[$i] = strtoupper($key[$i]);
						}
						$dash = ($key[$i] == '-') ? true : false;
					}
					$headers[$key] = $v;
				}
			}
		}
		return isset($headers) ? $headers : array();
	}
}

/**
 * NetAPI Main
 *
 * 1. Gets the query string from the Webserver.
 * 2. Connects to the database or loads the RDF file
 * 3. Calls the appropriate query or update function.
 */

// apache mod_rewrite support
if(isset($_SERVER['REDIRECT_URL']))
{
	// Extract model name from uri and map it to model URI
	preg_match("/\/([^\/]+)$/",$_SERVER['REDIRECT_URL'],$matches);

	$model = $matches[1];
}
// iis custom 404 support
elseif(strstr($_SERVER['QUERY_STRING'],'404') || strstr($_SERVER['QUERY_STRING'],'405'))
{
	// get all from last slash
	$model = substr($_SERVER['QUERY_STRING'], strrpos($_SERVER['QUERY_STRING'],'/')+1, strlen($_SERVER['QUERY_STRING']));

	// if there are GET params
	if(strstr($model,'?'))
	{
		// strip params of model
		$model = substr($model, 0, strpos($model,'?'));
		// fix request params
		foreach($_GET as $k => $v)
		{
			if(ereg('^(404|405)',$k))
			{
				$k = substr($k, strpos($k,'?')+1);
				$_GET[$k] = $v;
				$_REQUEST[$k] = $v;
			}
		}
	}
	//  else, remove IIS redirect query string
	else
	{
		$_SERVER['QUERY_STRING'] = '';
	}
}

if ($model == NULL){
   // Didn't get modelID from URL rewriting
   header('HTTP/1.0 500 Internal Server Error');
   echo "500 - Didn't get modelURI from URL rewriting.\n";
   return;
}

$modelId = $modelmap[$model];

if ($modelId == NULL){
   // Didn't get modelID from URL rewriting
   header('HTTP/1.0 404 Not Found');
   echo "404 - Model ".$model." not found";
   return;
} elseif (substr($modelId,0,3)=="db:") {
   // Database backed model
   $modelURI = substr($modelId,3);
   $database = new DbStore($NETAPI_DB_DRIVER, $NETAPI_DB_HOST, $NETAPI_DB_DB, $NETAPI_DB_USER, $NETAPI_DB_PASS);


   $type = 'model';
   if ($database->modelExists($modelURI) == False) {
   		$type = 'dataset';
       if($database->datasetExists($modelURI)== False){
   	   	// Model not found in the database
      	 header('HTTP/1.0 404 Not Found');
      	 echo "404 - Model ".$model." not found\n";
      	 return;
       }
   }
   if($type == 'model')
   	$model1 = $database->getModel($modelURI);
   	else
   	 	$model1 = $database->getDatasetDb($modelURI);
} elseif (substr($modelId,0,5)=="file:") {
   // File backed model
   $modelURI = substr($modelId,5);
   $model1 = new MemModel();
   $model1->load($modelURI);
} else {
   	// Undefined repository type
	header('HTTP/1.0 500 Internal Server Error');
    echo "500 - I don't understand ".$modelId;
}

// Process query or update operation
if ($_SERVER['QUERY_STRING'] != ''){

  $headers = getallheaders();

  if (isset($_REQUEST['op'])) {
  	$op = $_REQUEST['op'];
	if ($op == "add"){
	  // Add Operation
	  if (NETAPI_ALLOW_ADD == True) {
	      addStatementsToModel($model1,$headers['Content-Type'],$HTTP_RAW_POST_DATA);
	  } else {
	     // Add not allowed
         header('HTTP/1.0 405 Method Not Allowed');
         echo "405 - The add method is not allowed for model ".$model."\n";
	  }
	  return;
	} elseif ($op == "remove"){
	  // Remove Operation
	  if (NETAPI_ALLOW_REMOVE == True) {
	     removeFromModel($model1,$headers['Content-Type'],$HTTP_RAW_POST_DATA);
	  } else {
	  // Remove not allowed
         header('HTTP/1.0 405 Method Not Allowed');
         echo "405 - The remove method is not allowed for model ".$model."\n";
	  }
	  return;
	} else {
	  // Undefined Operation
	  header('HTTP/1.0 405 Method Not Allowed');
      echo "405 - The ".$op." method is unknown\n";
	}
  } else {
  	// Query Operation
  	$lang = 'SPARQL';
	if(isset($_REQUEST['lang']))
  		$lang = $_REQUEST['lang'];
	if(strtoupper($lang) == "FETCH" or $lang == "http://jena.hpl.hp.com/2003/07/query/fetch"){
      // Fetch Query
      $s = getSerializerAndSetContentType();
	  fetch($model1,$s);
	  return;
	} elseif (strtoupper($lang) == "RDQL" or $lang == "http://jena.hpl.hp.com/2003/07/query/RDQL"){
      // RDQL Query
      $s = getSerializerAndSetContentType();
	  rdql($model1,$s);
	  return;
	} elseif (strtoupper($lang) == "SPARQL"){
      // SPARQL Query
      $s = getSerializerAndSetContentType();
	  sparql($model1,$s);
	  return;
	} elseif (strtoupper($lang) == "SPO" or $lang == "http://jena.hpl.hp.com/2003/07/query/SPO"){
      // SPO Query
      $s = getSerializerAndSetContentType();
	  spoQuery($model1,$s);
	  return;
	} elseif (strtoupper($lang) == "REMOVESPO"){
      // SPO Query
	  $modelId = $modelmap[$model];
      spoQuery($model1,$s=false,true,$modelId);
	  return;
	}
  }
} else {
  // Return the whole model, because no query string or operation ha been given
  $s = getSerializerAndSetContentType();
  if (is_a($model1, 'MemModel')) {
  	echo $s->Serialize($model1);
  } else {
    echo $s->Serialize($model1->getMemModel());
  }
}


/**
 * Creates the appropriate RDF serializer depending on the requested content type.
 *
 * @access  private
 */
function getSerializerAndSetContentType() {
    if (substr_count($_SERVER['HTTP_ACCEPT'],"application/n-triples") != 0){
	  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3);
      $s = new NTripleSerializer();
	  header('Content-type: application/n-triples');
	} elseif (substr_count($_SERVER['HTTP_ACCEPT'],"application/n3")!= 0){
	  header('Content-type: application/n3');
	  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3);
	  $s = new N3Serializer();
	} elseif (substr_count($_SERVER['HTTP_ACCEPT'],"application/rdf+xml") != 0){
	  header('Content-type: application/rdf+xml');
	  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
	  $s = new RdfSerializer();
	} elseif (substr_count($_SERVER['HTTP_ACCEPT'],"text/xml") != 0){
	  header('Content-type: text/xml');
	  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
	  $s = new RdfSerializer();
	} else {
	  header('Content-type: text/xml');
	  include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
	  $s = new RdfSerializer();
	}
    return $s;
}

?>

<?php

// ----------------------------------------------------------------------------------
// RAP Net API SPARQL Query Operaton
// ----------------------------------------------------------------------------------

/**
 * The SPARQL query operation operation executes a SPARQL query against a model.
 *
 *
 * @version  $Id$
 * @author Tobias Gauß <tobias.gauss@web.de>
 *
 * @package netapi
 * @todo nothing
 * @access	public
 */
 
function sparql($model,$serializer){

  if (isset($_REQUEST['closure'])) {
     if (strtoupper($_REQUEST['closure']) == "TRUE" ) {
	   $closure = True;
     } else {
  	   $closure = False;
     }
  } else {
    $closure = False;
  }  	

  $query = $_REQUEST["query"];
  // php appears to escape quotes, so unescape them
  $query = str_replace('\"','"',$query);
  $query = str_replace("\'","'",$query);
  // decode %xx
  $query =rawurldecode($query); 

  $result = $model->sparqlQuery($query,'xml');	
  if($result instanceof MemModel)
  	echo $serializer->Serialize($result);
  else
  	echo $result;


}




?>
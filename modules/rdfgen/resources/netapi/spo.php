<?php

// ----------------------------------------------------------------------------------
// RAP Net API SPO Query Operaton
// ----------------------------------------------------------------------------------

/**
 * SPO (also known as "Triples" or "find(spo)") is an experimental minimal query language. 
 * An SPO query is a single triple pattern, with optional subject (parameter "s"), predicate (parameter "p"), 
 * and object (parameter "o", if a URIref, parameter "v" for a string literal). 
 * Absence of a parameter implies "any" for matching that slot of the triple pattern.
 *
 * @version  $Id: spo.php 268 2006-05-15 05:28:09Z tgauss $
 * @author Chris Bizer <chris@bizer.de>
 * @author Phil Dawes <pdawes@users.sf.net>
 *
 * @package netapi
 * @todo Support typed literals.
 * @access	public
 */

function spoQuery($model,$serializer,$remove=false,$modelId=false){

  // Get SPO query from HTTP request
  if (isset($_REQUEST['s'])) {
	$subject = new Resource($_REQUEST['s']);
  } else {
  	$subject = NULL;
  }
  if (isset($_REQUEST['p'])) {
	$predicate = new Resource($_REQUEST['p']);
  } else {
  	$predicate = NULL;
  }
  if (isset($_REQUEST['o'])) {
	$object = new Resource($_REQUEST['o']);
  } else if (isset($_REQUEST['v'])) {
	$object = new Literal($_REQUEST['v']);
  } else {
  	$object = NULL;
  }
  if (isset($_REQUEST['closure'])) {
     if (strtoupper($_REQUEST['closure']) == "TRUE" ) {
	   $closure = True;
     } else {
  	   $closure = False;
     }
  } else {
      $closure = False;
  }

  $outm = new MemModel();
  $resultmodel = $model->find($subject, $predicate, $object);
  $it = $resultmodel->getStatementIterator();
  while ($it->hasNext()){
	$stmt = $it->next();
	if($remove)
	{
		$model->remove($stmt);
		
	}
	else
	{
		$outm->add(new Statement($stmt->subject(),$stmt->predicate(), $stmt->object()));
		if (is_a($stmt->object(),'BlankNode') && $closure == True) {
		  getBNodeClosure($stmt->object(),$model,$outm);
		}
		if (is_a($stmt->subject(),'BlankNode') && $closure == True) {
		  getBNodeClosure($stmt->subject(),$model,$outm);
		}
	}
  }
  if($remove)
  { 
  	    
	  	if(substr($modelId,0,5)=="file:"){
	  		$model->saveAs(substr($modelId,5));
	  	}
  	  	if($resultmodel->size()>0){
  	  		header('200 - OK');
	  		echo "200 - OK";
  	  	}else{
  	  		echo "No matching statements";
  	  	}
  }
  else
  {
	  echo $serializer->Serialize($outm);
  }
  $outm->close();
}

?>
<?php

// ----------------------------------------------------------------------------------
// RAP Net API RDQL Query Operaton
// ----------------------------------------------------------------------------------

/**
 * The RDQL query operation operation executes a RDQL query against a model.
 *
 * @version  $Id: rdql.php 268 2006-05-15 05:28:09Z tgauss $
 * @author Phil Dawes <pdawes@users.sf.net>
 *
 * @package netapi
 * @todo nothing
 * @access	public
 */
 
function rdql($model,$serializer){

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

  /// debug
  //var_dump($query);
  	
  $r = new RdqlParser();
  $parsedq = $r->parseQuery($query);
  addMissingSelectVarsToQuery($parsedq);
  
  if (is_a($model, 'MemModel')) {
  	$eng = new RdqlMemEngine();
  } else {
      $eng = new RdqlDbEngine();
  }
  $rdqlres = $eng->queryModel($model,$parsedq,TRUE);
  $rdqlIter = new RdqlResultIterator($rdqlres);

  $outm = new MemModel();
  while ($rdqlIter->hasNext()) {
	$result=$rdqlIter->next();
	generateResultStatementsFromVarResult($result,$parsedq,$outm,$closure,$model);	
  }	  

  echo $serializer->Serialize($outm);

  $outm->close();

}


//
// What about bNodes? The bNode closure should also be added.
//

function generateResultStatementsFromVarResult(&$result,$parsedq, &$outm,$closure, &$model){
	foreach ($parsedq['patterns'] as $n => $pattern) {
	  
	  if (substr($pattern['subject']['value'], 0, 1) == '?')
		 $subj = $result[$pattern['subject']['value']];	  
	  else
		$subj = new Resource($pattern['subject']['value']);
	  
	  if (substr($pattern['predicate']['value'], 0, 1) == '?')
		 $pred = $result[$pattern['predicate']['value']];	  
	  else
		$pred = new Resource($pattern['predicate']['value']);			 

	  if (substr($pattern['object']['value'], 0, 1) == '?')
		 $obj = $result[$pattern['object']['value']];	  
	  else {	    	 		  
		if ($pattern['object']['is_literal']){
		  $obj = new Literal($pattern['object']['value']);
		  $obj->setDatatype($pattern['object']['l_dtype']);
		  $obj->setLanguage($pattern['object']['l_lang']);
		} else {
		  $obj = new Resource($pattern['object']['value']);
		}
	  }
	  
	  $stmt = new Statement($subj,$pred,$obj);
	  
	  // bNode closure
	  if (is_a($stmt->object(),'BlankNode') && $closure == True) {
	     getBNodeClosure($stmt->object(),$model,$outm);
	  }
	  if (is_a($stmt->subject(),'BlankNode') && $closure == True) {
	     getBNodeClosure($stmt->subject(),$model,$outm);
	  }  
	  // Add statement to model
	  $outm->add($stmt);
	}
}

// If there are variables used in the pattern but not 
// in the select clause, add them to the select clause
function addMissingSelectVarsToQuery(&$parsedq){
  foreach ($parsedq['patterns'] as $n => $pattern) {
	foreach ($pattern as $key => $val_1)
	  if ($val_1['value']{0}=='?') {
		if (!in_array($val_1['value'],$parsedq['selectVars'])){
		  array_push($parsedq['selectVars'],$val_1['value']);
		}
	  }
  }
}

?>
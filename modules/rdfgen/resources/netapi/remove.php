<?php

// ----------------------------------------------------------------------------------
// RAP Net API Remove Operaton
// ----------------------------------------------------------------------------------

/**
 * The remove operation allows the user to delete statements from a model on the server.
 *
 *
 * @version  $Id: remove.php 268 2006-05-15 05:28:09Z tgauss $
 * @author Phil Dawes <pdawes@users.sf.net>
 *
 * @package netapi
 * @todo nothing
 * @access	public
 */
 
function removeFromModel($model,$contenttype,$postdata){
  $p = getParser($contenttype);
  $m = $p->parse2model($postdata);
  $it = $m->getStatementIterator();
  while ($it->hasNext()){
	$statement = $it->next();
	$model->remove($statement);
  }
    echo "200 - The data has been removed from the model.";
}

function getParser($contenttype){
  if ($contenttype == "application/n-triples"){
	$p = new N3Parser();
  } elseif ($contenttype == "application/n3"){
	$p = new N3Parser();
  } elseif ($contenttype == "application/rdf+xml"){
	$p = new RdfParser();
  } else {
  	header('HTTP/1.0 415 Unsupported Media Type'); 
  	die("415 - I don't understand content type. Accepted content types are application/n-triples, application/n3, application/rdf+xml.");
  }

  return $p;
}

?>

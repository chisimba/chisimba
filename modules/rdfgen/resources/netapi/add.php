<?php

// ----------------------------------------------------------------------------------
// RAP Net API Add Operaton
// ----------------------------------------------------------------------------------

/**
 * The add operation allows the user to add statements to a model on the server.
 *
 * @version  $Id: add.php 268 2006-05-15 05:28:09Z tgauss $
 * @author Phil Dawes <pdawes@users.sf.net>
 *
 * @package netapi
 * @todo nothing
 * @access	public
 */

function addStatementsToModel($model,$contenttype,$postdata){
  $p = getParser($contenttype);
  $m = $p->parse2model($postdata);
  $it = $m->getStatementIterator();
  while ($it->hasNext()){
	$statement = $it->next();
	$model->add($statement);
  }
  echo "200 - The data has been added to the model.";
}

?>
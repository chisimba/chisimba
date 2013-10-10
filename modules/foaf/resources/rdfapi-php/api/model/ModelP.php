<?php
// ----------------------------------------------------------------------------------
// Model
// ----------------------------------------------------------------------------------
//
// Description               : Model package
//
//
// Author: Tobias Gauß	<tobias.gauss@web.de>
//
// ----------------------------------------------------------------------------------

// Include Model classes
require_once( RDFAPI_INCLUDE_DIR . 'model/Node.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/Literal.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/Resource.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/Blanknode.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/Statement.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/Model.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/MemModel.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/DbStore.php' );
require_once( RDFAPI_INCLUDE_DIR . 'util/StatementIterator.php' );
require_once( RDFAPI_INCLUDE_DIR . 'model/ModelFactory.php' );
require_once( RDFAPI_INCLUDE_DIR . 'sparql/SparqlClient.php' );
require_once( RDFAPI_INCLUDE_DIR . 'sparql/ClientQuery.php' );

?>
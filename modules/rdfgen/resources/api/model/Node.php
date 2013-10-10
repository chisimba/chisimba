<?php
require_once RDFAPI_INCLUDE_DIR . '/util/Object.php';

// ----------------------------------------------------------------------------------
// Class: Node
// ----------------------------------------------------------------------------------

/**
 * An abstract RDF node.
 * Can either be resource, literal or blank node.
 * Node is used in some comparisons like is_a($obj, "Node"),
 * meaning is $obj a resource, blank node or literal.
 *
 *
 * @version $Id: Node.php 348 2007-03-12 10:04:10Z cweiske $
 * @author Chris Bizer <chris@bizer.de>
 * @package model
 * @abstract
 *
 */
 class Node extends Object {
 } // end:RDFNode


?>
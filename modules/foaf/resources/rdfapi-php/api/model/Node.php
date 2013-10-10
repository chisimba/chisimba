<?php
require_once RDFAPI_INCLUDE_DIR . '/util/Object_rap.php';

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
 * @version $Id: Node.php 7228 2007-09-27 06:24:51Z kudakwashe $
 * @author Chris Bizer <chris@bizer.de>
 * @package model
 * @abstract
 *
 */
 class Node extends Object_rap {
 } // end:RDFNode


?>
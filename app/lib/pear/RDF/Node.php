<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Node
// ----------------------------------------------------------------------------------
/**
 * An abstract RDF node.
 * Can either be resource, literal or blank node.
 * Node is used in some comparisons like is_a($obj, 'RDF_Node'),
 * meaning is $obj a resource, blank node or literal.
 *
 * @version V0.7
 * @author Chris Bizer <chris@bizer.de>
 * @todo nothing
 * @package model
 * @abstract
 */
class RDF_Node extends RDF_Object
{

} // end:RDFNode

?>
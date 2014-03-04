<?php
/**
*   RDF Vocabulary Description Language 1.0: RDF Schema (RDFS) Vocabulary (Resource)
*
*   @version $Id: RDFS_C.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Daniel Westphal (dawe@gmx.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the
*   RDF Schema (RDFS).
*   For details about RDFS see: http://www.w3.org/TR/rdf-schema/.
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
class RDFS{

	function RESOURCE()
	{
		return  new Resource(RDF_SCHEMA_URI . 'Resource');

	}

	function LITERAL()
	{
		return  new Resource(RDF_SCHEMA_URI . 'Literal');

	}

	function RDFS_CLASS()
	{
		return  new Resource(RDF_SCHEMA_URI . 'Class');

	}

	function DATATYPE()
	{
		return  new Resource(RDF_SCHEMA_URI . 'Datatype');

	}

	function CONTAINER()
	{
		return  new Resource(RDF_SCHEMA_URI . 'Container');

	}

	function CONTAINER_MEMBERSHIP_PROPERTY()
	{
		return  new Resource(RDF_SCHEMA_URI . 'ContainerMembershipProperty');

	}

	function SUB_CLASS_OF()
	{
		return  new Resource(RDF_SCHEMA_URI . 'subClassOf');

	}

	function SUB_PROPERTY_OF()
	{
		return  new Resource(RDF_SCHEMA_URI . 'subPropertyOf');

	}

	function DOMAIN()
	{
		return  new Resource(RDF_SCHEMA_URI . 'domain');

	}

	function RANGE()
	{
		return  new Resource(RDF_SCHEMA_URI . 'range');

	}

	function LABEL()
	{
		return  new Resource(RDF_SCHEMA_URI . 'label');

	}

	function COMMENT()
	{
		return  new Resource(RDF_SCHEMA_URI . 'comment');

	}

	function MEMBER()
	{
		return  new Resource(RDF_SCHEMA_URI . 'member');

	}

	function SEEALSO()
	{
		return  new Resource(RDF_SCHEMA_URI . 'seeAlso');

	}

	function IS_DEFINED_BY()
	{
		return  new Resource(RDF_SCHEMA_URI . 'isDefinedBy');
	}

}
?>
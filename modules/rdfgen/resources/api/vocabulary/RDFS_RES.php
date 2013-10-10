<?php
/**
*   RDF Vocabulary Description Language 1.0: RDF Schema (RDFS) Vocabulary (ResResource)
*
*   @version $Id: RDFS_RES.php 431 2007-05-01 15:49:19Z cweiske $
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
class RDFS_RES{

	function RESOURCE()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'Resource');

	}

	function LITERAL()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'Literal');

	}

	function RDFS_CLASS()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'Class');

	}

	function DATATYPE()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'Datatype');

	}

	function CONTAINER()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'Container');

	}

	function CONTAINER_MEMBERSHIP_PROPERTY()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'ContainerMembershipProperty');

	}

	function SUB_CLASS_OF()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'subClassOf');

	}

	function SUB_PROPERTY_OF()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'subPropertyOf');

	}

	function DOMAIN()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'domain');

	}

	function RANGE()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'range');

	}

	function LABEL()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'label');

	}

	function COMMENT()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'comment');

	}

	function MEMBER()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'member');

	}

	function SEEALSO()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'seeAlso');

	}

	function IS_DEFINED_BY()
	{
		return  new ResResource(RDF_SCHEMA_URI . 'isDefinedBy');
	}
}
?>
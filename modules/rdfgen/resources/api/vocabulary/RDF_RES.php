<?php
/**
*   Resource Description Framework (RDF) Vocabulary (ResResource)
*
*   @version $Id: RDF_RES.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Daniel Westphal (dawe@gmx.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the
*   Resource Description Framework (RDF).
*   For details about RDF see: http://www.w3.org/RDF/.
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
class RDF_RES{

	// RDF concepts (constants are defined in constants.php)
	function ALT()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_ALT);

	}

	function BAG()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_BAG);

	}

	function PROPERTY()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_PROPERTY);

	}

	function SEQ()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_SEQ);

	}

	function STATEMENT()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_STATEMENT);

	}

	function RDF_LIST()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_LIST);

	}

	function NIL()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_NIL);

	}

	function TYPE()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_TYPE);

	}

	function REST()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_REST);

	}

	function FIRST()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_FIRST);

	}

	function SUBJECT()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_SUBJECT);

	}

	function PREDICATE()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_PREDICATE);

	}

	function OBJECT()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_OBJECT);

	}

	function DESCRIPTION()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_DESCRIPTION);

	}

	function ID()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_ID);

	}

	function ABOUT()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_ABOUT);

	}

	function ABOUT_EACH()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_ABOUT_EACH);

	}

	function ABOUT_EACH_PREFIX()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_ABOUT_EACH_PREFIX);

	}

	function BAG_ID()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_BAG_ID);

	}

	function RESOURCE()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_RESOURCE);

	}

	function PARSE_TYPE()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE);

	}

	function LITERAL()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_LITERAL);

	}

	function PARSE_TYPE_RESOURCE()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_RESOURCE);

	}

	function LI()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_LI);

	}

	function NODE_ID()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_NODEID);

	}

	function DATATYPE()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_DATATYPE);

	}

	function SEE_ALSO()
	{
		return  new ResResource(RDF_NAMESPACE_URI . RDF_SEEALSO);
	}
}


?>
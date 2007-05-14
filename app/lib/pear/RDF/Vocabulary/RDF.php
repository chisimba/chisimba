<?php 
// ----------------------------------------------------------------------------------
// Resource Description Framework (RDF) Vocabulary
// ----------------------------------------------------------------------------------
// Version                   : 0.3
// Authors                   : Daniel Westphal (dawe@gmx.de)

// Description               : Wrapper, defining resources for all terms of the
// Resource Description Framework (RDF).
// For details about RDF see: http://www.w3.org/RDF/.
// Using the wrapper allows you to define all aspects of
// the vocabulary in one spot, simplifing implementation and
// maintainence. Working with the vocabulary, you should use
// these resources as shortcuts in your code.
// 
// RDF concepts (constants are defined in constants.php)
$RDF_Alt =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_ALT);
$RDF_Bag =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_BAG);
$RDF_Property =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_PROPERTY);
$RDF_Seq =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_SEQ);
$RDF_Statement =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_STATEMENT);
$RDF_List =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_LIST);
$RDF_nil =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_NIL);
$RDF_type =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_TYPE);
$RDF_rest =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_REST);
$RDF_first =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_FIRST);
$RDF_subject =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_SUBJECT);
$RDF_predicate =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_PREDICATE);
$RDF_object =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_OBJECT);
$RDF_Description =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_DESCRIPTION);
$RDF_ID =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_ID);
$RDF_about =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_ABOUT);
$RDF_aboutEach =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_ABOUT_EACH);
$RDF_aboutEachPrefix =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_ABOUT_EACH_PREFIX);
$RDF_bagID =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_BAG_ID);
$RDF_resource =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_RESOURCE);
$RDF_parseType =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_PARSE_TYPE);
$RDF_Literal =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_LITERAL);
$RDF_Resource =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_RESOURCE);
$RDF_li =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_LI);
$RDF_nodeID =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_NODEID);
$RDF_datatype =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_DATATYPE);
$RDF_seeAlso =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_SEEALSO);

?>
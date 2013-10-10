<?php
/**
*   Resource Description Framework (RDF) Vocabulary
*
*   @version $Id: RDF.php 431 2007-05-01 15:49:19Z cweiske $
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


// RDF concepts (constants are defined in constants.php)
$RDF_Alt = new Resource(RDF_NAMESPACE_URI . RDF_ALT);
$RDF_Bag = new Resource(RDF_NAMESPACE_URI . RDF_BAG);
$RDF_Property = new Resource(RDF_NAMESPACE_URI . RDF_PROPERTY);
$RDF_Seq = new Resource(RDF_NAMESPACE_URI . RDF_SEQ);
$RDF_Statement = new Resource(RDF_NAMESPACE_URI . RDF_STATEMENT);
$RDF_List = new Resource(RDF_NAMESPACE_URI . RDF_LIST);
$RDF_nil = new Resource(RDF_NAMESPACE_URI . RDF_NIL);
$RDF_type = new Resource(RDF_NAMESPACE_URI . RDF_TYPE);
$RDF_rest = new Resource(RDF_NAMESPACE_URI . RDF_REST);
$RDF_first = new Resource(RDF_NAMESPACE_URI . RDF_FIRST);
$RDF_subject = new Resource(RDF_NAMESPACE_URI . RDF_SUBJECT);
$RDF_predicate = new Resource(RDF_NAMESPACE_URI . RDF_PREDICATE);
$RDF_object = new Resource(RDF_NAMESPACE_URI . RDF_OBJECT);
$RDF_Description = new Resource(RDF_NAMESPACE_URI . RDF_DESCRIPTION);
$RDF_ID = new Resource(RDF_NAMESPACE_URI . RDF_ID);
$RDF_about = new Resource(RDF_NAMESPACE_URI . RDF_ABOUT);
$RDF_aboutEach = new Resource(RDF_NAMESPACE_URI . RDF_ABOUT_EACH);
$RDF_aboutEachPrefix = new Resource(RDF_NAMESPACE_URI . RDF_ABOUT_EACH_PREFIX);
$RDF_bagID = new Resource(RDF_NAMESPACE_URI . RDF_BAG_ID);
$RDF_resource = new Resource(RDF_NAMESPACE_URI . RDF_RESOURCE);
$RDF_parseType = new Resource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE);
$RDF_Literal = new Resource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_LITERAL);
$RDF_Resource = new Resource(RDF_NAMESPACE_URI . RDF_PARSE_TYPE_RESOURCE);
$RDF_li = new Resource(RDF_NAMESPACE_URI . RDF_LI);
$RDF_nodeID = new Resource(RDF_NAMESPACE_URI . RDF_NODEID);
$RDF_datatype = new Resource(RDF_NAMESPACE_URI . RDF_DATATYPE);
$RDF_seeAlso = new Resource(RDF_NAMESPACE_URI . RDF_SEEALSO);



?>
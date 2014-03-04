<?php
// ----------------------------------------------------------------------------------
// Constants
// ----------------------------------------------------------------------------------
// @version                  : $Id: constants.php 558 2008-02-29 15:14:04Z cax $
// Authors                   : Chris Bizer (chris@bizer.de),
//                             Daniel Westphal (dawe@gmx.de),
//							   Leandro Mariano Lopez (llopez@xinergiaargentina.com),
//                             Radoslaw Oldakowski (radol@gmx.de)
//
// Description               : Constants and default configuration
// ----------------------------------------------------------------------------------



// ----------------------------------------------------------------------------------
// General
// ----------------------------------------------------------------------------------

define('RDFAPI_ERROR', 'RDFAPI error ');
define('DEFAULT_ALGORITHM', 'MD5');
define('DEFAULT_ENCODING', 'UTF-8');
define('INDENTATION', '   ');
define('LINEFEED', chr(10));

// ----------------------------------------------------------------------------------
// RAP Packages
// ----------------------------------------------------------------------------------
define('PACKAGE_MODEL','model/ModelP.php');
define('PACKAGE_UTILITY','util/Utility.php');
define('PACKAGE_DBASE','model/DBase.php');
define('PACKAGE_SYNTAX_RDF','syntax/SyntaxRDF.php');
define('PACKAGE_SYNTAX_N3','syntax/SyntaxN3.php');
define('PACKAGE_SYNTAX_JSON','syntax/SyntaxJSON.php');
define('PACKAGE_SYNTAX_GRDDL','syntax/SyntaxGRDDL.php');
define('PACKAGE_VOCABULARY','vocabulary/Vocabulary.php');
define('PACKAGE_RDQL','rdql/RDQL.php');
define('PACKAGE_INFMODEL','infModel/InfModelP.php');
define('PACKAGE_RESMODEL','resModel/ResModelP.php');
define('PACKAGE_ONTMODEL','ontModel/OntModelP.php');
define('PACKAGE_DATASET','dataset/DatasetP.php');
define('PACKAGE_SPARQL','sparql/SPARQL.php');
define('PACKAGE_SYNTAX_RSS','syntax/SyntaxRSS.php');
define('PACKAGE_SYNTAX_SPARQLRES','syntax/SyntaxSparqlRes.php');

// ----------------------------------------------------------------------------------
// Model
// ----------------------------------------------------------------------------------

// Defines a prefix used in the ID of automatically created bNodes.
define('BNODE_PREFIX', 'bNode');

// Sets the index of MemModell:
// IND_DEF: Defaultindex over subject, predicate, obeject seperate.
// IND_SPO: Index over subject+predicate+object.
// IND_SP:  Index over subject+predicate.
// IND_SO:  Index over subject+object.
define('NO_INDEX',-1);
define('IND_DEF',0);
define('IND_SPO',1);
define('IND_SP',2);
define('IND_SO',3);
define('INDEX_TYPE',IND_DEF);

// ----------------------------------------------------------------------------------
// ModelFactory
// ----------------------------------------------------------------------------------

define ('MEMMODEL','MemModel');
define ('DBMODEL','DbModel');
define ('INFMODELF','InfModelF');
define ('INFMODELB','InfModelB');
define ('ONTMODEL','OntModel');
define ('RESMODEL','ResModel');
define ('RDFS_VOCABULARY','RdfsVocabulary.php');

// ----------------------------------------------------------------------------------
// Parser
// ----------------------------------------------------------------------------------

// RdfParser: Set this option to false if you want to use IDs containing CombiningChars or
// Extenders (see http://www.w3.org/TR/REC-xml-names/#NT-NCName). If set to TRUE, they're assumed to be invalid.
define('VALIDATE_IDS', TRUE);

// RdfParser: Set this option to true if you want to parse UNICODE documents.
// WARNING: Setting the option TRUE significantly slows down the RDF-parser.
define('UNIC_RDF', FALSE);

// RdfParser: Set this option to true if you want to make sure that the created RDF-model doesnt contain
// duplicate RDF-statements. WARNING: Setting the option TRUE significantly slows down the RDF-parser.
define('CREATE_MODEL_WITHOUT_DUPLICATES', FALSE);

// N3 and N-Triple-Parser: Set this option to true in order to override the given bnode
// labels and rename them to the defined BNODE_PREFIX
define('FIX_BLANKNODES', TRUE);

define('NAMESPACE_SEPARATOR_CHAR','^');
define('NAMESPACE_SEPARATOR_STRING','^');
define('IN_TOP_LEVEL',0);
define('IN_RDF',1);
define('IN_DESCRIPTION',2);
define('IN_PROPERTY_UNKNOWN_OBJECT',3);
define('IN_PROPERTY_RESOURCE',4);
define('IN_PROPERTY_EMPTY_RESOURCE',5);
define('IN_PROPERTY_LITERAL',6);
define('IN_PROPERTY_PARSE_TYPE_LITERAL',7);
define('IN_PROPERTY_PARSE_TYPE_RESOURCE',8);
define('IN_XML',9);
define('IN_UNKNOWN',10);
define('IN_PROPERTY_PARSE_TYPE_COLLECTION', 11);
define('RDF_SUBJECT_TYPE_URI',0);
define('RDF_SUBJECT_TYPE_DISTRIBUTED',1);
define('RDF_SUBJECT_TYPE_PREFIX',2);
define('RDF_SUBJECT_TYPE_ANONYMOUS',3);
define('RDF_SUBJECT_TYPE_BNODE',4);
define('RDF_OBJECT_TYPE_RESOURCE',0);
define('RDF_OBJECT_TYPE_LITERAL',1);
define('RDF_OBJECT_TYPE_XML',2);
define('RDF_OBJECT_TYPE_BNODE',3);

// ----------------------------------------------------------------------------------
// Serializer
// ----------------------------------------------------------------------------------

// RDF, N3, N-Triple Serializer: set to TRUE in oder to suppres the "Generated by RAP"
// comment in the output files.
define('HIDE_ADVERTISE',FALSE);

// RDF Serializer: Set to TRUE, if the serializer should use entities for URIs.
define('SER_USE_ENTITIES', FALSE );

// RDF Serializer: Set to TRUE, if the serializer should serialize triples as XML
// attributes where possible.
define('SER_USE_ATTRIBUTES', FALSE );

// RDF Serializer: Set to TRUE in order to sort the statements of a model before
// serializing them.
define('SER_SORT_MODEL', FALSE );



// RDF Serializer: Set to TRUE, if the serializer should use qualified names for RDF
// reserved words.
// NOTE: There is only one default namespace allowed within an XML document.
//       Therefore if SER_RDF_QNAMES in is set to FALSE and you pass the parameter
//       $xml_default_namespace to the method serialize() of class RdfSerializer,
//       the model will be serialized as if SER_RDF_QNAMES were set to TRUE.
define('SER_RDF_QNAMES', TRUE );

// RDF Serializer: Set to TRUE, if the serializer should start documents with the
// xml declaration <?xml version="1.0" encoding="UTF-8" >.
define('SER_XML_DECLARATION', TRUE );

// N3 Serializer: Set to TRUE, if the N3 serializer should try to compress the blank node
// syntax using [] whereever possible.
define('N3SER_BNODE_SHORT', FALSE);

// RDF Serializer: Set to TRUE, if the serializer should write text values always as
// escaped CDATA.
define('USE_CDATA', FALSE);

define('USE_ANY_QUOTE', FALSE);
define('GENERAL_PREFIX_BASE','ns');
define('MAX_ALLOWED_ABBREVIATED_LENGTH',60);

// ----------------------------------------------------------------------------------
// Util
// ----------------------------------------------------------------------------------

// Definition of the colors used by the method RDFUtil:writeHTMLTable
define('HTML_TABLE_HEADER_COLOR', '#FFFFFF');
define('HTML_TABLE_RESOURCE_COLOR', '#FFFFCC');
define('HTML_TABLE_LITERAL_COLOR', '#E7E7EF');
define('HTML_TABLE_BNODE_COLOR', '#FFCCFF');
define('HTML_TABLE_RDF_NS_COLOR', '#CCFFCC');
define('HTML_TABLE_NS_ROW_COLOR1', '#FFFFFF');
define('HTML_TABLE_NS_ROW_COLOR0', '#E7E7EF');

// ----------------------------------------------------------------------------------
// RDF
// ----------------------------------------------------------------------------------

define('RDF_NAMESPACE_URI','http://www.w3.org/1999/02/22-rdf-syntax-ns#' );
define('RDF_NAMESPACE_PREFIX','rdf' );
define('RDF_RDF','RDF');
define('RDF_DESCRIPTION','Description');
define('RDF_ID','ID');
define('RDF_ABOUT','about');
define('RDF_ABOUT_EACH','aboutEach');
define('RDF_ABOUT_EACH_PREFIX','aboutEachPrefix');
define('RDF_BAG_ID','bagID');
define('RDF_RESOURCE','resource');
define('RDF_VALUE','value');
define('RDF_PARSE_TYPE','parseType');
define('RDF_PARSE_TYPE_LITERAL','Literal');
define('RDF_PARSE_TYPE_RESOURCE','Resource');
define('RDF_PARSE_TYPE_COLLECTION', 'Collection');
define('RDF_TYPE','type');
define('RDF_BAG','Bag');
define('RDF_SEQ','Seq');
define('RDF_ALT','Alt');
define('RDF_LI','li');
define('RDF_STATEMENT','Statement');
define('RDF_SUBJECT','subject');
define('RDF_PREDICATE','predicate');
define('RDF_OBJECT','object');
define('RDF_NODEID','nodeID');
define('RDF_DATATYPE','datatype');
define('RDF_SEEALSO','seeAlso');
define('RDF_PROPERTY','Property');
define('RDF_LIST','List');
define('RDF_NIL','nil');
define('RDF_REST','rest');
define('RDF_FIRST','first');
define('RDF_XMLLITERAL', 'XMLLiteral');

// ----------------------------------------------------------------------------------
// RDF Schema
// ----------------------------------------------------------------------------------

define('RDF_SCHEMA_URI','http://www.w3.org/2000/01/rdf-schema#' );
define('RDF_DATATYPE_SCHEMA_URI','http://www.w3.org/TR/xmlschema-2' );
define('RDF_SCHEMA_PREFIX', 'rdfs');
define('RDFS_SUBCLASSOF','subClassOf');
define('RDFS_SUBPROPERTYOF','subPropertyOf');
define('RDFS_RANGE','range');
define('RDFS_DOMAIN','domain');
define('RDFS_CLASS','Class');
define('RDFS_RESOURCE','Resource');
define('RDFS_DATATYPE','Datatype');
define('RDFS_LITERAL','Literal');
define('RDFS_SEE_ALSO','seeAlso');
define('RDFS_IS_DEFINED_BY','isDefinedBy');
define('RDFS_LABEL','label');
define('RDFS_COMMENT','comment');


// ----------------------------------------------------------------------------------
// OWL
// ----------------------------------------------------------------------------------

define('OWL_URI','http://www.w3.org/2002/07/owl#' );
define('OWL_PREFIX', 'owl');
define('OWL_SAME_AS','sameAs');
define('OWL_INVERSE_OF','inverseOf');


// ----------------------------------------------------------------------------------
// XML
// ----------------------------------------------------------------------------------

define('XML_NAMESPACE_PREFIX', 'xml');
define('XML_NAMESPACE_DECLARATION_PREFIX', 'xmlns');
define('XML_NAMESPACE_URI','http://www.w3.org/XML/1998/namespace' );
define('XML_LANG','lang');
define('DATATYPE_SHORTCUT_PREFIX','datatype:');
define('XML_SCHEMA','http://www.w3.org/2001/XMLSchema#');

// ----------------------------------------------------------------------------------
// RDF DATATYPE SHORTCUTS (extends datatype shortcuts to the full XML datatype URIs)
// ----------------------------------------------------------------------------------

$short_datatype = array(
    'STRING'    => RDF_DATATYPE_SCHEMA_URI . '#string',
    'DECIMAL'   => RDF_DATATYPE_SCHEMA_URI . '#decimal',
    'INTEGER'   => RDF_DATATYPE_SCHEMA_URI . '#integer',
    'INT'       => RDF_DATATYPE_SCHEMA_URI . '#int',
    'SHORT'     => RDF_DATATYPE_SCHEMA_URI . '#short',
    'BYTE'      => RDF_DATATYPE_SCHEMA_URI . '#byte',
    'LONG'      => RDF_DATATYPE_SCHEMA_URI . '#long',
    'LANGUAGE'  => RDF_DATATYPE_SCHEMA_URI . '#language',
    'NAME'      => RDF_DATATYPE_SCHEMA_URI . '#name'
);

// ----------------------------------------------------------------------------------
// Database
// ----------------------------------------------------------------------------------

if (!defined('ADODB_DB_DRIVER')) {
    if (isset($GLOBALS['dbConf'])) {
        define('ADODB_DB_DRIVER', $GLOBALS['dbConf']['type']);
        define('ADODB_DB_HOST'  , $GLOBALS['dbConf']['host']);
        define('ADODB_DB_NAME'  , $GLOBALS['dbConf']['database']);
        define('ADODB_DB_USER'  , $GLOBALS['dbConf']['user']);
        define('ADODB_DB_PASSWORD', $GLOBALS['dbConf']['password']);
    } else {
        define('ADODB_DB_DRIVER', 'mysql');
        define('ADODB_DB_HOST'  , 'localhost');
        define('ADODB_DB_NAME'  , 'kbrdfdb');
        define('ADODB_DB_USER'  , 'kbrdf');
        define('ADODB_DB_PASSWORD', 'db1');
    }
}
if (!defined('ADODB_DEBUG_MODE')) {
    define('ADODB_DEBUG_MODE', '0');
}


// ----------------------------------------------------------------------------------
// RDQL Error Messages
// ----------------------------------------------------------------------------------

define('RDQL_ERR','RDQL error ');
define('RDQL_SYN_ERR','RDQL syntax error ');
define('RDQL_SEL_ERR', RDQL_ERR .'in the SELECT clause: ');
define('RDQL_SRC_ERR', RDQL_ERR .'in the SOURCE clause: ');
define('RDQL_WHR_ERR', RDQL_ERR .'in the WHERE clause: ');
define('RDQL_AND_ERR', RDQL_ERR .'in the AND clause: ');
define('RDQL_USG_ERR', RDQL_ERR .'in the USING clause: ');


// ----------------------------------------------------------------------------------
// Vocabulary
// ----------------------------------------------------------------------------------
// namespace declarations
define('ATOM_NS'    , 'http://purl.org/atom/ns#');
define('DC_NS'      , 'http://purl.org/dc/elements/1.1/');
define('DCTERM_NS'  , 'http://purl.org/dc/terms/');
define('DCMITYPE_NS', 'http://purl.org/dc/dcmitype/');
define('FOAF_NS'    , 'http://xmlns.com/foaf/0.1/');
define('OWL_NS'     , 'http://www.w3.org/2002/07/owl#');
define('RSS_NS'     , 'http://purl.org/rss/1.0/');
define('VCARD_NS'   , 'http://www.w3.org/2001/vcard-rdf/3.0#');



// ----------------------------------------------------------------------------------
// RDQL, SPARQL and parser default namespace prefixes
// ----------------------------------------------------------------------------------

global $default_prefixes;
$default_prefixes = array(
   RDF_NAMESPACE_PREFIX => RDF_NAMESPACE_URI,
   RDF_SCHEMA_PREFIX => RDF_SCHEMA_URI,
   'xsd'  => 'http://www.w3.org/2001/XMLSchema#',
   OWL_PREFIX => OWL_URI,
   'dc' => 'http://purl.org/dc/elements/1.1/',
   'dcterms' => 'http://purl.org/dc/terms/',
   'vcard' => 'http://www.w3.org/2001/vcard-rdf/3.0#'
);



// ----------------------------------------------------------------------------------
// InfModel
// ----------------------------------------------------------------------------------

//activate / deactivate reasoning for the following schema constructs
//rdfs:subclass
define('INF_RES_SUBCLASSOF',true);
//rdfs:subproperty
define('INF_RES_SUBPROPERTYOF',true);
//rdfs:range
define('INF_RES_RANGE',true);
//rdfs:domain
define('INF_RES_DOMAIN',true);
//owl:sameAs
define('INF_RES_OWL_SAMEAS',true);
//owl:inverseOf
define('INF_RES_OWL_INVERSEOF',true);

//generic RDFS Rules from the RDF Schema doc:
//see: http://www.w3.org/TR/2004/REC-rdf-mt-20040210/#RDFSRules
define('INF_RES_RULE_RDFS12',false);
define('INF_RES_RULE_RDFS6',false);
define('INF_RES_RULE_RDFS8',false);
define('INF_RES_RULE_RDFS10',false);
define('INF_RES_RULE_RDFS13',false);

// ----------------------------------------------------------------------------------
// GRAPHVIZ
// ----------------------------------------------------------------------------------

// path to the dot binary
define('GRAPHVIZ_PATH', 'C:\/Programme\/ATT\/Graphviz\/bin\/dot.exe');

// directory for temporary files
// Attention: must be write-/readable by the webserver
define('GRAPHVIZ_TEMP', 'C:\/');

// display statistical data in generated images
// currently only number of statements drawn
define('GRAPHVIZ_STAT', TRUE);

// allowed file formats
// for security reasons (to prevent code injection)
define('GRAPHVIZ_FORMAT', 'svg, dot, jpg, png, gif, vrml');

// enable clickable URIs
// only supported by certain formats (e.g. SVG)
define('GRAPHVIZ_URI', FALSE);

// define parameters for the graphical output
// if a paramter is undefined, the default value of graphviz is used
// for further information see: http://www.graphviz.org/Documentation.php
$graphviz_param = array(
   'GRAPH_STYLE'     => 'rankdir="TB"',
   'RESOURCE_STYLE'  => 'style="filled",color="#FFD800",fontname="Courier",fontsize="10"',
   'LITERAL_STYLE'   => 'shape="box",style="filled",color="#B7FFAF",fontname="Courier",fontsize="10"',
   'PREDICATE_STYLE' => 'fontname="Courier",fontsize="10"',
   'INFERRED_STYLE'  => 'style="dotted",fontname="Courier",fontsize="10"',
   'BLANKNODE_STYLE' => 'style="filled",color="#DDDDDD",fontname="Courier",fontsize="10"',
   'BOX_STYLE'       => 'fontname="Courier",fontsize="8",color="#BBBBBB"'
);


// ----------------------------------------------------------------------------------
// SPARQL
// ----------------------------------------------------------------------------------

// Attribute lists for describing Resources. ['resource type'] = array(att1, att2, att3, ...).
//

$sparql_describe = array(
	FOAF_NS.'person' => array(FOAF_NS.'name',FOAF_NS.'homepage',FOAF_NS.'mbox')
);

?>

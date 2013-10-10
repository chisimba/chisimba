<?php
if(!defined('MORIARTY_DIR')) define('MORIARTY_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR ); 
if(!defined('MORIARTY_ARC_DIR')) define('MORIARTY_ARC_DIR', dirname(dirname(__FILE__))  . DIRECTORY_SEPARATOR . 'arc_2008_01_07' . DIRECTORY_SEPARATOR); 
if (!defined('MORIARTY_TEST_DIR') ) define('MORIARTY_TEST_DIR', MORIARTY_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR);
if (!defined('MORIARTY_PHPUNIT_DIR') ) define('MORIARTY_PHPUNIT_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'phpunit' . DIRECTORY_SEPARATOR);

define('RDF_TYPE', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
define('RDF_SUBJECT', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#subject');
define('RDF_PREDICATE', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate');
define('RDF_OBJECT', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#object');
define('RDF_STATEMENT', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement');

define('RDFS_LABEL', 'http://www.w3.org/2000/01/rdf-schema#label');
define('RDFS_COMMENT', 'http://www.w3.org/2000/01/rdf-schema#comment');

define('FOAF_DOCUMENT', 'http://xmlns.com/foaf/0.1/Document');
define('FOAF_ISPRIMARYTOPICOF', 'http://xmlns.com/foaf/0.1/isPrimaryTopicOf');
define('FOAF_NICK', 'http://xmlns.com/foaf/0.1/nick');
define('FOAF_NAME', 'http://xmlns.com/foaf/0.1/name');
define('FOAF_PRIMARYTOPIC', 'http://xmlns.com/foaf/0.1/primaryTopic');
define('FOAF_TOPIC', 'http://xmlns.com/foaf/0.1/topic');
define('FOAF_PAGE', 'http://xmlns.com/foaf/0.1/page');

define('RSS_ITEM', 'http://purl.org/rss/1.0/item');
define('RSS_TITLE', 'http://purl.org/rss/1.0/title');
define('RSS_LINK', 'http://purl.org/rss/1.0/link');
define('RSS_DESCRIPTION', 'http://purl.org/rss/1.0/description');
define('RSS_ITEMS', 'http://purl.org/rss/1.0/items');

define('DC_TITLE', 'http://purl.org/dc/elements/1.1/title');
define('DC_DESCRIPTION', 'http://purl.org/dc/elements/1.1/description');
define('DC_CREATOR', 'http://purl.org/dc/elements/1.1/creator');
define('DC_DATE', 'http://purl.org/dc/elements/1.1/date');

define('DCT_ABSTRACT', 'http://purl.org/dc/terms/abstract');
define('DCT_PROVENANCE', 'http://purl.org/dc/terms/provenance');

define('OS_STARTINDEX','http://a9.com/-/spec/opensearch/1.1/startIndex');
define('OS_ITEMSPERPAGE','http://a9.com/-/spec/opensearch/1.1/itemsPerPage');
define('OS_TOTALRESULTS','http://a9.com/-/spec/opensearch/1.1/totalResults');

define('MIME_RDFXML', 'application/rdf+xml');
define('MIME_RSS', 'application/rss+xml');
define('MIME_XML', 'application/xml');
define('MIME_SPARQLRESULTS', 'application/sparql-results+xml');
define('MIME_FORMENCODED', 'application/x-www-form-urlencoded');

define('CS_SUBJECTOFCHANGE', 'http://purl.org/vocab/changeset/schema#subjectOfChange');
define('CS_CREATEDDATE', 'http://purl.org/vocab/changeset/schema#createdDate');
define('CS_CREATORNAME', 'http://purl.org/vocab/changeset/schema#creatorName');
define('CS_CHANGEREASON', 'http://purl.org/vocab/changeset/schema#changeReason');
define('CS_CHANGESET', 'http://purl.org/vocab/changeset/schema#ChangeSet');
define('CS_REMOVAL', 'http://purl.org/vocab/changeset/schema#removal');
define('CS_ADDITION', 'http://purl.org/vocab/changeset/schema#addition');

define('FRM_MAPPEDDATATYPEPROPERTY', 'http://schemas.talis.com/2006/frame/schema#mappedDatatypeProperty');
define('FRM_PROPERTY', 'http://schemas.talis.com/2006/frame/schema#property');
define('FRM_NAME', 'http://schemas.talis.com/2006/frame/schema#name');

define('BF_ANALYZER', 'http://schemas.talis.com/2006/bigfoot/configuration#analyzer');
define('BF_CREDENTIAL', 'http://schemas.talis.com/2006/bigfoot/configuration#credential');
define('BF_FIELDWEIGHT', 'http://schemas.talis.com/2006/bigfoot/configuration#fieldWeight');
define('BF_GROUP', 'http://schemas.talis.com/2006/bigfoot/configuration#group');
define('BF_GROUPREF', 'http://schemas.talis.com/2006/bigfoot/configuration#groupRef');
define('BF_JOBTYPE', 'http://schemas.talis.com/2006/bigfoot/configuration#jobType');
define('BF_MD5CREDENTIAL', 'http://schemas.talis.com/2006/bigfoot/configuration#md5credential');
define('BF_SNAPSHOTURI', 'http://schemas.talis.com/2006/bigfoot/configuration#snapshotUri');
define('BF_STARTTIME', 'http://schemas.talis.com/2006/bigfoot/configuration#startTime');
define('BF_STORE', 'http://schemas.talis.com/2006/bigfoot/configuration#store');
define('BF_STOREREF', 'http://schemas.talis.com/2006/bigfoot/configuration#storeRef');
define('BF_STORETEMPLATE', 'http://schemas.talis.com/2006/bigfoot/configuration#storeTemplate');
define('BF_WEIGHT', 'http://schemas.talis.com/2006/bigfoot/configuration#weight');

define('BF_STOREGROUP', 'http://schemas.talis.com/2006/bigfoot/configuration#StoreGroup');
define('BF_STOREGROUPREQUEST', 'http://schemas.talis.com/2006/bigfoot/configuration#StoreGroupRequest');
define('BF_JOBREQUEST', 'http://schemas.talis.com/2006/bigfoot/configuration#JobRequest');
define('BF_REINDEXJOB', 'http://schemas.talis.com/2006/bigfoot/configuration#ReindexJob');
define('BF_RESETDATAJOB', 'http://schemas.talis.com/2006/bigfoot/configuration#ResetDataJob');
define('BF_RESTOREJOB', 'http://schemas.talis.com/2006/bigfoot/configuration#RestoreJob');
define('BF_SNAPSHOTJOB', 'http://schemas.talis.com/2006/bigfoot/configuration#SnapshotJob');
define('BF_USER', 'http://schemas.talis.com/2006/bigfoot/configuration#User');


define('ANALYZER_STANDARDEN', 'http://schemas.talis.com/2007/bigfoot/analyzers#standard-en');
define('ANALYZER_STANDARDEL', 'http://schemas.talis.com/2007/bigfoot/analyzers#standard-el');
define('ANALYZER_STANDARDDE', 'http://schemas.talis.com/2007/bigfoot/analyzers#standard-de');
define('ANALYZER_STANDARDFR', 'http://schemas.talis.com/2007/bigfoot/analyzers#standard-fr');
define('ANALYZER_STANDARDCJK', 'http://schemas.talis.com/2007/bigfoot/analyzers#standard-cjk');
define('ANALYZER_STANDARDNL', 'http://schemas.talis.com/2007/bigfoot/analyzers#standard-nl'); 
define('ANALYZER_KEYWORD', 'http://schemas.talis.com/2007/bigfoot/analyzers#keyword'); 
define('ANALYZER_NOSTOPEN', 'http://schemas.talis.com/2007/bigfoot/analyzers#nostop-en'); 
define('ANALYZER_NORMEN', 'http://schemas.talis.com/2007/bigfoot/analyzers#norm-en'); 



?>

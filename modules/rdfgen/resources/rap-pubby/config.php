<?PHP

// ----------------------------------------------------------------------------------
// RAP_Pubby - A Linked Data Frontend for RAP
// ----------------------------------------------------------------------------------

/**
 * Installation information is found in the RAP_Pubby documentation.
 *
 * @author  Radoslaw Oldakowski <radol@gmx.de>
 * @version 1.0, 29.02.2008
 * @package rap-pubby
 */
 
// ----------------------------------------------------------------------------------
// Include RAP Classes
// ----------------------------------------------------------------------------------

// Defines RAP include directories
// NOTE: Modify this if your rap-pubby is not installed in RAP subdirectory
define("RDFAPI_INCLUDE_DIR", "../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");


// include RAPpubby classes
include_once('RAPpubbyURIrewriter.php');
include_once('RAPpubbyDataSet.php');
include_once('RAPpubbyResDescr.php');
include_once('RAPpubbyHTMLSer.php');

// ----------------------------------------------------------------------------------
// RAP_Pubby Server Configuration Section
// ----------------------------------------------------------------------------------


// The root URL where the RAP_Pubby web application is installed, e.g. http://myserver/mydataset/.
define('PUBBY_WEBBASE', 'http://localhost/rdfapi-php/rap-pubby/');

// The URI of a resource which description will be displayed as the home page of the RAP_Pubby installation. 
// Note that you have to specify a dataset URI, not a mapped web URI.
define('PUBBY_INDEX_RESOURCE', 'http://www4.wiwiss.fu-berlin.de/is-group/projects/RAP');

// ----------------------------------------------------------------------------------
// RAP_Pubby Database Configuration
// ----------------------------------------------------------------------------------

// Note: in order to serve a DbModel the $_PUBBY_DATASET['loadRDF'] parameter must be set to '';
define('PUBBY_DB_DRIVER', '');
define('PUBBY_DB_HOST', '');
define('PUBBY_DB_DB', '');
define('PUBBY_DB_USER', '');
define('PUBBY_DB_PASS', '');

define('PUBBY_DBMODEL', '');


// ----------------------------------------------------------------------------------
// RAP_Pubby Dataset Configuration Section
// ----------------------------------------------------------------------------------

$_PUBBY_DATASET = array(
	
	// Load an RDF document from the Web or the file system and use it as the data source.
	// If specified, the database connection configured above will be ignored.
	'loadRDF'             => 'example/group.rdf',
   
	// The common URI prefix of the resource identifiers in the served dataset. 
	// Note: Only resources with this prefix will be mapped and made available by RAP_Pubby
	'datasetBase'         => 'http://www4.wiwiss.fu-berlin.de/is-group/',
   
	// If present, only dateset URIs matching this regular expression  will be mapped and made available by RAP_Pubby. 
	// The regular expression must match everything after the $_PUBBY_DATASET['datasetBase'] part of the URI.
	// For example: datasetBase = 'http://example.org/' and datasetURIPattern = '/(users|documents)\/.*/'
	// This will publish the dataset URI http://example.org/users/alice, 
	// but not http://example.org/invoices/5395842 because the URI part invoices/5395842 does not match the regular expression.
	// default value = '';
	'datasetURIPattern' => '',

	// If present, this string will be prefixed to the mapped web URIs. This is useful if you have to avoid potential name clashes 
	// with URIs already used by the server itself. For example, if the dataset includes a URI http://mydataset/page, 
	// and the dataset prefix is http://mydataset/, then there would be a clash after mapping because RAP_Pubby reserves 
	// the mapped URI http://myserver/mydataset/page for its own use. In this case, you may specify a prefix like "resource/", 
	// which will result in a mapped URI of http://myserver/mydataset/resource/page.
	// NOTE: the prefix must end with "/"
	'webResourcePrefix' => '',
	
	// Links to an RDF document whose prefix declarations will be used in output. 
	// e.g. 'usePrefixesFrom' => 'prefixes.n3', You can use the file prefixes.n3 in rap-pubby directory as template. 
	// Defaults to the empty URL, which means the prefixes from the input RAP model will be used.	
	'usePrefixesFrom' => 'prefixes.n3',
   
	// All statements inside the metadata file will be added as metadata to the RDF documents published 
	// for this dataset. This feature can be used for instance to add licensing information to your published documents.
	// You can use the file metadata.n3 in rap-pubby directory as template.
	'rdfDocumentMetadata' => 'metadata.n3',

	   
	// If set to true, an owl:sameAs statement of the form <web_uri> owl:sameAs <dataset_uri> will be present in Linked Data output.
	'addSameAsStatements' => false,
);


// ----------------------------------------------------------------------------------
// Pubby HTML Output Setting
// ----------------------------------------------------------------------------------

// URL of the template file used in HTML output
define ('PUBBY_HTML_SER__TEMPLATE', 'templ/template.html');

// URL of the template file rendering '404 - Not Found' information used in HTML output
define ('PUBBY_HTML_SER__TEMPLATE_404', 'templ/404_notFound.html');

// Link to directory where template includes (css-files, scripts, images) are located
define ('PUBBY_HTML_SER__TEMPL_INCL_DIR' , 'templ/');

// The name of the project, for display in page titles
define ('PUBBY_HTML_SER__PROJECT_NAME', 'PubbyExample@Localhost');

// A project homepage or similar URL, for linking in page titles
define ('PUBBY_HTML_SER__PROJECT_HOMEPAGE', 'http://localhost/rap-pubby/doc/RAP_Pubby.htm');

// For resource label and short description 
define ('PUBBY_HTML_SER__DEFAULT_LANG', 'en');

// The value of these RDF properties, if present in the dataset, will be used as labels and page titles for resources. 
// Note: If multiple properties are present only the first one found (in default language, if specified) will be showed.
$_PUBBY_HTML_SER['labelProperty'] = array (
   new Resource(RDF_SCHEMA_URI ."label"),  
   new Resource(DC_NS ."title"),
   new Resource(FOAF_NS ."name"),
);

// The value of these RDF properties, if present in the dataset, will be used as short textual description for resources. 
// Note: If multiple properties are present only the first one found (in default language, if specified) will be showed.
$_PUBBY_HTML_SER['commentProperty'] = array (
   new Resource(RDF_SCHEMA_URI ."comment"),
   new Resource(DC_NS ."description"),
);

// The value of these RDF properties, if present in the dataset, will be used as an image URL to show a depiction of the item.
// Note: If multiple properties are present only the first one found will be showed.
$_PUBBY_HTML_SER['imageProperty'] = array (
   new Resource(FOAF_NS ."depiction"),
);

?>
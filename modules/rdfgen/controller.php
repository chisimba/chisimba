<?php
/**
 * RDF Generator
 * 
 * controller class for rdfgen package
 * 
 * PHP version 5
 * 
 * GPL licenced
 * 
 * @category  Chisimba
 * @package   rdfgen
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   gpl
 * @version   $Id: controller.php 18640 2010-08-10 05:31:05Z paulscott $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
	die ( "You cannot view this page directly" );
}
// end security check


/**
 * Controller class for the rdfgen module
 * 
 * Controller class for the rdfgen module
 * 
 * @category  Chisimba
 * @package   rdfgen
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   gpl
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class rdfgen extends controller {
	
	/**
	 * Language object
	 * @var    unknown
	 * @access public 
	 */
	public $objLanguage;
	
	/**
	 * Config object
	 * @var    unknown
	 * @access public 
	 */
	public $objConfig;
	
	/**
	 * User object
	 *
	 * @var unknown_type
	 */
	public $objUser;
	
	public $objRdf;
	
	/**
	 * Constructor method to instantiate objects and get variables
	 */
	public function init() {
		try {
			$this->objLanguage = $this->getObject ( 'language', 'language' );
			$this->objConfig = $this->getObject ( 'altconfig', 'config' );
			$this->objUser = $this->getObject ( 'user', 'security' );
			$this->objRdf = $this->getObject ( 'rdf' );
			
			// Define the paths we will be needing
			define ( "RDFAPI_INCLUDE_DIR", $this->getResourcePath ( 'api/' ) );
			include (RDFAPI_INCLUDE_DIR . "RdfAPI.php");
		} catch ( customException $e ) {
			echo customException::cleanUp ();
			die ();
		}
	}
	/**
	 * Method to process actions to be taken
	 *
	 * @param string $action String indicating action to be taken
	 */
	public function dispatch($action = Null) {
		switch ($action) {
			default :
				$this->requiresLogin ( FALSE );
				
				$someDoc = new Resource ( "http://www.example.org/someDocument.html" );
				$creator = new Resource ( "http://www.purl.org/dc/elements/1.1/creator" );
				
				$statement1 = new Statement ( $someDoc, $creator, new Literal ( "Paul Scott" ) );
				
				$model1 = ModelFactory::getDefaultModel ();
				$model1->add ( $statement1 );
				
				// Output the RDF/XML serialization of $model1
				$message = $this->objLanguage->languageText ( "mod_rdfgen_message", "rdfgen", "This is an example as this module is a pure back end module and has no user functionality: <br />" );
				$message .= $model1->toStringIncludingTriples ();
				
				$this->setVarByRef ( 'message', $message );
				return 'view_tpl.php';
				break;
			
			case 'test' :
				$params = array ('url' => 'http://www.example.com/somepage.html', 'creator' => "Paul Scott", 'date' => date ( 'r' ), 'contributor' => 'some dude', 'coverage' => 'testing', 'description' => 'A test document', 'example data' => 'test', 'format' => 'html', 'identifier' => '', 'language' => 'en', 'publisher' => 'me', 'relation' => '', 'rights' => 'cc-by-sa', 'source' => 'me', 'subject' => 'testing', 'title' => 'test doc', 'type' => 'dynamic' );
				
				$message = $this->objRdf->generateDC ( $params );
				$this->appendArrayVar ( 'headerParams', "<!--" . file_get_contents($message) . "-->" );
				$this->setVarByRef ( 'message', $message );
				return 'view_tpl.php';
				break;
		}
	}
	
	/**
	 * Overide the login object in the parent class
	 *
	 * @param  void  
	 * @return bool  
	 * @access public
	 */
	public function requiresLogin($action) {
		return FALSE;
	}
}
?>

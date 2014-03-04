<?php
/**
 * RDF Generator Object class
 * 
 * Object extension class for rdfgen package
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
 * @version   $Id: rdf_class_inc.php 11958 2008-12-29 21:36:08Z charlvn $
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
 * Object class for the rdfgen module
 * 
 * Object extension class for the rdfgen module
 * 
 * @category  Chisimba
 * @package   rdfgen
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   gpl
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class rdf extends object {
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
	
	public $cachePath;
	
	public function init() {
		try {
			$this->objLanguage = $this->getObject ( 'language', 'language' );
			$this->objConfig = $this->getObject ( 'altconfig', 'config' );
			$this->objUser = $this->getObject ( 'user', 'security' );
			
			// Define the paths we will be needing
			//define("RDFAPI_INCLUDE_DIR", $this->getResourcePath('api/'));
			//include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
			//include(RDFAPI_INCLUDE_DIR . "syntax/RdfSerializer.php");
			

			$this->cachePath = $this->objConfig->getContentBasePath () . "rdfcache/";
			if (! file_exists ( $this->cachePath )) {
				mkdir ( $this->cachePath, 0777 );
			}
		} catch ( customException $e ) {
			echo customException::cleanUp ();
			die ();
		}
	}
	
	public function generateDC($params) {
		if (! is_array ( $params )) {
			throw new customException ( $this->objLanguage->languageText ( "mod_rdfgen_mustbearray", "rdfgen", "RDF Generator module: Input must be an array!" ) );
		} 

		else {
			$someDoc = new Resource ( $params ['url'] );
			
			// creator
			$creator = new Resource ( "http://www.purl.org/dc/elements/1.1/creator" );
			$create = new Statement ( $someDoc, $creator, new Literal ( $params ["creator"] ) );
			
			// title
			$tit = new Resource ( "http://www.purl.org/dc/elements/1.1/title" );
			$title = new Statement ( $someDoc, $tit, new Literal ( $params ["title"] ) );
			
			// subject
			$sub = new Resource ( "http://www.purl.org/dc/elements/1.1/subject" );
			$subject = new Statement ( $someDoc, $sub, new Literal ( $params ["subject"] ) );
			
			// description
			$des = new Resource ( "http://www.purl.org/dc/elements/1.1/description" );
			$description = new Statement ( $someDoc, $des, new Literal ( $params ["description"] ) );
			
			// publisher
			$pub = new Resource ( "http://www.purl.org/dc/elements/1.1/publisher" );
			$publisher = new Statement ( $someDoc, $pub, new Literal ( $params ["publisher"] ) );
			
			// contributor
			$con = new Resource ( "http://www.purl.org/dc/elements/1.1/contributor" );
			$contributor = new Statement ( $someDoc, $con, new Literal ( $params ["contributor"] ) );
			
			// date
			$dt = new Resource ( "http://www.purl.org/dc/elements/1.1/date" );
			$date = new Statement ( $someDoc, $dt, new Literal ( $params ["date"] ) );
			
			// type
			$tp = new Resource ( "http://www.purl.org/dc/elements/1.1/type" );
			$type = new Statement ( $someDoc, $tp, new Literal ( $params ["type"] ) );
			
			// format
			$for = new Resource ( "http://www.purl.org/dc/elements/1.1/format" );
			$format = new Statement ( $someDoc, $for, new Literal ( $params ["format"] ) );
			
			// identifier
			$id = new Resource ( "http://www.purl.org/dc/elements/1.1/identifier" );
			$identifier = new Statement ( $someDoc, $id, new Literal ( $params ["identifier"] ) );
			
			// source
			$src = new Resource ( "http://www.purl.org/dc/elements/1.1/source" );
			$source = new Statement ( $someDoc, $src, new Literal ( $params ["source"] ) );
			
			// language
			$lan = new Resource ( "http://www.purl.org/dc/elements/1.1/language" );
			$language = new Statement ( $someDoc, $lan, new Literal ( $params ["language"] ) );
			
			// relation
			$rel = new Resource ( "http://www.purl.org/dc/elements/1.1/relation" );
			$relation = new Statement ( $someDoc, $rel, new Literal ( $params ["relation"] ) );
			
			// coverage
			$cov = new Resource ( "http://www.purl.org/dc/elements/1.1/coverage" );
			$coverage = new Statement ( $someDoc, $cov, new Literal ( $params ["coverage"] ) );
			
			// rights
			$rig = new Resource ( "http://www.purl.org/dc/elements/1.1/rights" );
			$rights = new Statement ( $someDoc, $rig, new Literal ( $params ["rights"] ) );
			
			$model1 = ModelFactory::getDefaultModel ();
			
			// Add in all the fields
			$model1->add ( $create );
			$model1->add ( $title );
			$model1->add ( $subject );
			$model1->add ( $description );
			$model1->add ( $publisher );
			$model1->add ( $contributor );
			$model1->add ( $date );
			$model1->add ( $type );
			$model1->add ( $format );
			$model1->add ( $identifier );
			$model1->add ( $source );
			$model1->add ( $language );
			$model1->add ( $relation );
			$model1->add ( $coverage );
			$model1->add ( $rights );
			
			/*$ser = new RDFSerializer();
			$data =& $ser->serialize($model1);
			return $data;*/
			$params ['filename'] = 'test_gensrv123' . time ();
			$model1->saveAs ( $this->cachePath . $params ['filename'] . ".rdf", $type = 'rdf' );
			return $this->cachePath . $params ['filename'] . ".rdf"; // $model1->writeRdfToString();
		}
	}
}

<?PHP

// ----------------------------------------------------------------------------------
// RAP_Pubby - A Linked Data Frontend for RAP
// ----------------------------------------------------------------------------------

/**
 * Installation information is found in the RAP_Pubby documentation.
 *
 * @author  Radoslaw Oldakowski <radol@gmx.de>
 * @version 1.0, 19.12.2007
 * @package rap-pubby
 */
 

// toDo: delete this
include_once('RAPpubbyURIrewriter.php');

Class RAPpubbyDataset extends Object {
	
	var $m;
	var $ns = array();
	var $metadata;
	
	
	function RAPpubbyDataset() {
		
		global $_PUBBY_DATASET;
		
		$this->loadModel($_PUBBY_DATASET['loadRDF']);
		$this->loadNamespaces($_PUBBY_DATASET['usePrefixesFrom']);
		$this->loadMetadata($_PUBBY_DATASET['rdfDocumentMetadata']);
		
	}

	
	/**
	*
	*/	
	function & getResDescr ($resURI, $attach_metadata = false) {
		
		global $_PUBBY_DATASET;
	
		$r = new Resource(RAPpubbyURIrewriter::pubbyURItoDatasetURI($resURI));		
		$rd_m = new MemModel();	
		
		$rd_m = $this->m->find($r, NULL, NULL);
		$backlinks = $this->m->find(NULL, NULL, $r);
		$rd_m->addModel($backlinks);				
	
		$rew_rd_m = & RAPpubbyURIrewriter::rewriteURIsInResDescrModel($rd_m);
	
		if (!$rew_rd_m->isEmpty() && $_PUBBY_DATASET['addSameAsStatements']) {
		
			$rew_rd_m->add(new Statement(new Resource($resURI), new Resource(OWL_NS. "sameAs"), $r));		
		}	
		$rew_rd_m->addParsedNamespaces($this->ns);
		
		$rd = new RAPpubbyResDescr($resURI, $rew_rd_m);
		
		if ($attach_metadata && !$rd->isEmpty()) {
			$rd->attachMetadata($this->getMetadata($resURI));
		}						
		return $rd;	
	}
	
	
	// private -----------------------------------------------------
	
	
	/**
	* @return RAP model with metadata, blank node replaced with resource URI
	*/
	function & getMetadata ($resURI) {		
			
		$dataURI = RAPpubbyURIrewriter::resURItoDataURI($resURI);		

		$metaData = $this->metadata;
		$metaData->replace(new BlankNode(BNODE_PREFIX .'1'), NULL, NULL, new Resource($dataURI));
		
		return $metaData;
	}

	
	/**
	*
	*/
	function loadModel($url) {
				
		// load model from file
		if ($url) {
 			$this->m = new MemModel();
 			$this->m->load($url);
		}
		else {
 			$db = new DbStore(PUBBY_DB_DRIVER, PUBBY_DB_HOST, PUBBY_DB_DB, PUBBY_DB_USER, PUBBY_DB_PASS);
 			$this->m = $db->getModel(PUBBY_DBMODEL);
		}
	}
	
	
	/**
	* loads namespaces from file or rewrites from configuration model
	*/	
	function loadNamespaces($url) {	

		if ($url) {
			
			$nmsp_m = new MemModel();
			$nmsp_m->load($url);
			$this->ns = $nmsp_m->getParsedNamespaces();
			if (!$this->ns) {
				trigger_error("The file:" .$url ."does not contain any namespace declarations."
							  ."The prefixes from the configuration model will be used instead");
			}
		} 
		else {
			
			$nmsp = $this->m->getParsedNamespaces();
			if ($nmsp) {
				$this->ns = RAPpubbyURIrewriter::rewrNamespaces($nmsp);
			}
		}
	}
	
	
	/**
	*
	*/	
	function loadMetadata($url) {
		
		if ($url) {
			$this->metadata = new MemModel();
			$this->metadata->load($url);
		}
	}

	
	
	
}

?>
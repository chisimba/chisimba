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


Class RAPpubbyResDescr extends Object {

	var $res;
	var $m_rd;
	var $m_metadata;
	
	
	/**
	*
	*/
	function RAPpubbyResDescr ($resURI, &$m) {
		
		$this->res = new Resource($resURI);
		$this->m_rd = &$m;
		$this->m_metadata = new MemModel();
	}
	
	
	/**
	*
	*/	
	function isEmpty() {
		
		return $this->m_rd->isEmpty();		
	}
		
	
	/**
	*
	*/	
	function & getResource() {
				
		return $this->res;
	}

	
	/**
	*
	*/	
	function getResURI() {
				
		return $this->res->getURI();
	}
	
	
	/**
	*
	*/	
	function getDataURI() {
				
		return RAPpubbyURIrewriter::resURItoDataURI($this->res->getURI());
	}
	
	
	/**
	*
	*/	
	function getPageURI() {
				
		return RAPpubbyURIrewriter::resURItoPageURI($this->res->getURI());
	}	
	
	
	/**
	*
	*/	
	function & getModel() {
		
		return $this->m_rd;
	}			
	
	
	/**
	*
	*/	
	function attachMetadata(&$m) {
		
		$this->m_rd->addModel($m);
		$this->m_metadata = &$m;
	}
	
	
	/**
	*
	*/	
	function & getMetadataModel() {
		
		return $this->m_metadata;
	}
	
	
	function serialize ($format) {
		
		switch ($format) {
			case "html":
    			$s = new RAPpubbyHTMLSer();	
				return $s->serialize($this);
			case "n3":
				include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3);
	  			$s = new N3Serializer();	
	  			return $s->serialize($this->m_rd);			
			case "rdf/xml":
			default:
    			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
				$s = new RdfSerializer();
				return $s->serialize($this->m_rd);
		}
	}
	
}

?>
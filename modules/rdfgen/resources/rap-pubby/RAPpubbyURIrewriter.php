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


Class RAPpubbyURIrewriter extends Object {

	
	/**
	*
	*/
	function datasetURItoPubbyURI($uri) {
	
		global $_PUBBY_DATASET;

		return str_ireplace($_PUBBY_DATASET['datasetBase'], PUBBY_WEBBASE .$_PUBBY_DATASET['webResourcePrefix'], $uri);
	}
	
	
	/**
	*
	*/
	function pubbyURItoDatasetURI($uri) {
	
		global $_PUBBY_DATASET;

		return str_ireplace(PUBBY_WEBBASE .$_PUBBY_DATASET['webResourcePrefix'], $_PUBBY_DATASET['datasetBase'], $uri);
	}

	
	/**
	*
	*/
	function resURItoDataURI($resURI, $output='') {
		
		$dataURI = PUBBY_WEBBASE ."data/" .substr($resURI, strlen(PUBBY_WEBBASE));
		
		if ($output) {
			return $dataURI ."?output=$output";			
		}
		return $dataURI;
	}
	
	
	/**
	*
	*/
	function resURItoPageURI($resURI) {		

		return PUBBY_WEBBASE ."page/" .substr($resURI, strlen(PUBBY_WEBBASE));
	}
	
	
	/**
	*
	*/
	function dataURItoResURI($dataURI, $outputParam='') {
		
		$resURI = PUBBY_WEBBASE .substr($dataURI, strlen(PUBBY_WEBBASE. 'data/'));
		
		if ($outputParam) {
			return substr($resURI, 0, stripos($resURI, "?$outputParam"));
		}
		return $resURI;					   	
	}
	
	
	/**
	*
	*/
	function pageURItoResURI($pageURI) {
				
		return PUBBY_WEBBASE .substr($pageURI, strlen(PUBBY_WEBBASE. 'page/'));
	}

	
	/**
	*
	*/	
	function rewrNamespaces($ns) {
		
		global $_PUBBY_DATASET;
	
		foreach ($ns as $n => $prefix) {
			if (stripos($n, $_PUBBY_DATASET['datasetBase']) !== false) {
				unset($ns[$n]);			
				$ns[RAPpubbyURIrewriter::datasetURItoPubbyURI($n)] = $prefix; 
			}
		}	
		asort($ns);			
		return $ns;
	}
	
	
	/**
	*
	*/
	function & rewriteURIsInResDescrModel(&$rd_m) {
	
		global $_PUBBY_DATASET;
		global $namespaces;
		
		$rew_rd_m = new MemModel();
		
		// uri rewriting + regex filtering
		if ($_PUBBY_DATASET['datasetURIPattern'] != '') {
			
			$l = strlen($_PUBBY_DATASET['datasetBase']);
			
			$iter = $rd_m->getStatementIterator();
			while ($iter->hasNext()) {
				
				$triple = $iter->next();
				$subj = $triple->getSubject();
				
				// if subjURI is a datasetURI & does not match the pattern
				if (stripos($subj->getURI(), $_PUBBY_DATASET['datasetBase']) === 0 && 
					!preg_match($_PUBBY_DATASET['datasetURIPattern'], substr($subj->getURI(), $l))) {			    	
					continue;
				}
				else {
					// if predURI is a datasetURI & does not match the pattern
					$pred = $triple->getPredicate();
					if (stripos($pred->getURI(), $_PUBBY_DATASET['datasetBase']) === 0 && 
				 		!preg_match($_PUBBY_DATASET['datasetURIPattern'], substr($pred->getURI(), $l))) {			    	
						continue;
					}	
					else {
						// if obj is a Literal & objeURI is a datasetURI & does not match the pattern
						$obj = $triple->getObject();
						if (!is_a($obj, "Literal")) {
							if (stripos($obj->getURI(), $_PUBBY_DATASET['datasetBase']) === 0 && 
				 				!preg_match($_PUBBY_DATASET['datasetURIPattern'], substr($obj->getURI(), $l))) {
				 					continue;
				 			}
				 			else {
				 				$obj = new Resource(RAPpubbyURIrewriter::datasetURItoPubbyURI($obj->getURI()));
				 			}
				 		}
						$subj = new Resource(RAPpubbyURIrewriter::datasetURItoPubbyURI($subj->getURI()));
						$pred = new Resource(RAPpubbyURIrewriter::datasetURItoPubbyURI($pred->getURI()));
						
						$rew_rd_m->add(new Statement($subj, $pred, $obj));	
					}
				} 	
			}				
		}
		else {
			
			$iter = $rd_m->getStatementIterator();
			while ($iter->hasNext()) {
				
				$triple = $iter->next();			
				
				$subj = new Resource(RAPpubbyURIrewriter::datasetURItoPubbyURI($triple->getSubject()->getURI()));
				$pred = new Resource(RAPpubbyURIrewriter::datasetURItoPubbyURI($triple->getPredicate()->getURI()));
				$obj = $triple->getObject();
				
				if (!is_a($obj, "Literal")) {
					$obj = new Resource(RAPpubbyURIrewriter::datasetURItoPubbyURI($obj->getURI()));			
				}
				$rew_rd_m->add(new Statement($subj, $pred, $obj));	
			}
		}
	
		return $rew_rd_m;		
	}
	
}

?>
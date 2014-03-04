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


include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);


Class RAPpubbyHTMLSer extends Object {
	
	var $htmlTemplate;
	var $htmlTemplate_404;
	var $placeholders = array(
			'##_templInclDirURL_##'	  => '',
  	   	    '##_projetName_##'        => '',
		  	'##_projectHomepage_##'	  => '',
			'##_resURI_##'      	  => '',  
			'##_repURIdata_##'     	  => '',
			'##_resourceLabel_##'  	  => '',
		    '##_shortDescription_##'  => '',
			'##_imgLink_##'           => '',
			'##_prop_tableRows_##'    => '',			
		);
		

	/**
	*
	*/	
	function RAPpubbyHTMLSer ($template = PUBBY_HTML_SER__TEMPLATE, $template_404 = PUBBY_HTML_SER__TEMPLATE_404) {
		
        $this->loadTemplates($template, $template_404);
		
        $this->placeholders['##_templInclDirURL_##'] = PUBBY_WEBBASE .PUBBY_HTML_SER__TEMPL_INCL_DIR;
		$this->placeholders['##_projectName_##'] = PUBBY_HTML_SER__PROJECT_NAME;
		$this->placeholders['##_projectHomepage_##'] = PUBBY_HTML_SER__PROJECT_HOMEPAGE;
		
	}
	

	/**
	*
	*/	
	function serialize (&$rd) {
						
		
		if ($rd->isEmpty()) {
			$html = $this->htmlTemplate_404;
		}
		else {
			$html = $this->htmlTemplate;					
		}
		$ph = $this->generatePlaceholderValues($rd);		
		$html = str_ireplace(array_keys($ph), $ph, $html);			
		
		return $html;
	}

	
	/**
	*
	*/	
	function loadTemplates($t, $t_404) {

		if (file_exists($t)) {	
			$file = fopen($t, "r");
    		if ($file) {
    			$this->htmlTemplate = fread($file,filesize($t));
        		fclose($file);
    		}    
		} 
		else { 			
			trigger_error("Could not find template file: $t, ");
			$this->htmlTemplate = $this->getBasicTemplate();
		} 	
		
		if (file_exists($t_404)) {	
			$file = fopen($t_404, "r");
    		if ($file) {
    			$this->htmlTemplate_404 = fread($file,filesize($t_404));
        		fclose($file);
    		}    
		} 
		else { 			
			trigger_error("Could not find template file: $t_404, ");
			$this->htmlTemplate_404 = $this->getBasicTemplate_404();
		} 	
		
	}
	

	/**
	 *
	 */	
	function getBasicTemplate() {
		
		return '<h1>##_resURI_##</h1>
			     <table>
		           <tr><td><h3>Property</h3></td> <td><h3>Value</h3></td></tr>
				   ##_prop_tableRows_##
		         </table>';
	}
	
	function getBasicTemplate_404() {
		
		return '<h1>##_resURI_##</h1>
			     <p>The requested resource does not exist at this server, or no information about it is available.</p>';
	}
	

	/**
	*
	*/	
	function generatePlaceholderValues (&$rd) {
		
		$res = & $rd->getResource();	
		$m = & $rd->getModel();
		
		$ph = $this->placeholders;
		$ph['##_resURI_##'] = $res->getURI();
		$ph['##_repURIdata_##'] = $rd->getDataURI();
		$ph['##_resourceLabel_##'] = $this->findLabel($m, $res);
		$ph['##_shortDescription_##'] = $this->findComment($m, $res);
		$ph['##_imgLink_##'] = $this->findImgLink($m, $res);
		$ph['##_prop_tableRows_##'] = $this->renderResourceProperties($m, $res);
		
		return $ph;		
	}	

		
	/**
	* find only the first occurance of the label
	*/		 	
	function findLabel(&$m, &$res) {
	
		global $_PUBBY_HTML_SER;
		return $this->findLiteralValueLang($m, $res, $_PUBBY_HTML_SER['labelProperty']);
	}
	
	
	/**
	* find only the first occurance of the comment
	*/		 	
	function findComment(&$m, &$res) {
	
		global $_PUBBY_HTML_SER;
		return $this->findLiteralValueLang($m, $res, $_PUBBY_HTML_SER['commentProperty']);
	}
	
	
	/**
	*
	*/		
	function findImgLink (&$m, &$res) {
		
		global $_PUBBY_HTML_SER;
		
		foreach ($_PUBBY_HTML_SER['imageProperty'] as $prop) {
			
			$triple = $m->findFirstMatchingStatement($res, $prop, NULL);
			if ($triple) {
				return $triple->getObject()->getURI();
			}
		}
		return '';
	}
	
	
	/**
	* find only the first occurance of the Literal from an array of properties
	*/		 	
	function findLiteralValueLang(&$m, &$res, &$prop_array) {

		$tmp = '';
		
		// if no default lang is specified find any literal value
		if (PUBBY_HTML_SER__DEFAULT_LANG == '') {
			
			foreach ($prop_array as $prop) {
				
				$triple = $m->findFirstMatchingStatement($res, $prop, NULL);
				if ($triple) {
					return $triple->getObject()->getLabel();
				}
			}
		}
		else {
			foreach ($prop_array as $prop) {
				
				$result = $m->find($res, $prop, NULL);			
				
				$iter = $result->getStatementIterator();			
				while ($iter->hasNext()) {	
					
					$triple = $iter->next();				
					if ($triple->getObject()->getLanguage() == PUBBY_HTML_SER__DEFAULT_LANG) {						
						return $triple->getObject()->getLabel();
					}
				}
				// if no label with default lang was found take the first one
				// and store it in $tmp because another property could contain the default lang
				if (!$result->isEmpty()) {
					$triple = $result->findFirstMatchingStatement($res, $prop, NULL);
					if ($triple) {
						$tmp = $triple->getObject()->getLabel();
					}
				}
			}
		}									
		return $tmp;
	}
	

	/**
	* 
	*/	
	function renderResourceProperties (&$m, &$res) {
	
		$prop = array();
		
		// find properties
		$result = $m->find($res, NULL, NULL);		
		
		$iter = $result->getStatementIterator();			
		while ($iter->hasNext()) {	
			$triple = $iter->next();
			$propURI = $triple->getPredicate()->getURI();
			$prop[$propURI][false][] = $triple->getObject();			
		}
		
		// find inverse properteis (backlinks)
		$result = $m->find(NULL, NULL, $res);
		
		$iter = $result->getStatementIterator();			
		while ($iter->hasNext()) {	
			$triple = $iter->next();
			$propURI = $triple->getPredicate()->getURI();
			// inverse the property --> get subject instead of object
			$prop[$propURI][true][] = $triple->getSubject();

		}

		// sort properties
		uksort($prop, array($this, '_sortURIbyQName'));

		// render table rows with resource properties
		$nsPrefix = $m->getParsedNamespaces(); if (!$nsPrefix) $nsPrefix = array();
		$html_tableRows = '';
		$tr_class = array(true => 'odd', false => 'even');
		$odd = true;		

		foreach ($prop as $propURI => $propType) {						
			foreach ($propType as $inverseProp => $valArray) {			

				$tr='
			  		<tr class="' .$tr_class[$odd] .'">
		        		<td class="property">'
		          			.$this->renderPropURI($propURI, $nsPrefix, $inverseProp)
		      		  	.'</td>
		        		<td>
		          			<ul>';
				foreach ($valArray as $propValue) {		

					if (is_a($propValue, "Literal")) {
						$tr .='  				
		            			<li>'
		              				.$this->renderLiteral($propValue)
					  			.'</li>';		          					
					}			
					else {
						$tr .='  				
				        		<li>'
 	             					.$this->renderURI($propValue->getURI(), $nsPrefix)
            		  			.'</li>';         				
					}
				}
				$tr .='
				  		</ul>
		        	  </td>
		      		</tr>';
				$odd = !$odd;	
				$html_tableRows .= $tr;
			}						
		}		
		
		return $html_tableRows;
	}
	
	
	/**
	* 
	*/		
	function renderLiteral ($literal) {
		
		$html = '<span class="literal">' .$literal->getLabel();
		if ($literal->getLanguage()) {
			$html .= '<small> (' .$literal->getLanguage() .')</small>';
		}
		return $html .= '</span>';
		
	}
	
	
	/**
	* 
	*/			
	function renderPropURI ($URI, &$nsPrefix, $inverseProp=false) {
		
		if ($inverseProp) {
			return '<small>is </small>' .$this->renderURI($URI, $nsPrefix) .'<small> of</small>';
		}
		else {
			return $this->renderURI($URI, $nsPrefix);
		}
		
	}

	
	/**
	* 
	*/				
	function renderURI($URI, &$nsPrefix) {	
		
		$ns = RDFUtil::guessNamespace($URI);
		$qName = RDFUtil::guessName($URI);
		
		$html = '<a class="uri" href="' .$URI .'" title="' .$URI .'">'; 
		
		if (array_key_exists($ns, $nsPrefix)) {
			$html .= '<small>' .$nsPrefix[$ns] .':</small>' .$qName;
		}
		else {
			$html .= $URI;
		}
		$html .= '</a>';
		
		return $html;
		
	}	
	
	
	/**
	*  Call-back function for uksort()
	*/				
	function _sortURIbyQName ($a, $b) {
		
		return strcasecmp(RDFUtil::guessName($a), RDFUtil::guessName($b));	
	}
	
}

?>
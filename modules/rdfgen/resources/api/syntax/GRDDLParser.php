<?php
// ----------------------------------------------------------------------------------
// Class: RdfParser
// ----------------------------------------------------------------------------------


/**
 * A GRDDLParser. 
 * This class extracts rdf data from xhtml documents. It uses the PHP xsltprocessor.
 * Gleaning Resource Descriptions from Dialects of Languages (GRDDL):
 * (http://www.w3.org/TR/grddl/)
 * 
 * @version  $Id: GRDDLParser.php 320 2006-11-21 09:38:51Z tgauss $
 * @author Tobias Gauß <tobias.gauss@web.de>, 
 *
 * @package syntax
 * @access	public
 *
 */   
class GRDDLParser extends Object{
	
	
	/**
 	* Document link
 	* 
 	*
 	* @var     String
 	* @access	private
 	*/
	var $doclink;
	
	
	/**
 	* Stylesheet link
 	* 
 	*
 	* @var     String[]
 	* @access	private
 	*/
	var $stylelinks;
	
	
	/**
 	* DomDocument
 	* 
 	* @var     DomDocument
 	* @access	private
 	*/
	var $domdoc;
	
	
	/**
	* generates a MemModel and creates the DomDocument.
	*
	* @param String $doc
	* @access public
	* @return MemModel $model
	*/
	function generateModel($doc){
		$model = new MemModel();
		$this->doclink=$doc;
		$this->domdoc = new DomDocument; 
		$this->domdoc->load($doc);
		$this->_getStyles();
		$model = $this->_generateRDF();
		return $model;
	}
	

	/**
	* gets the used xsl stylesheets.
	*
	* @access private
	*/
	function _getStyles(){
		$link=$this->domdoc->getElementsByTagName('link');
		$i=0;
		while($link->item($i)!=''){
			$item = $link->item($i);
			if($item->getAttributeNode('rel')->value=='transformation'){
				$temp = $item->getAttributeNode('href')->value;
				if(substr($temp,0,1)=='/'){
					$pos = strrpos($this->doclink,'/');
					$part = substr($this->doclink,0,$pos);
					$this->stylelink[]=$part.$temp;
				}else{
					$this->stylelink[]=$temp;
				}
			}
			$i++;
		}
	}
	
	/*
	* uses the PHP build in xslt processor to
	* generate the RDF statements and uses the 
	* RDF- Parser to generate the model
	*
	* @access private
	* @return MemModel $model
	*/
	function _generateRDF(){
		$model=new MemModel();
		$model->setBaseURI($this->doclink);
		$proc = new xsltprocessor;
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
		$pars=new RdfParser();
		foreach($this->stylelink as $key => $value){
			$xsl = new DomDocument;
			$xsl->load($value);
			$proc->importStyleSheet($xsl);
			$model->addModel($pars->generateModel($proc->transformToXML($this->domdoc),$this->doclink));
		}
		return $model;
	}

	
	
}
?>
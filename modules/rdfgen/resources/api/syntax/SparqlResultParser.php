<?php
// ----------------------------------------------------------------------------------
// Class: SparqlResultParser
// ----------------------------------------------------------------------------------

/**
* Parses an xml document in SPARQL Result XML Format.
*
* @version  $Id: SparqlResultParser.php 442 2007-06-01 16:19:26Z cax $
* @author   Tobias Gauß <tobias.gauss@web.de>
*
* @package sparql
*/

class SparqlResultParser extends Object {


	var $arrOutput = array();
	var $resParser;
	var $strXmlData;
	var $counter = -1;
	var $mode = -1;
	var $varname;
	var $dtype;
	var $lang;
	var $varlist;
	var $bncounter = 0;
	var $current_literal;	
	
	
/**
* Main function of SparqlResultParser
*
* @param String  $strInputXML  input document.
*/
	function parse($strInputXML) {

		$this->resParser = xml_parser_create ();

		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

		xml_set_character_data_handler($this->resParser, "tagData");

		$this->strXmlData = xml_parse($this->resParser,$strInputXML );
		if(!$this->strXmlData) {
			echo "unable to parse result<br>".
			"server returned: <br>"
			.$strInputXML;
			
			return "false";
		
		}

		xml_parser_free($this->resParser);

		return $this->arrOutput;
	}
	
// private parser functions from here //	
	function tagOpen($parser, $name, $attrs) {
		//  $tag=array("name"=>$name,"attrs"=>$attrs);
		//  array_push($this->arrOutput,$tag);
		if(strtolower($name)=="variable"){
			if(isset($attrs['name']))
				$this->varlist[]=$attrs['name'];
			if(isset($attrs['NAME']))
				$this->varlist[]=$attrs['NAME'];
		}
		
		if(strtolower($name)=="result"){
			$this->counter++;
			if($this->counter > -1){
				foreach($this->varlist as $value){
					if(!isset($this->arrOutput[$this->counter][$value]))
						$this->arrOutput[$this->counter]["?".$value]='';
				}	
			}
			
		}
		if(strtolower($name)=="boolean"){
			$this->counter++;
			$this->mode = 3;
		}
		if(strtolower($name)=="binding"){
			$this->varname = null;
			$this->dtype   = null;
			$this->lang    = null;
			
			if(isset($attrs['name'])){
				$this->varname = "?".$attrs['name'];
			}else{
				$this->varname = "?".$attrs['NAME'];
			}
		}
		if(strtolower($name)=="uri"){
			$this->mode = 0;
		}
		if(strtolower($name)=="literal"){
			$this->mode = 1;
			$this->current_literal = "";
			if(isset($attrs['datatype'])){
				$this->dtype = $attrs['datatype'];
			}else if(isset($attrs['DATATYPE'])){
				$this->dtype = $attrs['DATATYPE'];
			}
			if(isset($attrs['xml:lang'])){
				$this->lang = $attrs['datatype'];
			}else if(isset($attrs['XML:lang'])){
				$this->lang = $attrs['XML:lang'];
			}else if(isset($attrs['XML:LANG'])){
				$this->lang = $attrs['XML:LANG'];
			}else if(isset($attrs['xml:LANG'])){
				$this->lang = $attrs['xml:LANG'];
			}
		}
		if(strtolower($name)=="bnode"){
			$this->mode = 2;
		}
	}

	function tagData($parser, $tagData) {
		switch($this->mode){
			case 0 :
			$this->arrOutput[$this->counter][$this->varname] = new Resource($tagData);
			$this->mode = -1;
			break;
			case 1:
			$this->current_literal .= $tagData;
			break;
			case 2:
			if($tagData=="/"){
				$bn = "bNode".$this->bncounter;
				$this->bncounter++;
			}else{
				$bn = $tagData;
			}
			$this->arrOutput[$this->counter][$this->varname] = new BlankNode($bn);
			$this->mode = -1;
			break;
			case 3:
			$this->arrOutput = $tagData;
			$this->mode = -1;
			break;

		}
		
	}

	function tagClosed($parser, $name) {
		if ($this->mode == 1) {
			$lit = new Literal($this->current_literal);
			if($this->lang)
				$lit->setLanguage($this->lang);
			if($this->dtype)
				$lit->setDatatype($this->dtype);
			$this->arrOutput[$this->counter][$this->varname] = $lit ;
			$this->mode = -1;
		}		
	}
}


?>
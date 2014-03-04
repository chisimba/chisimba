<?php
//require_once('tagregsitration_class_inc.php');
class xsltemplate
{
	const NAMESPACE 	= "http://chisimba.uwc.ac.za/xmltemplate/";
	public $dom 		= null;
	public $xsltproc 	= null;
	public $customTags 	= Array();

    public function __construct($file) {
    	$this->dom = new DOMDocument();
    	$this->dom->load($file);
   		$this->xsltproc = new XsltProcessor();
   		$this->xsltproc->registerPHPFunctions();
    }

    public function execute($model) {
		//process custom tags
		foreach($this->customTags as $reg) {
			$nodelist = $this->dom->getElementsByTagNameNS($reg->namespace, $reg->tagName);
			for($i = $nodelist->length; $i > 0; $i--){
				$reg->process($nodelist->item($i-1));
			}
		}

   		$this->xsltproc->importStyleSheet($this->dom);

		$modelDom = new DomDocument();
		$modelDom->appendChild($modelDom->createElement("model")); 	//root node
		xsltemplate::makeXML($model, $modelDom->documentElement);
		//echo $modelDom->saveXML();
 		return $this->xsltproc->transformToXml($modelDom);
    }

    /** Add a new custom tag registration */
    public function registerTag($namespace, $tagName, $function) {
    	$this->customTags[] = new tagregistration($namespace, $tagName, $function);
    }

    /** Makes a XML node from an object/ array / text */
    static function makeXML($model, $parent, $addToParent = false) {
		if(is_array($model)){
			foreach($model as $name => $value){
				if(!is_numeric($name)) {
					$node = $parent->ownerDocument->createElement($name);
	    			$parent->appendChild($node);
					xsltemplate::makeXml($value, $node, true);
				} else {
					$node = $parent;
					xsltemplate::makeXml($value, $node);
				}
			}
		} elseif (is_object($model)) {
			if($addToParent)
				$node = $parent;
			else{
				$node = $parent->ownerDocument->createElement(get_class($model));
				$parent->appendChild($node);
			}
			foreach($model as $propertyName => $propertyValue){
				$property = $parent->ownerDocument->createElement($propertyName);
	    		$node->appendChild($property);
   				xsltemplate::makeXml($propertyValue, $property);
			}
		} else {
			$parent->appendChild($parent->ownerDocument->createTextNode($model));
		}
    }
}
?>
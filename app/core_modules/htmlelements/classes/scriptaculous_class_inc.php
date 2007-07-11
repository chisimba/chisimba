<?php

/**
* Class to load the Prototype and Scriptaculous JavaScript
*
* This class merely loads the JavaScript for Prototype and Scriptaculous effects
* It is not a wrapper. Developers still need to code their own JS functions
*
* @author Tohir Solomons
* @package htmlelements
*/
class scriptaculous extends object
{
    /**
	* Constructor
	*/
	public function init()
    { }

	
	/**
	* Method to load the Scriptaculous JavaScript
	*
	* @param string $mimetype Mime Type of Page
    * @return string Scriptaculous JavaScript
	*/
	public function show($mimetype)
	{
		$usingXHTML = ($mimetype == 'application/xhtml+xml') ? TRUE : FALSE;
        
        // Load Prototype
        $returnStr = $this->getJavascriptFile('prototype/1.5.0_rc1/prototype.js','htmlelements')."\n";
        
        // Load Builder
        $returnStr .= $this->getJavascriptFile('scriptaculous/1.6.5/builder.js','htmlelements')."\n";
        
        // Load Effects
        $returnStr .= $this->getJavascriptFile('scriptaculous/1.6.5/effects.js','htmlelements')."\n";
        
        // Load appropriate Drag-and-Drop
        if ($usingXHTML) {
            $returnStr .= $this->getJavascriptFile('scriptaculous/1.6.5/dragdrop_XHTML.js','htmlelements')."\n";
        } else {
            $returnStr .= $this->getJavascriptFile('scriptaculous/1.6.5/dragdrop.js','htmlelements')."\n";
        }
        
        // Load Controls
        $returnStr .= $this->getJavascriptFile('scriptaculous/1.6.5/controls.js','htmlelements')."\n";
        
        // Load Slider
        $returnStr .= $this->getJavascriptFile('scriptaculous/1.6.5/slider.js','htmlelements')."\n";
        
        
//  Do not include the scriptaculous.js file
//  It tries to write directly to the DOM which is illegal in XHTML
//  echo $this->getJavascriptFile('scriptaculous/1.7.0/scriptaculous.js','htmlelements')."\n";
//    echo $this->getJavascriptFile('scriptaculous/1.7.0/unittest.js','htmlelements')."\n";
        
        return $returnStr;
	}

}

?>
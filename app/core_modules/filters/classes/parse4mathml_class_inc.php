<?php

/**
* Class to Parse for MathML expressions and render them for display in browsers
*
* This class takes a string, and then searches for MathML expressions that are wrapped in
* [MATH] tags and renders them for proper MathML display. A sample text is:
*
* [MATH]x+y=z[/MATH]
*
* The text needs to be enclosed by the [MATH] tags, else it will be displayed as normal HTML.
*
* Two rendering options are available:
* 1) The first is to render it as an image (default)
* 2) The second is to render it in an iframe with a MathML doctype and stylesheet
*
* @author Jeremy O'Connor
* @author Tohir Solomons
*/
class parse4mathml extends object
{
	
    /**
    * @var string $renderType Mode to render MathML: either image or iframe
    */
    public $renderType = 'image';
    
    /**
    * Constructor
    */
    public function init()
    {
        try {
            // Load the Class to Render MathML as Images
            $this->objMathImg = $this->getObject('mathimg','mathml');
            // Load the Iframe Class for Rendering as Iframe
            $this->loadClass('iframe','htmlelements');
            
            //$this->objMathMLParser = $this->getObject('mathmlparser','mathml');
        } catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
    
    /**
    * Method to Parse a String for MathML expressions and render them
    * @param string $str String to Parse
    * @return string String with MathML expressions rendered as either iframes or images
    */
    public function parseAll($str)
	{
		// Search for all items in [MATH] Tags
		$search = '/\[MATH\](.*)\[\/MATH\]/U';
        
        // Get All Matches
		preg_match_all($search, $str, $matches, PREG_PATTERN_ORDER);
        
        // Check whether there are matches
		if (!empty($matches)) {
            // Go Through Matches
		    foreach ($matches[1] as $match)
            {
                // Render Result
                if ($this->renderType == 'iframe') { // Either Iframe
                    $replace = $this->renderAsIframe($match);
                } else { // Or Image
                    $replace = $this->renderAsImage($match);
                }
                
                // Replace Text
				$str = preg_replace('/'.preg_quote('[MATH]'.$match.'[/MATH]','/').'/', $replace, $str);
			}
		}
        
        // Return String
		return $str;
	}
    
    /**
    * Method to Render a MathML expression as iframe
    * @access private
    * @param string $match Expression to Render
    * @return string
    */
    private function renderAsIframe($match)
    {
        $iframe = new iframe();
        $iframe->width = 150;
        $iframe->height = 120;
        $iframe->src = $this->uri(array('action'=>'render','formula'=>$match),'mathml');
        $iframe->frameborder = '0';
        
        return $iframe->show();
    }
    
    /**
    * Method to Render a MathML expression as an Image
    * @access private
    * @param string $match Expression to Render
    * @return string Image Tag with Path to Image
    */
    private function renderAsImage($match)
    {
        return $this->objMathImg->render($match);
    }
    
    public function parse($str)
    {
    	return $this->parseAll($str);
    }
}
?>
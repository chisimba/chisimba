<?php
/* -------------------- parse4display class ----------------*/

/**
* Class for the content for display by running 
* all the available text filters on the content.
* 
* @author Derek Keats
* 
* @param string $str The string to be parsed
* @return string $str The parsed string
* 
*/
class parse4display extends object {

    /**
    * @var string $objParse4smileys The object for the smiley parser
    */
    var $objParse4smileys;
    
    /**
    * @var string $objParse4kngtext The object for the special CODE parser
    */
    var $objParse4kngtext;
    
    /**
    * @var string $objUrl The object for the special url parser
    */
    var $objUrl;

    /**
    * 
    * Constructor method loads all the required classes
    * 
    */
    function init()
    {
        //Get the smiley parser
        $this->objParse4smileys = $this->getObject('parse4smileys', 'filters');
        //Create an instance of the URL object to link URLs in the string
        $this->objUrl = $this->getObject('url', 'strings'); 
    }
    
    /**
    * Method to run all required filters and prepare
    * the main blog content for display
    * 
    * @para,
    * 
    */
    function prepare($str)
    {
        /*
        * Activate any URLS or email addresses in the content, which
        * is another feature provided by the objUrl framework extension,
        * which was instantiated earlier using
        */
        $str = $this->objUrl->makeClickableLinks($str);
        //Identify external links with a small ICON
        $str = $this->objUrl->tagExtLinks($str);
        //Parse the string for smileys
        $str = $this->objParse4smileys->parseSmiley($str);
        return $str;
    }

}  #end of class
?>
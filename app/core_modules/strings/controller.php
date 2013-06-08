<?php
class strings extends controller 
{
    /**
    * $var string $action The action to performin dispatch
    */
    public $action;

    /**
    * Standard init method, create an instance of the language object
    */
    public function init()
    { 
        // Create instance of the strings framework extensions
        $this->objRandom = $this->getObject('random');
        $this->objH = $this->getObject('highlight');
        $this->objU = $this->getObject('url');
    } 
    
        /**
     *
     * The standard dispatch method for the myprofile module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     * @access public
     * @return string The method is called and executed and its results returned
     *
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=htmlentities($this->getParam('action', 'view'));
        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
    }
    
    /**
     * 
     * Parse a URL for use in showing the site summary in content
     * similar to the way Facebook does it.
     * 
     * @access public
     * @return VOID
     * 
     */
    public function __parseurl()
    {
        $url = $this->getParam('url', FALSE);
        if ($url) {
            // Get the site snipper.
            $objSnipper = $this->getObject('snipsite', 'strings');
            // Create the HTML document.
            $doc = new DOMDocument('UTF-8');
            // The outer layer that contains it all
            $outerDiv = $doc->createElement('div');
            $outerDiv->setAttribute('class', 'sitesnippet_wraper');
            
            //  Put image in a div.
            $imgDiv = $doc->createElement('div');
            $imgDiv->setAttribute('class', 'sitesnippet_img');
            $img = $doc->createElement('img');
            $img->setAttribute('src', $objSnipper->getImage());
            $imgDiv->appendChild($img);
            $outerDiv->appendChild($imgDiv);
            
            // Put the title in a div.
            $titleDiv = $doc->createElement('div');
            $titleDiv->setAttribute('class', 'sitesnippet_title');
            $title = $objSnipper->getTitle();
            $titleDiv->appendChild($doc->createTextNode($title));
            $outerDiv->appendChild($titleDiv);
            
            // Put description in a div.
            $contentDiv = $doc->createElement('div');
            $contentDiv->setAttribute('class', 'sitesnippet_desc');
            $p = $objSnipper->getParagraph();
            $contentDiv->appendChild($doc->createTextNode($p));
            $outerDiv->appendChild($contentDiv);
           
            // Put it all in the outter div
            $doc->appendChild($outerDiv);
            // Render it out.
            $ret = $doc->saveHTML();
            die($ret);

        } else {
            die("ERROR_NOURL");
        }
    }
    

    /**
    *
    * Method to return an error when the action is not a valid
    * action method
    *
    * @access private
    * @return string The dump template populated with the error message
    *
    */
    private function __actionError()
    {
        $action=htmlentities($this->getParam('action', NULL));
        // Load an instance of the language object.
        $objLanguage = $this->getObject('language', 'language');
        $this->setVar('str', "<h3>"
          . $objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $action . "</h3>");
        return 'dump_tpl.php';
    }
    
    /**
    *
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    *
    */
    public function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Method to convert the action parameter into the name of
    * a method of this class.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    *
    */
    public function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }

    /**
    *
    * This is a method to determine if the user has to
    * be logged in or not. Note that this is an example,
    * and if you use it view will be visible to non-logged in
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action', NULL);
        switch ($action)
        {
            case 'parseurl':
            default:
                return TRUE;
                break;
        }
     }

    public function __testmodule ()
    {
        $str = "<h1>Unit test for string class</h1>\n";
        //Random
        $str .= "<h2>Testing random_class_inc.php</h2>\n";
        $str .= "Random guid: ". $this->objRandom->guid()."<br />\n";
        $str .= "MD5 Random guid: ". $this->objRandom->md5Guid()."<br />\n";
        $str .= "UserId from Random guid: "
         . $this->objRandom->getUserIdFromGuid($this->objRandom->guid())
         ."<br />\n";
        $str .= "User full name from Random guid: "
         . $this->objRandom->getFullNameFromGuid($this->objRandom->guid())
         ."<br />\n";
        $str .= "UserId from Random guid (bad guid): "
         . $this->objRandom->getUserIdFromGuid("II_99_UIIUUIOUO")
         ."<br />\n";
        $str .= "UserId from Random guid (bad guid): "
         . $this->objRandom->getFullNameFromGuid("I_99_IUIIUUIOUO")
         ."<br />\n";
         
        //Highlight
        $str .= "<h2>Testing highlight_class_inc.php</h2>\n";
        $testStr="Now is the time for all good hackers to come to the aid of the party to aid the party.";
        $this->objH->keyword="to the";
        $str .= $this->objH->show($testStr);
        
        //URL
        $str .= "<h2>Testing url_class_inc.php</h2>\n";
        $testStr="Now is the time for all good hackers to come to the aid of the party at http://www.party.com .";
        $str .= $this->objU->makeClickableLinks($testStr);
        
        $this->setVarByRef('str', $str);
        return 'main_tpl.php';
        
    }

}//end class
?>
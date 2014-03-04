<?php
/* -------------------- google search form class ----------------*/

/**
* Class to provide a google search interface, yet trap the
* search terms before sending the search to google. if not using
* the google API, then it requires a hidden iframe in the 
* search window.
* 
* @Author Derek Keats, Jameel Sauls
*/
class search extends object {

    /**
    * @var object $objuser: string to hold the user object
    */
    public $objUser;
    
    /**
    * @var object $objLanguage: string to hold the language object
    */
    public $objLanguage;

    /**
    * @var string $imgLocation: string to hold the image location
    */
    public $imgLocation;
    
    /**
    * @var string the type of interface to build
    */
    public $interfaceType;

    /**
    * Constructor method to define the table
    */
    public function init()
    {
        $this->loadClass('button', 'htmlelements');
        $this->objConfig = $this->getObject('altconfig', 'config');
      
        $this->objUser = & $this->getObject('user', 'security');
        $this->imgLocation = $this->objConfig->getModuleUri().'/websearch/resources/images/';
        $this->objUser = & $this->getObject('user', 'security');
        $this->objLanguage = & $this->getObject('language', 'language');
        //Set the default interfaceType to VERTICAL_FULL
        $this->interfaceType = "VERTICAL_FULL";
    }
    
    /**
    * 
    * Method to render the dosearch javascript
    * @deprecated
    * 
    */
    public function renderJavascript()
    {
            return $this->getJavaScriptFile('dosearch.js', 'testing');
    }
     
    
    /**
    * 
    * Method to render a form to search google using
    * the KEWL.NextGen Google api module.
    * 
    */
    public function renderGoogleApiForm($searchterm=NULL)
    {
        $this->setVar('pageSuppressXML',true);
        //Get the spacer to define the layout
        $spacer = $this->_getSpacer();
        
        //Define the grab data location
        $params = urlencode($this->cleanParams());
        //Set the post action for the form
        $formAction = $this->uri(
          array('action' => 'gapi',
            'callingModule' => $this->getParam('module', NULL),
            'params' => $params,
            'searchengine' => 'googleapi'), 'websearch');
        
        
        
        //Define the google image
       $img = "<a href=\"http://scholar.google.com/\" target=\"_blank\">"
             . "<img src=\"".$this->objConfig->getModuleUri()."/websearch/resources/images/google_40wht.gif\" "
             . "title=\"Google scholar\" align=\"absmiddle\" alt=\"Google scholar\" "
             . "border= '0' /></a>";
        
        //Check if they have a google key registered
        if ( $this->_check4Key() ) {
            //Load and create the form class
            $this->loadClass('form','htmlelements');
            $objForm = new form('websearch');
            $objForm->method = "POST";
            $objForm->setAction($formAction);
            $objForm->displayType=3;
            //Load the label class and create the form labels
            $this->loadClass('label', 'htmlelements');
            $searchLabel = new label($this->objLanguage->languageText("mod_websearch_searchterms" ,"websearch") 
              . ': ' . $spacer, 'searchterm');
            //Put the google image in the form
            $objForm->addToForm($img . $spacer);
            //Get the value of the last search
            $value=$this->_getLastSearch("googleapi");
            if ($searchterm==NULL) {
                $searchterm = $value;
            }
            //Load the text input class and use it
            $this->loadClass('textinput','htmlelements');
            $objSh = new textinput('searchterm');
            $objSh->setValue($searchterm);
            $objSh->size="20";
            $objForm->addToForm($searchLabel->show() . $objSh->show() . $spacer);
            //If its a vertical full search then add the search history
            if ($this->interfaceType == "VERTICAL_FULL") {
                //Get the past search history
                $objDb = & $this->getObject('dbsearch');
                $ar = $objDb->getSearchHistory('googleapi');
                if ( count($ar) > 0 ) {
                    //Add the past searches label to the form
                    $pastLabel = new label($this->objLanguage->languageText("mod_websearch_pastsearches","websearch") 
                      . ':<br />', 'search_selector');
                    $objForm->addToForm($pastLabel->show());
                    //Create a dropdown for the history search selector
                    $objCat = $this->newObject("dropdown", "htmlelements");
                    $objCat->name = 'search_selector';
                    $objCat->extra=" size=\"1\" class=\"coursechooser\" "
                      . "label=\"". $this->objLanguage->languageText("mod_websearch_lestofprev", "websearch")
                      . "\" onChange=\"Javascript:document."
                      . "websearch.searchterm.value=document.websearch.search_selector.value;\"";
                    $objCat->addOption(""," ");
                    $objCat->addFromDB($ar, 'searchterm', 'searchterm', $value);

                    $objForm->addToForm($objCat->show() . $spacer);
                } else {
                    $objForm->addToForm($spacer);
                }
            }
            //Define the input button
            $this->loadClass('button', 'htmlelements');
            $objSubm = new button('submit');    
            $objSubm->setToSubmit();    
            $objSubm->setValue($this->objLanguage->languageText("mod_websearch_gsearch" ,"websearch"));
            $objForm->addToForm($objSubm->show());
            //Return the for for display
            return $objForm->show();
        } else {
            $enter = "<br />-&nbsp;<a href=\"". 
              $this->uri(array('action' => 'add'), 'userparamsadmin') . "\">" 
                . $this->objLanguage->languageText("mod_websearch_link4enterkey" ,"websearch")
                . "</a>";
            $ret = "<table bgcolor=\"#FFFFFF\" width=\"135\" align=\"center\"><tr><td>\n"
              . "<font size=\"-2\"><a href=\"http://www.google.com/\" target=\"_blank\">\n"
              . $img . "</a><br />\n" . $this->_recommend() . "<br />-&nbsp;<a href=\""
              . "https://www.google.com/accounts/NewAccount?continue=http:"
              . "//api.google.com/createkey&followup=http://api.google.com/createkey\" target=\"_blank\">"
              . $this->objLanguage->languageText("mod_websearch_link4key" , "websearch") . "</a>&nbsp;"
              . $enter . "<br />This is experimental</font></td></tr></table>\n";
            return $ret . "\n\n\n\n\n\n\n\n";
        }
    
    }
    
    
    /**
    * 
    * Method to render a Scholar Google search
    * box as a block
    * 
    */
    public function renderScholarGoogleForm()
    {
    
    
          
        //Define the scholar google image
        $img = "<a href=\"http://scholar.google.com/\" target=\"_blank\">"
             . "<img src=\"".$this->objConfig->getModuleUri()."/websearch/resources/images/scholar_logo.gif\" "
             . "title=\"Google scholar\" align=\"absmiddle\" alt=\"Google scholar\" "
             . "border= '0' /></a>";
        
        //get the querystrng params
        $params = urlencode($this->cleanParams());
        //Set the scholar google search formaction
        $formAction =  $this->uri(
          array('action' => 'schgoogle',
            'callingModule' => $this->getParam('module', NULL),
            'params' => $params,
            'searchengine' => 'google_scholar'), 'websearch');
        $value=$this->_getLastSearch("googleapi");
        //Load and create the form class
        $this->loadClass('form','htmlelements');
        $objForm = new form('f');
        $objForm->setAction($formAction);
        $objForm->displayType=3;
        //Add the google scholar text and links
        $str ="<font color=\"#60a63a\" size=\"-2\">"
          . "Stand on the shoulders<br />of giants"
          . "</font>";
        //Add the image to the form
        $objForm->addToForm($img . "<br />" . $str . "<br />");
        //Load the label class and create the form labels
        $this->loadClass('label', 'htmlelements');
        $qLabel = new label($this->objLanguage->languageText("mod_websearch_searchterms" ,"websearch") 
          . ':<br />', 'q');
        //The search input box
        $value=""; //dummy for later to use with memory
        $this->loadClass('textinput','htmlelements');
        $objSh = new textinput('q');
        $objSh->setValue($value);
        $objSh->size="20";
        $objForm->addToForm($qLabel->show() . $objSh->show());
        //Add the ie hidden input
        $objSh = new textinput('ie');
        $objSh->setValue("UTF-8");
        $objSh->fldType="hidden";
        $objForm->addToForm($objSh->show());
        //Add the ie hidden input
        $objSh = new textinput('oe');
        $objSh->setValue("UTF-8");
        $objSh->fldType="hidden";
        $objForm->addToForm($objSh->show());
        //Add the ie hidden input
        $objSh = new textinput('hl');
        $objSh->setValue("en");
        $objSh->fldType="hidden";
        $objForm->addToForm($objSh->show());
        //Provide the search history
        $objDb = & $this->getObject('dbsearch');
        $ar = $objDb->getSearchHistory('google_scholar');
        if ( count($ar) > 0 ) {
            //Add the past searches label to the form
            $pastLabel = new label('<br />' 
              . $this->objLanguage->languageText("mod_websearch_pastsearches" , "websearch") 
              . ':<br />', 'scg_selector');
            $objForm->addToForm($pastLabel->show());
            //Create a dropdown for the history search selector
            $this->loadClass("dropdown", "htmlelements");
            $objCat = new dropdown();
            $objCat->name = 'scg_selector';
           
            $objCat->addOption(""," ");
            $objCat->addFromDB($ar, 'searchterm', 'searchterm', $value);
            $objForm->addToForm($objCat->show() . "<br />");
        } else {
            $objForm->addToForm("<br />");
        }
        //Define the input button
        $this->loadClass('button', 'htmlelements');
        $objSubm1 = new button('btnG');  
        $objSubm1->setToSubmit();    
        $objSubm1->setValue($this->objLanguage->languageText("mod_websearch_scsearch","websearch"));
        $objForm->addToForm($objSubm1->show()."<br />");

        //Return the form
        return $objForm->show() . "\n\n\n\n\n\n\n\n";
    }
    
    
    
    public function renderWikiPediaForm()
    {
        //Set the image
        $img = "<IMG SRC=\""
          . $this->imgLocation . "wikipedia.jpg\" "
          . "border=\"0\" ALT=\"Wikipedia\" align=\"absmiddle\">";
        //Get any parameters
        $params = urlencode($this->cleanParams());
        //Define the grab data location
        $gdLocation = $this->uri(
          array('action' => 'grab_data',
            'callingModule' => $this->getParam('module', NULL),
            'params' => $params,
            'searchengine' => 'wikipedia'), 'websearch');
        
        //Create the form and set its action
        //$formAction = "http://en.wikipedia.org/wiki/Special:Search";
        //Set the scholar google search formaction
        $formAction =  $this->uri(
          array('action' => 'wikipedia',
            'callingModule' => $this->getParam('module', NULL),
            'params' => $params,
            'searchengine' => 'wikipedia'), 'websearch');
        
        
        $this->loadClass('form','htmlelements');
        $objForm = new form('wpsearchform');
        $objForm->setAction($formAction);
        $objForm->id="wpsearchform";
        $objForm->displayType=3;
        
        //Add the wikipedia image
        $objForm->addToForm($img);
        
        //The search input box
        $value=""; //dummy for later to use with memory
        $this->loadClass('textinput','htmlelements');
        $objSh = new textinput('search');
        $objSh->setValue($value);
        $objSh->size="20";
        $objForm->addToForm($objSh->show());
        
        //Provide the search history
        $objDb = & $this->getObject('dbsearch');
        $ar = $objDb->getSearchHistory('wikipedia');
        if ( count($ar) > 0 ) {
            //Add the past searches label to the form
            $pastLabel = new label('<br />' 
              . $this->objLanguage->languageText("mod_websearch_pastsearches", "websearch") 
              . ':<br />', 'sc_selector');
            $objForm->addToForm($pastLabel->show()."\n");
            //Create a dropdown for the history search selector
            $objCat = $this->newObject("dropdown", "htmlelements");
            $objCat->name = 'sc_selector';
          //  $objCat->extra=" size=\"1\" class=\"coursechooser\" " 
          //    . "label=\"". $this->objLanguage->languageText("mod_websearch_lestofprev")
          //    . "\" onChange=\"Javascript:document."
         //     . "wpsearchform.search.value=document.wpsearchform.sc_selector.value;\"";
            $objCat->addOption(""," ");
            $objCat->addFromDB($ar, 'searchterm', 'searchterm', $value);
            $objForm->addToForm($objCat->show() . "<br />\n");
        } else {
            $objForm->addToForm("<br />");
        }
        
        //Define the go button
        $this->loadClass('button', 'htmlelements');
        $objSubm = new button('go');    
        $objSubm->setToSubmit();    
        $objSubm->setValue($this->objLanguage->languageText("word_go"));
        $objForm->addToForm( $objSubm->show()."\n" );
        
        //Define the search button
        $this->loadClass('button', 'htmlelements');
        $objSubm2 = new button('fulltext'); 
        $objSubm2->setToSubmit();   
        $objSubm2->setValue($this->objLanguage->languageText("word_search"));
        $objForm->addToForm( "&nbsp;&nbsp;&nbsp;&nbsp;" . $objSubm2->show()  . "\n\n\n\n\n\n\n\n");

      
        //Return the form
        return $objForm->show();
    }
    
    /**
    * 
    * Method to intercept a wikipedia search in order
    * to grab the search term and store it in the search
    * history
    * 
    */
    public function interceptWikipedia()
    {
        //Create the form and set its action to the search engine
        $formAction = "http://en.wikipedia.org/wiki/Special:Search";
        $this->loadClass('form','htmlelements');
        $objForm = new form('searchform');
        $objForm->setAction($formAction);
        $objForm->id="searchform";
        $objForm->displayType=3;
        
        //The search input box
        $value = $this->getParam('search', NULL);
        $this->loadClass('textinput','htmlelements');
        $objSh = new textinput('search');
        $objSh->setValue($value);
        $objSh->fldType="hidden";
        $objForm->addToForm($objSh->show());
        //The input button simulation box
        $value = $this->getParam('go', NULL);
        //Define the search button
        $this->loadClass('button', 'htmlelements');
        $objSubm2 = new button($value); 
        $objSubm2->setToSubmit();   
        $objSubm2->setValue(">>");
        $objForm->addToForm( "<noscript>" . $objSubm2->show() . "</noscript>" );
        $objForm->addToForm( "Please wait" );
        //Return the form
        return $objForm->show();
    }
    
    
    /*------------------------------- PRIVATE METHODS BELOW HERE ---------------------*/

    /**
    * Method to return the querystring with the module=modulecode
    * part removed. I use preg_replace for case insensitive replacement.
    */
    public function cleanParams()
    {
        if ( isset($_SERVER['QUERY_STRING']) ) {
            $str = $_SERVER['QUERY_STRING'];
            $str = preg_replace("/module=/i", NULL, $str);
            $module=$this->getParam('module', NULL);
            $str = preg_replace("/$module/i", NULL, $str);
        } else {
            $str = NULL;
        }
        return $str;
    } #function cleanParams
  
  
    /**
    * Method to get the last search by the user
    * @param string $searchengine The search engine, typically googleapi
    * @param string $context The context code (not yet implelened)
    * @todo -cwebsearch Implement CONTEXT code.
    */
    public function _getLastSearch($searchengine, $context=NULL)
    {
        $objDb = & $this->getObject('dbsearch');
        $filter = " WHERE userid='" 
          . $this->objUser->userId() 
          ."' AND searchengine='" 
          . $searchengine . "' ";
        $ar = $objDb->getLastEntry($filter, 'datecreated');
        if ( count($ar) > 0 ) {
            return $ar[0]['searchterm'];
        } else {
            return NULL;
        } #if ( count($ar) > 0 )
    } #function _getLastSearch
    
    /**
    * Method to show the recommendation
    */
    public function _recommend()
    {
        $ret = $this->objLanguage->languageText("mod_websearch_getkeyinstructions" ,"websearch");
        return $ret;
    } #function _recommend
    
    /**
    * 
    * Method to get the spacer to determine layout
    * (normally VERTICAL_FULL, or HORIZONTAL_SIMPLE)
    * 
    */
    public function _getSpacer()
    {
        switch($this->interfaceType){
        	case "VERTICAL_FULL": 
        		return "<br />";
        		break;
        	default:
                return "&nbsp;";
        		break;
        } // switch
    }

    /**
    * 
    * Method to check for a google API key
    * 
    */
    //test
    public function _check4Key()
    {
        $objUprm = & $this->getObject('dbuserparamsadmin', 'userparamsadmin');
        return $objUprm->checkIfSet('Google API key', $this->objUser->userId());
    } #function _check4Key
}  #end of class
?>

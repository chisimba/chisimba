<?php

class websearch extends controller 
{

    /**
    * @var object $objuser: string to hold the user object
    */
    public $objUser;
    
    /**
    * @var object $objLanguage: string to hold the language object
    */
    public $objLanguage;
    
    /**
    * Standard KINKY constructor to instantiate
    * required objects
    */
    function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
    * Dispatch function to choose what to do 
    * based on action
    */
    function dispatch()
    {   
    	  $this->setVar('pageSuppressXML',true);
        $action = $this->getParam("action", NULL);
        $userId=$this->objUser->userId();
        switch ($action) {
            case NULL:
                //$objIcon = $this->newObject('getIcon', 'htmlelements');
                //$this->setVar('str', $objIcon->getAddIcon('http://localhost/index.php?module=blog'));
                $objTst = & $this->getObject('search','websearch');
                
                $this->appendArrayVar('headerParams', $objTst->renderJavascript());
                
                $this->setVar('str', $objTst->renderGoogleApiForm());
                $this->setVar('str2', $objTst->renderScholarGoogleForm());
                
                return 'search_tpl.php';
                
            case 'show_iframe_empty':
                //Suppress instant messaging in the page (keep it simple)
                $this->setVar('pageSuppressIM', TRUE);
                //Suppress container in the page (keep it simple)
                $this->setVar('pageSuppressContainer', TRUE);
                //Suppress banner in the page (keep it simple)
                $this->setVar('pageSuppressBanner', TRUE);
                //Suppress toolbar in the page (keep it simple)
                $this->setVar('pageSuppressToolbar', TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                return 'empty_tpl.php';
                break;
            case 'wikipedia':
                //Instantiate the search object
                $objTst = $this->getObject('search', 'websearch');
                
                // Send Header Params to the header
                $headerParams = '
<script language="JavaScript" type="text/javascript">
  <!--
	function fnSubmit() {
		document.searchform.submit();
		return;
	}
  // -->
</script>';
                $this->appendArrayVar('headerParams',$headerParams);
                
                // Set Body Params
                $bodyParams = 'onLoad="fnSubmit()"';
                $this->setVarByRef('bodyParams',$bodyParams);
                
                //Grab the data
                $objGrab = & $this->getObject('dbsearch');
                $objGrab->saveRecord();
                $this->setVar( 'str', $objTst->interceptWikipedia());
                return 'interceptor_tpl.php';
                break;

            //Work with the google api
            case "gapi":
                //Get the search term from querystring
                $searchTerm = $this->getParam('searchterm', NULL);
                //If it is not in the querystring then look in the form
                if ($searchTerm==NULL) {
                    $searchTerm = $this->getParam('q', NULL);
                }
                //Instantiate the google api class
                $objGapi=$this->getObject('googleapi');
                //Initialize the starting page to the first one
                $start = $this->getParam('start', 0);
                //Do the search and set some of the properties for use
                $ar = $objGapi->doSearch($searchTerm, $start, 10);
                //Pass the properties to the template
                if ( $ar ) {
                    $this->setVarByRef('ar', $ar);
                    $this->setVar('pages', $objGapi->pages);
                    $this->setVar('count', $objGapi->theMaxResults);
                    return 'apiresults_tpl.php';
                } else {
                    $this->setVar('str', $objGapi->error);
                    return 'dump_tpl.php';
                } #if ( $ar )
                break;
                
            //Work with the scholar google search
            case "schgoogle":
                $q=urlencode($this->getParam('q', NULL));
                //---delete these when all working
                $ie=$this->getParam('ie', NULL);
                $oe=$this->getParam('oe', NULL);
                $hl=$this->getParam('hl', NULL);
                //Grab the data
                $objGrab = & $this->getObject('dbsearch');
                $objGrab->saveRecord();
                //Go get the results parsed ----------------------THIS NEEDS FURTHERE DEVELOPMENT DO NOT DELETE
                //$objSr = $this->getObject('scholarg');
                //$this->setVar('str', $objSr->doSearch());
                //return 'dump_tpl.php';
                header("Location: http://scholar.google.com/scholar?q=$q&ie=$ie&oe=$oe&hl=$hl&btnG=Search");
                break;
                
            //Process a bookmark request for search results
            case "bookmark":
                //Suppress instant messaging in the page (keep it simple)
                $this->setVar('pageSuppressIM', TRUE);
                //Suppress container in the page (keep it simple)
                $this->setVar('pageSuppressContainer', TRUE);
                //Suppress banner in the page (keep it simple)
                $this->setVar('pageSuppressBanner', TRUE);
                //Suppress toolbar in the page (keep it simple)
                $this->setVar('pageSuppressToolbar', TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                //Get the title
                $title = urldecode($this->getParam('title', NULL));
                //Get the URL
                $url = urldecode($this->getParam('url', NULL));
                //Get the description from the snippet
                $description = urldecode($this->getParam('snippet', NULL));
                //Instantiate the bookmark dataclass
                $objDbBookmark = $this->getObject('dbbookmark', 'kbookmark');
                //Instantiate the dbgroup bookmark dataclass
                $objDbGroup = $this->getObject('dbgroup', 'kbookmark');
                //Get get the user id
                $userId = $this->objUser->userId();
                //Get the default group id for bookmark
                $groupId=$objDbGroup->getDefaultId($userId);
                //Set the link into the bookmark
                $objDbBookmark->insertSingle($groupId, $title, $url,
                    $description, date("Y/m/d H:i:s"), 0, NULL,
                    $userId, 0, NULL, 0);
                $str = "<b>" . $this->objLanguage->languageText("mod_websearch_bmarkadded") . "</b>:<br>$title<br>$url";
                $this->setVarByRef('str', $str);
                //Add the self closing script
                $headerParams = '
<SCRIPT>
  var pauseTime = 10000;
  tm = null;
  function fnKillme(){
    tm = setTimeout("self.close()",pauseTime);
  }
</SCRIPT> ';
                $this->appendArrayVar('headerParams',$headerParams);
                // Set Body Params to close the window automatically
                $bodyParams = 'onLoad="fnKillme()"';
                $this->setVarByRef('bodyParams',$bodyParams);
                
                return 'bmarkres_tpl.php';
                break;

            //An unknown action must have been passed
            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown").": ".$action);
                return 'dump_tpl.php';
                break;
        } #switch
    } 
} 

?>
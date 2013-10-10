<?php
/* -------------------- googleApi class ----------------*/

/**
* Class to provide a google search interface, for scholar google.
* It is a dirty hack for now because Google Scholar is not documented
* and made public. This class therefore uses some tricks.
* 
* @Author Derek Keats 
*/
class scholarg extends object 
{
    /**
    * Standard init function
    */
    public function init()
    {
        $objConfig = $this->getObject('config', 'config');
        $this->objUser = & $this->getObject('user', 'security');
        $this->imgLocation = $objConfig->siteRoot()
          . "modules/google" . "/resources/images/";
    }

    /**
    * Method to do the search. It gets the first page as a string
    * and then hands it off for processing. Links are transformed to local
    * links before returning it.
    */
    public function doSearch()
    {
        $q=urlencode($this->getParam('q', NULL));
        $ie=$this->getParam('ie', NULL);
        $oe=$this->getParam('oe', NULL);
        $hl=$this->getParam('hl', NULL);
        //Open the file and parse it.....just testing now
        $filename = "http://scholar.google.com/scholar?q=$q&ie=$ie&oe=$oe&hl=$hl&btnG=Search";
        $f = fopen($filename, "r");
        $str ="";
        $count=0;
        while( ! feof( $f ) ) {
            $count++;
            // Get a line 
            $line = fgets($f);
            $str .= $line;
        } #while
        fclose($f);
        //Extract the body part of the webpage
        $str = $this->_getBody($str);
        //Make the image local
        $str = $this->_getImage($str);
        //Set links to open in new window
        $str = $this->_parseExtLinks($str);
        //Set the form for local processing
        $str = $this->_makeFormLocal($str);
        //Fix stopwords link
        $str = $this->_fixStopWords($str);
        //Fix the advanced search link
        $str = $this->_fixAdvancedScholarSearch($str);
        //Fix the help link
        $str = $this->_fixHelp($str);
        //Fix the main links
        $str = $this->_fixLinks($str);
        //Fix view as HTML links
        $str = $this->_fixViewAsHtml($str);
        //Fix nav images
        $str = $this->_fixNavImages($str);
        return $str;
    }
    
    /** 
    * Method to get the body of a string that contains a whole webpage
    * 
    * @param string $str The webpage as a string
    * @return string $str the body of the webpage
    * 
    */
    public function _getBody($str)
    {
        if (preg_match("/<body.*>(.*)<\/body>/iseU",$str, $body)) { 
            $ret = $body[1];
            //$ret = substr($ret,strpos(strtolower($ret),">")+1);
            //$tmp = substr($tmp,strpos(strtolower($tmp),"<")+1);
            return "\n\n\n\n\n\n" . $ret; 
        } else {
            return NULL;
        }
    }
    
    /**
    * Method to extract reference to google scholar image
    * and return the local image
    * 
    * @param string $str The webpage as a string
    * @return string $str the body of the webpage with the 
    * image replaced
    * 
    */
    public function _getImage($str)
    {
        //Define the scholar google image
        $img = "<IMG SRC=\""
          . $this->imgLocation . "scholar_logo_lg.gif\" ";
        return str_replace("<img src=\"/images/scholar_results.gif\"", 
          $img, $str);
        
    
    }
    
    /**
    * Method to replace external links with a link to open
    * in a new window.
    * 
    * @param string $str The webpage as a string
    * @return string $str the body of the webpage with the 
    * links made external
    * 
    */
    public function _parseExtLinks($str)
    {
        return str_replace("<a href=\"http://scholar.google.com/\">", 
          "<a href=\"http://scholar.google.com/\" target=\"_blank\">", 
          $str);
    }
    
    /**
    * 
    */
    public function _makeFormLocal($str)
    {
        $objWebsearch = & $this -> getObject('websearch');
        $params = urlencode($objWebsearch->cleanParams());
        //Set the scholar google search formaction
        $formAction =  $this->uri(
          array('action' => 'schgoogle',
            'callingModule' => $this->getParam('module', NULL),
            'params' => $params,
            'searchengine' => 'google_scholar'), 'google');
        return str_replace("action=\"/scholar\"", $formAction, $str);
    }
    
    public function _fixStopWords($str)
    {
        return str_replace("<a class=w href=/help/basics.html#stopwords>", 
          "<a href=\"http://scholar.google.com/help/basics.html\" target=\"_blank\">", 
          $str);
    }
    
    
    public function _fixAdvancedScholarSearch($str)
    {
        return str_replace("a href=\"/advanced_scholar_search", 
          "a target=\"_blank\" href=\"http://scholar.google.com/advanced_scholar_search", 
          $str);
    }
    
    public function _fixHelp($str)
    {
        return str_replace("<a href=\"/scholar/about.html\">", 
          "<a href=\"http://scholar.google.com/scholar/about.html\" target=\"_blank\">", 
          $str);
    }
    
    public function _fixLinks($str)
    {
        return str_replace("a href=\"/url", 
          "a target=\"_blank\" href=\"http://scholar.google.com/url", $str);
    }
    
    public function _fixViewAsHtml($str)
    {
        return str_replace("a href=\"/scholar", 
          "a target=\"_blank\" href=\"http://scholar.google.com/scholar", $str);
    }

    
   public function _fixNavImages($str)
   {
        //Define the scholar google image
        $img = "img src=\"" . $this->imgLocation . "nav";
        return str_replace("img src=/nav", $img, $str);
   }
    

} #end of class

?>
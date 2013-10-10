<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* A block that shows the last items searched
*
* @author Derek Keats

* 
* $Id: block_lastsearch_class_inc.php 4755 2006-11-14 07:30:16Z jameel $
*
*/
class block_lastsearch extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;
    
    /**
    * @var object $objLanguage String to hold the language object
    */
    public $objLanguage;
    
    /**
    * @var object $objUser String to hold the user object
    */
    public $objUser;
    
    /**
    * Standard init function to instantiate language and user objects
    * and create title
    */
    public function init()
    {
        $this->objLanguage=&$this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->title=$this->objLanguage->languageText("mod_websearch_lastsearch", "websearch");
    }
    
    /**
    * Standard block show method. It uses the dbsearch
    * class to get the data for rendering to output
    */
    public function show()
	{
	    $objSh = $this->getObject('dbsearch');
        $goo = $objSh->getLastEntry(" WHERE searchengine='googleapi' ", "datecreated");
        if ( count($goo)>0 ) {
          $objTermLink =& $this->getObject('link', 'htmlelements');
          $objTermLink->link($this->uri(array('action'=>'gapi',
                                              'searchterm'=>$goo[0]['searchterm'],
                                              'searchengine'=>'googleapi')));
          $objTermLink->link = $goo[0]['searchterm'];
          $term = $objTermLink->show();
        } else {
            $term="";
        }
        $ret = "<font size=\"-2\">";
        $ret .= "<b>Google</b>: " . $term;
        $goo = $objSh->getLastEntry(" WHERE searchengine='google_scholar' ", "datecreated");
        if ( count($goo)>0 ) {
          $objTermLink =& $this->getObject('link', 'htmlelements');
          $objTermLink->link($this->uri(array('action'=>'schgoogle',
                                              'q'=>$goo[0]['searchterm'],
                                              'searchengine'=>'googlescholar')));
          $objTermLink->link = $goo[0]['searchterm'];
          $term = $objTermLink->show();
          } else {
                $term="";
                }
        $ret .= "<br /><b>" 
          . $this->objLanguage->languageText("mod_websearch_scholarg","websearch")
          . "</b>: " . $term;
        $goo = $objSh->getLastEntry(" WHERE searchengine='wikipedia' ", "datecreated");
        if ( count($goo)>0 ) {
        $objTermLink =& $this->getObject('link', 'htmlelements');
        $objTermLink->link($this->uri(array('action'=>'wikipedia',
                                            'search'=>$goo[0]['searchterm'],
                                            'searchengine'=>'wikipedia')));
        $objTermLink->link = $goo[0]['searchterm'];
        $term = $objTermLink->show();
        } else {
            $term="";
        }
        $ret .= "<br /><b>Wikipedia</b>: " . $term;
        $ret .= "</font>";
        return $ret;
    }
}
?>

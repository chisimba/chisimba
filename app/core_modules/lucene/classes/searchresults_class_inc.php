<?php

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Indexer class extends object
 * The indexer to allow developers to add documents to the search index
 *
 * @author    Tohir Solomons
 * @package   lucene
 * @copyright AVOIR UWC
 */
class searchresults extends object
{
    
    /**
	 * Standard initialisation method
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
    public function init()
    {
        $this->loadClass('link', 'htmlelements');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    /**
     * Method to perform a search
     * @param string $text Text to search for
     * @param string $module Module to search items for
     * @return string Formatted Search Results
     */
    public function search($text, $module=NULL)
    {
        $objIndexData = $this->getObject('indexdata');
        
        $indexer = $objIndexData->checkIndexPath();
        
        $phrase = $text;
        
        if ($module != NULL) {
            $phrase .= ' module:'.$module;
            
        }
        
        $query = Zend_Search_Lucene_Search_QueryParser::parse($phrase);
		
		$hits = $indexer->find($query);
		
		$return = '<ol>';
		foreach ($hits as $hit)
		{
			$link = new link ($hit->url);
            $link->link = $hit->title;
            
            
            $return .= '<li>'.$link->show().'<br />'.$hit->teaser.'<br /><br /></li>';
		}
		$return .= '</ol>';
        
        return $return;
    }
    
}

?>
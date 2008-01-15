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
 * Search Results
 *
 * This class retrieves search results for queries. Developers have the option of allowing
 * this class to format results for them or to do so themselves
 *
 * @author    Tohir Solomons
 * @package   search
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
     * Method to perform a search. This function returns results from Zend_Lucene_Search
     * and is unpolished as it doesn't check permission issues, etc.
     * 
     * @access private
     * @param string $text Text to search for     * 
     * @param string $module Module to search items for
     * @return object
     */
    private function search($text, $module=NULL)	
    {
        $objIndexData = $this->getObject('indexdata');
        
        $indexer = $objIndexData->checkIndexPath();
        
        $phrase = $text;
        
        if ($module != NULL) {
            
            if ($text != '') {
                $phrase .= ' AND ';
            }
            $phrase .= ' module:'.$module;
            
        }
        
        $query = Zend_Search_Lucene_Search_QueryParser::parse($phrase);
        
        return $indexer->find($query);
    }
    
    /**
     * Method to retrieve search results for queries
     *
     * This method retrieves search results and then performs further chisimba checks
     * such as permissions, user access, etc. Developers can take this results and format
     * as they desire.
     * 
     * @access public
     * @param string $text Text to search for     * 
     * @param string $module Module to search items for
     * @return object
     */
    public function getSearchResults($text, $module=NULL)
    {
        // Get Results
        $results = $this->search($text, $module);
        
        // Create New array
        $filteredResults = array();
        
        // Loop through results
        foreach ($results as $item)
        {
            $permissionOk = $this->checkPermission($item);
            
            // Check Start Date
            if (isset($item->dateavailable)) {
                $startDateOK = $this->checkStartDate($item->dateavailable);
            } else {
                $startDateOK = TRUE;
            }
            
            // Check End Date
            if (isset($item->dateunavailable)) {
                $endDateOK = $this->checkEndDate($item->dateavailable);
            } else {
                $endDateOK = TRUE;
            }
            
            // Check if OK to add
            if ($permissionOk && $startDateOK && $endDateOK) {
                $filteredResults[] = $item;
            }
        }
        
        return $filteredResults;
    }
    
    /**
     * Method to check whether a user has sufficient permissions to access a search result item
     * @param string $permission
     */
    private function checkPermission($item)
    {
        switch (strtolower($item->permissions))
        {
            case 'useronly':
                if ($this->objUser->userId() == $item->userId) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            
            case 'isadmin':
                return $this->objUser->isAdmin();
            
            case 'contextonly':
                $objContext = $this->getObject('dbcontext', 'context');
                if ($objContext->getContextCode == $item->context) {
                    return TRUE;
                } else {
                    return FALSE;
                }
                
            /* These permissions need to be fixed up*/
            case 'workgrouponly':
                return TRUE;
            
            /* These are done here because we need to use a regex - must still be fixed*/
            default:
                if (preg_match('/\Aisvalid\|/', $item->permissions)) {
                    return TRUE; // Temp - fix up
                
                } else if (preg_match('/\Aismember\|/', $item->permissions)) {
                    // Temp - fix up
                    
                } else if (preg_match('/\Aiscontextmember\|/', $item->permissions)) {
                    // Temp - fix up
                    
                } else {
                    return TRUE; // Default - Return TRUE;
                }
        }
    }
    
    /**
     * Method to check whether a search result is in a display window period and can be displayed - start date check
     * @param date $startDate
     * @return boolean
     */
    private function checkStartDate($startDate)
    {
        return TRUE;
    }
    
    /**
     * Method to check whether a search result is in a display window period and can be displayed  - end date check
     * @param date $startDate
     * @return boolean
     */
    private function checkEndDate($endDate)
    {
        return TRUE;
    }
    
    /**
     * Method to display search results
     * Given a search query, this method will retrieve results and format it for display
     *
     * @access public
     * @param string $text Text to search for     * 
     * @param string $module Module to search items for
     * @return string Formatted Results
     */
    public function displaySearchResults($text, $module=NULL)
    {
        $results = $this->getSearchResults($text, $module);
        
        $return = '<ol>';
        foreach ($results as $hit)
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
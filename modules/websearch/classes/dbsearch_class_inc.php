<?php
/* ----------- data class extends dbTable for tbl_searches------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_blog
*/
class dbsearch extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init() 
    {
        parent::init('tbl_websearch');
        $this->objUser = & $this->getObject('user', 'security');
    }

    /**
    * Save method for editing a record in this table
    *@param string $mode: edit if coming from edit, add if coming from add
    */
    public function saveRecord()
    {
        $searchTerm = $this->getParam('searchterm', NULL);
        if ($searchTerm == NULL) {
            $searchTerm = $this->getParam('q', NULL);
        }
        if ($searchTerm == NULL) {
            $searchTerm = $this->getParam('search', NULL);
        }
        $userId = $this->objUser->userId();
        $params = urldecode($this->getParam('params', NULL));
        $searchengine = $this->getParam('searchengine', NULL);
        $callingModule = $this->getParam('callingModule', "_default");
        // Get the context
        $this->objDbContext = &$this->getObject('dbcontext','context');
        $context = $this->objDbContext->getContextCode();
        // Are we in a context ?
        if ($context == NULL) {
            $context = "lobby";
        }
        $this->insert(array(
            'userid' => $userId,
            'searchterm' => $searchTerm,       
            'module' => $callingModule,
            'context' => $context,
            'params' => $params,
            'searchengine' => $searchengine,
            'datecreated' => date("Y/m/d H:i:s")));
    }
    
    /**
    * Method to return an array of past searches
    * @param string $searchengine The search engine, typically googleapi
    * @param string $context The context code (not yet implelened)
    * @todo -cwebsearch Implement CONTEXT code.
    */
    public function getSearchHistory($searchengine, $context=NULL)
    {
        $sql = "SELECT searchterm FROM tbl_websearch "
          . "WHERE userid='" 
          . $this->objUser->userId() 
          ."' AND searchengine='" 
          . $searchengine . "' ORDER BY datecreated DESC";
        $ar = $this->getArray($sql);
        if ( count($ar) > 0 ) {
            return $ar;
        } else {
            return NULL;
        } #if ( count($ar) > 0 )
    } #function _getLastSearch

} #end of class
?>
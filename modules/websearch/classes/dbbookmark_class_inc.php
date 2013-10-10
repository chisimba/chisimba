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
        parent::init('tbl_searches');
        $this->objUser = & $this->getObject('user', 'security');
    }

    /**
    * Save method for editing a record in this table
    *@param string $mode: edit if coming from edit, add if coming from add
    */
    public function addBookmark()
    {
        $searchTerm = $this->getParam('searchterm', NULL);
        $userId = $this->objUser->userId();
        $params = urldecode($this->getParam('params', NULL));
        $searchengine = $this->getParam('searchengine', NULL);
        $callingModule = $this->getParam('callingModule', NULL);

        $this->insert(array(
            'userid' => $userId,
            'searchTerm' => $searchTerm,
            'params' => $params,
            'module' => $callingModule,
            'searchengine' => $searchengine,
            'datecreated' => date("Y/m/d H:i:s")));
    }


} #end of class
?>
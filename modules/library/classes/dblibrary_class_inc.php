<?php
/* ----------- data class extends dbTable for tbl_library------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_library
*/
class dblibrary extends dbTable
{
    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_library');
        $this->objUser = & $this->getObject("user", "security");
    }

    /**
    * Save method for editing a record in this table
    *@param string $mode: edit if coming from edit, add if coming from add
    */
    function saveRecord($mode, $userId)
    {
        $id=$this->getParam('id', NULL);
        $title = $this->getParam('title', NULL);
        $description = $this->getParam('description', NULL);
        $url = $this->getParam('url', NULL);
        $creatorId = $this->getParam('creatorId', NULL);
        $dateCreated = $this->getParam('dateCreated', NULL);
        $modifierId = $this->getParam('modifierId', NULL);
        $dateModified = $this->getParam('dateModified', NULL);

        // if edit use update
        if ($mode=="edit") {
            $this->update("id", $id, array(
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'modifierId' => $this->objUser->userId(),
            'dateModified' => date("Y/m/d H:i:s")));

        }#if
        // if add use insert
        if ($mode=="add") {
            $this->insert(array(
              'title' => $title,
              'description' => $description,
              'url' => $url,
            'creatorid' => $this->objUser->userId(),
            'datecreated' => date("Y/m/d H:i:s")));

        }#if
    }#function



} #end of class
?>
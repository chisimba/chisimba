<?php
/* ----------- data class extends dbTable for tbl_storycategory------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_storycategory
*/
class dbstorycategory extends dbTable
{
    /**
    * @var string $errCode Code to return if there is an error
    */
    var $errCode;

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_storycategory');
        $this->objUser = & $this->getObject("user", "security");
        $this->objLanguage = & $this->getObject("language", "language");
    }

    /**
    * Save method for editing a record in this table
    *@param string $mode: edit if coming from edit, add if coming from add
    */
    function saveRecord($mode, $userId)
    {
        $id=$this->getParam('id', NULL);
        $category = $this->getParam('category', NULL);
        $title = $this->getParam('title', NULL);
        $modified = $this->getParam('modified', NULL);

        // if edit use update
        if ($mode=="edit") {
            $this->update("id", $id, array(
            'category' => $category,
            'title' => $title,
            'dateModified' => date("Y/m/d H:i:s"),
            'modifierId' => $this->objUser->userId()));
            return TRUE;
        }#if
        // if add use insert
        if ($mode=="add") {
            if ($this->checkIfExists($category)) {
                $this->errCode=$this->objLanguage->code2Txt("mod_storycategory_exists", 'storycategoryadmin')
                  . ": " . $category;
                return FALSE;
            } else {
                $this->insert(array(
                'category' => $category,
                'title' => $title,
                'dateCreated' => date("Y/m/d H:i:s"),
                'creatorId' => $this->objUser->userId()));
                return TRUE;
            }
        }#if
    }#function

    /**
    * 
    * Method to check if a category exists
    * 
    * @param string $category The category to check if it exists
    * @return TRUE|FALSE and set an error code if TRUE
    * 
    */
    function checkIfExists($category)
    {
        if ($this->valueExists('category', $category)) {
            return TRUE;
        } else {
            return FALSE;
        }
        
    }

} #end of class
?>
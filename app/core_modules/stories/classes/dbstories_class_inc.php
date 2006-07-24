<?php
/* -------------------- stories class ----------------*/

/**
* Class for the stories table in the database
*/
class dbStories extends dbTable
{

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_stories');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language', 'language');
    }

    /**
    * Save method for groups admin
    * @param string $mode: edit if coming from edit, add if coming from add
    */
    function saveStories($mode) {
        //The current user
        $userId = $this->objUser->userId();
        //Get data from form
        $category = TRIM($this->getParam('category', NULL));
        $isActive = TRIM($this->getParam('isActive', NULL));
        $parentId = TRIM($this->getParam('parentId', NULL));
        //Set Id to default if missing
        if ($parentId=='') {
            $parentId='base';
        }
        $language = TRIM($this->getParam('language', NULL));
        $title = TRIM($this->getParam('title', NULL));
        $abstract  = TRIM($this->getParam('abstract', NULL));
        $mainText = TRIM($this->getParam('mainText', NULL));
        $isSticky = TRIM($this->getParam('isSticky', NULL));
        $forceOrder = TRIM($this->getParam('forceOrder', NULL));
        $expirationDate = TRIM($this->getParam('expirationDate', NULL));
        // if edit use update
        if ($mode=="edit") {
            $rsArray=array(
                'category'=>$category,
                'parentId'=>$parentId,
                'language'=>$language,
                'isActive'=>$isActive,
                'title'=>$title,
                'abstract'=>$abstract,
                'mainText'=>$mainText,
                'isSticky'=>$isSticky,
                'modifierId'=>$userId,
                'dateModified'=>date('Y-m-d H:m:s'),
                'expirationDate'=>$expirationDate);
            $id = TRIM($this->getParam('id', NULL));
            $this->update("id", $id, $rsArray);
        } elseif ($mode=="add" || $mode="translate") {
            $this->insert(array(
              'category'=>$category,
              'parentId'=>$parentId,
              'language'=>$language,
              'isActive'=>$isActive,
              'title'=>$title,
              'abstract'=>$abstract,
              'mainText'=>$mainText,
              'isSticky'=>$isSticky,
              'creatorId'=>$userId,
              'dateCreated'=>date('Y-m-d H:m:s'),
              'expirationDate'=>$expirationDate));
        }
    }


    /**
    * Method to ask for confirmation to delete a group
    * @param string $keyvalue The group to be deleted
    */
    function deleteStoriesConfirm($key, $keyvalue) {
        global  $objButtons, $objLanguage, $objSkin, $objUser;
        include ('templates/confirmdelete_tpl.php');          # includes the form template
    }


    /**
    * Method to delete a group
    * @param string $keyvalue The group to be deleted
    */
    function deleteStories($key, $keyvalue) {
        $this->delete($key, $keyvalue);
    }

    /**
    * Method to retrieve the data for edit and prepare the vars for
    * the edit template.
    *
    * @param string $mode The mode should be edit or add
    */
    function getForEdit()
    {
        $order = $this->getParam("order", NULL);
        // retrieve the group ID from the querystring
        $keyvalue=$this->getParam("id", NULL);
        if (!$keyvalue) {
          die($this->objLanguage->languageText("modules_badkey",'stories').": ".$keyvalue);
        }
        // Get the data for edit
        $key="id";
        return $this->getRow($key, $keyvalue);
    }

    /**
    *
    * Method to return an array of all stories without the abstract and text
    * @param string $filter.
    *
    */
    function fetchStories($filter = NULL)
    {
        $sql="SELECT id, category, creatorId, isActive,
           parentId, language, title, dateCreated,
           expirationDate, notificationDate, isSticky
           FROM tbl_stories ";

        if($filter){
            $sql .= "WHERE $filter";
        }

        $sql .= "ORDER BY isSticky DESC, dateCreated DESC";
        return $this->getArray($sql);
    }

}  #end of class
?>
<?php
    /* -------------------- dbTable class ----------------*/
        // security check - must be included in all scripts

        if (!$GLOBALS['kewl_entry_point_run'])
        {
            die("You cannot view this page directly");
        }

        // end security check
    /**
    * The User Permissions class for CMS.
    * This class allows for the assigning of complex User specific restrictions.
    *
    * e.g. To check if a user has the right to post content to the front page.
    *
    * @package cmsadmin
    * @category chisimba
    * @copyright AVOIR 
    * @license GNU GPL
    * @author Charl Mert <charl.mert@gmail.com>
    */

        class dbuserpermissions extends dbTable
        {

        /**
        * The language object
        *
        * @access private
        * @var object
        */
            protected $_objLanguage;
            
        /**
        * The user object
        *
        * @var object
        */
            protected $_objUser;

        /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {        
                parent::init('tbl_cms_user_permissions');
                $this->table = 'tbl_cms_user_permissions';
                $this->_objLanguage = $this->getObject('language', 'language');
                //$this->_objSections =  $this->getObject('dbsections', 'cmsadmin');
                $this->_objUser =  $this->getObject('user', 'security');
                $this->_objGroupAdmin =  $this->getObject('groupadminmodel', 'groupadmin');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
        * Method to get a row from the DB
        *
        * @access public
        * @param id
        * @return array
        */
        public function getRecord($id= NULL)
        {
            $row = $this->getRow('id', $id); 
            return $row;    
        }

        /**
        * Method to delete a record
        *
        * @access public
        * @param id
        * @return boolean TRUE on success and FALSE on failure
        */
        public function deleteRecord($id = NULL)
        {
            $result = $this->delete('id', $id); 
            return $result;    
        }

        /**
        * Method to add a user to grant/revoke certain rights to
        *
        * @access public
        * @param userId The userId of the person who may add content to the frontpage
        * @return boolean
        */
        public function addUserPermission($userId = NULL, $canAdd = TRUE)
        {
            $fields['user_id'] = $userId;
            $fields['show_on_frontpage'] = $canAdd;

            //Checking for duplicates against user_id
            $row = $this->getRow('user_id',$userId);
            if (isset($row['user_id']) && $row['user_id'] != '') {
                // Update
                return $this->update('user_id', $userId, $fields);
            }

            //else insert
            return $this->insert($fields);
        }

        /**
        * Method to add a user to grant/revoke certain rights to
        *
        * @access public
        * @param userId The userId of the person who may add content to the frontpage
        * @return boolean
        */
        public function editUserPermission($userId = NULL, $canAdd = TRUE)
        {
            $fields['user_id'] = $userId;
            $fields['show_on_frontpage'] = $canAdd;
            return $this->update('user_id', $userId, $fields);
        }


        /**
        * Method to set weather or not a user can edit the front page contents.
        *
        * @access public
        * @param userId The userId of the person who may add content to the frontpage
        * @return boolean
        */
        public function setCanAddToFrontPage($userId = NULL, $canAdd = TRUE)
        {
            $fields['show_on_frontpage'] = $canAdd;
            return $this->update('user_id', $userId, $fields);
        }


        /**
        * Method to check if the user can edit User specific permissions
        * Currently only site administrators can edit these
        * @access public
        * @param contentid, userid
        * @return boolean
        */
        public function canEditUserPermissions($userId = NULL)
        {

            if ($userId == NULL){
                $userId = $this->_objUser->userId();
            }

            //Admin Can
            if ($this->_objUser->isAdmin()){
                return TRUE;
            }
            return FALSE;
        }

        /**
        * Method to check if the user can write to frontpage
        *
        * @access public
        * @param contentid, userid
        * @return boolean
        */
        public function canAddToFrontPage($userId = NULL)
        {

            if ($userId == NULL){
                $userId = $this->_objUser->userId();
            }

            //Admin Can Do All
            if ($this->_objUser->isAdmin()){
                return TRUE;
            }

            $row = $this->getRow('user_id', $userId);
            
            if (isset($row) && $row['show_on_frontpage'] == TRUE) { 
                return  TRUE;
            } else {
                return FALSE;
            }
        
        }


    }
?>

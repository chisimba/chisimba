<?php

/* -------------------- dbTable class ----------------*/
    // security check - must be included in all scripts

    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

    // end security check
/**
 * Security class for the cmsadmin module. Used to manage/restrict/allow security privileges
 * from and to each content item as well as every section.
 *
 * The current permissions allowable are read and write.
 *
 * The Content Item level permissions takes precedence i.e. a user can be denied access to 
 * an individual piece of content when he/she has access to the section
 *
 * Ownership can ownly be granted by the content owner, the section owner or admin.
 *
 * 
 * The Default policy is to allow everyone read access, the owner read/write and the administrators read/write
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR 
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 */

    class dbsecurity extends dbTable
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
                parent::init('tbl_cms_sections');
                $this->table = 'tbl_cms_sections';
                $this->_objLanguage = $this->getObject('language', 'language');
                $this->_objUser =  $this->getObject('user', 'security');
                $this->_objGroupAdmin =  $this->getObject('groupadminmodel', 'groupadmin');
                $this->_objContentGroup = $this->getObject('dbcontent_group');
                $this->_objContentUser = $this->getObject('dbcontent_user');
                $this->_objSectionGroup = $this->getObject('dbsection_group');
                $this->_objSectionUser = $this->getObject('dbsection_user');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }



            /**
             * Method to get a content row from the DB
             *
             * @access public
             * @param sectionid
             * @return array
             */
        public function getContentRow($contentid = NULL)
        {
            $this->_tableName = 'tbl_cms_content';
            $row = $this->getRow('id', $contentid);	
            $this->_tableName = 'tbl_cms_sections';
            return $row;	
        }


            /**
             * Method to get a section row from the DB
             *
             * @access public
             * @param sectionid
             * @return array
             */
        public function getSectionRow($sectionid = NULL)
        {
            $this->_tableName = 'tbl_cms_sections';
            return $this->getRow('id', $sectionid);	
        }


            /**
             * Method to set the owner of the specified section
             *
             * @access public
             * @param sectionid, userid
             * @return boolean
             */
        public function setOwner($sectionid = NULL, $userid = null)
        {
            $this->_tableName = 'tbl_cms_sections';
            $fields['userid'] = $userid;

            $this->update('id', $sectionid, $fields);
        }



            /**
             * Method to set the owner of the specified sections child CONTENT
             *
             * @access public
             * @param sectionid, userid
             * @return boolean
             */
        public function setContentOwnerPropagate($sectionid = NULL, $userid = null)
        {

            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //Get A List of all child contents
                $subContent = $this->getPagesInSection($subSecId);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];
                    $this->setContentOwner($subContId, $userid);
                }

                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->setPermissionsGroupPropagate($subSecId, $groupid, $read_access, $write_access);
                }
            }

            //Get A List of all child contents
            $subContent = $this->getPagesInSection($sectionid);
            foreach($subContent as $content){
                //Apply Permissions to child contents
                $subContId = $content['id'];
                $this->setContentOwner($subContId, $userid);
            }

        }



            /**
             * Method to set the owner of the specified sections child SECTIONS
             *
             * @access public
             * @param sectionid, userid
             * @return boolean
             */
        public function setOwnerPropagate($sectionid = NULL, $userid = null)
        {

            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //Applying Permissions to child content
                $this->setOwner($subSecId, $userid);

                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->setPermissionsGroupPropagate($subSecId, $groupid, $read_access, $write_access);
                }
            }

        }

    /**
     * Method to check the users WRITE access to a specific content
     *
     * @access public
     * @param contentid, userid
     * @return boolean
     */
        public function canUserWriteContent($contentId, $userId = NULL)
        {
            if ($userId == NULL){
                $userId = $this->_objUser->userId();
            }
            
            // Admin Edits All
            if ($this->_objUser->isAdmin()){
                return TRUE;
            }
            
            if (!$this->_objUser->isLoggedIn() || !$this->_objGroupAdmin->isGroupMember($userId, $this->_objGroupAdmin->getId('CMSAuthors'))) {
                return FALSE;
            }
            
            // Check user permissions first
            //$userRecord = $this->_objContentUser->getRow('user_id', $userId);
            $userRecord = $this->_objContentUser->getAll("WHERE content_id = '$contentId' AND user_id = '$userId'");
            if (isset($userRecord[0])) {
                if ($userRecord[0]['write_access'] == 1) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
            
            // Now check groups
            $groups = $this->_objContentGroup->getAll("WHERE content_id = '$contentId' AND write_access = '1'");
            foreach ($groups as $group) {
                if ($this->_objGroupAdmin->isGroupMember($userId, $group['group_id'])) {
                    return TRUE;
                }
            }
            
            // If nothing matched return false
            return FALSE;
        }



    /**
     * Method to checck the users READ access to a specific content
     *
     * @access public
     * @param contentid, userid
     * @return boolean
     */
        public function canUserReadContent($contentid, $userid = NULL)
        {	
            //Admin Sees All
            if ($this->_objUser->isAdmin()){
                return TRUE;
            }
            return true;
            if ($userid == NULL){
                $userid = $this->_objUser->userId();
            }

            //Checking Public Access
            if (!$this->isContentPublic($contentid)){
				//echo ($this->isContentPublic($contentid)? 'TRUE': 'FALSE')." : isContentPublic"; exit;
				$this->setVar('SECURITY_IS_CONTENT_PUBLIC', FALSE);
                return false;        
            }
            
            //User ID to compare with when checking groups is the
            //id column of the tbl_groupadmin_group_users table and NOT the uaerid column		
            $sql = "SELECT id from tbl_users WHERE userId = '$userid'";
            $data = $this->getArray($sql);
            if (isset($data[0])) {
                $userRawId = $data[0]['id'];
            }



            //Preparing a list of USER ID's
            $usersList = $this->getAssignedContentUsers($contentid);
            $usersCount = count($usersList);

            //Preparing a list of GROUP_ID's
            $groupsList = $this->getAssignedContentGroups($contentid);
            $groupsCount = count($groupsList);

            //Default READ Policy (If there aren't any users/groups defined then the user can read)
            if ($usersCount == 0 && $groupsCount == 0){
                return true;
            }				

            $globalChkCounter = 0;

            $content_row = $this->getContentRow($sectionid);
            $ownerid = $content_row['userid'];

            //If user is the owner of this content then offcourse return true
            if ($userid == $ownerid){
                return true;
            }

            //Checking Users
            $isMemberDefined = false;
            for ($x = 0; $x < $usersCount; $x++){
                $memberId = $usersList[$x]['user_id'];
                $memberReadAccess = $usersList[$x]['read_access'];
                $memberWriteAccess = $usersList[$x]['write_access'];

                $canRead = (($memberReadAccess == 1) ? true : false);
                $canWrite = (($memberWriteAccess == 1) ? true : false);
                $canPublic = (($memberPublicAccess == 1) ? true : false);

                //Checking Public vs Logged in access
                if (!$canPublic){
                    if (!$this->_objUser->isLoggedIn()){
                        return false;
                    }
                }            
            
                if ($canRead){
                    if ($userid == $memberId){
                        return true;
                    }
                }

                if ($memberId == $userid){
                    $isMemberDefined = true;
                }


            }       //End Loop

            //Only checking GROUPS when a user hasn't been denied access
            //Users are denied access by adding them to the Users list and taking away the
            //respective permissions

            if (!$isMemberDefined){
                //No luck, lets check the groups that the user belongs to
                //Displaying Groups
                for ($x = 0; $x < $groupsCount; $x++){
                    $memberId = $groupsList[$x]['group_id'];
                    $memberReadAccess = $groupsList[$x]['read_access'];
                    $memberWriteAccess = $groupsList[$x]['write_access'];

                    $canRead = (($memberReadAccess == 1) ? true : false);
                    $canWrite = (($memberWriteAccess == 1) ? true : false);
                    $canPublic = (($memberPublicAccess == 1) ? true : false);

                    //Checking Public vs Logged in access
                    if (!$canPublic){
                        if (!$this->_objUser->isLoggedIn()){
                            return false;
                        }
                    }        
            
                    if ($canRead){
                        $this->tableName = 'tbl_groupadmin_groupuser';
                        $isGroupMember = $this->_objGroupAdmin->isGroupMember($userRawId, $memberId);

                        if ($isGroupMember){
                            return true;
                        }

                    }
                }       //End Loop
            }

        }

    /**
     * Method to checck the users WRITE access to a specific section
     *
     * @access public
     * @param sectionid, userid
     * @return boolean
     */
        public function canUserWriteSection($sectionId, $userId = NULL)
        {
        
           if ($userId == NULL){
                $userId = $this->_objUser->userId();
            }
            
            // Admin Edits All
            if ($this->_objUser->isAdmin()){
                return TRUE;
            }
            
            if (!$this->_objUser->isLoggedIn() || !$this->_objGroupAdmin->isGroupMember($userId, $this->_objGroupAdmin->getId('CMSAuthors'))) {
                return FALSE;
            }

            
            // Check user permissions first
            //$userRecord = $this->_objSectionUser->getRow('user_id', $userId);
            $userRecord = $this->_objSectionUser->getAll("WHERE section_id = '$sectionId' AND user_id = '$userId'");
            if (isset($userRecord[0])) {
                if ($userRecord[0]['write_access'] == 1) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
            
            // Now check groups
            $groups = $this->_objSectionGroup->getAll("WHERE section_id = '$sectionId' AND write_access = '1'");
            foreach ($groups as $group) {
                if ($this->_objGroupAdmin->isGroupMember($userId, $group['group_id'])) {
                    return TRUE;
                }
            }
            
            // If nothing matched return false
            return FALSE;
        }



    /**
     * Method to checck the users READ access to a specific section
     *
     * @access public
     * @param sectionid, userid
     * @return boolean
     */
        public function canUserReadSection($sectionid = NULL, $userid = null)
        {	
            //Admin Sees All
            if ($this->_objUser->isAdmin()){
                return true;
            }
return true;
            //Checking Public Access
            if (!$this->isSectionPublic($sectionid)){
                return false;        
            }
        
            if ($userid == NULL){
                $userid = $this->_objUser->userId();
            }

            //User ID to compare with when checking groups is the
            //id column of the tbl_groupadmin_group_users table and NOT the uaerid column		
            $sql = "SELECT id from tbl_users WHERE userId = '$userid'";
            $data = $this->getArray($sql);
            if (isset($data[0])) {
                $userRawId = $data[0]['id'];
            }


            //Preparing a list of USER ID's
            $usersList = $this->getAssignedSectionUsers($sectionid);
            $usersCount = count($usersList);

            //Preparing a list of GROUP_ID's
            $groupsList = $this->getAssignedSectionGroups($sectionid);
            $groupsCount = count($groupsList);

            //Default READ Policy (If there aren't any users/groups defined then the user can read)
            if ($usersCount == 0 && $groupsCount == 0){
                return true;
            }

            $globalChkCounter = 0;

            $section_row = $this->getSectionRow($sectionid);
            $ownerid = $section_row['userid'];

            //If user is the owner of this section then offcourse return true
            if ($userid == $ownerid){
                return true;
            }

            //Checking Users
            $isMemberDefined = false;
            for ($x = 0; $x < $usersCount; $x++){
                $memberId = $usersList[$x]['user_id'];
                $memberReadAccess = $usersList[$x]['read_access'];
                $memberWriteAccess = $usersList[$x]['write_access'];

                $canRead = (($memberReadAccess == 1) ? true : false);
                $canWrite = (($memberWriteAccess == 1) ? true : false);
                $canPublic = (($memberPublicAccess == 1) ? true : false);

                //Checking Public vs Logged in access
                if (!$canPublic){
                    if (!$this->_objUser->isLoggedIn()){
                        return false;
                    }
                }
            
                if ($canRead){
                    if ($userid == $memberId){
                        return true;
                    }
                }

                if ($memberId == $userid){
                    $isMemberDefined = true;
                }



            }       //End Loop


            //No luck, lets check the groups that the user belongs to
            //Displaying Groups
            if (!$isMemberDefined){

                for ($x = 0; $x < $groupsCount; $x++){
                    $memberId = $groupsList[$x]['group_id'];
                    $memberReadAccess = $groupsList[$x]['read_access'];
                    $memberWriteAccess = $groupsList[$x]['write_access'];

                    $canRead = (($memberReadAccess == 1) ? true : false);
                    $canWrite = (($memberWriteAccess == 1) ? true : false);
                    $canPublic = (($memberPublicAccess == 1) ? true : false);

                    //Checking Public vs Logged in access
                    if (!$canPublic){
                        if (!$this->_objUser->isLoggedIn()){
                            return false;                        
                        }
                    }
                
                    if ($canRead){
                        if ($this->_objGroupAdmin->isGroupMember($userRawId, $memberId)){
                            return true;
                        }	
                    }
                }       //End Loop
            }	

            $sql = "";	
        }



    /**
     * Method to SET the PERMISSIONS for a SECTIONS USER 
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setPermissionsUser($sectionid, $userid, $read_access, $write_access)
        {
            $this->_tableName = 'tbl_cms_section_user';
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;

            $sql = "SELECT id FROM tbl_cms_section_user 
                    WHERE section_id = '$sectionid'
                        AND user_id = '$userid'";

            $data = $this->getArray($sql);

            if (count($data) > 0){		
                if (isset($data[0])) {
                    $id = $data[0]['id'];
                }
                $this->update('id', $id, $fields);
            } else {
                //section user mapping not found
            }
            $this->_tableName = 'tbl_cms_sections';
        }




    /**
     * Method to SET the PERMISSIONS for a SECTIONS GROUP
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setPermissionsGroup($sectionid = NULL, $groupid, $read_access, $write_access)
        {
            $this->_tableName = 'tbl_cms_section_group';
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;

            $sql = "SELECT id FROM tbl_cms_section_group
                                            WHERE section_id = '$sectionid'
                                            AND   group_id = '$groupid'";

            $data = $this->getArray($sql);

            if (count($data) > 0){
                if (isset($data[0])) {
                    $id = $data[0]['id'];
                }
                $this->update('id', $id, $fields);
            } else {
                //section user mapping not found
            }

            $this->_tableName = 'tbl_cms_sections';
        }


    /**
     * Method to get all pages in a specific section
     *
     * @param string $sectionId The id of the section
     * @return array $pages An array of all pages in the section
     * @access public
     * @author Warren Windvogel
     */
        public function getPagesInSection($sectionId, $isPublished=FALSE)
        {
            $this->_tableName = 'tbl_cms_content';

            $filter = "WHERE sectionid = '$sectionId' AND trash='0' ";
            if($isPublished){
                $filter .= "AND published='1' ";
            }
            $pages = $this->getAll($filter.' ORDER BY ordering');

            $secureData = array();
            foreach ($pages as $d){
                if ($this->canUserReadContent($d['id'])){
                    array_push($secureData, $d);
                }
            }
            $this->_tableName = 'tbl_cms_sections';
            return $secureData;
        }






    /**
     * Method to SET the PERMISSIONS for all CONTENT USER for a given section and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setContentPermissionsUserPropagate($sectionid = NULL, $userid, $read_access, $write_access)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //Get A List of all child contents
                $subContent = $this->getPagesInSection($subSecId);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];

                    $this->addContentPermissionsUser($subContId, $userid, $read_access, $write_access, TRUE);
                }


                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->setContentPermissionsUserPropagate($subSecId, $userid, $read_access, $write_access);
                }
            }
            //Get A List of all child contents for the main section
            $subContent = $this->getPagesInSection($sectionid);
            foreach($subContent as $content){
                //Apply Permissions to child contents
                $subContId = $content['id'];
                $this->addContentPermissionsUser($subContId, $userid, $read_access, $write_access, TRUE);
            }


            return true;
        }	















    /**
     * Method to SET the PERMISSIONS for all CONTENT GROUP for a given section and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setContentPermissionsGroupPropagate($sectionid = NULL, $groupid, $read_access, $write_access)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //Get A List of all child contents
                $subContent = $this->getPagesInSection($subSecId);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];
                    $this->addContentPermissionsGroup($subContId, $groupid, $read_access, $write_access, TRUE);
                }


                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->setContentPermissionsGroupPropagate($subSecId, $groupid, $read_access, $write_access);
                }
            }
            //Get A List of all child contents for the main section
            $subContent = $this->getPagesInSection($sectionid);
            foreach($subContent as $content){
                //Apply Permissions to child contents
                $subContId = $content['id'];
                $this->addContentPermissionsGroup($subContId, $groupid, $read_access, $write_access, TRUE);
            }


            return true;
        }	

    /**
     * Method to SET the PERMISSIONS for a SECTIONS USER and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setPermissionsUserPropagate($sectionid, $userid, $read_access, $write_access)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //Applying Permissions to child content
                $this->addSectionPermissionsUser($subSecId, $userid, $read_access, $write_access, TRUE);

                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->setPermissionsUserPropagate($subSecId, $userid, $read_access, $write_access);
                }
            }
        }





    /**
     * Method to DELETE the PERMISSIONS for a SECTIONS USER and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function deletePermissionsUserPropagate($sectionid = NULL)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //DELETING ALL Section Permissions for the given sectionid
                $this->deleteAllSectionPermissionsUser($subSecId);

                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->deletePermissionsUserPropagate($subSecId);
                }
            }
        }	




    /**
     * Method to DELETE the PERMISSIONS for a CONTENT USER and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function deleteContentPermissionsUserPropagate($sectionid = NULL)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //DELETING ALL Section Permissions for the given sectionid
                //Get A List of all child contents
                $subContent = $this->getPagesInSection($subSecId);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];
                    $this->deleteAllContentPermissionsUser($subContId);
                }


                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->deleteContentPermissionsGroupPropagate($subSecId, $groupid, $read_access, $write_access);
                }

                //DELETING ALL Section Permissions for the given sectionid
                //Get A List of all child contents
                $subContent = $this->getPagesInSection($sectionid);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];
                    //echo "CONTENT ID $subContId <br/>";
                    $this->deleteAllContentPermissionsUser($subContId);
                }


            }
        }	






    /**
     * Method to DELETE the PERMISSIONS for a CONTENT GROUP and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function deleteContentPermissionsGroupPropagate($sectionid = NULL)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //DELETING ALL Section Permissions for the given sectionid
                //Get A List of all child contents
                $subContent = $this->getPagesInSection($subSecId);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];
                    $this->deleteAllContentPermissionsGroup($subContId);
                }


                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->deleteContentPermissionsGroupPropagate($subSecId, $groupid, $read_access, $write_access);
                }

                //DELETING ALL Section Permissions for the given sectionid
                //Get A List of all child contents
                $subContent = $this->getPagesInSection($sectionid);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];
                    //echo "CONTENT ID $subContId <br/>";
                    $this->deleteAllContentPermissionsGroup($subContId);
                }


            }
        }	









    /**
     * Method to DELETE the PERMISSIONS for a SECTIONS GROUP and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function deletePermissionsGroupPropagate($sectionid = NULL)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //DELETING ALL Section Permissions for the given sectionid
                $this->deleteAllSectionPermissionsGroup($subSecId);

                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->deletePermissionsGroupPropagate($subSecId);
                }
            }
        }	





    /**
     * Method to SET the PERMISSIONS for a SECTIONS GROUP and all of it's child Content and subsections
     *
     * @access public
     * @param $sectionid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setPermissionsGroupPropagate($sectionid = NULL, $groupid, $read_access, $write_access)
        {
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

                //Applying Permissions to child content
                $this->addSectionPermissionsGroup($subSecId, $groupid, $read_access, $write_access, true);

                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->setPermissionsGroupPropagate($subSecId, $groupid, $read_access, $write_access);
                }
            }



        }

    /**
     * Method to check if a section has child/leaf node(s)
     *
     * @param string $id The id(pk) of the section
     * @return bool True if has nodes else False
     * @access public
     */
        public function hasNodes($id)
        {
            $nodes = $this->getAll("WHERE parentid = '$id' AND trash = 0");

            if (count($nodes) > 0) {
                $hasNodes = True;
            } else {
                $hasNodes = False;
            }
            return $hasNodes;
        }


    /**
     * Method to get all subsections in a specific section
     *
     * @param string $sectionId The id(pk) of the section
     * @param int $level The node level in question
     * @param string $order Either DESC or ASC
     * @param bool $isPublished TRUE | FALSE To get published sections
     * @return array $subsections An array of associative arrays for all categories in the section
     * @access public
     */
        public function getSubSectionsInSection($sectionId, $order = 'ASC', $isPublished = FALSE)
        {
            $this->_tableName = 'tbl_cms_sections';
            //echo "Section ID Get SubSections : ".$sectionId;
            if ($isPublished) {
                //return all subsections
                $secureSections = array();
                $sections = $this->getAll("WHERE published = 1 AND parentid = '$sectionId' AND trash = 0 ORDER BY ordering $order");

                foreach ($sections as $sec){
                    if ($this->canUserReadSection($sec['id'])){
                        array_push($secureSections, $sec);
                    }
                }

                return $secureSections;
            } else {
                //$secureSections = array();
                $sections = $this->getAll("WHERE parentid = '$sectionId' AND trash = 0 ORDER BY ordering $order");
                //foreach ($sections as $sec){
                //    if ($this->canUserReadSection($sec['id'])){
                //        array_push($secureSections, $sec);
                //    }
                //}
                //return $secureSections;
                return $sections;
            }
        }


    /**
     * Method to SET the owner of the specified CONTENT
     *
     * @access public
     * @param contentid, userid
     * @return boolean
     */
        public function setContentOwner($contentid = NULL, $userid = null)
        {
            $this->_tableName = 'tbl_cms_content';
            $fields['created_by'] = $userid;

            $this->update('id', $contentid, $fields);
            $this->_tableName = 'tbl_cms_sections';	
        }

    /**
     * Method to SET the PERMISSIONS for a CONTENT USER 
     *
     * @access public
     * @param $contentid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setContentPermissionsUser($contentid = NULL, $userid, $read_access, $write_access)
        {	
            $this->_tableName = 'tbl_cms_content_user';
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;

            $sql = "SELECT id FROM tbl_cms_content_user 
                                            WHERE content_id = '$contentid'
                                            AND   user_id = '$userid'";

            //echo $sql;
            //exit;

            $data = $this->getArray($sql);

            if (count($data) > 0){		
                if (isset($data[0])) {
                    $id = $data[0]['id'];
                }
                $this->update('id', $id, $fields);
            } else {
                //content user mapping not found
            }
            $this->_tableName = 'tbl_cms_sections';
        }


    /**
     * Method to SET the PERMISSIONS for a CONTENT items GROUP
     *
     * @access public
     * @param $contentid, $userid, bool $read_access, bool $write_access
     * @return boolean
     */
        public function setContentPermissionsGroup($contentid = NULL, $groupid, $read_access, $write_access)
        {
            $this->_tableName = 'tbl_cms_content_group';
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;

            $sql = "SELECT id FROM tbl_cms_content_group
                                            WHERE content_id = '$contentid'
                                            AND   group_id = '$groupid'";

            $data = $this->getArray($sql);

            if (count($data) > 0){
                if (isset($data[0])) {
                    $id = $data[0]['id'];
                }
                $this->update('id', $id, $fields);
            } else {
                //content user mapping not found
            }
            $this->_tableName = 'tbl_cms_sections';
        }



    /**
     * Method to get a list of authorized USER and GROUPS for the specified sectionid
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */		

        public function getAuthorizedSectionMembers($sectionid = null){

            //Getting a list of Authorized User ID's for the current section
            $sql = "SELECT user_id as id FROM tbl_cms_section_user WHERE section_id = '$sectionid'";
            $userMemberIds = $this->getArray($sql);
            //Building a comma separated list of id's to be used in the IN sql statement
            $in_part = '';

            //var_dump($userMemberIds);
            $id_count =  count($userMemberIds);

            for ($i = 0; $i < $id_count; $i++){
                $id = $userMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            $in_part = substr($in_part, 0, strlen($in_part) - 1);
            //Getting the list of USERS if any
            if ($in_part != ''){
                $sql = "SELECT userId as id, username, firstname, surname FROM tbl_users WHERE userId IN ($in_part)";
                $userMembers = $this->getArray($sql);
            }

            //Getting a list of Authorized Goup ID's for the current section
            $sql = "SELECT group_id as id FROM tbl_cms_section_group WHERE section_id = '$sectionid'";
            $groupMemberIds = $this->getArray($sql);

            //Building a comma separated list of id's to be used in the IN sql statement
            $id_count = count($groupMemberIds);
            $in_part = '';
            for ($i = 0; $i < $id_count; $i++){
                $id = $groupMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            $in_part = substr($in_part, 0, strlen($in_part) - 1);
            //Getting the list of GROUPS if any
            if ($in_part != ''){

                $sql = "SELECT id, name as username, description FROM tbl_groupadmin_group WHERE id IN ($in_part)";
                $groupMembers = $this->getArray($sql);
            }

            if (is_array($userMembers) && is_array($groupMembers)) 
            {
                $members = array_merge($groupMembers, $userMembers);
                return $members;
            } 

            else if (is_array($userMembers)) 
            {
                return $userMembers;
            }

            else if (is_array($groupMembers)) 
            {
                return $groupMembers;
            }
            else {
                return array();
            }
        }


    /**
     * Method to get a list of authorized USER and GROUPS for the specified CONTENTID
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */		

        public function getAuthorizedContentMembers($contentid = null){

            //Getting a list of Authorized User ID's for the current content
            $sql = "SELECT user_id as id FROM tbl_cms_content_user WHERE content_id = '$contentid'";
            $userMemberIds = $this->getArray($sql);
            //Building a comma separated list of id's to be used in the IN sql statement
            $in_part = '';

            //var_dump($userMemberIds);
            $id_count =  count($userMemberIds);

            for ($i = 0; $i < $id_count; $i++){
                $id = $userMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            $in_part = substr($in_part, 0, strlen($in_part) - 1);
            //Getting the list of USERS if any
            if ($in_part != ''){
                $sql = "SELECT userId as id, username, firstname, surname FROM tbl_users WHERE userId IN ($in_part)";
                $userMembers = $this->getArray($sql);
            }

            //Getting a list of Authorized Goup ID's for the current content
            $sql = "SELECT group_id as id FROM tbl_cms_content_group WHERE content_id = '$contentid'";
            $groupMemberIds = $this->getArray($sql);

            //Building a comma separated list of id's to be used in the IN sql statement
            $id_count = count($groupMemberIds);
            $in_part = '';
            for ($i = 0; $i < $id_count; $i++){
                $id = $groupMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            $in_part = substr($in_part, 0, strlen($in_part) - 1);
            //Getting the list of GROUPS if any
            if ($in_part != ''){

                $sql = "SELECT id, name as username, description FROM tbl_groupadmin_group WHERE id IN ($in_part)";
                $groupMembers = $this->getArray($sql);
            }

            if (is_array($userMembers) && is_array($groupMembers)) 
            {
                $members = array_merge($groupMembers, $userMembers);
                return $members;
            } 

            else if (is_array($userMembers)) 
            {
                return $userMembers;
            }

            else if (is_array($groupMembers)) 
            {
                return $groupMembers;
            }
            else {
                return array();
            }
        }



    /**
     * Method to get a list of USERS ASSIGNED TO THE SECTION
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */		

        public function getAssignedSectionUsers($sectionid = null){

            $sql = "SELECT su.id, su.user_id, su.read_access, su.write_access,
                        u.username, u.firstname, u.surname 
                    FROM tbl_cms_section_user as su, tbl_users as u 
                    WHERE su.user_id = u.userid
                        AND su.section_id = '$sectionid'
                    ORDER BY u.firstname ASC";
            
            $userMembers = $this->getArray($sql);
            return $userMembers;
        }


    /**
     * Method to get a list of GROUPS ASSIGNED TO THE SECTION
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */

        public function getAssignedSectionGroups($sectionid = null){
            $sql = "SELECT sg.id, sg.group_id, sg.read_access, sg.write_access,
                        g.name, g.description 
                    FROM tbl_cms_section_group as sg, tbl_groupadmin_group as g
                    WHERE sg.group_id = g.id 
                        AND sg.section_id = '$sectionid'
                    ORDER BY g.name ASC";

            $groupMembers = $this->getArray($sql);
            return $groupMembers;
        }



    /**
     * Method to get a list of USERS ASSIGNED TO THE CONTENT
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */		

        public function getAssignedContentUsers($contentid = null){

            $sql = "SELECT su.id, su.user_id, su.read_access, su.write_access, u.username, u.firstname, u.surname 
                                            FROM tbl_cms_content_user as su, tbl_users as u 
                                            WHERE su.user_id = u.userid 
                                            AND su.content_id = '$contentid'
                                            ORDER BY u.firstname ASC";
            //echo $sql;
            //exit;
            $userMembers = $this->getArray($sql);
            return $userMembers;
        }


    /**
     * Method to get a list of GROUPS ASSIGNED TO THE CONTENT
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */

        public function getAssignedContentGroups($contentid = null){
            $sql = "SELECT sg.id, sg.group_id, sg.read_access, sg.write_access, g.name, g.description 
                                            FROM tbl_cms_content_group as sg, tbl_groupadmin_group as g
                                            WHERE sg.group_id = g.id 
                                            AND sg.content_id = '$contentid'
                                            ORDER BY g.name ASC";

            $groupMembers = $this->getArray($sql);
            return $groupMembers;
        }


    /**
     * Method to get a list of ALL Chisimba Users
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */

        public function getAllUsers(){

            $sql = "SELECT userId, username, firstname, surname
                                            FROM tbl_users";
            $users = $this->getArray($sql);
            return $users;
        }




    /**
     * Method to get a list of UNAUTHORIZED USER and GROUPS for the specified sectionid
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */		

        public function getUnAuthorizedSectionMembers($sectionid = null){

            //Getting the list of Authorized members so we can compare
            //Getting a list of Authorized User ID's for the current section
            $sql = "SELECT user_id as id FROM tbl_cms_section_user WHERE section_id = '$sectionid'";
            $userMemberIds = $this->getArray($sql);

            //Building a comma separated list of id's to be used in the IN sql statement
            $in_part = '';
            $id_count = count($userMemberIds);
            for ($i = 0; $i < $id_count; $i++){
                $id = $userMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            //Getting the list of USERS if any
            if ($in_part != ''){
                $in_part = substr($in_part, 0, strlen($in_part) - 1);

                $sql = "SELECT userId as id, username, firstname, surname FROM tbl_users WHERE userId NOT IN ($in_part) ORDER BY firstname, surname ASC ";
                $userMembers = $this->getArray($sql);
            } else {
                //No ID's to exclude so we query all users
                $sql = "SELECT userId as id, username, firstname, surname FROM tbl_users ORDER BY firstname, surname ASC";
                $userMembers = $this->getArray($sql);
            }

            //Getting a list of Authorized Goup ID's for the current section
            $sql = "SELECT group_id as id FROM tbl_cms_section_group WHERE section_id = '$sectionid'";
            $groupMemberIds = $this->getArray($sql);

            //Building a comma separated list of id's to be used in the IN sql statement
            $in_part = '';
            $id_count = count($groupMemberIds);
            for ($i = 0; $i < $id_count; $i++){
                $id = $groupMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            //Getting the list of GROUPS if any
            if ($in_part != ''){
                $in_part = substr($in_part, 0, strlen($in_part) - 1);

                $sql = "SELECT id, name as username, description FROM tbl_groupadmin_group WHERE id NOT IN ($in_part) ORDER BY username ASC";
                $groupMembers = $this->getArray($sql);
            } else {
                //No ID's to exclude so we query all groups
                $sql = "SELECT id, name as username, description FROM tbl_groupadmin_group ORDER BY username ASC";
                $groupMembers = $this->getArray($sql);
            }

            if (is_array($userMembers) && is_array($groupMembers)) 
            {
                $members = array_merge($groupMembers, $userMembers);

                return $members;
            } 

            else if (is_array($userMembers)) 
            {
                return $userMembers;
            }

            else if (is_array($groupMembers)) 
            {
                return $groupMembers;
            }
            else {
                return array();
            }

        }


    /**
     * Method to get a list of UNAUTHORIZED USER and GROUPS for the specified contentid
     *
     * @author Charl Mert
     * @access public
     * @return array An array of associative arrays of the members
     */		

        public function getUnAuthorizedContentMembers($contentid = null){

            //Getting the list of Authorized members so we can compare
            //Getting a list of Authorized User ID's for the current content
            $sql = "SELECT user_id as id FROM tbl_cms_content_user WHERE content_id = '$contentid'";
            $userMemberIds = $this->getArray($sql);

            //Building a comma separated list of id's to be used in the IN sql statement
            $in_part = '';
            $id_count = count($userMemberIds);
            for ($i = 0; $i < $id_count; $i++){
                $id = $userMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            //Getting the list of USERS if any
            if ($in_part != ''){
                $in_part = substr($in_part, 0, strlen($in_part) - 1);

                $sql = "SELECT userid as id, username, firstname, surname FROM tbl_users WHERE userId NOT IN ($in_part) ORDER BY firstname, surname ASC ";
                $userMembers = $this->getArray($sql);
            } else {
                //No ID's to exclude so we query all users
                $sql = "SELECT userid as id, username, firstname, surname FROM tbl_users ORDER BY firstname, surname ASC";
                $userMembers = $this->getArray($sql);
            }

            //Getting a list of Authorized Goup ID's for the current content
            $sql = "SELECT group_id as id FROM tbl_cms_content_group WHERE content_id = '$contentid'";
            $groupMemberIds = $this->getArray($sql);

            //Building a comma separated list of id's to be used in the IN sql statement
            $in_part = '';
            $id_count = count($groupMemberIds);
            for ($i = 0; $i < $id_count; $i++){
                $id = $groupMemberIds[$i];
                if ($id != ''){
                    $in_part .= '\''.$id['id'].'\',';
                }
            }

            //Getting the list of GROUPS if any
            if ($in_part != ''){
                $in_part = substr($in_part, 0, strlen($in_part) - 1);

                $sql = "SELECT id, name as username, description FROM tbl_groupadmin_group WHERE id NOT IN ($in_part) ORDER BY username ASC";
                $groupMembers = $this->getArray($sql);
            } else {
                //No ID's to exclude so we query all groups
                $sql = "SELECT id, name as username, description FROM tbl_groupadmin_group ORDER BY username ASC";
                $groupMembers = $this->getArray($sql);
            }

            if (is_array($userMembers) && is_array($groupMembers)) 
            {
                $members = array_merge($groupMembers, $userMembers);

                return $members;
            } 

            else if (is_array($userMembers)) 
            {
                return $userMembers;
            }

            else if (is_array($groupMembers)) 
            {
                return $groupMembers;
            }
            else {
                return array();
            }

        }


    /**
     * Method to get a field from an multi dimensional array.
     * This Method will aid in returning a list of Authorized Section User ID's 
     *
     * @access public     
     * @param  array       is  associated array
     * @param  string      the field to get
     * @return array|false the only the required field as an array, otherwise FALSE
     */
        public function getSectionUserMembersField( $rows, $field )
        {
            $rowFields = array();
            foreach( $rows as $row ) {
                // Multi-dimensional
                if( is_array( $row ) ) {
                    //test for user id based on the result of getUnAuthorizedSectionMembers($sectionid)
                    if (isset($row['surname'])){

                        $rowFields[] = $row[$field];
                    }

                } else {
                    return false;
                }
            }
            return $rowFields;
        }				



    /**
     * Method to get a field from an multi dimensional array.
     * This Method will aid in returning a list of Authorized Section GROUP ID's 
     *
     * @access public     
     * @param  array       is  associated array
     * @param  string      the field to get
     * @return array|false the only the required field as an array, otherwise FALSE
     * @author Charl Mert
     */
        public function getSectionGroupMembersField( $rows, $field )
        {
            $rowFields = array();
            foreach( $rows as $row ) {
                // Multi-dimensional
                if( is_array( $row ) ) {
                    //test for user id based on the result of getUnAuthorizedSectionMembers($sectionid)
                    if (!isset($row['surname'])){
                        $rowFields[] = $row[$field];
                    }

                } else {
                    return false;
                }
            }
            return $rowFields;
        }					


    /**
     * Method to add a USER to the Content Permission
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function addContentPermissionsUser($contentid=null, $userid = null, $read_access = true, $write_access = true, $do_update = false)
        {
            if ($contentid == ''){
                return false;
            }

            //Checking weather the contentid / groupid key exists
            //if exists return true
            $sql = "SELECT id from tbl_cms_content_user WHERE content_id = '$contentid' AND user_id = '$userid'";

            $res = $this->getArray($sql);
            if (count($res) > 0){
                //The content to group mapping already exists so it's as if it's been added
                if ($do_update) {
                    $this->setContentPermissionsUser($contentid, $userid, $read_access, $write_access);
                }

                return true;
            }

            //Adding a single user id for the given content

            //tbl_cms_content_user
            $this->_tableName = 'tbl_cms_content_user';
            $fields['content_id'] = $contentid;
            $fields['user_id'] = $userid;
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;
            $this->insert($fields);

            $insert_id = $this->getLastInsertId();

            $this->_tableName = 'tbl_cms_sections';	
            return $insert_id;			

        }


    /**
     * Method to DELETE a USER from the Content Permissions List
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteContentPermissionsUser($contentid=null, $userid = null)
        {
            if ($contentid == ''){
                return false;
            }

            //Getting the exact ID that satisfies key content_id AND user_id
            $qry = "SELECT id FROM tbl_cms_content_user
                                            WHERE  content_id = '$contentid'
                                            AND user_id = '$userid' ";
            $data = $this->getArray($qry);
            if (isset($data[0])) {
                $content_user_id = $data[0]['id'];
            }

            //Deleting a single user id for the given content
            //tbl_cms_content_user
            $this->_tableName = 'tbl_cms_content_user';
            $this->delete('id', $content_user_id);
            $insert_id = $this->getLastInsertId();			

            $this->_tableName = 'tbl_cms_sections';

            return $insert_id;			

        }


    /**
     * Method to add a GROUP to the Content Permission
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function addContentPermissionsGroup($contentid=null, $groupid = null, $read_access = true, $write_access = true, $do_update = false)
        {
            //echo "Adding Content Permissions For Group :$groupid | ContentID : $contentid | Read Access : $read_access | Write Access : $write_access <br/>";

            if ($contentid == ''){
                return false;
            }

            //Checking weather the contentid / groupid key exists
            //if exists update it
            $sql = "SELECT id from tbl_cms_content_group WHERE content_id = '$contentid' AND group_id = '$groupid'";
            $res = $this->getArray($sql);
            if (count($res) > 0){
                //The content to group mapping already exists so update it
                if ($do_update){
                    $this->setContentPermissionsGroup($contentid, $groupid, $read_access, $write_access);
                }

                return true;	
            }

            //Adding a single user id for the given content

            //tbl_cms_content_user
            $this->_tableName = 'tbl_cms_content_group';
            $fields['content_id'] = $contentid;
            $fields['group_id'] = $groupid;
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;
            $this->insert($fields);

            $insert_id = $this->getLastInsertId();

            $this->_tableName = 'tbl_cms_sections';
            return $insert_id;			

        }


    /**
     * Method to DELETE a GROUP from the Content Permissions List
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteContentPermissionsGroup($contentid=null, $groupid = null)
        {
            if ($contentid == ''){
                return false;
            }

            //Getting the exact ID that satisfies key content_id AND user_id
            $qry = "SELECT id FROM tbl_cms_content_group
                                            WHERE  content_id = '$contentid'
                                            AND group_id = '$groupid' ";

            $data = $this->getArray($qry);
            if (isset($data[0])) {
                $content_group_id = $data[0]['id'];
            }

            //Deleting a single user id for the given content
            //tbl_cms_content_user
            $this->_tableName = 'tbl_cms_content_group';
            $this->delete('id', $content_group_id);

            $this->_tableName = 'tbl_cms_sections';

            $insert_id = $this->getLastInsertId();

            return $insert_id;			

        }


    /**
     * Method to add a USER to the Section Permission
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function addSectionPermissionsUser($sectionid=null, $userid = null, $read_access = true, $write_access = true, $do_update = false)
        {
            if ($sectionid == ''){
                return false;
            }

            //Checking weather the sectionid / groupid key exists
            //if exists return true
            $sql = "SELECT id from tbl_cms_section_user WHERE section_id = '$sectionid' AND user_id = '$userid'";
            $res = $this->getArray($sql);
            if (count($res) > 0){
                //The section to group mapping already exists so it's as if it's been added
                if ($do_update){
                    $this->setPermissionsUser($sectionid, $userid, $read_access, $write_access);
                }	

                return true;
            }

            //Adding a single user id for the given section

            //tbl_cms_section_user
            $this->_tableName = 'tbl_cms_section_user';
            $fields['section_id'] = $sectionid;
            $fields['user_id'] = $userid;
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;
            $this->insert($fields);

            $insert_id = $this->getLastInsertId();
            $this->_tableName = 'tbl_cms_sections';

            return $insert_id;			

        }


    /**
     * Method to DELETE a USER from the Section Permissions List
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteSectionPermissionsUser($sectionid=null, $userid = null)
        {
            if ($sectionid == ''){
                return false;
            }

            //Getting the exact ID that satisfies key section_id AND user_id
            $qry = "SELECT id FROM tbl_cms_section_user
                                            WHERE  section_id = '$sectionid'
                                            AND user_id = '$userid' ";
            $data = $this->getArray($qry);
            if (isset($data[0])) {
                $section_user_id = $data[0]['id'];
            }

            //Deleting a single user id for the given section
            //tbl_cms_section_user
            $this->_tableName = 'tbl_cms_section_user';
            $this->delete('id', $section_user_id);
            $insert_id = $this->getLastInsertId();
            $this->_tableName = 'tbl_cms_sections';
            return $insert_id;			

        }


    /**
     * Method to add a GROUP to the Section Permission
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function addSectionPermissionsGroup($sectionid=null, $groupid = null, $read_access = true, $write_access = true, $do_update = false)
        {
            if ($sectionid == ''){
                return false;
            }

            //Checking weather the sectionid / groupid key exists
            //if exists update it
            $sql = "SELECT id from tbl_cms_section_group WHERE section_id = '$sectionid' AND group_id = '$groupid'";
            $res = $this->getArray($sql);

            if (count($res) > 0){
                //The section to group mapping already exists so updating here
                if ($do_update){
                    $this->setPermissionsGroup($sectionid, $groupid, $read_access, $write_access);
                }

                return true;	
            }

            //Adding a single user id for the given section

            //tbl_cms_section_user
            $this->_tableName = 'tbl_cms_section_group';
            $fields['section_id'] = $sectionid;
            $fields['group_id'] = $groupid;
            $fields['read_access'] = $read_access;
            $fields['write_access'] = $write_access;
            $this->insert($fields);

            $insert_id = $this->getLastInsertId();

            $this->_tableName = 'tbl_cms_sections';
            return $insert_id;			

        }


    /**
     * Method to DELETE a GROUP from the Section Permissions List
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteSectionPermissionsGroup($sectionid=null, $groupid = null)
        {
            if ($sectionid == ''){
                return false;
            }

            //Getting the exact ID that satisfies key section_id AND user_id
            $qry = "SELECT id FROM tbl_cms_section_group
                                            WHERE  section_id = '$sectionid'
                                            AND group_id = '$groupid' ";

            $data = $this->getArray($qry);
            if (isset($data[0])) {
                $section_group_id = $data[0]['id'];
            }

            //Deleting a single user id for the given section
            //tbl_cms_section_user
            $this->_tableName = 'tbl_cms_section_group';
            $this->delete('id', $section_group_id);
            $insert_id = $this->getLastInsertId();

            $this->_tableName = 'tbl_cms_sections';

            return $insert_id;			

        }





    /**
     * Method to DELETE ALL GROUPS from the Section Permissions List for the given section
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteAllSectionPermissionsUser($sectionid=null)
        {
            if ($sectionid == ''){
                return false;
            }

            //Deleting ALL Section Group Permissions for the given section
            $qry = "DELETE FROM tbl_cms_section_user
                                            WHERE  section_id = '$sectionid'";

            $data = $this->getArray($qry);

            return true;			
        }







    /**
     * Method to DELETE ALL GROUPS from the Section Permissions List for the given section
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteAllSectionPermissionsGroup($sectionid=null)
        {
            if ($sectionid == ''){
                return false;
            }

            //Deleting ALL Section Group Permissions for the given section
            $qry = "DELETE FROM tbl_cms_section_group
                                            WHERE  section_id = '$sectionid'";

            $data = $this->getArray($qry);

            return true;			

        }








    /**
     * Method to DELETE ALL GROUPS from the Section Permissions List for the given section
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteAllContentPermissionsUser($contentid=null)
        {
            if ($contentid == ''){
                //TODO: Log Error
                return false;
            }

            //Deleting ALL Section Group Permissions for the given section
            $qry = "DELETE FROM tbl_cms_content_user
                                            WHERE content_id = '$contentid'";

            $data = $this->getArray($qry);

            return true;			

        }






    /**
     * Method to DELETE ALL GROUPS from the Section Permissions List for the given section
     *
     * @access public
     * @return last insert id OR false if anything failed
     */
        public function deleteAllContentPermissionsGroup($contentid=null)
        {
            if ($contentid == ''){
                //TODO: Log Error
                return false;
            }

            //Deleting ALL Section Group Permissions for the given section
            $qry = "DELETE FROM tbl_cms_content_group
                                            WHERE content_id = '$contentid'";

            $data = $this->getArray($qry);

            return true;			

        }


        /**
         * Method to set permissions for new content item based on the parent security
         * i.e. Inheriting Permissions
         *
         * @access public
         * @return last insert id OR false if anything failed
         */
        public function inheritContentPermissions($contentid=null)
        {
            $_objContent =  $this->newObject('dbcontent', 'cmsadmin');

            if ($contentid == ''){
                //TODO: Log Error
                return false;
            }
            
            //
            //  Inheriting Group Permissions
            // 
            
            //Getting the content items parent
            $section = $_objContent->getParent($contentid);
            
            if (isset($section['id'])){
                $sectionid = $section['id'];
            } else {
                log_debug("insertPermissionsGroup : Couldn't get parent id for contentid '$contentid'");
            }
            
            $sectionid = $section['id'];
            
            //Getting the parent sections permission settings GROUP
            $qry = "SELECT group_id, read_access, write_access FROM tbl_cms_section_group
                    WHERE section_id = '$sectionid'";
                        
            $dataArr = $this->getArray($qry);
            foreach ($dataArr as $data){
                $this->addContentPermissionsGroup($contentid, $data['group_id'], $data['read_access'], $data['write_access']);
            }
            
            //
            //  Inheriting User Permissions
            // 
            
            //Getting the parent sections permission settings USER
            $qry = "SELECT user_id, read_access, write_access FROM tbl_cms_section_user
                    WHERE section_id = '$sectionid'";
            
            $dataArr = $this->getArray($qry);
            
            foreach ($dataArr as $data){
                $this->addContentPermissionsUser($contentid, $data['user_id'], $data['read_access'], $data['write_access']);
            }

            return true;			

        }

        /**
         * Method to set permissions for new sections based on the parent section's security
         * i.e. Inheriting Permissions
         *
         * @access public
         * @return last insert id OR false if anything failed
         */
        public function inheritSectionPermissions($sectionid=null)
        {
            $_objSection =  $this->newObject('dbsections', 'cmsadmin');

            if ($sectionid == ''){
                //TODO: Log Error
                return false;
            }
            
            //
            //  Inheriting Group Permissions
            // 
            
            //Getting the section items parent
            $parent = $_objSection->getParent($sectionid);
            
            //var_dump($section); exit;
            if (isset($parent['parentid'])){
                $parentid = $parent['parentid'];
            } else {
                log_debug("insertPermissionsGroup : Couldn't get parent id for sectionid '$sectionid'");
            }
            
            $parentid = $parent['parentid'];
            
            //Getting the parent sections permission settings GROUP
            $qry = "SELECT group_id, read_access, write_access FROM tbl_cms_section_group
                    WHERE section_id = '$parentid'";
            
            $dataArr = $this->getArray($qry);
            foreach ($dataArr as $data){
                $result = $this->addSectionPermissionsGroup($sectionid, $data['group_id'], $data['read_access'], $data['write_access']);
            }
            
            //
            //  Inheriting User Permissions
            // 
            
            //Getting the parent sections permission settings USER
            $qry = "SELECT user_id, read_access, write_access FROM tbl_cms_section_user
                    WHERE section_id = '$parentid'";
            
            $dataArr = $this->getArray($qry);
            
            foreach ($dataArr as $data){
                $this->addSectionPermissionsUser($sectionid, $data['user_id'], $data['read_access'], $data['write_access']);
            }
            
            return true;			

        }
        


    /**
     * Method to SET the "Public" vs "Logged in" access for SECTIONS
     *
     * @access public
     * @param $sectionid, $public_access
     * @return boolean
     */
        public function setSectionPermissionsPublicAccess($sectionid = NULL, $public_access)
        {
            $this->_tableName = 'tbl_cms_sections';
            $fields['public_access'] = $public_access;

            $sql = "SELECT id FROM tbl_cms_sections
                WHERE id = '$sectionid'";

            $data = $this->getArray($sql);

            if (count($data) > 0){		
                if (isset($data[0])) {
                    $id = $data[0]['id'];
                }
                $this->update('id', $id, $fields);
            } else {
                //no section by that id
            }
            $this->_tableName = 'tbl_cms_sections';

        }
        

    /**
     * Method to SET the "Public" vs "Logged in" access for CONTENT (includes frontpage content)
     *
     * @access public
     * @param $sectionid, $public_access
     * @return boolean
     */
        public function setContentPermissionsPublicAccess($contentid = NULL, $public_access)
        {
            $this->_tableName = 'tbl_cms_content';
            $id = $contentid;
            $fields['public_access'] = $public_access;

            $this->update('id', $id, $fields);
            //var_dump($id); var_dump($fields); exit;
            
            //Checking for frontpage item to set
            $this->_tableName = 'tbl_cms_content_frontpage';
            $sql = "SELECT content_id FROM tbl_cms_content_frontpage
                WHERE content_id = '$contentid'";

            $data = $this->getArray($sql);

            if (count($data) > 0){
                if (isset($data[0])) {
                    $id = $data[0]['content_id'];
                }
                $this->update('content_id', $id, $fields);
            } else {
                //no content item by that id
            }
            $this->_tableName = 'tbl_cms_content';

        }
    
    
     /**
     * Method to SET the PUBLIC_ACCESS PERMISSIONS recursively
     *
     * @access public
     * @param $sectionid, $public_access
     * @return boolean
     */
        public function setSectionPermissionsPublicAccessPropagate($sectionid = NULL, $public_access)
        {	
            //Get A List of all child sections
            $subSections = $this->getSubSectionsInSection($sectionid);
            foreach($subSections as $section){
                //Apply Permissions to child sections
                $subSecId = $section['id'];

				//Setting the public access here
				$this->setSectionPermissionsPublicAccess($subSecId, $public_access);

                //Get A List of all child contents
                $subContent = $this->getPagesInSection($subSecId);
                foreach($subContent as $content){
                    //Apply Permissions to child contents
                    $subContId = $content['id'];
                    $this->setContentPermissionsPublicAccess($subContId, $public_access);
                }

                if ($this->hasNodes($section['id'])){
                    //Recursion to set the children of the child in question
                    $this->setSectionPermissionsPublicAccessPropagate($subSecId, $public_access);
                }
            }
            //Get A List of all child contents for the main section
            $subContent = $this->getPagesInSection($sectionid);
            foreach($subContent as $content){
                //Apply Permissions to child contents
                $subContId = $content['id'];
                $this->setContentPermissionsPublicAccess($subContId, $public_access);
            }

            return true;
        }	

    
    /**
     * Method to checck if the SECTION is can be accessed by the PUBLIC
     * based ob weather or not they're logged in
     * 
     * @access public
     * @param sectionid
     * @return boolean
     */
        public function isSectionPublic($sectionid = NULL)
        {	
            $this->_objSection = & $this->newObject('dbsections', 'cmsadmin');

            //Checking the db flag for this section
            $sectionArr = $this->_objSection->getSection($sectionid);
        
            if ($sectionArr['public_access'] == 0){
                if ($this->_objUser->isLoggedIn()){
                    return true;
                } else {
                    return false;
                }
            } else {
                //Public is allowed to access section
                return true;
            }

        
        }
    
        
    /**
     * Method to checck if the CONTENT Item is can be accessed by the PUBLIC
     * based ob weather or not they're logged in
     * 
     * @access public
     * @param sectionid
     * @return boolean
     */
        public function isContentPublic($contentid = NULL)
        {	
            $this->_objContent = & $this->newObject('dbcontent', 'cmsadmin');
        
            //Checking the db flag for this section
            $contentArr = $this->_objContent->getContentPage($contentid);
            if ($contentArr['public_access'] == 0){
                if ($this->_objUser->isLoggedIn()){
                    return true;
                } else {
                    return false;
                }
            } else {
                //Public is allowed to access content
                return true;
            }
        
        }
    
        /**
         * Method to securely publish / unpublish sections
         *
         * @param string id The id if the section
         * @param string $task Publish or unpublish
         * @access public
         * @return boolean
         * @author Charl Mert <charl.mert@gmail.com>
         */
        public function setSectionPublicAccess($id, $task = 'public')
        {
            switch($task){
                case 'public':
                $fields['public_access'] = 1;
                $this->setSectionPermissionsPublicAccess($id, true);
                $this->setSectionPermissionsPublicAccessPropagate($id, true);

                break;
                case 'unpublic':
                $fields['public_access'] = 0;
                $this->setSectionPermissionsPublicAccess($id, false);
                $this->setSectionPermissionsPublicAccessPropagate($id, false);

                break;
            }

            return true;
        }
        
        /**
         * Method to securely publish-unlock / unpublish-lock content from
         * being viewed by the public who haven't logged in
		 *
         * @param string id The id if the content item
         * @param string $task Publish or unpublish
         * @access public
         * @return boolean
         * @author Charl Mert <charl.mert@gmail.com>
         */
        public function setContentPublicAccess($id, $task = 'public')
        {
            switch($task){
                case 'public':
                $fields['public_access'] = 1;
                $this->setContentPermissionsPublicAccess($id, true);

                break;
                case 'unpublic':
                $fields['public_access'] = 0;
                $this->setContentPermissionsPublicAccess($id, false);

                break;
            }

            return true;
        }
        
    }
?>
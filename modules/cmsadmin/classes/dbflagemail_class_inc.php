<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the flag email manager.
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert <charl.mert@gmail.com>
*/

    class dbflagemail extends dbTable
    {

        /**
        * The user object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

        /**
        * The language object
        *
        * @access private
        * @var object
        */
        protected $_objLanguage;

        /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                parent::init('tbl_cms_flag_email');
                $this->table = 'tbl_cms_flag_email';
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->_objSysConf = $this->getObject('dbsysconfig', 'sysconfig');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }



        /**
         * Method to add an email record
         * @param string $name The name of the person to alert
         * @param string $email The email address of the person to alert
         * @param string $userid The id of the registered user to alert
         * @param string $sectionId The id of the section to recieve alerts for 
         * @param string $contentId The id of the content item to recieve alerts for
         * @access public
         * @return newId on success and FALSE on faliure
         */
        public function addEmail($name, $email, $userId = '', $sectionId = '', $contentId = '')
        {
            $newArr = array(
                          'name' => $name,
                          'email' => $email ,
                          'user_id' => $userId,
                          'section_id' => $sectionId,
                          'content_id' => $contentId,
            );
            
            $newId = $this->insert($newArr);
            return $newId;
        }

        /**
         * Method to edit a email record
         * @param string $emailId The name of the person to alert
         * @param string $name The name of the person to alert
         * @param string $email The email address of the person to alert
         * @param string $userid The id of the registered user to alert
         * @param string $sectionId The id of the section to recieve alerts for
         * @param string $contentId The id of the content item to recieve alerts for
         * @access public
         * @return newId on success and FALSE on faliure
         */
        public function editEmail($emailId, $name, $email, $userId = '', $sectionId = '', $contentId = '')
        {
            $newArr = array(
                          'name' => $name,
                          'email' => $email ,
                          'user_id' => $userId,
                          'section_id' => $sectionId,
                          'content_id' => $contentId,
            );

            $newId = $this->update('id', $emailId, $newArr);
            return $newId;
        }

        /**
        * Method to delete a flag record
        *
        * @param string $id The id of the flag record
        * @return boolean
        * @access public
        */
        public function deleteEmail($id)
        {
            //Delete Flag
            $result = $this->delete('id', $id);
            
            return $result;
        }


       /**
        * Method to retrieve a flag record
        *
        * @param string $id 
        * @return boolean
        * @access public
        */
        public function getEmail($id)
        {
            $result = $this->getAll(" WHERE id = '{$id}'");

			if (empty($result[0])) {
				return FALSE;
			}
            
            return $result[0];
        }

	}

?>

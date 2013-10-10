<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the Faculties.
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert <charl.mert@gmail.com>
*/

    class dbfaculty extends dbTable
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
                parent::init('tbl_cms_flag');
                $this->table = 'tbl_cms_flag';
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->_objSysConf = $this->getObject('dbsysconfig', 'sysconfig');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }



        /**
         * Method to add a flag for the given content item
         * @param string $contentId The id of the content item to flag for.
         * @param string $optionId The id of the Flag chosen to flag against.
         * @access public
         * @return flagId on success and FALSE on faliure
         */
        public function addFlag($contentId, $optionId)
        {
            $creatorid = $this->_objUser->userId();

            if (isset($_SERVER['REMOTE_ADDR'])) {
                $ipAddr = $_SERVER['REMOTE_ADDR'];
            } else {
                $ipAddr = '';
            }

            $newArr = array(
                          'content_id' => $contentId ,
                          'option_id' => $optionId ,
                          'created' => $this->now(),
                          'created_by' => $creatorid,
                          'ip_addr' => $ipAddr
            );
            $newId = $this->insert($newArr);
			
            return $newId;
        }


        /**
        * Method to delete a flag record
        *
        * @param string $id The id of the flag record
        * @return boolean
        * @access public
        */
        public function deleteFlag($id)
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
        public function getFlag($id)
        {
            $result = $this->getAll(" WHERE id = '{$id}'");

			if (empty($result[0])) {
				return FALSE;
			}
            
            return $result[0];
        }

	}

?>

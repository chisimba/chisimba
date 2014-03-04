<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the phpunit module.
*
* @package phpunit
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert <charl.mert@gmail.com>
*/

    class dblog extends dbTable
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
                parent::init('tbl_phpunit_log');
                $this->table = 'tbl_cms_content';
                $this->_objSectionGroup = & $this->getObject('dbsectiongroup', 'cmsadmin');
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objSecurity = & $this->getObject('dbsecurity', 'cmsadmin');
                $this->_objFrontPage = & $this->newObject('dbcontentfrontpage', 'cmsadmin');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->_objBlocks = & $this->newObject('dbblocks', 'cmsadmin');
                $this->objConf = & $this->newObject('altconfig', 'config');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
         * Method to get the log entries
         *
         * @param string $filter The Filter
         * @return  array An array of associative arrays of all content pages in relationto filter specified
         * @access public
         */
        public function getLogs($where_clause = '')
        {
            return $this->getAll($where_clause);
        }

        /**
         * Method to delete a specific log entry
         *
         * @access public
         * @return bool
         */
        public function deleteLog($logId){
            //Removing the log
            $this->delete('id', $logId);
            
            return TRUE;
        }

        /**
         * Method to save a record to the database
         *
         * @access public
         * @return bool
         */
        public function addLog()
        {
            
            $matchUrl = $this->getParam('txtMatchUrl');
            $txtTargetUrl = $this->getParam('txtTargetUrl');
            $isDynamic = ($this->getParam('is_dynamic', '0') == 'on')? 1 : 0;
            $ordering = $this->getParam('ordering', '0');

            //The redirect processor allows relative mapping so fully qualified targets are mandatory
            
            //adding http:// for known [www.] targets
            if (preg_match('/^www\./', $txtTargetUrl)){
                $txtTargetUrl = 'http://'.$txtTargetUrl;
            }

            $newArr = array(
                'match_url' => $matchUrl,
                'target_url' => $txtTargetUrl,
                'is_dynamic' => $isDynamic,
                'ordering' => $ordering,
                'datestamp' => $this->now()
                         
            );

            if ($mapId == ''){
                //Add
                $mapId = $this->insert($newArr);
            } else {
                //Edit
                if ($this->update('id', $mapId, $newArr) == false){
                    log_debug('Short URL: Error Updating ID: ['.$mapId.']');
                }
            }
           
            return $mapId;
        }


    }

?>

<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the library search module.
*
* @package librarysearch
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert <charl.mert@gmail.com>
*/

    class dbsources extends dbTable
    {

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
                parent::init('tbl_librarysearch_sources');
                $this->table = 'tbl_librarysearch_sources';
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->objConf = & $this->newObject('altconfig', 'config');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
         * Method to retrieve the target datasource URI
         *
         * @param string $sourceId The id of the source entry to retrieve
         * @return array Returns the record or FALSE
         * @access public
         */
        public function getSource($sourceId)
        {
            $source = $this->getAll("WHERE id = '$sourceId'");
            $source = $source[0];
            if (empty($source)) {
                return FALSE;
            }
            return $source;
        }


        /**
         * Method to retrieve all the sources
         *
         * @return array Returns the records or FALSE
         * @access public
         */
        public function getSources()
        {
            $source = $this->getAll();
            if (empty($source)) {
                return FALSE;
            }
            return $source;
        }



    }

?>

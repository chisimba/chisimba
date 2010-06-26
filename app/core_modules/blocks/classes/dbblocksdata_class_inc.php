<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the cmsadmin module. Used to access data in the blocks table. 
*
* @package block
* @category chisimba
* @copyright AVOIR 
* @license GNU GPL
* @author Charl Mert
*/

class dbblocksdata extends dbTable
{

        /**
        * The language  object
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
                parent::init('tbl_module_blocks');
           } catch (Exception $e){
       		throw customException($e->getMessage());
        	exit();
     	   }
        }

        /************************ tbl_module_block methods *************************/

        /**
         * Method to return all entries in blocks table
         *
         * @return array $entries An array of all entries in the module_blocks table
         * @access public
         */
        public function getBlockEntries()
        {
            $sql = 'SELECT * FROM tbl_module_blocks';
            $entries = $this->getArray($sql);

            return $entries;
        }
        
        /**
         * Method to return an entries in blocks table
         *
         * @param string $blockId The id of the block
         * @return array $entry An associative array of the blocks details
         * @access public
         */
        public function getBlock($blockId)
        {
            $entry = $this->getArray('SELECT * FROM tbl_module_blocks WHERE id = \''.$blockId.'\'');
            $entry = $entry['0'];

            return $entry;
        }

        /**
         * Method to return an entries in blocks table
         *
         * @param string $blockName The name of the block
         * @return array $entry An associative array of the blocks details
         * @access public
         */
        public function getBlockByName($blockName)
        {
            $entry = $this->getArray('SELECT * FROM tbl_module_blocks WHERE blockname = \''.$blockName.'\'');
            
            if (count($entry) == 0) {
                return FALSE;
            } else {
                return $entry['0'];
            }
        }

        /**
        *
        * Get an array of block info where the blocks belong to a particular
        * module
        *
        * @param string $owningModule The module for which we are looking for blocks
        * @return string Associative array of the block data
        */
        public function getBlocksByModule($owningModule)
        {
            $ret = $this->getArray('SELECT * FROM tbl_module_blocks WHERE moduleid = \''.$owningModule.'\'');
            if (count($ret) == 0) {
                return FALSE;
            } else {
                return $ret;
            }
        }

        /**
        *
        * Get an array of block names and id only where the blocks belong to a
        * particular module
        *
        * @param string $owningModule The module for which we are looking for blocks
        * @return string Associative array of the block data
        */
        public function getBlocksNameByModule($owningModule)
        {
            $ret = $this->getArray('SELECT blockname,id FROM tbl_module_blocks WHERE moduleid = \''.$owningModule.'\'');
            if (count($ret) == 0) {
                return FALSE;
            } else {
                return $ret;
            }
        }
}
?>

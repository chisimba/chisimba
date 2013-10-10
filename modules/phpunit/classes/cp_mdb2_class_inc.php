<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * This object will handle the code pattern matching to extract the methods based
 * on the target classes MDB2 implementation.
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 */

    class cp_mdb2 extends object
    {
      /**
        * objMdb2
        * Contains the MDB2 analyzer that logically extracts Data Managment methods agaist the MDB2 implementation.
        * 
        * @var object
        */
        public $objMdb2;

      /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                $this->objConfig =$this->getObject('altconfig', 'config');
                $this->objLanguage =$this->getObject('language', 'language');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }


       /**
        * This method performs a code pattern match against the MDB2 Layer to
        * determine if the class qualifies as a database ADD method
        *
        * @param $methodSource The string contents of the method
        * @access public
        * @return boolean
        */
        public function isAddMethod($methodSource)
        {
            //Checking for insert usage
            $regEx = '/.*insert\(.*/isU';
            if (preg_match($regEx, $methodSource)) {
                return TRUE;
            }

            return FALSE;
        }


       /**
        * This method performs a code pattern match against the MDB2 Layer to
        * determine if the class qualifies as a database EDIT method
        *
        * @param $methodSource The string contents of the method
        * @access public
        * @return boolean
        */
        public function isEditMethod($methodSource)
        {
            //Checking for insert usage
            $regEx = '/.*update\(.*/isU';
            if (preg_match($regEx, $methodSource)) {
                return TRUE;
            }

            return FALSE;
        }


    }

?>

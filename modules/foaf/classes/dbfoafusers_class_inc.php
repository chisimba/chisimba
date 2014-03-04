<?php
/* -------------------- dbfoafusers class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Model class to get as much FOAF useable information from tbl_users
 *
 * @author Paul Scott
 * @access public
 * @package foaf
 * @category foaf
 */
class dbfoaf extends dbtable
{
    /**
     * The config Object
     *
     * @var object
     */
    public $objConfig;
    /**
     * User object
     *
     * @var object
     */
    private $objUser;
    /**
     * Language subsystem
     *
     * @var object
     */
    private $objLanguage;
    /**
     * Initialise the required objects
     *
     * @param void
     * @return void
     */
    public function init() 
    {
        try {
            //initialize the parent table
            parent::init('tbl_users');
            //get the config object
            $this->objConfig = &$this->getObject('altconfig', 'config');
            //get the user stuff
            $this->objUser = &$this->getObject('user', 'security');
            //load up the language stuff
            $this->objLanguage = &$this->getObject('language', 'language');
        }
        //catch any exceptions
        catch(customException $e) {
            //clean up memory
            customException::cleanUp();
        }
    }
    /**
     * Method to get a recordset from tbl_users for a particular userId
     *
     * @param integer $userId
     * @return array
     */
    public function getRecordSet($userId) 
    {
        $sql = "WHERE userid = '$userId'";
        return $this->getAll($sql);
    }
}
?>
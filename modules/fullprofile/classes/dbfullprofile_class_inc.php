<?php
/* -------------------- dbfullprofile class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to handle all db interaction of the fullprofile module
 *
 * @author Warren Windvogel <warren.windvogel@wits.ac.za>
 * @access public
 * @package fullprofile
 */

class dbfullprofile extends dbtable
{
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
            parent::init('tbl_fullprofile_info');
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
     * Method to change database tables
     *
     * @param string $table
     * @access private
     * @return void
     */
    private function _changeTable($table)
    {
        parent::init($table);
    }

    /**
     * Method to search for users in the users table
     *
     * @param string $searchTerm What to search for
     * @access public
     * @return array $result An array of all users matching the criteria
     */
    public function searchUser($searchTerm)
    {
        $this->_changeTable('tbl_users');

        $sql = "SELECT userid FROM tbl_users WHERE username LIKE '%".$searchTerm."%' OR firstname LIKE '%".$searchTerm."%' OR surname LIKE '%".$searchTerm."%'";
        $result = $this->getArray($sql);
        return $result;
    }

    /**
     * Method to return random foaf friends
     *
     * @param string $userid The id of the user
     * @param int $limit The max number of friends to return
     * @access public
     * @return array $friends An array of friends
     */
    public function getRandFriends($userid, $limit = 9)
    {
        $this->_changeTable('tbl_foaf_friends');

        $sql = "SELECT fuserid FROM tbl_foaf_friends WHERE userid = '".$userid."' ORDER BY RAND() LIMIT ".$limit;
        $result = $this->getArray($sql);
        return $result;

    }

}
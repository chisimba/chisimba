<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * An module to show data saved by activity logging class.
 *
 * @author Derek Keats
 * @copyright GPL
 * @package logger
 * @version 0.1
 */
class logshow extends dbTable
{
    /**
     * Property to hold the user object
     *
     * @var string $objUser The user object
     */
    public $objUser;
    /**
     * Constructor method
     */
    public function init() 
    {
        try {
            parent::init('tbl_logger');
            $this->objUser = &$this->getObject('user', 'security');
        }
        catch(customException $e) {
            customException::cleanUp();
            exit;
        }
    }
    /**
     *
     * Method to add the current event
     *
     * @param string $userId The userId of the user to look up, defaults
     * to the current user
     * @param string $order An ORDER BY SQL clause to generate
     * a particular order
     *
     */
    public function showForUser($userId = NULL, $order = Null) 
    {
        if ($userId == NULL) {
            $userId = $this->objUser->userId();
        }
        $where = "WHERE tbl_logger.eventParamName='pagelog' AND tbl_logger.userId='".$userId."'";
        if ($order !== NULL) {
            $where = $where." ".$order;
        }
        $sql = "SELECT tbl_logger.userId,
          tbl_users.firstname, tbl_users.surname,
          tbl_logger.module, tbl_logger.eventParamValue,
          tbl_logger.dateCreated, tbl_logger.context
          FROM tbl_logger LEFT JOIN tbl_users ON
          tbl_logger.userId=tbl_users.userId $where ";
        return $this->getArray($sql);
    }
    /**
     * Method to provide a simple list of modules logged
     *
     * @param string $userId The userId of the user to look up, defaults
     * to the current user
     *
     */
    public function showModulesLogged($userId = NULL) 
    {
        if ($userId == NULL) {
            $userId = $this->objUser->userId();
        }
        $sql = "SELECT DISTINCT tbl_logger.module AS module
          FROM tbl_logger";
        return $this->getArray($sql);
    } // function showModulesLogged
    
    /**
     * Method to show stats grouped by user
     *
     * @param string $userId The userId of the user to look up, defaults
     * to the current user
     *
     */
    public function showStatsByUser($userId = NULL) 
    {
        if ($userId == NULL) {
            $userId = $this->objUser->userId();
        }
        $where = "WHERE tbl_logger.userId='".$userId."' ";
        $sql = "SELECT tbl_logger.userId,
          tbl_users.firstname, tbl_users.surname,
          tbl_logger.module, COUNT(tbl_logger.id) AS Calls
          FROM tbl_logger LEFT JOIN tbl_users ON
          tbl_logger.userId=tbl_users.userId
          $where GROUP BY tbl_logger.module";
        $ar = $this->getArray($sql);
        return $ar;
    } // function showStatsByUser
    
    /**
     * Method to show stats grouped by module
     *
     */
    public function showStatsByModule($userId = NULL) 
    {
        $sql = "SELECT module, COUNT(id) AS Calls, COUNT(DISTINCT userId) AS Users FROM tbl_logger GROUP BY module";
        return $this->getArray($sql);
    } // function showStatsByModule
    public function showStatsByDate($timeframe = NULL) 
    {
        $where = " WHERE dateCreated >= '".$timeframe."' ";
        return $this->getAll($where);
    }
} //end of class

?>
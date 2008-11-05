<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * An module to show data saved by activity logging class.
 *
 * @author    Derek Keats
 * @copyright GPL
 * @package   logger
 * @version   0.1
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
            $this->objUser = $this->getObject('user', 'security');
        }catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    /**
     *
     * Method to add the current event
     *
     * @param string $userId The userId of the user to look up, defaults
     *                       to the current user
     * @param string $order  An ORDER BY SQL clause to generate
     *                       a particular order
     *                       
     */
    public function showForUser($userId = NULL, $order = Null)
    {
        if ($userId == NULL) {
            $userId = $this->objUser->userId();
        }
        $where = "WHERE tbl_logger.eventparamname='pagelog' AND tbl_logger.userid='$userId.'";
        if ($order != NULL) {
            $where = $where." ".$order;
        }
        $sql = "SELECT tbl_logger.userid,
          tbl_users.firstname, tbl_users.surname,
          tbl_logger.module, tbl_logger.eventparamvalue,
          tbl_logger.datecreated, tbl_logger.context
          FROM tbl_logger LEFT JOIN tbl_users ON
          tbl_logger.userid=tbl_users.userid $where ";
        return $this->getArray($sql);
    }
    /**
     * Method to provide a simple list of modules logged
     *
     * @param string $userId The userId of the user to look up, defaults
     *                       to the current user
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
     *                       to the current user
     *                       
     */
    public function showStatsByUser($userId = NULL)
    {
        if ($userId == NULL) {
            $userId = $this->objUser->userId();
        }
        $where = "WHERE (logger.userid = users.userid) = '$userId' ";
        $sql = "select users.firstname, users.surname, logger.userid, logger.eventparamvalue, logger.datecreated, logger.context, logger.module from tbl_logger as logger, tbl_users as users ".$where;
        $ar = $this->getArray($sql);
        return $ar;
    } // function showStatsByUser

    /**
     * Method to show stats grouped by module
     *
     */
    public function showStatsByModule($userId = NULL)
    {
        $sql = "SELECT module, COUNT(id) AS calls, COUNT(DISTINCT userid) AS users FROM tbl_logger GROUP BY module";
        return $this->getArray($sql);
    } // function showStatsByModule
    

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  string $timeframe Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
    public function showStatsByDate($timeframe = NULL)
    {
        $where = " WHERE datecreated >= '".$timeframe."' ";
        return $this->getAll($where);
    }
    /**
     * @param  string $userId userId
     * @param  string $contextCode Context Code
     * @param  string $module module name
     * @return string Return description (if any) ...
     * @access public
     */
    public function userLoggerDetails($userId=Null,$contextCode=Null, $module=Null)
    {
	if($userId==Null){
		$userId=$this->objUser->userId();
	}
	if($contextCode==Null){
	        $where = " WHERE userid = '".$userId."' and module = '".$module."'";
	        return $this->getAll($where);
	}elseif($module==Null){
	        $where = " WHERE userid = '".$userId."' and context = '".$contextCode."'";
	}else{
	        $where = " WHERE userid = '".$userId."' and context = '".$contextCode."' and module = '".$module."'";
	}
//        $sql = "SELECT * FROM tbl_logger".$where;
        return $this->getAll($where);
//        return $this->getArray($sql);
    }
} //end of class

?>

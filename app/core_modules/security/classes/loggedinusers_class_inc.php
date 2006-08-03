<?php
/**
* Logged in users table.
*/
class loggedInUsers extends dbTable
{
    var $objConfig;
    var $systemTimeOut;

    function init()
    {
		parent::init('tbl_loggedinusers');
        $this->objConfig=&$this->getObject('altconfig','config');
        $this->systemTimeOut=$this->objConfig->getsystemTimeout();
    }

    /**
	* Insert a record.
    * @param string $userId The userId of the user logging in
    */
    function insertLogin($userId) {
        // Delete old logins
        $sql="DELETE FROM tbl_loggedinusers 
			WHERE 
				(userid = '$userId') 
				AND ((".$this->now()."-whenlastactive)>'{this->systemTimeOut}')
		";
		$this->query($sql);
        // Update the tbl_loggedinusers table
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $sesssionId=session_id();
        $contextCode="lobby";
        $theme="default";
        $myDate=date('Y-m-d H:m:s');
        $isInvisible=FALSE;
        $sql="
			INSERT INTO tbl_loggedinusers (
				userid, 
				ipaddress, 
				sessionid,
				whenloggedin,
             	whenlastactive, 
				isinvisible, 
				coursecode, 
				themeused
			)
            VALUES (
				'$userId', 
				'$ipAddress', 
				'$sessionId', 
				".$this->now().", 
				".$this->now().", 
				'$isInvisible', 
				'$contextCode', 
				'$theme'
			)";
		$this->query($sql);
    }

   /**
   * Logout user from the site. The method deletes
   * the user from the database table tbl_loggedinusers, destroys
   * the session, and redirects the user to the index page,
   * index.php.
   * @param string $userId The userId of the logged in user
   */
   function doLogout($userId) {
        $sql="DELETE FROM tbl_loggedinusers 
		WHERE 
			userid='$userId' 
			AND sessionid ='".session_id()."'
		";
		$this->query($sql);
   }

    /**
    * Update the current user's active timestamp.
    */
    function doUpdateLogin($userId,$contextCode='lobby')
    {
       	$sql="UPDATE tbl_loggedinusers
        SET 
		  	whenlastactive = ".$this->now().", 
			coursecode='$contextCode'  
		WHERE
          	userid='$userId' 
			AND sessionid ='".session_id()."'
		";
		$this->query($sql);
   }

    /**
     * Return the time logged in.
     */
    function getMyTimeOn($userId)
    {
		$sql="SELECT (whenlastactive - whenloggedin)/100 AS activetime FROM tbl_loggedinusers 
		WHERE 
			userid='$userId' 
			AND sessionid='".session_id()."'
		";
		$results = $this->getArray($sql);
		if (!empty($results)) {
			$timeActive=intval($results[0]['activetime']);
		}
		else {
			$timeActive=0;
		}
       	return $timeActive;
   }

    /**
    * Count active users.
    */
    function getActiveUserCount()
    {
        $sql="SELECT COUNT(id) AS usercount FROM tbl_loggedinusers";
		$results = $this->getArray($sql);
		if (!empty($results)) {
			$activeUserCount=intval($results[0]['usercount']);
		}
		else {
			$activeUserCount=0;
		}
        return $activeUserCount;
    }

    /**
    * Return how long since the user was last active.
    * @param string $userId
    */
    function getInactiveTime($userId)
    {
        $sql="SELECT 
			((".$this->now()."-whenlastactive)/100) AS inactivetime 
		FROM 
			tbl_loggedinusers 
		WHERE 
			userid='$userId' 
			AND sessionid='".session_id()."'
		";
		$results = $this->getArray($sql);
		if (!empty($results)) {
			$inactiveTime=intval($results[0]['inactivetime']);
		}
		else {
			$inactiveTime=10+$this->systemTimeOut;
		}
        return $inactiveTime;
    }

    /**
    * Method to clear inactive users
    */
    function clearInactive()
    {
        $sql="DELETE FROM tbl_loggedinusers 
		WHERE 
			((".$this->now()."-WhenLastActive)/100) > {$this->systemTimeOut}
		";
		$this->query($sql);
    }

    /**
    * Method to check if a specified userId is online
    * @param string $userId
    * returns TRUE|FALSE
    */
    function isUserOnline($userId)
    {
        $sql="SELECT COUNT(userid) AS count FROM tbl_loggedinusers WHERE userid='$userid'";
  		$results = $this->getArray($sql);
		if (!empty($results)) {
			if ($results[0]['count']>0) {
			    return true;
			}
		}
		else {
			return false;
		}
    }
}
?>
<?php
/* -------------------- UserLoginHistory CLASS ----------------*/

/**
* Class to handle login history of the user
*/
class userLoginHistory extends dbTable {

    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_userloginhistory');
    }

    /**
    * Method to add a login history entry to the tbl_userloginhistory 
    * table
    * @param string $userId: The Unique userId of the user who is logging in.
    * @access public
    * @return 
    */
    public function addHistoryEntry($userId)
    {
        $this->insert(
			array(
				'userid' => $userId,
                'lastLoginDateTime' => date('Y-m-d H:m:s')
			)
		);
    }

    /**
    * Property returns the last login date for the user
    * denoted by $userId
    * @modified by Megan Watson 29-09-2006 Removed call to fetchRow()
    * @param string $userId: The Unique userId of the user being looked up
    */
    public function doGetLastLogin($userId)
    {
        $sql="SELECT 
			MAX(lastLoginDateTime) AS laston 
		FROM 
			tbl_userloginhistory 
		WHERE 
			userid='$userId'
		";
        $rs = $this->query($sql);
        //$line = $rs->fetchRow();
        return $rs[0]['laston'];
    }
    /**
    * Property returns the last login date for the user
    * denoted by $userId
    * @param string $time: The time of the user being logged in
    * @access public
    * @return array
    */
    public function getLastLogin($time)
    {
    	$sql="SELECT user.username,user.userId,
			MAX(last.lastLoginDateTime) AS laston 
		FROM 
			tbl_userloginhistory as last, tbl_users as user
		WHERE 
			 last.lastLoginDateTime <'$time' AND user.userId=last.userId Group by userId
		";
        $rs = $this->query($sql);
       
        return $rs;
    }
    /**
    * Property returns the last login date for the user
    * denoted by $userId
    * @param none
    * @access public
    * @return array
    */
    public function getnowLogin()
    {
    	$now = date('Y-m-d H:');
        $sql="SELECT users.username,users.userId,
			MAX(last.lastLoginDateTime) AS laston 
		FROM 
			tbl_userloginhistory as last, tbl_users as users
		WHERE 
			 last.lastLoginDateTime >'$now'  AND users.userId=last.userId Group by users.username,users.userId
		";
        $rs = $this->query($sql);
       
        return $rs;
    }
}

?>
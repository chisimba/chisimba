<?php
/* -------------------- UserLoginHistory CLASS ----------------*/

/**
* Class to handle data operations on the logged in users table
*/
class loggedInUsers extends object
{
    var $objConfig;
    var $systemTimeOut;

    function init()
    {
        $this->objConfig=&$this->getObject('altconfig','config');
        $this->systemTimeOut=$this->objConfig->getsystemTimeout();
    }

    /**
    * Method to update the session login table tbl_loggedinusers
    * with the current login information. Note that I have not made a
    * dbTable derrived class for this as we need to discuss how to handle
    * it in relation to mirroring. One option is to have another column
    * for the site and allow sites to see who is online at the other sites.
    * This would be similar to what the IRC networks do, so maybe we should
    * look at the algorithms used for IRC.
    *
    * @param string $userId: The userId of the user logging in
    */
    function insertLogin($userId) {
        $globalObjDb=&$this->objEngine->getDbObj();
        // Update the tbl_loggedinusers table
        $ip_address=$_SERVER['REMOTE_ADDR'];
        $sessid=session_id();
        $coursecode="lobby";
        $theme="default";
        $myDate=date('Y-m-d H:m:s');
        $isinvisible=FALSE;
        // ---- Drop old logins just in case any persist by accident
        $sql="DELETE FROM tbl_loggedinusers
        WHERE userId = '".$userId."' and ((NOW()-WhenLastActive)>'".$this->systemTimeOut."')";
        if ( MDB2::isError( $globalObjDb->query( $sql ) ) ) {        #trap errors
            die($this->reportError("error_db_cannotexecutequery", MDB2::errorMessage($globalObjDb).'<br>SQL: '.$sql));
        }
        $sql="INSERT INTO tbl_loggedinusers
             (userId, ipAddress, sessionId,whenLoggedIn,
             WhenLastActive, isInvisible, coursecode, themeUsed)
             VALUES ('".$userId."', '".$ip_address."', '".$sessid."', NOW(), NOW(), '"
             .$isinvisible."', '".$coursecode."', '".$theme."')";
        if ( MDB2::isError( $globalObjDb->query( $sql ) ) ) {        #trap errors
           die($this->reportError("error_db_cannotexecutequery", MDB2::errorMessage($globalObjDb).'<br>SQL: '.$sql));
        }
    }


   /**
   * Method to logout user from the site. The method deletes
   * the user from the database table tbl_loggedinusers, destroys
   * the session, and redirects the user to the index page,
   * index.php.
   * @param string $userId: the userId of the logged in user
   */
   function doLogout($userId) {
       $globalObjDb=&$this->objEngine->getDbObj();
       $sql="DELETE FROM tbl_loggedinusers WHERE userId='".$userId."' and sessionId ='".session_id()."'";
       if ( MDB2::isError( $globalObjDb->query( $sql ) ) ) {        #trap errors
           die($this->reportError("error_db_cannotexecutequery", MDB2::errorMessage($globalObjDb).'<br>SQL: '.$sql));
       }
   }

    /**
     * Method to update the curren't user's active timestamp in the
     * tbl_loggedinusers table
     */
    function doUpdateLogin($userId,$context='lobby')
    {
       $globalObjDb=&$this->objEngine->getDbObj();
       $sql="UPDATE tbl_loggedinusers
          SET WhenLastActive = NOW(), courseCode='".$context."'  WHERE
          userId='".$userId."'and sessionId ='".session_id()."'";
       if ( MDB2::isError( $globalObjDb->query( $sql ) ) ) {
           die($this->reportError("error_db_cannotexecutequery", MDB2::errorMessage($globalObjDb).'<br>SQL: '.$sql));
       }
   }

    /**
     * Method to return the time logged in for the active user
     */
    function getMyTimeOn($userId)
    {
       $globalObjDb=&$this->objEngine->getDbObj();
       $timeActive=0;
       $sql="SELECT (WhenLastActive - whenLoggedIn)/100 AS activeTime FROM
         tbl_loggedinusers WHERE userId='".$userId."' and sessionId='".session_id()."'";
       if (MDB2::isError($globalObjDb)) {                                 #connect error
           die ($this->reportError("error_db_cannotconnect", $globalObjDb->getMessage()));
       }
       if ( MDB2::isError( $rs = $globalObjDb->query( $sql ) ) ) {        #recordset error
           die($this->reportError("error_db_cannotexecutequery", MDB2::errorMessage($globalObjDb).'<br>SQL: '.$sql));
       } else if ($rs) {
           $line = $rs->fetchRow();
           $timeActive=intval($line['activeTime']);
       }
       return $timeActive;
   }

    /**
    * Method to count active users
    */
    function getActiveUserCount()
    {
        $globalObjDb=&$this->objEngine->getDbObj();           # make the connection string available
        //Connect to database using PEAR DB and return recordset
        $sql="SELECT COUNT(id) AS usercount FROM tbl_loggedinusers";
        if ( MDB2::isError( $rs=$globalObjDb->query( $sql ) ) ) {        #trap errors
            die(MDB2::errorMessage($rs).'<br>SQL: '.$sql);
        }
        $line = $rs->fetchRow();
        return $line[0];
    }

    /**
    * Method to return how long since the user was last active
    * @param string $userId
    */
    function getInactiveTime($userId)
    {
        $globalObjDb=&$this->objEngine->getDbObj();          # make the connection string available
        //Connect to database using PEAR DB and return recordset
        $sql="SELECT ((NOW()-WhenLastActive)/100) AS inactiveTime FROM tbl_loggedinusers WHERE userId='".$userId."' and sessionId='".session_id()."'";
        if ( MDB2::isError( $rs=$globalObjDb->query( $sql ) ) ) {        #trap errors
            die(MDB2::errorMessage($rs).'<br>SQL: '.$sql);
        }
        $line = $rs->fetchRow();
        if (isset($line['inactiveTime'])){
            return $line['inactiveTime'];
        } else {
            return (10+$this->systemTimeOut);
        }
    }

    /**
    * Method to clear inactive users
    */
    function clearInactive()
    {
        //Connect to database using PEAR DB and return recordset
        //THE SQL may not be right
        $globalObjDb=&$this->objEngine->getDbObj();
        $sql="DELETE FROM tbl_loggedinusers WHERE ((NOW()-WhenLastActive)/100) > ".$this->systemTimeOut;
        if ( MDB2::isError( $globalObjDb->query( $sql ) ) ) {        #recordset error
            die($this->reportError("error_db_cannotexecutequery", MDB2::errorMessage($globalObjDb).'<br>SQL: '.$sql, FALSE));
        }
    }

    function reportError($code, $extra)
    {
        die ($code . $extra);
    }


    /**
    * Method to check if a specified userId is online
    * @param string $userId
    * returns TRUE|FALSE
    */
    function isUserOnline($userId)
    {
        $globalObjDb=&$this->objEngine->getDbObj();
        $sql="SELECT COUNT(userId) as isOnLine from tbl_loggedinusers WHERE userId='$userId'";
        if ( MDB2::isError( $rs=$globalObjDb->query( $sql ) ) ) {        #recordset error
            die($this->reportError("error_db_cannotexecutequery", MDB2::errorMessage($globalObjDb).'<br>SQL: '.$sql, FALSE));
        }
        $line = $rs->fetchRow();
        if ($line['isOnLine']>0){
            return TRUE;
        } else {
            return FALSE;
        }
    }

}  #end of class
?>
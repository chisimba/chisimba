<?php
/* -------------------- stories class ----------------*/

/**
* Class for the stories table in the database
*/
class dbloginhistory extends dbTable
{

    var $objUser;
    var $objLanguage;

    /**
    * Constructor method to define the table
    */
    function init() {
        parent::init('tbl_userloginhistory');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
    * 
    * Get the login history count from the database
    * 
    */
    function getLoginHistory()
    {
        $order= $this->getParam('order', 'surname');
        $sql="SELECT count(tbl_userloginhistory.userid) 
          AS logins, max(lastlogindatetime) 
          AS lastOn, tbl_users.title, tbl_users.firstname,
          tbl_users.surname, tbl_users.country, tbl_users.sex,
          tbl_users.emailaddress, tbl_users.userid FROM  tbl_userloginhistory
          LEFT JOIN tbl_users  ON tbl_userloginhistory.userid = tbl_users.userid
          GROUP BY tbl_userloginhistory.userid
          ORDER BY " . $order;
        return $this->getArray($sql);
    }
    
    /**
    * 
    * Method to get the total number of logins 
    * on the system
    * 
    */
    function getTotalLogins()
    {
        $sql="SELECT COUNT(userid) AS totallogins 
          FROM tbl_userloginhistory";
        $ar = $this->getArray($sql);
        return $ar[0]['totallogins'];
    }

	function getfemales()
	{
	$sql="SELECT COUNT(DISTINCT(userid)) AS females 
	  FROM tbl_users WHERE sex='F' ";
	$aa = $this->getArray($sql);
	return $aa[0]['females'];
	}


	function getmales()
	{
	$sql = "SELECT COUNT(tbl_users.sex) As males,  COUNT(DISTINCT(tbl_users.userId)) As users, COUNT(DISTINCT(tbl_userloginhistory.userid))
	FROM tbl_users, tbl_userloginhistory
	WHERE tbl_userloginhistory.userid = tbl_users.userid AND tbl_users.sex = 'M'";
	$bb = $this->getArray($sql);
	return $bb[0]['users'];
	}


    /*

	function getmales()
	{
	$sql="SELECT COUNT(DISTINCT(userid)) AS males 
	  FROM tbl_users WHERE sex='M' ";
	$bb = $this->getArray($sql);
	return $bb[0]['males'];
	}

*/
    function getUniqueLogins()
    {
        $sql="SELECT COUNT(DISTINCT(userid)) 
          AS uniquelogins FROM tbl_userloginhistory";
        $ar = $this->getArray($sql);
        return $ar[0]['uniquelogins'];
    }
    
}  #end of class
?>

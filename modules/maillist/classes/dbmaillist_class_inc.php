<?php
/* ----------- data class extends dbTable for tbl_maillist------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_maillist
* This is the top level model for this module.
*
*/
class dbmaillist extends dbTable
{

    /**
    * Constructor method to define the table and initialize the needed objects
    * @param void
    * @return object resource ID
    */
    public function init()
    {
        parent::init('tbl_maillist_mail');
        $this->objUser =  $this->getObject("user", "security");
    }

   /**
    * Save method for editing a record in this table
    *@param string $mode: edit if coming from edit, add if coming from add
    */
    public function saveRecord($mode, $userId, $mess)
    {
        $id = $this->getParam('id', NULL);
        $body = $mess['body'];
        $subject = $mess['subject'];
        $filename = $mess['fileid'];
        $from = $mess['from'];

        // if edit use update
        if ($mode=="edit") {
            $this->changeTable('tbl_maillist_mail');
            $this->update("id", $id, array(
            'body' => $body,
            'subject' => $subject,
            'sender' => $from,
            'fileid' => $filename,
            'userId' => $userId));
            
            $this->changeTable('tbl_maillist_mailarchive');
            
            $this->insert(array(
            'body' => $body,
            'subject' => $subject,
            'sender' => $from,
            'fileid' => $filename,
            'userId' => $userId));

        }//if
        // if add use insert
        if ($mode=="add") {
            $this->changeTable('tbl_maillist_mail');
            
            $this->insert(array(
            'body' => $body,
            'subject' => $subject,
            'sender' => $from,
            'fileid' => $filename,
            'userId' => $userId));
            
            $this->changeTable('tbl_maillist_mailarchive');
            
            $this->insert(array(
            'body' => $body,
            'subject' => $subject,
            'sender' => $from,
            'fileid' => $filename,
            'userId' => $userId));

        }//if
    }//public function

    /**
     * Method to return the messages from the database
     * According to the number of messages returned from the maildrop.
     * @param int $messcount
     * @return array $messages
     */
    public function getRecords($messcount)
    {
    	$this->changeTable('tbl_maillist_mail');
        
    	$messages = $this->getAll(); //"ORDER BY 'id' DESC LIMIT $messcount");
    	return $messages;
    }

    /**
     * Method to clear the mail table to avoid id screwups
     * @param void
     * @return void
     */
    public function clearTable($table = 'tbl_maillist_mail')
    {
    	$this->changeTable($table);
    	
    	$filter = "TRUNCATE TABLE '$table'";
    	$this->_execute($filter);
    }
	 /**
     * Method to add the admin group user to the subscribers table on install
     * @param
     * @return
     */
	public function setDefaultList($insarray)
    {
    	$this->changeTable('tbl_maillist_subscribers');
    	$this->insert($insarray);
    	$this->changeTable('tbl_maillist_mail');

    }
   /**Method to insert the list details into the mail list table
   *@param
   *@return
   */
    public function insertListDetails($inslist)
    {
		$this->changeTable('tbl_mailinglist_lists');
		$this->insert($inslist);
	}

 
    /**
     *Method to change the working table
     * @param mixed $table - The name of the table
     * @return null
     * @access public
     */
    public function changeTable($table)
    {
        parent::init($table);
    }
    
   

    /**
     * Method to get the userID from tbl users according to the email addy
     * @param mixed $email
     * @return int $userid
     */
    public function getID($email)
    {
    	$this->changeTable('tbl_users');
    	$filter = "WHERE emailAddress='$email'";
    	$res = $this->getAll($filter);
    	return $res[0]['userId'];
    }

    /**
     * Method to check the email address of the sender against
     * the users table to check for validity.
     * If the address is valid, get the list name from the subs table
     * @param int $userid
     * @param mixed $email
     * @return $listname
     */
    public function getListName($userId, $email)
    {
    	//change to tbl_users
    	$this->changeTable('tbl_users');
    	//check that the email addy and the userid corroberate
    	$sql = "WHERE emailAddress = '$email' AND userId = '$userId'";
    	$ok = $this->getAll($sql);
    	if($ok)
    	{
    		//get the listname from the subs table that the email addy is subbed to
    		//change to the subs table
    		$this->changeTable('tbl_maillist_subscribers');
    		$state1 = "WHERE userId = '$userId'";
    		$res = $this->getAll($state1);
    		$list = $res[0]['list'];
    		
    		//great! Now lets get everyone's UserID that belongs to that list.
    		$getids = "WHERE list = '$list'";
    		$subs = $this->getAll($getids);

    		return array('subs' => $subs, 'list'=>$list);
    	}
    	else {
    		//email doesn't match up!!!!
    		return FALSE;
    	}

    }

    /**
     * method to retrieve email addys from tbl_users array
     * @param int $uid - the user id
     * @return mixed $emails
     */
    public function getMailAddys($uid)
    {
    	$this->changeTable('tbl_users');
    	$state2 = $this->getAll("WHERE userId = '$uid'");
    	return $state2[0];

    }
    
    public public function checkValidUser() 
    {
        $this->_changeTable('tbl_users');
        $val = $this->getAll();
        return $val;
    }

} #end of class
?>
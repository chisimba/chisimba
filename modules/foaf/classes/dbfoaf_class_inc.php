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



/**
*NOTE:
*In the removeXXXX methods the following lines are found
*$sql = "DELETE FROM tbl_foaf_xxxx WHERE xxxx IS NULL";
*       $this->getArray($sql);            
*This happens because there was a bug that added new rows in the table
*everytime I delete an entry.
*
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
    public function getRecordSet($userId, $table , $filter = null) 
    {
        $this->_changeTable($table);
        $sql = "WHERE userid = '$userId'";
    if($filter != null)
    {
      $sql.= $filter;
    }
        return $this->getAll($sql);
    }
    /**
     * Method to insert the users filled in details to tbl_foaf_myfoaf
     *
     * @param integer $userid
     * @param array $array
     * @return bool
     */
    public function insertMyDetails($userid, $array) 
    {
        $this->_changeTable('tbl_foaf_myfoaf');
        $checker = $this->getRecordSet($userid, 'tbl_foaf_myfoaf');
        if (empty($checker)) {
            return $this->insert($array);
        } else {
            return $this->update('id', $checker[0]['id'], $array, 'tbl_foaf_myfoaf');
        }
    }
    /**
     * Method to get all known users from tbl_users
     *
     * @param void
     * @return array
     */
    public function getAllUsers() 
    {
        $this->_changeTable('tbl_users');
        return $this->getAll();
    }
    /**
     * Method to get the friends of a partiucular userid
     *
     * @param void
     * @return array
     */
    public function getFriends($userid = NULL)
    {
        $this->_changeTable('tbl_foaf_friends');
        if(is_null($userid)){
            $userid = $this->objUser->userId();
        }
        $frie = $this->getAll("WHERE userid = '$userid'");
        foreach($frie as $friends) {
            //echo $friends['fuserid'];
            //lookup the userid and get a name for display
            $this->_changeTable('tbl_users');
            $ret = $this->getAll("WHERE userid = '{$friends['fuserid']}'");
            $fullname = $ret[0]['firstname']." ".$ret[0]['surname'];
            $pkid = $friends['id'];
            $fid = $friends['fuserid'];
            $det[] = array(
                'id' => $pkid,
                'name' => $fullname,
                'fuserid' => $fid
            );
        }
        if (isset($det)) {
            return $det;
        }
    }
    /**
     * Method to insert a new friend into the friends table according to a userid
     *
     * @param array $friend
     * @return bool
     */
    public function insertFriend($friend) 
    {
        $this->_changeTable('tbl_foaf_friends');
        return $this->insert($friend);
    }
    /**
     * Method to remove a friend associated with the current userid
     *
     * @param array $friend
     * @return bool
     */
    public function removeFriend($friend) 
    {
        $this->_changeTable('tbl_foaf_friends');
        //print_r($friend);
       $this->delete('id' , $friend);
       $sql = "DELETE FROM tbl_foaf_friends WHERE fuserid is NULL";
       $this->getArray($sql);            

    }

    /**
     * Method to check if a user is a friend
     *
     * @param string $userid The users id
     * @param string $fuserid The user id of the friend to check for
     * @access public
     * @return bool True if friend, else False
     */
    public function isFriend($userid, $fuserid)
    {
        $this->_changeTable('tbl_foaf_friends');
        $sql = "SELECT COUNT(id) FROM tbl_foaf_friends WHERE userid = '".$userid."' AND fuserid = '".$fuserid."'";
        $result = $this->getArray($sql);

        if($result[0]['count(id)'] > 0){
            return TRUE;
        } else {
            return FALSE;
        }

    }

    /**
     * Method to insert an organization according to the current userid
     *
     * @param string $name
     * @param string $url
     * @return bool
     */
    public function insertOrg($name, $url) 
    {
        $this->_changeTable('tbl_foaf_organization');
        $uid = $this->objUser->userId();
        $ins = array(
            'userid' => $uid,
            'homepage' => $url,
            'name' => $name
        );
        return $this->insert($ins);
    }
    /**
     * Method to remove an associated organization from a userid
     * This is step one of two to get the org details
     *
     * @param void
     * @return array
     */
    public function remOrg() 
    {
        $userid = $this->objUser->userId();
        $this->_changeTable('tbl_foaf_organization');
        $ret = $this->getAll("WHERE userid = '$userid'");
        //print_r($ret);
        if(empty($ret))
        {
            return NULL;
        }
        foreach($ret as $returns) {
            $retarr[] = array(
                'id' => $returns['id'],
                'name' => $returns['name']
            );
        }
        return $retarr;
    }
    /**
     * Part two of org removal
     *
     * @param string $orgid
     * @return bool
     */
    public function removeOrg($orgid) 
    {
        $this->_changeTable('tbl_foaf_organization');
     $sql = "DELETE FROM tbl_foaf_organization WHERE homepage IS NULL";
        $this->getArray($sql);            
        return $this->delete('id', $orgid);
        
    }
  





  /**
     * Method to get the funders associated to a user
     * 
     *
     * @param string userId => the userId for the funders
     * @return array => the funders related to this userId
     */

     public function getFunders()
     {
          $this->_changeTable('tbl_foaf_fundedby');
             $sql = "WHERE userid='".$this->objUser->userId()."'";                                    
     
          return $this->getAll($sql);
    }
       
    
    /**
     * Method to insert a funder
     * 
     *
     * @param string userId => the userId for the funders
     * @return array => the funders related to this userId
     */


    

    public function insertFunder($url) 
      {
        $this->_changeTable('tbl_foaf_fundedby');

        $values = array(
            'userid' => $this->objUser->userId(),
            'funderurl' => $url        
        );
        return $this->insert($values);
      }







    /**
       *Method for removing funders
       *@param string funderId => id of the funder to be removed
    **/
                

      public function removeFunder($funderId) 
    {
        $this->_changeTable('tbl_foaf_fundedby');
        $this->delete('id', $funderId);
    $sql = "DELETE FROM tbl_foaf_fundedby WHERE funderurl is NULL";
        $this->getArray($sql);            
    }



//interests

    /**
     * Method to get the interests associated to a user
     * 
     *
     * @param string userId => the userId for the funders
     * @return array => the interests related to this userId
     */

     public function getInterests($userId = NULL)
     {
         if(is_null($userId)){
             $userId =  $this->objUser->userId();
         }
         $this->_changeTable('tbl_foaf_interests');
         $sql = "WHERE userid='".$userId."' ORDER BY interesturl";
         return $this->getAll($sql);
    }
       
    
    /**
     * Method to insert a interest
     * 
     *
     * @param string userId => the userId for the funders
     * @return array => the interests related to this userId
     */

    
    

    public function insertInterest($url) 
      {
        $this->_changeTable('tbl_foaf_interests');

        $values = array(
            'userid' => $this->objUser->userId(),
            'interesturl' => $url        
        );
        return $this->insert($values);
      }





    /**
       *Method for removing interests
       *@param string interestId => id of the funder to be removed
    **/
                

      public function removeInterest($interestId) 
    {
         $this->_changeTable('tbl_foaf_interests');
         $this->delete('id', $interestId);
      $sql = "DELETE FROM tbl_foaf_interests WHERE interesturl is NULL";
         $this->getArray($sql);            
     
    }


//depictions


/**
     * Method to get the depictions associated to a user
     * 
     *
     * @param string userId => the userId for the funders
     * @return array => the depictions related to this userId
     */

     public function getDepictions($userId = NULL)
     {
         if(is_null($userId)){
             $userId =  $this->objUser->userId();
         }
        $this->_changeTable('tbl_foaf_depiction');
       $sql = "WHERE userid='".$userId."'";                                    
       return $this->getAll($sql);
    }


/**
     * Method to insert a depiction
     * 
     *
     * @param string userId => the userId of the user
     * @return array => the depictions urls related to this userId
     */

    
    

    public function insertDepiction($url) 
      {
        $this->_changeTable('tbl_foaf_depiction');

        $values = array(
            'userid' => $this->objUser->userId(),
            'depictionurl' => $url        
        );
        return $this->insert($values);
      }







    /**
       *Method for removing depiction
       *@param string depictionId => id of the depiction to be removed
    **/
                

      public function removeDepiction($depictionId) 
    {
         $this->_changeTable('tbl_foaf_depiction');
         $this->delete('id', $depictionId);
      $sql = "DELETE FROM tbl_foaf_depiction WHERE depictionurl is NULL";
         $this->getArray($sql);            

    }



//pages


/**
     * Method to get the pages associated to a user
     * 
     *
     * 
     * @return array => the pages related to this userId
     */

     public function getPgs($userId = NULL)
     {
         if(is_null($userId)){
             $userId =  $this->objUser->userId();
         }
         $this->_changeTable('tbl_foaf_pages');
       $sql = "WHERE userid='".$userId."'"." ORDER BY title ";                                    
       return $this->getAll($sql);
    }


/**
     * Method to insert a page
     * 
     *
     * @param string userId => the userId of the user
     * @return array => the page uri related to this userId
     */

    
    

    public function insertPage($document_uri, $title, $description) 
      {
        $this->_changeTable('tbl_foaf_pages');

        $values = array(
            'userid' => $this->objUser->userId(),
            'page' => $document_uri,
             'title' => $title,
        'description' => $description
        );
        return $this->insert($values);
      }







    /**
       *Method for removing pages
       *@param string pageId => id of the page to be removed
    **/
                

      public function removePage($pageId) 
    {
         $this->_changeTable('tbl_foaf_pages');
         $this->delete('id', $pageId);
      $sql = "DELETE FROM tbl_foaf_pages WHERE page IS NULL";
         $this->getArray($sql);            

    }

//Accounts


/**
     * Method to get the accounts 
     * 
     *
     * @param void
     * @return array => the types of accounts in the database
     */

     public function getAccountTypes()
     {
        $this->_changeTable('tbl_foaf_accounts');
    $accountTypes = $this->getAll();
    $sql = "ORDER BY type ";
    //There must be the very basic FOAF account type in the database
    if(!isset($accountTypes) || empty($accountTypes))
    {

       $this->insertAccountType("onlineAccount");            
       $this->insertAccountType("onlineChatAccount", "http://xmlns.com/foaf/0.1/OnlineChatAccount");
       $this->insertAccountType("onlineEcommerceAccount", "http://xmlns.com/foaf/0.1/OnlineEcommerceAccount");
           $this->insertAccountType("onlineGamingAccount", "http://xmlns.com/foaf/0.1/OnlineGamingAccount");

    }    


                                      
       return $this->getAll($sql);
    }


/**
     * Method to insert an account
     * 
     *@param string $type=> The type of account e.g (onlineGamingAccount,onlineChatAccount ...)
     * @param string $url=> The url that provides the type of account specification
     * @return array 
     */

    
    

    public function insertAccountType($type) 
      {
        $this->_changeTable('tbl_foaf_accounts');

        $values = array(
            'type' => $type
        );
        return $this->insert($values);
      }







    /**
       *Method for removing accounts
       *@param string accountId => id of the account to be removed
    **/
                

      public function removeAccountType($accountId) 
    {
         $this->_changeTable('tbl_foaf_accounts');
         $this->delete('id', $accountId);
         $sql = "DELETE FROM tbl_foaf_accounts WHERE type IS NULL";
         $this->getArray($sql);            

    }


//user accounts


/**
     * Method to get user accounts 
     * 
     *
     * @param void
     * @return array => the accounts in the database
     */

     public function getAccounts($userId = NULL)
     {
         if(is_null($userId)){
             $userId =  $this->objUser->userId();
         }
        $this->_changeTable('tbl_foaf_useraccounts');
        $sql = "WHERE userid='".$userId."'"." ORDER BY accountname ";                                    
       return $this->getAll($sql);
    }


/**
     * Method to insert an account
     * 
     *@param string $accountName=> The user name provided for the account
     * @param string $accountServiceHomepage=>Indicates a homepage of the service provide for this online account. 
     *@param string $type=> The type of account e.g (onlineGamingAccount,onlineChatAccount ...)
     * @return array 
     */

    
    

    public function insertAccount($accountName, $accountServiceHomepage , $accountType , $url = null) 
        {
        $this->_changeTable('tbl_foaf_useraccounts');

        $values = array(
        'userid'=>$this->objUser->userId(),
        'accountName'=>$accountName,
        'accountServiceHomepage'=>$accountServiceHomepage,        
            'type' => $accountType,
        'url'=>$url    
        );
        return $this->insert($values);
       }







    /**
       *Method for removing accounts
       *@param string accountId => id of the account to be removed
    **/
                

      public function removeAccount($accountId) 
    {
         $this->_changeTable('tbl_foaf_useraccounts');
         $this->delete('id', $accountId);
         $sql = "DELETE FROM tbl_foaf_accounts WHERE type IS NULL";
         $this->getArray($sql);            

    }







//links


/**
     * Method to get all the FOAF related links
     * 
     *
     * @param void
     * @return array => the FOAF links in the database
     */

     public function getLinks()
     {
        $this->_changeTable('tbl_foaf_links');
        $sql = " ORDER BY title ";                                    
     
    return $this->getAll($sql);
    }


/**
     * Method to insert a link
     * 
     *@param string $title=> A descriptive and easy to remember (recommended) name for the link
     * @param string $url=>Indicates the link URL
     *@param string $description=> A sort descripton about the link 
     * @return array => The FOAF links in the database
     */

    
    

    public function insertLink($title, $url , $description = NULL) 
        {
        $this->_changeTable('tbl_foaf_links');

        $values = array(
        'title'=>$title,
        'url'=>$url,
        'description'=>$description,        
        );
        return $this->insert($values);
       }







    /**
       *Method for removing links
       *@param string linkId => id of the link to be removed
    **/
                

      public function removeLink($linkId) 
    {
         $this->_changeTable('tbl_foaf_links');
         $this->delete('id', $linkId);
         $sql = "DELETE FROM tbl_foaf_links WHERE url IS NULL";
         $this->getArray($sql);            

    }


    /**
       *Method for tracking repeated entries
       *@param string $field => the field to check for duplication
       *@param string $value => the value to be looked for
       *@param string $table => the table for looking for repeated values
       *@return  TRUE|FALSE => TRUE if it finds a repeated entry otherwise FALSE
    **/
                

      public function isRepeated($field, $value , $table) 
        {
       
           parent::init($table);
       $sql = "WHERE userid='".$this->objUser->userId()."'"." AND ".$field."='".$value."'";

       $results = $this->getAll($sql);
       
       if(!isset($results) || empty($results))
       {
          return false;
       } else {
         return true;
     }
                
     }   

    





  /**
     * Method to change database tables
     *
     * @param string $table
     * @return void
     */
    private function _changeTable($table) 
    {
        parent::init($table);
    }
}
?>

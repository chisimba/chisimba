<?
/**
* This class is used by the useradmin module
* @author James Scoble
*/
class useradmin extends dbtable
{

    var $objUser;
    var $objLanguage;
    var $objConfig;

    function init()
    {
	    parent::init('tbl_users');
        $this->objConfig=&$this->getObject('altconfig','config');
        $this->objUser=&$this->getObject('user','security');
        $this->objLanguage=&$this->getObject('language','language');
    }

    /**
    * method to add a new user
    * @param array $info data for new user
    * @returns string $id the PKID of the new login
    */
	/*
    function addUser($info)
    {
        $sdata['userId']=$info['userId'];
        $sdata['username']=$info['username'];
        $sdata['title']=$info['title'];
        $sdata['firstname']=$info['firstName'];
        $sdata['surname']=$info['surname'];
        $sdata['pass']=sha1($info['password']);
        $sdata['CreationDate']=date("Y-m-d");
        if (isset($info['howCreated'])){
            $sdata['howCreated']=$info['howCreated'];
        }
        $sdata['emailAddress']=$info['emailAddress'];
        $sdata['sex']=$info['sex'];
        $sdata['country']=$info['country'];
        $id=$this->insert($sdata);
        return $id;
    }
	*/

    /**
    * Method to create a user account from getParam()
    * @param string $userId
    * @returns string $id
    */
	/*
    function createUserAccount($userId,$howcreated='selfregister')
    {
        $password=$this->getParam('password');
        if ($password==''){
            $objPassword=&$this->getObject('passwords','useradmin');
            $password=$objPassword->createPassword();
        }
        $cryptpassword=sha1($password);
        $cdate=date("Y-m-d");
        $newdata=array(
            'userId'=>$userId,
            'username'=>$this->getParam('username'),
            'title'=>$this->getParam('title'),
            'firstname'=>$this->getParam('firstname'),
            'surname'=>$this->getParam('surname'),
            'pass'=>$cryptpassword,
            'CreationDate'=>$cdate,
            'howCreated'=>$howcreated,
            'emailAddress'=>$this->getParam('email'),
            'sex'=>$this->getParam('gender'),
            'country'=>$this->getParam('country')
            );
        $id=$this->insert($newdata);
        $this->emailPassword($newdata['userId'],$newdata['username'],$newdata['firstname'],$newdata['surname'],$newdata['emailAddress'], $password);
        return $id;
    }
	*/

    /**
    * Method to edit a user account using info from getParam()
    * Does not change the password or userId
    * @param string $userId
    * @returns string $id
    */
	/*
    function editUserAccount($userId)
    {
        $newdata=array(
            'username'=>$this->getParam('username'),
            'title'=>$this->getParam('title'),
            'firstname'=>$this->getParam('firstname'),
            'surname'=>$this->getParam('surname'),
            'emailAddress'=>$this->getParam('email'),
            'sex'=>$this->getParam('gender'),
            'country'=>$this->getParam('country')
            );
        // remove blank fields
        foreach ($newdata as $key=>$value)
        {
            if ($value==''){
                unset($newdata[$key]);
            }
        }
        $id=$this->update('userId',$userId,$newdata);
        return $id;
    }
	*/

    /**
    * method to lookup list of users for admin functions
    * @author James Scoble
    *
    * @param string $how - the method of searching used - username, surname or email
    * @param string $match - the pattern to match for
    * returns array $r1
    */
 	function getUsers($how,$match,$exact=FALSE)
 	{
 		$sql="SELECT * FROM tbl_users";
 		if (
			($how=='username')
			||($how=='surname')
			||($how=='emailaddress')
			||($how=='userid')
			||($how=='creationdate')
			||($how=='logins')
			||($how=='isActive')
		)
		{
			if ($match=='listall') {
            }
			else {
				if ($exact===TRUE){
				    $sql.=" WHERE $how = '$match'";
				/*
				} else if ($exact=='greater'){
				    $sql.=" WHERE $how > '$match'";
				} else if ($exact=='less'){
				    $sql.=" WHERE $how < '$match'";
				*/
				} else {
				    $sql.=" WHERE $how LIKE '$match%'";
				}
			}
			$sql .= " ORDER BY $how";
		}
        if ($how=='notused'){
            $sixMonthsAgo=date('Y-m-d',time()-15552000);
            $sql.=" WHERE logins='0' AND creationdate<'$sixMonthsAgo' ORDER BY creationdate";
        }
		$results=$this->getArray($sql);
		$count = count($results);
		for ($i=0;$i<$count;$i++) {
			 $keys = array_keys($results[$i]);
			 foreach ($keys as $key) {
				$results[$i][$key] = stripslashes($results[$i][$key]);
			 }
		}		
		return $results;
	}

    /**
    * This is a method to delete a group of users at once
    * using an array of userId's
    * It will not delete Site-Admin user accounts
    * @author James Scoble
    * @param array $userArray
    */
    function batchDelete($users)
    {
        foreach ($users as $user)
        {
            $isAdmin=$this->objUser->lookupAdmin($user);
            if (!$isAdmin){
                $this->delete('userid',$user);
            }
        }
    }

    /**
    * Check if userid exists.
    * @param string $userId
    */
    function checkUserIdAvailable($userId)
    {
        $sql="SELECT COUNT(*) AS count FROM tbl_users WHERE userId='$userId'";
        $count=$this->getArray($sql);
        if ($count[0]['count']>0) { 
			return "userid_taken";
		}
		else {
	        return true;
		}
    }

    /**
    * Check if username exists.
    * @param string $username
    */
    function checkUsernameAvailable($username)
    {
        $sql="SELECT COUNT(*) AS count FROM tbl_users WHERE username='$username'";
        $count=$this->getArray($sql);
        if ($count[0]['count']>0) { 
			return "username_taken";
		}
		else {
	        return true;
		}
    }

    /**
    * This is a method to change SQL password for specified userId
    * @author James Scoble
    * @param string $userId
    * @param string $oldpassword
    * @param string $newpassword
    * @returns TRUE|FALSE
    *
    * This function checks the supplied password against the one in the database.
    * Only if it matches does it change to the new one.
    */
    function changePassword($userId,$oldpassword,$newpassword)
    {
        $data=$this->getUsers('userid',$userId,TRUE);
		//print_r($data);
        if (strtolower($data[0]['pass'])==strtolower(sha1($oldpassword)))
        {
            // here we proceed to actually do the change
            $cryptpassword=sha1($newpassword);
            //$sql="update tbl_users set password='".$cryptpassword."' where userId='".$userId."'";
            $this->update('userid',$userId,array('pass'=>$cryptpassword));
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
    * This is a method to change a user's password to a random setting
    * and email the result. It checks to see if the username and email
    * address match before making any changes.
    * @param string $username
    * @param string $email
    * @returns string $status messagecode
    */
	/*
    function resetPassword($username,$email)
    {
        $username=trim($username);
        $email=trim($email);
        $sql="select userId, username, firstname, surname, pass from tbl_users where username='$username' and emailAddress='$email'";
        $result=$this->getArray($sql);
        if (isset($result[0])){
            // Get the user's info
            $userId=$result[0]['userId'];
            $password=$result[0]['pass'];
            $firstname=$result[0]['firstname'];
            $surname=$result[0]['surname'];
            if ($password!=(sha1('--LDAP--'))){
                $objPassword=&$this->getObject('passwords','useradmin');
                $newpassword=$objPassword->createPassword();
                $cryptpassword=sha1($newpassword);
                $this->update('userId',$userId,array('pass'=>$cryptpassword));
                $this->emailPassword($userId,$username,$firstname,$surname,$email,$newpassword);
                return "mod_useradmin_passwordreset";
            } else {
                // LDAP USER
                return "mod_useradmin_ldapnochange";
            }
        } else {
            // error that no such username/email exists
            return "mod_useradmin_nomatch";
        }
        return TRUE;
    }
	*/

    /**
    * Method to compose and send email for resetting of password
    * @param string $firstname - data to send
    * @param string $surname - data to send
    * @param string $userId - data to send
    * @param string $username - data to send
    * @param string $title - data to send
    * @param string $email - data to send
    * @param string $password - data to send
    */
	/*
    function emailPassword($userId,$username,$firstname,$surname,$email,$password)
    {
        $info=$this->siteURL();
        $emailtext=str_replace('SURNAME',$surname,str_replace('FIRSTNAME',$firstname,$this->objLanguage->languageText('mod_useradmin_greet1')))."\n"
        .$this->objLanguage->languageText('mod_useradmin_greet4')."\n"
        .$this->objLanguage->languageText('word_userid').": $userId\n"
        .$this->objLanguage->languageText('phrase_firstname').": $firstname\n"
        .$this->objLanguage->languageText('word_surname').": $surname\n"
        .$this->objLanguage->languageText('word_username').": $username\n"
        .$this->objLanguage->languageText('word_password').": $password\n"
        .$this->objLanguage->languageText('phrase_emailaddress').": $email\n"
        .$this->objLanguage->languageText('mod_useradmin_greet7','To login, go to')." "
        .$info['link']." (".$info['url'].")\n"
        .$this->objLanguage->languageText('word_sincerely')."\n"
        .$this->objLanguage->languageText('mod_useradmin_greet5')."\n";
        $subject=$this->objLanguage->languageText('mod_useradmin_greet6');
        $emailtext=str_replace('KEWL NextGen',$info['sitename'],$emailtext);
        $subject=str_replace('KEWL NextGen',$info['sitename'],$subject);
        $header="From: ".$this->objLanguage->languageText('mod_useradmin_greet5').'<noreply@'.$info['server'].">\r\n";
        @mail($email,$subject,$emailtext,$header);
    }
	*/

    /**
    * Method to determine site URL for email and other purposes
    * @returns array $kngdata an array of the info on the site
    */
	/*
    function siteURL()
    {
        $KNGname=$this->objConfig->getParam('KEWL_SITENAME','KEWL.NextGen');
        $WWWname=$_SERVER['SERVER_NAME'];
        $KNGpath=$this->objConfig->getParam('KEWL_SITE_ROOT');
        if ($KNGpath==''){
            $KNGpath=$_SERVER['PHP_SELF'];
        }
        $url='http://'.$WWWname.$KNGpath;
        return array(
            'url'=>$url,
            'sitename'=>$KNGname,
            'link'=>" <a href='$url'>$KNGname</a> ",
            'server'=>$WWWname
            );
    }
	*/

    /**
    * Is a user an LDAP user.
    * @param string $userId
    * @returns boolean
    */
    function isLDAPUser($userId)
    {
        $data=$this->getUsers('userId',$userId);
        return $data[0]['pass']==sha1('--LDAP--');
    }

    /**
    * method to create user folder
    * @author James Scoble, Paul Scott
    * @param string $userId
    */
	/*
    function makeUserFolder($userId)
    {
        // First we check that the 'userfiles' folder exists
        $courses = $this->objConfig->getcontentBasePath();
        if (!(file_exists($courses))){
            $oldumask = umask(0);
            @mkdir($courses, 0777); // or even 01777 so you get the sticky bit set
            umask($oldumask);
        }
        // Then we create the user folder
        $dir = $this->objConfig->getcontentBasePath().$userId;
        if (!(file_exists($dir))){
            $oldumask = umask(0);
            @mkdir($dir, 0777); // or even 01777 so you get the sticky bit set
            umask($oldumask);
        }
    }
	*/

    /**
    * Method to set a user to Active or InActive status
    * @param string $userId;
    * @param char $newstate;
    * @returns TRUE|FALSE
    */

	/*
    function setActive($userId,$newstate)
    {
        if ($this->valueExists('userid',$userId)){
            return $this->update('userid',$userId,array('isactive'=>$newstate));
        } else {
            return FALSE;
        }
    }
	*/
} // end of class sqlUsers

?>
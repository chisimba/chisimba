<?php
 /**
 * sqlusers class
 * 
 * This class is used by the useradmin module, to deal with the user tables
 * 
 * PHP version 5
 *  
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * 
 * @category  Chisimba
 * @package   security
 * @author James Scoble <jscoble@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
* This class is used by the useradmin module
* @author James Scoble
*/
class sqlUsers extends dbtable
{
 
    var $objUser;
    var $objLanguage;
    var $objConfig;
    
    function init()
    {
    parent::init('tbl_users');
        $this->objConfig=$this->getObject('config','config');
        $this->objUser=$this->getObject('user','security');
        $this->objLanguage=$this->getObject('language','language');
    }

    /**
    * method to add a new user
    * @param array $info data for new user
    * @returns string $id the PKID of the new login
    */
    function addUser($info)
    {
        $cdate=date("Y-m-d");
        $sdata['userId']=$info['userId'];
        $sdata['username']=$info['username'];
        $sdata['title']=$info['title'];
        //Workaround for possible inconsistancy in array index conventions and SQL fields
        if (isset($info['firstName'])){
            $sdata['firstname']=$info['firstName'];
        } else {
            $sdata['firstname']=$info['firstname'];
        }
        $sdata['surname']=$info['surname'];
        $sdata['PASS']=sha1($info['password']);
        $sdata['CreationDate']=$cdate;
        if (isset($info['howCreated'])){
            $sdata['howCreated']=$info['howCreated'];
        }
        if (isset($info['emailAddress'])){
            $sdata['emailaddress']=$info['emailAddress'];
        } else {
            $sdata['emailaddress']=$info['emailaddress'];
        }
        $sdata['sex']=$info['sex'];
        $sdata['country']=$info['country'];
        if (isset($info['staffnumber'])){
            $sdata['staffnumber']=$info['staffnumber'];
        }
        
        $id=$this->insert($sdata);
        // No longer creating user folders at this stage - might be used later
        //$this->makeUserFolder($info['userId']);
        
        //$tblusergroups=$this->newObject('usergroups','security');
        //$tblusergroups->newEntry($info['userId'],$info['accessLevel']);
        return $id;
    }

    /**
    * Method to create a user account from getParam()
    * @param string $userId
    * @returns string $id
    */
    function createUserAccount($userId,$howcreated='selfregister')
    {
        $password=$this->getParam('password');
        if ($password==''){
            $objPassword=$this->getObject('passwords','useradmin');
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
            'PASS'=>$cryptpassword,
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

    /**
    * Method to edit a user account using info from getParam()
    * Does not change the password or userId
    * @param string $userId
    * @returns string $id
    */
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
         //$sql="select userId,username,title,firstName,surname,emailAddress from tbl_users";
         $sql="select * from tbl_users";
         if (($how=='username')||($how=='surname')||($how=='emailAddress')||
                    ($how=='userId')||($how=='creationDate')||($how=='logins')||($how=='isActive'))
        {
            if ($match=='listall') {
                            $match='';
                        }
                        $match=addslashes($match);
                        if  ($exact==TRUE){
                            $sql.=" where ".$how." = '".$match."' order by ".$how;
                        } else if ($exact=='greater'){
                            $sql.=" where ".$how." > '".$match."' order by ".$how;
                        } else if ($exact=='less'){
                            $sql.=" where ".$how." < '".$match."' order by ".$how;
                        } else {
                            $sql.=" where ".$how." like '".$match."%' order by ".$how;
                        }
        }
                if ($how=='notused'){
                    $sixMonthsAgo=date('Y-m-d',time()-15552000);
                    $sql.=" where logins='0' and creationDate<'$sixMonthsAgo' order by creationDate";
                }

        $r1=$this->getArray($sql);
        return $r1;
    }  // end of function getUsers


    /**
    * This is a method to delete a group of users at once
    * using an array of userId's
    * It will not delete Site-Admin user accounts
    * @author James Scoble
    * @param array $userArray
    */
    function batchDelete($userArray)
    {
        foreach ($userArray as $line)
        {
            $isAdmin=$this->objUser->lookupAdmin($line);
            if ($isAdmin==FALSE){
                $this->delete('userId',$line);
            }
        }
    }

    /**
    * method to check info before adding into database
    * @author James Scoble
    * @param string $userId
    * @param string $username
    */
    function checkDbase($userId,$username)
    {      
        $sql="select COUNT(*) as count from tbl_users where username='".$username."'";
        $count=$this->getArray($sql);
        if ($count[0]['count']>0) { return "username_taken";}
        $sql="select COUNT(*) as count from tbl_users  where userId='".$userId."'";
        $count=$this->getArray($sql);
        if ($count[0]['count']) { return "userid_taken";}
        return "Looks Okay";
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
        $data=$this->getUsers('userId',$userId,TRUE);
        if ((($oldpassword=='ADMIN')&&($this->objUser->isAdmin()))||(strtolower($data[0]['PASSWORD'])==strtolower(sha1($oldpassword))))
        {
            // here we proceed to actually do the change
            $cryptpassword=sha1($newpassword);
            //$sql="update tbl_users set password='".$cryptpassword."' where userId='".$userId."'";
            $this->update('userId',$userId,array('password'=>$cryptpassword));
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
    function resetPassword($username,$email)
    {
        $username=trim($username);
        $email=trim($email);
        $sql="select userId, username, firstname, surname, PASS from tbl_users where username='$username' and emailAddress='$email'";
        $result=$this->getArray($sql);
        if (isset($result[0])){ 
            // Get the user's info 
            $userId=$result[0]['userId']; 
            $password=$result[0]['PASS']; 
            $firstname=$result[0]['firstname']; 
            $surname=$result[0]['surname']; 
            if ($password!=(sha1('--LDAP--'))){ 
                $objPassword=$this->getObject('passwords','useradmin'); 
                $newpassword=$objPassword->createPassword(); 
                $cryptpassword=sha1($newpassword); 
                $this->update('userId',$userId,array('password'=>$cryptpassword)); 
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
        .str_replace('SITENAME',$info['sitename'],$this->objLanguage->languageText('mod_useradmin_greet7','To login, go to'))." "
        .$info['link']." (".$info['url'].")\n"
        .$this->objLanguage->languageText('word_sincerely')."\n"
        .str_replace('SITENAME',$info['sitename'],$this->objLanguage->languageText('mod_useradmin_greet5'))."\n";
        $subject=str_replace('SITENAME',$info['sitename'],$this->objLanguage->languageText('mod_useradmin_greet6')); 
        $emailtext=str_replace('SITENAME',$info['sitename'],$emailtext);
        $subject=str_replace('SITENAME',$info['sitename'],$subject);
        $header="From: ".str_replace('SITENAME',$info['sitename'],$this->objLanguage->languageText('mod_useradmin_greet5')).'<noreply@'.$info['server'].">\r\n";
        @mail($email,$subject,$emailtext,$header);
    }
    
    /**
    * Method to determine site URL for email and other purposes
    * @returns array $kngdata an array of the info on the site
    */
    function siteURL()
    {
        $KNGname=$this->objConfig->getValue('KEWL_SITENAME','_site_');
        $WWWname=$_SERVER['SERVER_NAME'];
        $KNGpath=$this->objConfig->getValue('KEWL_SITE_ROOT','_site_');
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
    
    /**
    * method to tell if a user is an LDAP user
    * @author James Scoble
    * @param $userId
    * @returns boolean TRUE|FALSE
    */
    function isLDAPUser($userId)
    {
        $data=$this->getUsers('userId',$userId);
        if ($data[0]['PASSWORD']==sha1('--LDAP--'))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /** 
    * method to create user folder
    * @author James Scoble, Paul Scott
    * @param string $userId
    */
    function makeUserFolder($userId)
    {
        // First we check that the 'userfiles' folder exists
        $courses = $this->objConfig->userfiles();
        if (!(file_exists($courses))){
            $oldumask = umask(0);
            @mkdir($courses, 0777); // or even 01777 so you get the sticky bit set
            umask($oldumask);
        }
        // Then we create the user folder
        $dir = $this->objConfig->userfiles()."/".$userId;
        if (!(file_exists($dir))){
            $oldumask = umask(0);
            @mkdir($dir, 0777); // or even 01777 so you get the sticky bit set
            umask($oldumask);
        }
    }


    /**
    * Method to set a user to Active or InActive status
    * @param string $userId;
    * @param char $newstate;
    * @returns TRUE|FALSE
    */
    function setActive($userId,$newstate)
    {
        if ($this->valueExists('userId',$userId)){
            $arr=array('isActive'=>$newstate);
            return $this->update('userId',$userId,$arr);
        } else {
            return FALSE;
        }
    }
    

} // end of class sqlUsers

?>

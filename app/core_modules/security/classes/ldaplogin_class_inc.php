<?php
 /**
 * Ldaplogin class
 * 
 * This class interacts with a remote LDAP server to get information about users.
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/* Class of LDAP-related functions
* @author James Scoble
*/
class ldaplogin extends object
{

    //var $ldapserver="services-ds.uwc.ac.za"; // hard-coded for now - will be changed later
    private $ldapserver="192.102.9.68"; // hard-coded for now - will be changed later
    private $usernumber='generationqualifier';

    public function init()
    {
        if (defined('KEWL_LDAP_SERVER')){
            $dbconfig=$this->getObject('altconfig','config');
            $this->ldapserver=$dbconfig->ldapServer();
        }
    }

    /**
    * method to contact the ldap server and see if a given username is valid there
    * @author James Scoble
    * @param string $username
    * @param string $where the LDAP "domain" to look in
    * @return string|bool - string if successful, FALSE if not
    */
    public function checkUser($username,$where="o=UWC")
    {
        $ldapconn = ldap_connect($this->ldapserver);
        $ldapbind = @ldap_bind($ldapconn);
        if (!$ldapbind) {
            $this->setSession('ldaperror','FAIL');
            return FALSE;
        }
        $filter='cn='.$username;
        $look=array('dn');
        $find=ldap_search($ldapconn,$where, $filter,$look);
        $data=ldap_get_entries($ldapconn, $find);
        ldap_close($ldapconn);
        if ($data['count']>0) {
            //print_r($data); die();
            return $data[0]['dn'];
        } else {
            return FALSE;
        }
    }

    /**
    * method to contact the ldap server and see if a given username and password are valid there
    * @author James Scoble
    * @param string $username
    * @param string $passwd
    * @param string $where the LDAP "domain" to look in
    * @return string|bool - string if successful, FALSE if not
    */
    public function tryLogin($username,$passwd,$where='o=UWC')
    {
        // Check for blank password - there's a bug in LDAP that makes it accept '' as valid.
        if ($passwd==''){
            return FALSE;
        }
        // Get the user domain-name - return FALSE if its not there
        $dn=$this->checkUser($username);
        if (!$dn)
        {
            return FALSE;
        }
        // Now try to "login" to LDAP with the domain-name and password
        $ldapconn = ldap_connect($this->ldapserver);
        $ldapbind = @ldap_bind($ldapconn,$dn,$passwd);
        if (!$ldapbind){
            return FALSE;
        }
        // If the login succeeded we can get the info.
        $data=$this->getInfo($ldapconn, $username,$where);
        ldap_close($ldapconn);

        return $data; // send an array of the results
    }

    /**
    * method to get a user's info from LDAP
    * @author James Scoble
    * @param string $username
    * @param dbasehandle $ldapconn
    * @param string $where the LDAP "domain" to look in
    * @return string|bool - string if successful, FALSE if not
    */
    public function getInfo($ldapconn,$username,$where='o=uwc')
    {
        $filter='cn='.$username;
        $look=array('surname','givenname','mail',$this->usernumber);
        $find=ldap_search($ldapconn,$where, $filter,$look);
        $data=ldap_get_entries($ldapconn, $find);
        $results['username']=$username;
        $results['surname']=$data[0]['surname'][0];
        $results['firstname']=$data[0]['givenname'][0];
        $results['emailaddress']=$data[0]['mail'][0];
        if (isset($data[0][$this->usernumber][0]) && is_numeric($data[0][$this->usernumber][0]))
        {
            $results['userid']=$data[0][$this->usernumber][0];
        }
        else
        {
            $results['userid']=FALSE;
        }
        $results['title']='';
        $results['logins']='0';
        $results['password']='--LDAP--';

        if(!empty($results) || !is_bool($results['userid']))
        {

            return $results; // send an array of the results
        }
        else {
            return false;
        }
    }

    /**
    * method to check if a user is an Academic
    * @author James Scoble
    * @param string $username
    * @param string $where the LDAP "domain" to look in
    * @returns TRUE|FALSE
    */
    public function isAcademic($username,$where='ou=ACADEMIC,o=UWC')
    {
        $test=$this->checkUser($username,$where);
        if (!$test){
            return FALSE;
        } else {
            return TRUE;
        }
    }

}


?>

<?php
/**
 *
 * IMS XML message generator
 *
 * Builds the XML message for calling the IMS LTI rest url.  
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
 * @category  Chisimba
 * @package   twitter
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: tweetbox_class_inc.php 8227 2008-03-27 20:05:32Z dkeats $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* IMS XML message generator
*
* Builds the XML message for calling the IMS LTI rest url.  
*
* @author Derek Keats
* @package imslti
*
*/
class ltixmlmsg extends object
{
	
    /**
    *
    * Constructor for the ltiwrapper class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
		$this->objUser = $this->getObject('user', 'security');
    }
    
    public function set($param, $value)
    {
    	$this->$param = $value;
    }

    /**
    *
    * Method to render the tweetbox
    *
    * @access public
    * @return string The rendered tweetbox
    *
    */
    public function show()
    {
    	$xmlMsg = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    	$xmlMsg .= "<launchRequest>\n<launchData>\n";
    	$xmlMsg .= $this->getGroup();
    	$xmlMsg .= $this->getMembership();
    	$xmlMsg .= $this->getUser();
    	$xmlMsg .= $this->getLaunchDefinition();
    	$xmlMsg .= "</launchData>\n</launchRequest>\n";
    	
    }
    
    public function getGroup()
    {
    	return '<group>
      <available>true</available>
      <groupType>course</groupType>
      <shortDescription>Test</shortDescription>
    </group>';
    }
    
    /**
    * 
    * The value must be either Instructor or Student (note the case)
    *  
    */
    public function getMembership()
    {
    	if ($this->objUser->isAdmin() || $this->objUser->isLecturer()) {
    		$groupRole = "Instructor";
    	} else {
    		$groupRole = "Student";
    	}
    	return '<membership>
      <groupRoles>
        <groupRole>
          <value>' . $groupRole . '</value>
        </groupRole>
      </groupRoles>
    </membership>';
    }
    /**
     * 
     * The systemRole should be left at User as it is not currently used
     * in the implementations of the spec
     * 
     */
    public function getUser()
    {
    	$ltiUser = $this->objUser->userName();
    	$ltiEmail = $this->objUser->email();
    	$ltiFn = $this->objUser->getFirstname();
    	$ltiSn = $this->objUser->getSurname();
    	return '<user>
      <email>' . $ltiEmail . '</email>
      <firstName>' . $ltiFn . '</firstName>
      <fullName> ' . $ltiFn . " " . $ltiSn . '</fullName>
      <lastName>' . $ltiSn . '</lastName>
      <systemRole>User</systemRole>
    </user>';
    }
    
    public function getLaunchDefinition()
    {
    	return '  <launchDefinition>
    <displayTarget>IFrame</displayTarget>
    <launchLink>groupview</launchLink>
    <pageId>2</pageId>
    <toolConsumerId>moodle_0a1b_2</toolConsumerId>
    <toolId>wimbaclassroomlti</toolId>
    <userToken>admin</userToken>
  </launchDefinition>';
    }


}
?>
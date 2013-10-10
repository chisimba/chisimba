<?php
/**
 * Presence IM dbtable derived class
 *
 * Class to interact with the database for the popularity contest module
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
 * @category  chisimba
 * @package   im
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dbimpresence extends dbTable
{

    /**
     * Constructor
     *
     */
    public function init()
    {
        parent::init('tbl_im_presence');
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->timeLimit = $this->objSysConfig->getValue ( 'imtimelimit', 'im' );
    }


    public function updatePresence($userarr)
    {
        log_debug($userarr);


        //split user and user agent
        $userSplit = explode("/", $userarr['from']);

        // check if user exists in msg table
        $status = $this->userExists($userSplit[0]);
        $times = $this->now();
        $insarr['datesent'] = $times;
        $insarr['person'] = $userSplit[0];
        $person = $insarr['person'];
        $insarr['status'] = $userarr['type'];
        $insarr['presshow'] = $userarr['show'];
        $insarr['useragent'] = $userSplit[1];


        if($status == FALSE)
        {
            $insarr['counsilor'] = $this->assignUserToCounsilor($person);
            $this->addRecord($insarr);
        }
        else {

            // update the presence info for this user
            $this->update('id', $status[0]['id'], $insarr, 'tbl_im_presence');
        }
    }

    /**
     * Private method to insert a record to the popularity contest table as a log.
     *
     * This method takes the IP and module_name and inserts the record with a timestamp for temporal analysis.
     *
     * @param array $recarr
     * @return string $id
     */
    private function addRecord($insarr)
    {

        return $this->insert($insarr, 'tbl_im_presence');
    }

    public function userExists($user)
    {
        $count = $this->getRecordCount("WHERE person = '$user'");
        if($count > 0)
        {
            return $this->getAll("WHERE person = '$user'");
        }
        else {
            return FALSE;
        }
    }

    public function getPresence($jid)
    {
        $userSplit = explode("/", $jid);
        $res = $this->getAll("WHERE person = '$userSplit[0]'");
        if(!empty($res))
        {
            return $res[0]['presshow'];
        }
        else {
            return NULL;
        }
    }

    /**
     *Method to get all the active users
     *@return array
     *@access public
     */
    public function getAllActiveUsers($userId)
    {
        //$sql = "select distinct(person)as person from tbl_im_presence where counsilor='$userId' and status != 'unavailable' ORDER BY datesent ASC";
        $interval = $this->timeLimit;
		$sql="SELECT distinct(person) as person from tbl_im_presence WHERE counsilor='$userId' and 
				datesent > DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL $interval HOUR_MINUTE)";


		$ret = $this->query($sql);
		
	return $ret;
    }

    /**
     *MEthod to update eh patient coujnt and to assign
     *a counsilor to a patient
     *@param string $person
     */
    public function assignUserToCounsilor($person)
    {

        parent::init('tbl_im_users');

        //get the counsilor with the lowest number of patients
        $users = $this->getAll("ORDER BY patients ASC LIMIT 5" );
        $user = $users[0];

        //assign the patient to the counsilor
        $fields = array('person'=> $person,
                        'patients' => intval($user['patients']) + 1
                        );
        $this->update('id',$user['id'], $fields, 'tbl_im_users');

        parent::init('tbl_im_presence');

        return $user['userid'];
    }


    /**
    *
    * Method to get the number of users assigned to a counsilor
    * @param string $userId
    * @return array
    *
    */
    public function getUsers($userId)
    {
        return $this->getAll("WHERE counsilor = '$userId'");

    }

    /**
    * Method to get the number of users assigned
    * @return array
    */
    public function numOfUserAssigned($userId)
    {
        $sql = "WHERE counsilor = '$userId'";
        return $this->getRecordCount($sql);
    }

    /**
    * Method to truncate the presence table
    */
    public function resetCounsillors()
    {
        var_dump($this->dsn);die;
        //do the archiving first
        $sql = "INSERT INTO `chisimba1`.`tbl_das_messagearchive`
                    SELECT *
                    FROM `chisimba1`.`tbl_im`";
        $this->query($sql);
        
        //reset the presence table
        $sql = "TRUNCATE TABLE tbl_im_presence";
        $this->query($sql);
        
        //reset the counsellors
        $sql = "update tbl_im_users set patients=0";
        $this->query($sql);

        //reset the messages
        $sql = "TRUNCATE TABLE tbl_im";
        $this->query($sql);
    }

	/**
	* Method to reassign a conversation to someone else
	* @param string $patient
	* @param string $newCounsellor
	*/
	public function reAssignCounsellor($patient, $newCounsellor)
	{
		//die($patient.$newCounsellor);
		if(empty($patient) || empty($newCounsellor))
		{
			return false;
		}
		return $this->update('person',$patient,array('counsilor' => $newCounsellor)); 
	} 


	/**
	* Method to count the number of live conversations
	*/
	public function getLiveConversationCount()
	{

		$sql = 'SELECT count(id) as count WHERE datesent > $timelimit';
		return $this->query($sql);
	}
}
?>

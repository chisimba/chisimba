<?php
/**
 * message IM dbtable derived class
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
 * @package   imusers
 * @author    Wesley Nitsckie
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dbimusers extends dbTable
{

    /**
     * Constructor
     *
     */
    public function init()
    {
        parent::init('tbl_im_users');
        $this->objPresence = $this->getObject('dbimpresence');
    }

    /**
     *Method to check if a user is a counsilor
     *@param string userId
     *@return boolean
     */
    public function isCounsilor($userId)
    {
    //var_dump($this->valueExists('userid', $userId));

    if($this->valueExists('userid', $userId))
        {
            return TRUE;
        }else{
            return FALSE;
        }

    }

    /**
     *Method to add a counsilor
     *@param string $userId
     */
    public function addCounsilor($userId)
    {
        if (!$this->isCounsilor($userId))
        {
            return $this->insert(array('userid' => $userId));
        }
    }

    /**
     *Method to remove a counsilor
     *@param string $userId
     */
    public function removeCounsilor($userId)
    {
        if ($this->isCounsilor($userId))
        {
            //remove from users table as well
            $this->delete("userid", $userId);
			
            $this->update('counsilor', $userId, array('counsilor' => null), 'tbl_im_presence');
			
        }

		//re assign all this counsellors patients
		$this->redistributePatients();
    }

	/**
	* Method to redistribute a counsellors patients to the other 
	* counsellors
	*/
	public function redistributePatients()
	{
		$patients = $this->objPresence->getAll("WHERE isNull(counsilor)");
	
		foreach($patients as $patient)
		{
			$counsellorId = $this->assignUserToCounsilor($patient['person']);
			$this->update('person', $patient['person'], array('counsilor' => $counsellorId), 'tbl_im_presence');
		}
	}

	/**
	* Method to assign a user to a counsellor
	* @param string $person
	*/
    public function assignUserToCounsilor($person)
    {

        $users = $this->getAll("ORDER BY patients ASC");
        $user = $users[0];
        $this->update('id',$user['id'], array('person'=> $person, 'patients' => intval($user['patients']) + 1));
        return $user['userid'];
    }

	/**
	*Method to count the number of counsellors
	*/
	public function countCounsellors()
	{
		$sql = "SELECT count(id) as c from tbl_im_users";
		$rec = $this->query($sql);

		return $rec[0]['c'];
	}

    /**
    * Method to check if a user is set to receive
    * patient automattically
    */
    public function manualAssign($userId)
    {
        $row = $this->getRow('userid',$userId);
        
        if ($row['manualassign'] == '1')
        {
            return TRUE;
        } else {
            return FALSE;
        }
    }

	/**
	* Method to set the manual assignment
	* @param string $userid
	*/
	public function setManualAssignment($userId, $assign = 1)
	{
        $row = $this->getRow('userid',$userId);
        //var_dump($row);die;
        $assign =  ($this->manualAssign($userId)) ? 0 : 1;
		return $this->update('userid' , $userId, array('manualassign' => $assign));

	}


}

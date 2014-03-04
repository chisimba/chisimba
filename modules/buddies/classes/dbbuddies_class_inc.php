<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_buddies
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbBuddies extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init() 
    {
        parent::init('tbl_buddies');
        //$this->USE_PREPARED_STATEMENTS=True;
    }
    
    /**
    * 
    * Method to get the buddies that are online, returning
    * an array containing the buddyid and whenlast active.
    * 
    * Note: Added by Derek Keats on 2007 09 21 for use in the
    * webpresent module
    * 
    * @access public
    * @return string array An arrayof buddies online
    * 
    */
    public function getBuddiesOnline($userId)
    {
        $sql = "SELECT buddies.buddyid, online.whenlastactive "
          . " FROM tbl_buddies AS buddies," 
          . " tbl_loggedinusers AS online "
          . " WHERE buddies.buddyid = online.userid"
          . " AND buddies.userid='". $userId."'";
          return $this->getArray($sql);
    }
    
    /**
    * Returns an array that contains buddyId and Fullname 
    * ordered by first name where the person is my buddy, and 
    * nothing else in the array.
	* @param string $userId The user ID
	* @return array The buddies for a user
    */
	public function getBuddies($userId)
	{
		$sql = "SELECT 
            tbl_buddies.buddyId, 
            CONCAT(tbl_users.firstName, ' ', tbl_users.surname) AS Fullname
        FROM tbl_buddies, tbl_users
		WHERE 
		(tbl_buddies.buddyId = tbl_users.userId)
        AND (tbl_buddies.userId = '" . $userId . "')
        AND (tbl_buddies.isBuddy = '1')
        ORDER BY Fullname";
		return $this->getArray($sql);
	}

    /**
    * Returns an array that contains userId and Fullname 
    * ordered by first name where the person is my fan, and 
    * nothing else in the array.
	* @param string $userId The user ID
	* @return array The fans for a user
    */
	public function getFans($userId)
	{
		$sql = "SELECT 
            tbl_buddies.userId, 
            CONCAT(tbl_users.firstName, ' ', tbl_users.surname) AS Fullname
        FROM tbl_buddies, tbl_users
		WHERE 
		(tbl_buddies.userId = tbl_users.userId)
        AND (tbl_buddies.buddyId = '" . $userId . "')
        AND (tbl_buddies.isFan = '1')
        ORDER BY Fullname";
		return $this->getArray($sql);
	}

    /**
    * Return all records
	* @param string $userId The user ID
	* @return array The buddies for a user
    */
	public function listAll($userId)
	{
		$sql = "SELECT 
            tbl_buddies.buddyId AS buddyId, 
            tbl_buddies.isBuddy,
            tbl_buddies.isFan,
            tbl_users.firstName, 
            tbl_users.surname, 
            tbl_users.emailAddress 
        FROM tbl_buddies, tbl_users
		WHERE (tbl_buddies.userId = '" . $userId . "')
		AND (tbl_buddies.buddyId = tbl_users.userId)
        ORDER BY tbl_users.firstName, tbl_users.surname";
		return $this->getArray($sql);
	}

    /**
    * Return single record
	* @param string $userId The user ID
	* @param string $buddyId The buddy ID
    */
	public function listSingle($userId, $buddyId)
	{
		$sql = "SELECT * FROM tbl_buddies
        WHERE (userId = '" . $userId . "')
        AND (buddyId = '" . $buddyId . "')";
		return $this->getArray($sql);
	}

    /**
    * Return count 
	* @param string $userId The user ID
	* @param string $buddyId The buddy ID
    */
	public function countSingle($userId, $buddyId)
	{
		$sql = "SELECT count(*) AS count FROM tbl_buddies
        WHERE (userId = '" . $userId . "')
        AND (buddyId = '" . $buddyId . "')
        AND (isBuddy = '1')";
		return $this->getArray($sql);
	}

	/**
	* Insert a record
	* @param string $userId The user ID
	* @param string $buddyId The buddy ID
	*/
	public function insertSingle($userId, $buddyId)
	{
        $list = $this->listSingle($userId, $buddyId);
        if (empty($list)) {
    		$this->insert(array(
    			'userId' => $userId,
    			'buddyId' => $buddyId,
                'isBuddy' => '1',
                'isFan' => '0'
    		));
        }
        else {
            $this->update(
                'id',
                $list[0]['id'],
                array(
                    'isBuddy' => '1'
                )
            );
        };
        $list = $this->listSingle($buddyId, $userId);
        if (empty($list)) {
    		$this->insert(array(
    			'userId' => $buddyId,
    			'buddyId' => $userId,
                'isBuddy' => '0',
                'isFan' => '1'
    		));
        }
        else {
            $this->update(
                'id',
                $list[0]['id'],
                array(
                    'isFan' => '1'
                )
            );
        };
		return;	
	}

	/**
	* Deletes records
	* @param string $userId The user ID
	* @param string $buddyId The buddy ID
	*/
	public function deleteSingle($userId, $buddyId)
	{
        $list = $this->listSingle($userId, $buddyId);
        $this->update(
            'id',
            $list[0]['id'],
            array(
                'isbuddy' => '0'
            )
        );
        if ($list[0]['isfan']=='0') {
            $this->delete(
                'id',
                $list[0]['id']
            );
        }
        $list = $this->listSingle($buddyId, $userId);
        $this->update(
            'id',
            $list[0]['id'],
            array(
                'isFan' => '0'
            )
        );
        if ($list[0]['isbuddy']=='0') {
            $this->delete(
                'id',
                $list[0]['id']
            );
        }
        return;
	}
        
    /**
     *
     * Method to return all online buddies
     * 
     * @access public
     * @param string $userId The id of the user to get buddie for
     * @return array $buddies All online buddies 
     */
    public function getOnlineBuddies($userId)
    {
        $sql = "SELECT *"
          . " FROM tbl_buddies AS buddies," 
          . " tbl_loggedinusers AS online, "
          . " tbl_users as users"
          . " WHERE buddies.buddyid = online.userid"
          . " AND buddies.userid='". $userId."'"
          . " AND users.userid = online.userid";
          $data = $this->getArray($sql);
          
        $result = array();

        if ($data)
        {
            foreach ($data as $line)
            {
                $result[$line['userid']] = $line['firstname'] . ' ' . $line['surname'];
            }
        }
        return $result;
    }
}
?>

<?php
/* ------ data class extends dbTable for all messaging database tables ------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the messaging database tables
* @author Kevin Cyster
*/

class dbmessaging extends dbTable
{
    /**
    * @var object $objUser: The user class in the security module
    * @access private
    */
    private $objUser;

    /**
    * @var object $objContext: The dbcontext class in the context module
    * @access public
    */
    public $objContext;

    /**
    * @var object $objModules: The modules class in the modulecatalogue module
    * @access public
    */
    public $objModules;

    /**
    * @var object $objWorkgroup: The dbworkgroup class in the workgroup module
    * @access public
    */
    public $objWorkgroup;

    /**
    * @var string $userId: The user id of the current user
    * @access private
    */
    private $userId;

    /**
    * @var string $tblUsers: The name of an additional database table to be affected
    * @access private
    */
    private $tblUsers;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        $this->tblUsers = 'tbl_users';
        
        // system classes
        $this->objUser = $this->getObject('user', 'security');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');        
        $this->objContext = $this->getObject('dbcontext', 'context');
        if($this->objModules->checkIfRegistered('workgroup')){
            $this->objWorkgroup = $this->getObject('dbworkgroup', 'workgroup');
        }
        // global variables
        $this->userId = $this->objUser->userId();
    }
    
/* ------------------ Functions for changeing tables ------------------*/

	/**
	* Method to dynamically switch tables
	*
	* @access private
	* @param string $table: The name of the table
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _changeTable($table)
	{
		try {
			parent::init($table);
			return TRUE;
		}
		catch (Exception $e)
		{
		    throw customException($e->getMessage());
		    exit();
		}
	}
	
	/**
	* Method to set the rooms table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setRoomTable()
	{
        return $this->_changeTable('tbl_messaging_rooms');
    }

	/**
	* Method to set the messages table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setMessagesTable()
	{
        return $this->_changeTable('tbl_messaging_messages');
    }

	/**
	* Method to set the userlog table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setUserlogTable()
	{
        return $this->_changeTable('tbl_messaging_userlog');
    }
    
	/**
	* Method to set the users table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setUsersTable()
	{
        return $this->_changeTable('tbl_messaging_users');
    }
    
	/**
	* Method to set the banned users table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setBannedTable()
	{
        return $this->_changeTable('tbl_messaging_banned');
    }
    
	/**
	* Method to set the settings table
	*
	* @access private
	* @return boolean: TRUE on success FALSE on failure
	*/
	private function _setSettingsTable()
	{
        return $this->_changeTable('tbl_messaging_settings');
    }
    
/* ------------------ Functions for tbl_messaging_rooms ------------------*/

    /**
    * Method to add a chat room
    *
    * @access public
    * @param array $roomData: An array containing the chat room data  
    * @return string $roomId: The id of the chat room that was added
    **/
    public function addChatRoom($roomData)
    {
        $this->_setRoomTable();
        
        $fields['room_type'] = $roomData['room_type'];
        $fields['room_name'] = $roomData['room_name'];
        $fields['room_desc'] = $roomData['room_desc'];
        $fields['text_only'] = $roomData['text_only'];
        $fields['disabled'] = $roomData['disabled'];
        $fields['owner_id'] = $roomData['owner_id'];
        $fields['date_created'] = date('Y-m-d H:i:s');
        $fields['updated'] = date('Y-m-d H:i:s');
        $roomId = $this->insert($fields);
        return $roomId;
    }

    /**
    * Method for editing a room
    *
    * @access public
    * @param array $roomData: An array containing the chat room data  
    * @param string $roomId: The id of the chat room to edit
    * @return void
    */
    public function editChatRoom($roomId, $roomData)
    {
        $this->_setRoomTable();
        $fields['room_name'] = $roomData['room_name'];
        $fields['room_desc'] = $roomData['room_desc'];
        $fields['text_only'] = $roomData['text_only'];
        $fields['disabled'] = $roomData['disabled'];
        $fields['updated'] = date('Y-m-d H:i:s');
        $this->update('id', $roomId, $fields);
    }

    /**
    * Method to get a chat room
    *
    * @access public
    * @param string $roomId: The id of the room to get the data for
    * @return array/boolean $data: The chat room data / FALSE on failure
    **/
    public function getChatRoom($roomId)
    {
        $this->_setRoomTable();
        $sql = "WHERE id = '".$roomId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to return all chat room records
    *
    * @access public
    * @param string $contextCode: The context code if the user is in context
    * @return array $data: The chat room data
    **/
    public function listChatRooms($contextCode)
    {
        $this->_setRoomTable();
        if($contextCode != NULL){
            $sql = " WHERE owner_id = '".$contextCode."'";
            $sql .= " OR room_type < 2";
        }else{
            $sql = " WHERE room_type < 2";
        }
        $sql .= " ORDER BY date_created ";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for deleting a chat room
    *
    * @access public
    * @param string $roomId:  The id of the chat room to be deleted
    * @return void
    */
    public function deleteChatRoom($roomId)
    {
        $this->_setRoomTable();
        $this->delete('id', $roomId);
    }
    
    /**
    * Method to get a context chat room
    * 
    * @access public
    * @param string $contextCode: The context code | Current contextCode if NULL
    * @return array/boolean $data: The context chat room data / FALSE on failure
    */
    public function getContextChatRoom($contextCode = NULL)
    {
        $this->_setRoomTable();
        if($contextCode == NULL){            
            $contextCode = $this->objContext->getContextCode();
        }
        if($contextCode != NULL){
            $contextDetails = $this->objContext->getContextDetails($contextCode);
            $sql = " WHERE owner_id = '".$contextCode."'";
            $data = $this->getAll($sql);
            if(!empty($data)){
                return $data[0];
            }else{
                $fields['room_type'] = 2;
                $fields['room_name'] = $contextDetails['title'];
                $fields['room_desc'] = $contextDetails['about'];
                $fields['text_only'] = 0;
                $fields['disabled'] = 0;
                $fields['owner_id'] = $contextCode;
                $fields['date_created'] = date('Y-m-d H:i:s');
                $fields['updated'] = date('Y-m-d H:i:s');
                $roomId = $this->insert($fields);
                $data = $this->getChatRoom($roomId);
                return $data;
            }
        } else {
            return $this->getRow('id', 'init_1');
        }
    }
    
    /**
    * Method to get a workgroup chat room
    * 
    * @access public
    * @param string $workgroupId: The workgroup id| Current workgroupId if NULL
    * @return array/boolean $data: The workgroup chat room data / FALSE on failure
    */
    public function getWorkgroupChatRoom($workgroupId = NULL)
    {
        $this->_setRoomTable();
        if($workgroupId == NULL){            
            $workgroupId = $this->objWorkgroup->getWorkgroupId();
        }
        if($workgroupId != NULL){
            $workgroupDetails = $this->objWorkgroup->getDescription($workgroupId);
            $sql = " WHERE owner_id = '".$workgroupId."'";
            $data = $this->getAll($sql);
            if(!empty($data)){
                return $data[0];
            }else{
                $fields['room_type'] = 3;
                $fields['room_name'] = $workgroupDetails;
                $fields['room_desc'] = $workgroupDetails;
                $fields['text_only'] = 0;
                $fields['disabled'] = 0;
                $fields['owner_id'] = $workgroupId;
                $fields['date_created'] = date('Y-m-d H:i:s');
                $fields['updated'] = date('Y-m-d H:i:s');
                $roomId = $this->insert($fields);
                $data = $this->getChatRoom($roomId);
                return $data;
            }
        }
        return FALSE;
    }
    
    /**
    * Method to return the users moderator status
    * 
    * @access public
    * @param string $roomId: The id of the chat room
    * @param string $userId: The id of the user
    * @return boolean $isModerator: TRUE if the user is a moderator FALSE if not
    */
    public function isModerator($roomId = NULL, $userId = NULL)
    {
        $roomId = isset($roomId) ? $roomId : $this->getSession('chat_room_id');
        $userId = isset($userId) ? $userId : $this->userId;
        
        $roomData = $this->getChatRoom($roomId);
        $isModerator = FALSE;
        if($roomData['room_type'] == 0){
            $isAdmin = $this->objUser->inAdminGroup($userId);
            if($isAdmin){
                $isModerator = TRUE;
            }
        }elseif($roomData['room_type'] == 1){
            if($roomData['owner_id'] == $userId){
                $isModerator = TRUE;
            }
        }elseif($roomData['room_type'] == 2){
            $isLecturer = $this->objUser->isContextLecturer($userId);
            if($isLecturer){
                $isModerator = TRUE;
            }
        }elseif($roomData['room_type'] == 3){
            //TODO: Once workgroups has been ported
        }
        return $isModerator;
    }

/* ------------------ Functions for tbl_messaging_messages ------------------*/

    /**
    * Method to add a chat message
    *
    * @access public
    * @param string $message: The chat message that is being sent
    * @param boolean $system: TRUE if the message is system generated FALSE if not
    * @return string $messageId: The id of the chat mesasge that was added
    **/
    public function addChatMessage($message, $system = FALSE)
    {   
        $this->_setMessagesTable();        
        $roomId = $this->getSession('chat_room_id');
        $count = $this->getMessageCount($roomId);
        $date = date('Y-m-d H:i:s');

        if($system){
            $fields['sender_id'] = 'system';
        }else{
            $fields['sender_id'] = $this->userId;            
        }
        $fields['message'] = $message;
        $fields['recipient_id'] = $roomId;
        $fields['message_counter'] = $count + 1;
        $fields['date_created'] = $date;
        $fields['updated'] = $date;
        $messageId = $this->insert($fields);
        return $messageId;
    }

    /**
    * Method to add a chat message
    *
    * @access public
    * @param string $message: The IM message that is being sent
    * @param string $userId: The user the IM message is being sent to
    * @param boolean $system: TRUE if the message is system generated FALSE if not
    * @return string $messageId: The id of the IM mesasge that was added
    **/
    public function addImMessage($userId, $message, $system = FALSE)
    {   
        $this->_setMessagesTable();
        $count = $this->getMessageCount($userId);
        $date = date('Y-m-d H:i:s');
        
        if($system){
            $fields['sender_id'] = 'system';
        }else{
            $fields['sender_id'] = $this->userId;            
        }
        $fields['message_type'] = 1;
        $fields['message'] = $message;
        $fields['recipient_id'] = $userId;
        $fields['delivery_state'] = 0;
        $fields['message_counter'] = $count + 1;
        $fields['date_created'] = $date;
        $fields['updated'] = $date;
        $messageId = $this->insert($fields);
        return $messageId;
    }

    /**
    * Method to return chat room messages from a specific point
    *
    * @access public
    * @param string $roomId: The id of the room to get the messages of
    * @param string $counter: The position to get messages from
    * @return array/boolean $data: The chat room messages / FALSE on failure
    **/
    public function getChatMessages($roomId, $counter)
    {
        $this->_setMessagesTable();

        $sql = "WHERE recipient_id = '".$roomId."'";
        $sql .= " AND message_counter > '".$counter."'";
        $sql .= " ORDER BY message_counter";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to return a chat room messages for a period
    *
    * @access public
    * @param string $roomId: The id of the room to get the messages of
    * @param string $start: The start date to get messages from
    * @param string $end: The end date to get messaged to
    * @return array/boolean $data: The chat room messages / FALSE on failure
    **/
    public function getChatPeriod($roomId, $start, $end)
    {
        $this->_setMessagesTable();

        $sql = "WHERE recipient_id = '".$roomId."'";
        $sql .= " AND date_created >= '".$start."'";
        $sql .= " AND date_created <= '".$end."'";
        $sql .= " ORDER BY message_counter";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
    
    /**
    * Method to return the number of messages for the recipient
    *
    * @access public
    * @param string $recipientId: The id of the recipient to count messages
    * @return integer $messageCount: The number of messages for the recipient
    **/
    public function getMessageCount($recipientId)
    {
        $this->_setMessagesTable();

        $sql = "WHERE recipient_id = '".$recipientId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return count($data);
        }
        return 0;
    }

    /**
    * Method to return unread instant messages
    *
    * @access public
    * @return array/boolean $data: The instant messages for a user / FALSE on failure
    **/
    public function getAllIm()
    {
        $this->_setMessagesTable();

        $sql = "WHERE recipient_id = '".$this->userId."'";
        $sql .= " AND delivery_state < 1";
        $sql .= " ORDER BY message_counter";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to return instant message for display
    *
    * @access public
    * @return array/boolean $data: The instant messages for a user / FALSE on failure
    **/
    public function getIM()
    {
        $this->_setMessagesTable();

        $sql = "SELECT *, messages.id AS mid FROM tbl_messaging_messages AS messages";
        $sql .= " LEFT JOIN tbl_messaging_settings AS settings";
        $sql .= " ON messages.sender_id = settings.user_id";
        $sql .= " WHERE recipient_id = '".$this->userId."'";
        $sql .= " AND delivery_state < 1";
        $sql .= " ORDER BY message_counter";
        $data = $this->getArray($sql);
        if(!empty($data)){
            $imData = $data[0];
            $this->update('id', $imData['mid'], array('delivery_state' => 1));            
            return $imData;
        }
        return FALSE;
    }
/* ------------------ Functions for tbl_messaging_userlog ------------------*/

    /**
    * Method to get a user in a chat room
    *
    * @access public
    * @param string $roomId: The id of the chat room to get the user from
    * @param string $userId: The id of the user to get from the chat room
    * @return array/boolean $data: The chat room user data / FALSE on failure
    */
    public function getRoomUser($roomId, $userId)
    {
        $this->_setUserlogTable();
        $sql = "WHERE room_id = '".$roomId."'";
        $sql .= " AND user_id = '".$userId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to add a user to the list of users using a chat room
    *
    * @access public
    * @param string $roomId: The id of the chat room the user is entering
    * @param string $userId: The id of the user entering the chat room 
    * @return string $logId: The id of the user log that was added
    **/
    public function addRoomUser($roomId, $userId)
    {
        $this->_setUserlogTable();
        $this->deleteRoomUser($userId);
        
        $fields['room_id'] = $roomId;
        $fields['user_id'] = $userId;        
        $logId = $this->insert($fields);
        return $logId;
    }

    /**
    * Method for deleting a user from all chat rooms
    *
    * @access public
    * @param string $userId: The id of the user to be deleted from all chat rooms
    * @return void
    */
    public function deleteRoomUser($userId)
    {
        $this->_setUserlogTable();
        $this->delete('user_id', $userId);
    }
    
    /**
    * Method for deleting all users in a chat room
    *
    * @access public
    * @param string $roomId: The id of the chat room to delete users from
    * @return void
    */ 
    public function deleteRoomUsers($roomId)
    {
        $this->_setUserlogTable();
        $this->delete('room_id', $roomId);
    }
    
    /** 
    * Method to list the users in a chat room
    *
    * @access public
    * @param string $roomId:  The id of the chat room to list users for
    * @return array $data: The chat room user list
    */
    public function listRoomUsers($roomId)
    {
        $this->_setUserlogTable();
        $sql = "SELECT *, log.id AS logid, ban.id AS bannedid";
        $sql .= " FROM tbl_messaging_userlog AS log";
        $sql .= " INNER JOIN ".$this->tblUsers." AS users";
        $sql .= " ON log.user_id = users.userid";
        $sql .= " LEFT JOIN tbl_messaging_banned AS ban";
        $sql .= " ON log.user_id = ban.user_id";
        $sql .= " AND log.room_id = ban.room_id";
        $sql .= " WHERE log.room_id = '".$roomId."'";
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

/* ------------------ Functions for tbl_messaging_users ------------------*/

    /**
    * Method to add a user to a private chat room
    *
    * @access public
    * @param string $roomId: The id of the chat room
    * @param string $userId: The id of the user
    * @return string $roomUserId: The id of the chat room user that was added
    **/
    public function addPrivateRoomUser($roomId, $userId)
    {
        $this->_setUsersTable();
        $fields['room_id'] = $roomId;
        $fields['user_id'] = $userId;
        $fields['creator_id'] = $this->userId;
        $fields['updated'] = date('Y-m-d H:i:s');
        $roomUserId = $this->insert($fields);
        return $roomUserId;
    }

    /**
    * Method to return a list of users for a private chat room
    *
    * @access public
    * @param string $roomId: The id of the room to get the data of
    * @return array/boolean $data: The chat room user data / FALSE on failure
    **/
    public function listPrivateRoomUsers($roomId)
    {
        $this->_setUsersTable();
        $sql = " WHERE room_id = '".$roomId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to return a list of private chat rooms that the user is a member of
    *
    * @access public
    * @param string $userId: The id of the user to get the data of
    * @return array $data: The user chat roomdata
    **/
    public function listUserPrivateRooms($userId)
    {
        $this->_setUsersTable();
        $sql = " WHERE user_id = '".$userId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for deleting a private chat room user
    *
    * @access public
    * @param string $roomUserId: The id of the private chat room user to be deleted
    * @return
    */
    public function deletePrivateRoomUser($roomUserId)
    {
        $this->_setUsersTable();
        $this->delete('id', $roomUserId);
    }

    /**
    * Method for deleting all users in a private chat room
    *
    * @access public
    * @param string $roomId: The id of the private chat room to delete users from
    * @return
    */
    public function deletePrivateRoomUsers($roomId)
    {
        $this->_setUsersTable();
        $this->delete('room_id', $roomId);
    }

    /** 
    * Method to check if a user is a user of the chat room
    *
    * @access public
    * @param array $userId: The id of the user to check
    * @param array $roomId: The id of the chat room to check
    * @return array $data: The banned user data
    */
    public function getPrivateRoomUser($roomId, $userId)
    {
        $this->_setUsersTable();
        $sql = "WHERE user_id = '".$userId."'";
        $sql .= " AND room_id = '".$roomId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
/* ------------------ Functions for tbl_messaging_banned ------------------*/

    /**
    * Method to add a user to the banned user list
    *
    * @access public
    * @param array $banData: An array containing the banned user data
    * @return string $bannedId: The id of the banned user record
    **/
    public function addBannedUser($banData)
    {
        $this->_setBannedTable();
        $fields['room_id'] = $banData['room_id'];
        $fields['user_id'] = $banData['user_id'];        
        $fields['ban_reason'] = $banData['ban_reason'];        
        $fields['ban_type'] = $banData['ban_type'];
        if($banData['ban_type'] == 0){
            $fields['ban_start'] = date('Y-m-d H:i:s');        
            $fields['ban_stop'] = date('Y-m-d H:i:s', strtotime('+ '.$banData['ban_length'].' min'));        
        }        
        $bannedId = $this->insert($fields);
        return $bannedId;
    }

    /**
    * Method to edit a banned record
    *
    * @access public
    * @param string $bannedId: The id of the banned user record
    * @param array $banData: An array containing the banned user data
    * @return void
    **/
    public function editBannedUser($bannedId, $banData)
    {
        $this->_setBannedTable();
        $fields['ban_reason'] = $banData['ban_reason'];        
        $fields['ban_type'] = $banData['ban_type'];
        if($banData['ban_type'] == 0){
            $fields['ban_start'] = date('Y-m-d H:i:s');        
            $fields['ban_stop'] = date('Y-m-d H:i:s', strtotime('+ '.$banData['ban_length'].' min'));        
        }        
        $this->update('id', $bannedId, $fields);
    }

    /**
    * Method to return a banned user
    *
    * @access public
    * @param string $bannedId: The id of the banned user record
    * @return array/boolean $data: The user data / FALSE on failure
    **/
    public function getBannedUser($bannedId)
    {
        $this->_setBannedTable();
        $sql = "WHERE id = '".$bannedId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method for deleting a banned user record
    *
    * @access public
    * @param string $bannedId: The id of the banned user record to be deleted
    * @return void
    */
    public function deleteBannedUser($bannedId)
    {
        $this->_setBannedTable();
        $this->delete('id', $bannedId);
    }
    
    /**
    * Method for deleting all banned records for chat room
    *
    * @access public
    * @param string $roomId: The id of the chat room to delete records from
    * @return void
    */
    public function deleteBannedUsers($roomId)
    {
        $this->_setBannedTable();
        $this->delete('room_id', $roomId);
    }
    
    /** 
    * Method to list banned users of a chat room
    *
    * @access public
    * @param array $roomId: The id of the room to list banned users for
    * @return array/boolean $data: The banned user list / FALSE on failure
    */
    public function listBannedUsers($roomId)
    {
        $this->_setBannedTable();
        $sql = "WHERE room_id = '".$roomId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /** 
    * Method to check if a user is banned from a chat room
    *
    * @access public
    * @param array $userId: The id of the user to check
    * @param array $roomId: The id of the chat room to check
    * @return array/boolean $data: The banned user data / FALSE on failure
    */
    public function isBanned($userId, $roomId)
    {
        $this->_setBannedTable();
        $sql = "WHERE user_id = '".$userId."'";
        $sql .= " AND room_id = '".$roomId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }

/* ------------------ Functions for tbl_messaging_settings ------------------*/

    /**
    * Method to set the users instant messaging settings
    *
    * @access public
    * @param string $delivery: The IM delivery type
    * @param integer $display: An indicator to show how the name should be displayed
    * @param string $interval: The IM delivery interval if applicable
    * @return string $settingId: The id of the settings record
    */
    public function saveUserSettings($delivery, $display, $interval = NULL)
    {
        $this->_setSettingsTable();
        $this->deleteUserSettings($this->userId);
        $date = date('Y-m-d H:i:s');
        
        $fields = array();
        $fields['user_id'] = $this->userId;
        $fields['delivery_type'] = $delivery;
        if($delivery == 2){
            $fields['time_interval'] = $interval;
        }else{
            $fields['time_interval'] = 0;            
        }
        $fields['name_display'] = $display;
        $fields['updated'] = $date;
        $settingId = $this->insert($fields);
        return $settingId;
    }
    
    /**
    * Method to delete the users IM settings
    *
    * @access private
    * @param string $userId: The id of the user whose settings are to deleted
    * @return void
    */
    private function deleteUserSettings($userId)
    {
        $this->_setSettingsTable();
        $this->delete('user_id', $userId);
        
    }
    
    /**
    * Method to get the users IM settings
    * 
    * @access public
    * @return array/boolean $data: The users settings data / FALSE on failure
    */
    public function getUserSettings()
    {
        $this->_setSettingsTable();
        $sql = "WHERE user_id = '".$this->userId."'";
        $data = $this->getAll($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;        
    }
    
/* ------------------ Functions for tbl_users ------------------*/

    /** 
    * Method to search for users
    *
    * @access public
    * @param string $field: The field to search - firstname|surname
    * @param string $value: The field value to search for
    * @return array $array: The chat room user list
    */
    public function searchUsers($field, $value)
    {
        $this->_setUsersTable();
        $sql = " SELECT * FROM ".$this->tblUsers;
        $sql .= " WHERE ".$field." LIKE '".$value."%'";
        $data = $this->getArray($sql);
        if(!empty($data)){
            if(count($data) > 20){
                $array = array_slice($data, 0, 20);   
            }else{
                $array = $data;
            }
            return $array;
        }
        return array();
    }
    
/* ------------------ Functions for stausbar ------------------*/

    /**
     *
     * Method to get the number of instant messages for the user
     * 
     * @access public
     * @return array $messages The unread Instant messages the user has 
     */
    public function getInstantMessages()
    {
        $this->_setMessagesTable();
        $sql = "SELECT *, m.id as mid FROM tbl_messaging_messages as m";
        $sql .= " LEFT JOIN tbl_users as u";
        $sql .= " ON m.sender_id = u.userid";
        $sql .= " WHERE recipient_id = '$this->userId'";
        $sql .= " AND message_type = 1";
        $sql .= " AND delivery_state < 1";
        $sql .= " ORDER BY message_counter";
        $messages = $this->getArray($sql);
        
        return $messages;
    }
    
    /**
     *
     * Method to update the message as read
     * 
     * @access public
     * @param string $id The id of the message to update
     * @return VOID 
     */
    public function updateMessage($id)
    {
        $this->_setMessagesTable();
        return $this->update('id', $id, array('delivery_state' => 1));
    }
}
?>
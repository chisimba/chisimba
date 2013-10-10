<?php
/* -------------------- messaging extends controller ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* Module class to create and reply to online surveys
* @copyright (c) 2004 KEWL.NextGen
* @version 1.0
* @package survey
* @author Kevin Cyster
*
* $Id: controller.php
*/

class messaging extends controller
{
    /**
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * @var string $userId: The userId of the current user
    * @access public
    */
    public $userId;

    /**
    * @var string $name: The full name of the current user
    * @access public
    */
    public $name;

    /**
    * @var boolean $isAdmin: TRUE if the user is in the site admin group, FALSE if not
    * @access public
    */
    public $isAdmin;

    /**
    * @var object $objContext: The dbcontext class in the context module
    * @access public
    */
    public $objContext;

    /**
    * @var string $contextCode: The context code if the user is in a context
    * @access public
    */
    public $contextCode;

    //TODO: Once workgroups has been ported
    /**
    * @var object $objWorkgroup: The dbworkgroup class in the workgroup module
    * @access public
    */
//    public $objWorkgroup;

    /**
    * @var object $objDisplay: The display class in the messaging module
    * @access protected
    */
    protected $objDisplay;

    /**
    * @var object $dbMessaging: The dbmessaging database class in the messaging module
    * @access private
    */
    private $dbMessaging;

    /**
    * @var object $objDatetime: The datetime class in the utilities module
    * @access private
    */
    private $objDatetime;

    /**
    * @var object $objLanguage: The language class in the language module
    * @access private
    */
    private $objLanguage;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // system objects
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objDatetime = $this->getObject('dateandtime', 'utilities');
        $this->objLanguage = $this->getObject('language', 'language');
        //TODO: Once workgroups has been ported
        //$this->objWorkgroup = $this->getObject('dbworkgroup', 'workgroup');

        // system variables
        $this->userId = $this->objUser->userId();
        $this->name = $this->objUser->fullname($this->userId);
        $this->isAdmin = $this->objUser->inAdminGroup($this->userId);
        $this->contextCode = $this->objContext->getContextCode();
        
        // messaging objects
        $this->objDisplay = $this->getObject('chatdisplay', 'messaging');
        $this->dbMessaging = $this->getObject('dbmessaging', 'messaging');
    }

    /**
    * This is the main method of the class
    * It calls other functions depending on the value of $action
    *
    * @access public
    * @param string $action
    * @return
    */
    public function dispatch($action)
    {
        // Now the main switch statement to pass values for $action
        switch($action){
            // display a list of chat rooms
            case 'roomlist':
                $templateContent = $this->objDisplay->tplRoomList();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'normal');
                return 'template_tpl.php';
                break;
                
            // create a chat room
            case 'addroom':
                $templateContent = $this->objDisplay->tplAddRoom();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'normal');
                return 'template_tpl.php';
                break;
                
            // edit a chat room
            case 'editroom':
                $roomId = $this->getParam('roomId');
                $templateContent = $this->objDisplay->tplAddRoom('edit', $roomId);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'normal');
                return 'template_tpl.php';
                break;
                
            // submit chat room data
            case 'submitroom':
                $button = $this->getParam('button');
                if($button == 'cancel'){
                    return $this->nextAction('');
                }else{
                    $mode = $this->getParam('mode');
                    $roomName = $this->getParam('room_name');
                    $roomDesc = $this->getParam('room_desc');
                    $textOnly = $this->getParam('text_only');
                    $disabled = $this->getParam('disabled');
                    if($mode == 'add'){
                        $roomType = $this->getParam('room_type');
                        if($roomType == 1){
                            $ownerId = $this->userId;;
                        }elseif($roomType == 2){
                            $ownerId = $this->objContext->getContextCode();
                        }else{
                            // TODO: Once workgroups has been ported
                            //$ownerId = $this->objWorkgroup->getWorkgroupId();                            
                        }
                        $roomData = array(
                            'room_type' => $roomType,
                            'room_name' => $roomName,
                            'room_desc' => $roomDesc,
                            'text_only' => $textOnly,
                            'disabled' => $disabled,
                            'owner_id' => $ownerId,
                        );
                        $roomId = $this->dbMessaging->addChatRoom($roomData);
                        if($roomType == 1){
                            $chatUserId = $this->dbMessaging->addPrivateRoomUser($roomId, $this->userId);
                        }
                    }else{
                        $roomId = $this->getParam('roomId');
                        $roomData = array(
                            'room_name' => $roomName,
                            'room_desc' => $roomDesc,
                            'text_only' => $textOnly,
                            'disabled' => $disabled,
                        );
                        $this->dbMessaging->editChatRoom($roomId, $roomData);
                    }
                }
                return $this->nextAction('');
                break;
                
            // delete a chat room
            case 'deleteroom':
                $roomId = $this->getParam('roomId');
                $this->dbMessaging->deleteChatRoom($roomId);
                $this->dbMessaging->deletePrivateRoomUsers($roomId);
                $this->dbMessaging->deleteRoomUsers($roomId);
                $this->dbMessaging->deleteBannedUsers($roomId);
                return $this->nextAction('');
                break;
                
            // display the chat room description if the description is truncated
            case 'readmore':
                $roomId = $this->getParam('room_id');
                $templateContent = $this->objDisplay->popReadMore($roomId);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // display the chat room page when entering the chat room
            case 'enterroom':
                $roomId = $this->getParam('roomId');
                $textOnly = $this->getParam('textOnly');
                $this->setSession('chat_room_id', $roomId);
                $isModerator = $this->dbMessaging->isModerator();
                $this->setSession('is_moderator', $isModerator);
                $this->dbMessaging->addRoomUser($roomId, $this->userId);
                $counter = $this->dbMessaging->getMessageCount($roomId);
                //$this->setSession('message_counter', $counter);
                $array = array(
                    'name' => $this->name,
                );
                $message = $this->objLanguage->code2Txt('mod_messaging_userenter', 'messaging', $array);
                $this->dbMessaging->addChatMessage($message, TRUE);
                $templateContent = $this->objDisplay->tplChatRoom($counter);
                $this->setVarByRef('templateContent', $templateContent);
                if($textOnly != 1){
                    $this->setVar('mode', 'room');
                }else{
                    $this->setVar('mode', 'textroom');
                }
                return 'template_tpl.php';
                break;
                
            // exit the chat room page and display the chat room list
            case 'leaveroom':
                $roomId = $this->getSession('chat_room_id');
                $roomData = $this->dbMessaging->getChatRoom($roomId);
                $array = array(
                    'name' => $this->name,
                );
                $message = $this->objLanguage->code2Txt('mod_messaging_userexit', 'messaging', $array);
                $this->dbMessaging->addChatMessage($message, TRUE);
                $this->unsetSession('chat_room_id');
                $this->unsetSession('is_moderator');
                $this->unsetSession('message_counter');
                return $this->nextAction('');
                break;
                
            // display a list of users in the chat room
            case 'getusers':
                return $this->objDisplay->divGetOnlineUsers();
                break;
                
            // display the popup page to ban users
            case 'banpopup':
                $name = $this->getParam('name');
                $userId = $this->getParam('userId');
                $bannedId = $this->getParam('bannedId');
                $templateContent = $this->objDisplay->popBan($name, $userId, $bannedId);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // submit the banned user data
            case 'banuser':
                $roomId = $this->getSession('chat_room_id');
                $name = $this->getParam('name');
                $userId = $this->getParam('userId');
                $reason = $this->getParam('reason');
                $banType = $this->getParam('type');
                $banLength = $this->getParam('length');
                $bannedId = $this->getParam('bannedId');
                $banData = array(
                    'room_id' => $roomId,
                    'user_id' => $userId,
                    'ban_reason' => $reason,
                    'ban_type' => $banType,
                    'ban_length' => $banLength,
                );
                if($bannedId == NULL){
                    $this->dbMessaging->addBannedUser($banData);
                }else{
                    $this->dbMessaging->editBannedUser($bannedId, $banData);
                }
                if($banType == 2){
                    $array = array(
                        'mod' => $this->name,
                        'user' => $name,                    
                    );
                    $message = $this->objLanguage->code2Txt('mod_messaging_warnmsg', 'messaging', $array);
                    $message .= "\n".'<b>'.$this->objLanguage->languageText('mod_messaging_reason', 'messaging').':</b>';
                    $message .= "\n".$reason;
                }elseif($banType == 1){
                    $array = array(
                        'mod' => $this->name,
                        'user' => $name,                    
                    );
                    $message = $this->objLanguage->code2Txt('mod_messaging_banindefmsg', 'messaging', $array);
                    $message .= "\n".'<b>'.$this->objLanguage->languageText('mod_messaging_reason', 'messaging').':</b>';
                    $message .= "\n".$reason;
                }else{
                    $array = array(
                        'mod' => $this->name,
                        'user' => $name,
                        'time' => $banLength,                    
                    );
                    $message = $this->objLanguage->code2Txt('mod_messaging_bantempmsg', 'messaging', $array);                    
                    $message .= "\n".'<b>'.$this->objLanguage->languageText('mod_messaging_reason', 'messaging').':</b>';
                    $message .= "\n".$reason;
                }
                $this->dbMessaging->addChatMessage($message, TRUE);
                return $this->nextAction('confirmban', array(
                    'banType' => $banType,
                    'name' => $name,
                ));
                break;
                
            // display the popup page to confirm the banning of a user
            case 'confirmban':
                $name = $this->getParam('name');
                $banType = $this->getParam('banType');
                $templateContent = $this->objDisplay->popConfirmBan($banType, $name);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // display a message to the user if banned
            case 'getbanmsg':
                $type = $this->getParam('type');
                $date = $this->getParam('date');
                return $this->objDisplay->divBanMsg($type, $date);
                break;

            // display the popup page to unban users
            case 'unbanpopup':
                $name = $this->getParam('name');
                $bannedId = $this->getParam('bannedId');
                $templateContent = $this->objDisplay->popUnban($name, $bannedId);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // submit the unbanned user data
            case 'unbanusers':
                $bannedId = $this->getParam('bannedId');
                $name = $this->getParam('name');
                $this->dbMessaging->deleteBannedUser($bannedId);
                $array = array(
                    'mod' => $this->name,
                    'user' => $name,
                );
                $message = $this->objLanguage->code2Txt('mod_messaging_unbanindefmsg', 'messaging', $array);
                $this->dbMessaging->addChatMessage($message, TRUE);
                return $this->nextAction('confirmunban', array(
                    'name' => $name,
                ));
                break;

            // display the popup page to confirm the unbanning of a user
            case 'confirmunban':
                $name = $this->getParam('name');
                $templateContent = $this->objDisplay->popConfirmUnban($name);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // display the popup page to show more smiley icons
            case 'moresmileys':
                $templateContent = $this->objDisplay->popSmileys();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // submit the chat message data
            case 'chatform':
                $templateContent = $this->objDisplay->tplChatForm();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'iframe');
                return 'template_tpl.php';
                break;
                
            // submit the chat message data
            case 'sendchat':            
                $message = strip_tags($this->getParam('msg'), '<):-)>');
                // check to ensure closing tags are in place
                $codes = array(
                    '[B]' => '[/B]',
                    '[I]' => '[/I]',
                    '[U]' => '[/U]',
                    '[RED]' => '[/RED]',
                    '[BLUE]' => '[/BLUE]',
                    '[YELLOW]' => '[/YELLOW]',
                    '[GREEN]' => '[/GREEN]',
                    '[ORANGE]' => '[/ORANGE]',
                    '[PURPLE]' => '[/PURPLE]',
                    '[PINK]' => '[/PINK]',
                    '[S1]' => '[/S1]',
                    '[S2]' => '[/S2]',
                    '[S3]' => '[/S3]',
                    '[S4]' => '[/S4]',
                );
                foreach($codes as $open => $close){
                    $cntOpen = substr_count(strtoupper($message), strtoupper($open));
                    $cntClose = substr_count(strtoupper($message), strtoupper($close));
                    if($cntOpen > $cntClose){
                        for($i = $cntClose; $i < $cntOpen; $i++){
                            $message .= $close;
                        }
                    }
                }
                if($message != NULL){
                    $messageId = $this->dbMessaging->addChatMessage($message);
                }
                return $this->nextAction('chatform');
                break;
                
            // get the chat messages posted to a chat room
            case 'getchat':
                $counter = $this->getParam('counter');
                $mode = $this->getParam('mode');
                return $this->objDisplay->divGetChat($counter, $mode);
                break;
                
            // display the popup page to invite users to your chat room
            case 'invitepopup':
                $templateContent = $this->objDisplay->popInvite();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                                                                
            // display a list of users meeting the name criteria
            case 'invitelist':
                $option = $this->getParam('option');
                $value = $this->getparam('username');
                return $this->objDisplay->divGetInviteUsers($option, $value);
                break;
                
            // submit the invited user data
            case 'inviteuser':
                $roomId = $this->getSession('chat_room_id');
                $userId = $this->getParam('userId');
                $this->dbMessaging->addPrivateRoomUser($roomId, $userId);
                // TODO: send instant msg to user notifying him of the invitation 
                return $this->nextAction('confirminvite', array(
                    'userId' => $userId,
                ));
                break;
            
            case 'confirminvite':
                $userId = $this->getParam('userId');
                $templateContent = $this->objDisplay->popConfirmInvite($userId);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // display the popup page to remove users
            case 'removepopup':
                $templateContent = $this->objDisplay->popRemove();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // submit the removed user data
            case 'removeusers':
                $roomId = $this->getSession('chat_room_id');
                $userList = $this->getParam('userId');
                foreach($userList as $userId){
                    $roomUser = $this->dbMessaging->getPrivateRoomUser($roomId, $userId);
                    $roomUserId = $roomUser['id'];
                    $this->dbMessaging->deletePrivateRoomUser($roomUserId);
                }
                $users = implode('|', $userList);
                return $this->nextAction('confirmremove', array(
                    'users' => $users,
                ));
                break;

            // display the popup page to confirm the removal of a user
            case 'confirmremove':
                $users = $this->getParam('users');
                $templateContent = $this->objDisplay->popConfirmRemove($users);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // display the popup page for log functionality
            case 'logs':
                $templateContent = $this->objDisplay->popLogs();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // display the popup page for log functionality
            case 'getlog':
                $type = $this->getParam('type');
                $start = $this->getParam('start');
                $end = $this->getParam('end');
                $templateContent = $this->objDisplay->popChatLog($type, $start, $end);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('pageSuppressXML', TRUE);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // Save the chat log to a file
            case 'savelog':
                $type = $this->getParam('type');
                $start = $this->getParam('start');
                $end = $this->getParam('end');
                $mode = $this->getParam('mode');
                $templateContent = $this->objDisplay->popChatLog($type, $start, $end, $mode);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setPageTemplate('savelog_page_tpl.php');
                return 'savelog_tpl.php';
                break;
                
            // get im settings
            case 'getimsettings':
                return $this->objDisplay->divGetImSettings();
                break;
                
            case 'checkforim':
                return $this->objDisplay->divCheckIm();
                break;
                
            // Instant messaging
            case 'im':
                $templateContent = $this->objDisplay->popIM();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // get users for im
            case 'getimusers':
                $option = $this->getParam('option');
                $value = $this->getparam('value');
                return $this->objDisplay->divGetImUsers($option, $value);
                break;
                
            // send im
            case 'sendim':
                $userId = $this->getParam('userId');
                $message = $this->getParam('message');
                $imMsgId = $this->dbMessaging->addImMessage($userId, $message);
                return $this->nextAction('confirmim', array(
                    'userId' => $userId,
                ));
                break;
                
            // confirm im
            case 'confirmim':
                $userId = $this->getParam('userId');
                $templateContent = $this->objDisplay->popConfirmIm($userId);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // get im 
            case 'getim':
                return $this->objDisplay->divGetIM();
                break;
                
            // display instant message in a popup
            case 'displayim':
                $templateContent = $this->objDisplay->popDisplayIM();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;               
            
            // Instant messaging
            case 'imsettings':
                $templateContent = $this->objDisplay->popImSettings();
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                
            // submit the settings
            case 'submitsettings':
                $display = $this->getParam('display');
                $delivery = $this->getParam('delivery');
                $interval = $this->getParam('interval');
                $this->dbMessaging->saveUserSettings($delivery, $display, $interval);
                $templateContent = $this->objDisplay->popIM(TRUE);
                $this->setVarByRef('templateContent', $templateContent);
                $this->setVar('mode', 'popup');
                return 'template_tpl.php';
                break;
                               
            // display the chat room list as default            
            default:
                $this->unsetSession('chat_room_id');
                $this->unsetSession('is_moderator');
                $this->unsetSession('message_counter');
                $this->dbMessaging->deleteRoomUser($this->userId);
                return $this->nextAction('roomlist', array(), 'messaging');
                break;
        }
    }
}
?>
<?php

class smssender extends controller
{

    function init()
    {

        $this->objUser =& $this->getObject('user', 'security');
        $this->objUserModel =& $this->getObject('useradmin_model2', 'security');
        $this->objSmsSentMessages =& $this->getObject('db_smssentmessages');
    }
    
    function dispatch($action)
    {
        $this->setLayoutTemplate('sms_layout.php');
        switch ($action)
        {
            case 'listusers':
                return $this->giveList();
            case 'sendmessage':
                return $this->sendSMS();
            case 'smssent':
                return $this->smsSent();
            default:
                $this->setVar('mode', 'add');
                return 'send.php';
        }
        
    }

    
    function giveList()
    {
        $key = $_POST['autocomplete_parameter'];
        
        //$users = $this->objUserModel->getUsers($key, 'firstname', 'firstname, surname');
        $sql = 'WHERE ((firstname LIKE \''.$key.'%\') OR (surname LIKE \''.$key.'%\') OR  (cellnumber LIKE \''.$key.'%\')) AND cellnumber != \'\' ORDER BY firstname, surname, cellnumber';
        
        $users = $this->objUserModel->getAll($sql);
        
        if (count($users) == 0) {
            echo '';
        } else {
            echo '<ul>';
            foreach ($users as $user)
            {
                echo '<li><span class="informal">'.$user['firstname'].' '.$user['surname'].'</span> '.$user['cellnumber'].'</li>';
            }
            echo '</ul>';
        }
    }
    
    /**
    * Method to Send the SMS
    */
    function sendSMS()
    {

        

        $number = $this->getParam('autocomplete_parameter');
        $message = $this->getParam('message1');
        
        if ($number == '' OR $message == '') {
            $this->setVar('mode', 'fixup');
            $this->setVar('errorMessage',  'Number and Message Needs to be filled in');
            return 'send.php';
        }
        
        $mysms = $this->getObject('clickatellsms');
        $cellnumber = $this->getObject('cellnumber');

        $number = $cellnumber->fixnumber($number);
        $result =  $mysms->send ($number, $this->objUser->fullName(), $message);

        $pieces = explode("*", $result);
        
        if ($pieces[0] == 'OK')  {
            return $this->nextAction('smssent', array('message'=>$result));
        } else {
            $this->setVar('mode', 'fixup');
            $this->setVar('errorMessage',  $pieces[1]);
            return 'send.php';
        }
    }
    
    function smsSent()
    {
        $message = $this->getParam('message');
        $parts = explode('*', $message);
        
        if (count($parts) != 3) {
            return $this->nextAction(NULL, array('error'=>'norecordsentemail'));
        }
        
        $sms = $this->objSmsSentMessages->getSentSms($parts[2]);
        
        if ($sms == FALSE) {
            return $this->nextAction(NULL, array('error'=>'norecordsentemail'));
        }
        
        $this->setVarByRef('sms', $sms);
        
        return 'viewsentsms.php';
    }
    
    
}
?>

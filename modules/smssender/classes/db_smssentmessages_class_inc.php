<?php

/**
* Class to Record all SMS Message Sent
*
* This class integrates with the clickatell sms class and logs all messages sent,
* including successes and failures
*
* @author Tohir Solomons
*/
class db_smssentmessages extends dbTable
{
    /**
    * Class constructor
    * @access public
    */
    public function init ()
    {
        parent::init('tbl_smssender_sentmessages');
        $this->objUser =& $this->getObject('user', 'security');
    }
    
    /**
    * Method to record a message sent
    *
    * @param string $recipientnum Number of the Recipient
    * @param string $message Message sent to Recipient
    * @param char $result Result of the attempt to send a message, either Y or N
    * @param string $messageid Message Id of the sent Message if sucessful
    * @param string $recipientId User Id of the Recipie
    */
    public function addSentMessage($recipientnum, $message, $result, $messageid='', $recipientId='')
    {
        if (strtolower($result) == 'y') {
            $result = 'Y';
        } else {
            $result = 'N';
        }
        
        return $this->insert(array(
            'sender' => $this->objUser->userId(),
            'recipientnumber' => $recipientnum,
            'recipient' => $recipientId,
            'message' => $message,
            'messageid' => $messageid,
            'result' => $result,
            'datesent' => strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }
    
    /**
    * Method to get the details of a sent sms
    * @param string $id Record Id of the Send SMS
    * @return array
    */
    public function getSentSms($id)
    {
        return $this->getRow('id', $id);
    }
}


?>
<?php
ini_set("error_reporting", "E_ALL & ~E_NOTICE");
/* ----------- controller class extends controller for mirrordb------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* Controller class for maillist
* @author Paul Scott
* @version $Id: controller.php 6777 2007-07-06 13:21:50Z pkuti $
* @copyright GNU/GPL 2005 UWC
*
*/
class maillist extends controller
{

//declare properties 
public $objLanguage;
public $objConfig;
public $objUser;

    /**
    * @var string $action The action parameter from the querystring
    */
    public $action;

    /**
    * Standard constructor method
    * @param void
    * @return void
    */
    public function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //The user object
        $this->objUser =  & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        //get the db table derived class for this module
        $this->objDb = &$this->getObject("dbmaillist");
        //the good 'ol config object
        $this->objConfig = &$this->getObject("config","config");
        //get the mail fetcher class
        $this->objMail = &$this->getObject("mail");
        //Object used to send off mail with attachments
        $this->mailerThing = $this->getObject('kngemail','utilities');
        $this->objGroup =& $this->getObject('groupadminmodel', 'groupadmin');

    }

    /**
    * Standard dispatch method
    * @param void
    * @return void
    */
    public function dispatch($action=null)
    {
        switch ($this->$action) {
            //null case performs a validity check, if it passes it goes to getmail
            //case null:
            case null:
                //check that the config vars exist, if not create them.
                $pname_server = 'mailserver';
                $pname_user = 'mailusername';
                $pname_password = 'mailpassword';
                $pname_list = 'maillistname'; //kinkylist
                $pname_listemail = 'maillistemail'; //list email addy for use

                $pmodule = 'maillist';

                $setupOK = array('server' => $this->objConfig->checkIfSet($pname_server, $pmodule),
                'user' => $this->objConfig->checkIfSet($pname_user, $pmodule),
                'password' => $this->objConfig->checkIfSet($pname_password, $pmodule),
                'listname' => $this->objConfig->checkIfSet($pname_list, $pmodule),
                'listemail' => $this->objConfig->checkIfSet($pname_listemail, $pmodule));

                if(empty($setupOK['server']) || empty($setupOK['user']) ||
                empty($setupOK['password']) || empty($setupOK['listname'])
                || empty($setupOK['listemail']))
                {
                    $id = $this->objUser->PKId();
                    $hmm = $this->objUser->isInAdminGrp($id);

                    if($hmm != TRUE)
                    {
                        return "baduser_tpl.php";
                    }
                    else {
                        $this->nextAction("setupform");
                    }
                }
                else {

                    //$this->nextAction('cron');
                    //check that the basepath variable can write to a directory
                    if(!file_exists($this->objConfig->contentBasePath() . '/mail'))
                    {
                        //create the directory
                        mkdir($this->objConfig->contentBasePath() . '/mail',0777);
                    }
                    //set up the mailer object with the params needed...
                    $this->objMail->setup($this->objConfig->getValue('mailserver', "maillist"),
                    $this->objConfig->getValue('mailpassword', "maillist"),
                    $this->objConfig->getValue('mailusername', "maillist"),
                    $this->objConfig->contentBasePath() . '/mail/');

                    //check for messages
                    $msgs = $this->objMail->connectMail(true); //true=delete the messages from server

                    //If there are no messages
                    if($msgs == 0)
                    {
                        //we have to set the number of messages to NULL so that the last mail is NOT
                        //returned
                        $num_msgs = NULL;
                    }
                    //OK there are messages, so we carry on with the number of messages
                    else {
                        $num_msgs = array('msgcount' => count($msgs));
                    }
                    //get the decoded mail
                    $decodedMail = $this->objMail->decodeMessages($msgs);

                    //get the message count from the previous action
                    $messcount = $num_msgs['msgcount']; //$this->getParam('msgcount');
                    //if the message count is NULL
                    echo $messcount;
                    if($messcount == NULL)
                    {
                        //set the count to zero for the db action
                        //we need to do this so that the PEAR::DB layer doesn't crap out.
                        $messcount = 0;
                        //return the message to the log/template
                        echo "No New messages";
                    }

                    //OK we have the message count from this drop,
                    //so lets select the last <count> messages from the db
                    //and pass them on to the subscribers
                    $messages = $this->objDb->getRecords($messcount);

                    //loop through the message array
                    foreach($messages as $m)
                    {
                        //original file -> $m
                        $file = $m['fileid'];
                        $body = $m['body'];
                        $subject = $m['subject'];
                        $sender = $m['sender'];

                        //drop off the timestamp that makes the file unique
                        //we do so that the files with same
                        //names don't overwrite each other
                        $file2 = preg_split('/_/',$file);
                        $filerenamed = $file2[0];
                        echo "Sending messages $messcount Messages";
                        //copy the file to a renamed filename
                        @copy($this->objConfig->contentBasePath().'mail/'.$file,
                        $this->objConfig->contentBasePath().'mail/'.$filerenamed);

                        //get the senders list subscription
                        // from the message mail addy, then we need to get the other subs
                        //from the subscribers table and collate with the users table
                        $send = explode(' ',$sender);
                        $sender = $send[0] . " " . $send[1];
                        $addy = $send[2];
                        $addy = str_replace('<','',$addy);
                        $addy = str_replace('>','',$addy);
                        $theId = $this->objDb->getID($addy);

                        //ok now lets get the email from the userID
                        //This is a heavy spam filter! Only nextgen users can use it
                        $subs = $this->objDb->getListName($theId, $addy);
                        foreach ($subs['subs'] as $subscriber)
                        {
                            //yet another check and get the mail addys
                            $uid = $subscriber['userId'];
                            $address = $this->objDb->getMailAddys($uid);
                            $list = $subscriber['list'];
                            //build the message now
                            $this->email($address,$filerenamed,$body,$subject,$sender,$list,$uid,$sender);
                        }

                        //delete the renamed file, coz it sent now
                        @unlink($this->objConfig->contentBasePath().'mail/'.$filerenamed);
                    }//end foreach

                    //clean up the table
                    $this->objDb->clearTable();
                    //no real need to send out a template, so we don't do one.
                    //this and the previous action should be done via cron
                }
                break;

                //setup case, should only be done on first run
            case "setupform":
                //display the form to fill out the details...
                return "setupform_tpl.php";
                break;

                //case to set the form info to the db
            case "setinfo":
                $userId = $this->objUser->userId();
                $server = $this->getParam('server');
                $username = $this->getParam('username');
                $password = $this->getParam('password');
                $listemail = $this->getParam('listemail');
                $listname = $this->getParam('listname');
                
                           

                //OK we have the params, now we set them in the Config db
                $pname_server = 'mailserver';
                $pname_user = 'mailusername';
                $pname_password = 'mailpassword';
                $pname_list = 'maillistname'; //kinkylist
                $pname_listemail = 'maillistemail'; //list email addy for use
                

                $pmodule = 'maillist';

                $ar = array(array('pname' => $pname_server, 'pvalue' => $server),
                array('pname' => $pname_user, 'pvalue' => $username),
                array('pname' => $pname_password, 'pvalue' => $password),
                array('pname' => $pname_list, 'pvalue' => $listname),
                array('pname' => $pname_listemail, 'pvalue' => $listemail));

                foreach ($ar as $line) {
                    $pname = $line['pname'];
                    $pvalue = $line['pvalue'];
                    $this->objConfig->insertParam($pname, $pmodule, $pvalue);
                }

                //add the listname as default to the subscribers table
                //and subscribe the user doing the form
                $userId = $this->objUser->userId();
                $listname = $this->objConfig->getValue('maillistname', "maillist");
                $listemail = $this->objConfig->getValue('maillistemail', "maillist");
                $insarray = array('userId' => $userId, 'list' => $listname);
                //do the insert!
                $this->objDb->setDefaultList($insarray);
                
                //Insert the listname into the list table
                $inslist=array('creatorId'=>$userId, 'mailinglist'=>$listname, 'pop3server'=>$server, 'email'=>$listemail, 'password'=>$password);
                $this->objDb->insertListDetails($inslist);

                //Carry on with business as usual :)
                //$this->nextAction('getmail');
                $this->nextAction('viewresults');

                break;
                
				//after creating a list, display success message and list email
            case "viewresults":
                $listname = $this->objConfig->getValue('maillistname', "maillist");
                $listemail = $this->objConfig->getValue('maillistemail', "maillist");
                $this->setVarByRef('listname', $listname);
                $this->setVarByRef('listemail', $listemail);
                return "view_results_tpl.php";
                break;

                //default case for unknown action
            case "default":
                //echo $this->objLanguage->languageText("phrase_actionunknown").": ".$action;
                //$this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown").": ".$action);
                break;

        }//switch
    } // dispatch

    /**
     * Method to override login requirement
     * @param void
     * @return false
     */
    public function requiresLogin()
    {
        return FALSE;
    }

    /**
     * Method to send off the mail to the subscribers
     * @param array $subs - the subscribers array
     * @param file $file the file
     * @param mixed $body - the message body
     * @param mixed $subject - the message subject
     * @param mixed $sender - the message originator
     * @return void
     */
    public function email($subs,$file,$body,$subject,$sender,$list,$uid)
    {
        $head = $this->objLanguage->languageText("mod_maillist_on") . " " . date('r') . " " . $sender . " " . $this->objLanguage->languageText("mod_maillist_wrote") . ": ";
        $head .= "<br><br>";
        $body = nl2br($body);
        $body = $head . $body;
        //get the basename of the file for the attachment description
        $filename = basename($this->objConfig->contentBasePath().'mail/'.$file);
        //the file (binary and or text data
        $actualFile = file_get_contents($this->objConfig->contentBasePath().'mail/'.$file);

        //get the subscribers real name
        $subscribername = $subs['firstName'] . ' ' . $subs['surname'];
        //get the email address
        $subsaddy = $subs['emailAddress'];
        //if the list name doesn't exist, get it from config
        if(!isset($list))
        {
            $list = $this->objConfig->getValue('maillistname', "maillist"); //'kinkylist';
        }
        $listmail = $this->objConfig->getValue('maillistemail', "maillist"); //email addy
        //set up the mailer class
        $this->mailerThing->setup($listmail,$list,'localhost');
        //this means there is no file to attach - "mail" is the directory name
        if($file[0] == 'mail')
        {
            //set the attachment and description to NULL
            $this->mailerThing->sendMail($subscribername, $subject, $subsaddy, $body, $html = FALSE, $attachment = NULL, $attachment_descrip=NULL);

        }
        else {
            //bomb off the mail with attach & descrip
            $this->mailerThing->sendMail($subscribername, $subject, $subsaddy, $body, $html = FALSE, $attachment = $actualFile, $attachment_descrip=$filename);

        }//end else
    }//end function
}//class
?>

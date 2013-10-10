<?php

/* ----------- logic class extends object for module maillist------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

//include the fun stuff
include('Net/POP3.php');
include('Mail/mimeDecode.php');

/**
 * Mail class to fetch html and plain text email from a pop3 server
 * @author Paul Scott
 * @copyright GNU/GPL UWC 2005
 * @package maillist
 */

class mail extends object
{
	/**
	 * @var string mailserv - the mail server hostname
	 */
	public $mailserv;

	/**
	 * @var string mailuser - the mail username
	 */
	public $mailuser;

	/**
	 * @var string mailpass - the mail server password
	 */
	public $mailpass;

	/**
	 * @var string basepath - the path for saving attachments to
	 */
	public $basepath;

	/**
	 * @var string spamword - spam filtering keyword
	 */
	public $spamword;

	/**
	 * Class constructor
	 * Standard init function
	 * @param void
	 * @return construction and class instantiation
	 */
	function init()
	{
		//The user object
		$this->objUser =   $this->getObject("user", "security");
        //get the db table derived class for this module
        $this->objDbMaillistMaillist = $this->getObject("dbmaillist",'maillist');

	}
	
	/**
	 * function to connect to the pop3 server Non SASL
	 * @param string $delete - default false
	 * @return array $msg - the mail message array
	 */
	public function maildecode() 
    {
        //grab the DSN from the config file
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objImap = $this->getObject('imap', 'mail');
        $this->dsn = $this->objConfig->getItem('MAILLIST_MAIL_DSN');
        try {
            //grab a list of all valid users to an array for verification later
            $valid = $this->objDbMaillist->checkValidUser();
            $valadds = array();
            //cycle through the valid email addresses and check that the mail is from a real user
            foreach($valid as $addys) {
                $valadds[] = array(
                    'address' => $addys['emailaddress'],
                    'userid' => $addys['userid']
                );
            }
            //connect to the IMAP/POP3 server
            $this->conn = $this->objImap->factory($this->dsn);
            //grab the mail headers
            $this->objImap->getHeaders();
            //var_dump($this->objImap->getHeaders());
            //check mail
            $this->thebox = $this->objImap->checkMbox();
            //get the mail folders
            $this->folders = $this->objImap->populateFolders($this->thebox);
            //count the messages
            $this->msgCount = $this->objImap->numMails();
            //echo $this->msgCount;
            //get the meassge headers
            $i = 1;
            //parse the messages
            while ($i <= $this->msgCount) {
                //get the header info
                $headerinfo = $this->objImap->getHeaderInfo($i);
                //from
                $address = $headerinfo->fromaddress;
                //subject
                $subject = $headerinfo->subject;
                //date
                $date = $headerinfo->Date;
                //message flag
                $read = $headerinfo->Unseen;
                //message body
                $bod = $this->objImap->getMessage($i);
                //check if there is an attachment
                if (empty($bod[1])) {
                    //nope no attachments
                    $attachments = NULL;
                } else {
                    //set the attachment
                    $attachments = $bod[1];
                    //loop through the attachments and write them down
                    
                }
                //make sure the body doesn't have any nasty chars
                $message = @htmlentities($bod[0]);
                //check for a valid user
                if (!empty($address)) {
                    //check the address against tbl_users to see if its valid.
                    //just get the email addy, we dont need the name as it can be faked
                    $fadd = $address;
                    //get rid of the RFC formatted email bits
                    $parts = explode("<", $fadd);
                    $parts = explode(">", $parts[1]);
                    //raw address string that we can use to check against
                    $addy = $parts[0];
                    //check if the address we get from the msg is in the array of valid addresses
                    foreach($valadds as $user) {
                        //check if there is a match to the user list
                        if ($user['address'] != $addy) {
                            //Nope, no match, not validated!
                            $validated = NULL;
                        } else {
                            //echo "Valid user!";
                            //match found, you are a valid user dude!
                            $validated = TRUE;
                            //set the userid
                            $userid = $user['userid'];
                            //all is cool, so lets break out of this loop and carry on
                            break;
                        }
                    }
                }
                if ($validated == TRUE) {
                    //insert the mail data into an array for manipulation
                    $data[] = array(
                        'userid' => $userid,
                        'address' => $address,
                        'subject' => $subject,
                        'date' => $date,
                        'messageid' => $i,
                        'read' => $read,
                        'body' => $message,
                        'attachments' => $attachments
                    );
                }
                //delete the message as we don't need it anymore
                echo "sorting " . $this->msgCount . "messages";
                $this->objImap->delMsg($i);
                $i++;
            }
            //is the data var set?
            if (!isset($data)) {
                $data = array();
            }
            //lets look at the data now
            foreach($data as $datum) {
                $newbod = $datum['body'];
                if (!empty($datum['attachments'])) {
                    if (is_array($datum['attachments'])) {
                        foreach($datum['attachments'] as $files) {
                            //do check for multiple attachments
                            //set the filename of the attachment
                            $fname = $files['filename'];
                            $filenamearr = explode(".", $fname);
                            $ext = pathinfo($fname);
                            $filename = $filenamearr[0] . "_" . time() . "." . $ext['extension'];
                            //decode the attachment data
                            $filedata = base64_decode($files['filedata']);
                            //set the path to write down the file to
                            $path = $this->objConfig->getContentBasePath() . 'users/' . $userid . '/';
                            $fullpath = $this->objConfig->getsiteRoot() . "/usrfiles/users/" . $userid . '/';
                            //check that the data dir is there
                            if (!file_exists($path)) {
                                //dir doesn't exist so create it quickly
                                mkdir($path, 0777);
                            }
                            //fix up the filename a little
                            $filename = str_replace(" ", "_", $filename);
                            $filename = str_replace("%20", "_", $filename);
                            //change directory to the data dir
                            chdir($path);
                            //write the file
                            $handle = fopen($filename, 'wb');
                            fwrite($handle, $filedata);
                            fclose($handle);
                
                        }
                    } else {
                        //set the filename of the attachment
                        $fname = $datum['attachments'][0]['filename'];
                        $filenamearr = explode(".", $fname);
                        $ext = pathinfo($fname);
                        $filename = $filenamearr[0] . "_" . time() . "." . $ext['extension'];
                        //decode the attachment data
                        $filedata = base64_decode($datum['attachments'][0]['filedata']);
                        //set the path to write down the file to
                        $path = $this->objConfig->getContentBasePath() . 'users/' . $userid . '/';
                        //check that the data dir is there
                        //fix up the filename a little
                        $filename = str_replace(" ", "_", $filename);
                        if (!file_exists($path)) {
                            //dir doesn't exist so create it quickly
                            mkdir($path, 0777);
                        }
                        //change directory to the data dir
                        chdir($path);
                        //write the file
                        $handle = fopen($filename, 'wb');
                        fwrite($handle, $filedata);
                        fclose($handle);
                    }
                } else {
                    //no attachments to worry about
                    $newbod = $datum['body'];
                }
                //Write the new post to the database as a "Quick Post"
                $ret = array($datum['userid'], array(
                    'posttitle' => $datum['subject'],
                    'postcontent' => $newbod,
                    'postcat' => 0,
                    'postexcerpt' => '',
                    'poststatus' => '0',
                    'commentstatus' => 'Y',
                    'postmodified' => date('r') ,
                    'commentcount' => 0,
                    'postdate' => $datum['date']
                ) , 'mail');
            }
        }
        //any issues?
        catch(customException $e) {
            //clean up and die!
            customException::cleanUp();
        }
    }
}//end class
?>
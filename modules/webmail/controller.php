<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check
class webmail extends controller
{
	public $objImap;
	public $objLog;
	public $objLanguage;
	public $thebox;
	public $folders;
	public $msgCount;
	public $conn;
	public $dsn;


	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objImap = $this->getObject('imap');
			$this->objFiles = $this->getObject('upload', 'filemanager');
			$this->objConfig = $this->getObject('altconfig','config');
			//Get the activity logger class
			$this->objLog = $this->newObject('logactivity', 'logger');
			//Log this module call
			$this->objLog->log();
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}
	/**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
	public function dispatch($action = Null)
	{
		switch ($action) {
			default:
				//Mailbox DSN
				$this->dsn = NULL; //"imap://fsiu:fsiu@itsnw.uwc.ac.za:143/INBOX";
				try {
					//connect to the server
					$this->conn = $this->objImap->factory($this->dsn);
					$this->objImap->getHeaders();
					//check mail
					$this->thebox = $this->objImap->checkMbox();
					//var_dump($thebox);
					$this->folders = $this->objImap->populateFolders($this->thebox);
					$this->msgCount = $this->objImap->numMails();
					$this->setVarByRef('folders', $folders);

					//get the meassge headers
					$i = 1;
					while ($i <= $this->msgCount)
					{
						//echo $i;
						$headerinfo = @$this->objImap->getHeaderInfo($i);
						//from
						$address = @$headerinfo->fromaddress;
						//subject
						$subject = @$headerinfo->subject;
						//date
						$date = @$headerinfo->Date;
						//message flag
						$read = @$headerinfo->Unseen;

						$data[] = array('address' => $address, 'subject' => $subject, 'date' => $date, 'messageid' => $i, 'read' => $read);

						$i++;
					}
					$this->setVarByRef('data', $data);

					return "inbox_tpl.php";

				}
				catch(customException $e) {
					customException::cleanUp();
				}
			case 'getcalendar':
				//Calendar DSN
				$caldsn = "imap://fsiu:fsiu@itsnw.uwc.ac.za:143/Calendar";
				try {
					//connect to the server
					$this->objImap->factory($caldsn);
					//check mail
					$calbox = $this->objImap->checkMbox();
					//var_dump($calbox);
					$calfolders = $this->objImap->populateFolders($calbox);
					//print_r($calfolders);
					$this->setVarByRef('calfolders', $calfolders);
				}
				catch(customException $e) {
					customException::cleanUp();
				}
				break;

			case 'getmessage':
				{

					$this->setVar('pageSuppressIM', TRUE);
            		$this->setVar('pageSuppressBanner', TRUE);
            		$this->setVar('pageSuppressToolbar', TRUE);
            		$this->setVar('pageSuppressContainer', TRUE);
            		$this->setVar('suppressFooter', TRUE);
					$this->setVar('pageSuppressTrailingDiv', TRUE);

					$this->footerStr = NULL;

					$msgid = $this->getParam('msgid');
					//Mailbox DSN
					$this->dsn = "imap://fsiu:fsiu@itsnw.uwc.ac.za:143/INBOX";
					$this->conn = $this->objImap->factory($this->dsn);
					$this->objImap->getHeaders();
					$themess = $this->objImap->getMessage($msgid);
					$this->setVarByRef('message',$themess);
					//print_r($themess);
					//sort out the attachments
					if(empty($themess[1]))
					{
						$attachments = NULL;
					}
					else {
						$attachments = $themess[1];
					}
					$this->setVarByRef('attachments', $attachments);

					$message = htmlentities($themess[0]);
					$this->setVarByRef('message', $message);

					return 'msgview_tpl.php';
					break;
				}



		}
	}
}
?>
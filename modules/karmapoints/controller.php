<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Karmapoints module
* @author Brent van Rensburg
* @copyright 2007 University of the Western Cape
* $Id: controller.php 6945 2007-08-01 09:03:14Z lilji25 $
*/
class karmapoints extends controller
{
    public $objLog;
    public $objLanguage;
    public $objDbKarma;
    
    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
          	$this->objUser = $this->getObject('user', 'security');
          	$this->objLanguage = $this->getObject('language', 'language');
          	$this->objDbKarma = $this->getObject('dbkarma');
          	//Get the activity logger class
          	$this->objLog = $this->newObject('logactivity', 'logger');
          	//Log this module call
          	$this->objLog->log();
		$this->objlistedUsers = $this->getObject('userloginhistory', 'security');
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
            case 'addpoint':
            	$userid = $this->objUser->userId();
            	$this->objDbKarma->addPoints($userid, '1');
            	break;
	    case 'send':
		$userId = $this->objUser->userId();
		$allUsers = $this->objUser->getAll("WHERE userId != '$userId'");
		$this->setVarByRef('allUsers', $allUsers);

		$contBlog = current($this->objDbKarma->getContribution($userId, 'blog'));
		$contDiscussion = current($this->objDbKarma->getContribution($userId, 'discussion'));
		$contComment = current($this->objDbKarma->getContribution($userId, 'comment'));
		$contContent = current($this->objDbKarma->getContribution($userId, 'content'));
		$contReceived = current($this->objDbKarma->getContribution($userId, 'received'));
		$contSent = current($this->objDbKarma->getContribution($userId, 'sent'));
		//get total points
		$totalPoints = $contBlog['points'] + $contDiscussion['points'] + $contComment['points'] + $contContent['points'] + $contReceived['points'] - $contSent['points'];

		$this->setVarByRef('totalPoints', $totalPoints);

		return "sendPoints_tpl.php";
		break;
	    case 'donate':
		$currentUser = $this->objUser->userId();
		$donatedPoints = $this->getParam('points');
		$donatee = $this->getParam('donatee');		

		$contBlog = current($this->objDbKarma->getContribution($currentUser, 'blog'));
		$contDiscussion = current($this->objDbKarma->getContribution($currentUser, 'discussion'));
		$contComment = current($this->objDbKarma->getContribution($currentUser, 'comment'));
		$contContent = current($this->objDbKarma->getContribution($currentUser, 'content'));
		$contReceived = current($this->objDbKarma->getContribution($currentUser, 'received'));
		$contSent = current($this->objDbKarma->getContribution($currentUser, 'sent'));
		$totalPoints = $contBlog['points'] + $contDiscussion['points'] + $contComment['points'] + $contContent['points'] + $contReceived['points'] - $contSent['points'];

		$currentPoints = $this->objDbKarma->getContribution($currentUser, 'received');

		if($donatedPoints > $totalPoints)
		{
			$notEnough = $this->objLanguage->languageText('mod_karmapoints_notEnough', 'karmapoints');
			$userId = $this->objUser->userId();
			$this->setVarByRef('notEnough', $notEnough);
			$allUsers = $this->objUser->getAll("WHERE userId != '$userId'");
			$this->setVarByRef('allUsers', $allUsers);
			return "sendPoints_tpl.php";
		}
		else
		{
			$this->objDbKarma->addPoints($donatee, 'received', $donatedPoints);
			$this->objDbKarma->addPoints($currentUser, 'sent', $donatedPoints);
			//return "main_tpl.php";
		}
	    default:
		//Create avariable for the user ID
		$currentUser = $this->objUser->userId();
		$userId = $this->getParam('theUser', $currentUser);
		$contBlog = $this->objDbKarma->getContribution($userId, 'blog');
		$this->setVarByRef('contBlog', current($contBlog));
		$contDiscussion = $this->objDbKarma->getContribution($userId, 'discussion');
		$this->setVarByRef('contDiscussion', current($contDiscussion));
		$contComment = $this->objDbKarma->getContribution($userId, 'comment');
		$this->setVarByRef('contComment', current($contComment));
		$contContent = $this->objDbKarma->getContribution($userId, 'content');
		$this->setVarByRef('contContent', current($contContent));
		$contReceived = $this->objDbKarma->getContribution($userId, 'received');
		$this->setVarByRef('contReceived', current($contReceived));
		$contSent = $this->objDbKarma->getContribution($userId, 'sent');
		$this->setVarByRef('contSent', current($contSent));
		$allUsers = $this->objDbKarma->getNames();

		$this->setVarByRef('allUsers', $allUsers);

		//Send the user ID to the template
		$this->setVarByRef('user', $userId);
            	return "main_tpl.php";
            	// show the users account
            	//echo "The person whose mind is always free from attachment, who has subdued the mind and senses, and who is free from desires, attains the supreme perfection of freedom from Karma through renunciation."; die();
            
        }
    }
}
?>
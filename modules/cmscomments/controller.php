<?php
/* ----------- controller class extends controller for tbl_quotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for cmscomments module. The cmscomment module allows
* the associating of cms style comments with any table in the database. It
* is used, for example, with the cms module.
*
* @author Prince Mbekwa,Paul Scott
* @package cmscomments
* @version $Id: controller.php 6870 2007-07-20 09:17:52Z pmbekwa $
* @copyright 2006 GNU GPL AVOIR
* @access public
* @filesource
*/
class cmscomments extends controller
{
    /**
    *
    * @var string $action The action parameter from the querystring
    *
    */
    public $action;

    /**
    *
    * @var object $objUser String to hold instance of the user object
    *
    */
    public $objUser;

    /**
    *
    * @var $objLanguage $objUser String to hold instance of the language object
    *
    */
    public $objLanguage;

    public $objComm;

    public $objDbcomm;

    /**
    *
    * Standard constructor method to retrieve the action from the
    * querystring, and instantiate the user and lanaguage objects
    *
    */
  public  function init()
    {
    	try {
    		$this->objDbcomm = $this->getObject('dbcmscomments');
    		$this->objComm = $this->getObject('commentapi');
        	//Retrieve the action parameter from the querystring
        	$this->action = $this->getParam('action', Null);
        	//Create an instance of the User object
        	$this->objUser =  & $this->getObject("user", "security");
        	//Create an instance of the language object
        	$this->objLanguage = &$this->getObject("language", "language");
    	}
    	catch (customException $e)
    	{
    		customException::cleanUp();
    		exit;
    	}
    }

    /**
    * Standard dispatch method to handle adding and saving
    * of comments
    *
    * @access public
    * @param void
    * @return void
    */
 	public  function dispatch()
    {
        $action = $this->getParam('action');
    	switch ($action) {
            case null:
            	$this->setVarByRef('errmsg', $this->objLanguage->languageText("mod_cmscomments_phrase_noaction", "cmscomments"));
            	return 'noaction_tpl.php';
            	break;
            case 'add':
            	//check if the user is logged in
				if($this->objUser->isLoggedIn() == TRUE)
				{
					$this->setVar('pageSuppressToolbar', TRUE);
                	$this->setVar('pageSuppressBanner', TRUE);
                	$this->setVar('pageSuppressIM',TRUE);
                	//Suppress footer in the page (keep it simple)
                	$this->setVar('suppressFooter', TRUE);
                	return "input_tpl.php";
                }
                else {
                	return 'notloggedin_tpl.php';
                }
                break;

            case 'addtodb':
            	//$this->requiresLogin(FALSE);
            	if(!$this->objUser->isLoggedIn())
            	{
            		$captcha = $this->getParam('request_captcha');
            	}
            	$addinfo['useremail'] = $this->getParam('email');
            	$addinfo['postuserid'] = $this->getParam('userid');
            	$addinfo['postid'] = $this->getParam('id');
            	$addinfo['table'] = $this->getParam('table');
            	$addinfo['mod'] = $this->getParam('mod');
            	$addinfo['aurl'] = $this->getParam('url');
            	$addinfo['ctype'] = $this->getParam('type');
            	$addinfo['comment'] = $this->getParam('comment');
            	$addinfo = $this->objComm->addToDb($addinfo);
            	//print_r($addinfo);
            	//check that the captcha is correct
            	if(!$this->objUser->isLoggedIn())
            	{
          			if (md5(strtoupper($captcha)) != $this->getParam('captcha') || empty($captcha))
          			{
          				//get a timeoutmessage and display it...
          				$tmsg = $this->getObject('timeoutmessage', 'htmlelements');
          				$tmsg->setMessage = $this->objLanguage->languageText("mod_cmscomments_badcaptcha", "cmscomments");
          				$msg = $tmsg->show();
          				$this->setVarByRef('msg', $msg);
          				$this->nextAction('viewsingle',array('postid' => $addinfo['postid'], 'userid' => $this->objUser->userId(), 'comment' => $addinfo['comment'], 'useremail' => $addinfo['useremail']), $addinfo['mod']);
          				exit;
          			}
          		}
            	//print_r($addinfo);die();
            	$this->objDbcomm->addComm2Db($addinfo);

            	$this->nextAction('viewsingle',array('postid' => $addinfo['postid'], 'userid' => $this->objUser->userId()), $addinfo['mod']);
            	
            case 'updatecomment':
            	$commid = $this->getParam('commid');
            	$edits = nl2br($this->getParam('newcomment'));
            	echo $this->objDbcomm->updateComment($commid, $edits);
            	break;
            	
            case 'deletecomment':
            	$commentid = $this->getParam('commentid');
            	$postid = $this->getParam('postid');
            	$module = 'cms';
            	$this->objDbcomm->deletecomment($commentid);
            	
            	$this->nextAction('viewsingle',array('postid' => $postid), $module);
            	
            default:
            	die("unknown action");
            	break;
        }
    }
    
    /**
     * Ovveride the login object in the parent class
     *
     * @param void
     * @return bool
     * @access public
     */
    public function requiresLogin()
    {
        return FALSE;
    }
}
?>
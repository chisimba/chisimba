<?php
/* ----------- blogcomments API class ------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * blogcomments public API class
 *
 * @author Paul Scott
 * @copyright AVOIR GNU/GPL
 * @access public
 * @filesource
 * @package blogcomments
 * @category chisimba
 */

class commentapi extends object
{
	/**
	 * The user object inherited from the security class
	 *
	 * @var object
	 */
	protected $objUser;

	/**
	 * The language Object inherited from the language object
	 *
	 * @var object
	 */
	protected $objLanguage;
	
	public $showfullname;

	/**
	 * Standard init function to __construct the class
	 *
	 * @param void
	 * @return void
	 * @access public
	 */
	public function init()
	{
		try {
                    $this->objLanguage = $this->getObject('language', 'language');
                    $this->objUser =  $this->getObject("user", "security");
                    $this->objDbBlog = $this->getObject("dbblog", "blog");
                    $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                    $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
                    // Load scriptaclous since we can no longer guarantee it is there
                    $scriptaculous = $this->getObject('scriptaculous', 'prototype');
                    $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}

	}

	/**
	 * Method to return a nicely formatted form to add a comment
	 *
	 * @param postid $postid
	 * @param module $module
	 * @param table $table
	 * @param whether you want the htmleditor or not $editor
	 * @param Should it be displayed in a featurebox? $featurebox
	 * @param Do we want to show the types dropdown? $showtypes
	 * @return string form
	 */
	public function commentAddForm($postid, $module, $table, $postuserid = NULL, $editor = TRUE, $featurebox = TRUE, $showtypes = TRUE, $captcha = FALSE, $comment = NULL, $useremail = NULL)
	{
		try {
			$this->loadClass('form', 'htmlelements');
			$this->loadClass('textinput', 'htmlelements');
			$this->loadClass('textarea', 'htmlelements');
			$this->loadClass('button', 'htmlelements');
			//$this->loadClass('htmlarea', 'htmlelements');
			$this->loadClass('dropdown', 'htmlelements');
			$this->loadClass('label', 'htmlelements');
			$objCaptcha = $this->getObject('captcha', 'utilities');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
		$required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'system', 'Required').'</span>';
		$cform = new form('commentadd', $this->uri(array('module' => 'cmscomments', 'action' => 'addtodb', 'table' => $table, 'mod' => $module, 'postid' => $postid, 'userid' => $postuserid)));
		$cfieldset = $this->getObject('fieldset', 'htmlelements');
		//$cfieldset->setLegend($this->objLanguage->languageText('mod_blogcomments_addcomment', 'blogcomments'));
		$ctbl = $this->newObject('htmltable', 'htmlelements');
		$ctbl->cellpadding = 5;

		//start the inputs
		//textinput for author url
		$url = new textinput('url');
		$urllabel = new label($this->objLanguage->languageText("mod_cmscomments_url", "cmscomments") . ':', 'comm_input_url');
		$ctbl->startRow();
		$ctbl->addCell($urllabel->show());
		$ctbl->endRow();
		$ctbl->startRow();
		$ctbl->addCell($url->show());
		$ctbl->endRow();

		//textinput for author email
		$email = new textinput('email');
		if($this->objUser->isLoggedIn())
		{
			$email->setValue($this->objUser->email());
		}
		elseif(isset($useremail))
		{
			$email->setValue($useremail);
		}
		$emaillabel = new label($this->objLanguage->languageText("mod_cmscomments_email", "cmscomments") . ':', 'input_email');
		$ctbl->startRow();
		$ctbl->addCell($emaillabel->show().$required);
		$ctbl->endRow();
		$ctbl->startRow();
		$ctbl->addCell($email->show());
		$ctbl->endRow();

		//textarea for the comment
		$commlabel = new label($this->objLanguage->languageText('mod_cmscomments_Comment', 'cmscomments') .':', 'input_comminput');
		$ctbl->startRow();
		$ctbl->addCell($commlabel->show());
		$ctbl->endRow();
		$ctbl->startRow();
		if($editor == TRUE)
		{
			//echo "start";
			$comm = $this->getObject('htmlarea','htmlelements');
			$comm->setName('comment');
			if(isset($comment))
			{
				$comm->setContent($comment);
			}
			$comm->height = 400;
			$comm->width = 420;
			$comm->setBasicToolBar();
			$ctbl->addCell($comm->showFCKEditor());
		}
		else {
			$comm = new textarea;
			$comm->setName('comment');
			if(isset($comment))
			{
				$comm->setValue($comment);
			}
			$ctbl->addCell($comm->show());
		}
		$ctbl->endRow();
		//comment type dropdown
		if($showtypes == TRUE)
		{
			$objCat = $this->getObject('dbcommenttype', 'commenttypeadmin');
        	$tar = $objCat->getAll();
			$ddlabel = new label($this->objLanguage->languageText('mod_cmscomments_commenttype', 'cmscomments') .':', 'input_comtypeinput');
        	$ctype = $this->newObject("dropdown", "htmlelements");
        	$ctype->name = 'type';
        	$ctype->SetId('input_type');
        	$ctype->addOption("", $this->objLanguage->languageText("mod_cmscomments_selecttype",'cmscomments'));
        	$ctype->addFromDB($tar, 'title', 'type');
			$ctbl->startRow();
			$ctbl->addCell($ddlabel->show());
			$ctbl->endRow();
			$ctbl->startRow();
			$ctbl->addCell($ctype->show());
			$ctbl->endRow();
		}
		$ctbl->startRow();
		if(!$this->objUser->isLoggedIn())
		{
			$captcha = new textinput('request_captcha');
			$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'input_request_captcha');
			$ctbl->addCell(stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')).'<br /><div id="captchaDiv">'.$objCaptcha->show().'</div>'.$captcha->show().$required.'  <a href="javascript:redraw();">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>');
			$ctbl->endRow();
			//$cform->addRule('comment', $this->objLanguage->languageText("mod_blogcomments_commentval",'blogcomments'), 'required');
			$cform->addRule('email', $this->objLanguage->languageText("mod_cmscomments_emailval",'cmscomments'), 'required');
			$cform->addRule('request_captcha', $this->objLanguage->languageText("mod_cmscomments_captchaval",'cmscomments'), 'required');
		}

 		//end off the form and add the buttons
		$this->objCButton = &new button($this->objLanguage->languageText('word_save', 'system'));
		$this->objCButton->setValue($this->objLanguage->languageText('word_save', 'system'));
		$this->objCButton->setToSubmit();

		$cfieldset->addContent($ctbl->show());
		$cform->addToForm($cfieldset->show());
		$cform->addToForm($this->objCButton->show());

		if($featurebox == TRUE)
		{
			$objFeaturebox = $this->getObject('featurebox', 'navigation');
			return $objFeaturebox->showContent($this->objLanguage->languageText("mod_cmscomments_formhead", "cmscomments"), $cform->show());
		}
		else {
			return $cform->show();
		}
	}

	/**
	 * Method to show the comments in the comments table to the user on a singleview post display
	 *
	 * @param string $pid
	 * @return string
	 */
	public function showComments($pid)
	{
		//get the post info
		$post = $this->grabPostInfo($pid);
		$post = $post[0];
		//print_r($post);
		$washer = $this->getObject('washout', 'utilities');
		$this->objDbComm = $this->getObject('dbcmscomments');
		$objFeatureBox = $this->newObject('featurebox', 'navigation');
		$comms = $this->objDbComm->grabComments($pid);
		//loop through the trackbacks and build a featurebox to show em
		if(empty($comms))
		{
			//shouldn't happen except on permalinks....?
			return $objFeatureBox->showComment($this->objLanguage->languageText("mod_cmscomments_comment4post", "cmscomments"), "<em>".$this->objLanguage->languageText("mod_cmscomments_nocomments", "cmscomments")."</em>");
		}

		$commtext = NULL;
		foreach($comms as $comm)
		{
			//build up the display
			$ctable = $this->newObject('htmltable', 'htmlelements');
			$ctable->cellpadding = 2;
			//$ctable->width = '80%';
			//set up the header row
			$ctable->startHeaderRow();
			$ctable->addHeaderCell('');
			$ctable->addHeaderCell('');
			$ctable->endHeaderRow();

			//build in author with url if available, with [email] as fbox head
			//then content as the content
			//where did it come from?
			$auth = $comm['comment_author'];
			$authurl = $comm['comment_author_url'];
			$authemail = $comm['comment_author_email'];
			$commentdate = $comm['comment_date'];
			$hrcdate = date('r', $commentdate);
			//do a check to see if the comment author is the viewer so that they can edit the comment inline
			//get the userid
			$viewerid = $this->objUser->userId();
			$vemail = $this->objUser->email($viewerid);
			if($post['userid'] == $this->objUser->userId())
			{
				//$this->objConfig = $this->getObject('altconfig', 'config');
				//$scripts = '<script type="text/javascript" src="core_modules/htmlelements/resources/scriptaculous/1.6.5/scriptaculous.js"></script>';
				//echo file_get_contents("/var/www/chisimba_framework/app/core_modules/htmlelements/resources/scriptaculous/1.6.5/scriptaculous.js");
                   //   <script src="core_modules/htmlelements/resources/script.aculos.us/src/scriptaculous.js" type="text/javascript"></script>
                   //   <script src="core_modules/htmlelements/resources/script.aculos.us/src/unittest.js" type="text/javascript"></script>';
        		//$this->appendArrayVar('headerParams',$scripts);
				//display the inline editor
				$updateuri = 'index.php'; //$this->uri(array('module' =>'blogcomments','action' => 'updatecomment'));
				$commid = $comm['id'];
				$commcont = $comm['comment_content'];
				$commcont = str_replace("<p>", '', $commcont);
				$commcont = str_replace('</p>', '', $commcont);
				$script = '<p id="editme2">'.stripslashes(nl2br($commcont)).'</p>';
				$script .= '<script type="text/javascript">';
				$script .= "new Ajax.InPlaceEditor('editme2', '$updateuri', {rows:15,cols:40, callback: function(form, value) { return 'module=blogcomments&action=updatecomment&commid=' + escape('$commid') + '&newcomment=' +escape(value) }});";
				$script .= "</script>";
				//var_dump($script);
				$this->objIcon = $this->getObject('geticon', 'htmlelements');
				$delIcon = $this->objIcon->getDeleteIconWithConfirm($comm['id'], array(
                    'module' => 'cmscomments',
                    'action' => 'deletecomment',
                    'commentid' => $comm['id'],
                    'postid' => $pid
                ) , 'cmscomments');
                //$delic = $delIcon->show();
                
				$fboxcontent = $script."<br /><br />".$delIcon; //stripslashes($comm['comment_content']); // . "<br /><br />" . $delIcon;
			}
			elseif($vemail == $comm['comment_author_email'])
			{
				//$scripts = '<script type="text/javascript" src="core_modules/htmlelements/resources/scriptaculous/1.6.5/scriptaculous.js"></script>';
				//$this->appendArrayVar('headerParams',$scripts);
				//$scripts = '<script src="core_modules/htmlelements/resources/script.aculos.us/lib/prototype.js" type="text/javascript"></script>
                      //<script src="core_modules/htmlelements/resources/script.aculos.us/src/scriptaculous.js" type="text/javascript"></script>
                      //<script src="core_modules/htmlelements/resources/script.aculos.us/src/unittest.js" type="text/javascript"></script>';
        		//$this->appendArrayVar('headerParams',$scripts);
				
                //display the inline editor
				$updateuri = 'index.php'; //$this->uri(array('module' =>'blogcomments','action' => 'updatecomment'));
				$commid = $comm['id'];
				$script = '<p id="editme2">'.stripslashes($comm['comment_content']).'</p>';
				$script .= '<script type="text/javascript">';
				$script .= "new Ajax.InPlaceEditor('editme2', '$updateuri', {rows:15,cols:40, callback: function(form, value) { return 'module=blogcomments&action=updatecomment&commid=' + escape('$commid') + '&newcomment=' +escape(value) }});";
				$script .= "</script>";
				
				//echo $updateuri; die();
				$this->objIcon = $this->getObject('geticon', 'htmlelements');
                $edIcon = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'updatecomment',
                    'id' => $comm['id'],
                    'module' => 'cmscomments'
                )));
                //$editic = $edIcon->show();
                $delIcon = $this->objIcon->getDeleteIconWithConfirm($comm['id'], array(
                    'module' => 'cmscomments',
                    'action' => 'deletecomment',
                    'commentid' => $comm['id'],
                    'postid' => $pid
                ) , 'cmscomments');
                //$delic = $delIcon->show();
                
				$fboxcontent = $script."<br /><br />".$delIcon; //stripslashes($comm['comment_content']); // . "<br /><br />" . $delIcon;
			}
			else {
				$fboxcontent = stripslashes($comm['comment_content']);	
			}
			//$link = new href(urlencode($authurl), $auth, NULL);
			//$link->show();

			$authemail = "[".$authemail."]";
			$authhead = $auth; // . " " . $authemail; // . " (".htmlentities($authurl).")";
			if(isset($delIcon))
			{
				$fboxhead = $authhead; // . " " . $authemail;
			}
			else {
				$fboxhead = $authhead;
			}

			

			$commtext .= $objFeatureBox->showComment($fboxhead . " - ".$hrcdate, $washer->parseText($fboxcontent));
		}
		return $commtext;
	}


	/**
	 * Adds a comment from user input to the database
	 *
	 * @param comment info array $cominfo
	 * @return bool
	 */
	public function addToDb($cominfo)
	{
		if(!isset($cominfo['useragent']))
		{
			$cominfo['useragent'] = $_SERVER['HTTP_USER_AGENT'];
		}
		if(!isset($cominfo['useremail']))
		{
			$cominfo['useremail'] = $this->objUser->email();
		}
		if(!isset($cominfo['userid']))
		{
			$cominfo['userid'] = $this->objUser->userId();
		}
		if(!isset($cominfo['commentauthor']))
		{
			if($this->showfullname == 'FALSE')
            {
            	$cominfo['commentauthor'] = $this->objUser->userName($cominfo['userid']);
            }
            else {
            	$cominfo['commentauthor'] = $this->objUser->fullname($cominfo['userid']);
            }
			//$cominfo['commentauthor'] = $this->objUser->fullname($cominfo['userid']);
		}
		if(!isset($cominfo['ip']))
		{
			$cominfo['ip'] = $_SERVER['REMOTE_ADDR'];
		}
		if(!isset($cominfo['date']))
		{
			$cominfo['date'] = time();
		}

		//print_r($cominfo);
		return $cominfo;
	}

	/**
	 * Method to return the comment count (record count) for a post
	 *
	 * @param item ID $pid
	 * @return integer
	 */
	public function getCount($pid)
	{
		$this->objDbComm = $this->getObject('dbcmscomments');
		return $this->objDbComm->commentCount($pid);
	}
	
	/**
	 * Method to grab all the post info for a post ID
	 * 
	 * @param string $pid
	 * @return array
	 */
	public function grabPostInfo($pid)
	{
		return $this->objDbBlog->getPostById($pid);
	}

}//end class
?>
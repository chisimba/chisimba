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
            $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->showfullname = $this->sysConfig->getValue('show_fullname', 'blog');
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
		$cform = new form('commentadd', $this->uri(array('module' => 'blogcomments', 'action' => 'addtodb', 'table' => $table, 'mod' => $module, 'postid' => $postid, 'userid' => $postuserid)));
		$cfieldset = $this->getObject('fieldset', 'htmlelements');
		//$cfieldset->setLegend($this->objLanguage->languageText('mod_blogcomments_addcomment', 'blogcomments'));
		$ctbl = $this->newObject('htmltable', 'htmlelements');
		$ctbl->cellpadding = 5;

		//start the inputs
		//textinput for author name
		$author = new textinput('commentauthor');
                if ($this->objUser->isLoggedIn() == TRUE) {
                    $authorName = $this->objUser->fullName($postuserid);
                } else {
                    $authorName = "";
                }
                $author->setValue($authorName);
		$authorlabel = new label($this->objLanguage->languageText("mod_blogcomments_yourname", "blogcomments") . ':', 'comm_input_name');
		$ctbl->startRow();
		$ctbl->addCell($authorlabel->show());
		$ctbl->endRow();
		$ctbl->startRow();
		$ctbl->addCell($author->show());
		$ctbl->endRow();
		
		//textinput for author url
		$url = new textinput('url');
		$url->setValue("http://");
		$urllabel = new label($this->objLanguage->languageText("mod_blogcomments_url", "blogcomments") . ':', 'comm_input_url');
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
		$emaillabel = new label($this->objLanguage->languageText("mod_blogcomments_email", "blogcomments") . ':', 'input_email');
		$ctbl->startRow();
		$ctbl->addCell($emaillabel->show().$required);
		$ctbl->endRow();
		$ctbl->startRow();
		$ctbl->addCell($email->show());
		$ctbl->endRow();

		//textarea for the comment
		$commlabel = new label($this->objLanguage->languageText('mod_blogcomments_comment', 'blogcomments') .':', 'input_comminput');
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
			$comm->width = "100%";
			$comm->setBasicToolBar();
			$ctbl->addCell($comm->show());
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
			$ddlabel = new label($this->objLanguage->languageText('mod_blogcomments_commenttype', 'blogcomments') .':', 'input_comtypeinput');
        	$ctype = $this->newObject("dropdown", "htmlelements");
        	$ctype->name = 'type';
        	$ctype->SetId('input_type');
        	$ctype->addOption("", $this->objLanguage->languageText("mod_blogcomments_selecttype",'blogcomments'));
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
			$cform->addRule('email', $this->objLanguage->languageText("mod_blogcomments_emailval",'blogcomments'), 'required');
			$cform->addRule('request_captcha', $this->objLanguage->languageText("mod_blogcomments_captchaval",'blogcomments'), 'required');
		}

 		//end off the form and add the buttons
		$this->objCButton = &new button($this->objLanguage->languageText('word_save', 'system'));
		$this->objCButton->setValue($this->objLanguage->languageText('word_save', 'system'));
                // Add the save icon
                $this->objCButton->setIconClass("save");
		$this->objCButton->setToSubmit();

		$cfieldset->addContent($ctbl->show());
		$cform->addToForm($cfieldset->show());
		$cform->addToForm($this->objCButton->show());

		if($featurebox == TRUE)
		{
			$objFeaturebox = $this->getObject('featurebox', 'navigation');
			return $objFeaturebox->showContent($this->objLanguage->languageText("mod_blogcomments_formhead", "blogcomments"), $cform->show());
		}
		else {
			return $cform->show();
		}
	}

	public function showJblogComments($postid) {
	    $washer = $this->getObject('washout', 'utilities');
        $this->objDbComm = $this->getObject('dbblogcomments');
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $comms = $this->objDbComm->grabComments($postid);
	    if(empty($comms))
        {
            //shouldn't happen except on permalinks....?
            return $objFeatureBox->showComment($this->objLanguage->languageText("mod_blogcomments_comment4post", "blogcomments"), "<em>".$this->objLanguage->languageText("mod_blogcomments_nocomments", "blogcomments")."</em>");
        }
        $this->loadClass ( 'htmlheading', 'htmlelements' );

        // Add in a heading
        $header = new htmlHeading ( );
        $header->str = $this->objLanguage->languageText("mod_blogcomments_comment4post", "blogcomments");
        $header->type = 3;

        $commtext = $header->show();
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
            $fboxcontent = stripslashes($comm['comment_content']);

            $authemail = explode('@', $authemail);
            $authemail = $authemail[0];
            $authhead = $authemail; //$auth; // . " " . $authemail; // . " (".htmlentities($authurl).")";
            if(isset($delIcon))
            {
               $fboxhead = $authhead; // . " " . $authemail;
            }
            else {
                $fboxhead = $authhead;
            }
            $commtext .= $objFeatureBox->showComment($fboxhead . " On ".$hrcdate, $washer->parseText($fboxcontent));
        }

        return $commtext;

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
		$this->objDbComm = $this->getObject('dbblogcomments');
		$objFeatureBox = $this->newObject('featurebox', 'navigation');
		$comms = $this->objDbComm->grabComments($pid);
		//loop through the trackbacks and build a featurebox to show em
		if(empty($comms))
		{
			//shouldn't happen except on permalinks....?
			return $objFeatureBox->showComment($this->objLanguage->languageText("mod_blogcomments_comment4post", "blogcomments"), "<em>".$this->objLanguage->languageText("mod_blogcomments_nocomments", "blogcomments")."</em>");
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
			/*
			$viewerid = $this->objUser->userId();
			$vemail = $this->objUser->email($viewerid);
			//var_dump($comm); var_dump($this->objUser->userId());
			if($comm['userid'] == $this->objUser->userId())
			{
				//display the inline editor
				$updateuri = 'index.php';
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
                    'module' => 'blogcomments',
                    'action' => 'deletecomment',
                    'commentid' => $comm['id'],
                    'postid' => $pid
                ) , 'blogcomments');

				$fboxcontent = $script."<br /><br />".$delIcon;
			}
			elseif($vemail == $comm['comment_author_email'])
			{
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
                    'module' => 'blogcomments'
                )));
                //$editic = $edIcon->show();
                $delIcon = $this->objIcon->getDeleteIconWithConfirm($comm['id'], array(
                    'module' => 'blogcomments',
                    'action' => 'deletecomment',
                    'commentid' => $comm['id'],
                    'postid' => $pid
                ) , 'blogcomments');
                //$delic = $delIcon->show();

				$fboxcontent = $script."<br /><br />".$delIcon; //stripslashes($comm['comment_content']); // . "<br /><br />" . $delIcon;
			} */
			/*else*/ if ($this->objUser->inAdminGroup($this->objUser->userId())) {
			    $this->objIcon = $this->getObject('geticon', 'htmlelements');
			    $delIcon = $this->objIcon->getDeleteIconWithConfirm($comm['id'], array(
                    'module' => 'blogcomments',
                    'action' => 'deletecomment',
                    'commentid' => $comm['id'],
                    'postid' => $pid
                ) , 'blogcomments');
                
                $fboxcontent = stripslashes($comm['comment_content'])."<br /><br />".$delIcon;
			}
			else {
				$fboxcontent = stripslashes($comm['comment_content']);
			}
			$authemail = "[".$authemail."]";
			$this->loadClass('href', 'htmlelements');
            $aulink = new href($authurl, $auth, 'target="_blank"');
            
            //$aulink->link = $auth;
            $aulink = $aulink->show();
			//var_dump($aulink); die();
			$authhead = $aulink; // . " " . $authemail; // . " (".htmlentities($authurl).")";
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
		$this->objDbComm = $this->getObject('dbblogcomments');
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
	    $this->objDbBlog = $this->getObject("dbblog", "blog");
		return $this->objDbBlog->getPostById($pid);
	}
	
	public function asyncComments($itemid = 'test')
	{
	    $this->loadClass('htmlheading', 'htmlelements');
	    $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
	    // heading
	    $header = new htmlheading();
        $header->type = 2;
        $header->str = $this->objLanguage->languageText("mod_blogcomments_formhead", "blogcomments");
        $header = $header->show();
        
        $rheader = new htmlheading();
        $rheader->type = 2;
        $rheader->str = $this->objLanguage->languageText("mod_blogcomments_comments", "blogcomments");
        $rheader = $rheader->show();
        
        // labels
        $nameLabel = new label($this->objLanguage->languageText('mod_blogcomments_yourname', 'blogcomments').'&nbsp;', 'input_name');
        $commentLabel = new label($this->objLanguage->languageText('mod_blogcomments_comment', 'blogcomments').'&nbsp;', 'input_comment');
        
        // button
        $button = new button ('add', $this->objLanguage->languageText("mod_blogcomments_add", "blogcomments"));
        $button->setId("add");
        $button = $button->show();
        
        // textarea
        $commBox = new textarea('comment', '', 5, 50);
        
        // text input
        $tin = new textinput('input', NULL, NULL);
        $tin->setValue($this->objUser->fullName());

        // layout
        $ctbl = $this->newObject('htmltable', 'htmlelements');
		$ctbl->cellpadding = 5;

		//start the inputs
		$ctbl->startRow();
		$ctbl->addCell($nameLabel->show());
		$ctbl->endRow();
		$ctbl->startRow();
		$ctbl->addCell($tin->show());
		$ctbl->endRow();
		$ctbl->startRow();
		$ctbl->addCell($commentLabel->show());
		$ctbl->endRow();
        $ctbl->startRow();
		$ctbl->addCell($commBox->show());
		$ctbl->endRow();
		$ctbl->startRow();
		$ctbl->addCell($button);
		$ctbl->endRow();
		$ctbl = $ctbl->show();
		if(!$this->objUser->isLoggedIn())
        {
            $nheader = new htmlheading();
            $nheader->type = 2;
            $nheader->str = "<em>".$this->objLanguage->languageText("mod_blogcomments_logintocomment", "blogcomments")."</em>";
            $nheader = $nheader->show();
            $ctbl = NULL;
            $leave = $nheader;
        }
        else 
        {
            $leave = $header."<br />".$ctbl;
        }
                
        $js = NULL;
	    $js .= $this->getJavascriptFile('1.4.2/jquery-1.4.2.min.js', 'jquery');
	    $input = '<div id="leaveComment">'.$leave.'</div>
		          <div id="comments">'.$rheader.'</div>';
		          
		$js .= '<script type="text/javascript">
		    $(function() {
				//retrieve comments to display on page
				$.getJSON("index.php?module=blogcomments&action=getcomments&itemid='.$itemid.'&jsoncallback=?", function(data) {
				    //loop through all items in the JSON array
				    for (var x = 0; x < data.length; x++) {
				        //create a container for each comment
				        var div = $("<div>").addClass("row").appendTo("#comments");
				        //add author name and comment to container
				        $("<label>").text(data[x].comment_author).appendTo(div);
				        $("<div>").addClass("comment").text(data[x].comment_content).appendTo(div);
				    }
				});
				//add click handler for button
				$("#add").click(function() {
				    //define ajax config object
				    var ajaxOpts = {
				        type: "post",
				        url: "index.php?module=blogcomments&action=savecomment&itemid='.$itemid.'",
				        data: "author=" + $("#leaveComment").find("input").val() + "&comment=" + $("#leaveComment").find("textarea").val(),
				        success: function(data) {
				            //create a container for the new comment
				            var div = $("<div>").addClass("row").appendTo("#comments");
				            //add author name and comment to container
				            $("<label>").text($("#leaveComment").find("input").val()).appendTo(div);
				            $("<div>").addClass("comment").text($("#leaveComment").find("textarea").val()).appendTo(div);
				            //empty inputs
				            //$("#leaveComment").find("input").val("");
				            $("#leaveComment").find("textarea").val("");
				        }
				    };
				    $.ajax(ajaxOpts);
				});
        });
        </script>';
		return $input.$js;
	}

}//end class
?>

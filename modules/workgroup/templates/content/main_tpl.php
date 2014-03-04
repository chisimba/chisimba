<?php

// Load Classes Needed
$this->loadClass('link', 'htmlelements');

$pageText = $this->newObject('htmlheading','htmlelements');
$pageText->align='left';

$loading = $this->getObject('geticon', 'htmlelements');
$loading->setIcon('loader');
/*
// Display the heading. 
$pageText->type=1;
$pageText->str=ucwords($objLanguage->code2Txt('word_workgroup')) . ' : ' . $workgroupDescription;    
$middleContent = $pageText->show();
*/

$middleContent = '';

// -------------------------------------------------------------------------

//Join Discussion Forum 
$pageText->type=3;
$pageText->str=ucwords($objLanguage->code2Txt("mod_workgroup_forum",'workgroup'));
$middleContent .= $pageText->show();


$workgroupPosts =& $this->getObject('dbpost', 'forum');

$middleContent .= '<div class="wrapperLightBkg" style="border: 1px dotted #c0c0c0;">'.$workgroupPosts->getWorkGroupPosts($this->workgroupId, $contextCode).'</div>';

$forumLink = new link ($this->uri(array('action'=>'workgroup'), 'forum'));
$forumLink->link = $objLanguage->languageText('mod_workgroup_enterforum','workgroup');

$middleContent .= '<h3 align="center">'.$forumLink->show().'</h3>';

// -------------------------------------------------------------------------

// -------------------------------------------------------------------------

//View Group Assignments
$pageText->type=3;
$pageText->str=ucwords($objLanguage->code2Txt("mod_assignment_groupassignments",'assignment',null,'Workgroup Assignments'));
$middleContent .= $pageText->show();

/*
$workgroupPosts =& $this->getObject('dbpost', 'forum');

$middleContent .= '<div class="wrapperLightBkg" style="border: 1px dotted #c0c0c0;">'.$workgroupPosts->getWorkGroupPosts($this->workgroupId, $contextCode).'</div>';
*/
$fromworkgroup = 1;
$assignmentLink = new link ($this->uri(array('action'=>'home', 'workgroupId' => $this->workgroupId, 'fromworkgroup'=>$fromworkgroup), 'assignment'));
$assignmentLink->link = $objLanguage->languageText('mod_assignment_viewassignment','assignment','View Assignments');

//$middleContent .= '<h3 align="center">'.$assignmentLink->show().'</h3>';
$middleContent .= '<div class="wrapperLightBkg" style="border: 1px dotted #c0c0c0;"><h3 align="center">'.$assignmentLink->show().'</h3></div>';

// -------------------------------------------------------------------------

// Join fileshare.    
$pageText->type=3;
$pageText->str=ucwords($objLanguage->code2Txt("mod_workgroup_fileshare",'workgroup'));
$middleContent .= $pageText->show();

$fileshareLink = new link($this->uri(array('action'=>'uploadDocument'),'workgroup'));
//$fileshareLink->link = ucwords($objLanguage->code2Txt('mod_workgroup_enterfileshare','workgroup'));

$fileshareUploadLink = new link($this->uri(array('action'=>'upload'), 'workgroup'));
$fileshareUploadLink->link = $objLanguage->languageText('mod_workgroup_uploaddocument','workgroup');

$middleContent .= '<h3 align="center">'.$fileshareLink->show().'</h3>';
$middleContent .= '<h3 align="center">'.$fileshareUploadLink->show().'</h3>';

$script = $this->getJavaScriptFile('workgroup.js');
$this->appendArrayVar('headerParams', $script);
$this->appendArrayVar('bodyOnLoad', "getWorkgroupFiles('".$this->workgroupId."')");
$middleContent .= '<div id="browsefiles">'.$loading->show().'</div>';
        
        
		

/*
$refLink = new link('#');
		$url = $this->uri(array(
                'action'=>'comment',
                'id' => $id
            ));
		$refLink->link = $objLanguage->languageText("phrase_addreference");
		$refLink->extra = "onclick=\"javascript:window.open('{$url}', 'refs', 'width=440, height=200, left=100,top=100,scrollbars = yes');\"";
		$linkWindow = $refLink->show();

*/
// -------------------------------------------------------------------------

// Logout
$middleContent .= "<a href=\"".
$this->uri(array('action'=>'leaveworkgroup'))
."\">".ucwords($objLanguage->code2Txt("mod_workgroup_logout",'workgroup'))."</a>"."<br/><br/>";


// ------------- START RIGHT CONTENT ------------------- //

$rightContent = NULL;

// Check if Instant Messaging is registered
$moduleCheck =& $this->getObject('modules', 'modulecatalogue');
$instantMessaging = $moduleCheck->checkIfRegistered('instantmessaging');

if (!empty($lecturers)) {
    $pageText->str=ucwords($objLanguage->code2Txt('mod_workgroup_lecturers','workgroup'));
    //$rightContent .= $pageText->show();

    $table=$this->newObject('htmltable','htmlelements');
    $table->cellspacing='1';
    $table->cellpadding='5';    
    
    $oddOrEven = "odd";
    foreach ($lecturers as $lecturer) {
        $table->startRow();    
        $oddOrEven = ($oddOrEven=="even")? "odd":"even";
        $table->addCell($lecturer['fullname'], "null", "top", "left", $oddOrEven, null);
        
        // Only show Instant Messaging if it is registered
        if ($instantMessaging) {
            // Display IM button.        
			$imPopup =& $this->getObject('popup','instantmessaging');
			$imPopup->setup($lecturer['userid'], null, $objLanguage->languageText('phrase_sendinstantmessage'));
			$option = $imPopup->show(); 
            $table->addCell($option, "null", "top", "left", $oddOrEven, null);
        }
        $table->endRow();
    }
    
    //$rightContent .= $table->show(); 
}

// Display the members of the workgroup.
if (empty($members)) {
    // If no members, output the heading
    $pageText->type=3;        
    $pageText->str=$objLanguage->languageText('mod_workgroup_members','workgroup');
    $rightContent .= $pageText->show();        
    
    // Check if user is a lecturer.
    $objContextCondition = &$this->getObject('contextcondition','contextpermissions');
    if ($objContextCondition->isContextMember('Lecturers')) {
        $href = $this->uri(array('action'=>'manage', 'workgroupid'=>$this->workgroupId),'workgroupadmin');
        $url = "<a href=\"$href\">".$objLanguage->languageText('word_here')."</a>";
        $rightContent .= $objLanguage->code2Txt('mod_workgroup_nomembers','workgroup',array('URL'=>$url));
    }
} else {    
    // else output the heading plus a link to send an instant message to the entire group
    
    // Only show Instant Messaging if it is registered
    if ($instantMessaging) {
	    // Send an instant message to all members.        
		$imPopup =& $this->getObject('popup','instantmessaging');
		$imPopup->setup($workgroupId, 'workgroup', $objLanguage->languageText('phrase_sendinstantmessagetoall'));
		$option = $imPopup->show(); 
        $pageText->str=$option.$objLanguage->languageText('mod_workgroup_members','workgroup');
    } else {
        $pageText->str=$objLanguage->languageText('mod_workgroup_members','workgroup');
    }
    $rightContent .= $pageText->show();

    
    $tblclass=$this->newObject('htmltable','htmlelements');
    $tblclass->cellspacing='1';
    $tblclass->cellpadding='5';    
    
    $oddOrEven = "odd";
    foreach ($members as $member) {
        $tblclass->startRow();    
        $oddOrEven = ($oddOrEven=="even")? "odd":"even";	
        $tblclass->addCell($member['fullname'], "null", "top", "left", $oddOrEven, null);
        
        // Only show Instant Messaging if it is registered
        if ($instantMessaging) {
            // Display IM button.        
			$imPopup =& $this->getObject('popup','instantmessaging');
			$imPopup->setup($member['userid'], null, $objLanguage->languageText('phrase_sendinstantmessage'));
			$option = $imPopup->show(); 
            $tblclass->addCell($option, "null", "top", "left", $oddOrEven, null);
        }
        $tblclass->endRow();
    }
    
    $rightContent .= $tblclass->show(); 
}

// Check if user is a lecturer.
$objContextCondition = &$this->getObject('contextcondition','contextpermissions');
if ($objContextCondition->isContextMember('Lecturers')) {
    $rep=array('author' => '','context' => '');
    $rightContent .= '<br/><br/><p><em>'.$this->objLanguage->code2txt('mod_workgroup_youarealecturer','workgroup', $rep).'</em></p>';
}


// ------------- END RIGHT CONTENT ------------------- //
$this->objBlock = $this->newObject('blocks', 'blocks');
//$chatBlock = $this->objBlock->showBlock('workgroupchat', 'messaging');

$cssLayout =& $this->getObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

/*
$menuBar=& $this->getObject('workgroupmenu');
*/
$toolbar = $this->getObject('contextsidebar','context');
$sideMenu=$toolbar->show();
$cssLayout->setLeftColumnContent($rightContent.$sideMenu);
$cssLayout->setMiddleColumnContent($middleContent);
//$cssLayout->setRightColumnContent($chatBlock);
echo $cssLayout->show(); 


?>

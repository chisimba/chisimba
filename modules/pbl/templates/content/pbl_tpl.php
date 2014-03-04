<?php
/**
* PBL Session Entry Point
* @package pbl
*/


/**
* Template for displaying the pbl classroom
*/

    // set body parameters to focus the chat area on loading the page
    $bodyParams='onload="javascript:document.chat.chatline.select();document.chat.chatline.focus();"';
//    $this->setVarByRef('bodyParams',$bodyParams);
    
    $this->appendArrayVar('bodyOnLoad', $bodyParams);

    $this->setVar('pageSuppressToolbar', TRUE);
    $this->setVar('pageSuppressBanner', TRUE);

    // set up html objects
    $this->loadClass('htmltable', 'htmlelements');
    $this->loadClass('iframe', 'htmlelements');
    $this->loadClass('link', 'htmlelements');
    $this->loadClass('form', 'htmlelements');
    $this->loadClass('button', 'htmlelements');
    $this->loadClass('textinput', 'htmlelements');
    $this->loadClass('textarea', 'htmlelements');
//    $this->loadClass('multitabbedbox', 'htmlelements');
    $objTab = $this->newObject('tabcontent', 'htmlelements');
    $objHead = $this->newObject('htmlheading','htmlelements');
    $objIcon = $this->newObject('getIcon','htmlelements');
    $objMessage = $this->newObject('timeoutmessage','htmlelements');

// if there's no user id or classroom id then redirect to index page
    $sesClass = $this->getSession('classroom', '');
    $sesUserId = $this->getSession('pbl_user_id', '');

    if(empty($sesClass) || empty($sesUserId)){
        header('Location: ' .$this->uri(array(),'pbl'));
    }

    // set up table
    $objTable = new htmltable();
    $objTable->cellpadding = '2';

// initialize pbl header
    $sesClassName = $this->getSession('classname');
    $sesCaseName = $this->getSession('casename');

    $menu = $this->objLanguage->code2Txt('mod_pbl_classroomincase', 'pbl', array('classname' => $sesClassName, 'casename' => $sesCaseName));
    $lbCaseInfo = $this->objLanguage->languageText('mod_pbl_caseinfo', 'pbl');
    $lbTasks = $this->objLanguage->languageText('word_tasks');
    $lbNotes = $this->objLanguage->languageText('word_notes');
    $lbNoteBook = $this->objLanguage->languageText('word_notebook');
    $lbRestoreChat = $this->objLanguage->languageText('mod_pbl_restorechat', 'pbl');
    $viewLog = $this->objLanguage->languageText('phrase_viewpbllog');
    $lbLogIn = $this->objLanguage->languageText('mod_pbl_loggedin','pbl');
    $lbHelp = $this->objLanguage->languageText('mod_pbl_typehelp', 'pbl');
    $lbSend = $this->objLanguage->languageText('word_send');

    $objHead->type = 1;
    $objHead->str = $menu;

// initialise pbl menu - exit & help
    $menu2= $this->pbl->getMenuBar2();

// header: display menu and header
    $objTable->trClass='header';
    $objTable->row_attributes="width='100%' height='20' ";
    $objTable->startRow();
    $objTable->addCell($objHead->show(),'88%','bottom','left','','colspan="3"');
    $objTable->addCell($menu2,'12%','center','center','',' colspan="1"');
    $objTable->endRow();

    $objTable->row_attributes='';
    $objTable->startRow();
    $objTable->addCell('','25%');
    $objTable->addCell('','40%');
    $objTable->addCell('','20%');
    $objTable->addCell('','15%');
    $objTable->endRow();

// set up iframe containing case information
    $objIframe1 = new iframe();
    $objIframe1->name='board';
    $objIframe1->id='board';
    $objIframe1->src=$this->uri(array('action'=>'showboard'));
    $objIframe1->width='97%';
    $objIframe1->height='200px';
    $objIframe1->frameborder='0';
    $objIframe1->scrolling='yes';
    $board = '<b>'. strtoupper($lbCaseInfo).'</b><br />';
    $board .= $objIframe1->show();

// set up iframe containing tasks
    $objIframe2 = new iframe();
    $objIframe2->name='tasks';
    $objIframe2->id='tasks';
    $objIframe2->src=$this->uri(array('action'=>'showtasks'));
    $objIframe2->width='97%';
    $objIframe2->height='200px';
    $objIframe2->frameborder='0';
    $objIframe2->scrolling='yes';
    $tasks = '<b>'.strtoupper($lbTasks).'</b><br />';
    $tasks .= $objIframe2->show();

// iframe to hold learning issues and hypothesis
    $objIframe3 = new iframe();
    $objIframe3->name='content';
    $objIframe3->id='content';
    $objIframe3->src=$this->uri(array('action'=>'showcontent'));
    $objIframe3->width='100%;';
    $objIframe3->height='250px';
    $objIframe3->frameborder='0';
    $showcontent = $objIframe3->show();

$notes = '<b>'.strtoupper($lbNotes).'</b><br />';
if(!empty($msg)){
    $objMessage->setMessage($msg.'<br />');
    $objMessage->setTimeout(7000);
    $notes .= $objMessage->show();
}
$notes .= $this->classroom->writeNotes('notes');

$browser = getenv("HTTP_USER_AGENT");
if (preg_match("/MSIE/i", "$browser")){
    $boxWidth='97%';
}else{
    $boxWidth='60%';
}

/* Build a multi tabbed box to hold case information, tasks and notepad
$objBox = new multitabbedBox('225px',$boxWidth);
$tab1['name'] = $lbCaseInfo;
$tab1['content'] = $board;
$tab1['default'] = TRUE;
$objBox->addTab($tab1);
$tab2['name'] = $lbTasks;
$tab2['content'] = $tasks;
$objBox->addTab($tab2);
$tab3['name'] = $lbNoteBook;
$tab3['content'] = $notes;
$objBox->addTab($tab3);
*/

$objTab->init();
$objTab->addTab($lbCaseInfo, $board);
$objTab->addTab($lbTasks, $tasks);
$objTab->addTab($lbNoteBook, $notes);


    // display case content area: multitabbed box and content iframe
    $objTable->trClass='header';
    $objTable->startRow();
    $objTable->addCell($objTab->show(),'','top','left','multiboxfix',' style="height: 260px;" colspan="2"');
//    $objTable->addCell($objBox->show(),'','top','left','multiboxfix',' style="height: 260px;" colspan="2"');
    //$objTable->addCell($showcaseinfo,'','top','left','',' style="height: 260px;" colspan=2');
    $objTable->addCell($showcontent,'','top','left','',' colspan="2"');
    $objTable->endRow();

    // set up chat area iframe
    $objIframe5 = new iframe();
    $objIframe5->name = 'chatarea';
    $objIframe5->id = 'chatarea';
    $objIframe5->src = $this->uri(array('action'=>'showchat'));
    $objIframe5->width = '500px';
    $objIframe5->height = '120px';
    $objIframe5->frameborder = '0';
    $showchat = $objIframe5->show();

    // set up link to restore the chat from the previous sessions if required
    $objLink = new link($this->uri(array('action'=>'restore')));
    $objLink->link = $lbRestoreChat;
    $restore = $objLink->show();

    $objTable2 = new htmltable();
    $objTable2->startRow();
    $objTable2->addCell($showchat,'80%','center','','',' rowspan="2"');
    $objTable2->addCell($restore,'20%','center','center');
    $objTable2->endRow();

    $objIcon->setIcon('chat/viewlog');
    $objIcon->title=$viewLog;
    $objIcon->extra=" onclick=\"javascript:window.open('" .$this->uri(array('action'=>'viewlog'))
    ."', 'pbllog', 'width=600, height=500, scrollbars=1')\" ";
    $objLink = new link('#');
    $objLink->link = $objIcon->show();

    $objTable2->startRow();
    $objTable2->addCell($viewLog.'<br />'.$objLink->show(),'','','center');
    $objTable2->endRow();

    // display chat area
    $objTable->trClass='header';
    $objTable->row_attributes='';
    $objTable->startRow();
    $objTable->addCell($objTable2->show(),'','center','','scene',"height='90' colspan='3'");

    $filter="classroomid='" .$sesClass."' and isavailable='1'";
    $userid=$this->dbloggedin->findUserIds($filter);
    
    // get user ids of users loggedin in to the classroom - get users fullname from users table
    $users=array();
    
        if($userid){
            $i=0;
            foreach($userid as $val){
                if(!empty($val['studentid'])){
                    $user=$this->objGroupUser->getUsers(NULL," where id='".$val['studentid']."' ");
                    $listId = $user[0]['fullName'];
                    if($val['position'] == 'c' || $val['position'] == 's' || $val['position'] == 'f')
                        $listId .= ' ('.$val['position'].')';
                    $users[$i] = $listId;
                    $i++;
                }
            }
        }
        
    // create list of users logged in to the classroom
    $userlist='';
    foreach($users as $val){
        $userlist .= $val ."\n";
    }
    
    $objText = new textarea('users',$userlist,'10','12');
    $objText->value=$userlist;
    $objText->extra=' readonly="readonly"';

    // display list of logged in users/students in specific classroom
    $showstudents = '<p><font size="3"><b>' .$lbLogIn .'</b></font></p>'.$objText->show();
    $objTable->addCell($showstudents,'','center','center','scene',"height='7' rowspan='2'");
    $objTable->endRow();

    $objTable->trClass='header';
    $objTable->row_attributes="width='100%' height='12'";
    $objTable->startRow();

    // create form to submit a line to the chat
    $objForm = new form('chat', $this->uri(array('action'=>'chatline')));
    $objForm->method='get';
    
    $objText = new textarea('chatline',NULL,'1','70');
    $objText->extra=" wrap='hard' onclick='clearfocus()' onkeypress='if(event.keyCode==13)document.forms.chat.submit();'";
    $objText->setContent($lbHelp);
    $objForm->addToForm($objText->show());
        
    $objButton = new button('chat', $lbSend);
    $objButton->setToSubmit();
    $objButton->setIconClass("sync");
    $send = '&nbsp;&nbsp;' .$objButton->show();
    $objForm->addToForm($send);
        
    $objInput = new textinput('module','pbl', 'hidden');
    $send = $objInput->show();
        
    $objInput = new textinput('action','chatline', 'hidden');
    $send .= $objInput->show();
    $objForm->addToForm($send);
    $showchatline='<!-- chat user\'s input  -->'.$objForm->show().$this->jscript->clearFocus('chat','chatline');

    // display form
    $objTable->addCell($showchatline,'','center','','scene',"height='12' colspan='3'");
    $objTable->endRow();

    // display table
    echo $objTable->show();
    
?>
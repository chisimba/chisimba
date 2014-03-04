<?php
/*
* PBL index Page.
* @package pbl
*/

/*
* Template to display the PBL home page.
*/

$this->setLayoutTemplate('pbl_layout_tpl.php');

// set up html objects
$this->loadClass('htmltable','htmlelements');
$this->loadClass('link','htmlelements');
$objHead = $this->newObject('htmlheading','htmlelements');
$objLayer = $this->newObject('layer','htmlelements');
$objIcon = $this->getObject('geticon','htmlelements');

// get groups object from groupadmin
$objGroups=$this->getObject('groupAdminModel','groupadmin');

$lbLobby = $this->objLanguage->languageText('word_lobby');

// get current time, set classes to open 10mins before start time
    $time = time()+(10*60);
    $time = date("Y-m-d H:i:s",$time);

// check if in context and get code and title
    if($this->objContext->isInContext()){
        $contextcode = $this->objContext->getContextCode();
        $contexttitle = $this->objContext->getTitle();
    } else {
        $contextcode = 'lobby';
        $contexttitle = $lbLobby;
    }

// get username & fullname of current user
    $username = $this->objUser->userName();
    $fullname = $this->objUser->fullName();

// get user id
    $userid = $this->objUser->getUserId($username);
    $userId = $this->objUser->PKId($userid);

// check if user has admin rights
    $access = $this->objUser->isAdmin();
    if($access){
        $this->isAdmin = TRUE;
    }else {
    // Check if user is lecturer - display link to pbladmin
    if($contextcode){
            $groupid = $objGroups->getLeafId(array($contextcode,'Lecturers'));
        if($objGroups->isGroupMember( $userId, $groupid ))
            $access = 1;
        }
    }

    // Save user id in session
    $this->setSession('pbl_user_id', $userId);

    $objHead->type = 4;
    $objHead->str = $contexttitle;
    
    // Right panel contains a list of links into classrooms
    $rightMenu = '';
 
    // set up language items
    $caseLabel = $this->objLanguage->languageText('word_case');
    $classLabel = $this->objLanguage->languageText('word_classroom');
    $startLabel = $this->objLanguage->languageText('phrase_startingdate');
    $studentLabel = $this->objLanguage->code2Txt('mod_pbl_studentsloggedin', 'pbl');
    $closed = $this->objLanguage->languageText('mod_pbl_registeredclassrooms', 'pbl');
    $open = $this->objLanguage->languageText('mod_pbl_openclassrooms', 'pbl');
    $title = $this->objLanguage->languageText('mod_pbl_enterclassroom', 'pbl');
    $lbPblAdmin = $this->objLanguage->languageText('mod_pbladmin_pbladmin', 'pbladmin');
    $lbHere = $this->objLanguage->languageText('word_here');
    
    $tableHd = array();
    $tableHd[] = $caseLabel;
    $tableHd[] = $classLabel;
    $tableHd[] = $startLabel;    
    $tableHd[] = $studentLabel;
    
    // find list of classrooms associated with user or open
    $closedClass = $this->dbloggedin->getClassId($userId);
    $openClass = $this->dbloggedin->getClassId('student');

    // Build menu
    if(!empty($closedClass)){
        $objHead->type = 6;
        $objHead->str = $closed;
        $closedStr = $objHead->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->cellspacing = '2';
        $objTable->addHeader($tableHd, 'heading');
    
        $i = 0;
        foreach($closedClass as $val){
            $rowClass = ($i++%2 == 0)? 'odd':'even';
            $tbRow = array();
            // for each classroom: get info (name, open time)
            $filter = "id = '$val' and status = 'c' and context = '$contextcode'";
            $class = $this->dbclassroom->getName($filter);
            if($class){
                $case = $this->dbcases->getEntry($class[0]['caseid']);
                $str = $class[0]['name'];
                $count = '';
                // if current time is 10 mins before start time, make a clickable link
                if($class[0]['opentime']<$time){
                    $objLink = new link($this->uri(array('action'=>'startpbl','clid'=>$class[0]['id']),'pbl'));
                    $objLink->link = $str;
                    $objLink->title = $title;
                    $str = $objLink->show();
                    $count = $this->dbloggedin->getCount("classroomid='".$class[0]['id']."' and isavailable=1");
                    $count .= '&nbsp;&nbsp;/&nbsp;&nbsp;';
                    $count .= $this->dbloggedin->getCount("classroomid='".$class[0]['id']."'");
                }
                $tbRow[] = $case['name'];
                $tbRow[] = $str;
                //$str.='<br />&nbsp;&nbsp;&nbsp;&nbsp;'.$this->formatDate($class[0]['opentime']);
                //$closedStr.='<p>'.$str;
                $tbRow[] = $this->objDate->formatDate($class[0]['opentime']);
                $tbRow[] = $count;
            }
            $objTable->addRow($tbRow,$rowClass);
        }
        //$rightMenu.=$closedStr;
        $rightMenu = $closedStr.$objTable->show().'<br />';
    }
    
    if($openClass){
        $objHead->type = 6;
        $objHead->str = $open;
        $openStr = $objHead->show();
        
        $objTable1 = new htmltable();
        $objTable1->cellpadding = '4';
        $objTable1->cellspacing = '2';
        $objTable1->addHeader($tableHd, 'heading');
            
        $i = 0;
        foreach($openClass as $val){
            $rowClass = ($i++%2 == 0)? 'odd':'even';
            $tbRow = array();
            // for each classroom: get info (name, open time)
            $filter = "id='$val' and status='o' and context='$contextcode'";
            $class = $this->dbclassroom->getName($filter);
            if($class){
                $case = $this->dbcases->getEntry($class[0]['caseid']);
                $str = $class[0]['name'];
                $count = '';
                // if current time is 10 mins before start time, make a clickable link
                if($class[0]['opentime']<$time){
                    $objLink = new link($this->uri(array('action'=>'startpbl','clid'=>$class[0]['id']),'pbl'));
                    $objLink->link = $str;
                    $objLink->title = $title;
                    $str = $objLink->show();
                    $count = $this->dbloggedin->getCount("classroomid='".$class[0]['id']."' and isavailable=1");
                }
                $tbRow[] = $case['name'];
                $tbRow[] = $str;
                //$str.='<br />&nbsp;&nbsp;&nbsp;&nbsp;'.$class[0]['opentime'];
                //$openStr.='<p>'.$str;
                $tbRow[] = $this->objDate->formatDate($class[0]['opentime']);
                $tbRow[] = $count;
            }
            $objTable1->addRow($tbRow,$rowClass);
        }
        //$rightMenu.=$openStr;
        $rightMenu .= $openStr.$objTable1->show();
    }
    
    // Display link to pbladmin if user has access (admin or lecturer in the context)
    if($access){
    $objIcon->setIcon('modules/pbladmin');
    $objIcon->title = $lbPblAdmin;
    $objLink = new link($this->uri(array(''),'pbladmin'));
    $objLink->link = $objIcon->show().'&nbsp;' .$lbPblAdmin;
    $objLink->title = $lbPblAdmin;
    $admin = "<div class='adminiconlink'>".$objLink->show()."</div>";

    $rightMenu.='<p>&nbsp;</p>'.$admin;
    }    
 
                                                                                                                         
// Introduction
    $introHead = $this->objLanguage->languageText('word_introduction');
    $intro = $this->objLanguage->code2Txt('mod_pbl_paraIntro', 'pbl');
    
    $objLink = new link('#');
    $objLink->title = '';
    $objLink->extra = "onclick=\"javascript:window.open('" .$this->uri(array('action'=>'showinfo'))."', 'pblinfo', 'width=550, height=400, scrollbars=1')\"";
    $objLink->link = $lbHere;
     
    $moreinfo = '<br />&nbsp;<br />'.$this->objLanguage->code2Txt('mod_pbl_formoreinfo', 'pbl', array('here' => $objLink->show()));
    
    $objLink->extra = '';
    $objHead->type = 4;
    $objHead->str = $introHead;
    $header2 = $objHead->show();

    $mainBody = $header2.$intro.$moreinfo;

    $this->setVar('rightContent', $mainBody);
    
    echo $rightMenu;
?>
<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * An module to format the logged data in a user friendly manner.
 *
 * @author    Megan Watson
 * @copyright (c) 2007 University of the Western Cape
 * @package   logger
 * @version   0.1
 */
class logdisplay extends object
{
    /**
     * Constructor method
     */
    public function init()
    {
        try {
            $this->logShow = $this->getObject('logshow', 'logger');

            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objCatalogue = $this->getObject('catalogueconfig', 'modulecatalogue');
            $objModules = $this->getObject('modules', 'modulecatalogue');
            $this->objContext = $this->getObject('dbcontext', 'context');
        //Load ContextContent class
            if ($objModules->checkIfRegistered('contextcontent')){
                $this->objContentOrder = $this->getObject('db_contextcontent_order','contextcontent');
                $this->contextFlag=TRUE;
            } else {
                $this->contextFlag=FALSE;
            }
            $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
            $this->loadClass('htmlheading', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
            $this->loadClass('windowpop', 'htmlelements');
        }catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

    /**
    * Method to display the list of modules and their usage
    *
    * @access public
    * @return string html
    */
    public function show()
    {
        $data = $this->logShow->showStatsByModule();

        $hdModules = $this->objLanguage->languageText('mod_logger_statisticsbymodule', 'logger');
        $lbModule = $this->objLanguage->languageText('phrase_modulename');
        $lbHits = $this->objLanguage->languageText('word_hits');
        $lbUsers = $this->objLanguage->languageText('phrase_numberofusers');
        $lnDescription = $this->objLanguage->languageText('phrase_viewmoduledescription');

        $objHead = new htmlheading();
        $objHead->str = ucwords($hdModules);
        $objHead->type = 1;
        $str = $objHead->show();

        if(!empty($data)){
            $headerParams=$this->getJavascriptFile('new_sorttable.js','htmlelements');
            $this->appendArrayVar('headerParams', $headerParams);

            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->id = 'newtable';
            $objTable->css_class = 'sorttable';
            $objTable->row_attributes = 'name="row_'.$objTable->id.'"';

            $objTable->startRow();
            $objTable->addCell($lbModule, '60%', '','', 'heading');
            $objTable->addCell($lbHits, '10%', '','', 'heading');
            $objTable->addCell($lbUsers, '10%', '','', 'heading');
            $objTable->addCell('', '20%', '','', 'heading');
            $objTable->endRow();

            foreach($data as $item){
                $module = $item['module'];

                $objPop = new windowpop();
                $objPop->set('location', $this->uri(array('action' => 'showmoduleinfo', 'mod' => $module)));
                $objPop->set('linktext', $lnDescription);
                $objPop->set('width', '250');
                $objPop->set('height', '300');
                $objPop->set('left', '300');
                $objPop->set('top', '400');
                $objPop->set('resizable', 'yes');
                $link = $objPop->show();

                $row = array();
                $row[] = $module;
                $row[] = $item['calls'];
                $row[] = $item['users'];
                $row[] = $link;

                $objTable->row_attributes = "name='row_".$objTable->id."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className=''; \"";
                $objTable->addRow($row);
            }
            $str .= $objTable->show();
        }

        return $str.'<br />';
    }

    /**
    * Method to display the list of modules and their usage
    *
    * @access public
    * @return string html
    */
    public function statsByUser()
    {
        $data = $this->logShow->showStatsByModule();

        $hdModules = $this->objLanguage->languageText('mod_logger_statisticsbymodule', 'logger');
        $lbModule = $this->objLanguage->languageText('phrase_modulename');
        $lbHits = $this->objLanguage->languageText('word_hits');
        $lbUsers = $this->objLanguage->languageText('phrase_numberofusers');
        $lnDescription = $this->objLanguage->languageText('phrase_viewmoduledescription');

        $objHead = new htmlheading();
        $objHead->str = ucwords($hdModules);
        $objHead->type = 1;
        $str = $objHead->show();

        if(!empty($data)){
            $headerParams=$this->getJavascriptFile('new_sorttable.js','htmlelements');
            $this->appendArrayVar('headerParams', $headerParams);

            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->id = 'newtable';
            $objTable->css_class = 'sorttable';
            $objTable->row_attributes = 'name="row_'.$objTable->id.'"';

            $objTable->startRow();
            $objTable->addCell($lbModule, '60%', '','', 'heading');
            $objTable->addCell($lbHits, '10%', '','', 'heading');
            $objTable->addCell($lbUsers, '10%', '','', 'heading');
            $objTable->addCell('', '20%', '','', 'heading');
            $objTable->endRow();

            foreach($data as $item){
                $module = $item['module'];

                $objPop = new windowpop();
                $objPop->set('location', $this->uri(array('action' => 'showmoduleinfo', 'mod' => $module)));
                $objPop->set('linktext', $lnDescription);
                $objPop->set('width', '250');
                $objPop->set('height', '300');
                $objPop->set('left', '300');
                $objPop->set('top', '400');
                $objPop->set('resizable', 'yes');
                $link = $objPop->show();

                $row = array();
                $row[] = $module;
                $row[] = $item['calls'];
                $row[] = $item['users'];
                $row[] = $link;

                $objTable->row_attributes = "name='row_".$objTable->id."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className=''; \"";
                $objTable->addRow($row);
            }
            $str .= $objTable->show();
        }

        return $str.'<br />';
    }

    /**
    * Method to display the module description
    *
    * @access public
    * @return string html
    */
    public function moduleInfo($module)
    {
        $lnClose = $this->objLanguage->languageText('word_close');
        $modArr = $this->objCatalogue->getModuleDescription($module);
        $modArr2 = $this->objCatalogue->getModuleName($module);

        $description = $modArr[0];
        $modName = $modArr2[0];

        $objLink = new link('#');
        $objLink->link = $lnClose;
        $objLink->extra = 'onclick = "javascript: window.close()"';
        $description .= '<p align="center">'.$objLink->show().'</p>';

        return $this->objFeatureBox->showContent($modName, $description);
    }

    /**
    * Method to display the left menu with the index
    *
    * @access public
    * @return string html
    */
    public function leftMenu()
    {
        $hdMenu = $this->objLanguage->languageText('word_menu');
        $lnModules = $this->objLanguage->languageText('mod_logger_statisticsbymodule', 'logger');
        $lnUser = $this->objLanguage->languageText('mod_logger_statisticsbyuser', 'logger');
        $lnStatistics = $this->objLanguage->languageText('mod_logger_statistics', 'logger');
        $lnPages = $this->objLanguage->languageText('mod_logger_pagespermodule', 'logger');
        $lnCoursePages = $this->objLanguage->languageText('mod_logger_pagespercourse', 'logger');

        $str = '<ul>';

        $objLink = new link($this->uri(''));
        $objLink->link = $lnModules;
        $str .= '<li>'.$objLink->show().'</li>';

        if ($this->contextFlag){
            $objLink = new link($this->uri(array('action' => 'showstatsbycontext')));
            $objLink->link = $lnCoursePages;
            $str .= '<li>'.$objLink->show().'</li>';
        }
    $hasAccess = $this->objEngine->_objUser->isLecturer();
    if($hasAccess){
        $userId=$this->objUser->userId();
        $role='Lecturers';
        $objContextGroups = $this->getObject('managegroups','contextgroups');
        $lectRole = $objContextGroups->rolecontextcodes($userId,$role);
        if(!empty($lectRole)){
            foreach($lectRole as $myLectRole){
                $contextTitle = $this->objContext->getField('title',$myLectRole);
                    $objLink = new link($this->uri(array('action' => 'userstats')));
                    $objLink->link($this->uri(array(
                        'module' => 'logger',
                        'action' => 'statsbycontext',
                        'contextcode' => $myLectRole
                        )));

                    $objLink->link = $contextTitle." ".$lnStatistics;
                    $str .= '<li>'.$objLink->show().'</li>';
            }
        }
    }else{
        $userId=$this->objUser->userId();
        $objContextGroups = $this->getObject('managegroups','contextgroups');
        $studRole = $objContextGroups->rolecontextcodes($userId,$role='Students');
        foreach($studRole as $myStudRole){
            $contextTitle = $this->objContext->getField('title',$myStudRole);
                $objLink = new link($this->uri(array('action' => 'userstats')));
                $objLink->link($this->uri(array(
                    'module' => 'logger',
                    'action' => 'userstatsbycontext',
                    'userId' => $myStudRole['userid'],
                    'contextcode' => $myStudRole
                    )));
                $objLink->link = $contextTitle." ".$lnStatistics;
                $str .= '<li>'.$objLink->show().'</li>';
        }
    }
        /*
        $objLink = new link($this->uri(array('action' => 'userstats')));
        $objLink->link = $lnUser;
        $str .= '<li>'.$objLink->show().'</li>';

        $objLink = new link($this->uri(''));
        $objLink->link = $lnPages;
        $str .= '<li>'.$objLink->show().'</li>';
        */
        $str .= '</ul>';

        return $this->objFeatureBox->show($hdMenu, $str);
    }
    /**
    * Method to display the context pages visited by a user
    * @added by Paul Mungai
    * @access public
    * @return string html
    */
    public function getVisitedPages($userId=NULL,$contextCode=NULL, $module=NULL){
    $this->objUserLoginHistory = $this->getObject('userloginhistory','security');
        $hdCoursePages = $this->objLanguage->languageText('mod_logger_visitedpageshistory', 'logger');
    $lbFullName =  $this->objLanguage->languageText('mod_logger_fullname', 'logger');
    $lbUserAccesses =  $this->objLanguage->languageText('mod_logger_totalUserAccesses', 'logger');
    $lbFirstLogin = $this->objLanguage->languageText('mod_logger_firstLogin', 'logger');
    $lbLastLogin = $this->objLanguage->languageText('mod_logger_lastLogin', 'logger');
    $lbWordNo = $this->objLanguage->languageText('mod_logger_wordNo', 'logger');
    $lbPageName = $this->objLanguage->languageText('mod_logger_pageName', 'logger');
    $lbAccessTime = $this->objLanguage->languageText('mod_logger_accessTime', 'logger');
    if($userId==NULL){
        $userId=$this->objUser->userId();
    }
    //get user login info
    $userLastLogin = $this->objUserLoginHistory->doGetLastLogin($userId);
    $userFirstLogin = $this->objUserLoginHistory->doGetFirstLogin($userId);
    $userLookUp = $this->objUser->lookupData($this->objUser->userName($userId));

    $objTable = new htmltable();
    $objTable->width='100%';
    $objTable->border='0';
    $objTable->cellspacing='0';
    $objTable->cellpadding='10';

    $objTable->startRow();
    $objTable->addCell("<b>".$lbFullName.": </b>".$this->objUser->fullname($userId), '40%', 'top', 'left');
    $objTable->addCell("<b>".$lbUserAccesses.": </b>".$userLookUp['logins'], '60%', 'top', 'left');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell("<b>".$lbFirstLogin.": </b>".$userFirstLogin, '40%', 'top', 'left');
    $objTable->addCell("<b>".$lbLastLogin.": </b>".$userLastLogin, '60%', 'top', 'left');
    $objTable->endRow();
    if($contextCode==Null){
        $sql = $this->logShow->userLoggerDetails($userId,$contextCode=Null, $module);
    }elseif($module==Null){
        $sql = $this->logShow->userLoggerDetails($userId,$contextCode, $module=Null);
    }else{
        $sql = $this->logShow->userLoggerDetails($userId,$contextCode, $module);
    }

    $contexts=array();
    $key = 0;
    foreach($sql as $myContext){
        $contexts[$key]=$myContext['context'];
        $key = $key+1;
    }

    //Remove duplicate values from the array
    $userContexts = array_unique($contexts);

        $objHead = new htmlheading();
        $objHead->str = ucwords($hdCoursePages);
        $objHead->type = 2;
        $objHead->align = 'center';
        $str = $objHead->show()."<br />".$objTable->show()."<br />";

    $class='even';
    foreach($userContexts as $thisContext){
        $contextTitle = $this->objContext->getField('title',$thisContext);
        $objTable = new htmltable();
        $objTable->width='100%';
        $objTable->border='0';
        $objTable->cellspacing='0';
        $objTable->cellpadding='10';

        $objTable->startRow();
        $objTable->addHeaderCell("<b>".$contextTitle."</b>", '100%', 'top', 'left',Null,'colspan=3');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell("<b>".$lbWordNo.".</b>", '5%', 'top', 'left');
        $objTable->addCell("<b>".$lbPageName."</b>", '75%', 'top', 'left');
        $objTable->addCell("<b>".$lbAccessTime."</b>", '25%', 'top', 'left');
        $objTable->endRow();

        $mySql = $this->logShow->userLoggerDetails($userId,$thisContext, $module='contextcontent');
        $key = 1;
        $eventparamvalue = array();
        $dateCreated = array();
        foreach($mySql as $myContext){
            $eventparamvalue[$key]=$myContext['eventparamvalue'];
            $dateCreated[$key]=$myContext['datecreated'];
            $strEventParamValue = explode('=',$eventparamvalue[$key],2);
            $action=$strEventParamValue[0];
            $param=$strEventParamValue[1];
            if($action=='id'){
                $strParam = explode('&',$param,2);
                $pageId=$strParam[0];//i.e. gen5Srv7Nme24_1731_1217434951
                $paramMsg=$strParam[1];//i.e. message=&action=viewpage
            }elseif($action!=='NULL'){
                $action=$strEventParamValue[0];//i.e. action
                $param=$strEventParamValue[1];//i.e. viewpage&id=gen5Srv7Nme24_54
                $strParam = explode('&',$param,2);
                $paramAction=$strParam[0];//viewpage
                $paramId=$strParam[1];//id=gen5Srv7Nme24_5450_1216228562
                $strId = explode('=',$paramId,2);
                $nameId = $strId[0];//id
                $pageId= $strId[1];//gen5Srv7Nme24_5450_1216228562
            }
                        if ($this->contextFlag){
                            //Get page title
             $thisPageTitle = $this->objContentOrder->getPage($pageId, $thisContext);
                        } else {
                            $thisPageTitle['menutitle']=array();
                        }

            $pageTitle = $thisPageTitle['menutitle'];
            if(!empty($pageTitle)){
                $objTable->startRow();
                $objTable->addCell($key, Null, 'top', 'left',$class);
                $objTable->addCell($pageTitle, Null, 'top', 'left',$class);
                $objTable->addCell($dateCreated[$key], Null, 'top', 'left',$class);
                $objTable->endRow();
                $key = $key+1;

                if($class=='odd'){
                    $class='even';
                }else{
                    $class='odd';
                }
            }
        }
        $str .= $objTable->show()."<br />";
    }
    return $str."<br />";
    }
    /**
    * Method to display the context users
    *
    * @access public
    * @return string html
    */
    public function getContextUsers($userId=Null,$contextCode=Null){
        $hdCoursePages = $this->objLanguage->languageText('mod_logger_visitedpageshistory', 'logger');
        $hdStatsUser = $this->objLanguage->languageText('mod_logger_statisticsbyuser', 'logger');
        $objHead = new htmlheading();
        $objHead->str = ucwords($hdCoursePages." ".$this->objUser->fullname($userId));
        $objHead->type = 1;
//        $str = $objHead->show();
    $str = " ";
    $contextTitle = $this->objContext->getField('title',$contextCode);
    $objTable = new htmltable();
    $objTable->width='100%';
    $objTable->border='0';
    $objTable->cellspacing='0';
    $objTable->cellpadding='10';

    $objTable->startRow();
    $objTable->addHeaderCell("<b>".$contextTitle." ".$hdStatsUser."</b>", '100%', 'top', 'left',Null,'colspan=2');
    $objTable->endRow();

    if($userId==NULL){
        $userId=$this->objUser->userId();
    }
    $objContextGroups = $this->getObject('managegroups','contextgroups');
    $studRole = $objContextGroups->contextUsers( $role="Students", $contextCode, $fields=NULL );
    $key = 1;
    $class='odd';
    foreach($studRole as $myStudRole){
        $studentNames=$this->objUser->fullname($myStudRole['userid']);
            $objLink = &$this->getObject("link", "htmlelements");
            $objLink->link($this->uri(array(
                'module' => 'logger',
                'action' => 'userstatsbycontext',
                'userId' => $myStudRole['userid'],
                'contextcode' => $contextCode
                )));
            $objLink->link = $studentNames;
        $objTable->startRow();
        $objTable->addCell($key, '5%', 'top', 'left',$class);
        $objTable->addCell($objLink->show(), '95%', 'top', 'left',$class);
        $objTable->endRow();
        $key = $key + 1;
        if($class=='odd'){
            $class='even';
        }else{
            $class='odd';
        }
    }
    $str .= $objTable->show()."<br />";

    return $str."<br />";
    }
}
?>

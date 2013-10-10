<?php
/**
* configure class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class to set up the ETD repository.
* The class provides an interface for and institution to enter their information and
* set up the submission / approval process for an etd before it can be archived. It also
* provides an interface to set up permissions, giving the appropriate people access to different
* parts of the process.
*
* @author Megan Watson
* @copyright (c) 2004 UWC
* @version 0.2
* @modified by Megan Watson on 2006 11 05 Ported to 5ive / chisimba
*/

class configure extends object
{
    /**
    * @var array $results The search results for users
    * @access private
    */
    private $results = array();
    
    /**
    * Constructor method
    */
    public function init()
    {
        try{
            $this->dbCopyright = $this->getObject('dbcopyright', 'etd');
            $this->dbDegrees = $this->getObject('dbdegrees', 'etd');
            $this->dbProcess = $this->getObject('dbprocess', 'etd');
            $this->dbIntro = $this->getObject('dbintro', 'etd');
            
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objDbConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $this->objUserDb = $this->getObject('usersdb', 'groupadmin');
            $this->objUser = $this->getObject('user', 'security');
            $this->pkId = $this->objUser->PKId();
            $this->userId = $this->objUser->userId();
            
            $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
            $this->objHead = $this->newObject('htmlheading', 'htmlelements');
            $this->objEditor = $this->newObject('htmlarea', 'htmlelements');
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('tabbedbox', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            
        }catch(Exception $e){
            throw customException($e->message());
            exit();
        }
    }
    
    /**
    * Method to make the introductory text editable
    *
    * @access private
    * @return string html
    */
    function editIntro()
    {
        $btnSave = $this->objLanguage->languageText('word_save');
        $lbCodes = $this->objLanguage->languageText('mod_etd_introusecodes', 'etd');
        $lbInstitutionCode = $this->objLanguage->languageText('mod_etd_codeinstitution', 'etd');
        $lbAbbrCode = $this->objLanguage->languageText('mod_etd_codeabbrevinstitution', 'etd');
        
        $introData = $this->dbIntro->getIntro();
        if(!empty($introData)){
            $introduction = $introData['content_text'];
        }else{
            $introduction = $this->objLanguage->languageText('mod_etd_welcomeintro', 'etd');
        }
        
        // Explanation of codes
        $editorStr = '<p>'.$lbCodes.'<ul><li>'.$lbInstitutionCode.'</li><li>'.$lbAbbrCode.'</li></ul></p>';
        
        // Form for updating the intro
        $this->objEditor->init('introduction', $introduction, '10', '50');
        $this->objEditor->width = '400px';
        $this->objEditor->height = '400px';
        $this->objEditor->setBasicToolBar(); 
        $editorStr .= '<p>'.$this->objEditor->showFCKEditor().'</p>';
        
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $editorStr .= '<p>'.$objButton->show().'</p>';
        
        if(isset($introData['id']) && !empty($introData['id'])){
            $objInput = new textinput('id', $introData['id'], 'hidden');
            $editorStr .= $objInput->show();
        }
        
        $objForm = new form('saveintro', $this->uri(array('action' => 'saveconfig', 'mode' => 'saveintro', 'nextmode' => 'showintro')));
        $objForm->addToForm($editorStr);
        
        // Preview the intro
        $editShow = $this->dbIntro->parseIntro($introduction);
        
        // Display
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->startRow();
        $objTable->addCell($objForm->show());
        $objTable->addCell('', '3%');
        $objTable->addCell($editShow, '50%');
        $objTable->endRow();
        
        $str = '<p>'.$objTable->show().'</p>';
        return $str;
    }
    
    /**
    * Method to display an interface for editing the footer text
    *
    * @access private
    * @return string html
    */
    function editFooter()
    {
        $btnSave = $this->objLanguage->languageText('word_save');
        
        $data = $this->dbIntro->getContent('footer');
        
        $footer = ''; $editorStr = '';
        if(!empty($data)){
            $footer = $data['content_text'];
        }
        
        // Form for updating the intro
        $this->objEditor->init('footer', $footer, '10', '50');
        $this->objEditor->width = '400px';
        $this->objEditor->height = '400px';
        //$this->objEditor->setBasicToolBar(); 
        $editorStr = $this->objEditor->showFCKEditor();
        
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $editorStr .= '<p>'.$objButton->show().'</p>';
        
        if(isset($data['id']) && !empty($data['id'])){
            $objInput = new textinput('id', $data['id'], 'hidden');
            $editorStr .= $objInput->show();
        }
        
        $objForm = new form('saveintro', $this->uri(array('action' => 'saveconfig', 'mode' => 'savefooter', 'nextmode' => 'showfooter')));
        $objForm->addToForm($editorStr);
                
        // Display
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->startRow();
        $objTable->addCell($objForm->show());
        $objTable->addCell('', '3%');
        $objTable->addCell($footer, '50%');
        $objTable->endRow();
        
        $str = '<p>'.$objTable->show().'</p>';
        return $str;
    }
    
    /**
    * Method to display an interface for editing the FAQ
    *
    * @access private
    * @return string html
    */
    function editFaq()
    {
        $btnSave = $this->objLanguage->languageText('word_save');
        
        $data = $this->dbIntro->getContent('faq');
        
        $faq = ''; $editorStr = '';
        if(!empty($data)){
            $faq = $data['content_text'];
        }
        
        // Form for updating the intro
        $this->objEditor->init('faq', $faq, '10', '50');
        $this->objEditor->width = '400px';
        $this->objEditor->height = '600px';
        //$this->objEditor->setBasicToolBar(); 
        $editorStr = $this->objEditor->showFCKEditor();
        
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $editorStr .= '<p>'.$objButton->show().'</p>';
        
        if(isset($data['id']) && !empty($data['id'])){
            $objInput = new textinput('id', $data['id'], 'hidden');
            $editorStr .= $objInput->show();
        }
        
        $objForm = new form('saveintro', $this->uri(array('action' => 'saveconfig', 'mode' => 'savefaq', 'nextmode' => 'showfaq')));
        $objForm->addToForm($editorStr);
                
        // Display
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->startRow();
        $objTable->addCell($objForm->show());
        $objTable->addCell('', '3%');
        $objTable->addCell($faq, '50%');
        $objTable->endRow();
        
        $str = '<p>'.$objTable->show().'</p>';
        return $str;
    }
    
    /**
    * Method to display a form for entering the institution specific information.
    *
    * @access private
    * @return string html
    */
    function getInstitute()
    {
        $lbName = $this->objLanguage->languageText('mod_etd_nameinstitution', 'etd');
        $lbYear = $this->objLanguage->languageText('mod_etd_earliestarchivalyear', 'etd');
        $lbCopyright = $this->objLanguage->languageText('mod_etd_copyrightpublicationcond', 'etd');
        $lbAccept = $this->objLanguage->code2Txt('mod_etd_tobeaccepted', 'etd');
        $lbCodes = $this->objLanguage->languageText('mod_etd_usecodes', 'etd');
        $btnUpdate = $this->objLanguage->languageText('word_update');
        $btnChange = $this->objLanguage->languageText('word_change');
        $codeName = $this->objLanguage->languageText('mod_etd_codestudentname', 'etd');
        $codeDegree = $this->objLanguage->languageText('mod_etd_codedegree', 'etd');
        $codeDept = $this->objLanguage->languageText('mod_etd_codedepartment', 'etd');

        // institute name
        $str = '<p><b>'.$lbName.':</b>&nbsp;&nbsp;'.$this->objConfig->getInstitutionName().'</p>';
        
        // archival year
        $str .= '<p><b>'.$lbYear.':</b>&nbsp;&nbsp;';
        
        $objButton = new button('save', $btnChange);
        $objButton->setToSubmit();
        
        $objDrop = new dropdown('year');
        $curYr = date('Y');
        for($i = 1900; $i <= $curYr; $i = $i + 5){
            $objDrop->addOption($i, $i);
        }
        $objDrop->setSelected($this->objDbConfig->getValue('ARCHIVE_START_YEAR', 'etd'));
        $dropStr = $objDrop->show().'&nbsp;&nbsp;'.$objButton->show();
        
        $objForm = new form('changeyear', $this->uri(array('action' => 'saveconfig', 'mode' => 'saveyear')));
        $objForm->addToForm($dropStr);
        $str .= $objForm->show().'</p>';

        // copyright
        $str .= '<p><b>'.$lbCopyright.'</b><br />('.$lbAccept.')</p>';
        
        $editorStr = '<p>'.$lbCodes.'<ul><li>'.$codeName.'</li><li>'.$codeDegree.'</li><li>'.$codeDept.'</li></ul></p>';
        
        $lang = '';
        // Get the copyright message and replace the student name, department and degree
        $copy = $this->dbCopyright->getCopyright($lang);
        $copyright = '';
        if(isset($copy['copyright'])){
            $copyright = $copy['copyright'];
        }
        
        $copyShow = str_replace('[-studentname-]', 'Name', $copyright);
        $copyShow = str_replace('[-departmentname-]', 'Department', $copyShow);
        $copyShow = str_replace('[-degreename-]', 'Degree', $copyShow);
       
        $this->objEditor->init('copyright', $copyright, '10', '50');
        $this->objEditor->width = '400px';
        $this->objEditor->height = '400px';
        $this->objEditor->setBasicToolBar(); 
        $editorStr .= '<p>'.$this->objEditor->showFCKEditor().'</p>';
        
        $objButton = new button('save', $btnUpdate);
        $objButton->setToSubmit();
        $editorStr .= '<p>'.$objButton->show().'</p>';
        
        if(isset($copy['id'])){
            $objInput = new textinput('copyId', $copy['id'], 'hidden');
            $editorStr .= $objInput->show();
        }
        $objInput = new textinput('language', 'en', 'hidden');
        $editorStr .= $objInput->show();
            
        $objForm = new form('updatecopy', $this->uri(array('action' => 'saveconfig', 'mode' => 'savecopyright')));
        $objForm->addToForm($editorStr);
        
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->startRow();
        $objTable->addCell($objForm->show());
        $objTable->addCell('', '3%');
        $objTable->addCell($copyShow, '50%');
        $objTable->endRow();
        
        $str .= '<p>'.$objTable->show().'</p>';

/*

        $lang = strtolower($this->objConfig->defaultLanguageAbbrev());
        $this->objLabel = new label($lbLanguage, 'input_language');
        $this->objDrop = new dropdown('language');
        foreach($this->objLangCode->iso_639_2_tags as $key => $item){
            $this->objDrop->addOption($key, $item);
        }
        $this->objDrop->setSelected($lang);
*/
        return $str;
    }
    
    /**
    * Method to display the faculties, degrees and departments for adding / editing / deleting
    *
    * @access private
    * @return string html
    */
    private function getFaculty($mode = NULL, $data = NULL)
    {
        $name = '';
        $itemId = $this->getSession('update_id');
        if(isset($itemId) && !empty($itemId)){
            if(!empty($data)){
                foreach($data as $item){
                    if($item['id'] == $itemId){
                        $itemData = $item;
                        $name = $item['name'];
                        continue;
                    }
                }
            }
        }
        
        $lbFaculty = $this->objLanguage->languageText('phrase_updatefaculties');
        $lbDepartment = $this->objLanguage->languageText('phrase_updatedepartments');
        $lbDegree = $this->objLanguage->languageText('phrase_updatedegrees');
        $lbSave = $this->objLanguage->languageText('word_save');
        
        // Index - faculty, degree, department
        $indexStr = '<ul>';
        
        $objLink = new link($this->uri(array('action' => 'showconfig', 'mode' => 'updatefaculty')));
        $objLink->link = $lbFaculty;
        $indexStr .= '<li>'.$objLink->show().'</li>';
        
        $objLink = new link($this->uri(array('action' => 'showconfig', 'mode' => 'updatedepartment')));
        $objLink->link = $lbDepartment;
        $indexStr .= '<li>'.$objLink->show().'</li>';
        
        $objLink = new link($this->uri(array('action' => 'showconfig', 'mode' => 'updatedegree')));
        $objLink->link = $lbDegree;
        $indexStr .= '<li>'.$objLink->show().'</li>';
        
        $indexStr .= '</ul>';
        
        // Form - submit box
        $formStr = '';
        if(isset($mode) && !empty($mode)){
            switch($mode){
                case 'faculty':
                    $formHead = $lbFaculty;
                    break;
                case 'department':
                    $formHead = $lbDepartment;
                    break;
                case 'degree':
                    $formHead = $lbDegree;
                    break;
            }
            
            $formStr = '<p><b>'.$formHead.'</b></p>';
            
            $objInput = new textinput('name', $name, '', 60);
            $form = '<p>'.$objInput->show().'</p>';
            
            if(isset($itemId) && !empty($itemId)){
                $objInput = new textinput('id', $itemId, 'hidden');
                $form .= $objInput->show();
            }
            
            $objButton = new button('save', $lbSave);
            $objButton->setToSubmit();
            $form .= '<p>'.$objButton->show().'</p>';
            
            $objInput = new textinput('type', $mode, 'hidden', 60);
            $hidden = $objInput->show();
            
            $objForm = new form('update', $this->uri(array('action' => 'saveconfig', 'mode' => 'saveupdate', 'nextmode' => 'update'.$mode)));
            $objForm->addToForm($form);
            $objForm->addToForm($hidden);
            $formStr .= $objForm->show();
        }
        
        // List - updated
        $listStr = '';
        if(!empty($mode) && !empty($data)){
            $listStr = '<ul>';
            
            foreach($data as $item){
                // edit icon
                $this->objIcon->setIcon('edit');
                $this->objIcon->extra = ' width="15px" height="15px"';
                $this->objIcon->title = $this->objLanguage->languagetext('word_edit');
                $linkArr = array('action' => 'showconfig', 'mode' => 'editupdate', 'type' => $mode, 'id' => $item['id']);
                $objLink = new link($this->uri($linkArr));
                $objLink->link = $this->objIcon->show();
        
                $icons = $objLink->show();
                
                // delete icon
                $this->objIcon->setIcon('delete');
                $this->objIcon->extra = ' width="15px" height="15px"';
                $this->objIcon->title = $this->objLanguage->languagetext('word_delete');
                $linkArr = array('action' => 'saveconfig', 'mode' => 'deleteupdate', 'nextmode' => 'update'.$mode, 'save' => TRUE, 'id' => $item['id']);
                $objLink = new link($this->uri($linkArr));
                $objLink->link = $this->objIcon->show();
        
                $icons .= $objLink->show();
                
                $listStr .= '<li>'. htmlentities($item['name']).'&nbsp;&nbsp;&nbsp;'.$icons.'</li>';
            }
            
            $listStr .= '</ul>';
        }
        
        // Display
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->startRow();
        $objTable->addCell($indexStr);
        $objTable->addCell('', '3%');
        $objTable->addCell($listStr, '50%', '','','', 'rowspan="2"');
        $objTable->endRow();
        
        $objTable->addRow(array($formStr, ''));
        
        return $objTable->show();
    }
    
    /**
    * Method to configure the submission process for a resource / thesis.
    * The steps available are: submit to supervisor for approval; - to do
    * submit to external examiners; - to do
    * submit to examination board (persons to be selected); 
    * submit for metadata editing;
    * submit to manager for final approval and archiving to repository.
    *
    * @access private
    * @return string html
    */
    private function getSubmissionProcess($userType = '', $users = NULL)
    {
        // Get process steps
        $data = $this->dbProcess->getSteps();
                
        $lbConfigure = $this->objLanguage->languageText('mod_etd_configureprocess', 'etd');
        $lbStudent = $this->objLanguage->languageText('mod_etd_stepstudent', 'etd');
        $lbManager = $this->objLanguage->languageText('mod_etd_stepmanager', 'etd');
        $lbAdd = $this->objLanguage->languageText('phrase_addstep');
        $lbRemove = $this->objLanguage->languageText('phrase_removestep');
        $lbStep = $this->objLanguage->languageText('word_step');
        $lbManage = $this->objLanguage->languageText('phrase_manageusers');
        $lbNoUsers = $this->objLanguage->languageText('mod_etd_nousersingroup', 'etd');
        $lbRemoveUser = $this->objLanguage->languageText('phrase_removeuser');
        $lbAddUser = $this->objLanguage->languageText('phrase_adduser');
        $lbSearchBy = $this->objLanguage->languageText('phrase_searchby');
        $lbSurname = $this->objLanguage->languageText('word_surname');
        $lbFirst = $this->objLanguage->languageText('phrase_firstname');
        $btnSearch = $this->objLanguage->languageText('word_search');
        $btnSave = $this->objLanguage->languageText('word_save');
        $noUsersFound = $this->objLanguage->languageText('mod_etd_nousersfound', 'etd');
        
        $str = '<p>'.$lbConfigure.'</p>';
        
        /* ** List available options ** */
        $listStr = '<ul>';
        $listStr .= '<li>'.$lbStudent.'</li>';
        
        if(!empty($data)){
            $optionArr = array('action' => 'saveconfig', 'mode' => 'saveprocess', 'nextmode' => '', 'save' => TRUE);
            foreach($data as $item){
                // Ignore manager
                // Supervisor, external - to do
                if($item['step_type'] == 'manager' || $item['step_type'] == 'external' || $item['step_type'] == 'supervisor'){
                    continue;
                }
                // Create add / remove link
                $linkArr = $optionArr;
                $linkArr['stepid'] = $item['id'];
                
                if($item['is_active'] == 1){
                    $link = $lbRemove;
                    $linkArr['type'] = 'remove';
                }else{
                    $link = $lbAdd;
                    $linkArr['type'] = 'add';
                }
                
                $objLink = new link($this->uri($linkArr));
                $objLink->link = $link;
                
                $label = $this->objLanguage->languageText('mod_etd_step'.$item['step_type'], 'etd');
                $listStr .= '<li>'.$label.'&nbsp;&nbsp;&nbsp;'.$objLink->show().'</li>';
            }
        }
        
        $listStr .= '<li>'.$lbManager.'</li>';
        $listStr .= '</ul>';
        
        /* ** Display steps ** */
        $dispStr = '';
        if(!empty($data)){
            $dispStr = '<ul>';
            $i = 0;
            foreach($data as $item){
                if($item['is_active'] == 0){
                    continue;
                }
                $i++;
                $type = $this->objLanguage->languageText('mod_etd_step'.$item['step_type'], 'etd');
                
                // Create link to manage users in the group for the step
                $linkArr = array('action' => 'showconfig', 'mode' => 'manageusers', 'type' => $item['step_type'], 'group' => $item['group_name']);
                $objLink = new link($this->uri($linkArr));
                $objLink->link = $lbManage;
                
                $dispStr .= '<li>'.$lbStep.' '.$i.':&nbsp;&nbsp;&nbsp;'.$type.'&nbsp;&nbsp;&nbsp;['.$objLink->show().']</li>';
            }
            $dispStr .= '</ul>';
        }       
        
        /* ** List users in the selected group ** */
        
        $userStr = ''; $headStr = ''; $addStr = '';
        if(isset($userType) && !empty($userType)){
            $name = $this->objLanguage->languageText('mod_etd_step'.$userType, 'etd');
            $headStr = '<h6>'.$name.'</h6>';
            
            if(isset($users) && !empty($users)){
                $userStr .= '<ul>';
                foreach($users as $item){
                    $remLink = '';
                    // Create link to remove user from the group
                    if(!($this->pkId == $item['user_id'])){
                        $linkArr = array('action' => 'saveconfig', 'mode' => 'saveusers', 'nextmode' => 'manageusers', 'save' => TRUE, 'type' => 'remove', 'id' => $item['user_id']);
                        $objLink = new link($this->uri($linkArr));
                        $objLink->link = $lbRemoveUser;
                        $remLink = '['.$objLink->show().']';
                    }
                
                    $userStr .= '<li>'.$item['surname'].', '.$item['firstname'].'&nbsp;&nbsp;'.$remLink.'</li>';
                }
                $userStr .= '</ul>';
            }else{
                $userStr .= '<p class = "noRecordsMessage">'.$lbNoUsers.'</p>';
            }
        
            /* ** Form to add users to the group ** */
        
            $addStr = '<p><b>'.$lbAddUser.'</b></p>';
            
            $objLabel = new label($lbSearchBy.': ', 'input_field');
            
            $objDrop = new dropdown('field');
            $objDrop->addOption('surname', $lbSurname);
            $objDrop->addOption('firstname', $lbFirst);
            
            $formStr = '<p>'.$objLabel->show().'&nbsp;&nbsp;'.$objDrop->show();
            
            $objInput = new textinput('name', '', '', 40);
            $objButton = new button('save', $btnSearch);
            $objButton->setToSubmit();
            $formStr .= '&nbsp;&nbsp;'.$objInput->show().'&nbsp;&nbsp;'.$objButton->show().'</p>';
            
            $objForm = new form('searchusers', $this->uri(array('action' => 'showconfig', 'mode' => 'searchusers')));
            $objForm->addToForm($formStr);
            $addStr .= $objForm->show();
            
            // Display results
            if(isset($this->results) && !empty($this->results)){
                
                if($this->results === FALSE){
                    $addStr .= '<p class = "noRecordsMessage">'.$noUsersFound.'</p>';
                }else{
                    $objTable = new htmltable();
                    $objTable->cellpadding = '5';
                    $objTable->cellspacing = '2';
                    
                    $objTable->row_attributes = " onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className='';\"";
                    foreach($this->results as $item){
                        $objCheck = new checkbox('users[]');
                        $objCheck->setValue($item['id']);
                        $objTable->addRow(array($item['surname'].', '.$item['firstname'], $objCheck->show()));
                    }
                    
                    $objButton = new button('save', $btnSave);
                    $objButton->setToSubmit();
                    $objTable->row_attributes = '';
                    $objTable->startRow();
                    $objTable->addCell('');
                    $objTable->addCell($objButton->show(), '55%');
                    $objTable->endRow();
                    
                    $formArr = array('action' => 'saveconfig', 'mode' => 'saveusers', 'nextmode' => 'manageusers', 'type' => 'add');
                    $objForm = new form('updateusers', $this->uri($formArr));
                    $objForm->addToForm($objTable->show());
                    $addStr .= $objForm->show();
                }
            }
        }
        
        /* ** Display page ** */
        
        $objTable = new htmltable();
        $objTable->cellpadding = '5';
        $objTable->cellspacing = '2';
        
        $objTable->startRow();
        $objTable->addCell($listStr);
        $objTable->addCell('', '3%');
        $objTable->addCell($dispStr, '50%');
        $objTable->endRow();
        
        $objTable->addRow(array($headStr));
        $objTable->addRow(array($addStr, '', $userStr));
        
        $str .= $objTable->show();
        
        return $str;
    }
    
    /**
    * Method to display a link to editing the faq
    *
    * @access private
    * @param bool $back Indicator for displaying the link back to configuration
    * @return string html
    */
    private function updateContent($back = FALSE)
    {
        $lbFaq = $this->objLanguage->languageText('phrase_frequentlyaskedquestions');
        $lbFooter = $this->objLanguage->languageText('phrase_footercontent');
        $lbIntro = $this->objLanguage->languageText('word_introduction');
        $lbConfig = $this->objLanguage->languageText('phrase_configuresystem');
        
        $objLink = new link($this->uri(array('action' => 'showconfig', 'mode' => 'showfaq')));
        $objLink->link = $lbFaq;
        $str = '<p><ul><li>'.$objLink->show().'</li>';
        
        $objLink = new link($this->uri(array('action' => 'showconfig', 'mode' => 'showintro')));
        $objLink->link = $lbIntro;
        $str .= '<li style="padding-top:5px;">'.$objLink->show().'</li>';
        
        $objLink = new link($this->uri(array('action' => 'showconfig', 'mode' => 'showfooter')));
        $objLink->link = $lbFooter;
        $str .= '<li style="padding-top:5px;">'.$objLink->show().'</li>';
        
        if($back){
            $objLink = new link($this->uri(array('action' => 'showconfig')));
            $objLink->link = $lbConfig;
            $str .= '<li style="padding-top:5px;">'.$objLink->show().'</li>';
        }
        
        $str .= '</ul></p>';
        
        return $str;
    }
    
    /**
    * Method to display the configurable parameters - institution information, copyright, embargoes, approval settings
    *
    * @access private
    * @return string html
    */
    private function showConfig($facultyMode = '', $userMode = '', $data = NULL)
    {
        $head = $this->objLanguage->languageText('phrase_configuresystem');
        $lbInstitution = $this->objLanguage->languageText('phrase_institutioninformation');
        $lbFaculty = $this->objLanguage->languageText('phrase_facultyinformation');
        $lbSubmission = $this->objLanguage->languageText('phrase_submissionprocess');
        $lbIntro = $this->objLanguage->languageText('phrase_editintroduction');
        $lbContent = $this->objLanguage->languageText('phrase_updatecontent');
        
        $this->objHead->str = $head;
        $this->objHead->type = 1;
        $str = $this->objHead->show();
        
        // Degrees / faculties / departments
        $str .= $this->objFeatureBox->show($lbFaculty, $this->getFaculty($facultyMode, $data));

        // Institution information
        $str .= $this->objFeatureBox->show($lbInstitution, $this->getInstitute());
        
        // Submission process
        $str .= $this->objFeatureBox->show($lbSubmission, $this->getSubmissionProcess($userMode, $data));
        
        // Introduction
        //$str .= $this->objFeatureBox->show($lbIntro, $this->editIntro());//$userMode, $data));
        
        // Content editing
        $str .= $this->objFeatureBox->show($lbContent, $this->updateContent());
        
        $str .= '<br />';
        return $str;
    }

    /**
    * Entry portal into the class
    *
    * @access public
    * @param string $mode The action to be taken
    * @return string html
    */
    public function show($mode)
    {
        $this->unsetSession('update_id');
        switch($mode){
            case 'permissions':
                break;
                
            /* ** Main config - institution info ** */
                
            case 'saveyear':
                $pvalue = $this->getParam('year');
                $this->objDbConfig->changeParam('ARCHIVE_START_YEAR', 'etd', $pvalue);
                return TRUE;
                break;
                
            case 'savecopyright':
                $copyId = $this->getParam('copyId');
                $this->dbCopyright->addCopyright($this->objUser->userId(), $copyId);
                return TRUE;
                break;
                
            /* ** Faculties, departments and degrees ** */
                
            case 'updatefaculty':
                $data = $this->dbDegrees->getList('faculty');
                return $this->showConfig('faculty', '', $data);
                
            case 'updatedepartment':
                $data = $this->dbDegrees->getList('department');
                return $this->showConfig('department', '', $data);
                
            case 'updatedegree':
                $data = $this->dbDegrees->getList('degree');
                return $this->showConfig('degree', '', $data);
                
            case 'saveupdate':
                $name = $this->getParam('name');
                $type = $this->getParam('type');
                $id = $this->getParam('id');
                $this->unsetSession('update_id');
                if(isset($id) && !empty($id)){
                    $this->dbDegrees->updateDB($id, $name, $type);
                }
                $this->dbDegrees->addItem($name, $type, $id);
                return TRUE;
                break;
                
            case 'editupdate':
                $type = $this->getParam('type');
                $id = $this->getParam('id');
                $this->setSession('update_id', $id);
                $data = $this->dbDegrees->getList($type);
                return $this->showConfig($type, '', $data);
                break;
                
            case 'deleteupdate':
                $id = $this->getParam('id');
                $this->dbDegrees->deleteItem($id);
                return TRUE;
                break;
                
            /* ** Submission process & manage users ** */
                
            case 'saveprocess':
                $type = $this->getParam('type');
                $stepId = $this->getParam('stepid');
                if($type == 'add'){
                    $this->dbProcess->addStep($stepId);
                }else if($type == 'remove'){
                    $this->dbProcess->removeStep($stepId);
                }
                break;
                
            case 'searchusers':
                $field = $this->getParam('field');
                $name = strtolower($this->getParam('name'));
                
                $this->results = FALSE;
                
                $fields = array('firstname', 'surname', 'id');
                $filter = " WHERE LOWER({$field}) LIKE '%$name%' ORDER BY surname";
                $data = $this->objUserDb->getUsers($fields, $filter);
                
                if(!empty($data)){
                    $this->results = $data;
                }
                
            case 'manageusers':
                $type = $this->getParam('type');
                $group = $this->getParam('group');
                
                if(isset($group) && !empty($group)){
                    $this->setSession('config_group', $group);
                    $this->setSession('config_type', $type);
                }else{
                    $type = $this->getSession('config_type');
                    $group = $this->getSession('config_group');
                }
                
                // get group id
                $groupId = $this->objGroups->getLeafId(array($group));
                if(isset($groupId) && !empty($groupId)){
                    $fields = 'firstname, surname, userid, user_id';
                    $filter = ' ORDER by surname ';
                    $users = $this->objGroups->getGroupUsers( $groupId, $fields, $filter);
                }else{
                    // create the group if it doesn't exist
                    $this->objGroups->addGroup($group, $group);
                    $users = array();
                }
                return $this->showConfig('', $type, $users);
                break;
                
            case 'saveusers':
                $type = $this->getParam('type');
                $group = $this->getSession('config_group');
                
                // get group id
                $groupId = $this->objGroups->getLeafId(array($group));
                if($type == 'add'){
                    $users = $this->getParam('users');
                    if(!empty($users)){
                        foreach($users as $item){
                            $this->objGroups->addGroupUser($groupId, $item);
                        }
                    }
                }else if($type == 'remove'){
                    // remove user from group
                    $userId = $this->getParam('id');
                    $this->objGroups->deleteGroupUser($groupId, $userId);
                }
                break;
                
            /* *** Content *** */
            
            case 'showintro':
                $lbIntro = $this->objLanguage->languageText('phrase_editintroduction');
                $lbContent = $this->objLanguage->languageText('phrase_updatecontent');
                $str = $this->objFeatureBox->show($lbContent, $this->updateContent(TRUE));
                $str .= $this->objFeatureBox->show($lbIntro, $this->editIntro());
                return $str;
            
            case 'saveintro':
                $id = $this->getParam('id');
                $this->dbIntro->addIntro($this->userId, $id);
                break;
                
            case 'showfooter':
                $lbFooter = $this->objLanguage->languageText('phrase_editfooter');
                $lbContent = $this->objLanguage->languageText('phrase_updatecontent');
                $str = $this->objFeatureBox->show($lbContent, $this->updateContent(TRUE));
                $str .= $this->objFeatureBox->show($lbFooter, $this->editFooter());
                return $str;
                
            case 'savefooter':
                $id = $this->getParam('id');
                $footer = $this->getParam('footer');
                $this->dbIntro->addContent($footer, 'footer', $this->userId, $id);
                break;
                
            case 'showfaq':
                $lbFaq = $this->objLanguage->languageText('phrase_editfaq');
                $lbContent = $this->objLanguage->languageText('phrase_updatecontent');
                $str = $this->objFeatureBox->show($lbContent, $this->updateContent(TRUE));
                $str .= $this->objFeatureBox->show($lbFaq, $this->editFaq());
                return $str;
                
            case 'savefaq':
                $id = $this->getParam('id');
                $faq = $this->getParam('faq');
                $this->dbIntro->addContent($faq, 'faq', $this->userId, $id);
                break;

            default:
                $this->unsetSession('config_type');
                $this->unsetSession('config_group');
                return $this->showConfig('');
        }
    }
}
?>
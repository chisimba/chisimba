<?php
/* ----------- examdisplay class extends object ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Display class for examiners module
* @author Kevin Cyster
*/

class examdisplay extends object
{
    /**
    * @var object $objLanguage: The language class of the language module
    * @access private
    */
    private $objLanguage;
     
    /**
    * @var object $objExamDb: The dbexams class in the examiners module
    * @access public
    */
    public $objExamDb;

    /**
    * @var object $objIcon: The geticon class in the htmlelements module
    * @access public
    */
    public $objIcon;

    /**
    * @var object $objDate: The dateandtime class in the utilities module
    * @access public
    */
    public $objDate;

    /**
    * @var object $objEditor: The htmlarea class in the htmlelements module
    * @access public
    */
    public $objEditor;

    /**
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * @var object $objGroup: The groupadminmodel class in the groupadmin module
    * @access public
    */
    public $objGroup;

    /**
    * @var string $filePath: The path to the subject files
    * @access public
    */
    public $filePath;

    /**
    * @var string $fileLink: The link to the subject files
    * @access public
    */
    public $fileLink;

    /**
    * @var string $pkId: The of the users record on the users table (NOT USERID)
    * @access public
    */
    public $pkId;

    /**
    * @var bool $isAdmin: TRUE if the user is in the Admin group FALSE if not
    * @access public
    */
    public $isAdmin;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {   
        // load html element classes
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('layer', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
		
        // system classes
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objExamDb = $this->getObject('dbexams', 'examiners');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objEditor = $this->newObject('htmlarea', 'htmlelements');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        $this->objUser = $this->getObject('user', 'security');

        $objUser = $this->getObject('user', 'security');
        $userId = $objUser->userId();
        $this->pkId = $objUser->PKId($userId);
        $this->isAdmin = $objUser->inAdminGroup($userId);

        $this->objGroup = $this->getObject('groupadminmodel', 'groupadmin');

        $objConfig = $this->getObject('altconfig', 'config');     
        $css = '<link id="calender_css" type="text/css" rel="stylesheet" href="'.$objConfig->getModuleURI().$this->getParam('module').'/resources/examiners.css" />';
        $this->appendArrayVar('headerParams', $css);

        $contentBasePath = $objConfig->getcontentBasePath();
        $this->filePath = $contentBasePath.'modules/examiners/';
        
        //$objConfig = $this->getObject('dbsysconfig', 'sysconfig');     
        $siteRoot = $objConfig->getsiteRoot();
        $contentPath = $objConfig->getcontentPath();
        $this->fileLink = $siteRoot.$contentPath.'modules/examiners/';
    }
    
	/**
	* Method to display the home page for the examiners module
	*
	* @access public
	* @return string $str: The output string
	*/
	public function showHome()
	{
        return 'you are not part of faculty administation';            
    }

	/**
	* Method to display the add/edit examiner page
	*
	* @access public
	* @param string $facId: The id of the faculty
	* @param string $depId: The id of the department
	* @param string $userId: The user id of the examiner
	* @return string $str: The output string
	*/
	public function showAddEditExaminer($facId, $depId, $userId)
	{
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $examiner = $this->objExamDb->getExaminerById($userId);
        if($examiner == FALSE){
            $id = '';
            $title = '';
            $name = '';
            $surname = '';
            $organisation = '';
            $email = '';
            $tel = '';
            $ext = '';
            $cell = '';
            $address = '';
        }else{
            $id = $examiner['id'];
            $title = $examiner['title'];
            $name = $examiner['first_name'];
            $surname = $examiner['surname'];
            $organisation = $examiner['organisation'];
            $email = $examiner['email_address'];
            $tel = $examiner['tel_no'];
            $ext = $examiner['extension'];
            $cell = $examiner['cell_no'];
            $address = $examiner['address'];
        }
        
        // set up text elements
        $lblSelect = $this->objLanguage->languageText('word_select');
        $lblSave = $this->objLanguage->languageText('word_save');
        $lblCancel = $this->objLanguage->languageText('word_cancel');        
        $lblMr = $this->objLanguage->languageText('word_mr');        
        $lblMiss = $this->objLanguage->languageText('word_miss');        
        $lblMrs = $this->objLanguage->languageText('word_mrs');        
        $lblMs = $this->objLanguage->languageText('word_ms');        
        $lblDr = $this->objLanguage->languageText('word_dr');        
        $lblProf = $this->objLanguage->languageText('word_prof');        
        $lblRev = $this->objLanguage->languageText('word_rev');        
        $lblHon = $this->objLanguage->languageText('word_hon');        
        $lblTitle = $this->objLanguage->languageText('word_title');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');        
        $lblOrg = $this->objLanguage->languageText('word_organisation');        
        $lblAddress = $this->objLanguage->languageText('word_address');        
        $lblEmail = $this->objLanguage->languageText('phrase_emailaddress');        
        $lblTel = $this->objLanguage->languageText('phrase_telephonenumber');        
        $lblCell = $this->objLanguage->languageText('phrase_mobilenumber');        
        $lblExt = $this->objLanguage->languageText('word_extension');
        $lblAdd = $this->objLanguage->languageText('mod_examiners_addexaminer', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editexaminer', 'examiners');        
        $lblNameRequired = $this->objLanguage->languageText('mod_examiners_requiredexaminername', 'examiners');        
        $lblSurnameRequired = $this->objLanguage->languageText('mod_examiners_requiredsurname', 'examiners');        
        $lblEmailRequired = $this->objLanguage->languageText('mod_examiners_requiredemail', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnexaminers', 'examiners');
        
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $lblHeading = $examiner ? $lblEdit : $lblAdd;
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
                
        // set up htmlelements
        $this->objDrop = new dropdown('title');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        $this->objDrop->addOption($lblMr, $lblMr.'&#160;');
        $this->objDrop->addOption($lblMrs, $lblMrs.'&#160;');
        $this->objDrop->addOption($lblMiss, $lblMiss.'&#160;');
        $this->objDrop->addOption($lblMs, $lblMs.'&#160;');
        $this->objDrop->addOption($lblDr, $lblDr.'&#160;');
        $this->objDrop->addOption($lblProf, $lblProf.'&#160;');
        $this->objDrop->addOption($lblRev, $lblRev.'&#160;');
        $this->objDrop->addOption($lblHon, $lblHon.'&#160;');
        $this->objDrop->setSelected($title);
        $drpTitle = $this->objDrop->show();
        
        $this->objInput = new textinput('name', $name, 'text', '66');
        $inpName = $this->objInput->show();

        $this->objInput = new textinput('surname', $surname, 'text', '66');
        $inpSurname = $this->objInput->show();

        $this->objInput = new textinput('org', $organisation, 'text', '66');
        $inpOrg = $this->objInput->show();

        $this->objInput = new textinput('email', $email, 'text', '40');
        $inpEmail = $this->objInput->show();

        $this->objInput = new textinput('tel', $tel);
        $inpTel = $this->objInput->show();

        $this->objInput = new textinput('ext', $ext);
        $inpExt = $this->objInput->show();

        $this->objInput = new textinput('cell', $cell);
        $inpCell = $this->objInput->show();

        $this->objText = new textarea('address', $address, '5', '50');
        $txtAddress = $this->objText->show();
        
        $this->objButton=new button('submit',$lblSave);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpading = '5';        
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblTitle.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($drpTitle, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblName.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpName, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblSurname.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpSurname, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblOrg.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpOrg, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblEmail.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpEmail, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblTel.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpTel, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblExt.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpExt, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblCell.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpCell, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblAddress.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($txtAddress, '', '', '', '', '');
        $this->objTable->endRow();        
        $tblDisplay = $this->objTable->show();
        
        // set up forms
        $this->objForm = new form('frmExaminers',$this->uri(array(
            'action' => 'save_examiner',
            'f' => $facId,
            'd' => $depId,
            'u' => $id,
        ), 'examiners'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $this->objForm->addRule('name', $lblNameRequired, 'required');
        $this->objForm->addRule('surname', $lblSurnameRequired, 'required');
        $this->objForm->addRule('email', $lblEmailRequired, 'required');        
        $frmSubmit = $this->objForm->show();
    
        $this->objForm = new form('frmCancel',$this->uri(array(
            'action' => 'examiners',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'examiners',
            'f' => $facId,
            'd' => $depId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmSubmit;
        $str .= $frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

	/**
	* Method to display a list of examiners
	*
	* @access public
	* @param string $facId: The id of the faculty
	* @param string $depId: The id of the department
	* @return string $str: The output string
	*/
	public function showExaminers($facId, $depId)
	{
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);
        
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $examiners = $this->objExamDb->getExaminersByDepartment($depId);

        // set up text elements
        $lblList = $this->objLanguage->languageText('mod_examiners_examinerlist', 'examiners');        
        $lblAdd = $this->objLanguage->languageText('mod_examiners_addexaminertitle', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editexaminertitle', 'examiners');        
        $lblDelete = $this->objLanguage->languageText('mod_examiners_deleteexaminertitle', 'examiners');        
        $lblConfirm = $this->objLanguage->languageText('mod_examiners_examinerconfirm', 'examiners');        
        $lblTitle = $this->objLanguage->languageText('word_title');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');        
        $lblOrg = $this->objLanguage->languageText('word_organisation');        
        $lblAddress = $this->objLanguage->languageText('word_address');        
        $lblEmail = $this->objLanguage->languageText('phrase_emailaddress');        
        $lblTel = $this->objLanguage->languageText('phrase_telephonenumber');        
        $lblCell = $this->objLanguage->languageText('phrase_mobilenumber');        
        $lblExt = $this->objLanguage->languageText('word_extension');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnexaminers', 'examiners');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_noexaminers', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returndepartment', 'examiners');
                
        // set up add examiner icon
        $this->objIcon->title = $lblAdd;
        $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
            'action' => 'examiner',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();
        
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblList.'&#160;'.$icoAdd;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "examinerList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblTitle, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'header', '');
        $this->objTable->addCell($lblOrg, '', '', '', 'header', '');
        $this->objTable->addCell($lblEmail, '', '', '', 'header', '');
        $this->objTable->addCell($lblTel, '', '', '', 'header', '');
        $this->objTable->addCell($lblExt, '', '', '', 'header', '');
        $this->objTable->addCell($lblCell, '', '', '', 'header', '');
        $this->objTable->addCell($lblAddress, '', '', '', 'header', '');
        $this->objTable->addCell('', '5%', '', '', 'header', '');
        $this->objTable->endRow();
        if($examiners == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="10"');
            $this->objTable->endRow();
        }else{
            foreach($examiners as $examiner){
                // set up edit icon
                $this->objIcon->title = $lblEdit;
                $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'examiner',
                    'f' => $facId,
                    'd' => $depId,
                    'u' => $examiner['id'],
                ), 'examiners'));
                
                // set up delete icon
                $deleteArray = array(
                    'action' => 'delete_examiner',
                    'u' => $examiner['id'],
                );
                $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'examiners', $lblConfirm);
                
                $this->objTable->startRow();
                $this->objTable->addCell($examiner['title'], '', 'top', '', '', '');
                $this->objTable->addCell($examiner['first_name'], '', 'top', '', '', '');
                $this->objTable->addCell($examiner['surname'], '', 'top', '', '', '');
                $this->objTable->addCell($examiner['organisation'], '', 'top', '', '', '');
                $this->objTable->addCell($examiner['email_address'], '', 'top', '', '', '');
                $this->objTable->addCell($examiner['tel_no'], '', 'top', '', '', '');
                $this->objTable->addCell($examiner['extension'], '', 'top', '', '', '');
                $this->objTable->addCell('<nobr>'.$examiner['cell_no'].'</nobr>', '', 'top', '', '', '');
                $this->objTable->addCell(nl2br($examiner['address']), '', 'top', '', '', '');
                $this->objTable->addCell($icoEdit.$icoDelete, '', '', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'departments',
            'f' => $facId,
            'd' => $depId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $tblDisplay;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

	/**
	* Method to display the add/edit faculty page
	*
	* @access public
	* @param string $facId: The id of the faculty
	* @return string $str: The output string
	*/
	public function showAddEditFaculty($facId)
	{
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        if($faculty == FALSE){
            $id = '';
            $name = '';
        }else{
            $id = $faculty['id'];
            $name = $faculty['faculty_name'];
        }
        
        // set up text elements
        $lblSave = $this->objLanguage->languageText('word_save');
        $lblCancel = $this->objLanguage->languageText('word_cancel');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblAdd = $this->objLanguage->languageText('mod_examiners_addfaculty', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editfaculty', 'examiners');        
        $lblNameRequired = $this->objLanguage->languageText('mod_examiners_requiredfacultyname', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnfaculty', 'examiners');
        
        // set up page heading
        $lblHeading = $faculty ? $lblEdit : $lblAdd;
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();
                
        // set up htmlelements
        $this->objInput = new textinput('name', $name, 'text', '66');
        $inpName = $this->objInput->show();

        $this->objButton=new button('submit',$lblSave);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpading = '5';        
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblName.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpName, '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();
        
        // set up forms
        $this->objForm = new form('frmFaculties',$this->uri(array(
            'action' => 'save_faculty',
            'f' => $id,
        ), 'examiners'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $this->objForm->addRule('name', $lblNameRequired, 'required');
        $frmSubmit = $this->objForm->show();
    
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'faculties',
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'faculties',
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmSubmit;
        $str .= $frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

	/**
	* Method to display the add/edit department page
	*
	* @access public
	* @param string $facId: The id of the faculty
	* @param string $depId: The id of the department
	* @return string $str: The output string
	*/
	public function showAddEditDepartment($facId, $depId)
	{
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        if($department == FALSE){
            $id = '';
            $name = '';
        }else{
            $id = $department['id'];
            $name = $department['department_name'];
        }
        
        // set up text elements
        $lblSave = $this->objLanguage->languageText('word_save');
        $lblCancel = $this->objLanguage->languageText('word_cancel');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblAdd = $this->objLanguage->languageText('mod_examiners_adddepartment', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editdepartment', 'examiners');        
        $lblNameRequired = $this->objLanguage->languageText('mod_examiners_requireddepartmentname', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returndepartment', 'examiners');
        
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $lblHeading = $department ? $lblEdit : $lblAdd;
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
                
        // set up htmlelements
        $this->objInput = new textinput('name', $name, 'text', '66');
        $inpName = $this->objInput->show();

        $this->objButton=new button('submit',$lblSave);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpading = '5';        
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblName.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpName, '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();
        
        // set up forms
        $this->objForm = new form('frmDepartments',$this->uri(array(
            'action' => 'save_department',
            'f' => $facId,
            'd' => $id,
        ), 'examiners'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $this->objForm->addRule('name', $lblNameRequired, 'required');
        $frmSubmit = $this->objForm->show();
    
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'departments',
            'f' => $facId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'departments',
            'f' => $facId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmSubmit;
        $str .= $frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

	/**
	* Method to display a list of faculties
	*
	* @access public
	* @return string $str: The output string
	*/
	public function showFaculties()
	{
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);
        
        // get data
        $faculties = $this->objExamDb->getAllFaculties();

        // set up text elements
        $lblExaminerList = $this->objLanguage->languageText('mod_examiners_examinerlist', 'examiners');        
        $lblList = $this->objLanguage->languageText('mod_examiners_facultylist', 'examiners');        
        $lblAdd = $this->objLanguage->languageText('mod_examiners_addfacultytitle', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editfacultytitle', 'examiners');        
        $lblDelete = $this->objLanguage->languageText('mod_examiners_deletefacultytitle', 'examiners');        
        $lblConfirm = $this->objLanguage->languageText('mod_examiners_facultyconfirm', 'examiners');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnfaculty', 'examiners');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nofaculties', 'examiners');       
        $lblHeads = $this->objLanguage->languageText('mod_examiners_facultyheadlist', 'examiners');
                
        // set up add examiner icon
        $this->objIcon->title = $lblAdd;
        $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
            'action' => 'faculty',
        ), 'examiners'));

        // set up page heading
        $this->objHeading = new htmlHeading();
        if($this->isAdmin){
            $this->objHeading->str = $lblList.'&#160;'.$icoAdd;
        }else{
            $this->objHeading->str = $lblList;
        }
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "facultyList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($faculties == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="2"');
            $this->objTable->endRow();
        }else{
            foreach($faculties as $faculty){
                $userLevel = $this->userLevel($faculty['id']);
                
                // set up groups icon
                $this->objIcon->title = $lblHeads;
                $icoGroups = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'fac_heads',
                    'f' => $faculty['id'],
                ), 'examiners'), 'groups');
                
                // set up edit icon
                $this->objIcon->title = $lblEdit;
                $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'faculty',
                    'f' => $faculty['id'],
                ), 'examiners'));
                
                // set up delete icon
                $deleteArray = array(
                    'action' => 'delete_faculty',
                    'f' => $faculty['id'],
                );
                $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'examiners', $lblConfirm);
                
                if($userLevel != FALSE or $this->isAdmin){
                    // set up department link
                    $this->objLink = new link($this->uri(array(
                        'action' => 'departments',
                        'f' => $faculty['id'],
                    ),'examiners'));
                    $this->objLink->link = $faculty['faculty_name'];
                    $lnkName = $this->objLink->show();
                }else{
                    $lnkName = $faculty['faculty_name'];
                }

                $this->objTable->startRow();
                $this->objTable->addCell($lnkName, '', 'top', '', '', '');
                if($this->isAdmin){
                    $this->objTable->addCell($icoGroups.'&#160;'.$icoEdit.'&#160;'.$icoDelete, '', '', '', '', '');
                }else{
                    $this->objTable->addCell('', '', '', '', '', '');
                }
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
                
        // set up page
        $str = $heading;
        $str .= $tblDisplay;
        //$str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

	/**
	* Method to display a list of departments
	*
	* @access public
	* @param string $facId: The id of the faculty
	* @param bool $download: TRUE if a download is available | FALSE if not
	* @return string $str: The output string
	*/
	public function showDepartments($facId, $download = 'FALSE')
	{
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);
        
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $departments = $this->objExamDb->getAllDepartmentsPerFaculty($facId);
        $userLevel = $this->userLevel($facId);

        // set up text elements
        $lblExaminerList = $this->objLanguage->languageText('mod_examiners_examinerlist', 'examiners');        
        $lblList = $this->objLanguage->languageText('mod_examiners_departmentlist', 'examiners');        
        $lblAdd = $this->objLanguage->languageText('mod_examiners_adddepartmenttitle', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editdepartmenttitle', 'examiners');        
        $lblDelete = $this->objLanguage->languageText('mod_examiners_deletedepartmenttitle', 'examiners');        
        $lblConfirm = $this->objLanguage->languageText('mod_examiners_departmentconfirm', 'examiners');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nodepartments', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnfaculty', 'examiners');
        $lblFaculty = $this->objLanguage->languageText('word_faculty');
        $lblHeads = $this->objLanguage->languageText('mod_examiners_departmentheadlist', 'examiners');
        $lblAdmin = $this->objLanguage->languageText('mod_examiners_adminlist', 'examiners');
        $lblExport = $this->objLanguage->languageText('mod_examiners_exporttitle', 'examiners');        
        $lblDownload = $this->objLanguage->languageText('mod_examiners_downloadtitle', 'examiners');        
           
        // set up add examiner icon
        $this->objIcon->title = $lblAdd;
        $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
            'action' => 'department',
            'f' => $facId,
        ), 'examiners'));

        // set up export icon
        if($departments == FALSE){
            $icoExport = '';
        }else{
            $this->objIcon->title = $lblExport;
            $icoExport = $this->objIcon->getLinkedIcon($this->uri(array(
                'action' => 'fac_export',
                'f' => $facId,
            ), 'examiners'), 'exportcvs');
        }
                
        // set up download icon
        if($download == 'FALSE'){
            $link = '';
        }else{
            $this->objIcon->title = $lblDownload;
            $this->objIcon->setIcon('download');
            $icoDownload = $this->objIcon->show();

            $file = glob($this->filePath.$facId.'.csv');
            if(!empty($file)){
                $this->objLink = new link($this->uri(array(
                    'action' => 'fac_download',
                    'f' => $facId,
                ), 'examiners'));
                $this->objLink->link = $icoDownload;
                $link = $this->objLink->show();
            }else{
                $link = '';
            }
        }

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        if($this->isAdmin or $userLevel == 'facHead'){
            $this->objHeading->str = $lblList.'&#160;'.$icoAdd.'&#160;'.$icoExport.'&#160;'.$link;
        }else{
            $this->objHeading->str = $lblList;
        }
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "departmentList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell('', '15%', '', '', 'header', '');
        $this->objTable->endRow();
        if($departments == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="2"');
            $this->objTable->endRow();
        }else{
            foreach($departments as $department){
                $userLevel = $this->userLevel($facId, $department['id']);

                // set up groups icon
                $this->objIcon->title = $lblHeads;
                $icoGroups = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'dep_heads',
                    'f' => $facId,
                    'd' => $department['id'],
                ), 'examiners'), 'groups');
                
                // set up groups icon
                $this->objIcon->title = $lblAdmin;
                $icoAdmin = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'dep_admin',
                    'f' => $facId,
                    'd' => $department['id'],
                ), 'examiners'), 'managing_users');
                
                // set up edit icon
                $this->objIcon->title = $lblEdit;
                $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'department',
                    'f' => $facId,
                    'd' => $department['id'],
                ), 'examiners'));
                
                // set up delete icon
                $deleteArray = array(
                    'action' => 'delete_department',
                    'f' => $facId,
                    'd' => $department['id'],
                );
                $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'examiners', $lblConfirm);
                
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead' or $userLevel == 'depAdmin'){
                    // set up subject link
                    $this->objLink = new link($this->uri(array(
                        'action' => 'subjects',
                        'f' => $facId,
                        'd' => $department['id'],
                    ),'examiners'));
                    $this->objLink->link = $department['department_name'];
                    $lnkName = $this->objLink->show();
                }else{
                    $lnkName = $department['department_name'];
                }

                // set up examiners icon
                $this->objIcon->title = $lblExaminerList;
                $this->objIcon->extra = '';
                $icoExaminers = $this->objIcon->getLinkedIcon($this->uri(array(
                    'action' => 'examiners',
                    'f' => $facId,
                    'd' => $department['id'],
                )), 'customerdetails');

                $this->objTable->startRow();
                $this->objTable->addCell($lnkName, '', 'top', '', '', '');
                if($this->isAdmin or $userLevel == 'facHead'){
                    $this->objTable->addCell($icoGroups.'&#160;'.$icoAdmin.'&#160;'.$icoExaminers.'&#160;'.$icoEdit.'&#160;'.$icoDelete, '', '', '', '', '');
                }elseif($userLevel == 'depHead'){
                    $this->objTable->addCell($icoAdmin.'&#160;'.$icoExaminers, '', '', '', '', '');
                }elseif($userLevel != FALSE){
                    $this->objTable->addCell($icoExaminers, '', '', '', '', '');
                }else{
                    $this->objTable->addCell('', '', '', '', '', '');
                }
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'faculties',
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $tblDisplay;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

	/**
	* Method to display the add/edit subject page
	*
	* @access public
	* @param string $facId: The id of the faculty
	* @param string $depId: The id of the department
	* @param string $subjId: The id of the subject
	* @return string $str: The output string
	*/
	public function showAddEditSubject($facId, $depId, $subjId)
	{
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $subject = $this->objExamDb->getSubjectById($subjId);
        $department = $this->objExamDb->getDepartmentById($depId);
        if($subject == FALSE){
            $id = '';
            $code = '';
            $name = '';
            $level = '1';
            $status = '1';
            $style = 'style="display: none;"';
            $year = '';
        }else{
            $id = $subject['id'];
            $code = $subject['course_code'];
            $name = $subject['course_name'];
            $level = $subject['course_level'];
            $status = $subject['course_status'];
            if($status == 1){
                $style = 'style="display: none" ';
            }else{
                $style = '';
            }
            $year = $subject['last_active_year'];
        }
        
        // set up text elements
        $lblSelect = $this->objLanguage->languageText('word_select');
        $lblSave = $this->objLanguage->languageText('word_save');
        $lblCancel = $this->objLanguage->languageText('word_cancel');        
        $lblCode = $this->objLanguage->languageText('word_code');        
        $lblName = $this->objLanguage->languageText('word_name');
        $lblLevel = $this->objLanguage->languageText('word_level');
        $lblStatus = $this->objLanguage->languageText('word_status');        
        $lblInactive = $this->objLanguage->languageText('word_inactive');
        $lblActive = $this->objLanguage->languageText('word_active');
        $lblUndergraduate = $this->objLanguage->languageText('word_undergraduate');
        $lblPostgraduate = $this->objLanguage->languageText('word_postgraduate');
        $lblAdd = $this->objLanguage->languageText('mod_examiners_addsubject', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editsubject', 'examiners');        
        $lblNameRequired = $this->objLanguage->languageText('mod_examiners_requiredsubjectname', 'examiners');        
        $lblCodeRequired = $this->objLanguage->languageText('mod_examiners_requiredsubjectcode', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnsubject', 'examiners');
        $lblFile = $this->objLanguage->languageText('word_file');
        $lblYear = $this->objLanguage->languageText('word_year');
        
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $lblHeading = $subject ? $lblEdit : $lblAdd;
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblHeading;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
                
        // set up htmlelements
        $this->objInput = new textinput('code', $code, 'text', '');
        $inpCode = $this->objInput->show();

        $this->objInput = new textinput('name', $name, 'text', '66');
        $inpName = $this->objInput->show();
        
        $this->objDrop = new dropdown('level');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        $this->objDrop->addOption('1', $lblUndergraduate.'&#160;');
        $this->objDrop->addOption('2', $lblPostgraduate.'&#160;');
        $this->objDrop->setSelected($level);
        $drpLevel = $this->objDrop->show();
        
        $this->objDrop = new dropdown('status');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        $this->objDrop->addOption('1', $lblActive.'&#160;');
        $this->objDrop->addOption('2', $lblInactive.'&#160;');
        $this->objDrop->setSelected($status);
        $this->objDrop->extra = 'onchange="if(this.value == \'1\'){Element.hide(\'year\');}else{Element.show(\'year\');}adjustLayout();"';
        $drpStatus = $this->objDrop->show();
        
        $this->objDrop = new dropdown('y');
        for($loop = date('Y') + 1; $loop >= 2006; $loop--){
            $this->objDrop->addOption($loop, $loop.'&#160;');
        }
        $this->objDrop->setSelected($year);
        $drpYear = $this->objDrop->show();
        
        $this->objInput = new textinput('file', '', 'file', '66');
        $inpFile = $this->objInput->show();
        
        $this->objButton=new button('submit',$lblSave);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpading = '5';        
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblCode.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpCode, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblName.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpName, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblLevel.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($drpLevel, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblStatus.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($drpStatus, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = $style.'id="year"';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblYear.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($drpYear, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->row_attributes = '';
        $this->objTable->startRow();
        $this->objTable->addCell('<b>'.$lblFile.'&#160;&#58;</b>', '', '', '', '', '');
        $this->objTable->addCell($inpFile, '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();
        
        // set up forms
        $this->objForm = new form('frmSubjects',$this->uri(array(
            'action' => 'save_subject',
            'f' => $facId,
            's' => $id,
            'd' => $depId,
        ), 'examiners'));
        $this->objForm->extra = ' enctype="multipart/form-data"';    
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $this->objForm->addRule('code', $lblCodeRequired, 'required');
        $this->objForm->addRule('name', $lblNameRequired, 'required');
        $frmSubmit = $this->objForm->show();
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'subjects',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'subjects',
            'f' => $facId,
            'd' => $depId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmSubmit;
        $str .= $frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

	/**
	* Method to display a list of departments
	*
	* @access public
	* @param string $facId: The id of the faculty
	* @param string $depId: The id of the department
	* @param bool $download: TRUE if a download is available | FALSE if not
	* @return string $str: The output string
	*/
	public function showSubjects($facId, $depId, $download = 'FALSE')
	{
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);
        
        // get data
        $subjects = $this->objExamDb->getSubjectsByDepartment($depId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $faculty = $this->objExamDb->getFacultyById($facId);
        $userLevel = $this->userLevel($facId, $depId);
        
        // set up text elements
        $lblList = $this->objLanguage->languageText('mod_examiners_subjectlist', 'examiners');        
        $lblAdd = $this->objLanguage->languageText('mod_examiners_addsubjecttitle', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editsubjecttitle', 'examiners');        
        $lblDelete = $this->objLanguage->languageText('mod_examiners_deletesubjecttitle', 'examiners');        
        $lblConfirm = $this->objLanguage->languageText('mod_examiners_subjectconfirm', 'examiners');        
        $lblCode = $this->objLanguage->languageText('word_code');        
        $lblName = $this->objLanguage->languageText('word_name');
        $lblLevel = $this->objLanguage->languageText('word_level');
        $lblStatus = $this->objLanguage->languageText('word_status');
        $lblInactive = $this->objLanguage->languageText('word_inactive');
        $lblActive = $this->objLanguage->languageText('word_active');
        $lblUndergraduate = $this->objLanguage->languageText('word_undergraduate');
        $lblPostgraduate = $this->objLanguage->languageText('word_postgraduate');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnsubject', 'examiners');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nosubjects', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returndepartment', 'examiners');
        $lblExaminerList = $this->objLanguage->languageText('mod_examiners_examinerlist', 'examiners');        
        $lblExport = $this->objLanguage->languageText('mod_examiners_exporttitle', 'examiners');        
        $lblDownload = $this->objLanguage->languageText('mod_examiners_downloadtitle', 'examiners');        
                
        // set up add examiner icon
        $this->objIcon->title = $lblAdd;
        $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
            'action' => 'subject',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));

        // set up export icon
        if($subjects == FALSE){
            $icoExport = '';
        }else{
            $this->objIcon->title = $lblExport;
            $icoExport = $this->objIcon->getLinkedIcon($this->uri(array(
                'action' => 'dep_export',
                'f' => $facId,
                'd' => $depId,
            ), 'examiners'), 'exportcvs');
        }
                
        // set up download icon
        if($download == 'FALSE'){
            $link = '';
        }else{
            $this->objIcon->title = $lblDownload;
            $this->objIcon->setIcon('download');
            $icoDownload = $this->objIcon->show();

            $file = glob($this->filePath.$depId.'.csv');
            if(!empty($file)){
                $this->objLink = new link($this->uri(array(
                    'action' => 'dep_download',
                    'f' => $facId,
                    'd' => $depId,
                ), 'examiners'));
                $this->objLink->link = $icoDownload;
                $link = $this->objLink->show();
            }else{
                $link = '';
            }
        }

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();
        
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        $this->objHeading = new htmlHeading();
        if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
            $this->objHeading->str = $lblList.'&#160;'.$icoAdd.'&#160;'.$icoExport.'&#160;'.$link;
        }else{
            $this->objHeading->str = $lblList;            
        }
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "subjectList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblCode, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblLevel, '', '', '', 'header', '');
        $this->objTable->addCell($lblStatus, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($subjects == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            foreach($subjects as $subject){
                // get file
                $file = glob($this->filePath.$subject['id'].'.*');
                if(!empty($file)){
                    $this->objLink = new link($this->uri(array(
                        'action' => 'download',
                        'f' => $facId,
                        'd' => $depId,
                        'file' => basename($file[0]),
                    ), 'examiners'));
                    $this->objLink->link = $subject['course_code'];
                    $code = $this->objLink->show();
                }else{
                    $code = $subject['course_code'];
                }
                
                // set up edit icon
                $this->objIcon->title = $lblEdit;
                $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
                    'action' => 'subject',
                    'f' => $facId,
                    's' => $subject['id'],
                    'd' => $subject['dep_id'],
                ), 'examiners'));
                
                // set up delete icon
                $deleteArray = array(
                    'action' => 'delete_subject',
                    'f' => $facId,
                    's' => $subject['id'],
                    'd' => $subject['dep_id'],
                );
                $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'examiners', $lblConfirm);
                
                // set up examiners link
                $this->objLink = new link($this->uri(array(
                    'action' => 'matrix',
                    'f' => $facId,
                    'd' => $department['id'],
                    's' => $subject['id'],
                ),'examiners'));
                $this->objLink->link = $subject['course_name'];
                $lnkName = $this->objLink->show();

                if($subject['course_level'] == 1){
                    $level = $lblUndergraduate;
                }else{
                    $level = $lblPostgraduate;
                }
                
                if($subject['course_status'] == 1){
                    $status = $lblActive;
                }else{
                    $status = '<b>'.$lblInactive.'</b>';
                }

                $this->objTable->startRow();
                $this->objTable->addCell($code, '', 'top', '', '', '');
                $this->objTable->addCell($lnkName, '', 'top', '', '', '');
                $this->objTable->addCell($level, '', 'top', '', '', '');
                $this->objTable->addCell($status, '', 'top', '', '', '');
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
                    $this->objTable->addCell($icoEdit.'&#160;'.$icoDelete, '', '', '', '', '');
                }else{
                    $this->objTable->addCell('', '', '', '', '', '');                    
                }
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'departments',
            'f' => $facId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $tblDisplay;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to show the examiner matrix
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @param string $subjId: The id of the subject
	* @return string $str: The output string
	*/
	public function showMatrix($facId, $depId, $subjId, $year)
	{
        // get data
        $year = (isset($year) && !empty($year)) ? $year : date('Y') + 1;   
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $subject = $this->objExamDb->getSubjectById($subjId);
        $matrix = $this->objExamDb->getMatrixByYear($facId, $depId, $subjId, $year);

        // set up text elements
        $lblMatrix = $this->objLanguage->languageText('mod_examiners_matrix', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnsubject', 'examiners');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nomatrix', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnsubject', 'examiners');
        $lblFirst = $this->objLanguage->languageText('mod_examiners_first', 'examiners');
        $lblSecond = $this->objLanguage->languageText('mod_examiners_second', 'examiners');
        $lblModerate = $this->objLanguage->languageText('mod_examiners_moderator', 'examiners');
        $lblAlternate = $this->objLanguage->languageText('mod_examiners_alternate', 'examiners');
        $lblRemark = $this->objLanguage->languageText('mod_examiners_remark', 'examiners');
        $lblAdd = $this->objLanguage->languageText('mod_examiners_addmatrixtitle', 'examiners');        
        $lblEdit = $this->objLanguage->languageText('mod_examiners_editmatrixtitle', 'examiners');        
        $lblDelete = $this->objLanguage->languageText('mod_examiners_deletematrixtitle', 'examiners');        
        $lblConfirm = $this->objLanguage->languageText('mod_examiners_matrixconfirm', 'examiners');        

        // set up add add icon
        $this->objIcon->title = $lblAdd;
        $icoAdd = $this->objIcon->getAddIcon($this->uri(array(
            'action' => 'edit_matrix',
            'f' => $facId,
            'd' => $depId,
            's' => $subjId,
            'y' => $year,
        ), 'examiners'));

        // set up edit icon
        $this->objIcon->title = $lblEdit;
        $icoEdit = $this->objIcon->getEditIcon($this->uri(array(
            'action' => 'edit_matrix',
            'f' => $facId,
            'd' => $depId,
            's' => $subjId,
            'y' => $year,
        ), 'examiners'));
                
        // set up delete icon
        $deleteArray = array(
            'action' => 'delete_matrix',
            'f' => $facId,
            'd' => $depId,
            's' => $subjId,
            'y' => $year,
        );
        $icoDelete = $this->objIcon->getDeleteIconWithConfirm('', $deleteArray, 'examiners', $lblConfirm);
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();
                
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
                
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $subject['course_code'].'&#160;&#58;&#160;'.$subject['course_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        if($matrix == FALSE){
            $this->objHeading->str = $lblMatrix.'&#160;'.$icoAdd;            
        }else{
            $this->objHeading->str = $lblMatrix.'&#160;'.$icoEdit.'&#160;'.$icoDelete;
        }
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        // set up htmlelements
        $this->objDrop = new dropdown('y');
        for($loop = date('Y') + 1; $loop >= 2006; $loop--){
            $this->objDrop->addOption($loop, $loop.'&#160;');
        }
        $this->objDrop->setSelected($year);
        $this->objDrop->extra = 'onchange="javascript:$(\'form_frmMatrix\').submit();"';
        $drpYear = $this->objDrop->show();

        // set up form
        $this->objForm = new form('frmMatrix',$this->uri(array(
            'action' => 'matrix',
            'f' => $facId,
            'd' => $depId,
            's' => $subjId,
        ), 'examiners'));
        $this->objForm->addToForm($drpYear);
        $frmYear = $this->objForm->show();
    
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';        
        $this->objTable->cellspacing = '2';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblFirst, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblSecond, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblModerate, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblAlternate, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblRemark, '20%', '', '', 'header', '');
        $this->objTable->endRow();
        if($matrix == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            $this->objTable->startRow();
            foreach($matrix as $record){
                $user = $record['title'];
                $user .= '&#160;'.$record['first_name'];
                $user .= '&#160;'.$record['surname'].'<br />';
                $user .= isset($record['organisation']) ? $record['organisation'].'<br />' : '';
                $user .= isset($record['email_address']) ? $record['email_address'].'<br />' : '';
                $user .= isset($record['tel_no']) ? $record['tel_no'].'<br />' : '';
                $user .= isset($record['extension']) ? 'Ext&#160;&#58;'.$record['extension'].'<br />' : '';
                $user .= isset($record['cell_no']) ? $record['cell_no'].'<br />' : '';
                $user .= isset($record['address']) ? nl2br($record['address']) : '';
                $this->objTable->addCell($user, '', 'top', '', '', '');
            }
            $this->objTable->endRow();
            $this->objTable->startRow();
            foreach($matrix as $record){
                $user = isset($record['remarks']) ? nl2br($record['remarks']) : '';
                $this->objTable->addCell($user, '', '', '', '', '');
            }
            $this->objTable->endRow();
        }
        $tblDisplay = $this->objTable->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'subjects',
            'f' => $facId,
            'd' => $depId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmYear;
        $str .= $tblDisplay;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

    /**
    * Method to show the examiner matrix
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @param string $subjId: The id of the subject
	* @return string $str: The output string
	*/
	public function showEditMatrix($facId, $depId, $subjId, $year)
	{
        // get data
        $year = (isset($year) && !empty($year)) ? $year : date('Y');   
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $subject = $this->objExamDb->getSubjectById($subjId);
        $matrix = $this->objExamDb->getMatrixByYear($facId, $depId, $subjId, $year);
        $first = '';
        $firstText = '';
        $second = '';
        $secondText = '';
        $moderate = '';
        $moderateText = '';
        $alternate = '';
        $alternateText = '';
        $remarking = '';
        $remarkingText = '';
        if($matrix != FALSE){
            if($matrix[0] != FALSE){
                $first = $matrix[0]['exam_id'];
                $firstText = $matrix[0]['remarks'];
            }
            if($matrix[1] != FALSE){
                $second = $matrix[1]['exam_id'];
                $secondText = $matrix[1]['remarks'];
            }
            if($matrix[2] != FALSE){
                $moderate = $matrix[2]['exam_id'];
                $moderateText = $matrix[2]['remarks'];
            }
            if($matrix[3] != FALSE){
                $alternate = $matrix[3]['exam_id'];
                $alternateText = $matrix[3]['remarks'];
            }
            if($matrix[4] != FALSE){
                $remarking = $matrix[4]['exam_id'];
                $remarkingText = $matrix[4]['remarks'];
            }
        }
        $examiners = $this->objExamDb->getExaminersByDepartment($depId);

        // set up text elements
        $lblEdit = $this->objLanguage->languageText('word_edit');
        $lblMatrix = $this->objLanguage->languageText('mod_examiners_matrix', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnsubject', 'examiners');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nomatrix', 'examiners');        
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnmatrix', 'examiners');
        $lblFirst = $this->objLanguage->languageText('mod_examiners_first', 'examiners');
        $lblSecond = $this->objLanguage->languageText('mod_examiners_second', 'examiners');
        $lblModerate = $this->objLanguage->languageText('mod_examiners_moderator', 'examiners');
        $lblAlternate = $this->objLanguage->languageText('mod_examiners_alternate', 'examiners');
        $lblRemarking = $this->objLanguage->languageText('mod_examiners_remark', 'examiners');
        $lblSelect = $this->objLanguage->languageText('word_select');
        $lblSave = $this->objLanguage->languageText('word_save');
        $lblCancel = $this->objLanguage->languageText('word_cancel');        

        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
                
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $subject['course_code'].'&#160;&#58;&#160;'.$subject['course_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblEdit.'&#160;'.strtolower($lblMatrix);
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $year;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up htmlelements
        $this->objDrop = new dropdown('first');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        if($examiners != FALSE){
            foreach($examiners as $examiner){
                $name = $examiner['title'].'&#160;'.$examiner['first_name'].'&#160;'.$examiner['surname'].'&#160;';
                $this->objDrop->addOption($examiner['id'], $name);
            }
        }
        $this->objDrop->setSelected($first);
        $drpFirst = $this->objDrop->show();

        $this->objText = new textarea('text_first', $firstText, '5', '');
        $txtFirst = $this->objText->show();

        $this->objDrop = new dropdown('second');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        if($examiners != FALSE){
            foreach($examiners as $examiner){
                $name = $examiner['title'].'&#160;'.$examiner['first_name'].'&#160;'.$examiner['surname'].'&#160;';
                $this->objDrop->addOption($examiner['id'], $name);
            }
        }
        $this->objDrop->setSelected($second);
        $drpSecond = $this->objDrop->show();

        $this->objText = new textarea('text_second', $secondText, '5', '');
        $txtSecond = $this->objText->show();

        $this->objDrop = new dropdown('moderate');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        if($examiners != FALSE){
            foreach($examiners as $examiner){
                $name = $examiner['title'].'&#160;'.$examiner['first_name'].'&#160;'.$examiner['surname'].'&#160;';
                $this->objDrop->addOption($examiner['id'], $name);
            }
        }
        $this->objDrop->setSelected($moderate);
        $drpModerate = $this->objDrop->show();

        $this->objText = new textarea('text_moderate', $moderateText, '5', '');
        $txtModerate = $this->objText->show();

        $this->objDrop = new dropdown('alternate');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        if($examiners != FALSE){
            foreach($examiners as $examiner){
                $name = $examiner['title'].'&#160;'.$examiner['first_name'].'&#160;'.$examiner['surname'].'&#160;';
                $this->objDrop->addOption($examiner['id'], $name);
            }
        }
        $this->objDrop->setSelected($alternate);
        $drpAlternate = $this->objDrop->show();

        $this->objText = new textarea('text_alternate', $alternateText, '5', '');
        $txtAlternate = $this->objText->show();

        $this->objDrop = new dropdown('remarking');
        $this->objDrop->addOption('', $lblSelect.'&#160;');
        if($examiners != FALSE){
            foreach($examiners as $examiner){
                $name = $examiner['title'].'&#160;'.$examiner['first_name'].'&#160;'.$examiner['surname'].'&#160;';
                $this->objDrop->addOption($examiner['id'], $name);
            }
        }
        $this->objDrop->setSelected($remarking);
        $drpRemarking = $this->objDrop->show();

        $this->objText = new textarea('text_remarking', $remarkingText, '5', '');
        $txtRemarking = $this->objText->show();

        $this->objButton=new button('submit',$lblSave);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';        
        $this->objTable->cellspacing = '2';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblFirst, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblSecond, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblModerate, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblAlternate, '20%', '', '', 'header', '');
        $this->objTable->addCell($lblRemarking, '20%', '', '', 'header', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($drpFirst, '', '', '', '', '');
        $this->objTable->addCell($drpSecond, '', '', '', '', '');
        $this->objTable->addCell($drpModerate, '', '', '', '', '');
        $this->objTable->addCell($drpAlternate, '', '', '', '', '');
        $this->objTable->addCell($drpRemarking, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($txtFirst, '', '', '', '', '');
        $this->objTable->addCell($txtSecond, '', '', '', '', '');
        $this->objTable->addCell($txtModerate, '', '', '', '', '');
        $this->objTable->addCell($txtAlternate, '', '', '', '', '');
        $this->objTable->addCell($txtRemarking, '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();
        
        // set up forms
        $this->objForm = new form('frmSubjects',$this->uri(array(
            'action' => 'save_matrix',
            'f' => $facId,
            's' => $subjId,
            'd' => $depId,
            'y' => $year,
        ), 'examiners'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $frmSubmit = $this->objForm->show();
    
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'matrix',
            'f' => $facId,
            's' => $subjId,
            'd' => $depId,
            'y' => $year,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'matrix',
            'f' => $facId,
            's' => $subjId,
            'd' => $depId,
            'y' => $year,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmSubmit;
        $str .= $frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to create the faculty security groups
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $name: The name of the faculty
    * @return VOID
    */
    public function createFacultyGroups($facId, $name)
    {
        $groupId = $this->objGroup->addGroup($name, $facId);
    }    
    
    /**
    * Method to edit the faculty security groups name
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $name: The name of the faculty
    * @return VOID
    */
    public function editFacultyGroups($facId, $name)
    {
        $groupId = $this->objGroup->getId($facId, 'description');
        $this->objGroup->setName($groupId, $name);
    }    

    /**
    * Method to delete the faculty security groups
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @return VOID
    */
    public function deleteFacultyGroups($facId)
    {
        $groupId = $this->objGroup->getId($facId, 'description');
        $this->objGroup->deleteGroup($groupId);
    }
    
    /**
    * Method to create the department security groups
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @param string $name: The name of the department
    * @return VOID
    */
    public function createDepartmentGroups($facId, $depId, $name)
    {
        $lblDepartment = $this->objLanguage->languageText('word_department');
        $lblAdmin = $this->objLanguage->languageText('word_administrator');

        $facGroupId = $this->objGroup->getId($facId, 'description');
        $depGroupId = $this->objGroup->addGroup($name, $depId, $facGroupId);
        $this->objGroup->addGroup($lblDepartment.' '.$lblAdmin, $lblDepartment.' '.$lblAdmin, $depGroupId);        
    }    
    
    /**
    * Method to edit the department security groups name
    *
    * @access public
    * @param string $depId: The id of the department
    * @param string $name: The name of the department
    * @return VOID
    */
    public function editDepartmentGroups($depId, $name)
    {
        $groupId = $this->objGroup->getId($depId, 'description');
        $this->objGroup->setName($groupId, $name);
    }    

    /**
    * Method to delete the department security groups
    *
    * @access public
    * @param string $depId: The id of the department
    * @return VOID
    */
    public function deleteDepartmentGroups($depId)
    {
        $groupId = $this->objGroup->getId($depId, 'description');
        $this->objGroup->deleteGroup($groupId);
    }
    
    /**
    * Method to return the users status within a faculty
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @return string $status: The status of the user with in the faculty | FALSE if not in faculty    
    */
    public function userLevel($facId, $depId = NULL)
    {
        $facHead = $this->_isFacHead($facId);
        if($facHead){
            return 'facHead';
        }
        if($depId != NULL){
            $depHead = $this->_isDepHead($depId);
            if($depHead){
                return 'depHead';
            }
            $depAdmin = $this->_isDepAdmin($depId);
            if($depAdmin){
                return 'depAdmin';
            }
        }
        if($depId == NULL){
            $isMember = $this->_isMember($facId);
            if($isMember){
                return 'isMember';
            }
        }
        return FALSE;
    }
    
    /**
    * Method to determine if the user is a faculty head
    *
    * @access private
    * @param string $facId: The id of the faculty
    * @return string|bool $status: TRUE if faculty head| FALSE if not faculty head   
    */
    private function _isFacHead($facId)
    {
        $groupId = $this->objGroup->getId($facId, 'description');
        $facHead = $this->objGroup->isGroupMember($this->pkId, $groupId);
        if($facHead){
            return TRUE;
        }
        return FALSE;
    }
    
    /**
    * Method to determine if the user is a department head
    *
    * @access private
    * @param string $depId: The id of the department
    * @return string|bool $status: TRUE if department head| FALSE if not department head   
    */
    private function _isDepHead($depId)
    {
        $groupId = $this->objGroup->getId($depId, 'description');
        $depHead = $this->objGroup->isGroupMember($this->pkId, $groupId);
        if($depHead){
            return TRUE;
        }
        return FALSE;
    }
    
    /**
    * Method to determine if the user is a department administrator
    *
    * @access private
    * @param string $depId: The id of the department
    * @return string|bool $status: TRUE if department administrator| FALSE if not department administrator
    */
    private function _isDepAdmin($depId)
    {
        $groupId = $this->objGroup->getId($depId, 'description');
        $groups = $this->objGroup->getSubgroups($groupId);
        $depAdmin = $this->objGroup->isGroupMember($this->pkId, $groups[1]);
        if($depAdmin){
            return TRUE;
        }
        return FALSE;
    }
    
    /**
    * Method to determine if the user is a member of the faculty
    *
    * @access private
    * @param string $facId: The id of the faculty
    * @return string|bool $status: TRUE if member of the faculty| FALSE if not member of the faculty
    */
    private function _isMember($facId)
    {
        $groupId = $this->objGroup->getId($facId, 'description');
        $isMember = $this->objGroup->isSubGroupMember($this->pkId, $groupId);
        if($isMember){
            return TRUE;
        }
        return FALSE;
    }
    
    /**
    * Method to display the faculty heads page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @return string $str: The output string
    */
    public function showFacultyHeads($facId)
    {
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $groupId = $this->objGroup->getId($facId, 'description');
        $groupUsers = $this->objGroup->getGroupUsers($groupId, array(
            'userid',
            'firstname',
            'surname',
        ));
              
        // set up text elements
        $lblFacHeadList = $this->objLanguage->languageText('mod_examiners_facultyheadlist', 'examiners');        
        $lblSearch = $this->objLanguage->languageText('word_search');        
        $lblBy = $this->objLanguage->languageText('word_by');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblUserId = $this->objLanguage->languageText('phrase_userid');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nofacultyheads', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnfaculty', 'examiners');
        $lblMember = $this->objLanguage->languageText('mod_examiners_facmembertitle', 'examiners');
        $lblNonMember = $this->objLanguage->languageText('mod_examiners_nonfacmembertitle', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblUpdate = $this->objLanguage->languageText('mod_examiners_update', 'examiners');
        $lblShow = $this->objLanguage->languageText('word_show');
        $lblUsers = $this->objLanguage->languageText('mod_examiners_usersperpage', 'examiners');
        $lblCriteria = $this->objLanguage->languageText('word_criteria');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblFacHeadList;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up htmlelements
        $this->objInput = new textinput('members', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "facultyList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblUserId, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($groupUsers == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            foreach($groupUsers as $groupUser){
                // set up member icon
                $this->objIcon->title = $lblMember;
                $icoGroups = $this->objIcon->setIcon('user');
                $icoUser = $this->objIcon->show();
                
                $this->objIcon->title = $lblNonMember;
                $icoGroups = $this->objIcon->setIcon('not_applicable');
                $icoNotApp = $this->objIcon->show();

                // set up radios
                $this->objRadio = new radio($this->objUser->PKId($groupUser['userid']));
                $this->objRadio->addOption('member', $icoUser);
                $this->objRadio->addOption('nonmember', $icoNotApp);
                $this->objRadio->setSelected('member');
                $this->objRadio->setBreakSpace('&#160;&#160;');
                $radUser = $this->objRadio->show();
                
                $this->objTable->startRow();
                $this->objTable->addCell($groupUser['userid'], '', 'top', '', '', '');
                $this->objTable->addCell($groupUser['firstname'], '', 'top', '', '', '');
                $this->objTable->addCell($groupUser['surname'], '', 'top', '', '', '');
                $this->objTable->addCell($radUser, '', 'top', '', '', '');
                $this->objTable->addCell('', '', 'top', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
        
        //set up buttons
        $this->objButton = new button('update', $lblUpdate);
        $this->objButton->extra = 'onclick="javascript:
            var myMembers = $(\'input_members\');
            var myForm = $(\'form_frmUpdate\');
            var myRadios = myForm.getInputs(\'radio\');
            for(i=0;i<myRadios.length;i++){
                if(myRadios[i].checked == true){
                    if(myRadios[i].value == \'member\'){
                        myMembers.value = myMembers.value + \'|\' + myRadios[i].name;
                    }
                }
            }
            $(\'form_frmUpdate\').submit();"';
        $btnUpdate = $this->objButton->show();
        
        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmUpdate', $this->uri(array(
            'action' => 'save_fac_heads',
            'f' => $facId,
        )), 'examoners');
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm($inpHidden.$btnUpdate.'&#160;'.$btnCancel);
        $frmUpdate = $this->objForm->show();
                
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'fac_heads',
            'f' => $facId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up search area
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblSearch;
        $this->objHeading->type = 3;
        $string = $this->objHeading->show();
        
        $this->objDrop = new dropdown('field');
        $this->objDrop->addOption(NULL, $lblSearch);
        $this->objDrop->addOption('firstname', $lblName);
        $this->objDrop->addOption('surname', $lblSurname);
        $drpSearch = $this->objDrop->show();
        
        $this->objDrop = new dropdown('count');
        for($i = 25; $i <= 100; $i = $i + 25){
            $this->objDrop->addOption($i, $i);           
        }
        $drpUsers = $this->objDrop->show();
        
        // set up htmlelements
        $this->objInput = new textinput('criteria', '', 'text', '');
        $inpSearch = $this->objInput->show();

        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
                
        $this->objTable->startRow();
        $this->objTable->addCell($lblSearch.'&#160;'.strtolower($lblBy).'&#160;'.$drpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblCriteria.'&#160;'.$inpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblShow.'&#160;'.$drpUsers.'&#160;'.strtolower($lblUsers), '', '', '', '', '');
        $this->objTable->endRow();
        $tblSearch = $this->objTable->show();
        
        $this->objButton = new button('search', $lblSearch);
        $this->objButton->setToSubmit();
        $btnSearch = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmSearch', $this->uri(array(
            'action' => 'fac_users',
            'f' => $facId,
        )), 'examoners');
        $this->objForm->addToForm($tblSearch);
        $this->objForm->addToForm($btnSearch);
        $frmSearch = $this->objForm->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'faculties',
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmUpdate.$frmCancel;
        $str .= $string.$frmSearch;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to display the search for users page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @return string $str: The output string
    */
    public function showFacSearch($facId, $field, $criteria, $count, $page)
    {
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $field = empty($field) ? 'surname' : $field;
        $faculty = $this->objExamDb->getFacultyById($facId);        
        $allUsers = $this->objExamDb->getUsers($field, $criteria);
        $pageUsers = array_chunk($allUsers, $count);
        $users = $pageUsers[$page - 1];
        $groupId = $this->objGroup->getId($facId, 'description');
        $groupUsers = $this->objGroup->getGroupUsers($groupId, array(
            'userid',
            'firstname',
            'surname',
        ));

        // set up text elements
        $lblFacHeadList = $this->objLanguage->languageText('mod_examiners_facultyheadlist', 'examiners');        
        $lblSearch = $this->objLanguage->languageText('word_search');        
        $lblBy = $this->objLanguage->languageText('word_by');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblUserId = $this->objLanguage->languageText('phrase_userid');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nofacultyheads', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnfacultyhead', 'examiners');
        $lblMember = $this->objLanguage->languageText('mod_examiners_facmembertitle', 'examiners');
        $lblNonMember = $this->objLanguage->languageText('mod_examiners_nonfacmembertitle', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblUpdate = $this->objLanguage->languageText('mod_examiners_update', 'examiners');
        $lblShow = $this->objLanguage->languageText('word_show');
        $lblUsers = $this->objLanguage->languageText('mod_examiners_usersperpage', 'examiners');
        $lblPage = $this->objLanguage->languageText('word_page');
        $lblCriteria = $this->objLanguage->languageText('word_criteria');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblFacHeadList;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up page links
        $links = '';
        for($i = 1; $i <= count($pageUsers); $i++){
            $this->objLink = new link($this->uri(array(
                'action' => 'fac_users',
                'f' => $facId,
                'field' => $field,
                'criteria' => $criteria,
                'count' => $count,
                'page' => $i,
            )), 'examiners');
            $this->objLink->link = $lblPage.'&#160;'.$i;
            $lnkPage = $this->objLink->show();
            if($i < count($pageUsers)){
                $links .= $lnkPage.'&#160;|&#160;';
            }elseif(count($pageUsers) == 1){
                $links = '';
            }else{
               $links .= $lnkPage;
            }        
        }
    
        // set up htmlelements
        $this->objInput = new textinput('members', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "facultyList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblUserId, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($users == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            foreach($users as $user){
                // set up member icon
                $this->objIcon->title = $lblMember;
                $icoGroups = $this->objIcon->setIcon('user');
                $icoUser = $this->objIcon->show();
                
                $this->objIcon->title = $lblNonMember;
                $icoGroups = $this->objIcon->setIcon('not_applicable');
                $icoNotApp = $this->objIcon->show();

                // set up radios
                $this->objRadio = new radio($this->objUser->PKId($user['userid']));
                $this->objRadio->addOption('member', $icoUser);
                $this->objRadio->addOption('nonmember', $icoNotApp);
                foreach($groupUsers as $groupUser){
                    if($groupUser['userid'] == $user['userid']){
                        $this->objRadio->setSelected('member');
                        break;
                    }else{
                        $this->objRadio->setSelected('nonmember');
                    }
                }
                $this->objRadio->setBreakSpace('&#160;&#160;');
                $radUser = $this->objRadio->show();
                
                $this->objTable->startRow();
                $this->objTable->addCell($user['userid'], '', 'top', '', '', '');
                $this->objTable->addCell($user['firstname'], '', 'top', '', '', '');
                $this->objTable->addCell($user['surname'], '', 'top', '', '', '');
                $this->objTable->addCell($radUser, '', 'top', '', '', '');
                $this->objTable->addCell('', '', 'top', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
        
        //set up buttons
        $this->objButton = new button('update', $lblUpdate);
        $this->objButton->extra = 'onclick="javascript:
            var myMembers = $(\'input_members\');
            var myForm = $(\'form_frmUpdate\');
            var myRadios = myForm.getInputs(\'radio\');
            for(i=0;i<myRadios.length;i++){
                if(myRadios[i].checked == true){
                    if(myRadios[i].value == \'member\'){
                        myMembers.value = myMembers.value + \'|\' + myRadios[i].name;
                    }
                }
            }
            $(\'form_frmUpdate\').submit();"';
        $btnUpdate = $this->objButton->show();
        
        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmUpdate', $this->uri(array(
            'action' => 'save_fac_heads',
            'f' => $facId,
        )), 'examoners');
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm($inpHidden.$btnUpdate.'&#160;'.$btnCancel);
        $frmUpdate = $this->objForm->show();
                
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'fac_heads',
            'f' => $facId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up search area
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblSearch;
        $this->objHeading->type = 3;
        $string = $this->objHeading->show();
        
        $this->objDrop = new dropdown('field');
        $this->objDrop->addOption(NULL, $lblSearch);
        $this->objDrop->addOption('firstname', $lblName);
        $this->objDrop->addOption('surname', $lblSurname);
        $this->objDrop->setSelected($field);
        $drpSearch = $this->objDrop->show();
        
        $this->objDrop = new dropdown('count');
        for($i = 25; $i <= 100; $i = $i + 25){
            $this->objDrop->addOption($i, $i);           
        }
        $this->objDrop->setSelected($count);
        $drpUsers = $this->objDrop->show();
        
        // set up htmlelements
        $this->objInput = new textinput('criteria', $criteria, 'text', '');
        $inpSearch = $this->objInput->show();

        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
                
        $this->objTable->startRow();
        $this->objTable->addCell($lblSearch.'&#160;'.strtolower($lblBy).'&#160;'.$drpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblCriteria.'&#160;'.$inpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblShow.'&#160;'.$drpUsers.'&#160;'.strtolower($lblUsers), '', '', '', '', '');
        $this->objTable->endRow();
        $tblSearch = $this->objTable->show();
        
        $this->objButton = new button('search', $lblSearch);
        $this->objButton->setToSubmit();
        $btnSearch = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmSearch', $this->uri(array(
            'action' => 'fac_users',
            'f' => $facId,
        )), 'examoners');
        $this->objForm->addToForm($tblSearch);
        $this->objForm->addToForm($btnSearch);
        $frmSearch = $this->objForm->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'fac_heads',
            'f' => $facId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $string.$frmSearch;
        $str .= $heading.$links;
        $str .= $frmUpdate.$frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to display the department heads page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @return string $str: The output string
    */
    public function showDepartmentHeads($facId, $depId)
    {
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $groupId = $this->objGroup->getId($depId, 'description');
        $groupUsers = $this->objGroup->getGroupUsers($groupId, array(
            'userid',
            'firstname',
            'surname',
        ));
              
        // set up text elements
        $lblDepHeadList = $this->objLanguage->languageText('mod_examiners_departmentheadlist', 'examiners');        
        $lblSearch = $this->objLanguage->languageText('word_search');        
        $lblBy = $this->objLanguage->languageText('word_by');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblUserId = $this->objLanguage->languageText('phrase_userid');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nodepartmentheads', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returndepartment', 'examiners');
        $lblMember = $this->objLanguage->languageText('mod_examiners_depmembertitle', 'examiners');
        $lblNonMember = $this->objLanguage->languageText('mod_examiners_nondepmembertitle', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblUpdate = $this->objLanguage->languageText('mod_examiners_update', 'examiners');
        $lblShow = $this->objLanguage->languageText('word_show');
        $lblUsers = $this->objLanguage->languageText('mod_examiners_usersperpage', 'examiners');
        $lblCriteria = $this->objLanguage->languageText('word_criteria');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblDepHeadList;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up htmlelements
        $this->objInput = new textinput('members', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "departmentList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblUserId, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($groupUsers == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            foreach($groupUsers as $groupUser){
                // set up member icon
                $this->objIcon->title = $lblMember;
                $icoGroups = $this->objIcon->setIcon('user');
                $icoUser = $this->objIcon->show();
                
                $this->objIcon->title = $lblNonMember;
                $icoGroups = $this->objIcon->setIcon('not_applicable');
                $icoNotApp = $this->objIcon->show();

                // set up radios
                $this->objRadio = new radio($this->objUser->PKId($groupUser['userid']));
                $this->objRadio->addOption('member', $icoUser);
                $this->objRadio->addOption('nonmember', $icoNotApp);
                $this->objRadio->setSelected('member');
                $this->objRadio->setBreakSpace('&#160;&#160;');
                $radUser = $this->objRadio->show();
                
                $this->objTable->startRow();
                $this->objTable->addCell($groupUser['userid'], '', 'top', '', '', '');
                $this->objTable->addCell($groupUser['firstname'], '', 'top', '', '', '');
                $this->objTable->addCell($groupUser['surname'], '', 'top', '', '', '');
                $this->objTable->addCell($radUser, '', 'top', '', '', '');
                $this->objTable->addCell('', '', 'top', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
        
        //set up buttons
        $this->objButton = new button('update', $lblUpdate);
        $this->objButton->extra = 'onclick="javascript:
            var myMembers = $(\'input_members\');
            var myForm = $(\'form_frmUpdate\');
            var myRadios = myForm.getInputs(\'radio\');
            for(i=0;i<myRadios.length;i++){
                if(myRadios[i].checked == true){
                    if(myRadios[i].value == \'member\'){
                        myMembers.value = myMembers.value + \'|\' + myRadios[i].name;
                    }
                }
            }
            $(\'form_frmUpdate\').submit();"';
        $btnUpdate = $this->objButton->show();
        
        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmUpdate', $this->uri(array(
            'action' => 'save_dep_heads',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm($inpHidden.$btnUpdate.'&#160;'.$btnCancel);
        $frmUpdate = $this->objForm->show();
                
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'dep_heads',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up search area
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblSearch;
        $this->objHeading->type = 3;
        $string = $this->objHeading->show();
        
        $this->objDrop = new dropdown('field');
        $this->objDrop->addOption(NULL, $lblSearch);
        $this->objDrop->addOption('firstname', $lblName);
        $this->objDrop->addOption('surname', $lblSurname);
        $drpSearch = $this->objDrop->show();
        
        $this->objDrop = new dropdown('count');
        for($i = 25; $i <= 100; $i = $i + 25){
            $this->objDrop->addOption($i, $i);           
        }
        $drpUsers = $this->objDrop->show();
        
        // set up htmlelements
        $this->objInput = new textinput('criteria', '', 'text', '');
        $inpSearch = $this->objInput->show();

        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
                
        $this->objTable->startRow();
        $this->objTable->addCell($lblSearch.'&#160;'.strtolower($lblBy).'&#160;'.$drpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblCriteria.'&#160;'.$inpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblShow.'&#160;'.$drpUsers.'&#160;'.strtolower($lblUsers), '', '', '', '', '');
        $this->objTable->endRow();
        $tblSearch = $this->objTable->show();
        
        $this->objButton = new button('search', $lblSearch);
        $this->objButton->setToSubmit();
        $btnSearch = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmSearch', $this->uri(array(
            'action' => 'dep_users',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblSearch);
        $this->objForm->addToForm($btnSearch);
        $frmSearch = $this->objForm->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'departments',
            'f' => $facId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmUpdate.$frmCancel;
        $str .= $string.$frmSearch;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to display the search for users page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @return string $str: The output string
    */
    public function showDepSearch($facId, $depId, $field, $criteria, $count, $page)
    {
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $field = empty($field) ? 'surname' : $field;
        $faculty = $this->objExamDb->getFacultyById($facId);        
        $department = $this->objExamDb->getDepartmentById($depId);        
        $allUsers = $this->objExamDb->getUsers($field, $criteria);
        $pageUsers = array_chunk($allUsers, $count);
        $users = $pageUsers[$page - 1];
        $groupId = $this->objGroup->getId($depId, 'description');
        $groupUsers = $this->objGroup->getGroupUsers($groupId, array(
            'userid',
            'firstname',
            'surname',
        ));

        // set up text elements
        $lblFacHeadList = $this->objLanguage->languageText('mod_examiners_departmentheadlist', 'examiners');        
        $lblSearch = $this->objLanguage->languageText('word_search');        
        $lblBy = $this->objLanguage->languageText('word_by');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblUserId = $this->objLanguage->languageText('phrase_userid');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nodepartmentheads', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returndepartmenthead', 'examiners');
        $lblMember = $this->objLanguage->languageText('mod_examiners_depmembertitle', 'examiners');
        $lblNonMember = $this->objLanguage->languageText('mod_examiners_nondepmembertitle', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblUpdate = $this->objLanguage->languageText('mod_examiners_update', 'examiners');
        $lblShow = $this->objLanguage->languageText('word_show');
        $lblUsers = $this->objLanguage->languageText('mod_examiners_usersperpage', 'examiners');
        $lblPage = $this->objLanguage->languageText('word_page');
        $lblCriteria = $this->objLanguage->languageText('word_criteria');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblFacHeadList;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up page links
        $links = '';
        for($i = 1; $i <= count($pageUsers); $i++){
            $this->objLink = new link($this->uri(array(
                'action' => 'dep_users',
                'f' => $facId,
                'd' => $depId,
                'field' => $field,
                'criteria' => $criteria,
                'count' => $count,
                'page' => $i,
            )), 'examiners');
            $this->objLink->link = $lblPage.'&#160;'.$i;
            $lnkPage = $this->objLink->show();
            if($i < count($pageUsers)){
                $links .= $lnkPage.'&#160;|&#160;';
            }elseif(count($pageUsers) == 1){
                $links = '';
            }else{
               $links .= $lnkPage;
            }        
        }
    
        // set up htmlelements
        $this->objInput = new textinput('members', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "facultyList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblUserId, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($users == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            foreach($users as $user){
                // set up member icon
                $this->objIcon->title = $lblMember;
                $icoGroups = $this->objIcon->setIcon('user');
                $icoUser = $this->objIcon->show();
                
                $this->objIcon->title = $lblNonMember;
                $icoGroups = $this->objIcon->setIcon('not_applicable');
                $icoNotApp = $this->objIcon->show();

                // set up radios
                $this->objRadio = new radio($this->objUser->PKId($user['userid']));
                $this->objRadio->addOption('member', $icoUser);
                $this->objRadio->addOption('nonmember', $icoNotApp);
                foreach($groupUsers as $groupUser){
                    if($groupUser['userid'] == $user['userid']){
                        $this->objRadio->setSelected('member');
                        break;
                    }else{
                        $this->objRadio->setSelected('nonmember');
                    }
                }
                $this->objRadio->setBreakSpace('&#160;&#160;');
                $radUser = $this->objRadio->show();
                
                $this->objTable->startRow();
                $this->objTable->addCell($user['userid'], '', 'top', '', '', '');
                $this->objTable->addCell($user['firstname'], '', 'top', '', '', '');
                $this->objTable->addCell($user['surname'], '', 'top', '', '', '');
                $this->objTable->addCell($radUser, '', 'top', '', '', '');
                $this->objTable->addCell('', '', 'top', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
        
        //set up buttons
        $this->objButton = new button('update', $lblUpdate);
        $this->objButton->extra = 'onclick="javascript:
            var myMembers = $(\'input_members\');
            var myForm = $(\'form_frmUpdate\');
            var myRadios = myForm.getInputs(\'radio\');
            for(i=0;i<myRadios.length;i++){
                if(myRadios[i].checked == true){
                    if(myRadios[i].value == \'member\'){
                        myMembers.value = myMembers.value + \'|\' + myRadios[i].name;
                    }
                }
            }
            $(\'form_frmUpdate\').submit();"';
        $btnUpdate = $this->objButton->show();
        
        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmUpdate', $this->uri(array(
            'action' => 'save_dep_heads',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm($inpHidden.$btnUpdate.'&#160;'.$btnCancel);
        $frmUpdate = $this->objForm->show();
                
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'dep_heads',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up search area
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblSearch;
        $this->objHeading->type = 3;
        $string = $this->objHeading->show();
        
        $this->objDrop = new dropdown('field');
        $this->objDrop->addOption(NULL, $lblSearch);
        $this->objDrop->addOption('firstname', $lblName);
        $this->objDrop->addOption('surname', $lblSurname);
        $this->objDrop->setSelected($field);
        $drpSearch = $this->objDrop->show();
        
        $this->objDrop = new dropdown('count');
        for($i = 25; $i <= 100; $i = $i + 25){
            $this->objDrop->addOption($i, $i);           
        }
        $this->objDrop->setSelected($count);
        $drpUsers = $this->objDrop->show();
        
        // set up htmlelements
        $this->objInput = new textinput('criteria', $criteria, 'text', '');
        $inpSearch = $this->objInput->show();

        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
                
        $this->objTable->startRow();
        $this->objTable->addCell($lblSearch.'&#160;'.strtolower($lblBy).'&#160;'.$drpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblCriteria.'&#160;'.$inpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblShow.'&#160;'.$drpUsers.'&#160;'.strtolower($lblUsers), '', '', '', '', '');
        $this->objTable->endRow();
        $tblSearch = $this->objTable->show();
        
        $this->objButton = new button('search', $lblSearch);
        $this->objButton->setToSubmit();
        $btnSearch = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmSearch', $this->uri(array(
            'action' => 'dep_users',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblSearch);
        $this->objForm->addToForm($btnSearch);
        $frmSearch = $this->objForm->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'dep_heads',
            'f' => $facId,
            'd' => $depId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $string.$frmSearch;
        $str .= $heading.$links;
        $str .= $frmUpdate.$frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }

    /**
    * Method to display the department admin page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @return string $str: The output string
    */
    public function showDepartmentAdmin($facId, $depId)
    {
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $groupId = $this->objGroup->getId($depId, 'description');
        $groups = $this->objGroup->getSubgroups($groupId);
        $groupUsers = $this->objGroup->getGroupUsers($groups[1], array(
            'userid',
            'firstname',
            'surname',
        ));
              
        // set up text elements
        $lblDepHeadList = $this->objLanguage->languageText('mod_examiners_adminlist', 'examiners');        
        $lblSearch = $this->objLanguage->languageText('word_search');        
        $lblBy = $this->objLanguage->languageText('word_by');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblUserId = $this->objLanguage->languageText('phrase_userid');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nodepartmentadmin', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returndepartment', 'examiners');
        $lblMember = $this->objLanguage->languageText('mod_examiners_adminmembertitle', 'examiners');
        $lblNonMember = $this->objLanguage->languageText('mod_examiners_nonadminmembertitle', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblUpdate = $this->objLanguage->languageText('mod_examiners_update', 'examiners');
        $lblShow = $this->objLanguage->languageText('word_show');
        $lblUsers = $this->objLanguage->languageText('mod_examiners_usersperpage', 'examiners');
        $lblCriteria = $this->objLanguage->languageText('word_criteria');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblDepHeadList;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up htmlelements
        $this->objInput = new textinput('members', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "adminList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblUserId, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($groupUsers == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            foreach($groupUsers as $groupUser){
                // set up member icon
                $this->objIcon->title = $lblMember;
                $icoGroups = $this->objIcon->setIcon('user');
                $icoUser = $this->objIcon->show();
                
                $this->objIcon->title = $lblNonMember;
                $icoGroups = $this->objIcon->setIcon('not_applicable');
                $icoNotApp = $this->objIcon->show();

                // set up radios
                $this->objRadio = new radio($this->objUser->PKId($groupUser['userid']));
                $this->objRadio->addOption('member', $icoUser);
                $this->objRadio->addOption('nonmember', $icoNotApp);
                $this->objRadio->setSelected('member');
                $this->objRadio->setBreakSpace('&#160;&#160;');
                $radUser = $this->objRadio->show();
                
                $this->objTable->startRow();
                $this->objTable->addCell($groupUser['userid'], '', 'top', '', '', '');
                $this->objTable->addCell($groupUser['firstname'], '', 'top', '', '', '');
                $this->objTable->addCell($groupUser['surname'], '', 'top', '', '', '');
                $this->objTable->addCell($radUser, '', 'top', '', '', '');
                $this->objTable->addCell('', '', 'top', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
        
        //set up buttons
        $this->objButton = new button('update', $lblUpdate);
        $this->objButton->extra = 'onclick="javascript:
            var myMembers = $(\'input_members\');
            var myForm = $(\'form_frmUpdate\');
            var myRadios = myForm.getInputs(\'radio\');
            for(i=0;i<myRadios.length;i++){
                if(myRadios[i].checked == true){
                    if(myRadios[i].value == \'member\'){
                        myMembers.value = myMembers.value + \'|\' + myRadios[i].name;
                    }
                }
            }
            $(\'form_frmUpdate\').submit();"';
        $btnUpdate = $this->objButton->show();
        
        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmUpdate', $this->uri(array(
            'action' => 'save_dep_admin',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm($inpHidden.$btnUpdate.'&#160;'.$btnCancel);
        $frmUpdate = $this->objForm->show();
                
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'dep_admin',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up search area
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblSearch;
        $this->objHeading->type = 3;
        $string = $this->objHeading->show();
        
        $this->objDrop = new dropdown('field');
        $this->objDrop->addOption(NULL, $lblSearch);
        $this->objDrop->addOption('firstname', $lblName);
        $this->objDrop->addOption('surname', $lblSurname);
        $drpSearch = $this->objDrop->show();
        
        $this->objDrop = new dropdown('count');
        for($i = 25; $i <= 100; $i = $i + 25){
            $this->objDrop->addOption($i, $i);           
        }
        $drpUsers = $this->objDrop->show();
        
        // set up htmlelements
        $this->objInput = new textinput('criteria', '', 'text', '');
        $inpSearch = $this->objInput->show();

        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
                
        $this->objTable->startRow();
        $this->objTable->addCell($lblSearch.'&#160;'.strtolower($lblBy).'&#160;'.$drpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblCriteria.'&#160;'.$inpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblShow.'&#160;'.$drpUsers.'&#160;'.strtolower($lblUsers), '', '', '', '', '');
        $this->objTable->endRow();
        $tblSearch = $this->objTable->show();
        
        $this->objButton = new button('search', $lblSearch);
        $this->objButton->setToSubmit();
        $btnSearch = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmSearch', $this->uri(array(
            'action' => 'admin_users',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblSearch);
        $this->objForm->addToForm($btnSearch);
        $frmSearch = $this->objForm->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'departments',
            'f' => $facId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading;
        $str .= $frmUpdate.$frmCancel;
        $str .= $string.$frmSearch;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to display the search for users page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @return string $str: The output string
    */
    public function showAdminSearch($facId, $depId, $field, $criteria, $count, $page)
    {
        // append javascript
        $headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
        $this->appendArrayVar('headerParams', $headerParams);

        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $field = empty($field) ? 'surname' : $field;
        $faculty = $this->objExamDb->getFacultyById($facId);        
        $department = $this->objExamDb->getDepartmentById($depId);        
        $allUsers = $this->objExamDb->getUsers($field, $criteria);
        $pageUsers = array_chunk($allUsers, $count);
        $users = $pageUsers[$page - 1];
        $groupId = $this->objGroup->getId($depId, 'description');
        $groups = $this->objGroup->getSubgroups($groupId);
        $groupUsers = $this->objGroup->getGroupUsers($groups[1], array(
            'userid',
            'firstname',
            'surname',
        ));

        // set up text elements
        $lblFacHeadList = $this->objLanguage->languageText('mod_examiners_adminlist', 'examiners');        
        $lblSearch = $this->objLanguage->languageText('word_search');        
        $lblBy = $this->objLanguage->languageText('word_by');        
        $lblName = $this->objLanguage->languageText('word_name');        
        $lblSurname = $this->objLanguage->languageText('word_surname');
        $lblUserId = $this->objLanguage->languageText('phrase_userid');
        $lblNoRecords = $this->objLanguage->languageText('mod_examiners_nodepartmentadmin', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnadmin', 'examiners');
        $lblMember = $this->objLanguage->languageText('mod_examiners_adminmembertitle', 'examiners');
        $lblNonMember = $this->objLanguage->languageText('mod_examiners_nonadminmembertitle', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblUpdate = $this->objLanguage->languageText('mod_examiners_update', 'examiners');
        $lblShow = $this->objLanguage->languageText('word_show');
        $lblUsers = $this->objLanguage->languageText('mod_examiners_usersperpage', 'examiners');
        $lblPage = $this->objLanguage->languageText('word_page');
        $lblCriteria = $this->objLanguage->languageText('word_criteria');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblFacHeadList;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up page links
        $links = '';
        for($i = 1; $i <= count($pageUsers); $i++){
            $this->objLink = new link($this->uri(array(
                'action' => 'admin_users',
                'f' => $facId,
                'd' => $depId,
                'field' => $field,
                'criteria' => $criteria,
                'count' => $count,
                'page' => $i,
            )), 'examiners');
            $this->objLink->link = $lblPage.'&#160;'.$i;
            $lnkPage = $this->objLink->show();
            if($i < count($pageUsers)){
                $links .= $lnkPage.'&#160;|&#160;';
            }elseif(count($pageUsers) == 1){
                $links = '';
            }else{
               $links .= $lnkPage;
            }        
        }
    
        // set up htmlelements
        $this->objInput = new textinput('members', '', 'hidden', '');
        $inpHidden = $this->objInput->show();

        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->id = "facultyList";
        $this->objTable->css_class = "sorttable";
        $this->objTable->cellpadding = '5';        
        $this->objTable->row_attributes = 'onmouseover="this.className=\'ruler\';" onmouseout="this.className=\'\';" name="row_'.$this->objTable->id.'"';
        $this->objTable->startRow();
        $this->objTable->addCell($lblUserId, '', '', '', 'header', '');
        $this->objTable->addCell($lblName, '', '', '', 'header', '');
        $this->objTable->addCell($lblSurname, '', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->addCell('', '10%', '', '', 'header', '');
        $this->objTable->endRow();
        if($users == FALSE){
            $this->objTable->startRow();
            $this->objTable->addCell($lblNoRecords, '', '', '', 'noRecordsMessage', 'colspan="5"');
            $this->objTable->endRow();
        }else{
            foreach($users as $user){
                // set up member icon
                $this->objIcon->title = $lblMember;
                $icoGroups = $this->objIcon->setIcon('user');
                $icoUser = $this->objIcon->show();
                
                $this->objIcon->title = $lblNonMember;
                $icoGroups = $this->objIcon->setIcon('not_applicable');
                $icoNotApp = $this->objIcon->show();

                // set up radios
                $this->objRadio = new radio($this->objUser->PKId($user['userid']));
                $this->objRadio->addOption('member', $icoUser);
                $this->objRadio->addOption('nonmember', $icoNotApp);
                foreach($groupUsers as $groupUser){
                    if($groupUser['userid'] == $user['userid']){
                        $this->objRadio->setSelected('member');
                        break;
                    }else{
                        $this->objRadio->setSelected('nonmember');
                    }
                }
                $this->objRadio->setBreakSpace('&#160;&#160;');
                $radUser = $this->objRadio->show();
                
                $this->objTable->startRow();
                $this->objTable->addCell($user['userid'], '', 'top', '', '', '');
                $this->objTable->addCell($user['firstname'], '', 'top', '', '', '');
                $this->objTable->addCell($user['surname'], '', 'top', '', '', '');
                $this->objTable->addCell($radUser, '', 'top', '', '', '');
                $this->objTable->addCell('', '', 'top', '', '', '');
                $this->objTable->endRow();
            }
        }
        $tblDisplay = $this->objTable->show();
        
        //set up buttons
        $this->objButton = new button('update', $lblUpdate);
        $this->objButton->extra = 'onclick="javascript:
            var myMembers = $(\'input_members\');
            var myForm = $(\'form_frmUpdate\');
            var myRadios = myForm.getInputs(\'radio\');
            for(i=0;i<myRadios.length;i++){
                if(myRadios[i].checked == true){
                    if(myRadios[i].value == \'member\'){
                        myMembers.value = myMembers.value + \'|\' + myRadios[i].name;
                    }
                }
            }
            $(\'form_frmUpdate\').submit();"';
        $btnUpdate = $this->objButton->show();
        
        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmUpdate', $this->uri(array(
            'action' => 'save_dep_admin',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm($inpHidden.$btnUpdate.'&#160;'.$btnCancel);
        $frmUpdate = $this->objForm->show();
                
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'dep_admin',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up search area
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblSearch;
        $this->objHeading->type = 3;
        $string = $this->objHeading->show();
        
        $this->objDrop = new dropdown('field');
        $this->objDrop->addOption(NULL, $lblSearch);
        $this->objDrop->addOption('firstname', $lblName);
        $this->objDrop->addOption('surname', $lblSurname);
        $this->objDrop->setSelected($field);
        $drpSearch = $this->objDrop->show();
        
        $this->objDrop = new dropdown('count');
        for($i = 25; $i <= 100; $i = $i + 25){
            $this->objDrop->addOption($i, $i);           
        }
        $this->objDrop->setSelected($count);
        $drpUsers = $this->objDrop->show();
        
        // set up htmlelements
        $this->objInput = new textinput('criteria', $criteria, 'text', '');
        $inpSearch = $this->objInput->show();

        $this->objTable = new htmltable();
        $this->objTable->cellpadding = '5';
                
        $this->objTable->startRow();
        $this->objTable->addCell($lblSearch.'&#160;'.strtolower($lblBy).'&#160;'.$drpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblCriteria.'&#160;'.$inpSearch, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblShow.'&#160;'.$drpUsers.'&#160;'.strtolower($lblUsers), '', '', '', '', '');
        $this->objTable->endRow();
        $tblSearch = $this->objTable->show();
        
        $this->objButton = new button('search', $lblSearch);
        $this->objButton->setToSubmit();
        $btnSearch = $this->objButton->show();
        
        // set up form
        $this->objForm = new form('frmSearch', $this->uri(array(
            'action' => 'admin_users',
            'f' => $facId,
            'd' => $depId,
        )), 'examoners');
        $this->objForm->addToForm($tblSearch);
        $this->objForm->addToForm($btnSearch);
        $frmSearch = $this->objForm->show();
                
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'dep_admin',
            'f' => $facId,
            'd' => $depId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $string.$frmSearch;
        $str .= $heading.$links;
        $str .= $frmUpdate.$frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to show the export page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @return string $str: The output string
    */
    public function showExportForDep($facId, $depId)
    {
        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);        
        $department = $this->objExamDb->getDepartmentById($depId);        

        // set up text elements
        $lblExportHead = $this->objLanguage->languageText('mod_examiners_export', 'examiners');        
        $lblYear = $this->objLanguage->languageText('word_year');        
        $lblUndergrad = $this->objLanguage->languageText('mod_examiners_undergraduate', 'examiners');
        $lblPostgrad = $this->objLanguage->languageText('mod_examiners_postgraduate', 'examiners');
        $lblSubject = $this->objLanguage->languageText('mod_examiners_subjects', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returnsubject', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblExport = $this->objLanguage->languageText('word_export');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $department['department_name'];
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblExportHead;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up html elements
        $this->objRadio = new radio('option');
        $this->objRadio->addOption('', '  '.$lblSubject);
        $this->objRadio->addOption('1', '  '. $lblUndergrad);
        $this->objRadio->addOption('2', '  '.$lblPostgrad);
        $this->objRadio->setSelected('');
        $this->objRadio->setBreakSpace('<br />');
        $radOption = $this->objRadio->show();
        
        $this->objDrop = new dropdown('y');
        for($loop = date('Y') + 1; $loop >= 2006; $loop--){
            $this->objDrop->addOption($loop, $loop.'&#160;');
        }
        $this->objDrop->setSelected(date('Y') + 1);
        $drpYear = $this->objDrop->show();
        
        $this->objButton=new button('submit',$lblExport);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpading = '5';        
        $this->objTable->startRow();
        $this->objTable->addCell($radOption, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblYear.'&#058;'.$drpYear, '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();
        
        // set up forms
        $this->objForm = new form('frmExport',$this->uri(array(
            'action' => 'dep_do_export',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $frmSubmit = $this->objForm->show();
    
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'subjects',
            'f' => $facId,
            'd' => $depId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'subjects',
            'f' => $facId,
            'd' => $depId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading.'<br />';
        $str .= $frmSubmit;
        $str .= $frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to do the export of the matrix
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @param string $option: The type of export that should be done
    * @param string $year: The year for which the export must be done
    * @return bool $result: TRUE if the file was created | FALSE if not
    */
    public function doDepExport($facId, $depId, $option, $year)
    {
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $department = $this->objExamDb->getDepartmentById($depId);
        $subjects = $this->objExamDb->getSubjectsByDepartment($depId, $option);
        if($subjects != FALSE){
            foreach($subjects as $subject){
                $examiners = $this->objExamDb->getMatrixByYear($facId, $depId, $subject['id'], $year);
                if($examiners != FALSE){
                    $subject['examiners'] = $examiners;
                    $matrix[] = $subject;
                }else{
                    $matrix[] = $subject;
                }
            }       
        }else{
            return 'FALSE';
        }

        // set up text elements
        $lblFirst = $this->objLanguage->languageText('mod_examiners_first', 'examiners');
        $lblSecond = $this->objLanguage->languageText('mod_examiners_second', 'examiners');
        $lblModerate = $this->objLanguage->languageText('mod_examiners_moderator', 'examiners');
        $lblAlternate = $this->objLanguage->languageText('mod_examiners_alternate', 'examiners');
        $lblRemark = $this->objLanguage->languageText('mod_examiners_remark', 'examiners');
        $lblCode = $this->objLanguage->languageText('word_code');
        $lblSubject = $this->objLanguage->languageText('word_subject');
        
        $file = $this->filePath.'/'.$depId.'.csv';
        $outputFile = fopen($file, 'wb');
        $str = '"'.$faculty['faculty_name'].'"'."\n";
        fwrite($outputFile, $str);

        $str = '"'.$department['department_name'].' - '.$year.'"'."\n";
        fwrite($outputFile, $str);

        $str = '"'.$lblCode.'"';
        $str .= ',"'.$lblSubject.'"';
        $str .= ',"'.$lblFirst.'"';
        $str .= ',"'.$lblSecond.'"';
        $str .= ',"'.$lblModerate.'"';
        $str .= ',"'.$lblAlternate.'"';
        $str .= ',"'.$lblRemark.'"'."\n";
        fwrite($outputFile, $str);
        
        foreach($matrix as $data){
            $line = '"'.$data['course_code'].'"';   
            $line .= ',"'.$data['course_name'].'"';
            if(isset($data['examiners'])){
                foreach($data['examiners'] as $examiner){
                    if($examiner != FALSE){
                        $line .= ',"';
                        $line .= $examiner['title'].' '.$examiner['first_name'].' '.$examiner['surname']."\n";
                        $line .= $examiner['organisation']."\n";
                        $line .= $examiner['email_address']."\n";
                        if(!empty($examiner['tel_no'])){
                            $line .= $examiner['tel_no']."\n";
                        }
                        if(!empty($examiner['extension'])){
                            $line .= $examiner['extension']."\n";
                        }
                        if(!empty($examiner['cell_no'])){
                            $line .= $examiner['cell_no']."\n";
                        }
                        if(!empty($examiner['address'])){
                            $line .= $examiner['address']."\n";
                        }
                        if(!empty($examiner['remarks'])){
                            $line .= $examiner['remarks']."\n";
                        }
                        $line .= '"';
                    }else{
                        $line .= ',""';
                    }
                }
            }
            $line .= "\n";
            fwrite($outputFile, $line);
        }
        fclose($outputFile);
        return 'TRUE';
    }

    /**
    * Method to show the export page
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @return string $str: The output string
    */
    public function showExportForFac($facId)
    {
        $objHighlightLabels = $this->newObject('highlightlabels', 'htmlelements');
        echo $objHighlightLabels->show();

        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);        

        // set up text elements
        $lblExportHead = $this->objLanguage->languageText('mod_examiners_export', 'examiners');        
        $lblYear = $this->objLanguage->languageText('word_year');        
        $lblUndergrad = $this->objLanguage->languageText('mod_examiners_undergraduate', 'examiners');
        $lblPostgrad = $this->objLanguage->languageText('mod_examiners_postgraduate', 'examiners');
        $lblSubject = $this->objLanguage->languageText('mod_examiners_subjects', 'examiners');
        $lblReturn = $this->objLanguage->languageText('mod_examiners_returndepartment', 'examiners');
        $lblCancel = $this->objLanguage->languageText('word_cancel');
        $lblExport = $this->objLanguage->languageText('word_export');
                
        // set up page heading
        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $faculty['faculty_name'];
        $this->objHeading->type = 3;
        $heading = $this->objHeading->show();

        $this->objHeading = new htmlHeading();
        $this->objHeading->str = $lblExportHead;
        $this->objHeading->type = 3;
        $heading .= $this->objHeading->show();
        
        // set up html elements
        $this->objRadio = new radio('option');
        $this->objRadio->addOption('', '  '.$lblSubject);
        $this->objRadio->addOption('1', '  '. $lblUndergrad);
        $this->objRadio->addOption('2', '  '.$lblPostgrad);
        $this->objRadio->setSelected('');
        $this->objRadio->setBreakSpace('<br />');
        $radOption = $this->objRadio->show();
        
        $this->objDrop = new dropdown('y');
        for($loop = date('Y') + 1; $loop >= 2006; $loop--){
            $this->objDrop->addOption($loop, $loop.'&#160;');
        }
        $this->objDrop->setSelected(date('Y') + 1);
        $drpYear = $this->objDrop->show();
        
        $this->objButton=new button('submit',$lblExport);
        $this->objButton->setToSubmit();
        $btnSubmit = $this->objButton->show();

        $this->objButton=new button('cancel',$lblCancel);
        $this->objButton->extra = 'onclick="$(\'form_frmCancel\').submit();"';
        $btnCancel = $this->objButton->show();
        
        // set up display table
        $this->objTable = new htmltable();
        $this->objTable->cellspacing = '2';
        $this->objTable->cellpading = '5';        
        $this->objTable->startRow();
        $this->objTable->addCell($radOption, '', '', '', '', '');
        $this->objTable->endRow();
        $this->objTable->startRow();
        $this->objTable->addCell($lblYear.'&#058;'.$drpYear, '', '', '', '', '');
        $this->objTable->endRow();
        $tblDisplay = $this->objTable->show();
        
        // set up forms
        $this->objForm = new form('frmExport',$this->uri(array(
            'action' => 'fac_do_export',
            'f' => $facId,
        ), 'examiners'));
        $this->objForm->addToForm($tblDisplay);
        $this->objForm->addToForm('<br />'.$btnSubmit.'&#160;'.$btnCancel);
        $frmSubmit = $this->objForm->show();
    
        $this->objForm=new form('frmCancel',$this->uri(array(
            'action' => 'departments',
            'f' => $facId,
        ), 'examiners'));
        $frmCancel = $this->objForm->show();
        
        // set up return link
        $this->objLink = new link($this->uri(array(
            'action' => 'departments',
            'f' => $facId,
        ),'examiners'));
        $this->objLink->link = $lblReturn;
        $lnkReturn = $this->objLink->show();

        // set up page
        $str = $heading.'<br />';
        $str .= $frmSubmit;
        $str .= $frmCancel;
        $str .= '<br />'.$lnkReturn;
               
        return $str;        
    }
    
    /**
    * Method to do the export of the matrix
    *
    * @access public
    * @param string $facId: The id of the faculty
    * @param string $depId: The id of the department
    * @param string $option: The type of export that should be done
    * @param string $year: The year for which the export must be done
    * @return bool $result: TRUE if the file was created | FALSE if not
    */
    public function doFacExport($facId, $option, $year)
    {
        // set up text elements
        $lblFirst = $this->objLanguage->languageText('mod_examiners_first', 'examiners');
        $lblSecond = $this->objLanguage->languageText('mod_examiners_second', 'examiners');
        $lblModerate = $this->objLanguage->languageText('mod_examiners_moderator', 'examiners');
        $lblAlternate = $this->objLanguage->languageText('mod_examiners_alternate', 'examiners');
        $lblRemark = $this->objLanguage->languageText('mod_examiners_remark', 'examiners');
        $lblCode = $this->objLanguage->languageText('word_code');
        $lblSubject = $this->objLanguage->languageText('word_subject');
        
        // get data
        $faculty = $this->objExamDb->getFacultyById($facId);
        $departments = $this->objExamDb->getAllDepartmentsPerFaculty($facId);
        if($departments == FALSE){
            return 'FALSE';
        }else{
            $file = $this->filePath.'/'.$facId.'.csv';
            $outputFile = fopen($file, 'wb');
            $str = '"'.$faculty['faculty_name'].'"'."\n";
            fwrite($outputFile, $str);

            foreach($departments as $department){
                $str = '"'.$department['department_name'].' - '.$year.'"'."\n";
                fwrite($outputFile, $str);

                $subjects = $this->objExamDb->getSubjectsByDepartment($department['id'], $option);
                if($subjects != FALSE){
                    foreach($subjects as $subject){
                        $examiners = $this->objExamDb->getMatrixByYear($facId, $department['id'], $subject['id'], $year);
                        if($examiners != FALSE){
                            $subject['examiners'] = $examiners;
                            $matrix[] = $subject;
                        }else{
                            $matrix[] = $subject;
                        }
                    }       
                }else{
                    return 'FALSE';
                }

                $str = '"'.$lblCode.'"';
                $str .= ',"'.$lblSubject.'"';
                $str .= ',"'.$lblFirst.'"';
                $str .= ',"'.$lblSecond.'"';
                $str .= ',"'.$lblModerate.'"';
                $str .= ',"'.$lblAlternate.'"';
                $str .= ',"'.$lblRemark.'"'."\n";
                fwrite($outputFile, $str);
        
                foreach($matrix as $data){
                    $line = '"'.$data['course_code'].'"';   
                    $line .= ',"'.$data['course_name'].'"';
                    if(isset($data['examiners'])){
                        foreach($data['examiners'] as $examiner){
                            if($examiner != FALSE){
                                $line .= ',"';
                                $line .= $examiner['title'].' '.$examiner['first_name'].' '.$examiner['surname']."\n";
                                $line .= $examiner['organisation']."\n";
                                $line .= $examiner['email_address']."\n";
                                if(!empty($examiner['tel_no'])){
                                    $line .= $examiner['tel_no']."\n";
                                }
                                if(!empty($examiner['extension'])){
                                    $line .= $examiner['extension']."\n";
                                }
                                if(!empty($examiner['cell_no'])){
                                    $line .= $examiner['cell_no']."\n";
                                }
                                if(!empty($examiner['address'])){
                                    $line .= $examiner['address']."\n";
                                }
                                if(!empty($examiner['remarks'])){
                                    $line .= $examiner['remarks']."\n";
                                }
                                $line .= '"';
                            }else{
                                $line .= ',""';
                            }
                        }
                    }
                    $line .= "\n";
                    fwrite($outputFile, $line);
                }
                $line .= "\n";
                fwrite($outputFile, $line);
            }
            fclose($outputFile);
            return 'TRUE';
        }
    }
}
?>
<?php 
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
/**
* 
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package groupadmin
* @subpackage controller
* @version 0.1
* @since 22 November 2004
* @author Jonathan Abrahams 
* @filesource 
*/
/**
* Class to manage groups.
* 
* @package groupadmin
* @subpackage controller
* @access public 
* @author Jonathan Abrahams 
*/
class groupadmin extends controller {
    /**
    * 
    * @var groupAdminModel an object reference.
    */
    public $objGroupAdminModel;
    /**
    * 
    * @var langauge an object reference.
    */
    public $objLanguage;
    /**
    * 
    * @var strValidate an object reference.
    */
    public $objStrValidate;
    /**
    * 
    * @var string the current groups Id
    */
    public $groupId;
    /**
    * 
    * @var array a list of current groups members
    */
    public $memberList;
    /**
    * 
    * @var array a list of current groups non-members
    */
    public $usersList;
    /**
    * 
    * @var bool the validation status of a form.
    */
    public $valid;

    /**
    * Method that initializes the objects
    * 
    * @access private 
    * @return nothing 
    */
    function init()
    {
        $this->objGroupAdminModel = &$this->getObject( 'groupAdminModel', 'groupadmin' );
        $this->objLanguage = &$this->getObject( 'language', 'language' );
        $this->objStrValidate = &$this->getObject( 'strvalidate', 'strings' );
        $this->objTree = &$this->getObject( 'groupadmin_tree', 'groupadmin' );
        $this->objMembers = &$this->getObject( 'groupadmin_members', 'groupadmin' );

        $this->setVarByRef( 'objGroupAdminModel', $this->objGroupAdminModel );
        $this->setVarByRef( 'objLanguage', $this->objLangauge ); 
        
        // Create an array of words to abstract
         // Create an array of words to abstract
        $this->abstractionArray = array(
                'Lecturers'=>ucwords($this->objLanguage->code2Txt('word_lecturers')), 
                'Students'=>ucwords($this->objLanguage->code2Txt('word_students'))
            );
        
        // Get the activity logger class
        $this->objLog = $this->newObject( 'logactivity', 'logger' ); 
        // Log this module call
        $this->objLog->log();
    }

    /**
    * Method to handel the messages.
    * 
    * @param string $action the message.
    * @access private 
    * @return string the template file name.
    */
    function dispatch( $action )
    {
        $hasAccess = $this->objEngine->_objUser->isContextLecturer();
        $hasAccess|= $this->objEngine->_objUser->isAdmin();
        $this->setVar('pageSuppressXML',true);
        if( !$hasAccess ) {
            $link = $this->newObject( 'link', 'htmlelements');
            $link->href = "javascript:history.back();";
            $link->link = $this->objLanguage->languageText( 'word_back' );
            $arr = array( 'LINK' => $link->show() );
            $lngCode = 'mod_groupadmin_errNotAdmin';
            $errMessage = $this->objLanguage->code2Txt( $lngCode, 'groupadmin',$arr );
            $this->setVar('errMessage', $errMessage );
            return 'error_tpl.php';
        }
        
        $this->setLayoutTemplate( "groupadmin_layout_tpl.php" );
        switch ( $action ) {
            case 'main' : return $this->showMain();
            case 'main_form' : return $this->processMainForm();

            case 'create' : return $this->showCreate();
            case 'create_form' : return $this->processCreateForm();

            case 'edit' : return $this->showEdit();
            case 'edit_form' : return $this->processEditForm();

            case 'delete' : return $this->showDelete();
            case 'delete_form' : return $this->processDeleteForm();

            case 'view' : return $this->showView();
            case 'view_form' : return $this->processViewForm();

            default : return $this->showMain();
        } 
    } 
    // /////////////////////////////////////////////////////////
    // ------------------------ Main -------------------------//
    // /////////////////////////////////////////////////////////
    /**
    * Method to show the main view
    * 
    * @access private 
    * @return string the template file name.
    */
    function showMain()
    {
        $groupId = $this->groupId();
        $objLanguage = &$this->objLanguage;
        $objGroupAdminModel = &$this->objGroupAdminModel; 
        // On first entry set groupId to first root node | null.
        if ( !$groupId ) {
            $groupRoot = $this->objGroupAdminModel->getRoot();
            $groupId = null;
        } else if ( !$this->objGroupAdminModel->valueExists( 'id', $groupId ) ) {
            $groupRoot = $this->objGroupAdminModel->getRoot();
            $groupId = null;
            $this->setVar( 'groupId', $groupId );
        } 

        // Groups tree menu
        $treeNav = $this->objTree->showDHTML();

        // Create the link buttons
        $lnkEdit = &$this->lnkEdit( $groupId );
        $lnkDelete = &$this->lnkDelete( $groupId );
        $lnkCreate = &$this->lnkCreate();
        $lnkIcnCreate = &$this->lnkIcnCreate();
        
        // Initialise variables
        $info = $this->infoGroup( $groupId );
        $fullPath = $info['fullPath'];
        $groupName = $info['groupName'];
        $groupDesc = $info['groupDesc'];
        $nodeList = '';
        $hdrFirstName = $objLanguage->languageText( 'mod_groupadmin_hdrFirstName','groupadmin' );
        $hdrSurname = $objLanguage->languageText( 'mod_groupadmin_hdrSurName' ,'groupadmin');
        $pageTitle = $objLanguage->languageText( 'mod_groupadmin_ttlGroupAdmin','groupadmin' );
        $nodeTitle = $objLanguage->languageText( 'mod_groupadmin_hdrMemberList','groupadmin' ) . ": " . $fullPath;
        $nodeControls = implode( "&nbsp;/&nbsp;", array( $lnkEdit->show(), $lnkDelete->show() ) );
        $treeControls = $lnkCreate->show();

        // Group selected?
        if ( $groupId ) {
            // Get the members of the group
            $this->objMembers->setGroupId( $groupId );
            $this->objMembers->setHeaders( array( $hdrFirstName , $hdrSurname ) );
            // Folder statistics : Count members in all subfolders.
            $totalCount = $this->objMembers->getTotalCount();
            // Members : Show the members in all subfolders
            $nodeList = ( $totalCount > 0 ) ? $this->objMembers->show() : "&nbsp;";
        }
        
        
        
        // Abstract Path
        foreach ($this->abstractionArray as $name=>$value)
        {
            $fullPath = str_replace($name, $value, $fullPath);
        }

        // PACKAGE TEMPLATE variables
        $this->setVar( 'rightInfo', $this->getLegend() );
        $this->setVar( 'pageTitle', $pageTitle );
        $this->setVar( 'fullPath', $fullPath );
        $this->setVar( 'lnkIcnCreate', $lnkIcnCreate );
        $this->setVar( 'treeNav', $treeNav );
        $this->setVar( 'treeControls', $treeControls );
        $this->setVar( 'nodeList', $nodeList );
        $this->setVar( 'nodeControls', $nodeControls );

        return 'main_tpl.php';
    } 

    /**
    * Method to get the tree legend.
    *
    * @public private
    * @return object the tree legend for display in the right column.
    */
    function getLegend()
    {
        // HTML ELEMENTS
        $tblInfo = &$this->newObject( 'htmltable','htmlelements' );
        $objIcon = &$this->newObject( 'geticon', 'htmlelements' );
        $rightInfo = &$this->newObject('tabbedbox','htmlelements');

        // LANGUAGE TEXT
        $lng = &$this->objLanguage;
        $lblLegend = ucfirst($lng->code2Txt("mod_groupadmin_lblLegend",'groupadmin'));
        $lblWhite = ucfirst($lng->code2Txt("mod_groupadmin_lblWhite",'groupadmin'));
        $lblGreen = ucfirst($lng->code2Txt("mod_groupadmin_lblGreen",'groupadmin'));
        $lblOrange = ucfirst($lng->code2Txt("mod_groupadmin_lblOrange",'groupadmin'));

        // INITIALISE
        $tblInfo->cellspacing = 5;
        $tblInfo->cellpadding = 2;
        $objTree = &$this->getObject('groupadmin_tree');

        // Get icon info.
        $imageFolder = $objTree->_treeIcons;
        $arrFolders = $objTree->_arrTreeIcons;
		
        $arrFolders['empty']['meaning'] = $lblWhite;
        $arrFolders['context']['meaning'] = $lblGreen;
        $arrFolders['members']['meaning'] = $lblOrange;

        // INSERT ROWS
        foreach( $arrFolders as $key=>$folder ) {
            if($key=='root') continue; // Ignore root icon
            $objIcon->setIcon($folder['open'],'gif',$imageFolder);
            $objIcon->title = $folder['meaning'];
            $tblInfo->startRow();
                $tblInfo->addCell('<nobr>'.$objIcon->show().'-</nobr>',null,'center');
                $tblInfo->addCell($folder['meaning']);
            $tblInfo->endRow();
        }

        // INSERT TABLE
        $rightInfo->addTabLabel($lblLegend);
        $rightInfo->addBoxContent($tblInfo->show());
        
        // RETURN
        return $rightInfo;
    }

    /**
    * Method to process the main form post/get elements
    * 
    * @access private 
    * @return string the template file name.
    */
    function processMainForm()
    {
        $returnAction = 'main';
        
        if ( $this->getParam( 'btnCreate' ) ) {
            $returnAction = 'create';
        } 
        $groupId = $this->groupId();
        if ( $groupId ) {
            if ( $this->getParam( 'btnEdit' ) ) {
                $returnAction = 'edit';
            } 

            if ( $this->getParam( 'btnDelete' ) ) {
                $returnAction = 'delete';
            } 
        } 

        return $this->nextAction(array('action'=>$returnAction));//$this->showMain();
    } 
    // ///////////////////////////////////////////////////
    // ---------------------- Create -------------------//
    // ///////////////////////////////////////////////////
    /**
    * Method to show the create group view
    * 
    * @access private 
    * @return string the template file name.
    */
    function showCreate()
    {
        $objLanguage = &$this->objLanguage;

        $oldName = $this->getParam( 'oldName', NULL );
        $this->setVar( 'oldName', $oldName );
        $errName = NULL;
        
        $oldDescription = $this->getParam( 'oldDescription', NULL );
        $this->setVar( 'oldDescription', $oldDescription );
        $errDesc = NULL;
        
        $valid = $this->getParam( 'valid','yes' )=='yes';
        $this->setVar( 'valid', $valid );

        if( empty($oldDescription )&&!$valid) {
            $focusObject = 'tinDescription';
            $errDesc = ' <SPAN class="error">Only letters and numbers allowed.</SPAN>';
        }

        if( empty($oldName)&&!$valid ){
            $focusObject = 'tinName';
            $errName = ' <SPAN class="error">Only letters and numbers allowed.</SPAN>';
        }

        if( $valid ) {
            $focusObject = 'tinName';
        }
            
        $bodyParams = "onload=\"document.frmCreate['$focusObject'].focus();document.frmCreate['$focusObject'].select();\"";
        $this->objEngine->setVar( 'bodyParams', $bodyParams );

        if ( $valid ) {
            $str = 'mod_groupadmin_msgSaved';
        } else {
            $str = 'mod_groupadmin_msgNotSaved';
        } 

        if ( $this->getParam( 'confirm' ) ) {
            $arrOfRep = array( 'TIMESTAMP' => date( "h:i:s" ) );
            $confirmMsg = $objLanguage->code2Txt( $str, $arrOfRep );
            $this->setVar( 'confirmMsg', $confirmMsg );
        }
         
        // Page Title
        $pageTitle = $objLanguage->languageText( 'mod_groupadmin_ttlCreateGroup' ,'groupadmin' ); 
        // Form Object
        $form = &$this->getObject( 'form', 'htmlelements' );
        $form->name = 'frmCreate';
        $form->displayType = '3';
        $form->action = $this->uri ( array( 'action' => 'create_form' ) ); 
        // Form Title: Create a new group
        $lblTitle = $objLanguage->languageText( 'mod_groupadmin_ttlCreateGroup' ,'groupadmin' ); 
        // Form Content: Group name label
        $lblName = $objLanguage->languageText( 'mod_groupadmin_lblName','groupadmin'); 
        // Form Content: Group name text input element
        $tinName = &$this->newObject( 'textinput', 'htmlelements' );
        $tinName->name = 'tinName';
        $tinName->value = $oldName;

        // Form Content: Group description label
        $lblDesc = $objLanguage->languageText( 'mod_groupadmin_lblDescription','groupadmin' ); 
        // Form Content: Group description text input element
        $tinDesc = &$this->newObject( 'textinput', 'htmlelements' );
        $tinDesc->name = 'tinDescription';
        $tinDesc->value = $oldDescription;
        // Form Content: Parent/Home group label
        $lblParent = $lblGroupName = $objLanguage->languageText( 'mod_groupadmin_lblParent','groupadmin' ); 
        // Form Content: Parent/Home group drop down list element

        $ddbParent = &new tree_dropdown( $this->objTree->getTreeMenu() );
        $ddbParent->selected = $this->getParam('oldGroupId');
        
        // Form Controls: Save button
        $btnSave = &$this->newObject( 'button', 'htmlelements' );
        $btnSave->name = 'btnSave';
        $btnSave->value = $objLanguage->languageText( 'word_save');
        $btnSave->setToSubmit(); 
        // Link method
        $lnkSave = "<A href=\"#\" onclick=\"javascript:document.frmCreate['button'].value='saved'; document.frmCreate.submit()\">";
        $lnkSave .= $objLanguage->languageText( 'word_save' ) . "</A>"; 
        // Form Controls: Cancel button
        $btnCancel = &$this->newObject( 'button', 'htmlelements' );
        $btnCancel->name = 'btnCancel';
        $btnCancel->value = $objLanguage->languageText( 'word_cancel');
        $btnCancel->setToSubmit(); 
        // Link method
        $lnkCancel = "<A href=\"#\" onclick=\"javascript:document.frmCreate['button'].value='cancel'; document.frmCreate.submit()\">";
        $lnkCancel .= $objLanguage->languageText( 'word_cancel' ) . "</A>"; 
        // FORM CONTENT
        $formContent = '<DIV id=blog-content>'; 
        // GROUP NAME
        $formContent .= '<DIV id=formline>';
        $formContent .= '<DIV id=formlabel>' . $lblName . ':</DIV>';
        $formContent .= '<DIV id=formelement>' . $tinName->show() . $errName . '</DIV>';
        $formContent .= '</DIV>'; 
        // GROUP DESCRIPTION
        $formContent .= '<DIV id=formline>';
        $formContent .= '<DIV id=formlabel>' . $lblDesc . ':</DIV>';
        $formContent .= '<DIV id=formelement>' . $tinDesc->show() . $errDesc . '</DIV>';
        $formContent .= '</DIV>'; 
        // PARENT/HOME GROUP
        $formContent .= '<DIV id=formline>';
        $formContent .= '<DIV id=formlabel>' . $lblParent . ':</DIV>';
        $formContent .= '<DIV id=formelement>' . $ddbParent->show() . '</DIV>';
        $formContent .= '</DIV>';
        $formContent .= '</DIV>';
        $form->addToForm( $formContent ); 
        // FORM CONTROLS
        $formControls = '<DIV id=blog-footer>';
        $formControls .= $lnkSave . "&nbsp;/&nbsp;";
        $formControls .= $lnkCancel;
        $formControls .= '</DIV>';
        $form->addToForm( $formControls );
        $form->addToForm( "<input type='hidden' name='button' value='saved'>" );
        $form->addToForm( "<input type='hidden' name='confirm' value='TRUE'>" ); 
        // Back link button
        $lnkBack = &$this->newObject( 'link', 'htmlelements' );
        $lnkBack->href = $this->uri ( array() );
        $lnkBack->link = $objLanguage->languageText( 'mod_groupadmin_back','groupadmin' );

        $confirm = $this->getParam( 'confirm' ) ? TRUE : FALSE;
        $objTimeoutMessage1 = &$this->getObject( 'timeoutmessage', 'htmlelements' );
        $objTimeoutMessage1->cssId = 'confirmTimeout';
        $objTimeoutMessage1->showJScript();

        $this->setVar( 'pageTitle', $pageTitle );
        $this->setVar( 'lblName', $lblName );
        $this->setVar( 'lblDesc', $lblDesc );
        $this->setVar( 'confirm', $confirm );
        $this->setVar( 'lnkBack', $lnkBack );
        $this->setVar( 'form', $form );

        return 'creategroup_tpl.php';
    } 

    /**
    * Method to process the create form post/get elements
    * 
    * @access private 
    * @return string the template file name.
    */
    function processCreateForm()
    {
        $validName = $this->objStrValidate->isAlphaNumeric( $this->getParam( 'tinName' ) );
        $validDescription = $this->objStrValidate->isAlphaNumeric( $this->getParam( 'tinDescription' ) );
        $groupId = $this->getParam( 'HTML_TreeMenu_Dropdown_1' ) == '' ? null : $this->getParam( 'HTML_TreeMenu_Dropdown_1' );

        $returnAction = 'main';
        $data = array();
        
        if ( $this->getParam( 'button' ) == 'saved' ) {
            if ( $validName && $validDescription ) {
                $newGroupId = $this->objGroupAdminModel->addGroup( $this->getParam( 'tinName' ),
                    $this->getParam( 'tinDescription' ),
                    $groupId
                    );
                $data['valid'] = 'yes';
                $data['groupId'] = $newGroupId;
                $returnAction = 'edit';
            } else {
                $data['oldName'] = $validName ? $this->getParam( 'tinName' ) : NULL;
                $data['oldDescription'] = $validDescription ? $this->getParam( 'tinDescription' ) : NULL;
                $data['oldGroupId'] = $groupId;
                $data['valid'] = 'no';
                $returnAction = 'create';
            }
        } 

        if ( $this->getParam( 'button' ) == 'cancel' ) {
            $returnAction = 'main';
        } 

        return $this->nextAction( $returnAction, $data );
    } 
    // /////////////////////////////////////////////////
    // -------------------- Edit ---------------------//
    // /////////////////////////////////////////////////
    /**
    * Method to show the edit group members view
    * 
    * @access private 
    * @return string the template file name.
    */
    function showEdit()
    {
        $objLanguage = &$this->objLanguage;
        $objGroupAdminModel = &$this->objGroupAdminModel;

        $groupId = $this->groupId();
        $memberList = $this->memberList();
        $usersList = $this->usersList();

        if ( $this->groupId == '' ) {
            $errMsg = 'unknown group';
            $this->setVar( 'errorMsg', $errMsg );
        } 

        if ( $this->getParam( 'confirm' ) ) {
            $str = 'mod_groupadmin_msgSaved';
            $arrOfRep = array( 'TIMESTAMP' => date( "h:i:s" ) );
            $confirmMsg = $objLanguage->code2Txt( $str, 'groupadmin',$arrOfRep );
            $this->setVar( 'confirmMsg', $confirmMsg );
        } 
        // Context aware
        if ( $this->getParam( 'return' ) == 'context' ) {
            $this->setLayoutTemplate( "context_layout_tpl.php" );
        } 
        // Members list dropdown
        $this->loadClass('dropdown', 'htmlelements');
        $lstMembers = new dropdown('list2[]');
        //$lstMembers->name = 'list2[]';
        $lstMembers->extra = ' style="width:100pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list2[]\'],this.form[\'list1[]\'],true)"';
        foreach ( $memberList as $user ) {
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstMembers->addOption( $userPKId, $fullName );
        } 
        // Users list dropdown
        $lstUsers = new dropdown('list1[]');
        $lstUsers->extra = ' style="width:100pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list1[]\'],this.form[\'list2[]\'],true)"';
        foreach ( $usersList as $user ) {
            $fullName = $user['firstname'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstUsers->addOption( $userPKId, $fullName );
        } 
        // Build the nonMember table.
        $hdrUsers = $objLanguage->languageText( 'mod_groupadmin_hdrUsers','groupadmin' );
        $tblUsers = '<table><tr><th>' . $hdrUsers . '</th></tr><tr><td>' . $lstUsers->show() . '</td></tr></table>'; 
        // Build the Member table.
        $hdrMemberList = $objLanguage->languageText( 'mod_groupadmin_hdrMemberList','groupadmin' );
        $tblMembers = '<table><tr><th>' . $hdrMemberList . '</th></tr><tr><td>' . $lstMembers->show() . '</td></tr></table>'; 
        // The save button
        $btnSave = $this->newObject( 'button', 'htmlelements' );
        $btnSave->name = 'btnSave';
        $btnSave->cssClass = null;
        $btnSave->value = $objLanguage->languageText( 'word_save' );
        $btnSave->onclick = "selectAllOptions(this.form['list2[]'])";
        $btnSave->setToSubmit(); 
        // The save link button
        $btnSave = $this->newObject( 'button', 'htmlelements' );
        $btnSave->name = 'btnSave';
        $btnSave->cssClass = null;
        $btnSave->value = $objLanguage->languageText( 'word_save' );
        $btnSave->onclick = "selectAllOptions(this.form['list2[]'])";
        $btnSave->setToSubmit(); 
        // Link method
        $lnkSave = "<A href=\"#\" onclick=\"javascript:selectAllOptions(document.frmEdit['list2[]']); document.frmEdit['button'].value='save'; document.frmEdit.submit()\">";
        $lnkSave .= $objLanguage->languageText( 'word_save' ) . "</A>"; 
        // The cancel button
        $btnCancel = $this->newObject( 'button', 'htmlelements' );
        $btnCancel->name = 'btnCancel';
        $btnCancel->value = $objLanguage->languageText( 'word_Cancel' );
        $btnCancel->setToSubmit(); 
        // Link method
        $lnkCancel = "<A href=\"#\" onclick=\"javascript:document.frmEdit['button'].value='cancel'; document.frmEdit.submit()\">";
        $lnkCancel .= $objLanguage->languageText( 'word_cancel' ) . "</A>"; 
        // Form control buttons
        $buttons = array( $lnkSave, $lnkCancel ); 
        // The move selected items right button
        $btnRight = $this->newObject( 'button', 'htmlelements' );
        $btnRight->name = 'right';
        $btnRight->value = htmlspecialchars( '>>' );
        $btnRight->onclick = "moveSelectedOptions(this.form['list1[]'],this.form['list2[]'],true)"; 
        // Link method
        $lnkRight = "<A href=\"#\" onclick=\"javascript:moveSelectedOptions(document.frmEdit['list1[]'],document.frmEdit['list2[]'],true)\">";
        $lnkRight .= htmlspecialchars( '>>' ) . "</A>"; 
        // The move all items right button
        $btnRightAll = $this->newObject( 'button', 'htmlelements' );
        $btnRightAll->name = 'right';
        $btnRightAll->value = htmlspecialchars( 'All >>' );
        $btnRightAll->onclick = "moveAllOptions(this.form['list1[]'],this.form['list2[]'],true)"; 
        // Link method
        $lnkRightAll = "<A href=\"#\" onclick=\"javascript:moveAllOptions(document.frmEdit['list1[]'],document.frmEdit['list2[]'],true)\">";
        $lnkRightAll .= htmlspecialchars( 'All >>' ) . "</A>"; 
        // The move selected items left button
        $btnLeft = $this->newObject( 'button', 'htmlelements' );
        $btnLeft->name = 'left';
        $btnLeft->value = htmlspecialchars( '<<' );
        $btnLeft->onclick = "moveSelectedOptions(this.form['list2[]'],this.form['list1[]'],true)"; 
        // Link method
        $lnkLeft = "<A href=\"#\" onclick=\"javascript:moveSelectedOptions(document.frmEdit['list2[]'],document.frmEdit['list1[]'],true)\">";
        $lnkLeft .= htmlspecialchars( '<<' ) . "</A>"; 
        // The move all items left button
        $btnLeftAll = $this->newObject( 'button', 'htmlelements' );
        $btnLeftAll->name = 'left';
        $btnLeftAll->value = htmlspecialchars( 'All <<' );
        $btnLeftAll->onclick = "moveAllOptions(this.form['list2[]'],this.form['list1[]'],true)"; 
        // Link method
        $lnkLeftAll = "<A href=\"#\" onclick=\"javascript:moveAllOptions(document.frmEdit['list2[]'],document.frmEdit['list1[]'],true)\">";
        $lnkLeftAll .= htmlspecialchars( 'All <<' ) . "</A>"; 
        // The move items (Insert and Remove) buttons
        $btns = array( $lnkRight, $lnkRightAll, $lnkLeft, $lnkLeftAll );
        $tblInsertRemove = '<div>' . implode( '<BR><BR>', $btns ) . '</div>'; 
        // Form Layout Elements
        $tblLayout = &$this->newObject( 'htmltable', 'htmlelements' );
        $tblLayout->row_attributes = 'align=center';
        $tblLayout->width = '99%';
        $tblLayout->startRow();
        $tblLayout->addCell( $tblUsers, null, null );
        $tblLayout->addCell( $tblInsertRemove, null, null );
        $tblLayout->addCell( $tblMembers, null, null );
        $tblLayout->endRow(); 
        // Title and Header
        $ttlEditGroup = $objLanguage->languageText( 'mod_groupadmin_ttlEditGroup','groupadmin' );
        $hlpEditGroup = $objLanguage->languageText( 'mod_groupadmin_hlpEditGroup','groupadmin' );
        $hdrEditGroup = $objLanguage->languageText( 'mod_groupadmin_hdrEditGroup','groupadmin' ); 
        // Get the Group name and path
        $fullPath = $objGroupAdminModel->getFullPath( $groupId );

        if ( isset( $errorMsg ) ) {
            $fullPath = $errorMsg;
        } 
        // Context Home Icon
        $lblContextHome = $this->objLanguage->languageText( "word_course" ) . ' ' . $this->objLanguage->languageText( "word_home" );
        $icnContextHome = &$this->newObject( 'geticon', 'htmlelements' );
        $icnContextHome->setIcon( 'home' );
        $icnContextHome->alt = $lblContextHome;

        $lnkContextHome = &$this->newObject( 'link', 'htmlelements' );
        $lnkContextHome->href = $this->uri( array(), 'context' );
        $lnkContextHome->link = $icnContextHome->show() . $lblContextHome;

        $return = $this->getParam( 'return' ) == 'context' ? 'context' : 'main';
        $confirm = $this->getParam( 'confirm' ) ? TRUE : FALSE; 
        // Form Elements
        $frmEdit = &$this->getObject( 'form', 'htmlelements' );
        $frmEdit->name = 'frmEdit';
        $frmEdit->displayType = '3';
        $frmEdit->action = $this->uri ( array( 'action' => 'edit_form' ) );
        $frmEdit->addToForm( "<div id='blog-content'>" . $tblLayout->show() . "</div>" );
        $frmEdit->addToForm( "<div id='blog-footer'>" . implode( '&nbsp;', $buttons ) . "</DIV>" );
        $frmEdit->addToForm( "<input type='hidden' name='groupId' value='$groupId'>" );
        $frmEdit->addToForm( "<input type='hidden' name='return' value='$return'>" );
        $frmEdit->addToForm( "<input type='hidden' name='confirm' value='TRUE'>" );
        $frmEdit->addToForm( "<input type='hidden' name='button' value='saved'>" ); 
        // Back link button
        $lnkBack = &$this->newObject( 'link', 'htmlelements' );
        $lnkBack->href = $this->uri ( array() );
        $lnkBack->link = $objLanguage->languageText( 'mod_groupadmin_back' ,'groupadmin'); 
        // $lnkBack->cssClass = 'pseudobutton';
        $this->setVar( 'frmEdit', $frmEdit );
        $this->setVar( 'return', $return );
        $this->setVar( 'lnkContextHome', $lnkContextHome );
        $this->setVar( 'lnkBack', $lnkBack );
        $this->setVar( 'ttlEditGroup', $ttlEditGroup );
        $this->setVar( 'fullPath', $fullPath );
        $this->setVar( 'confirm', $confirm );

        return 'editgroup_tpl.php';
    } 

    /**
    * Method to show the context
    * 
    * @access private 
    * @return nothing 
    */
    function showContext()
    {
        header( 'Location:index.php?module=context' );
    } 

    /**
    * Method to redirect to correct template.
    * 
    * @access private 
    * @return the template file name.
    */
    function redirect( $redirect )
    {
        $groupId = $this->groupId();
        switch ( $redirect ) {
            case 'main':
                return $this->nextAction('main', array('groupId'=>$groupId));
            case 'context':
                return $this->showContext();
            case 'confirm' :
                return $this->nextAction('main', array('groupId'=>$groupId));
            default :
                return $this->nextAction('main', array('groupId'=>$groupId));
        } 
    } 

    /**
    * Method to process the edit form post/get elements
    * 
    * @access private 
    * @return string the template file name.
    */
    function processEditForm()
    {
        $groupId = $this->groupId();
        $redirect = 'edit';

        if ( $this->getParam( 'button' ) == 'save' && $groupId <> '' ) {
            // Get the revised member ids
            $list = $this->getParam( 'list2' ) ? $this->getParam( 'list2' ): array(); 
            // Get the original member ids
            $fields = array ( 'tbl_users.id' );
            $memberList = &$this->objGroupAdminModel->getGroupUsers( $groupId, $fields );
            $oldList = $this->objGroupAdminModel->getField( $memberList, 'id' ); 
            // Get the added member ids
            $addList = array_diff( $list, $oldList ); 
            // Get the deleted member ids
            $delList = array_diff( $oldList, $list ); 
            // Add these members
            foreach( $addList as $userId ) {
                $this->objGroupAdminModel->addGroupUser( $groupId, $userId );
            } 
            // Delete these members
            foreach( $delList as $userId ) {
                $this->objGroupAdminModel->deleteGroupUser( $groupId, $userId );
            } 
            return $this->redirect( 'confirm' );
        } 

        if ( $this->getParam( 'button' ) == 'cancel' ) {
        } 

        return $this->redirect( $this->getParam( 'return' ) );
    } 
    // ///////////////////////////////////////////////
    // ------------------- Delete ------------------//
    // ///////////////////////////////////////////////
    /**
    * Method to show the delete group view
    * 
    * @access private 
    * @return string the template file name.
    */
    function showDelete()
    {
        $groupId = $this->groupId();
        $objLanguage = $this->objLanguage;

        if ( $this->getParam( 'confirm' ) ) {
            $arrOfRep = array();
            $str = 'mod_groupadmin_msgDeleted';
            $arrOfRep['GROUPNAME'] = $this->getParam( 'oldFullPath' );
            $arrOfRep['TIMESTAMP'] = date( "h:i:s" );
            $confirmMsg = $objLanguage->code2Txt( $str, 'groupadmin',$arrOfRep );
            $this->setVar( 'confirmMsg', $confirmMsg );
        } 
        // The form buttons
        $lblYes = $objLanguage->languageText( 'word_yes' );
        $lblNo = $objLanguage->languageText( 'word_no' );
        // Back link button
        $lnkBack = &$this->newObject( 'link', 'htmlelements' );
        $lnkBack->href = $this->uri ( array() );
        $lnkBack->link = $objLanguage->languageText( 'mod_groupadmin_back','groupadmin' );

        $btnYes = "<input type='submit' class='button' name='btnDelete' value='$lblYes'>";
        $btnNo = "<input type='submit' class='button' name='btnCancel' value='$lblNo'>";

        $lnkYes = "<A href=\"#\" onclick=\"javascript:document.frmDelete['button'].value='yes'; document.frmDelete.submit()\">";
        $lnkYes .= $objLanguage->languageText( 'word_yes' ) . "</A>";

        $lnkNo = "<A href=\"#\" onclick=\"javascript:document.frmDelete['button'].value='no'; document.frmDelete.submit()\">";
        $lnkNo .= $objLanguage->languageText( 'word_no' ) . "</A>";

        $buttons = array ( $lnkYes, $lnkNo );
        // Create the form
        $frmDeleteForm = &$this->getObject( 'form', 'htmlelements' );
        $frmDeleteForm->name = 'frmDelete';
        $frmDeleteForm->displayType = '3';
        $frmDeleteForm->action = $this->uri ( array( 'action' => 'delete_form' ) );
        $frmDeleteForm->addToForm ( implode ( '&nbsp;', $buttons ) );
        $frmDeleteForm->addToForm ( "<input type='hidden' name='action' value='delete_form'>" );
        $frmDeleteForm->addToForm ( "<input type='hidden' name='groupId' value='$groupId'>" );
        $frmDeleteForm->addToForm ( "<input type='hidden' name='button' value=''>" );
        $frmDeleteForm->addToForm ( "<input type='hidden' name='confirm' value='TRUE'>" );
        // Title with standard stuff
        $ttlDeleteGroup = $objLanguage->languageText( 'mod_groupadmin_ttlDeleteGroup','groupadmin' );
        $hlpDeleteGroup = $objLanguage->languageText( 'mod_groupadmin_hlpDeleteGroup','groupadmin' );
        $hdrDeleteGroup = $objLanguage->languageText( 'mod_groupadmin_hdrDeleteGroup','groupadmin' );

        $fullPath = $this->objGroupAdminModel->getFullPath( $groupId );
        $frmDeleteForm->addToForm ( "<input type='hidden' name='oldFullPath' value='$fullPath'>" );

        $confirm = $this->getParam( 'confirm' ) ? TRUE : FALSE;

        $this->setVar( 'ttlDeleteGroup', $ttlDeleteGroup );
        $this->setVar( 'fullPath', $fullPath );
        $this->setVar( 'lnkBack', $lnkBack );

        $this->setVar( 'frmDeleteForm', $frmDeleteForm );
        $this->setVar( 'confirm', $confirm );

        return 'deletegroup_tpl.php';
    } 

    /**
    * Method to process the delete form post/get elements
    * 
    * @access private 
    * @return string the template file name.
    */
    function processDeleteForm()
    {
        $groupId = $this->groupId();

        if ( $this->getParam( 'button' ) == 'yes' ) {
            $this->objGroupAdminModel->deleteGroup( $groupId );
            return $this->showDelete();
        } 

        if ( $this->getParam( 'button' ) == 'no' ) {
            return $this->showMain();
        } 

        return $this->showMain();
    } 

    /**
    * Method to get the group id
    * 
    * @access private 
    * @return string groupId param returned from the template.
    */
    function groupId()
    {
        $this->groupId = $this->getParam( 'groupId' );
        $this->setVar( 'groupId', $this->groupId );
        return $this->groupId;
    } 

    /**
    * Method to get all members of the current group.
    * 
    * @access private 
    * @return array containing the members.
    */
    function &memberList()
    {
        $objGAM = &$this->objGroupAdminModel; 
        // The member list of this group
        $fields = array ( 'firstName', 'surname', 'tbl_users.id' );
        $this->memberList = $objGAM->getGroupUsers( $this->groupId, $fields ); 
        // The template variable is set and it returns the member list
        $this->setVarByRef( 'memberList', $this->memberList );
        return $this->memberList;
    } 

    /**
    * Method to get all users not in the selected group.
    * 
    * @access private 
    * @return array of all non members.
    */
    function &usersList()
    {
        $objGAM = &$this->objGroupAdminModel; 
        // Users list need the firstname, surname, and userId fields.
        $fields = array ( 'firstName', 'surname', 'id' );
        $memberIds = $objGAM->getField( $this->memberList, 'id' );
        $filter = "'" . implode( "', '", $memberIds ) . "'";
        $this->usersList = &$objGAM->getUsers( $fields, " WHERE id NOT IN($filter) ORDER BY UPPER(firstName)" );

        //sort( $this->usersList );

        $this->setVarByRef( 'usersList', $this->usersList );
        return $this->usersList;
    }
    /**
    * Method to create the edit link button.
    * @access private
    * @param string $groupId The group to edit.
    * @return link a link object reference
    */
    function &lnkEdit( $groupId )
    {
        // Edit members link button
        $lnkEdit = &$this->newObject( 'link', 'htmlelements' );
        $lnkEdit->href = $this->uri ( array( 'action'=>'edit', 'groupId'=>$groupId ) );
        $lnkEdit->link = $this->objLanguage->languageText( 'mod_groupadmin_edit' ,'groupadmin');
        return $lnkEdit;
    }
    
    /**
    * Method to create the delete link button.
    * @access private
    * @param string $groupId The group to delete.
    * @return link a link object reference
    */
    function &lnkDelete( $groupId )
    {
        // Delete group and members link button
        $lnkDelete = &$this->newObject( 'link', 'htmlelements' );
        $lnkDelete->href = $this->uri ( array( 'action'=>'delete', 'groupId'=>$groupId ) );
        $lnkDelete->link = $this->objLanguage->languageText( 'mod_groupadmin_delete','groupadmin' );

        return $lnkDelete;
    }

    /**
    * Method to create the create link button.
    * @access private
    * @return link a link object reference
    */
    function &lnkCreate()
    {
        // Create a new group link button
        $lnkCreate = &$this->newObject( 'link', 'htmlelements' );
        $lnkCreate->href = $this->uri ( array( 'action' => 'create' ) );
        $lnkCreate->link = $this->objLanguage->languageText( 'mod_groupadmin_ttlCreateGroup','groupadmin' );

        return $lnkCreate;
    }

    /**
    * Method to create the create link button.
    * @access private
    * @return link a link object reference
    */
    function &lnkIcnCreate()
    {
        $icn = &$this->newObject( 'geticon', 'htmlelements' );
        $href = $this->uri ( array( 'action' => 'create' ) );
        $lnkIcnCreate = $icn->getAddIcon( $href );
        return $lnkIcnCreate;
    }
    /**
    * Method to gather group information.
    * @access private
    * @return array list of usefull group information.
    */
    function infoGroup( $groupId )
    {
        $info = array();
        // Full path
        $info['fullPath'] = $this->objGroupAdminModel->getFullPath( $groupId );
        // Group name
        $info['groupName'] = $this->objGroupAdminModel->getName( $groupId );
        // Group description
        $info['groupDesc'] = $this->objGroupAdminModel->getDescription( $groupId );

        return $info;
    }
} //end of class

?>

<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package contextgroups
* @subpackage controller
* @version 0.1
* @since 15 February 2005
* @author Jonathan Abrahams
* @filesource
*/
/**
* Controller class for the context groups module.
* Purpose of this module is to allow for context member management.
* It should hide the group information from the user.
* Target user: Lecturers.
* Precondition : User must be in a context.
* Tasks: Add/remove Lecturers, students, or guests.
*/
class contextgroups extends controller
{
    /**
    * @var groupadminmodel Object reference.
    */
    var $_objGroupAdmin;
    /**
    * @var dbcontect Object reference.
    */
    var $_objDBContext;
    /**
    * @var language Object reference.
    */
    var $_objLanguage;
    /**
    * @var contextcondition Object reference.
    */
    var $_objContextCondition;

    /**
    * Method to initialise the module.
    */
    function init()
    {
        $this->_objGroupAdmin = &$this->getObject('groupadminmodel','groupadmin');
        $this->_objDBContext = &$this->getObject('dbcontext','context');
        $this->_objLanguage = &$this->getObject('language','language');
        $this->_objContextCondition = &$this->getObject('contextcondition','contextpermissions');
        
        $this->lectGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Lecturers' ) );
        $this->studGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Students' ) );
        $this->guestGroupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), 'Guest' ) );
        
        $this->setVar( 'lectGroupId' , $this->lectGroupId );
        $this->setVar( 'studGroupId' , $this->studGroupId );
        $this->setVar( 'guestGroupId' , $this->guestGroupId );

        $this->setVar( 'linkToContextHome', $this->linkToContextHome() );
        
        $this->errCodes= array();
        // Action to take for errors.
        $this->errCodes['notInContext']= array(
            'action' => $this->linkToModule('word_home','postlogin'),
            'title'  => 'mod_contextgroups_ttlNotInContext',
            'error'  => 'mod_contextgroups_notInContext');
        $this->errCodes['notLect']= array(
            'action' => $this->linkBack('word_back'),
            'title'  => 'mod_contextgroups_ttlNotLect',
            'error'  => 'mod_contextgroups_notLect');

    }

    /**
    * Method used by the framework to handle messages.
    */
    function dispatch( $action )
    {
        // Precondition: Must be in context.
        if( !$this->_objDBContext->isInContext() ) {
            // If not in context redirect to error page.
            return $this->showError('notInContext');
        }
/*
        else // Only Admin and Lecturers users can access this module.
        if( !( $this->objEngine->_objUser->isAdmin() || $this->_objContextCondition->isContextMember('Lecturers') ) ) {
            return $this->showError('notLect');
        }
*/

        // Normal course of events.
        switch( $action ) {
            case 'lecturers_form' :
                return $this->processManage( 'Lecturers' );
            case 'students_form' :
                return $this->processManage( 'Students' );
            case 'guest_form' :
                return $this->processManage( 'Guest' );

            case 'manage_lect' :
                return $this->showManage('Lecturers');
            case 'manage_stud' :
                return $this->showManage('Students');
            case 'manage_guest' :
                return $this->showManage('Guest');

            case 'main':
            default : return $this->showMain();
        }
    }

    /**
    * Method to show the main template.
    * @return string template name.
    */
    function showMain( )
    {
        $objMembers = &$this->getObject('groupadmin_members','groupadmin');
        $objMembers->setHeaders( array( 'Firstname', 'Surname') );
        $this->setVarByRef('objMembers',$objMembers);

        $lnkLect = $this->newObject('link', 'htmlelements');
        $lnkLect->href = $this->uri( array( 'action'=>'manage_lect' ) );
        $lnkLect->link = $this->_objLanguage->code2Txt('mod_contextgroups_managelects',array('authors'=>''));
        $this->setVarByRef('lnkLect', $lnkLect );

        $lnkStud = $this->newObject('link', 'htmlelements');
        $lnkStud->href = $this->uri( array( 'action'=>'manage_stud' ) );
        $lnkStud->link = $this->_objLanguage->code2Txt('mod_contextgroups_managestuds',array('readonlys'=>''));
        $this->setVarByRef('lnkStud', $lnkStud );

        $lnkGuest = $this->newObject('link', 'htmlelements');
        $lnkGuest->href = $this->uri( array( 'action'=>'manage_guest' ) );
        $lnkGuest->link = $this->_objLanguage->languageText('mod_contextgroups_manageguests');
        $this->setVarByRef('lnkGuest', $lnkGuest );

        $title = $this->_objLanguage->code2Txt(
            'mod_contextgroups_ttlManage',
            array( 'TITLE'=> $this->_objDBContext->getTitle() ) );
            
        $this->setVar('ttlLecturers', $this->_objLanguage->code2Txt('mod_contextgroups_ttlLecturers',array('authors'=>'')) );        $this->setVar('title',$title);
        $this->setVar('ttlStudents', $this->_objLanguage->code2Txt('mod_contextgroups_ttlStudents') );
        $this->setVar('ttlGuests', $this->_objLanguage->languageText('mod_contextgroups_ttlGuest') );
        
        return 'main_tpl.php';
    }
    
    /**
    * Method to process the request to manage a member group.
    * @param string the group to be managed.
    */
    function processManage( $groupName )
    {
        $groupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), $groupName ) );
        if ( $this->getParam( 'button' ) == 'save' && $groupId <> '' ) {
            // Get the revised member ids
            $list = $this->getParam( 'list2' ) ? $this->getParam( 'list2' ): array();

            // Get the original member ids
            $fields = array ( 'tbl_users.id' );
            $memberList = &$this->_objGroupAdmin->getGroupUsers( $groupId, $fields );
            $oldList = $this->_objGroupAdmin->getField( $memberList, 'id' );
            // Get the added member ids
            $addList = array_diff( $list, $oldList );
            // Get the deleted member ids
            $delList = array_diff( $oldList, $list );
            // Add these members
            foreach( $addList as $userId ) {
                $this->_objGroupAdmin->addGroupUser( $groupId, $userId );
            }
            // Delete these members
            foreach( $delList as $userId ) {
                $this->_objGroupAdmin->deleteGroupUser( $groupId, $userId );
            }
        }
        if ( $this->getParam( 'button' ) == 'cancel' && $groupId <> '' ) {

        }
        // After processing return to main
        return $this->nextAction( 'main', array() );

    }
    /**
    * Method to show the manage member group template.
    * @param string the group to be managed.
    */
    function showManage( $groupName )
    {
        $groupId = $this->_objGroupAdmin->getLeafId( array( $this->_objDBContext->getContextCode(), $groupName ) );
        // The member list of this group
        $fields = array ( 'firstName', 'surname', 'tbl_users.id' );
        $memberList = $this->_objGroupAdmin->getGroupUsers( $groupId, $fields );
        $memberIds  = $this->_objGroupAdmin->getField( $memberList, 'id' );
        $filter = "'" . implode( "', '", $memberIds ) . "'";
        
        // Users list need the firstname, surname, and userId fields.
        $fields = array ( 'firstName', 'surname', 'id' );
        $usersList = $this->_objGroupAdmin->getUsers( $fields, " WHERE id NOT IN($filter)" );
        sort( $usersList );

        // Members list dropdown
        $lstMembers = $this->newObject( 'dropdown', 'htmlelements' );
        $lstMembers->name = 'list2[]';
        $lstMembers->extra = ' style="width:100pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list2[]\'],this.form[\'list1[]\'],true)"';
        foreach ( $memberList as $user ) {
            $fullName = $user['firstName'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstMembers->addOption( $userPKId, $fullName );
        }
		
		$tblLayoutM= &$this->newObject( 'htmltable', 'htmlelements' );
		$tblLayoutM->row_attributes = 'align=center ';
		$tblLayoutM->width = '100px';
		$tblLayoutM->startRow();
			$tblLayoutM->addCell( $this->_objLanguage->code2Txt('mod_contextgroups_ttl'.$groupName),null,null,null,'heading' );
		$tblLayoutM->endRow();
		$tblLayoutM->startRow();
			$tblLayoutM->addCell( $lstMembers->show() );
		$tblLayoutM->endRow();
        $this->setVarByRef('lstMembers', $tblLayoutM);
        
        // Users list dropdown
        $lstUsers = $this->newObject( 'dropdown', 'htmlelements' );
        $lstUsers->name = 'list1[]';
        $lstUsers->extra = ' style="width:100pt" MULTIPLE SIZE=10 onDblClick="moveSelectedOptions(this.form[\'list1[]\'],this.form[\'list2[]\'],true)"';
        foreach ( $usersList as $user ) {
            $fullName = $user['firstName'] . " " . $user['surname'];
            $userPKId = $user['id'];
            $lstUsers->addOption( $userPKId, $fullName );

        }
		$tblLayoutU= &$this->newObject( 'htmltable', 'htmlelements' );
		$tblLayoutU->row_attributes = 'align=center';
		$tblLayoutU->width = '100px';
		$tblLayoutU->startRow();
			$tblLayoutU->addCell( $this->_objLanguage->code2Txt('mod_contextgroups_ttlUsers'),'10%',null,null,'heading' );
		$tblLayoutU->endRow();
		$tblLayoutU->startRow();
			$tblLayoutU->addCell( $lstUsers->show() );
		$tblLayoutU->endRow();
        $this->setVarByRef('lstUsers', $tblLayoutU );

        // Link method
        $lnkSave = $this->newObject('link','htmlelements');
        $lnkSave->href  = '#';
        $lnkSave->extra = 'onclick="javascript:';
        $lnkSave->extra.= 'selectAllOptions( document.frmManage[\'list2[]\'] ); ';
        $lnkSave->extra.= 'document.frmManage[\'button\'].value=\'save\'; ';
        $lnkSave->extra.= 'document.frmManage.submit(); "';
        $lnkSave->link  = $this->_objLanguage->languageText( 'word_save' );

        $lnkCancel = $this->newObject('link','htmlelements');
        $lnkCancel->href  = '#';
        $lnkCancel->extra = 'onclick="javascript:';
        $lnkCancel->extra.= 'document.frmManage[\'button\'].value=\'cancel\'; ';
        $lnkCancel->extra.= 'document.frmManage.submit(); "';
        $lnkCancel->link  = $this->_objLanguage->languageText( 'word_cancel' );

        $ctrlButtons = array();
        $ctrlButtons['lnkSave'] = $lnkSave->show();
        $ctrlButtons['lnkCancel'] = $lnkCancel->show();
        $this->setVar('ctrlButtons',$ctrlButtons);

        $navButtons = array();
        $navButtons['lnkRight']    = $this->navLink('>>','Selected',"frmManage['list1[]']", "frmManage['list2[]']");
        $navButtons['lnkRightAll'] = $this->navLink('All >>','All',"frmManage['list1[]']", "frmManage['list2[]']");
        $navButtons['lnkLeft']     = $this->navLink('<<','Selected',"frmManage['list2[]']", "frmManage['list1[]']");
        $navButtons['lnkLeftAll']  = $this->navLink('All <<','All',"frmManage['list2[]']", "frmManage['list1[]']");
        $this->setVar('navButtons',$navButtons);

        $frmManage = &$this->getObject( 'form', 'htmlelements' );
        $frmManage->name = 'frmManage';
        $frmManage->displayType = '3';
        $frmManage->action = $this->uri ( array( 'action' => $groupName.'_form' ) );
        $frmManage->addToForm("<input type='hidden' name='button' value=''>");
        $this->setVarByRef('frmManage', $frmManage );

        $title = $this->_objLanguage->code2Txt(
            'mod_contextgroups_ttlManageMembers', array(
                'GROUPNAME'=>$groupName,
                'TITLE'=>$this->_objDBContext->getTitle() )
            );
        $this->setVar('title', $title );

        return 'manage_tpl.php';
    }
    
    /**
    * Method to create a navigation button link
    */
    function navLink( $linkText, $moveType, $from, $to )
    {
        $link = $this->newObject('link','htmlelements');
        $link->href  = '#';
        $link->extra = 'onclick="javascript:';
        $link->extra.= 'move'.$moveType.'Options';
        $link->extra.= '( document.'.$from;
        $link->extra.= ', document.'.$to;
        $link->extra.= ', true );"';
        $link->link  = htmlspecialchars( $linkText );
        return $link->show();
    }
    /**
    * Method to show the error message.
    * @param string The error code.
    * @return string template name.
    */
    function showError( $errCode )
    {
        // Extracts error, action, title for this error
        extract($this->errCodes[$errCode]);
        
        // Translate text.
        $errMessage = $this->_objLanguage->code2Txt($error,array('ACTION'=>$action));
        $errTitle = $this->_objLanguage->code2Txt($title);

        // Set template variables.
        $this->setVar('title', $errTitle );
        $this->setVar('errMessage',$errMessage);
        return 'error_tpl.php';
    }
    
    /**
    * Method to redirect to another module
    * @param string The link text.
    * @param string The module to be redirected to.
    * @param array The parameters that are needed.
    * @return string HTML link element.
    */
    function linkToModule( $linkName, $moduleName, $params = array() )
    {
        $link = $this->newObject('link','htmlelements');
        $link->href=$this->uri( $params,$moduleName);
        $link->link = $this->_objLanguage->languageText($linkName);
        return $link->show();
    }

    /**
    * Method to redirect to previous page
    * @param string The link text.
    * @return string HTML link element.
    */
    function linkBack( $linkName )
    {
        $link = $this->newObject('link','htmlelements');
        $link->href= "javascript:history.back();";
        $link->link = $this->_objLanguage->languageText($linkName);
        return $link->show();
    }
    
    /**
    * Method to redirect to context home.
    * @return string HTML link element.
    */
    function linkToContextHome()
    {
        $lblContextHome = $this->_objLanguage->languageText( "word_course" ) . ' ' . $this->_objLanguage->languageText( "word_home" );

        $icnContextHome = &$this->newObject( 'geticon', 'htmlelements' );
        $icnContextHome->setIcon( 'home' );
        $icnContextHome->alt = $lblContextHome;

        $lnkContextHome = &$this->newObject( 'link', 'htmlelements' );
        $lnkContextHome->href = $this->uri( array(), 'context' );
        $lnkContextHome->link = $icnContextHome->show() . $lblContextHome;
        return $lnkContextHome->show();
    }
}
?>

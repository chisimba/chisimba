<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check
/**
 * The examiners controller manages the examiners module
 * @author Kevin Cyster 
 * @copyright 2008, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package examiners
 */

class examiners extends controller {

    /**
    * @var object $objExamDisplay: The examdisplay class in the examiners module
    * @access public
    */
    public $objExamDisplay;
    
    /**
    * @var object $objExamDb: The dbexams class in the examiners module
    * @access public
    */
    public $objExamDb;
        
    /**
    * @var object $filePath: The file path to module description files
    * @access public
    */
    public $filePath;

    /**
    * @var bool $isAdmin: TRUE if the user is in the Admin group FALSE if not
    * @access public
    */
    public $isAdmin;

    /**
    * @var object $objGroup: The groupadminmodel class in the groupadmin module
    * @access public
    */
    public $objGroup;

    /**
    * @var object $objUser: The user class in the security module
    * @access public
    */
    public $objUser;

    /**
    * Method to initialise the controller
    * 
    * @access public
    * @return void
    */
    public function init()
    {
        $this->objExamDisplay = $this->newObject('examdisplay', 'examiners');
        $this->objExamDb = $this->newObject('dbexams', 'examiners');        
        $this->objGroup = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser = $this->getObject('user', 'security');
        $userId = $this->objUser->userId();
        $pkId = $this->objUser->PKId($userId);
        $this->isAdmin = $this->objUser->inAdminGroup($userId);
        
        $objConfig = $this->newObject('altconfig', 'config');
        $contentBasePath = $objConfig->getcontentBasePath();
        $this->filePath = $contentBasePath.'modules/examiners/';
        if(!is_dir($this->filePath)){
            mkdir($contentBasePath.'modules/', 0777);
            mkdir($contentBasePath.'modules/examiners', 0777);
        }
    }
    
    /**
    * Method the engine uses to kickstart the module
    * 
    * @access public
    * @param string $action: The action to be performed
    * @return void
    */
    function dispatch($action)
    {
        switch($action){
            case 'home':
                $templateContent = $this->objExamDisplay->showHome();
         		$this->setVarByRef('templateContent', $templateContent);
         		return 'template_tpl.php';
         		break;
                
             case 'faculties':
                $templateContent = $this->objExamDisplay->showFaculties();
         		$this->setVarByRef('templateContent', $templateContent);
         		return 'template_tpl.php';
         		break;
                
            case 'faculty':
                if($this->isAdmin){
                    $facId = $this->getParam('f');
                    $templateContent = $this->objExamDisplay->showAddEditFaculty($facId);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');	  
         		break;
                
            case 'save_faculty':
                if($this->isAdmin){                
                    $facId = $this->getParam('f');
                    $name = $this->getParam('name');
                    if($facId == ''){
                        $facId = $this->objExamDb->addFaculty($name);
                        $this->objExamDisplay->createFacultyGroups($facId, $name);
                    }else{
                        $this->objExamDb->editFaculty($facId, $name);
                        $this->objExamDisplay->editFacultyGroups($facId, $name);
                    }
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'delete_faculty':
                if($this->isAdmin){    
                    $facId = $this->getParam('f');
                    $this->objExamDb->deleteFaculty($facId);
                    $this->objExamDisplay->deleteFacultyGroups($facId);
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'fac_heads':
                if($this->isAdmin){
                    $facId = $this->getParam('f');
                    $templateContent = $this->objExamDisplay->showFacultyHeads($facId);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'save_fac_heads':
                if($this->isAdmin){
                    $facId = $this->getParam('f');
                    $members = $this->getParam('members');
                    $ids = explode('|', $members);
                    $nonmembers = $this->getParam('nonmembers');
                    $groupId = $this->objGroup->getId($facId, 'description');
                    $groupUsers = $this->objGroup->getGroupUsers($groupId, array(
                        'userid',
                        'firstname',
                        'surname',
                    ));
                    foreach($groupUsers as $user){
                        $pkId = $this->objUser->PKId($user['userid']);
                        $this->objGroup->deleteGroupUser($groupId, $pkId);
                    }
                    foreach($ids as $id){
                        if(!empty($id)){
                            $this->objGroup->addGroupUser($groupId, $id);
                        }
                    }
                    return $this->nextAction('fac_heads', array(
                        'f' => $facId,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'fac_users':
                if($this->isAdmin){
                    $facId = $this->getParam('f');
                    $field = $this->getParam('field');
                    $criteria = $this->getParam('criteria');
                    $count = $this->getParam('count');
                    $page = $this->getParam('page', 1);
                    $templateContent = $this->objExamDisplay->showFacSearch($facId, $field, $criteria, $count, $page);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'departments':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($userLevel != FALSE or $this->isAdmin){
                    $download = $this->getParam('download', 'FALSE');
                    $templateContent = $this->objExamDisplay->showDepartments($facId, $download);
             		$this->setVarByRef('templateContent', $templateContent);
             		return 'template_tpl.php';
         	    }
                return $this->nextAction('faculties', array(), 'examiners');
         	    break;
                
            case 'department':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($userLevel == 'facHead' or $this->isAdmin){
                    $depId = $this->getParam('d');
                    $templateContent = $this->objExamDisplay->showAddEditDepartment($facId, $depId);
             		$this->setVarByRef('templateContent', $templateContent);
         	  	    return 'template_tpl.php';
         	    }
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
                
            case 'save_department':
                $facId = $this->getParam('f');                
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($userLevel == 'facHead' or $this->isAdmin){
                    $depId = $this->getParam('d');
                    $name = $this->getParam('name');
                    if($depId == ''){
                        $depId = $this->objExamDb->addDepartment($facId, $name);
                        $this->objExamDisplay->createDepartmentGroups($facId, $depId, $name);
                    }else{
                        $this->objExamDb->editDepartment($depId, $name);
                        $this->objExamDisplay->editDepartmentGroups($depId, $name);
                    }
                    return $this->nextAction('departments', array(
                        'f' => $facId,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'delete_department':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($userLevel == 'facHead' or $this->isAdmin){
                    $depId = $this->getParam('d');
                    $this->objExamDb->deleteDepartment($depId);
                    $this->objExamDisplay->deleteDepartmentGroups($depId);
                    return $this->nextAction('departments', array(
                        'f' => $facId,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;

            case 'dep_heads':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($userLevel == 'facHead' or $this->isAdmin){
                    $depId = $this->getParam('d');
                    $templateContent = $this->objExamDisplay->showDepartmentHeads($facId, $depId);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'save_dep_heads':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($userLevel == 'facHead' or $this->isAdmin){
                    $depId = $this->getParam('d');
                    $members = $this->getParam('members');
                    $ids = explode('|', $members);
                    $nonmembers = $this->getParam('nonmembers');
                    $groupId = $this->objGroup->getId($depId, 'description');
                    $groupUsers = $this->objGroup->getGroupUsers($groupId, array(
                        'userid',
                        'firstname',
                        'surname',
                    ));
                    foreach($groupUsers as $user){
                        $pkId = $this->objUser->PKId($user['userid']);
                        $this->objGroup->deleteGroupUser($groupId, $pkId);
                    }
                    foreach($ids as $id){
                        if(!empty($id)){
                            $this->objGroup->addGroupUser($groupId, $id);
                        }
                    }
                    return $this->nextAction('dep_heads', array(
                        'f' => $facId,
                        'd' => $depId,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'dep_users':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($userLevel == 'facHead' or $this->isAdmin){
                    $depId = $this->getParam('d');
                    $field = $this->getParam('field');
                    $criteria = $this->getParam('criteria');
                    $count = $this->getParam('count');
                    $page = $this->getParam('page', 1);
                    $templateContent = $this->objExamDisplay->showDepSearch($facId, $depId, $field, $criteria, $count, $page);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'dep_admin':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($userLevel == 'facHead' or $userLevel == 'depHead' or $this->isAdmin){
                    $templateContent = $this->objExamDisplay->showDepartmentAdmin($facId, $depId);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'save_dep_admin':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($userLevel == 'facHead' or $userLevel == 'depHead' or $this->isAdmin){
                    $members = $this->getParam('members');
                    $ids = explode('|', $members);
                    $nonmembers = $this->getParam('nonmembers');
                    $groupId = $this->objGroup->getId($depId, 'description');
                    $groups = $this->objGroup->getSubgroups($groupId);
                    $groupUsers = $this->objGroup->getGroupUsers($groups[1], array(
                        'userid',
                        'firstname',
                        'surname',
                    ));
                    foreach($groupUsers as $user){
                        $pkId = $this->objUser->PKId($user['userid']);
                        $this->objGroup->deleteGroupUser($groups[1], $pkId);
                    }
                    foreach($ids as $id){
                        if(!empty($id)){
                            $this->objGroup->addGroupUser($groups[1], $id);
                        }
                    }
                    return $this->nextAction('dep_heads', array(
                        'f' => $facId,
                        'd' => $depId,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'admin_users':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($userLevel == 'facHead' or $userLevel == 'depHead' or $this->isAdmin){
                    $field = $this->getParam('field');
                    $criteria = $this->getParam('criteria');
                    $count = $this->getParam('count');
                    $page = $this->getParam('page', 1);
                    $templateContent = $this->objExamDisplay->showAdminSearch($facId, $depId, $field, $criteria, $count, $page);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'subjects':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $download = $this->getParam('download', 'FALSE');
                $templateContent = $this->objExamDisplay->showSubjects($facId, $depId, $download);
             	$this->setVarByRef('templateContent', $templateContent);
             	return 'template_tpl.php';
         		break;
                
            case 'subject':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
                    $subjId = $this->getParam('s');
                    $templateContent = $this->objExamDisplay->showAddEditSubject($facId, $depId, $subjId);
                    $this->setVarByRef('templateContent', $templateContent);
         		    return 'template_tpl.php';
                }else{
                    return $this->nextAction('faculties', array(), 'examiners');
                }
         		break;
                
            case 'save_subject':                
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
                    $subjId = $this->getParam('s');
                    $code = $this->getParam('code');
                    $name = $this->getParam('name');
                    $level = $this->getParam('level');
                    $status = $this->getParam('status');
                    if($status == '2'){
                        $year = $this->getParam('year');                    
                    }else{
                        $year = NULL;
                    }
                    $file = $_FILES;
                    if($subjId == ''){
                        $subjId = $this->objExamDb->addSubject($facId, $depId, $code, $name, $level, $status, $year);
                    }else{
                        $this->objExamDb->editSubject($subjId, $code, $name, $level, $status, $year);
                    }
                    if(is_uploaded_file($file['file']['tmp_name']) && $file['file']['error'] == 0){
                        $filename = explode('.', basename($file['file']['name']));
                        $ext = array_pop($filename);
                        move_uploaded_file($file['file']['tmp_name'], $this->filePath.$subjId.'.'.$ext);
                    }
                    return $this->nextAction('subjects', array(
                        'f' => $facId,
                        'd' => $depId,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'delete_subject':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
                    $subjId = $this->getParam('s');
                    $subject = $this->objExamDb->getSubjectById($subjId);
                    $this->objExamDb->deleteSubject($subjId);
                    $file = glob($this->filePath.$subject['course_code'].'.*');
                    if(!empty($file)){
                        unlink($this->filePath.basename($file[0]));               
                    }
                     return $this->nextAction('subjects', array(
                        'f' => $facId,
                        'd' => $depId,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;

            case 'examiners':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $templateContent = $this->objExamDisplay->showExaminers($facId, $depId);
                    $this->setVarByRef('templateContent', $templateContent);
         		    return 'template_tpl.php';
         		}
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
                
            case 'examiner':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $userId = $this->getParam('u');
                    $templateContent = $this->objExamDisplay->showAddEditExaminer($facId, $depId, $userId);
             		$this->setVarByRef('templateContent', $templateContent);
         	      	return 'template_tpl.php';
         		}
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
                
            case 'save_examiner':                
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $userId = $this->getParam('u');
                    $title = $this->getParam('title');
                    $name = $this->getParam('name');
                    $surname = $this->getParam('surname');
                    $org = $this->getParam('org');
                    $email = $this->getParam('email');
                    $tel = $this->getParam('tel');
                    $ext = $this->getParam('ext');
                    $cell = $this->getParam('cell');
                    $address = str_replace(',', "\n", $this->getParam('address'));
                    if($userId == ''){
                        $this->objExamDb->addExaminer($facId, $depId, $title, $name, $surname, $org, $email, $tel, $ext, $cell, $address);
                    }else{
                        $this->objExamDb->editExaminer($userId, $title, $name, $surname, $org, $email, $tel, $ext, $cell, $address);
                   }
                    return $this->nextAction('examiners', array(
                        'f' => $facId,
                        'd' => $depId,
                    ), 'examiners');
         		}
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'delete_examiner':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $userId = $this->getParam('u');
                    $this->objExamDb->deleteExaminer($userId);
                    return $this->nextAction('examiners', array(
                        'f' => $facId,
                        'd' => $depId,
                    ), 'examiners');
         		}
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'matrix':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $subjId = $this->getParam('s');
                    $year = $this->getParam('y');
                    $templateContent = $this->objExamDisplay->showMatrix($facId, $depId, $subjId, $year);
         		    $this->setVarByRef('templateContent', $templateContent);
         		    return 'template_tpl.php';
         		}
                return $this->nextAction('faculties', array(), 'examiners');
         		break;

            case 'edit_matrix':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $subjId = $this->getParam('s');
                    $year = $this->getParam('y');
                    $templateContent = $this->objExamDisplay->showEditMatrix($facId, $depId, $subjId, $year);
         	      	$this->setVarByRef('templateContent', $templateContent);
             		return 'template_tpl.php';
         		}
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
         		
            case 'save_matrix':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $subjId = $this->getParam('s');
                    $year = $this->getParam('y');
                    $first = $this->getParam('first');
                    $second = $this->getParam('second');
                    $moderate = $this->getParam('moderate');
                    $alternate = $this->getParam('alternate');
                    $remark = $this->getParam('remarking');
                    $firstRemark = $this->getParam('text_first');
                    $secondRemark = $this->getParam('text_second');
                    $moderateRemark = $this->getParam('text_moderate');
                    $alternateRemark = $this->getParam('text_alternate');
                    $remarkingRemark = $this->getParam('text_remarking');
                    if($first != ''){
                        $this->objExamDb->updateFirst($facId, $depId, $subjId, $year, $first, $firstRemark);
                    }
                    if($second != ''){
                        $this->objExamDb->updateSecond($facId, $depId, $subjId, $year, $second, $secondRemark);
                    }
                    if($moderate != ''){
                        $this->objExamDb->updateModerate($facId, $depId, $subjId, $year, $moderate, $moderateRemark);
                    }
                    if($alternate != ''){
                        $this->objExamDb->updateAlternate($facId, $depId, $subjId, $year, $alternate, $alternateRemark);
                    }
                    if($remark != ''){
                        $this->objExamDb->updateRemarking($facId, $depId, $subjId, $year, $remark, $remarkingRemark);
                    }
                    return $this->nextAction('matrix', array(
                        'f' => $facId,
                        'd' => $depId,
                        's' => $subjId,
                        'y' => $year,
                    ), 'examiners');
         		}
                return $this->nextAction('faculties', array(), 'examiners');
                break;
                
            case 'delete_matrix':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $subjId = $this->getParam('s');
                    $year = $this->getParam('y');
                    $this->objExamDb->deleteMatrix($facId, $depId, $subjId, $year);
                    return $this->nextAction('matrix', array(
                        'f' => $facId,
                        'd' => $depId,
                        's' => $subjId,
                        'y' => $year,
                    ), 'examiners');
         		}
                return $this->nextAction('faculties', array(), 'examiners');
                break;

            case 'dep_export':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
                    $templateContent = $this->objExamDisplay->showExportForDep($facId, $depId);
                    $this->setVarByRef('templateContent', $templateContent);
         		    return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
         		
            case 'dep_do_export':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
                    $option = $this->getParam('option');
                    $year = $this->getParam('y');
                    $download = $this->objExamDisplay->doDepExport($facId, $depId, $option, $year);
                    return $this->nextAction('subjects', array(
                        'f' => $facId,
                        'd' => $depId,
                        'download' => $download,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
                
            case 'dep_download':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel == 'facHead' or $userLevel == 'depHead'){
                    $this->setVarByRef('id', $depId);
                    $this->setPageTemplate('filedownload_page_tpl.php');
                    return 'filedownload_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
         		break;

            case 'fac_export':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($this->isAdmin or $userLevel == 'facHead'){
                    $templateContent = $this->objExamDisplay->showExportForFac($facId);
                    $this->setVarByRef('templateContent', $templateContent);
         		    return 'template_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
         		
            case 'fac_do_export':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($this->isAdmin or $userLevel == 'facHead'){
                    $option = $this->getParam('option');
                    $year = $this->getParam('y');
                    $download = $this->objExamDisplay->doFacExport($facId, $option, $year);
                    return $this->nextAction('departments', array(
                        'f' => $facId,
                        'download' => $download,
                    ), 'examiners');
                }
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
                
            case 'fac_download':
                $facId = $this->getParam('f');
                $userLevel = $this->objExamDisplay->userLevel($facId);
                if($this->isAdmin or $userLevel == 'facHead'){
                    $this->setVarByRef('id', $facId);
                    $this->setPageTemplate('filedownload_page_tpl.php');
                    return 'filedownload_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
         		break;
         		
            case 'download':
                $facId = $this->getParam('f');
                $depId = $this->getParam('d');
                $userLevel = $this->objExamDisplay->userLevel($facId, $depId);
                if($this->isAdmin or $userLevel != FALSE){
                    $file = $this->getParam('file');
                    $this->setVarByRef('file', $file);
                    $this->setVarByRef('type', 'download');
                    $this->setPageTemplate('filedownload_page_tpl.php');
                    return 'filedownload_tpl.php';
                }
                return $this->nextAction('faculties', array(), 'examiners');
                break;

 			default:
                return $this->nextAction('faculties', array(), 'examiners');
                break;
        }
    }
}
?>
<?php
/**
* pbladmin class extends controller.
* @package pbladmin
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
       ('You cannot view this page directly');
}
// end security check

/**
 * This class contains the administrative tools for the Problem Based Learning module.
 * The class is used to install and delete cases and classrooms for use in PBL.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbladmin
 * @version 1
 */

class pbladmin extends controller
{
    public $msg = '';
    public $caseid = '';
    public $isAdmin = FALSE, $isLecturer = FALSE, $isStudent = FALSE;
    public $username = '';
    public $contextcode = '';

    /**
     * Method to construct the class
     */
    public function init()
    {
        // Get instances of the pbladmin class pblparser
        $this->parser = &$this->getObject('pblparser');
        $this->textParser = &$this->getObject('textparser');
        
        // Get instances of the pbl classes
        $this->jscript = &$this->getObject('jscript', 'pbl');
        $this->dbCases = &$this->getObject('dbcases', 'pbl');
        $this->dbLoggedin = &$this->getObject('dbloggedin', 'pbl');
        $this->dbClassroom = &$this->getObject('dbclassroom', 'pbl');
        
        // Get instances of the html elements:
        $this->loadClass('dropdown', 'htmlelements');
        
        // Get an instance of the confirm delete object
        $this->objConfirm = &$this->getObject('confirm', 'utilities');
        // Get an instance of the language object
        $this->objLanguage = &$this->getObject('language', 'language');
        // Get an instance of the user object
        $this->objUser = &$this->getObject('user', 'security');
        // Get an instance of the context object
        $this->objContext = &$this->getObject('dbcontext', 'context');
        // Get an instance of the groups object
        $this->objGroups = &$this->getObject('groupAdminModel', 'groupadmin');
        // Get an instance of the groups usersdb object
        $this->objGroupUser = &$this->getObject('usersdb', 'groupadmin');
        $this->objHelp = &$this->newObject('helplink','help');
        $this->objDate = &$this->newObject('dateandtime','utilities');
        $this->objModules =& $this->newObject('modules','modulecatalogue');

        // Log this call if registered
        if(!$this->objModules->checkIfRegistered('logger', 'logger')){
            //Get the activity logger class
            $this->objLog=$this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
        }
    }

    /**
     * The standard dispatch method for the module.
     * The dispatch() method must
     * return the name of a page body template which will render the module
     * output (for more details see Modules and templating).
     */
    public function dispatch($action)
    {
        $incontext = FALSE;
        // check if in context, if yes get code
        if ($this->objContext->isInContext()) {
            $incontext = TRUE;
            $this->contextcode = $this->objContext->getContextCode();
            $this->context = $this->objContext->getTitle();
        } else {
            $this->context = $this->objLanguage->languageText('word_lobby');
            $this->contextcode = '';
        }
        // get user name
        $this->username = $this->objUser->userName();
        $this->userId = $this->objUser->userId();

        // get user group for permissions
        $this->setAccess($this->username);

        if(!($this->isAdmin) && !($this->isLecturer)){
            header('Location: '.$this->uri(array(''),'postlogin'));
        }

        switch ($action) {
            // upload an xml (.pbl) file
            case 'upload':
                $back = $this->getParam('back');
                if (isset($back) && !empty($back)){
                     return $this->nextAction('showcase');
                 }
                return $this->upload();

            // display template for creating a new case
            case 'createcase':
                $data=array();
                $data['num'] = 1;
                $data['minfo'] = FAlSE;
                $this->setVarByRef('data',$data);
                return 'create_tpl.php';

            case 'addmcq':
                $this->setVar('task', 'mcq');
                return 'addtask.php';

            case 'addcaq':
                $this->setVar('task', 'caq');
                return 'addtask.php';

            case 'addmore':
                $data = $_POST;
                $data['options'] = $this->getParam('options', 4);
                $this->setVarByRef('data', $data);
                return 'addtask.php';

            // add the new case to the database
            case 'create':
                $exit = $this->getParam('cancel');
                
                if (isset($exit) && !empty($exit)){
                    return $this->nextAction('showcase');
                }
                return $this->create();

            // delete a case
            case 'delete':
                $msg = '';
                $cname = $this->getParam('cname');
                $cid = $this->getParam('cid');

                if (!empty($cid)) {
                    if ($this->parser->unInstallCase($cname, $cid) === FALSE) {
                        $msg = '<p><b>' . $this->objLanguage->code2Txt('mod_pbladmin_casenotfound', 'pbladmin', array('casename' => $cname)).'</b></p>';
                        break;
                    }
                    $msg = '<p><b>' .$this->objLanguage->code2Txt('mod_pbladmin_casedeleted', 'pbladmin', array('casename' => $cname)). '</b></p>';
                }
                return $this->nextAction('showcase', array('msg' => $msg));

            // save a classroom in a case
            case 'saveclass':
                $exit = $this->getParam('exit');
                if(isset($exit) && !empty($exit)){
                    return $this->nextAction('');
                }
                return $this->saveClass();

            // Get students in a class and display the form to add or remove them
            case 'editstudents':
                return $this->editStudents();

            // add/remove students to/from a classroom
            case 'savestudents':
                return $this->addStudent();

            // from view cases - add / edit a class
            case 'gotoclass':
                $classId = $this->getParam('classlist', '');
                if(substr($classId, 0, 5) == '!new_'){
                    return $this->addClass();
                }
                return $this->nextAction('editclass', array('id' => $classId));

            // edit a classroom
            case 'editclass':
                $this->editClass();

            // add a new classroom
            case 'addclass':
                return $this->addClass();

            // delete a classroom
            case 'deleteclass':
                $did = $this->getParam('id');
                $this->dbClassroom->deleteClass($did);
                $this->dbLoggedin->removeFromClass(TRUE, $did);
                return $this->nextAction('');

            // show list of classrooms for a case
            case 'showcase':
                return 'admin_tpl.php';

            case 'moveclass':
                return $this->moveClass();

            default:
                return $this->home();
        }
        return $template;
    }

    /**
    * Method to build and display a list of pbl classes on the front page.
    * @return The template to display
    */
    public function home()
    {
        $code = $this->contextcode;
        if(empty($code)){
            $code = 'lobby';
        }
        
        $data = $this->dbClassroom->getClasses($code);
        if(!empty($data)){
            foreach($data as $key=>$line){
                $name = $this->dbCases->getEntry($line['caseid'], "ORDER BY caseid, name");
                $data[$key]['casename'] = $name['name'];
            }
        }
        // Get a list of cases for assigning to classes as a group.
        $cases = $this->getInstalledCases();

        $this->setVarByRef('data', $data);
        $this->setVarByRef('cases', $cases);
        return 'classlist_tpl.php';
    }

    /**
    * Method to assign a case to a selection of classes.
    * The method checks for a new start date and updates the class.
    */
    public function moveClass()
    {
        $fields = array();
        if(isset($_POST['save'])){
            if(isset($_POST['case'])){
                $case = $_POST['case'];
                $fields['caseid'] = $case;
            }
        }

        if(!empty($_POST['date'])){
            $date = $_POST['date'];
            $fields['opentime'] = $_POST['date'];
        }

        if(!empty($_POST['changecase'])){
            foreach($_POST['changecase'] as $line){
                $fields['activescene'] = '';
                $filter = "id='$line'";

                $this->dbClassroom->saveClass($fields, $filter);
            }
        }

        return $this->nextAction('');
    }

    /**
    * Method to get students in a class and display the form to add or remove them.
    * @return The template to display
    */
    public function editStudents()
    {
        $id = $this->getParam('id');
        $students = array();
        $users = array();
        $userIds = array();
        $newusers = array();

        // get the list of users in the student group for the context
        $groupid = $this->objGroups->getLeafId(array($this->contextcode,'Students'));

//        $fields['id'] = 'user_id as id';
//        $fields['name'] = "CONCAT( firstName, ' ', surname ) as name";
        $fields = array('user_id as id', 'firstname', 'surname');
        $filter = " LEFT JOIN tbl_pbl_loggedin AS log";
        $filter .= " ON (tbl_users.id=log.studentid) AND (log.classroomid='$id')";
        $filter .= " WHERE (log.studentid IS NULL)";
        $users = $this->objGroups->getGroupUsers($groupid, $fields, $filter);

        // Get the list of students in a class
        $filter = "position!='f' AND classroomid='$id' AND studentid != 'student'";
        $userIds = $this->dbLoggedin->findUserIds($filter);
        
        // find user name from table users
        if(!empty($userIds)){
            $i=0;
            foreach($userIds as $val){
                $student = $this->objGroupUser->getUsers(NULL," where id='".$val['studentid']."' ");
                if(!empty($student)){
                    $students[$i]['id'] = $val['studentid'];
                    $students[$i]['name'] = $student[0]['firstname'].' '.$student[0]['surname'];
                    $i++;
                }
            }
        }
        
        if(!empty($users)){
            if(!empty($students)){
                foreach($users as $item){
                    $skip = false;
                    foreach($students as $val){
                        if($item['id'] == $val['id']){
                            $skip = true;
                            break;
                        }
                    }
                    if(!$skip){
                        $newusers[] = $item;
                    }
                }
            }else{
                $newusers = $users;
            }
        }

        // Check for a confirm message
        $msg = $this->getParam('msg');
        if($msg){
            $this->setVarByRef('msg', $msg);
        }

        $this->setVarByRef('id', $id);
        $this->setVarByRef('users', $newusers);
        $this->setVarByRef('students', $students);
        return 'editstudents_tpl.php';
    }

    /**
    * Method to add or remove students from a class.
    * @return The next action: editstudents
    */
    public function addStudent()
    {
        $btnAction = $this->getParam('exit');
        $id = $this->getParam('id');
        $msg = 'false';
        if($btnAction == 'exit'){
            return $this->nextAction('editclass', array('id'=>$id));
        }
        if($btnAction == 'save'){
            // Get the revised member ids
            $list = $this->getParam( 'list2' ) ? $this->getParam( 'list2' ): array();

            // Get the original member ids
            $filter = "position!='f' AND classroomid='$id'";
            $list2 = $this->dbLoggedin->findUserIds($filter);

            // Get a single array of student ids
            $oldList = array();
            if(!empty($list2)){
                $oldList = $this->objGroups->getField($list2, 'studentid');
            }
                    
            if(!empty($list)){
                if(!empty($oldList)){
                    // Get the added member ids
                    $addList = array_diff( $list, $oldList );
                    
                    // Get the deleted member ids
                    $delList = array_diff( $oldList, $list );

                    // Add these members
                    if(!empty($addList)){
                        foreach($addList as $val) {
                            $this->dbLoggedin->addToClass($id, $val);
                         }
                    }
                        
                    // Delete these members
                    if(!empty($delList)){
                        foreach($delList as $val){
                            $this->dbLoggedin->removeFromClass(FALSE, $id, $val);
                        }
                    }
                    $msg = 'true';
                    
                }else{
                    // Add these members
                    foreach($list as $val) {
                        $this->dbLoggedin->addToClass($id, $val);
                    }
                    $msg = 'true';
                }
            }else{
                if(!empty($oldList)){
                    // Delete these members
                    foreach($oldList as $val){
                        $this->dbLoggedin->removeFromClass(FALSE, $id, $val);
                    }
                }
                        
            }
            return $this->nextAction('editstudents', array('id'=>$id, 'msg'=>$msg));
        }
    }

    /**
    * Method to upload a new pbl case.
    * The method determines if the case is text or xml and calls the appropriate parser.
    * @return The template to display
    */
    public function upload()
    {
        $back = $this->getParam('back');
        $save = $this->getParam('save');
        
        if(isset($back) && !empty($back)){
            return $this->nextAction('showcase');
        }
        
         if (isset($save) && !empty($save)){
             $tempfile = $_FILES['upload']['tmp_name'];

             if(!(strstr($_FILES['upload']['type'], 'text')===FALSE)){
                 $this->textParser->parseText($tempfile);
             }else{
                 if($this->parser->parse($tempfile)){
                     $msg = $this->objLanguage->languageText('mod_pbladmin_uploadsuccess', 'pbladmin');
                 }else{
                     $msg = $this->objLanguage->languageText('mod_pbladmin_uploadfailed', 'pbladmin').'!';
                 }
             }
             return $this->nextAction('showcase',array('msg'=>$msg));
         }
         return 'upload_tpl.php';
    }

    /**
    * Method to create a new case.
    * @return The template to display
    */
    public function create()
    {
        $continue = $this->getParam('continue');
        $finish = $this->getParam('finish');
        $minfo = $this->getParam('minfo');
        
        $code = $this->contextcode;
        if(empty($code)){
            $code = 'lobby';
        }
        
        $data=array();
        if(empty($minfo)){
            $result = $this->textParser->createCase($this->userId, $code);
            $data['minfo'] = $result['sceneid'];
            $data['casename'] = $result['casename'];
            $data['caseid'] = $result['caseid'];
        }else{
            $data['caseid'] = $this->getParam('caseid');
            $data['casename'] = $this->getParam('casename');
            $data['minfo'] = $this->textParser->setSceneAssocs($data['caseid']);
        }
        
        if (isset($finish) && !empty($finish)){
            $msg = $this->objLanguage->languageText('mod_pbladmin_casecreatedsuccess', 'pbladmin');
            return $this->nextAction('showcase',array('msg'=>$msg));
        }
        
        $data['num'] = ++$_POST['num'];
        $this->setVarByRef('data',$data);
        return 'create_tpl.php';
    }

    /**
    * Method to get details for a class to be edited.
    * @return
    */
    public function editClass()
    {
        $id = $this->getParam('id');
        $mode = 'edit';

        // Get class
        $class = $this->dbClassroom->getClass($id);

        // Get students in class
        $filter = "position!='f' AND classroomid='$id'";
        $userids = $this->dbLoggedin->findUserIds($filter);
        $users = array();
        if(!empty($userids)){
            $i=0;
            foreach($userids as $val){
                $user = $this->objGroupUser->getUsers(NULL," where id='".$val['studentid']."' ");
                if(isset($user) && !empty($user)){
                    $users[$i]['id'] = $val['studentid'];
                    $users[$i]['name'] = $user[0]['firstname'].' '.$user[0]['surname'];
                }
                $i++;
            }
        }

        $this->setVarByRef('mode', $mode);
        $this->setVarByRef('class', $class);
        $this->setVarByRef('students', $users);
    }

    /**
    * Method to display template to add or edit a class.
    * @return The template to display
    */
    public function addClass()
    {
        $code = $this->contextcode;
        if(empty($code)){
            $code = 'lobby';
        }
        
         // Get Lecturers
         $groupid = $this->objGroups->getLeafId(array($this->contextcode, 'Lecturers'));
         $lecturers = $this->objGroups->getGroupUsers($groupid, array('user_id as id', 'firstName', 'surname'));

         // Get Cases
         $cases = $this->dbCases->getCases("context='{$code}'");

         $this->setVarByRef('lecturers', $lecturers);
         $this->setVarByRef('cases', $cases);
         return 'editclass_tpl.php';
    }

    /**
    * Method to save a new or edited classroom.
    * @return The template to display.
    */
    public function saveClass()
    {
        $mode = $this->getParam('mode');

        $code = $this->contextcode;
        if(empty($code)){
            $code = 'lobby';
        }

        // Save class
        $fields = array();
        $caseid = $this->getParam('case');

        $fields['name'] = $this->getParam('class');
        $fields['owner'] = $this->userId;
        $fields['caseid'] = $caseid;
        $fields['context'] = $code;
        $fields['facilitator'] = $this->getParam('facilitator');
        $fields['chair'] = $this->getParam('chair');
        $fields['scribe'] = $this->getParam('scribe');
        $fields['status'] = $this->getParam('status');
        $fields['opentime'] = $this->getParam('timestamp');

        // if in edit mode, add class id as a filter
        if ($mode == 'edit') {
            $id = $this->getParam('id');
            $filter = "id='" . $id . "'";
            $this->dbClassroom->saveClass($fields, $filter);
        } else {
            $id = $this->dbClassroom->saveClass($fields);
        }

        // if class is open: add global student to the classroom
        if ($fields['status'] == 'o') {
            if ($this->dbLoggedin->isLoggedIn('student', $id) === FALSE) {
                $this->dbLoggedin->addToClass($id, 'student');
            }
        }
            // update associated classroom id for students
        //    $this->dbLoggedin->changeClassId($id);
        // update position of chair in group
        if($this->getParam('scribe')) {
            $this->dbLoggedin->setPosition($id, $this->getParam('scribe'), 's');
        }
        if($this->getParam('chair')){
            $this->dbLoggedin->setPosition($id, $this->getParam('chair'), 'c');
        }

        // assign the facilitator to the classroom if not already there and not virtual
        if($this->getParam('facilitator') != $this->objLanguage->languageText('word_virtual')){
            if(!$this->dbLoggedin->getPosition($id, $this->getParam('facilitator'))){
                $this->dbLoggedin->addToClass($id, $this->getParam('facilitator'));
            }
            $this->dbLoggedin->setPosition($id, $this->getParam('facilitator'), 'f');
        }

        $save = $this->getParam('save');
        if(isset($save) && !empty($save)){
            return $this->nextAction('');
        }

        // Go to edit students page
        return $this->nextAction('editstudents', array('id' => $id));
    }

    /**
     * Method to set the level of access the current user is allowed.
     *
     * @param string $username Username of current user
     * @return
     */
    public function setAccess($username)
    {
        // check if user has super admin rights
        $access = $this->objUser->isAdmin();
        if ($access) {
            $this->isAdmin = TRUE;
        } else {
            // get user id (pk id - used in groupadmin)
            $userid = $this->objUser->getUserId($username);
            $userId = $this->objUser->PKId($userid);
            // find member group from groups created in context
            if ($this->getGroup($userId, 'Lecturers')){
                $this->isLecturer = TRUE;
            }
            if ($this->getGroup($userId, 'Students')){
                $this->isStudent = TRUE;
            }
            if ($this->getGroup($userId, 'Guests')){
                $this->isGuest = TRUE;
            }
        }
    }

    /**
     * Method to check if the current user is a member of a group (lecturers, students, etc) in the current context
     *
     * @param string $userId User id
     * @param string $group Group to check
     * @return bool True if a member, FALSE if not
     */
    public function getGroup($userId, $group)
    {
        // get group id
        $groupid = $this->objGroups->getLeafId(array($this->contextcode, $group));
        // check if user is a member
        if ($this->objGroups->isGroupMember($userId, $groupid)) {
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Method to get a list of cases installed in the current context.
     *
     * @return array $cases Case info
     */
    public function getInstalledCases()
    {
        $code = $this->contextcode;
        if(empty($code)){
            $code = 'lobby';
        }
        
        $cases = array();
        $cases = $this->dbCases->getCases("context='{$code}'");
        return $cases;
    }

    /**
     * Method to create a dropdown list of classrooms for a specific case.
     *
     * @param string $cid Case id
     * @param string $num Number of dropdown
     * @param string $name Name of dropdown
     * @param string $currentid Id to be used as the selected element
     * @return string $drop A dropdown list of classrooms
     */
    public function getClassList($cid, $num = '1', $name = 'classlist', $currentid = '')
    {
        // get list of classrooms associated with case
        $drop = FALSE;
        $skip = FALSE;
        $list = array();
        $filter = "caseid='$cid'";
        $classlist = $this->dbClassroom->getName($filter);

        if ($classlist) {
            $i = '0';
            foreach($classlist as $k => $v) {
                foreach($v as $key => $val) {
                    if ($v['id'] == $currentid) {
                        $skip = TRUE;
                    } else {
                        $list[$i][$key] = $val;
                    }
                }
                if (!$skip) {
                    $i++;
                } else {
                    $skip = FALSE;
                }
            }
            if($skip){
                $i++;
            }
            $list[$i]['id'] = "!new_$cid";
            $list[$i]['name'] = $this->objLanguage->languageText('mod_pbladmin_createnewclass', 'pbladmin');
            $drop = $this->makeDropDown($list, $name, $num);
        } else {
            $list[0]['id'] = "!new_$cid";
            $list[0]['name'] = $this->objLanguage->languageText('mod_pbladmin_createnewclass', 'pbladmin');
            $drop2 = $this->makeDropDown($list, $name, $num);
            $drop = array('FALSE', $drop2);
        }
        return $drop;
    }

    /**
     * Method to create a dropdown list given an associative array of elements containing the keys id and name
     *
     * @param associative $ array $list Array of elements: id and name
     * @param string $name Name of dropdown
     * @param string $num Number of dropdown
     * @param string $selected Selected element
     * @param bool $custom Uses the id_name for the element name
     * @param string $extra Additional paramaters for the dropdown list eg size
     * @return string $objDropdown A dropdown list of elements
     */
    public function makeDropDown($list, $name, $num = '1', $selected = NULL, $custom = NULL, $extra = NULL)
    {
        $objDropdown[$num] = new dropdown($name);

        if ($extra){
            $objDropdown[$num]->extra = $extra;
        }
        $next = '-1';
        $set = $selected;

        if ($list) {
            foreach($list as $k => $item) {
                foreach($item as $key => $val) {
                    if ($key == 'id') {
                        $id = $val;
                        if ($k == $next) {
                            if ($custom) {
                                $objDropdown[$num]->addOption($id . '*' . $value, $value);
                                if ($set == $id){
                                    $set = $selected . '*' . $value;
                                }
                            } else {
                                $objDropdown[$num]->addOption($id, $value);
                            }
                        } else {
                            $next = $k;
                        }
                    } else if ($key == 'name') {
                        $value = $val;
                        if ($k == $next) {
                            if ($custom) {
                                $objDropdown[$num]->addOption($id . '*' . $value, $value);
                                if ($set == $id){
                                    $set = $selected . '*' . $value;
                                }
                            } else {
                                $objDropdown[$num]->addOption($id, $value);
                            }
                        } else {
                            $next = $k;
                        }
                    }
                }
            }
            if ($selected) {
                $objDropdown[$num]->setSelected($set);
            }
        } else {
            $objDropdown[$num]->addOption('none', 'empty');
        }
        return $objDropdown[$num]->show();
    }

    /**
    * Method to take a datetime string and reformat it as text.
    * @param string $date The date in datetime format.
    * @return string $ret The formatted date.
    *
    public function formatDate($date)
    {
        $ret = substr($date,8,2);
        $ret .= ' '.$this->objDate->monthFull(substr($date,5,2));
        $ret .= ' '.substr($date,0,4);

        $time = substr($date,11,5);
        if(!empty($time) && $time!=0){
            $ret .= ' '.$time;
        }
        return $ret;
    }*/
}

?>
<?php
/* ----------- gradebook object class ------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
 * Model class for the gradebook object
 */
class gradebookfunctions extends controller {
    //variable holding the user object
    public $objUser;

    //variable holding the language object
    public $objLanguage;

    //geticon object
    public $objGetIcon;

    //administrative evaluation
    public $isAdmin;

    //lecturer evaluation
    public $isLecturer;

    //database table
    public $table;

    //groupadmin variables
    public $objGroupMembers;
    public $objGroupAdmin;
    public $objDBContext;
    public $objMembers;
    public $studGroupId;

    //context variables
    public $contextObject;
    public $contextCode;

    /**
     * initialization function making declaration of the
     * whatsnew table to be used within the module
     */

    public function init() {
        //Get an instance of the user object
        $this->objUser = & $this->getObject('user', 'security');
        //get an instance of the permissions object
        $this->objPerm =& $this->getObject('contextcondition', 'contextpermissions');
        $this->isAdmin=$this->objUser->isAdmin();
        $this->isLecturer=$this->objPerm->isContextMember('Lecturers');
        //the getIcon object
        $this->objGetIcon =& $this->getObject('geticon', 'htmlelements');
        // Get an instance of the language object
        $this->objLanguage = & $this->getObject('language', 'language');
        //group management
        $this->objGroupMembers = &$this->getObject('groupadmin_members','groupadmin');
        $this->objGroupAdmin = &$this->getObject('groupadminmodel','groupadmin');
        $this->objDBContext = &$this->getObject('dbcontext','context');
        $this->objMembers = &$this->getObject('groupusersdb','groupadmin');
        //context management
        $this->contextObject =& $this->getObject('dbcontext', 'context');
        $this->userContext =& $this->getObject('usercontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
    }


    /**
     * function to retrieve info for a student within a context
     * results are returned in an array
     * @param: $field - which db field should be returned
     */

    public function getStudentInContextInfo($field) {
        $ar=array();
        //get the studentGroupId
        $contextCode = $this->contextObject->getContextCode();
        $this->studGroupId = $this->objGroupAdmin->getLeafId(array($this->contextObject->getContextCode(),'Students'));
        $contextStudents = $this->userContext->getContextStudents($contextCode);
        if(!empty($contextStudents)) {
            foreach($contextStudents as $contextStudent) {
                $ar[] = $this->objUser->getItemFromPkId($contextStudent["id"],$field);
            }
        }
        /*
		$this->objGroupMembers->setGroupId($this->studGroupId);
		//get the info
		$membersArr=array();
		$membersArr=$this->objMembers->getSubGroupUsers($this->studGroupId, array('*'));
		if(!empty($membersArr)){
			foreach($membersArr as $thenames) {
				$ar[] = $this->objUser->getItemFromPkId($thenames["user_id"],$field);
			}
        	}
        */
       // if($ar) {
            return $ar;
        //} else {
          //  return FALSE;
        //}
    }

    /**
     * function to retrieve the number of students in a given context
     */

    public function getNumberStudentsInContext() {
        //get the studentGroupId
        $this->studGroupId = $this->objGroupAdmin->getLeafId(array($this->contextObject->getContextCode(),'Students'));
        $this->objGroupMembers->setGroupId($this->studGroupId);

        $ar=array();
        //$ar=$this->objMembers->getSubGroupUsers($this->studGroupId, array('*'));
        $contextStudents = $this->userContext->getContextStudents($this->contextCode);
        $num=0;
        //$num=count($ar);
        $num=count($contextStudents);

        if($num) {
            return $num;
        } else {
            return '0';
        }
    }
}
?>

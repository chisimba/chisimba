<?php
/* -------------------- gradebook class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * controller class for the gradebook module
 * authors: Otim Samuel
 * date: 2005 05 06 (6th-May-2005)
 */

class gradebook extends controller {
    //user object - security and access rights validation
    public $objUser;

    //language object - multilingularity
    public $objLanguage;

    //logs
    public $objLog;

    //gradebook object
    public $objGradebook;

    //button object
    public $objButtons;

    //form object
    public $objForm;

    //heading
    public $heading;

    //heading object
    public $objHeading;

    //management of layer attributes <div tags etc
    public $objDiv;

    //context object
    public $objContext;

    //context code object
    public $contextCode;

    /**
     * initilization function - declaration of required objects
     */

    public function init() {
        $this->objUser=& $this->getObject('user','security');
        $this->objLanguage = & $this->getObject('language', 'language');
        $this->objForm = & $this->getObject('form','htmlelements');
        $this->objHeading =& $this->getObject('htmlheading','htmlelements');
        $this->objDiv =& $this->getObject('layer', 'htmlelements');
        $this->objPerm =& $this->getObject('contextcondition', 'contextpermissions');
        $this->objGradebook =& $this->getObject('gradebookfunctions','gradebook');
        //log activity from this class
        $this->objLog=$this->newObject('logactivity', 'logger');
        $this->objLog->log();
        $this->objButtons = & $this->getObject('navbuttons', 'navigation');
        // Get an instance of the context object
        $this->objContext = $this->getObject('dbcontext','context');
        //Get context code
        $this->contextCode = $this->objContext->getContextCode();
    }

    /**
     * dispatch() function for the gradebook module:
     * providing standard controlls for the module's logic and execution
     */

    public function dispatch($action) {
        //get the parameter from the querystring
        $action = $this->getParam("action", NULL);
        if ($this->contextCode == '') {
            return $this->nextAction(NULL, array('error'=>'notincontext'), '_default');
        }

        //assignment object
        $objAssignment = 0;
        $objAssignment = $this->getObject('dbassignment_old','assignment');
        $objAssignmentSubmit = 0;
        $objAssignmentSubmit = $this->getObject('dbassignmentsubmit_old','assignment');
        //essay object
        $objEssaytopics = 0;
        $objEssaytopics = $this->getObject('dbessay_topics','essay');
        $objEssaybook = 0;
        $objEssaybook = $this->getObject('dbessay_book','essay');
        //testadmin object
        $objTestadmin = 0;
        $objTestadmin = $this->getObject('dbtestadmin','mcqtests');
        $objTestresults = 0;
        $objTestresults = $this->getObject('dbresults','mcqtests');
        //worksheet object
        $objWorksheet = 0;
        $objWorksheet = $this->getObject('dbworksheet','worksheet');
        $objWorksheetresults = 0;
        $objWorksheetresults = $this->getObject('dbworksheetresults','worksheet');

        switch($action) {
            //default action
            case NULL:
                if($this->objUser->isAdmin() || $this->objPerm->isContextMember('Lecturers')) {                    
                    return "main_admin_tpl.php";
                } else {
                    return "main_user_tpl.php";
                }
                break;
            //view the details of the assessment
            case 'assessmentDetails':
                return "assignment_details_tpl.php";
                break;
            //view the grades based on assessment
            case 'viewByAssessment':
                return "view_assessment_tpl.php";
                break;
            //view the details based on assignments
            case 'assignmentDetails':
                return "assessment_details_tpl.php";
                break;
            //upload marks for offline assessments
            case 'uploadMarks':
                return "upload_tpl.php";
                break;
            //save marks for offline assessments
            case 'saveMarks':
            //get the submitted variables
                $assessmentName = 0;
                $assessmentName = $this->getParam("assessmentName", NULL);
                $assessmentType = 0;
                $assessmentType = $this->getParam("assessmentType", NULL);
                $percentFinalMark = 0;
                $percentFinalMark = $this->getParam("percentFinalMark", NULL);
                $numberStudents = 0;
                $numberStudents = $this->getParam("numberStudents", NULL);
                $contextCode = 0;
                $contextCode = $this->getParam("contextCode", NULL);
                $closingDate = 0;
                $closingDate = $this->getParam("closingDate", NULL);
                $description = 0;
                $description = $this->getParam("description", NULL);

                switch($assessmentType) {
                    case 'Essays':
                        //insert into tbl_essay_topics
                        $fields = array();
                        $fields['name'] = $assessmentName;
                        $fields['percentage'] = $percentFinalMark;
                        $fields['userid'] = $this->objUser->userId();
                        $fields['context'] = $contextCode;
                        $fields['closing_date'] = $closingDate;
                        $fields['description'] = $description;
                        $id = $objEssaytopics->addTopic($fields);
                        break;
                    case 'MCQ Tests':
                        //insert into tbl_tests
                        $fields = array();
                        $fields['name'] = $assessmentName;
                        $fields['percentage'] = $percentFinalMark;
                        $fields['userid'] = $this->objUser->userId();
                        $fields['context'] = $contextCode;
                        $fields['closingdate'] = $closingDate;
                        $fields['description'] = $assessmentName;
                        $fields['status'] = "closed";
                        $fields['testtype'] = "offline";
                        $id = $objTestadmin->addTest($fields);
                        break;
                    case 'Online Worksheets':
                        //insert into tbl_worksheet
                        $fields = array();
                        $fields['name'] = $assessmentName;
                        $fields['percentage'] = $percentFinalMark;
                        $fields['userid'] = $this->objUser->userId();
                        $fields['context'] = $contextCode;
                        $fields['closing_date'] = $closingDate;
                        $fields['description'] = $description;
                        $id = $objWorksheet->addWorksheet($fields);
                        break;
                    case 'Assignments':
                    default:                        
                        //insert into tbl_assignment
                        $fields = array();
                        $fields['name'] = $assessmentName;
                        $fields['percentage'] = $percentFinalMark;
                        $fields['userid'] = $this->objUser->userId();
                        $fields['context'] = $contextCode;
                        $fields['closing_date'] = $closingDate;
                        $fields['description'] = $description;
                        $id = $objAssignment->addAssignment($fields);
                        break;
                }
                $totalMarkMCQ = 0;
                for($i=1;$i<=$numberStudents;$i++) {
                    //get the userId
                    $userId = 0;
                    $userId = $this->getParam("userid".$i, NULL);

                    //get the student mark
                    $studentMark = 0;
                    $studentMark = $this->getParam("studentMark".$i, NULL);

                    switch($assessmentType) {
                        case 'Essays':
                            //insert into tbl_essay_book
                            $fields = array();
                            $fields['topicid'] = $id;
                            $fields['studentid'] = $userId;
                            $fields['context'] = $contextCode;
                            $fields['mark'] = $studentMark;
                            $objEssaybook->bookEssay($fields);
                            break;
                        case 'MCQ Tests':
                            //insert into tbl_test_results
                            $fields = array();
                            $fields['testid'] = $id;
                            $fields['studentid'] = $userId;
                            $fields['mark'] = $studentMark;
                            $totalMarkMCQ += $studentMark;
                            $objTestresults->addResult($fields);
                            break;
                        case 'Online Worksheets':
                            //insert into tbl_worksheet_results
                            $fields = array();
                            $fields['worksheet_id'] = $id;
                            $fields['userid'] = $userId;
                            $fields['mark'] = $studentMark;
                            $objWorksheetresults->addResult($fields);
                            break;
                        case 'Assignments':
                        default:
                            //insert into tbl_assignment_submit
                            $fields = array();
                            $fields['assignmentid'] = $id;
                            $fields['userid'] = $userId;
                            $fields['mark'] = $studentMark;
                            $objAssignmentSubmit->addSubmit($fields);
                            break;
                    }
                }
                //Store MCQ total mark
                $objTestadmin->setTotal($id, $totalMarkMCQ);
                return $this->nextAction('viewByAssessment',array('dropdownAssessments'=>$assessmentType));
                break;
        }
    }
}
?>
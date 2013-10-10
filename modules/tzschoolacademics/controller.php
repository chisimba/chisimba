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
// end security check

/**
 * Controller class for academic module
 *
 * @category  Chisimba
 * @package   SMIS Academics
 * @author    Academic module team
 */
class tzschoolacademics extends controller {

    public $lang;
    private $user;
    private $registrar;
    var $dbobject;

    public function init() {

        $this->loadClass('user', 'security');
        $this->lang = $this->getObject('language', 'language');
        $this->user = $this->getObject('user', 'security');
        $this->registrar=$this->getObject('registrar');
        $this->dbobject = $this->getObject('dbsubjectmodule');
    }

    public function dispatch($action) {

        $action = $this->getParam('action', 'main');
        $this->setLayoutTemplate('main_layout_tpl.php');
        switch ($action) {
            case 'register_student':
                return 'student_registration_tpl.php';
                break;
            case 'register_teacher':
                return 'teacher_registration_tpl.php';
                break;
            case 'register_subject':
                return 'subject_registration_tpl.php';
                break;
            case 'register_class':
                return 'class_registration_tpl.php';
                break;
            case 'reg_setup':
                return 'registration_setup_tpl.php';
                break;

            case 'upload_result':
                return 'load_upload_form_tpl.php';
                break;
            case 'add_student':
                $this->registrar->registerStudent();
                return 'student_registration_tpl.php';
                break;
            case 'add_class':
                $this->registrar->registerClass();
                return 'class_registration_tpl.php';
                break;
            case 'add_teacher':
                $this->registrar->registerTeacher();
                return 'teacher_registration_tpl.php';
                break;
            case 'add_subject':
                $this->registrar->registerSubject();
                return 'subject_registration_tpl.php';
                break;
            
            case 'view_subject':
                return 'change_resultform_tpl.php';
                break;

            case 'display':
                   $output = $this->getparam('display');
                   if(!empty($output))
                   {
                   $subject = $this->getParam('subject');
                   $academic_year = $this->getParam('aca_year');
                   $term  = $this->getParam('term');
                   $this->setVar('subject', $subject);
                   $this->setVar('academic_year', $academic_year);
                   $this->setVar('term', $term);
                   $this->setVar('option', 'change_result');
                   return 'change_result_tpl.php';
                  }
               break;

             case 'editresults':
                
                 $regno = $this->getParam('id');
                 $subj = $this->getParam('subject');
                 $acayear = $this->getParam('aca_year');
                 $term = $this->getParam('term');
                 $this->setVar('regno', $regno);
                 $this->setVar('subj', $subj);
                 $this->setVar('term', $term);
                 $this->setVar('acayear', $acayear);
                 $this->setVar('option', 'editresults');
                 return 'edit_results_tpl.php';
                 break;
             
              case   'editbysubject':
                    $regno = $this->getParam('regno');
                    $score = $this->getParam('score');
                    $field_array = array('score' => $score);
                    $this->dbobject->update_marks($regno,$field_array);
                    $this->setVar('option', 'change_result');
                    return 'edit_results_tpl.php';
                 break;
             
            case 'StudentResults':
                $view = $this->getParam('View');
                if (!empty($view)) {
                    $regno = $this->getParam('st_reg');
                    $exam = $this->getParam('exam');
                    $term = $this->getParam('term_id');
                    $year_id = $this->getParam('year');
                    $class = $this->getParam('class');

                    $this->setVar('option', 'view');
                    $this->setVar('regno', $regno);
                    $this->setVar('exam', $exam);
                    $this->setVar('term', $term);
                    $this->setVar('year_id', $year_id);
                    $this->setVar('class', $class);
                    return 'student_results_tpl.php';
                } else {
                    return 'student_results_tpl.php';
                }
                break;


            case 'ClassResult':
                $view = $this->getParam('View');
                if (!empty($view)) {

                    $exam_type = $this->getParam('exam_type');
                    $term_id = $this->getParam('semester');
                    $year_id = $this->getParam('academic_year');
                    $class_id = $this->getParam('class');

                    $this->setVar('exam_type', $exam_type);
                    $this->setVar('term_id', $term_id);
                    $this->setVar('year_id', $year_id);
                    $this->setVar('class_id', $class_id);

                    $this->setVar('option', 'class_result');
                    return 'class_result_tpl.php';
                } else {
                    return 'class_result_tpl.php';
                }
                break;

            //////

            case 'SubjectResults':
                $view = $this->getParam('View');
                if (!empty($view)) {
                    $subject = $this->getParam('subjct');
                    $exam = $this->getParam('exam');
                    $term = $this->getParam('term_id');
                    $year_id = $this->getParam('year');
                    $class = $this->getParam('class');

                    $this->setVar('subject_id', $subject);
                    $this->setVar('exam_id', $exam);
                    $this->setVar('term_id', $term);
                    $this->setVar('year_id', $year_id);
                    $this->setVar('class_id', $class);

                    $this->setVar('option', 'sub_result');
                    return 'subject_result_tpl.php';
                }
                else {
                    return 'subject_result_tpl.php';
                }


            ////deafult page- home tpl
            default:
                return 'academics_home_tpl.php';
                break;
        }
}

 /*
    public function requiresLogin($action) {
        return false;
    }
  */

}

?>

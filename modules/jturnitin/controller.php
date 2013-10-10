<?php
//ini_set('error_reporting', 'E_ALL & ~E_NOTICE');
/**
 * TurnItIn controller class
 *
 * Class to control the IM module
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  chisimba
 * @package   turnitin
 * @author    Wesley Nitsckie <wesleynitsckie@gmail.com>
 * @copyright 2009 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */

class jturnitin extends controller {

    /**
     * The constructor
     *
     */
    public function init() {
        try {
            $this->objUtils = $this->getObject('utilities');
            $this->objTOps = $this->getObject('turnitinops');
            $this->objUser = $this->getObject('user', 'security');
            $this->objDBContext = $this->getObject('dbcontext', 'context');
            $this->objForms = $this->getObject('forms');
            $this->objLanguage = $this->getObject ( 'language', 'language' );
            $this->objTAssDB = $this->getObject('turnitindbass');
            $this->objSubmittedAssignments = $this->getObject('turnitinsubmittedassignments');
            $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');

            // Supressing Prototype and Setting jQuery Version with Template Variables
            $this->setVar('SUPPRESS_PROTOTYPE', true); //Can't stop prototype in the public space as this might impact blocks
            //$this->setVar('SUPPRESS_JQUERY', true);
            //$this->setVar('JQUERY_VERSION', '1.3.2');
            $this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
            $this->eventDispatcher->addObserver ( array ($this->objActivityStreamer, 'postmade' ) );

        }catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }

    /**
     * The standard dispatch funtion
     *
     * @param unknown_type $action
     */

    public function dispatch($action) {
        if(!$this->objDBContext->isInContext()) {
            return "needtojoin_tpl.php";
        }
        // Set the layout template.
        $this->setLayoutTemplate("layout_tpl.php");

        switch ($action) {
            //creates a user profile (if one does not exist) and logs the user in (instructor or student)
            case 'createlecturer':

                print $this->objTOps->createLecturer($this->objUtils->getUserParams());
                break;

            case 'createstudent':
                print $this->objTOps->createStudent($this->objUtils->getUserParams());
                break;

            //create a class with a lecturer assigned to it
            case 'createclass':
                print $this->objTOps->createClass(array_merge(
                        $this->objUtils->getUserParams(),
                        $this->objUtils->getClassParams()));
                break;

            //the student is assigned to a class
            case 'joinclass':
                print $this->objTOps->joinClass(array_merge(
                        $this->objUtils->getUserParams(),
                        $this->objUtils->getClassParams()));
                break;

            //create an assessment: requires lecturer and class details
            case 'createassessment':
                if($this->objDBContext->isInContext()) {

                    print $this->objTOps->createAssessment(array_merge(
                            $this->objUtils->getUserParams(),
                            $this->objUtils->getClassParams(),
                            $this->objUtils->getAssessmentParams()));
                } else {
                    return false;
                }
                break;

            //student submit a paper
            case 'submitassessment':
                print $this->objTOps->submitAssessment(array_merge(
                        $this->objUtils->getUserParams(),
                        $this->objUtils->getClassParams(),
                        $this->objUtils->getAssessmentParams(),
                        $this->objUtils->getSubmissionInfo()));
                break;
            //student submit a paper
            case 'sub':
                $sessionid = $this->objUtils->getUserSession();
                if(!$sessionid==false) {
                    $ar = array_merge(
                            array('sessionid' => $sessionid),
                            $this->objUtils->getUserParams(),
                            $this->objUtils->getClassParams(),
                            $this->objUtils->getAssessmentParams(),
                            $this->objUtils->getSubmissionInfo());
                    error_log($ar);
                    //var_dump($ar);
                    //die;
                    print $this->objTOps->redirectSubmit($ar);
                } else {
                    print "FAILED to get the session id";
                }
                exit(0);
                break;
            case 'ajax_lectureruploadassessment':
                $title=$this->getParam('papertitle');
                $firstname=$this->getParam('firstname');
                $lastname=$this->getParam('lastname');
                $res= $this->objUtils->saveFile("",$title);
                $file=$res['file'];
                $filename=$res['filename'];
                if (is_file($file)) {

                    echo     '{"success":"true", "msg": "'.$file.'|'.$title.'|'.$filename.'|'.$firstname.'|'.$lastname.'"}';

                }else {
                    echo     '{"success":"false", "msg": "'.htmlentities("Error occured. Could not upload paper").'"}';
                }

                exit(0);
                break;
            case 'ajax_returnreport':
            case 'returnreport':
                $objectid=$this->getParam('objectid');
                $mode=$this->getParam("mode");

                $userparams= $this->objUtils->getUserParams();
                if($mode == 'lecturer') {
                    $userid=$this->objSubmittedAssignments->getUserId($objectid);
                    $userparams= $this->objUtils->getUserParamsUsingId($userid);

                }

                print $this->objTOps->getReport(array_merge(
                        $userparams,
                        $this->objUtils->getClassParams(),
                        $this->objUtils->getAssessmentParams(),
                        $this->objUtils->getSubmissionInfo()),$this->objDBContext->getContextCode());
                break;

            case 'viewsubmission':
                break;

            case 'deletesubmission':
                $oid=$this->getParam("oid");
                $paperTitle=$this->getParam("papertitle");

                $userid=$this->objSubmittedAssignments->getUserId($oid);
                $userparams= $this->objUtils->getUserParamsUsingId($userid);
                $userparams= $this->objUtils->getUserParams();
                $deleteparams=array("oid"=>$oid,"assignmenttitle"=>$paperTitle);
                print $this->objTOps->deleteSubmission(
                        array_merge(
                        $userparams,
                        $deleteparams,
                        $this->objUtils->getClassParams()
                ));
                break;

            case 'listsubmissions':

                print $this->objTOps->listSubmissions(array_merge(
                        $this->objUtils->getUserParams(),
                        $this->objUtils->getClassParams(),
                        $this->objUtils->getAssessmentParams()));
                break;

            case 'adminstats':
                $this->objTOps->adminStats($this->objUtils->getUserParams());
                break;

            case 'apilogin':
                $params = array("firstname" => $this->getParam('firstname'),
                        "lastname" => $this->getParam('lastname'),
                        "password" => $this->getParam('password'),
                        "email" => $this->getParam('email'),);

                print $this->objTOps->APILogin($params);
                break;


            case 'callback':
            //log the callbacks
                var_dump($_REQUEST);
                exit(0);
                return 'callback_tpl.php';
                break;
            default:

            case 'main':
                return "initializer_tpl.php";
                break;
            case 'loadusertemplate':
                return $this->objUtils->userTemplate();

              //  $this->appendArrayVar('headerParams', '<script language="JavaScript" type="text/javascript">Ext.MessageBox.hide();</script>');

               // return "main_tpl.php";
                break;

            case 'downloadfile':
                return $this->objUtils->downloadFile($this->getParam('filename'),$this->getParam('userid'));

                break;

            //------- Ajax methods-------//
            case 'ajax_addassignment':
                echo $this->objUtils->doAddAssignment();

                exit(0);
                break;
            case 'ajax_updateassignment':
                echo $this->objUtils->doUpdateAssignment();
                exit(0);
                break;
            case 'json_getassessments':
                echo $this->objUtils->formatJsonAssignments($this->objTAssDB->getAssignments( $this->objDBContext->getContextCode()));
                //echo $this->objForms->jsonGetAssessments();
                exit(0);
                break;
            case 'json_getsubmissions':
                echo $this->objUtils->formatSubmissions($this->objDBContext->getContextCode());
                exit(0);
            case 'json_getstudentassessments':
                echo  $this->objUtils->formmatStudentAssessments($this->objTAssDB->getStudentAssessments(
                $this->objDBContext->getContextCode(),
                $this->objUser->userId()));
                exit(0);
                break;
            case 'getassessmentdetails':
                break;
            case 'deleteassignment':

                $contextcode=$this->getParam('contextcode');
                $uem=$this->getParam('instructoremail');
                $title=$this->getParam('title');

                $assParams=$this->objUtils->getAssessmentParams();
                $this->setLayoutTemplate(NULL);
                $res= $this->objTOps->editAssessment(array_merge(
                        $this->objUtils->getUserParams(),
                        $this->objUtils->getClassParams(),
                        $assParams));

                $assgparams=array('assignmenttitle'=>$res['object']->assign,
                        'assignmentinstruct'=>$res['object']->ainst,
                        'assignmentdatestart'=>$res['object']->dtstart,
                        'assignmentdatedue'=>$res['object']->dtdue
                );


                $results= $this->objTOps->deleteAssessment(array_merge(
                        $this->objUtils->getUserParams(),$this->objUtils->getClassParams(),$assgparams));
                $message=$results['message'];

                /*$myFile = "/web/elearn.wits.ac.za/packages/turnitin/debugx.txt";
                $fh = fopen($myFile, 'w') or die("can't open file");
                $stringData = $message.', '.$title;
                fwrite($fh, $stringData);
                fclose($fh);*/
//die();
                if($message =='Successful!') {
                    $this->objTAssDB->deleteAssignment($title, $contextcode);
                }
                echo     '{"success":"true", "msg": "'.$message.'"}';
                break;

            case 'editassignment':
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('suppressFooter', TRUE);

                $contextcode=$this->getParam('contextcode');
                $uem=$this->getParam('instructoremail');
                $title=$this->getParam('title');
                $assParams=$this->objUtils->getAssessmentParams();
                $this->setLayoutTemplate(NULL);
                $res= $this->objTOps->editAssessment(array_merge(
                        $this->objUtils->getUserParams(),
                        $this->objUtils->getClassParams(),
                        $assParams));
                

                $this->setVarByRef("title", $res['object']->assign);
                $this->setVarByRef("instructions", $res['object']->ainst);
                $this->setVarByRef("datestart", $res['object']->dtstart);
                $this->setVarByRef("dateend", $res['object']->dtdue);
                $this->setVarByRef("id", $this->getParam("id"));

                $this->setVarByRef("generate", $res['object']->generate);
                $this->setVarByRef("sviewreports", $res['object']->sviewreports);
                $this->setVarByRef("repository", $res['object']->repository);
                $this->setVarByRef("searchpapers", $res['object']->searchpapers);
                $this->setVarByRef("searchinternet", $res['object']->searchinternet);
                $this->setVarByRef("searchjournals", $res['object']->searchjournals);
                $this->setVarByRef("searchinstitution", $res['object']->searchinstitution);
                $this->setVarByRef("latesubmissions", $res['object']->latesubmissions);



                return "editassignment_tpl.php";
                break;
            case 'getfullscorereport':
                echo  $this->objUtils->formatScore($this->objTAssDB->getStudentAssessments(
                $this->objDBContext->getContextCode(),
                $this->objUser->userId()));
                exit(0);
                break;
            case 'submit_assessment':
                $papertitle=$this->getParam('papertitle');
                $filename=$this->getParam('filename');
                $assigntitle=$this->getParam('assignmenttitle');
                $filepath=$this->getParam('filepath');
                $userparams=$this->objUtils->getUserParams();
                $classparams=$this->objUtils->getClassParams();
                $submitParams=array('filepath'=>$filepath,'papertitle'=>$papertitle,'assign'=>$assigntitle);

                $finalArray=array_merge($userparams,$classparams,$submitParams);

                $res=$this->objTOps->submitPaper($finalArray);
                $objectid= $res['objectid'];
                $returncode=$res['code'];

                $this->objSubmittedAssignments->addSubmittedAssignment($objectid,$this->objDBContext->getContextCode(),$assigntitle,$filename,$returncode);

                $message=$res['message'];
                echo     '{"success":"true", "msg": "'.$message.'","code":"'.$returncode.'"}';

                break;
            case 'lecturer_submit_assessment':
                $papertitle=$this->getParam('papertitle');
                $filename=$this->getParam('filename');
                $assigntitle=$this->getParam('assignmenttitle');
                $filepath=$this->getParam('filepath');
                $firstname=$this->getParam('firstname');
                $lastname=$this->getParam('lastname');
                $userparams=$this->objUtils->getUserParams();
                $classparams=$this->objUtils->getClassParams();
                $submitParams=array(
                        'filepath'=>$filepath,
                        'papertitle'=>$papertitle,
                        'assign'=>$assigntitle,
                        'pfirstname'=>$firstname,
                        'plastname'=>$lastname);
                $finalArray=array_merge($userparams,$classparams,$submitParams);

                $res=$this->objTOps->submitPaperAsLecturer($finalArray);
                $objectid= $res['objectid'];
                $returncode=$res['code'];

                $this->objSubmittedAssignments->addSubmittedAssignment($objectid,$this->objDBContext->getContextCode(),$assigntitle,$filename,$returncode);

                $message=$res['message'];
                echo     '{"success":"true", "msg": "'.$message.'","code":"'.$returncode.'"}';

                break;
            case 'ajax_uploadassessment':
                $title=$this->getParam('papertitle');
                $res= $this->objUtils->saveFile("",$title);
                $file=$res['file'];
                $filename=$res['filename'];
                if (is_file($file)) {

                    echo     '{"success":"true", "msg": "'.$file.'|'.$title.'|'.$filename.'"}';

                }else {
                    echo     '{"success":"false", "msg": "'.htmlentities("Error occured. Could not upload paper").'"}';
                }

                $this->eventDispatcher->post($this->objActivityStreamer, "jturnitin", array('title'=>'Assignment Submitted',
                        'link'=> $this->uri(array()),
                        'contextcode' => $this->objDBContext->getContextCode(),
                        'author' => $this->objUser->fullname(),
                        'description'=> $this->objUser->fullname()." submitted an assignment to Turnitin"));
                //echo '{"success":"true"}';
                exit(0);
                break;
        }
    }


    /**
     * Standard engine function
     * being overridden
     *
     * @return boolean
     */
    public function requiresLogin() {
        if($this->getParam('action') == 'callback') {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}


?>

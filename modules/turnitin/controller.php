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

class turnitin extends controller 
{
	
	/**
	 * The constructor
	 *
	 */
	public function init()
	{
		 try{		
			$this->objUtils = $this->getObject('utilities');
			$this->objTOps = $this->getObject('turnitinops');
			$this->objUser = $this->getObject('user', 'security');
			$this->objDBContext = $this->getObject('dbcontext', 'context');
			$this->objForms = $this->getObject('forms');
			$this->objTAssDB = $this->getObject('turnitindbass');
			$this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
			
			// Supressing Prototype and Setting jQuery Version with Template Variables
			$this->setVar('SUPPRESS_PROTOTYPE', true); //Can't stop prototype in the public space as this might impact blocks
			//$this->setVar('SUPPRESS_JQUERY', true);
			//$this->setVar('JQUERY_VERSION', '1.3.2');
			$this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
		 	$this->eventDispatcher->addObserver ( array ($this->objActivityStreamer, 'postmade' ) );
		 	
		 	//log the user into TII
		 	//$this->objUtils->getUserSession();		
		 	
		}catch(Exception $e){
            throw customException($e->getMessage());
            exit();
        }
	}
	
	/**
	 * The standard dispatch funtion
	 *
	 * @param unknown_type $action
	 */
	
	public function dispatch($action)
	{
		// Set the layout template.
        $this->setLayoutTemplate("layout_tpl.php");
               
		switch ($action)
		{
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
				if($this->objDBContext->isInContext())
				{
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
			     if(!$sessionid==false){
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
				
			case 'ajax_returnreport':
			case 'returnreport':
				print $this->objTOps->getReport(array_merge(
														$this->objUtils->getUserParams(), 
														$this->objUtils->getClassParams(),  
														$this->objUtils->getAssessmentParams(),
														$this->objUtils->getSubmissionInfo()));
				break;
				
			case 'viewsubmission':
				break;
				
			case 'deletesubmission':
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
				return $this->objUtils->userTemplate();			
				return "main_tpl.php";
				break;
				
				
				
			//------- Ajax methods-------//
			case 'ajax_addassignment':
				//echo "{'success' : 'true', 'msg' : 'ja it works -> ".$this->getParam('title').$this->getParam('startdt').$this->getParam('duedt').$this->getParam('instructions')."'}"; 
				//echo "{'success' : false, 'errors' : ['clientCode': 'Client not found', 'portOfLoading' : 'This field must not be null']}";   
				
				echo $this->objUtils->doAddAssignment();
				exit(0);
				break;
				
			case 'json_getassessments':
				echo $this->objUtils->formatJsonAssignments($this->objTAssDB->getAssignments( $this->objDBContext->getContextCode()));
				//echo $this->objForms->jsonGetAssessments();
				exit(0);
				break;
			case 'json_getsubmissions':
				echo $this->objUtils->formatSubmissions();
				exit(0);
			case 'json_getstudentassessments':
				echo  $this->objUtils->formmatStudentAssessments($this->objTAssDB->getStudentAssessments(
									$this->objDBContext->getContextCode(), 
									$this->objUser->userId()));
				exit(0);
				break;
				
			case 'ajax_sumbitassessment':
				echo $this->objUtils->doFileUpload();
				 $this->eventDispatcher->post($this->objActivityStreamer, "turnitin", array('title'=>'Assignment Submitted',
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
	public function requiresLogin()
	{
		if($this->getParam('action') == 'callback')
		{
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
}
	

?>
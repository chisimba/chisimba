<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Workgroup Admin module
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
* $Id: controller.php 22254 2011-08-18 12:45:44Z joconnor $
*/
class workgroupadmin extends controller
{
    var $objUser;
	var $objLanguage;
	var $objDbWorkgroup;
	var $objDbWorkgroupUsers;

    /**
    * The Init function
    */
    function init()
    {
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language','language');
		$this->objDbWorkgroup =& $this->getObject('dbworkgroup','workgroup');
		$this->objDbWorkgroupUsers =& $this->getObject('dbworkgroupusers','workgroup');
        //$this->objHelp=& $this->getObject('helplink','help');
        //$this->objHelp->rootModule="helloworld";
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Set it to log once per session
        //$this->objLog->logOncePerSession = TRUE;
        //Log this module call
        $this->objLog->log();
    }

    /**
    * The dispatch funtion
    * @param string $action The action
    * @return string The content template file
    */
    function dispatch($action=Null)
    {
        //$this->objConfig = &$this->getObject('altconfig','config');
        //$systemType = $this->objConfig->getValue("SYSTEM_TYPE", "contextabstract");
        //$isAlumni = true; //($systemType == "alumni");
        //$this->setVar('isAlumni',$isAlumni);
        // Set the layout template.
        $this->setLayoutTemplate("layout_tpl.php");
        // 1. ignore action at moment as we only do one thing - say hello
        // 2. load the data object (calls the magical getObject which finds the
        //    appropriate file, includes it, and either instantiates the object,
        //    or returns the existing instance if there is one. In this case we
        //    are not actually getting a data object, just a helper to the
        //    controller.
        // 3. Pass variables to the template
        $this->setVarByRef('objUser', $this->objUser);
		$this->setVarByRef('objLanguage', $this->objLanguage);
        //$this->setVarByRef('objHelp', $this->objHelp);
        // return the name of the template to use  because it is a page content template
        // the file must live in the templates/content subdir of the module directory
		// Get context code.
		$objDbContext = $this->getObject('dbcontext','context');
		$contextCode = $objDbContext->getContextCode();
		$this->setVarByRef('contextCode', $contextCode);
        // Check if we are not in a context...
		if ($contextCode == null) {
            //if ($isAlumni) {
    		//	$contextTitle = "Lobby";
    		//	$this->setVarByRef('contextTitle', $contextTitle);
            //}
            //else {
                return "error_tpl.php";
            //}
		}
		else {
            // ... else
			$contextTitle = 'context0'; //$objDbContext->getTitle();
			$this->setVarByRef('contextTitle', $contextTitle);
		}

		switch($action){
            case 'create':
                return "create_tpl.php";
			case "createconfirm":
                // Create a new workgroup.
				$id = $this->objDbWorkgroup->insertSingle(
					$contextCode,
					$this->getParam('newworkgroup')
				);
                //$id = $this->objDbWorkgroup->getLastInsertId();
                return $this->nextAction('manage', array('workgroupId'=>$id));
				break;
            case 'rename':
				$id = $this->getParam('workgroupId',NULL);
				$this->setVarByRef("id", $id);
				$list = $this->objDbWorkgroup->listSingle($id);
				$workgroup = $list[0]['description'];
				$this->setVarByRef("workgroup", $workgroup);
                return "rename_tpl.php";
			case "renameconfirm":
				$id = $this->getParam('workgroupId',NULL);
				$this->objDbWorkgroup->updateSingle(
					$id,
					$this->getParam('newworkgroup')
				);
				return $this->nextAction('confirm', null);
			case "delete":
                // Delete a workgroup.
				$id = $this->getParam('workgroupId',NULL);
				$this->objDbWorkgroup->deleteSingle($id);
				$this->objDbWorkgroupUsers->deleteAll($id);
				break;
			case "manage":
    			//ob_start();
                // Edit the workgroup.
				$id = $this->getParam('workgroupId',NULL);
				$workgroups = $this->objDbWorkgroup->listSingle($id);
				$workgroup = $workgroups[0];
				$this->setVarByRef("workgroup", $workgroup);
				$members = $this->objDbWorkgroupUsers->listAll($workgroup['id']);
			    //echo "PRE:\n";
			    //var_dump($members);
                foreach ($members as $key=>$member){
                    $members[$key]['display']=$member['fullname'].' ('.$member['username'].')';
                }
			    //echo "POST:\n";
			    //var_dump($members);
				$this->setVarByRef("members", $members);
                //echo "CONTROLLER::_templateRefs:\n";
                //var_dump($this->objEngine->_templateRefs['members']);
                //if ($isAlumni) {
                //    $objUsers = $this->getObject('dbusers','workgroup');
                //    $users = $objUsers->listAll();
        		//	  $this->setVarByRef("users", $users);
                //} else {
                    // Get the members of all workgroups in the context.
    				//$membersOfAll = $this->objDbWorkgroupUsers->getAllInContext($contextCode);
//				$members = $this->objDbWorkgroupUsers->listAll($workgroup['id']);

                // Get the groupAdminModel object.
				$groups = $this->getObject("groupAdminModel", "groupadmin");

                // Get list of lecturers
				$gid = $groups->getLeafId(array($contextCode,'Lecturers'));
				$lecturers = $groups->getGroupUsers($gid, array('userid',"CONCAT(surname, ', ', firstname) AS fullname"), "ORDER BY fullname ASC"); //, CONCAT(surname, firstname) AS key
//                $this->setVarByRef('lecturers', $lecturers);
                // Get list of students
				$gid=$groups->getLeafId(array($contextCode,'Students'));
				$students = $groups->getGroupUsers($gid, array('userid',"CONCAT(surname, ', ', firstname) AS fullname"), "ORDER BY fullname ASC"); //, CONCAT(surname, firstname) AS key
//    			$this->setVarByRef('students',$students);
				$_users = array_merge($lecturers, $students);

                $users = array();
                foreach ($_users as $_user) {
                    $inWorkgroup = false;
                    foreach ($members as $member) {
                        if ($_user['userid'] == $member['userid']) {
                            $inWorkgroup = true;
                            break;
                        }
                    }
                    if (!$inWorkgroup) {
                        if (!isset($_user['fullname'])) {
//                            $_user['key'] = $_user['surname'].$_user['firstname'];
                            $_user['fullname'] = $_user['surname'].', '.$_user['firstname'];

                        }
//                        $users[$_user['surname'].$_user['firstname']] = $_user;
                        $users[$_user['fullname'].$_user['username']] = $_user;
                    }
                }
			    //echo "\n\nUSERS:\n";
			    //var_dump($users);
                ksort($users);
			    //var_dump($users);
                foreach ($users as $key=>$user){
                    $users[$key]['display']=$user['fullname'].' ('.$user['username'].')';
                }
			    //var_dump($users);
                $this->setVarByRef("users", $users);
                //}
				$this->setVar('pageSuppressXML',true);
			    //$dump = ob_get_contents();
			    //ob_end_clean();
			    //$this->setVar('dump', $dump);
				return "managenew_tpl.php";
            case "processform":
                if( $this->getParam( 'button' ) == 'save' ) {
                    //.. do the save action ..
    				$workgroupId = $this->getParam('workgroupId',NULL);
                    $rightData = $this->getParam( 'rightList' );
                    // First delete all users in the workgroup.
        		    $this->objDbWorkgroupUsers->deleteAll($workgroupId);
                    if (!empty($rightData)) {
                        foreach ($rightData as $userId) {
                		    $this->objDbWorkgroupUsers->insertSingle($workgroupId,$userId);
                        }
                    }
					$this->setVar('confirm',$this->objLanguage->languageText('mod_workgroupadmin_changessaved','workgroupadmin'));
                    return $this->nextaction('confirm',NULL);
                } else if ( $this->getParam( 'button' ) == 'cancel' ) {
                    //.. do the cancel action ..
                    return $this->nextaction(NULL,NULL);
                }
            // Add selected users to workgroup.
			case 'confirm':
				$this->setVar('confirm',$this->objLanguage->languageText('mod_workgroupadmin_changessaved','workgroupadmin'));
				break;
			default:
				;
		} // switch
        // List all the workgroups.
		$workgroups = $this->objDbWorkgroup->listAll($contextCode);
		$this->setVarByRef('workgroups', $workgroups);
        return "main_tpl.php";
    }
}
?>

<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Course Lecturer module
* @author Jeremy O'Connor as used in the Buddies Module, edited by John Abakpa
*/
class courselecturers extends controller
{
    public $objUser;
    public $objIcq;
    public $objYahoo;

    public function init()
    {
        $this->objUser =& $this->getObject('user', 'security');
        $this->objDbUserparams = & $this->getObject('dbuserparamsadmin', "userparamsadmin");
        $this->objIcq = & $this->getObject("icq", 'communications');
        $this->objYahoo = & $this->getObject("yahoo", 'communications');
    }
    
    public function dispatch($action=Null)
    {
        // Set the layout template.
        //$this->setLayoutTemplate("layout_tpl.php");
        // 1. ignore action at moment as we only do one thing - say hello
        // 2. load the data object (calls the magical getObject which finds the
        //    appropriate file, includes it, and either instantiates the object,
        //    or returns the existing instance if there is one. In this case we
        //    are not actually getting a data object, just a helper to the 
        //    controller.
        // 3. Pass variables to the template
        $this->setVarByRef('objUser', $this->objUser);
	     $this->setVar('pageSuppressXML',true);        
        
        
        switch($action){
			// Get list of users
			case "listusers":
				$how = $this->getParam('how');
				$searchField = $this->getParam('searchField');
				$this->setVar('searchField',$searchField);
				$objDbUsers =& $this->getObject('dbusers','buddies'); 
				if ($searchField == "listall") {
					$allUsers = $objDbUsers->listAll();
				}
				else {
					$allUsers = $objDbUsers->listSelected($how, $searchField);
				}
		        $this->setVarByRef('allUsers', $allUsers);
                $isBuddy = array();
                foreach ($allUsers as $user) {
            		$objDbBuddies =& $this->getObject('dbbuddies','buddies');
            		$count = $objDbBuddies->countSingle($this->objUser->userId(), $user['userid']);
                    $isBuddy[] = $count[0]['count'] > 0;
                }
		        $this->setVarByRef('isBuddy', $isBuddy);
				return "users_tpl.php";
			// Add a user as a Lecturer
			case "addbudy":
				$buddyId = $this->getParam('buddyId', null);
				$objDbBuddies =& $this->getObject('dbbuddies','buddies'); 
				$objDbBuddies->insertSingle($this->objUser->userId(),$buddyId);
				$how = $this->getParam('how');
				$searchField = $this->getParam('searchField');
				$this->setVar('searchField',$searchField);
				$objDbUsers =& $this->getObject('dbusers','buddies'); 
				if ($searchField == "listall") {
					$allUsers = $objDbUsers->listAll();
				}
				else {
					$allUsers = $objDbUsers->listSelected($how, $searchField);
				}
		        $this->setVarByRef('allUsers', $allUsers);
                $isBuddy = array();
                foreach ($allUsers as $user) {
            		$objDbBuddies =& $this->getObject('dbbuddies','buddies');
            		$count = $objDbBuddies->countSingle($this->objUser->userId(), $user['userid']);
                    $isBuddy[] = $count[0]['count'] > 0;
                }
		        $this->setVarByRef('isBuddy', $isBuddy);
				return "users_tpl.php";
			case "removebudy":
				$buddyId = $this->getParam('buddyId', null);
				$objDbBuddies =& $this->getObject('dbbuddies','buddies'); 
				$objDbBuddies->deleteSingle($this->objUser->userId(),$buddyId);
				$buddies = $objDbBuddies->listAll($this->objUser->userId());
		        $this->setVarByRef('buddies', $buddies);
				break;
        	default:
        } // switch
		// Get Lecturer
		$objDbBuddies =& $this->getObject('dbbuddies','buddies'); 
		$buddies = $objDbBuddies->listAll($this->objUser->userId());
        $this->setVarByRef('buddies', $buddies);
        // Create array to denote which Lecturer are online/offline
		$objDbLoggedInUsers =& $this->getObject('dbloggedinusers','buddies'); 
		$buddiesOnline = array();
        $Icq = array();
        $Yahoo = array();
        $this->setVarByRef('buddiesOnline', $buddiesOnline);
        $this->setVarByRef('Icq', $Icq);
        $this->setVarByRef('Yahoo', $Yahoo);
		foreach ($buddies as $buddy) {
			$list = $objDbLoggedInUsers->listSingle($buddy['buddyid']);
			if (empty($list)) {
			    $buddiesOnline[] = false;
			}
			else {
			    $buddiesOnline[] = true;
			}
           $Icq[]=$this->objIcq->getStatusIcon($buddy['buddyid'], 'byuserid');
           $Yahoo[]=$this->objYahoo->getStatusIcon($buddy['buddyid'], 'byuserid');
		}
		return "main_tpl.php";
    }
}    
?>

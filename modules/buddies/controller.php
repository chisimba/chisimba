<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Buddies module
* @author Jeremy O'Connor
*/
class buddies extends controller
{
    public $objUser;
    public $objIcq;
    public $objYahoo;

    public function init()
    {
        $this->objUser =& $this->getObject('user', 'security');
        //$this->objHelp=& $this->getObject('helplink','help');
        //$this->objHelp->rootModule="helloworld";
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
        
        //$this->setVarByRef('objHelp', $this->objHelp);
        // return the name of the template to use  because it is a page content template
        // the file must live in the templates/content subdir of the module directory
        switch($action){
			// Get list of users
            /*
			case "users":
				$objDbUsers =& $this->getObject('dbusers'); 
				$allUsers = $objDbUsers->listAll();
		        $this->setVarByRef('allUsers', $allUsers);
                $isBuddy = array();
                foreach ($allUsers as $user) {
            		$objDbBuddies =& $this->getObject('dbbuddies');
            		$count = $objDbBuddies->countSingle($this->objUser->userId(), $user['userId']);
                    $isBuddy[] = $count[0]['count'] > 0;
                }
		        $this->setVarByRef('isBuddy', $isBuddy);
				return "users_tpl.php";
            */
			// Get list of users
			case "listusers":
				$how = $this->getParam('how');
				$searchField = $this->getParam('searchField');
				$this->setVar('searchField',$searchField);
				$objDbUsers =& $this->getObject('dbusers'); 
				if ($searchField == "listall") {
					$allUsers = $objDbUsers->listAll();
				}
				else {
					$allUsers = $objDbUsers->listSelected($how, $searchField);
				}
		        $this->setVarByRef('allUsers', $allUsers);
                $isBuddy = array();
                foreach ($allUsers as $user) {
            		$objDbBuddies =& $this->getObject('dbbuddies');
            		$count = $objDbBuddies->countSingle($this->objUser->userId(), $user['userid']);
                    $isBuddy[] = $count[0]['count'] > 0;
                }
		        $this->setVarByRef('isBuddy', $isBuddy);
				return "users_tpl.php";
			// Add a user as a buddy
			case "addbudy":
				$buddyId = $this->getParam('buddyId', null);
				$objDbBuddies =& $this->getObject('dbbuddies'); 
				$objDbBuddies->insertSingle($this->objUser->userId(),$buddyId);
				//$objDbUsers =& $this->getObject('dbusers');
				//$allUsers = $objDbUsers->listAll();
		        //$this->setVarByRef('allUsers', $allUsers);
				//return "users_tpl.php";
				//$objDbBuddies =& $this->getObject('dbbuddies');
				//$buddies = $objDbBuddies->listAll($this->objUser->userId());
		        //$this->setVarByRef('buddies', $buddies);
				$how = $this->getParam('how');
				$searchField = $this->getParam('searchField');
				$this->setVar('searchField',$searchField);
				$objDbUsers =& $this->getObject('dbusers'); 
				if ($searchField == "listall") {
					$allUsers = $objDbUsers->listAll();
				}
				else {
					$allUsers = $objDbUsers->listSelected($how, $searchField);
				}
		        $this->setVarByRef('allUsers', $allUsers);
                $isBuddy = array();
                foreach ($allUsers as $user) {
            		$objDbBuddies =& $this->getObject('dbbuddies');
            		$count = $objDbBuddies->countSingle($this->objUser->userId(), $user['userid']);
                    $isBuddy[] = $count[0]['count'] > 0;
                }
		        $this->setVarByRef('isBuddy', $isBuddy);
				return "users_tpl.php";
			case "removebudy":
				$buddyId = $this->getParam('buddyId', null);
				$objDbBuddies =& $this->getObject('dbbuddies'); 
				$objDbBuddies->deleteSingle($this->objUser->userId(),$buddyId);
				$buddies = $objDbBuddies->listAll($this->objUser->userId());
		        $this->setVarByRef('buddies', $buddies);
				break;
        	default:
        } // switch
		// Get Buddies
		$objDbBuddies =& $this->getObject('dbbuddies'); 
		$buddies = $objDbBuddies->listAll($this->objUser->userId());
        $this->setVarByRef('buddies', $buddies);
        // Create array to denote which buddies are online/offline
		$objDbLoggedInUsers =& $this->getObject('dbloggedinusers'); 
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
        //return "main_tpl.php";
    }
}    
?>
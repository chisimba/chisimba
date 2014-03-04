<?php 
//-----sports class extends controller---------

//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Controller class for the module sports
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary, Kaddu Ismael
*
* 
*/

class sports extends controller{
//language object for multilinguility
var $objLanguage;
//instance of htmlheading
var $objHeading; 
//form object
var $objForm;
//database object
var $objDBSports;

//class constructor
public function init()
{
// Create an instance of the user object
	$this->objUser = &$this->getObject('user', 'security');
// creating an instance of the language object
	$this->objLanguage = & $this->getObject('language','language');
// creating an instance of the htmlheading object
	$objHeading = & $this->getObject('htmlheading','htmlelements');
// Get the DB object.
	$this->objDBSports =& $this->getObject('dbsports','sportsadmin');
	
	$this->objDbtournament =& $this->getObject('dbtournament','sportsadmin');
	
	$this->objForm = & $this->getObject('form','htmlelements');
	$this->loadClass('tabbedbox', 'htmlelements');
    $this->loadClass('form', 'htmlelements');
    $this->loadClass('button', 'htmlelements'); 
}


//function to dispatch control to the different classes

public function dispatch($action)
{
	$action = $this->getParam("action", NULL);
	$this->setLayoutTemplate('sports_layout_tpl.php');

     switch($action){

        case NULL: 
			
            return "main_tpl.php";
		break;
					
		//action to take to the sports activity detail
		
		case 'sportdetails':
		 $sportid = $this->getParam('sportid',NULL);			
		//for adding breadcrumbs fro better navigation
		 $objTools = & $this->newObject('tools', 'toolbar'); 
		 $this->sportid = $sportid;
		 $crumbs = $this->getBreadCrumbs($sportid, TRUE);
		 $objTools->addToBreadCrumbs($crumbs);
		 
		 //for adding breadcrumbs
		 $objTools = & $this->newObject('tools','toolbar');			     
		 $crumbs = $this->getBreadCrumbs($sportid,TRUE,'tournament');
		 $objTools->addToBreadCrumbs($crumbs);
		 
		return "sportsdetail_tpl.php";				
				
		break;
		
		//giving the details of the tournament
		case "tournamentdetails":			
		 $sportid = $this->getParam('sportid',NULL);
		 $this->sportid = $sportid ;
		 $tournamentid = $this->getParam('tournamentid',NULL); 
		  
		 //for adding breadcrumbs
		 $objTools = & $this->newObject('tools','toolbar');			     
		 $crumbs = $this->getBreadCrumbs($tournamentid,TRUE,'tournament',$tournamentid);
		 $objTools->addToBreadCrumbs($crumbs);				 
		 return "tournamentdetail_tpl.php";
		  
		break;
		
		//case to show the details of the team in a fixture under a particular tournament
		case "showteamfixturedetails":		  
			 $teamid = $this->getParam('teamid',NULL);
			 $fixtureid = $this->getParam('fixtureid',NULL);
			 $tournamentid = $this->getParam('tournamentid',NULL);
			 $sportid = $this->getParam('sportid',NULL);
					 
		 return "showteamfixturedetails_tpl.php";	
		 break;
		 
		 case "teamdetails":
		  $teamid = $this->getParam('teamid',NULL);
		  $this->setVar('teamid',$teamid);
		  
		   //for adding breadcrumbs
			 $objTools = & $this->newObject('tools','toolbar');			     
			 $crumbs = $this->getBreadCrumbs($tournamentid,TRUE,'tournament',$tournamentid);
			 $objTools->addToBreadCrumbs($crumbs);	
		 
		  return "teamdetail_tpl.php";
		 break;
		
		//case for viewing player album
		case "viewplayeralbum":
			$sportid = $this->getParam('sportid',NULL);
			 //for adding breadcrumbs
			 $objTools = & $this->newObject('tools','toolbar');			     
			 $crumbs = $this->getBreadCrumbs($tournamentid,TRUE,'tournament',$tournamentid);
			 $objTools->addToBreadCrumbs($crumbs);	
			 
		return "playeralbum_tpl.php";
		
		break;
		
		//case for showing the details of the player
		case "playerdetails":
		  $playerid = $this->getParam('playerid',NULL);		
		  $this->setVar('playerid', $playerid);
		   //for adding breadcrumbs
			 $objTools = & $this->newObject('tools','toolbar');			     
			 $crumbs = $this->getBreadCrumbs($tournamentid,TRUE,'tournament',$tournamentid);
			 $objTools->addToBreadCrumbs($crumbs);	
		  
		  return "playerdetails_tpl.php";
		  
		break;
		
	}// End of Switch

}// End of function dispatch()

/**
     * Method to get a list of bread crumbs to navigate through the module
     * @param string $sportid The Node Id
	 * @param - $item - the item being considered 
     * @return array
     */
    public function getBreadCrumbs($id = NULL,$showContentLink= FALSE,$item=NULL,$itemid=NULL)
     {	 	
		  $objLink = & $this->getObject('link', 'htmlelements');
		  $arrNodeLinks = array(); 
	  
	  switch ($item){
	    case NULL:
		 	    
		//get the name of the sport
          $sportname = $this->objDBSports->getSportsById($id);
		  if ($sportname!=NULL) {
		   
               $Links[] = $sportname;
			   
             } 
		  else {
               $Links[] = $this->objLanguage->languageText('mod_sportsadmin_notfound');
			   
             }
      
	       $objLink = & $this->getObject('link', 'htmlelements');

            if ($showContentLink) {
                // Link to Content
                $objLink = new link ($this->uri(array('action' => 'sportdetails','sportid'=>$sportid)));
                $objLink->link = $sportname;                    
                $arrNodeLinks[] = $objLink->show();
            }
		
	  break; 
	  
	  case "tournament":	  
	    //get the name of the sport         
	     $sportLink = new link ($this->uri(array('action' => 'sportdetails','sportid'=>$sportid)));
         $sportLink->link = $this->objDBSports->getSportsById($this->sportid);	 
									
		  $tourn = $this->objLanguage->languageText('mod_sportsadmin_tournament');		
          // Link to Content
          $objLink = new link ($this->uri(array('item'=>'tournament','action'=>'sportdetails','sportid'=>$this->sportid)));	
			   
		  $objLink->link = $tourn;			
            			 
		if ($itemid) {		  
  //get the name of the tournament
     $tournament_name = new link ($this->uri(array('item'=>'tournament','action'=>'tournamentdetails','sportid'=>$this->sportid,'tournamentid'=>$itemid)));				
     $tournament_name->link = $this->objDbtournament->getTournamentNameById($itemid);       			   
             }      
	      
          $objLink = & $this->getObject('link', 'htmlelements');
		  
		  
            if ($showContentLink) {			
			  if ($itemid) {	 
                $arrNodeLinks[] =  $tournament_name->show(); 
				
				}
                $arrNodeLinks[] = $objLink->show();
				$arrNodeLinks[] =  $sportLink->show(); 
            }
	  break;
	  
	  } 	             	
       
        return array_reverse($arrNodeLinks);
		
     }//closing the function

}//closing the class

?>
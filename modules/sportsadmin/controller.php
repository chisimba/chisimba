<?php 
//-----sportsadmin class extends controller------

//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Controller class for the module sports
* @copyright AVOIR
* @author Nsabagwa Mary
* @author Kaddu Ismael
* @package sports
* @licence GPL
*/

class sportsadmin extends controller{
/* 
        * @var string $action A string to hold the value of the action parameter
        *  retrieved from the querystring
        * @access public
        * 
        */
	public $action;
	
	/*
	   * Language object
	   *
	   * @var object
	   */
	public $objLanguage;
	 
	/**
	     * Heading object
	     *
	     * @var object
	     */
	public $objHeading; 
	
	/**
	     * Form object
	     *
	     * @var object
	     */
	public $objForm;
	
	/**
	     * Database abstraction object
	     *
	     * @var object
	     */
	public $objDBSports;

	//sportid 
	private $sportid;

 /*
    * 
    * The standard init method to initialise the sportsadmin object and assign some of
    * the objects used in all action derived methods.
    *
    * @access public
    * 
    */
public function init()
{
   try {
			
		// Create an instance of the user object
		$this->objUser = &$this->getObject('user', 'security');
		// creating an instance of the language object
		$this->objLanguage = & $this->getObject('language','language');
		// creating an instance of the htmlheading object
		$objHeading = & $this->getObject('htmlheading','htmlelements'); 
	       $this->loadClass('link','htmlelements'); 
		$this->objDBSports =& $this->getObject('dbsports');
		$this->objDbtournament =& $this->getObject('dbtournament');
		$this->objDbsportsnews = & $this->getObject('sportsnews');
		$this->objDbfixtures =& $this->getObject('dbfixtures');
		$this->objDbteam = & $this->getObject('dbteam');
		$this->objDbscores = & $this->getObject('dbscores');
		$this->objDbplayer = & $this->getObject('dbplayer');
		$this->objDbplayerdata=& $this->getObject('dbplayerdata');
		$this->objDbmatchplayers = $this->getObject('dbmatchplayer');
		$this->objConfig =& $this->getObject('altconfig','config');
		$this->playerfolder = $this->objConfig->getcontentBasePath().'players';
		$this->logopath = $this->objConfig->getcontentBasePath().'teamlogos';

              //Get the activity logger class
              $this->objLog=$this->newObject('logactivity', 'logger');
              //Log this module call
              $this->objLog->log();
	   	
	}
    catch(customException $e) {
        //oops, something not there - bail out
        echo customException::cleanUp();
        //we don't want to even attempt anything else right now.
        die();
    }
}


/**
   * 
    * A standard method to handle actions from the querystring.
    * The dispatch function converts action values to function
    * names, and then calls those functions to perform the action
    * that was specified.
    *
    * @access public
    * @return string The results of the method denoted by the action
    *   querystring parameter. Usually this will be a template populated
    *   with content.
    * 
 */

public function dispatch($action)
{  
		 
	$action = $this->getParam("action", NULL);
	$this->setLayoutTemplate('sportsadmin_layout_tpl.php');

     switch($action){

        case NULL: 
			
            return "main_tpl.php";

		break;
		
		case 'add':
		  $id = $this->getParam('id', NULL);
			
		   	return "sportsadd_tpl.php";
		break;
		
		case 'addnews':
		  $teamid = $this->getParam('teamid',NULL);
		  $sportid = $this->getParam('sportid',NULL);
		  
		  return "addsportsnews_tpl.php";		  
		
		break;
		
		case "savenews":
			 $teamid = $this->getParam('teamid',NULL);
			 $sportid = $this->getParam('sportid',NULL);			
			 $news = $this->getParam('news',NULL);
			 $this->objDbsportsnews->saveNews($teamid,$sportid,$news);
			  
			 return "teamdetail_tpl.php";		
		break;	
		
		
		case "deleteplayerinfo":
			$infoid = $this->getParam('infoid',NULL);
			$playerid = $this->getParam('playerid',NULL);
			$sportid = $this->getParam('sportid',NULL);
			
			$this->objDbplayerdata->deleteplayerdata($infoid);
			
			return "playerdetails_tpl.php";
		
		break;
		
		case "deleteplayer":		
		
		$playerid = $this->getParam('playerid',NULL);
		$item = $this->getParam('item',NULL);
		$sportid = $this->getParam('sportid',NULL);
		
		$this->objDbplayer->deleteplayer($playerid);
		return "sportsdetail_tpl.php";
		break;
		
		
		//case for viewing player album
		case "viewplayeralbum":
			$sportid = $this->getParam('sportid',NULL);
			
			return "playeralbum_tpl.php";
		break;
		
		case "saveplayerdata":
			$sportid = $this->getParam('sportid',NULL);
			$playerid = $this->getParam('playerid',NULL);
			$event = $this->getParam('event',NULL);
			
			$this->objDbplayerdata->addplayerinfo($playerid,$event);
			
			return "playerdetails_tpl.php";
		break;
		
		case "addplayerdata":
			$playerid = $this->getParam('playerid',NULL);
			$sportid = $this->getParam('sportid',NULL);
			
			 return "playerdataadd_tpl.php";
		break;
		
		case 'save':
			
			$name = $this->getParam('name', NULL);
			$description = $this->getParam('description', NULL);
			$evaluation = $this->getParam('evaluation',NULL);
			//Insert into the database
			$this->objDBSports->insertSport($name,$player_no,$description,$evaluation);
			return "main_tpl.php";
		break;
		
		case 'modify':
			$id = $this->getParam('id', NULL);
			$name = $this->getParam('name', NULL);
			$description = $this->getParam('description', NULL);
			$evaluation = $this->getParam('evaluation',NULL);
		 //proceed to insert
			$this->objDBSports->updateSport($id,$name,$player_no,$description,$evaluation);
			return "main_tpl.php";
		break;
		
		case "modifyplayerinfo":
			$event = $this->getParam('event',NULL);
			$infoid = $this->getParam('infoid',NULL);
			$sportid = $this->getParam('sportid',NULL);
			$playerid = $this->getParam('playerid',NULL);
			
			$this->objDbplayerdata->modifyplayerinfo($infoid,$event);
			
			return "playerdetails_tpl.php";
		break;
		
		case "modifyteam":		
		
		$tournamentdetails = $this->getParam('tournamentdetails',NULL);
		$item = $this->getParam('fixtures',NULL);
		$sportid = $this->getParam('sportid',NULL);
		$homeground = $this->getParam('ground',NULL);
		$name = $this->getParam('teamfield',NULL);			
		$motto = $this->getParam('motto');
		$teamid = $this->getParam('teamid',NULL);
		
		$filename = $_FILES['logo_file']['name'];
		$tmp = $_FILES['logo_file']['tmp_name'];
		$size = $_FILES['logo_file']['size'];
		
		 //get the extension so that only permitted image types can be allowed
		 $path_info = pathinfo($filename);
		$extn = $path_info['extension'];
		 
		 $path= $this->logopath.'/'.$filename;
		if(is_uploaded_file($tmp)){
		
		  switch($extn){
		 
		  		case "gif":
				case "png":
				case "tiff":
				case "psd":
				case "swf":
				case "bmp":
				case "jpg":				
				$res= move_uploaded_file($tmp,$path);
			    break;
				
				}//closing the switch		
			if($res){
			$this->objDbteam->modifyteam($teamid,$name,$homeground,$motto);
			return "sportsdetail_tpl.php";
			
			}
			
			 return "addteam_tpl.php";
					
		
		
		}
		
		break;
		
		case 'sportdetails':
		    $teamid = $this->getParam('teamid',NULL);		
		    $addurl = $this->getParam('addurl',NULL);		    		    
		    $sportid = $this->getParam('sportid',NULL);
						
			if($this->getParam('addurl',NULL)=='addurl'){
				$addform = $this->objLanguage->languageText('mod_sportsadmin_great');
				
				$data = $this->setVarByRef('addform',$addform);
			
			}
			
	     //for adding breadcrumbs
			$objTools = & $this->newObject('tools', 'toolbar'); 
			$this->sportid = $sportid;
			$crumbs = $this->getBreadCrumbs($sportid, TRUE);
			$objTools->addToBreadCrumbs($crumbs);
			 
		//for adding breadcrumbs
			/*$objTools = & $this->newObject('tools','toolbar');			     
			$crumbs = $this->getBreadCrumbs($tournamentid,TRUE,'tournament');
			$objTools->addToBreadCrumbs($crumbs);	*/		
			 
			return "sportsdetail_tpl.php";				
				
		break;
		
		//modifying value entered for a fixture
		case "editfixture":
		     $item = $this->getParam('item',NULL);
			 $teama = $this->getParam('teama',NULL);
		     $teamb = $this->getParam('teamb',NULL);
			 $fixtureid = $this->getParam('fixtureid',NULL); 
			 $sportid = $this->getParam($sportid ,$fixtureid);
			 $tournamentid = $this->getParam('tournamentid',NULL);		 	   
			   
			return "showfixturedata_tpl.php";
		
		break;
		
		case "modifyfixture":
		    
			$place = $this->getParam('place',NULL);
			$startDate = $this->getParam('startDate',NULL);
			$fixtureid = $this->getParam('fixtureid',NULL);
			
					
			$this->objDbfixtures->modifyfixture($fixtureid,$place,$startDate);			
		 return "sportsdetail_tpl.php";
		break;
		
		case 'playersdetails':
		      $item = $this->getParam('item',NULL);
		     $sportid = $this->getParam('sportid',NULL);
			 
			 return "tournament_main_tpl.php";
						 
		break;
		
		
		case "addfixtures":
					
			 $sportid = $this->getParam('sportid',NULL);
			 $item = $this->getParam('item',NULL);		
			return $this->addfixtures( $sportid, $item);
			 
	    break;
		
		case "addtournament":
			 $sportid = $this->getParam('sportid',NULL);
			 $useEdit = $this->getParam('useEdit',NULL);
			 $tournamentid = $this->getParam('tournamentid',NULL);		
			 return "addtournament_tpl.php";
		break;
		
	
		//case for searching for players using the different categories
		case "search":
		
		  $sportid = $this->getParam('sportid',NULL);
		  $searchoption = $this->getParam('searchoption',NULL);
		  $searchfield = $this->getParam('searchfield',NULL);			
		
		return "searchplayer_tpl.php";
		
		break;
		
		//case for showing the details of the player
		case "playerdetails":
		  $playerid = $this->getParam('playerid',NULL);		
		  $this->setVar('playerid', $playerid);
		  
		  return "playerdetails_tpl.php";
		  
		break;
		
		case "addplayer":			
			$sportid = $this->getParam('sportid',NULL);
			
		    return "addplayer_tpl.php";
		break;
		
		case "modifyplayer":
		$item = $this->getParam('item',NULL);
		$sportid = $this->getParam('sportid',NULL);
		$teamid = $this->getParam('teamid',NULL);
		$fixtureid = $this->getParam('fixtureid',NULL);
		$tournamentid = $this->getParam('tournamentid',NULL);
		$playerid = $this->getParam('playerid',NULL);
		
		
		//the values to be changed
		$position = $this->getParam('position',NULL);
		$name = $this->getParam('playerfield',NULL);
		$team = $this->getParam('team',NULL);
		$dob = $this->getParam('dob',NULL);
		$country = $this->getParam('country',NULL);
		
		
		 //check if the file has been uploaded
		$userfile=$_FILES['playerimage']['name'];
        $size=$_FILES['playerimage']['size'];
        $type=$_FILES['playerimage']['type'];
        $tmp=$_FILES['playerimage']['tmp_name'];
		
		if(is_uploaded_file($tmp)){
		$path_info = pathinfo($userfile);
		$file_extn = $path_info['extension'];
		
		//check for all those extensions that are allowed		
		$path = $this->playerfolder.'/'.$userfile;
		switch($file_extn)
			{			
				case "jpeg":
				case "gif":
				case "png":
				case "tiff":
				case "psd":
				case "swf":
				case "bmp":
				case "jpg":
				case "JPG":
				$res = move_uploaded_file($tmp,$path);
								
				}				
				//if the file has been moved to the directory
				if($res){
				$this->objDbplayer->modifyplayer($playerid,$position,$name,$team,$country);
		        return "sportsdetail_tpl.php";
				
				}
						
		}//closing if file uploaded
		
		
		
		
		
		break;
		
		
		/*
		* Action for saving the entered data in the tournament table
		* 
		*/
		case "savetournament":
		
		   $item = $this->getParam('tournament',NULL);
		   $tournament = $this->getParam('tournament',NULL);
		   $sportId = $this->getParam('sportid',NULL);
		   $sponsor = $this->getParam('sponsor',NULL);
		   $enddate = $this->getParam('enddate',NULL);
		   $startdate = $this->getParam('startdate',NULL);
		   		   
		   $this->objDbtournament->saveTournament($tournament,$sportId,$sponsor,$enddate,$startdate);    	
			  
		   return "sportsdetail_tpl.php";		
				
		break;
		
	  case "modifytournament":
	  	  $item = $this->getParam('tournament',NULL);	  
	      $tournamentid = $this->getParam('tournamentid',NULL);
		  $sportId = $this->getParam('sportid',NULL);
		  $sponsor = $this->getParam('sponsor',NULL);
		  $enddate = $this->getParam('enddate',NULL);
		  $startdate = $this->getParam('startdate',NULL);
		  $tournamentname = $this->getParam('tournament',NULL);
		   
		  $this->objDbtournament->updateTournament($tournamentid,$sponsor,$enddate,$startdate,$tournamentname);	  
		 return "sportsdetail_tpl.php";		 
	  break;
		
		//giving the details of the tournament
		case "tournamentdetails":	
		$sportid  = $this->getParam('sportid',NULL);
		$tournamentid  = $this->getParam('tournamentid',NULL);
		$this->objDbfixture = & $this->getObject('dbfixtures');
		$fixturelist = $this->objDbfixture->getFixturesForTournament($tournamentid,$sportid);
		$this->setVar('fixturelist',$fixturelist);		 
		 
		 $this->sportid = $sportid ;
		 
		 $this->setLayoutTemplate('sportstournament_layout_tpl.php');
		 $tournamentid = $this->getParam('tournamentid',NULL); 
		  
		 //for adding breadcrumbs
		 $objTools = & $this->newObject('tools','toolbar');			     
		 $crumbs = $this->getBreadCrumbs($tournamentid,TRUE,'tournament',$tournamentid);
		 $objTools->addToBreadCrumbs($crumbs);					 
		 return "tournamentdetail_tpl.php";
		  
		break;
		
		//add scores to a fixture
		case "addscores":
		
		$teamid = $this->getParam('teamid',NULL);
		$fixtureid = $this->getParam('fixtureid',NULL);
		$playerid = $this->getParam('playerid',NULL);	
		$sportid = $this->getParam('sportid',NULL);
		$tournamentid = $this->getParam('tournamentid',NULL);
		
		return "scoreadd_tpl.php";
		
		break;
		
		//saving aplayer
		case "saveplayer":
		
		 $item = $this->getParam('item',NULL); 		 
		 $sportid = $this->getParam('sportid',NULL);		 
		 $name = $this->getParam('playerfield',NULL);
		 $team = $this->getParam('team',NULL);
		 $country = $this->getParam('country',NULL);
		 $dob = $this->getParam('dob',NULL);
		 $position = $this->getParam('position',NULL);
		 $teamid = $this->getParam('teamid',NULL);
		 		 
		 //check if the file has been uploaded
		$userfile=$_FILES['playerimage']['name'];
        $size=$_FILES['playerimage']['size'];
        $type=$_FILES['playerimage']['type'];
		
        $tmp =$_FILES['playerimage']['tmp_name'];	
				
		if(is_uploaded_file($tmp)){
		
		$path_info = pathinfo($userfile);
		$file_extn = $path_info['extension'];
	 
	  //check for all those extensions that are allowed		
	  if(!file_exists($this->playerfolder)){
		 $this->objDbplayer->makeFolder($this->playerfolder);		
		  
		 }					 
		 $this->objDbplayer->uploadfile($this->playerfolder);			  
		
		  $this->objDbplayer->saveplayer($name,$team ,$country, $dob ,$position, $sportid,$userfile );
		        
			if(empty($teamid)){
				return "sportsdetail_tpl.php";	
				}	
				else  return "teamdetail_tpl.php";										
		}	
		
		break;
		
		case "addteam":
		 $teamid = $this->getParam('teamid',NULL);
		
		 return "addteam_tpl.php";
		
		break;
		
		case "savefixture":
		
			$team_B = $this->getParam('team_b',NULL);
			$team_A = $this->getParam('team_a',NULL);				 
			$item = $this->getParam('fixtures',NULL);
			$sportId = $this->getParam('sportid',NULL);				
			$place = $this->getParam('place',NULL);
			$startDate = $this->getParam('startDate',NULL);
			$tournamentId = $this->getParam('tournamentid',NULL);
			
										
			$this->objDbfixtures->insertFixture($sportId,$team_A,$team_B,$place,$startDate,$tournamentId);
			
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
		
		case "saveteam":
		 $tournamentdetails = $this->getParam('tournamentdetails',NULL);
		 $item = $this->getParam('fixtures',NULL);
		 $sportId = $this->getParam('sportid',NULL);
		 $homeground = $this->getParam('ground',NULL);
		 $name = $this->getParam('teamfield',NULL);			
		 $motto = $this->getParam('motto');
		 
		 $filename = $_FILES['playerimage']['name'];
		 $tmp = $_FILES['playerimage']['tmp_name'];
		 $size = $_FILES['playerimage']['size'];
		 
		 //get the extension so that only permitted image types can be allowed
		 $path_info = pathinfo($filename);
		$extn = $path_info['extension'];
		
		if(!file_exists($this->logopath)){
		 $this->objDbplayer->makeFolder($this->logopath);		
		  
		 }					
		 
		$this->objDbplayer->uploadfile($this->logopath); 
		
		$this->objDbteam->saveteam($sportId, $name,$homeground,$filename,$motto);	 	
				
		return "sportsdetail_tpl.php";	
			
		 /*else {
		 return "addteam_tpl.php";
		  }		*/ 
		
		break;
		
		case "teamdetails":
		  $teamid = $this->getParam('teamid',NULL);
		  $this->setVar('teamid',$teamid);
		  
		  return "teamdetail_tpl.php";
		  
		break;
		
		//case for adding players to a match
		case "addplayertofixture":
		     $fixtureid = $this->getParam('fixtureid',NULL);			 
		     $sportid = $this->getParam('sportid',NULL);
			//echo $sportid; exit;
			return $this->addplayertofixture($fixtureid,$sportid);
		  
		 
		break;
		
		
		//case for adding  list of player to a given match
		case "addplayertomatch":
		$sportid = $this->getParam('sportid',NULL);
		$tournamentid = $this->getParam('tournamentid',NULL);
		$fixtureid = $this->getParam('fixtureid',NULL);
		$teamid = $this->getParam('teamid',NULL);			
	   
		
		//get the members that have been selected for the match
		$list = $this->getParam('list') ? $this->getParam('list',NULL):array();	
				
		//get the members of the fixture who are already selected
		$players_list = $this->objDbmatchplayers->getPlayersForTeamInFixture($teamid,$fixtureid);
		   
		  	
		$rowFields = array();
		
        foreach($players_list as $row) {		
		
            // Multi-dimensional array
            if( is_array( $row ) ) {			
                $rowFields[] = $row['playerid'];
            } 
        }        
		
		//get the list of new members only
		$newmembers = array_diff($list,$rowFields);
			
		//get the deleted member list
		$deletedmembers = array_diff($rowFields ,$list);			
		
		//delete the members who have been removed from the list		
		foreach($deletedmembers as $de){
				
		 $this->objDbmatchplayers->removePlayer($de);			 
		 	
		}			

		//add the new members one by one
			foreach($newmembers as $n){
			
			$this->objDbmatchplayers->saveplayer($n,$teamid,$fixtureid,$tournamentid,$sportid);			
			
			}					
		
		return "showteamfixturedetails_tpl.php";	 
				
		break;		
		
		
		/*
		* A case to delete a partcular tournament belonging to a 
		* particular sport
		*/
		case "deletetournament":
		   $sportid = $this->getParam('sportid',NULL);
		   $tournamentid = $this->getParam('tournamentid',NULL);
		   $delete = $this->objDbtournament->deleteTournament($sportid,$tournamentid);
		   
		   return "sportsdetail_tpl.php";
		    
		break;
		
		//case for deleting the team
		case "deleteteam":
		  $teamid = $this->getParam('teamid',NULL);
		  $sportid = $this->getParam('sportid',NULL);
		 // $item = $this->getParam('teamid',NULL);
		  // echo $sportid; exit;
		  
	 $delete = $this->objDbteam->deleteTeam($sportid,$teamid);
		   
		   return "sportsdetail_tpl.php";
		  
		break;
		
		
		//case for viewing members of a selected team
		case "viewteammembers":
		$teamid = $this->getParam('teamid',NULL);
		 
		 return "teamplayers_tpl.php";
		break;
		
		/*
		* deleting a given fixture
		*/
		
		case "deletefixture":
		     $sportid = $this->getParam('sportid',NULL);
			 $fixtureid = $this->getParam('fixtureid',NULL);
			 $tournamentid = $this->getParam('tournamentid',NULL);
			 $item = $this->getParam('item',NULL);
						 
			$this->objDbfixtures->deletefixture($sportid,$fixtureid);
			 
			return "tournamentdetail_tpl.php";
		    break;		
		
		/*
		* A case to delete a particular sport
		*/
		case 'delete':
		/**
		* remove the submitted information
		* expected variables:
		* @param $id - delete from tbl_sports where id=$id
		*/
			$id = $this->getParam('id', NULL);
		//proceed to delete
			$this->objDBSports->deleteSport($id);
		return "main_tpl.php";
		break;
		
		/*
		*  case to pick details of teams in a fixture 
		*
		*/
		case "pickscoredetails":
		//$fixtureid = '';
			$sportid = $this->getParam('sportid',NULL);
			$item = $this->getParam('item',NULL);
			$fixtureid = $this->getParam('fixtureid',NULL);
			$submitbutton = $this->getParam('submitdetail',NULL);		
		return "sportsdetail_tpl.php";
		
		//gives details of scores who scored time for the scores etc
		case "scoredetails":	
		$this->setLayoutTemplate('sportstournament_layout_tpl.php');	
		$fixtureid = $this->getParam('fixtureid',NULL);  
		$tournamentid = $this->getParam('tournamentid',NULL);
		$sportid = $this->getParam('sportid',NULL);
		$teamid = $this->getParam('teamid',NULL);
		
		return "scoredetail_tpl.php";
		break;
		
		
			
		break;
		
		case "savescores":
			$sportid = $this->getParam('sportid',NULL);
			$item = $this->getParam('item',NULL);
			$tournamentId = $this->getParam('tournamentid',NULL);
			$teamid = $this->getParam('teamid',NULL);
			$fixtureid = $this->getParam('fixtureid',NULL);
			$time = $this->getParam('time',NULL);
			$playerid = $this->getParam('playerid',NULL);
		
		
		$this->objDbscores->insertdata($sportid,$tournamentId,$teamid,$fixtureid,$time,$playerid );
		
		return "showteamfixturedetails_tpl.php";
		break;
		
		case 'tablestandings':
		    $sportid = $this->getParam('sportid',NULL);
		    $item = $this->getParam('item',NULL);
			
			return 'tablestandings_tpl.php';
						
		break;
		
				
	}// End of Switch

}// End of public function dispatch()


	
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
									
		  $tourn = $this->objLanguage->languageText('mod_sportsadmin_tournament','sportsadmin');		
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
				$arrNodeLinks[] = $sportLink->show(); 
            }
	  break;
	  
	  } 	             	
       
        return array_reverse($arrNodeLinks);
		
     }//closing the public function
	 
	 
	   /**
    * Method to show the options from which to pick the teams for the fixture
    * @param 
    */
 public  function addfixtures($sportid,$item)
    {
	//echo "there"; exit;
$this->appendArrayVar('headerParams', '<script src="modules/sportsadmin/resources/sorttable.js" language="JavaScript" type="text/javascript"></script>');	   
	   
$this->loadClass('hiddeninput','htmlelements');
//team a value
$team_a = new hiddeninput('team_a');
$team_b = new hiddeninput('team_b');    		

$frmManage = &$this->getObject( 'form', 'htmlelements' );
$frmManage->name = 'frmManage';
$frmManage->displayType = '3';	   
$frmManage->action = $this->uri(array('action'=>'savefixture','item'=>'fixtures','sportid'=>$sportid,'tournamentid'=>$this->getParam('tournamentid',NULL)));
   
$this->loadClass('textinput','htmlelements');

$memberListdrop = $this->objDbteam->getAll($sportid);      		
$teams_label = $this->objLanguage->languageText('mod_sportsadmin_registeredteams','sportsadmin');  

// Members list dropdown
$lstMembers = $this->newObject( 'dropdown', 'htmlelements');
$lstMembers->name = 'list[]';
$lstMembers->extra = ' multiple="multiple" style="width:100pt" size="10" ';
        foreach ( $memberListdrop  as $user ) {
        			
			 $lstMembers->addOption($user['id'],$user['name']);
        }		
$allteams = '<table><tr><th>'.$teams_label.'</th></tr><tr><td>'.$lstMembers->show().'</td></tr></table>';		
		
$selectedteamA = new textinput('selectedteamA');
$teamAmember = '<table><tr><th>' . $this->objLanguage->languageText('mod_sportsadmin_teamamembers','sportsadmin') .'</th></tr><tr><td>' .$selectedteamA->show().'</td></tr></table>';
		
$selectedteamB = new textinput('selectedteamB');
$teamBmember = '<table><tr><th>'. $this->objLanguage->languageText('mod_sportsadmin_teambmembers','sportsadmin') .'</th></tr><tr><td>'.$selectedteamB->show().'</td></tr></table>';



$tblLayoutM= &$this->newObject( 'htmltable', 'htmlelements' );
$tblLayoutM->row_attributes = 'align="center" ';
$tblLayoutM->width = '100px';

$tblLayoutM->startRow();			
$tblLayoutM->endRow();
$tblLayoutM->startRow();
$tblLayoutM->addCell($allteams);
$tblLayoutM->endRow();
$this->setVarByRef('allteams', $tblLayoutM);

//table for the team a members
$tblLayoutU= &$this->newObject( 'htmltable', 'htmlelements' );
$tblLayoutU->row_attributes = 'align="center"';
$tblLayoutU->width = '100px';
$tblLayoutU->startRow();			
$tblLayoutU->endRow();
$tblLayoutU->startRow();
$tblLayoutU->addCell($teamAmember);
$tblLayoutU->endRow();
$this->setVarByRef('teamAmember', $tblLayoutU);			

//table for the team B members
$tblLayoutB= &$this->newObject( 'htmltable', 'htmlelements');
$tblLayoutB->row_attributes = 'align="center"';
$tblLayoutB->width = '100px';


$tblLayoutB->startRow();		
$tblLayoutB->addCell($teamBmember);
$tblLayoutB->endRow();
$this->setVarByRef('teamBmember', $tblLayoutB);				
$this->_objLanguage = & $this->getObject('language','language');

// Link method
$lnkSave = $this->newObject('link','htmlelements');      
$lnkSave->href  = '#';       
$lnkSave->extra.= 'selectAllOptions(document.forms[\'frmManage\'][\'list[]\'] ); ';
$lnkSave->extra = 'onclick="javascript:';
$lnkSave->extra.= 'document.forms[\'frmManage\'][\'button\'].value=\'save\'; ';
$lnkSave->extra.= 'document.forms[\'frmManage\'].submit(); "';
$lnkSave->link  = $this->_objLanguage->languageText( 'word_save');		
		
//create the button to submit
$lnksubmit = & $this->getObject('button','htmlelements');
$lnksubmit->name = "save";
$lnksubmit->value =$this->_objLanguage->languageText( 'word_save');
$lnksubmit->setToSubmit();

$this->setVarByRef('lnksubmit',$lnksubmit);

$lnkCancel = $this->newObject('link','htmlelements');
$lnkCancel->href  = $this->uri(array('item'=>'tournament','action'=>'tournamentdetails','sportid'=>$sportid,'tournamentid'=>$this->getParam('tournamentid',NULL)));
$lnkCancel->link  = $this->_objLanguage->languageText( 'word_cancel');

$ctrlButtons = array();
$ctrlButtons['lnkSave'] = $lnkSave->show();
$ctrlButtons['lnkCancel'] = $lnkCancel->show();
$this->setVar('ctrlButtons',$ctrlButtons);

		//forms['frmManage']['list2[]']
$lnkALeft = "<a href=\"#\" onclick=\"javascript:moveOptionToLeft(document.forms['frmManage']['selectedi'],document.forms['frmManage']['selectedteamA'],document.forms['frmManage']['list[]'])\">";
$lnkALeft .= htmlspecialchars('<< From team 1')."</a> "; 

$lnkright = "<a href=\"#\" onclick=\"javascript:moveSelectedAOption(document.forms['frmManage']['list[]'],document.forms['frmManage']['selectedteamA'],document.forms['frmManage']['team_a'])\">";
$lnkright .= htmlspecialchars('>> To team 1')."</a> "; 


// The move selected items left button for the second team
 $lnkBLeft = "<a href=\"#\" onclick=\"javascript:moveOptionToLeft(document.forms['frmManage']['bright'],document.forms['frmManage']['selectedteamB'],document.forms['frmManage']['list[]']))\">";
 $lnkBLeft.= htmlspecialchars('<< From team 2'). "</a>"; 



$lnkBright = "<a href=\"#\" onclick=\"javascript:moveSelectedBOption(document.forms['frmManage']['list[]'],document.forms['frmManage']['selectedteamB'],document.forms['frmManage']['team_b'])\">";
$lnkBright .= htmlspecialchars( '>> To team 2' )."</a>"; 

$btns = array($lnkALeft, $lnkright);
$buttons = '<div>'. implode( '<br/>', $btns ) . '</div>'; 

$btns_b = array($lnkBLeft,$lnkBright);
$buttons_b = '<div>' .implode( '<br/>', $btns_b) . '</div>'; 		

//$placeinput = new textinput('place');
		
		$this->setVar('buttons',$buttons);
		$this->setVar('buttons_b',$buttons_b);		
        $this->setVar('team_a',$team_a);
	    $this->setVar('team_b',$team_b);
		
		
        $frmManage->addToForm("<input type='hidden' name='button' value=''/>");           
        
        $this->setVarByRef('frmManage', $frmManage );
        return 'addfixtures_tpl.php';
    }
	
		
	
public function addplayertofixture($fixtureid,$sportid){

$this->objDbfixture = & $this->getObject('dbfixtures');

$this->appendArrayVar('headerParams', '<script src="core_modules/groupadmin/resources/selectbox.js" language="JavaScript" type="text/javascript"></script>');

$this->loadClass('hiddeninput','htmlelements');	   
$frmManage = &$this->getObject('form', 'htmlelements' );
$frmManage->name = 'frmManage';
$frmManage->displayType = '3';	   
$frmManage->action = $this->uri(array('action'=>'addplayertomatch'));

$this->loadClass('textinput','htmlelements');	
$fixturedata = $this->objDbfixture->getFixtureforgame($sportid,$fixtureid);
	
//pick the teams in the fixture
foreach($fixturedata  as $f){
	$team_aid = $f['team_a'];
	$team_bid = $f['team_b'];
}

$teamid  = $this->getParam('teamid',NULL);

//get the name of the team
$teamname = $this->objDbteam->getTeamNameById($teamid);

$teama =  $this->objDbteam->getTeamNameById($team_aid);
$teamb =  $this->objDbteam->getTeamNameById($team_bid);

//pick the members from the database if any for a given match
$fixtureplayers = $this->objDbmatchplayers->getPlayersForTeamInFixture($teamid,$fixtureid);

//get all players in selected team
$team_members = $this->objDbplayer->getTeamMembers($teamid,$sportid);

$nonMatchMembers = $this->objDbmatchplayers->getTeamMembersNotInMatch($teamid,$fixtureid);

 $nonmembers = $this->newObject( 'dropdown', 'htmlelements');
 $nonmembers->name = 'list2[]';
 $nonmembers->extra = " multiple='multiple' style='width:100pt' size='10'";
 

if(!empty($nonMatchMembers)){

  foreach ($nonMatchMembers  as $m){
           $name = $m['name'];
           $memberPKId = $m['id'];
           $nonmembers->addOption($memberPKId,$name);
        } 		
}

$memberstable = "<table><tr><th>".$teamname."&nbsp;".$this->objLanguage->languageText('mod_sportsadmin_teammembers','sportsadmin')."</th></tr><tr><td>".$nonmembers->show()."</td></tr></table>";



$selectedmembers = $this->newObject('dropdown', 'htmlelements');
$selectedmembers->name = 'list[]';
$selectedmembers->extra = 'multiple="multiple" style="width:100pt" size="10" ';

if(!empty($fixtureplayers )){ 

  foreach($fixtureplayers  as $f){
    $playername = $this->objDbplayer->getPlayerNameById($f['playerid']);
    $memberPKId = $f['playerid'];
    $selectedmembers->addOption($memberPKId,$playername);   
  }

}

$selected = "<table><tr><th>".$this->objLanguage->languageText('mod_sportsadmin_selectedmembers','sportsadmin')."</th></tr><tr><td>".$selectedmembers->show()."</td></tr></table>";

$tblLayoutMm= &$this->newObject( 'htmltable','htmlelements' );
$tblLayoutMm->row_attributes = 'align="center" ';
$tblLayoutMm->width = '100px';

$tblLayoutMm->startRow();			
$tblLayoutMm->endRow();
$tblLayoutMm->startRow();
$tblLayoutMm->addCell($memberstable);
$tblLayoutMm->endRow();

$this->setVarByRef('memberstable', $tblLayoutMm);

//The save button
$btnSave = $this->newObject( 'button', 'htmlelements' );
$btnSave->name = 'btnSave';
$btnSave->value = $this->objLanguage->languageText( 'word_save');
$btnSave->onclick = "selectAllOptions(this.form['list[]'])";
$btnSave->setToSubmit(); 

$this->setVarByRef('btnSave', $btnSave);	

$tblLayoutM= &$this->newObject( 'htmltable', 'htmlelements' );
$tblLayoutM->row_attributes = 'align="center" ';
$tblLayoutM->width = '100px';

$tblLayoutM->startRow();
$tblLayoutM->addCell($selected);
$tblLayoutM->endRow();

$this->setVarByRef('selected', $tblLayoutM);

$this->_objLanguage = & $this->getObject('language','language');        

$tournamentid = $this->getParam('tournamentid',NULL);

$lnkCancel = $this->newObject('link','htmlelements');
$lnkCancel->href  = $this->uri(array('item'=>'tournament','action'=>'showteamfixturedetails','sportid'=>$sportid,'fixtureid'=>$fixtureid,'tournamentid'=>$tournamentid));
$lnkCancel->link  = $this->_objLanguage->languageText( 'word_cancel');
$this->setVar('lnkCancel',$lnkCancel);	

$lnkALeft = "<a href=\"#\" onclick=\"javascript:moveSelectedOptions(document.forms['frmManage']['list2[]'],document.forms['frmManage']['list[]'])\">";
$lnkALeft .= htmlspecialchars('>>')."</a> ";      

$lnkright = "<a href=\"#\" onclick=\"javascript:moveAllOptions(document.forms['frmManage']['list2[]'],document.forms['frmManage']['list[]'])\">";
$lnkright .= htmlspecialchars('All >>')."</a> "; 


// The move selected items left button for the second team
$lnkBLeft = "<a href=\"#\" onclick=\"javascript:moveSelectedOptions(document.forms['frmManage']['list[]'],document.forms['frmManage']['list2[]'])\">";
$lnkBLeft.= htmlspecialchars('<<'). "</a>"; 
 
 
$lnkBright = "<a href=\"#\" onclick=\"javascript:moveAllOptions(document.forms['frmManage']['list[]'],document.forms['frmManage']['list2[]'])\">";
$lnkBright .= htmlspecialchars( 'All <<' )."</a>"; 

$btns = array($lnkALeft, $lnkright);
$buttons = '<div>'. implode( '<br/>', $btns ) . '</div>';

$btns_b = array($lnkBLeft,$lnkBright);
$buttons_b = '<div>' .implode( '<br/>', $btns_b) . '</div>'; 	
		
$this->setVar('buttons',$buttons);
$this->setVar('buttons_b',$buttons_b);		
$this->setVar('team_a',$team_a);
$this->setVar('team_b',$team_b);


$this->setVarByRef('frmManage', $frmManage);

return "addplayertomatch_tpl.php";
	}
	
	
	

	/**
    * Override the default requirement for login
    */
    public function requiresLogin()
    {
        return False;  
    }
}//closing the class

?>
<?php 
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*
*  Class to interact with tbl_sportsnews table
*  @Author : Nsabagwa Mary
*/

class sportsnews extends dbtable{
function init(){

parent::init('tbl_sportsnews');
$this->table = "tbl_sportsnews";

}


/*
* Function to save the news for the team entered
* @Param $teamid - id of the team
* @param $sportid - id of the sport
*/

public function saveNews($teamid,$sportid,$news){
$this->objUser = & $this->getObject('user','security');
$userid = $this->objUser->userId();

//echo $userid."is the username"; exit; 

$sql = $this->insert(
    array(
	 'teamId'=>$teamid,
	 'sportId'=>$sportid,
	 'news'=>$news,
	 'creator'=>$userid	 
	 
	));


}

/*function to get the latest entries of news
* @ param $teamid - id of the team whose latest sport is being looked for
*@ param $sportid - sport to which 
*/
public function getLatestNewsForTeam($teamid,$sportid){
$sql = " SELECT * FROM ".$this->table." WHERE teamId='".$teamid."' and sportId='".$sportid."' order by dateCreated desc limit 3";

$ar = $this->getArray($sql);
  if ($ar){
  return $ar;
  }
  else{ return false;}

}

}//closing the class
?>
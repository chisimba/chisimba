<?php 


/*
 * A class to interact with tbl_pastpaper table
 *  @Author : Nsabagwa Mary
*/

class dbanswers extends dbTable{

function init(){
parent::init('tbl_pastpaper_answers');
$this->table = 'tbl_pastpaper_answers';

//instance of the language items
$this->objLanguage = & $this->getObject('language','language');
$this->objUser = & $this->getObject('user','security');
$this->_objDBContext = & $this->getObject('dbcontext','context');


}

/*
* Function to save the pastpaper answers
* @param $file_name - name of the file to be saved
* @param $paperid - number of the question paper whose answers are being saved
*/
public function saveanswers($file_name,$paperid){

$this->_objDBContext = $this->getObject('dbcontext','context');
$this->objUser = $this->getObject('user','security');

  $uploadDate = date('Y-m-d H:i:s');
   $contextCode = $this->_objDBContext->getContextCode(); 
   if(!$contextCode){$contextCode = "lobby";}  
   
    $fields = array(    'addedBy' => $this->objUser->userId(),						
                        'filename' => $file_name,
                        'dateuploaded' => $uploadDate,
						'published'=> 0 ,
						'paperid'=>  $paperid                 
                       
                        ); 
		   $ret = $this->insert($fields);
		   return $ret;
}


/*
* Function to get all the answers for a specific paper
* @param $paperid- id of the paper whose answers are being added
*/
public function getpastpaperanswers($paperid){
//give a full list of the answers if user is an administrator or context lecturer
$this->objpastpapers = & $this->getObject('pastpaper');
if($this->objUser->isCourseAdmin() || $this->objpastpapers->getPaperAuthor($paperid)){
	$sql = "select * from $this->table where paperid='$paperid'";
		}
	
else {
 
	$sql = "select * from $this->table where paperid='$paperid' and published=1";
    
}



 $ar = $this->getArray($sql);
	if($ar){return $ar;}
	
	else return false;
	
	


}

/*
* Function to check if the paper ha answers
* @param $paperid- id of the paper
*/
public function hasAnswers($paperid){
$sql = "select count(paperid) as number from $this->table where paperid='$paperid' ";
$ar = $this->getArray($sql);

if($ar[0]['number']!=0){
  return "yes";

}

else return "No";

}
/*
* Function to check if answers submitted by the user can be visible
* @param id - id of the paper that is being checked for visibility
*/
function isVisible($id){
$sql = "select * from $this->table where id='$id'";
$ar = $this->getArray($sql);

if($ar[0]['published']!=0){
  return "yes";

}

else return "No";

}

public function isPaperCreator($id){

$creator = $this->objUser->userId();

//check if the current user is the creator of the context 
$sql = "select * from $this->table where id='$id'";
$ar = $this->getArray($sql);

if ($ar[0]['creator']==$creator){
   return true;
}
  return false;

}


/*Function to check for the permissions of the user on the paper
* Only users who add the answers  and the contextadministrators are allowed to delete, and 
* edit the papers
*/

function isPaperAdmin($id){

$contextCode  = $this->_objDBContext->getContextCode();

if(!$contextCode){ 
//if the user is the one who added the paper or is a context adminstrator
  if($this->isPaperCreator($id) || $this->objUser->isAdmin()){
    return true;
  
  }
  
  else return false;
}

else {
//check for the administrators of the context i.e lecturers and administrators
if($this->objUser->isCourseAdmin())
   {return true;}
else return false;

}



}

/*
* Function to make the submitted answers visible to all user
*/
public function publish($id){

$this->update("id", $id, array(
			'published'=> 1,				
			'updated' => date("Y-m-d H:m:s")));

}

/*
* Function to make the submitted answers visible to all user
*/
public function unpublish($paperid){
//$updatedBy = $this->objUser->userId();

$this->update("id", $paperid, array(
			'published'=> 0,				
			'updated' => date("Y-m-d H:m:s")));

}


/*
* Function to delete the answers for a particular paper
* @param  $paperid - id of the paper to be deleted
*/

function deleteanswers($id){
$sql = "DELETE FROM ".$this->table." WHERE paperid='$id'";
return $this->query($sql);

}


}//closing the class

?>
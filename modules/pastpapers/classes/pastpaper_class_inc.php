<?php 


/*
 * A class to interact with tbl_pastpaper table
 *  
*/

class pastpaper extends dbTable{

function init(){
parent::init('tbl_pastpapers');
$this->table = 'tbl_pastpapers';

//instance of the language items
$this->objLanguage = & $this->getObject('language','language');
$this->objConfig = & $this->getParam('altconfig','config');
$this->objDbanswers = $this->getObject('dbanswers');

}

/*
* Function to upload the pastpapers
* $filename - name of the file that has been uploaded
*/
function uploadfile($folder,$contextCode='lobby'){
$this->objConfig = & $this->getObject('altconfig','config');
//$contextCode = $this->_objDBContext->getContextCode();
 if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
   $file_name = $_FILES['filename']['name'];   
   $tmp = $_FILES['filename']['tmp_name'];
   $size = $_FILES['filename']['size'];     
  // getModulePath()
   $folderpath = $folder."/".$file_name;     
   $path = str_replace('\\', '/',$folderpath);
   
   //check extensions
    $path_info = pathinfo($file_name);
    $extn = $path_info['extension'];
	
    switch($extn){ 
			case "doc":
			case "pdf":
			case "txt":
			case "ppt":	
			case "xls":				
			 $res= move_uploaded_file($tmp,$path);
			break;
		
		}//closing the switch	
	 if($res){

        return true;
  }
  else return false;	
}//closing if not uploaded

}


/*
* Function that adds the past paper details to the database
* @param - $file_name - name of the file
* @param - $examyear Year the paper was sat for
*/
function savepaper($file_name,$examyear,$topic,$option ){
$this->_objDBContext = $this->getObject('dbcontext','context');
$this->objUser = $this->getObject('user','security');

   $uploadDate = date('Y-m-d H:i:s');
   $contextCode = $this->_objDBContext->getContextCode();    
    $fields = array( 'contextcode' => $contextCode,
                        'userid' => $this->objUser->userId(),
						'topic'=>$topic,
                        'filename' => $file_name,
                        'dateuploaded' => $uploadDate,
						'options'=> $option,
                        'hasanswers' => 0,
                        'examyear' =>$examyear
                        ); 
		   $ret = $this->insert($fields);
		   return $ret;
}


/*
* Function to get all the available pastpapers for the course
* @param $contextcode 
*/
function getpapersforcontext($contextcode){

//check if person is in context
if(!$contextcode){

   $sql = "select * from ".$this->table." where contextcode IS NULL";
}
else {
  $sql = "select * from ".$this->table." where contextcode ='$contextcode'";
  
   }   
   
$ar = $this->getArray($sql);
if($ar ){ return $ar;}

 else return false;


}


/*
* Funcion to didplay a list of all the papaers that are in the conext outside the one user is in
*/
function getotherpapers($contextcode){
if($contextcode){
$sql = "select * from ".$this->table." where contextcode !='$contextcode' or contextcode IS NULL ";
}

else 
  $sql = "select * from ".$this->table." where contextcode IS NOT NULL ";

$ar = $this->getArray($sql);
if($ar ){ return $ar;}

 else return false;



}
/*
* Function to get the details of the paper
*/
function getPaperDetails($paperid){
$this->_objDBContext = & $this->getObject('dbcontext','context');

$sql = "select * from ".$this->table." where id ='$paperid'";
$ar = $this->getArray($sql);
if($ar ){ return $this->_objDBContext->getTitle($this->_objDBContext->getContextCode())."&nbsp;(&nbsp;".$ar[0]['examyear']."&nbsp;".$ar[0]['topic'].")";}

 else return false;


}

/*
* Function to check if other people can add answers
*/
function allCanAddAnswers($contextCode,$id)
    {	
	$sql = "select * from $this->table where contextcode= '$contextCode' and id='$id'";
	$ar = $this->getArray($sql);
	if($ar){
	
	return $ar[0]['options'];
	
	  }
	
	else return false;
	
         
    } 
	

     /**
    * method to create specified folder
    * @access public
    * @param string $folder The folder that needs to be created
    */
    public function makeFolder($folder_name,$contextCode=NULL)
    {   $this->objConfig = & $this->getObject('altconfig','config');
        $dir = $this->objConfig->getcontentBasePath().'content';

        if (!(file_exists($dir))){
            $oldumask = umask(0);
            $ret = mkdir($dir, 0777);
            umask($oldumask);
        }
        
        if ($contextCode==''){	
		
			$dir_i = $this->objConfig->getcontentBasePath().'content/'.$folder_name;
			$dir = str_replace('\\', '/',$dir_i);
        } else {
            $dir_i =$this->objConfig->getcontentBasePath().'content/'.$contextCode.'/'.$folder_name;
			$dir = str_replace('\\', '/',$dir_i);
        }
		

		
        if (!(file_exists($dir))){		
		//die($dir);
            $oldumask = umask(0);
            $ret_2 = mkdir($dir, 0777);  //STILL NEED A DECISION ON BLOBS OR FILE SYSTEM
            umask($oldumask);
			
			
        }
        else
        {
            $ret = FALSE;
        }
        
        return $ret;
    }
    
//function to search through the past papers  

function searchforpapers($option,$searchoption ){
$sql = "select * from $this->table where  $searchoption like '%$option%'";


$ar = $this->getArray($sql);

if($ar){ return $ar;}
else return false;
	
}

function getPaperAuthor($id){
$sql = "select * from $this->table where id ='$id'";
$ar = $this->getArray($sql);
if($ar){
  return $ar[0]['userid'];}

else return false;
}

/*
* Function for adding deleting a paper
*/
function deletepaper($paperid){
 
$sql = "DELETE FROM ".$this->table." WHERE id='$paperid'";

///delete the answers also
$this->objDbanswers->deleteanswers($paperid);

return $this->query($sql);

}
   



}//closing the class


?>
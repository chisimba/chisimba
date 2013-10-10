<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* 
* @ Athor Nsabagwa Mary
**/



class pastpapers extends controller
{

/**
    * @var  object $objLanguage
    */
   //var $objLanguage;
	
 public function init(){
	$this->objLanguage = $this->getObject('language','language');
	$this->_objDBContext =& $this->getObject('dbcontext','context');
	$this->objConfig = $this->getObject('altconfig','config');
	$this->objUser = $this->getObject('user','security');
    $this->pastpaper =& $this->getObject('pastpaper');
	$this->objDbanswers = & $this->getObject('dbanswers');
	

	
	}//end of function	
	
	public function dispatch($action){
	$this->setLayoutTemplate('pastpaper_layout_tpl.php');
	
	 switch ($action) { 
	 
	   case "add":	  
	    return "add_tpl.php";
	   break;
	   case "savepaper":
	   
	     $contextCode = $this->_objDBContext->getContextCode();
	     $topic = $this->getParam('topic',NULL); 
	     $examyear = $this->getParam('date',NULL);
		 $option = $this->getParam('option',NULL);
	     $this->pastpaper =& $this->getObject('pastpaper');
		 
		  $pastpaperfolder = "papers";
		if($contextCode){
		 $folder = $this->objConfig->getcontentBasePath().'content/'.$contextCode.'/'.$pastpaperfolder;
		 }
		 
		 else {
		 $folder = $this->objConfig->getcontentBasePath().'content/'.$pastpaperfolder;

		 }
		 if(!file_exists($folder)){
		 $this->pastpaper->makeFolder("papers",$contextCode);		 
		 }
		 //echo  $folder; exit;
		 $this->pastpaper->uploadfile($folder,$contextCode);		
		
	  $file_name = $_FILES['filename']['name'];			 
	  $this->pastpaper->savepaper($file_name,$examyear,$topic, $option);
	  return "main_tpl.php";   	 
		
	   break;
	   
	   case "saveanswers":
	   	 $contextCode = $this->_objDBContext->getContextCode();
		  $answersfolder = "answers";
		  
	     $paperid = $this->getParam('paperid',NULL); 		   
	     $this->pastpaper =& $this->getObject('pastpaper');		 
		  
		 
		if($contextCode){
		$folder = $this->objConfig->getcontentBasePath().'content/'.$contextCode.'/'.$answersfolder;
		 }
		 
		 else {
		 $folder = $this->objConfig->getcontentBasePath().'content/'.$answersfolder;	

		 }
		 
		 if(!file_exists($folder)){
		 $this->pastpaper->makeFolder("answers",$contextCode);		 
		 }
		
		
		 $this->pastpaper->uploadfile($folder,$contextCode);		
		
	  $file_name = $_FILES['filename']['name'];			 
	  $this->objDbanswers->saveanswers($file_name,$paperid);
	  
	  return "showanswers_tpl.php";   	 
	   
	   break;
	   
	   case "viewanswers":
	   $paperid = $this->getParam('paperid',NULL);	   
	   
	     return "showanswers_tpl.php";
	   break;
	   
	   case "otherpapers":
	   return "otherpapers_tpl.php"; 
	   break;
	   
	   case "addanswers":
	     $paperid = $this->getParam('paperid',NULL);
		 return "addanswers_tpl.php";		 
	   break;
	   
	   case "publish":
	     $id = $this->getParam('id',NULL);
	     $paperid = $this->getParam('paperid',NULL);
	     $this->objDbanswers->publish($id);
	    
		return "showanswers_tpl.php"; 
	   break;
	   
	   case "deletepaper":
	   $paperid = $this->getParam('id',NULL);
	   
	
	     $this->pastpaper->deletepaper($paperid);
		 return "main_tpl.php";
	   break;
	   
	   case "unpublish":
	      $id = $this->getParam('id',NULL);
	      $paperid = $this->getParam('paperid',NULL);
	     $this->objDbanswers->unpublish($id);
	    
		return "showanswers_tpl.php"; 
	    
	   break;
	   
	   case "search":
	   $search = $this->getParam('search',NULL);	
	   $searchoption = $this->getParam('searchoption',NULL);    
	   return "search_tpl.php";
	   break;	   
	   
	   default : return "main_tpl.php";
	 
	 }//closing the switch 
	 
	 
	 }
	 
	
	
	
	}



?>
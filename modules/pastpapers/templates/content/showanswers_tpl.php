<?php 
/*
* A template to display a list of available pastpapers for a given context
* or lobby
* Author : Nsabagwa Mary
* A template to give a list of the answers for the pastpapers
*/
$paperid = $this->getParam('paperid',NULL);
$this->objpastpapers = & $this->getObject('pastpaper');
if($this->_objDBContext->isInContext()){
$contextCode  = $this->_objDBContext->getContextCode();
//get the name of the context
$contextName = $this->_objDBContext->getTitle($contextCode);
 }

else {
// the person is in lobby if not in course
$contextName = $this->objLanguage->languageText('mod_pastpapers_lobby','pastpapers'); 

 }
 
//the link class
$this->loadClass('link','htmlelements');


//instance of the user class for permissions
$this->objUser = $this->getObject('user','security');
$this->objLanguage = & $this->getObject('language','language');
$addicon = $this->getObject('geticon','htmlelements');
$this->loadClass('htmltable','htmlelements');
$heading = & $this->getObject('htmlheading','htmlelements');

$this->objDbanswers = $this->getObject('dbanswers');
$heading->align = "center";

$content .= $heading->show();

$content = "";

$table = new htmltable();

//check if there are any papers to display
$paperlist = $this->objDbanswers->getpastpaperanswers($paperid);


if(!empty($paperlist)){


$heading->str = $this->objLanguage->languageText('mod_pastpapers_answerlist','pastpapers')."&nbsp;".$contextName;

$heading->str .= "&nbsp;".$addicon->getAddIcon($this->uri(array('action'=>'addanswers','paperid'=>$paperid)));

//}

	$table->startRow();
	$table->addHeaderCell($this->objLanguage->languageText('mod_fileshare_filename','fileshare'));
    $table->addHeaderCell($this->objLanguage->languageText('word_date'));
	$table->addHeaderCell($this->objLanguage->languageText('mod_pastpapers_addedby','pastpapers'));
	//if this is an administrator or a contextauthor
	if($this->objUser->isCourseAdmin() || $this->objpastpapers->getPaperAuthor($paperid)==$this->objUser->userId()){
    $table->addHeaderCell($this->objLanguage->languageText('word_visible'));	
	}
	$table->endRow();
	
	foreach($paperlist as $p){
	
	$filelink = new link();
	$filelink->link = $p['filename'];	
	$root = $downloadlink->href .= str_replace('\\', '/',$this->objConfig->getcontentRoot());
	$filelink->href = $root."content/".$contextCode."/answers/".$p['filename'];		
	
     $addedby = $this->objUser->fullname($p['addedBy']);
	 
	 //put another link for the lecturer to publish the document
	 $publishlink = new link();
	 
	 $visibility = $this->objDbanswers->isVisible($p['id']);
	 if($visibility =='No'){
	
     $publishlink->link = $this->objLanguage->languageText('mod_pastpapers_makevisible','pastpapers');
	 $publishlink->href = $this->uri(array('action'=>'publish','paperid'=>$paperid,'id'=>$p['id']));
	 
	 }
	 
	 else { 
	
	 $publishlink->link = $this->objLanguage->languageText('mod_pastpapers_unpublish','pastpapers');
	 $publishlink->href = $this->uri(array('action'=>'unpublish','paperid'=>$paperid,'id'=>$p['id']));
	 
	 }
	 
	  $table->startRow();
	  $table->addCell($filelink->show());
	  $table->addCell($p['dateuploaded']);
	  $table->addCell($addedby);
	  if($this->objUser->isCourseAdmin() || $this->objpastpapers->getPaperAuthor($paperid)==$this->objUser->userId()){
	  
	  $table->addCell($this->objDbanswers->isVisible($p['id'])."[".$publishlink->show()."]");
	  }
	  $table->endRow();	
	}///closing foreach
}//closing if not empty



else {

   $table->startRow();
   $table->addCell($this->objLanguage->languageText('mod_pastpapers_noanswersfor','pastpapers')."&nbsp;".$contextName."&nbsp;".$addicon->getAddIcon($this->uri(array('action'=>'addanswers','paperid'=>$paperid))));
   $table->endRow(); 

}
//note for those who submitted answers for the past papers
$note = $this->objLanguage->languageText('mod_pastpapers_note','pastpapers');


$content .= $heading->show();
$content .= $table->show();
//$content .= $note;

echo $content; 

?>
<?php 
/*
* A template to display a list of available pastpapers for a given context
or lobby
*/
$objIcon = & $this->newObject('geticon', 'htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('radio','htmlelements');
$this->loadClass('button','htmlelements');
$this->objUser = & $this->getObject('user','security');
$this->pastpapers = $this->getObject('pastpaper');
$this->_objDBContext->getContextCode();
$heading = $this->getObject('htmlheading','htmlelements');
$heading->align = "center";
$content = "";
$this->objLanguage= $this->getObject('language','language');
$this->loadClass('htmltable','htmlelements');
$addicon = $this->getObject('geticon','htmlelements');
$this->objPastpapers = & $this->getObject('pastpaper');
$this->objDbanswers = & $this->getObject('dbanswers');

//include the link class
$this->loadClass('link','htmlelements');
$table->width = "90%";
$table->align = "center";
$table->cellspacing =4;

if($this->_objDBContext->isInContext()){

$contextCode  = $this->_objDBContext->getContextCode();
//get the name of the context
$contextName = $this->_objDBContext->getTitle($contextCode);
 }

else {
// the person is in lobby if not in course
$contextName = $this->objLanguage->languageText('word_inlobby'); 

 }
 
$search_label =  $this->objLanguage->languageText('mod_pastpapers_searchby','pastpapers'); 
$searchfield = new textinput('search');
//submit button
$submit = new button('submit',$this->objLanguage->languageText('word_submit'));
$submit->setToSubmit();

//options to use for searching
$searchoption = new radio("searchoption");

$searchoption->addOption('topic',$this->objLanguage->languageText('mod_pastpapers_topic','pastpapers'));
$searchoption->addOption('examyear',$this->objLanguage->languageText('word_date'));
$searchoption->setSelected('topic');
 
$searchdata = $search_label."&nbsp;".$searchfield->show()."&nbsp;".$searchoption->show()."&nbsp;".$submit->show();
$uri = $this->uri(array('action'=>'search'));
$form = new form('searchform',$uri);
$form->addToForm($searchdata);
 
$content .=  $form->show();
 
 
//check if there are some past papers in the database and if there are some, display a list 
$past_papers = $this->pastpapers->getpapersforcontext($contextCode);  
$table = new htmltable();
$table->cellspacing = 2;

if(empty($past_papers)){  

$heading->str = $this->objLanguage->languageText('mod_pastpapers_listofpapers','pastpapers')."&nbsp;".$contextName."&nbsp;".$addicon->getAddIcon($this->uri(array('action'=>'add')));

}

else {


//begin collecting one by one row 
    $table->startRow();
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_fileshare_filename','fileshare')."<b/>",'','','left');	
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_topic','pastpapers')."<b/>",'','','left');		
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_examtime','pastpapers')."<b/>",'','','left');		
    $table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_hasanswers','pastpapers')."<b/>",'','','left');	
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_addedby','pastpapers')."<b/>",'','','left');	
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_dateadded','pastpapers')."<b/>",'','','left');
 //check the permissions of the person logged in and whether students can add answers
 //if ($this->objUser->isCourseAdmin() && $this->objPastpapers->allCanAddAnswers($contextCode)){  
   
    $table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_answers','pastpapers')."<b/>",'','','left');
   $table->addHeaderCell("<b/>".$this->objLanguage->languageText('word_action')."<b/>",'','','left');

	$table->endRow();
	
	$class = 'even';
	foreach($past_papers as $p){
	 $class = ($class == 'odd') ? 'even':'odd';	 
	// if($p['hasanswers']==0){$hasanswers = "No";} else {$hasanswers = "Yes";}
	 $addedby = $this->objUser->fullname($p['userid']);
	 $answerexists = $this->objDbanswers->hasAnswers($p['id']);
	
	$downloadlink = new link();
	$downloadlink->link = $p['filename'];
	$root = $downloadlink->href .= str_replace('\\', '/',$this->objConfig->getcontentRoot());
	$downloadlink->href = $root."content/".$contextCode."/papers/".$p['filename'];	
	
	$answeraddlink = new link($this->uri(array('action'=>'addanswers','paperid'=>$p['id'])));
	$answeraddlink->link = $this->objLanguage->languageText('mod_pastpapers_addanswers','pastpapers');
	
	$viewlink = new link($this->uri(array('action'=>'viewanswers','paperid'=>$p['id'])));
	$viewlink->link = $this->objLanguage->languageText('mod_pastpapers_viewanswers','pastpapers'); 
	
	
	
	
	$table->startRow();	
	$table->addCell($downloadlink->show(),'','','left',$class);
	$table->addCell($p['topic'],'','','left',$class);
	$table->addCell($p['examyear'],'','','left',$class);	
	$table->addCell($answerexists,'','','left',$class);
	$table->addCell($addedby,'','','left',$class);
	$table->addCell($p['dateuploaded'],'','','left',$class);
	
	
	if($contextCode){
	  if($this->objUser->isCourseAdmin() || $this->objDbanswers->hasAnswers($p['id'])=='yes'){
	  
	  $action = $answeraddlink->show();
	
		$view = $viewlink->show();
		
		$view = "";
	 
	  $table->addCell($action."|".$view,'','','left',$class);
	  
	  }
	
	
	}
	else {	
	
	//not in context but user is the site admin or author
	 if($this->objUser->isAdmin() ||  $this->objPastpapers->getPaperAuthor($p['id'])== $this->objUser->userId()){
	   
		 $action = $answeraddlink->show();		 
		 
		if($this->objDbanswers->hasAnswers($p['id'])=='yes'){
		$view = $viewlink->show();}
		
		else  {$view = "";}
	   $table->addCell($action."|".$view,'','','left',$class);
	   
	   }
	   
else {
	   	$action = $this->objLanguage->languageText('mod_pastpapers_cannotadd','pastpapers');

	   if($this->objDbanswers->hasAnswers($p['id'])=='yes'){
			$view = $viewlink->show();
		
		}
		
		else  {
		   $view = "";

		}
	 
	  $table->addCell($action."|".$view,'','','left',$class);
	   
	   }
	
	
	}
	
	//link for adding a delete 
	$link = new link();
	$objIcon->setIcon('delete');
    $link->link = $objIcon->show();
    $link->href = $this->uri(array('action' => 'deletepaper','id' => $p['id']));
	  if($this->objUser->isAdmin() ||  $this->objPastpapers->getPaperAuthor($p['id'])== $this->objUser->userId()){
	 $table->addCell($link->show(),'','','left',$class);
	 }
	 
	$table->endRow();
	
}

}
$content .= $heading->show();

$content .= $table->show();

//add a link for viewing the other pastpapers outside the context
$viewothers = new link($this->uri(array('action'=>'otherpapers')));
$viewothers->link = $this->objLanguage->languageText('mod_pastpapers_otherpapers','pastpapers');

$content .= "<br/>".$viewothers->show();
echo $content;
?>
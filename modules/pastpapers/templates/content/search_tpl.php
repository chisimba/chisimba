<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* 
* @ Author Nsabagwa Mary
**/
$option = $this->getParam('search',NULL);
$searchoption = $this->getParam('searchoption',NULL);
$this->loadClass('link','htmlelements');
$this->objPastpapers = & $this->getObject('pastpaper');
$heading = $this->getObject('htmlheading','htmlelements');
$heading->str = $this->objLanguage->languageText('mod_pastpapers_results','pastpapers')."&nbsp;".$option;
$heading->align = "center";


echo $heading->show();

$this->loadClass('htmltable','htmlelements');
$paperlist = $this->pastpaper->searchforpapers($option,$searchoption );
	$table = new htmltable();
	$table->cellspacing= 2;
if(!empty($paperlist)){

    
	 
	$table->startRow();
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_fileshare_filename','fileshare')."<b/>",'','','left');	
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_topic','pastpapers')."<b/>",'','','left');		
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_examtime','pastpapers')."<b/>",'','','left');		
    $table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_hasanswers','pastpapers')."<b/>",'','','left');	
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_addedby','pastpapers')."<b/>",'','','left');	
	$table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_dateadded','pastpapers')."<b/>",'','','left');
 
    $table->addHeaderCell("<b/>".$this->objLanguage->languageText('mod_pastpapers_answers','pastpapers')."<b/>",'','','left');
$table->endRow();
	
	foreach($paperlist as $p){
	
	$downloadlink = new link();
	$downloadlink->link = $p['filename'];
	$root = $downloadlink->href .= str_replace('\\', '/',$this->objConfig->getcontentRoot());
	$downloadlink->href = $root."content/".$contextCode."papers/".$p['filename'];	
	
	$answeraddlink = new link($this->uri(array('action'=>'addanswers','paperid'=>$p['id'])));
	$answeraddlink->link = $this->objLanguage->languageText('mod_pastpapers_addanswers','pastpapers');
	
	$viewlink = new link($this->uri(array('action'=>'viewanswers','paperid'=>$p['id'])));
	$viewlink->link = $this->objLanguage->languageText('mod_pastpapers_viewanswers','pastpapers');
	
    $answerexists = $this->objDbanswers->hasAnswers($p['id']);
	
	 $class = ($class == 'odd') ? 'even':'odd';	 
	 $addedby = $this->objUser->fullname($p['userid']);
	
	
	$table->startRow();
	$table->addCell($downloadlink->show(),'','','left',$class);
	$table->addCell($p['topic'],'','','left',$class);
	$table->addCell($p['examyear'],'','','left',$class);	
	$table->addCell($answerexists,'','','left',$class);
	$table->addCell($addedby,'','','left',$class);
	$table->addCell($p['dateuploaded'],'','','left',$class);
	 if (!($this->objUser->isCourseAdmin()) && $this->objPastpapers->allCanAddAnswers($contextCode,$p['id'])==0){  
        $action = $this->objLanguage->languageText('mod_pastpapers_cannotadd','pastpapers');
		if($this->objDbanswers->hasAnswers($p['id'])=='yes'){
		$view = $viewlink->show();}
		
		else $view = "";		
	   $table->addCell($action."|".$view,'','','left',$class);
	
	}
	
	else {
	$action = $answeraddlink->show();
	if($this->objDbanswers->hasAnswers($p['id'])=='yes'){
		$view = $viewlink->show();}
		
		else $view = "";
	 
	  $table->addCell($action."|".$view,'','','left',$class);
	}
	$table->endRow();
		
		
		
		
	}
	
}

else {
 
	$table->startRow();
	$table->addCell($this->objLanguage->languageText('mod_pastpapers_noresults','pastpapers')."&nbsp;<b>".$option."</b>");
	$table->endRow();

}

echo $table->show();


?>
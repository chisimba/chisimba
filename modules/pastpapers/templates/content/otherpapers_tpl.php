<?php 
//template that displays a list of the pastpapers outside the context in which user is
$this->pastpapers = $this->getObject('pastpaper');
$this->_objDBContext->getContextCode();
$heading = $this->getObject('htmlheading','htmlelements');
$heading->align = "center";
$content = "";
$this->objLanguage= $this->getObject('language','language');
$this->loadClass('htmltable','htmlelements');
$addicon =$this->getObject('geticon','htmlelements');
$table = $this->getObject('htmltable','htmlelements');

//include he link class
$this->loadClass('link','htmlelements');
$table->width = "90%";
$table->align = "center";
$table->cellspacing =2;
 
$contextCode  = $this->_objDBContext->getContextCode();
$contextName = $this->_objDBContext->getTitle($contextCode);
  
//check if there are some past papers in the database and if there are some, display a list 
$past_papers = $this->pastpapers->getotherpapers($contextCode);  

if(!empty($past_papers)){  
$heading->str = $this->objLanguage->languageText('mod_pastpapers_listofotherpapers','pastpapers');

//begin collecting one by one row 
    $table->startRow();
	$table->addHeaderCell("<b>".$this->objLanguage->languageText('mod_context_context','context')."</b>",'','','left');	

	$table->addHeaderCell("<b>".$this->objLanguage->languageText('mod_fileshare_filename','fileshare')."</b>",'','','left');	
		$table->addHeaderCell("<b>".$this->objLanguage->languageText('mod_pastpapers_examtime','pastpapers')."</b>",'','','left');		

    $table->addHeaderCell("<b>".$this->objLanguage->languageText('mod_pastpapers_hasanswers','pastpapers')."</b>",'','','left');	
	$table->addHeaderCell("<b>".$this->objLanguage->languageText('mod_pastpapers_addedby','pastpapers')."</b>",'','','left');	
	$table->addHeaderCell("<b>".$this->objLanguage->languageText('mod_pastpapers_dateadded','pastpapers')."</b>",'','','left');	
	$table->endRow();
	
	
	$class = 'even';
	foreach($past_papers as $p){
	 $class = ($class == 'odd') ? 'even':'odd';
	 
	 if($p['hasanswers']==0){$hasanswers = "No";} else {$hasanswers = "Yes";}
	 $addedby = $this->objUser->fullname($p['userid']);	
		
		
	$downloadlink = new link();
	$downloadlink->link = $p['filename'];
	
	if($p['contextcode']){
	  $downloadlink->href = str_replace('\\', '/',$this->objConfig->getcontentRoot())."content/".$contextCode."/papers/".$p['filename'];	
	}
	
	else 	
	   	$downloadlink->href = str_replace('\\', '/',$this->objConfig->getcontentRoot())."content/papers/".$p['filename'];	
		
	if($p['contextcode']){
	  $contextname = $this->_objDBContext->getTitle($p['contextcode']);
	}
	
	else 
	   $contextname = $this->objLanguage->languageText('mod_pastpapers_lobby','pastpapers');
	
	
	$table->startRow();	
	
	$table->addCell($contextname,'','','left',$class);
	
	$table->addCell($downloadlink->show(),'','','left',$class);
	$table->addCell($p['examyear'],'','','left',$class);	
	$table->addCell($hasanswers,'','','left',$class);
	$table->addCell($addedby,'','','left',$class);
	$table->addCell($p['dateuploaded'],'','','left',$class);
	$table->endRow();	
	}

}
else {
 $heading->str = $this->objLanguage->languageText('mod_pastpapers_nopapersforothers','pastpapers');
 
    $table->startRow();
	$table->addCell("",'','','left');	
	$table->endRow();
}

$content .= $heading->show();

$content .= $table->show();

//add a link for viewing the other pastpapers outside the context
$viewothers = new link($this->uri(array('action'=>NULL)));
$viewothers->link = $contextName ;

$content .= "<br/>".$viewothers->show();
echo $content;

?>
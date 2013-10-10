<?php

/* -------------------- rubric class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Class for general functions within rubric
* @author Paul Mungai
* @copyright (c) 2009 UoN
* @package rubric
* @version 0.1
*/

class functions_rubric extends object
{

    public function init()
    {
     $this->objWashout = $this->getObject('washout','utilities');
					$this->loadClass('htmlheading', 'htmlelements');
					$this->loadClass('link', 'htmlelements');
     //$objLanguage = &$this->getObject('language', 'language');
     $this->objIcon= $this->newObject('geticon','htmlelements');
					$objPopup=&$this->loadClass('windowpop','htmlelements');
					$this->objLanguage =& $this->getObject('language','language');
					$this->objDbRubricTables =& $this->getObject('dbrubrictables','rubric'); 
					$this->objDbRubricPerformances =& $this->getObject('dbrubricperformances','rubric'); 
					$this->objDbRubricObjectives =& $this->getObject('dbrubricobjectives','rubric'); 
					$this->objDbRubricCells =& $this->getObject('dbrubriccells','rubric'); 
					$this->objDbRubricAssessments =& $this->getObject('dbrubricassessments','rubric'); 
    }
    
    /**
     * 
     *Method to output student rubrics
     *@param string $contextCode
     * @return array
     *example: displayrubric($contextCode, $userId, $uriModule='eportfolio', $assessmentAction='assessments', $viewTableAction='viewtable'); 
     */
    public function displayrubric($contextCode, $userId=Null, $uriModule, $assessmentAction, $viewTableAction)
    { 
     	$tables = $this->objDbRubricTables->listAll($contextCode, $contextCode == 'root' ? $userId : NULL);
     	if(!empty($tables)){
      	if ($this->contextCode != 'root') {
      		$pdtables = $this->objDbRubricTables->listAll("root", $userId);
     	}
      // Load needed classes
      $this->loadClass('link', 'htmlelements');
      $tblclassB = $this->newObject('htmltable','htmlelements');
      $tblclassB->width='100%';
      $tblclassB->border='0';
      $tblclassB->cellspacing='0';
      $tblclassB->cellpadding='5';	
        
      $tblclassB->startHeaderRow();
      $tblclassB->addHeaderCell($this->objLanguage->languageText('word_title'), '30%');
      $tblclassB->addHeaderCell($this->objLanguage->languageText('rubric_description','rubric'), '53%');
      $tblclassB->addHeaderCell($this->objLanguage->languageText('word_view'), '17%');
      $tblclassB->endHeaderRow();    
	
      // Display tables.	
      $oddOrEven = "odd";
     	foreach ($tables as $table) {        
        $tblclassB->startRow();
        $oddOrEven = ($oddOrEven=="even")? "odd":"even";		    
        $tblclassB->addCell($table['title'], "null", "top", "left", $oddOrEven, null);
        $tblclassB->addCell($table['description'], "null", "top", "left", $oddOrEven, null);		
        
        // Start of Rubric Options
        $options = NULL;
        
        if ($contextCode != "root") {
       		$this->objIcon->title=$this->objLanguage->languageText("word_view")."&nbsp;".$this->objLanguage->languageText("word_assessments","rubric");
       		$this->objIcon->setIcon('assessments');
       		$commentIconA = $this->objIcon->show();
 
	       	$objPopupA = new windowpop();
       		$objPopupA->set('location',$this->uri(array('action' => $assessmentAction,'tableId'=>$table['id'],'studentId' => $userId),$uriModule));
       		$objPopupA->set('linktext',$commentIconA);
       		$objPopupA->set('width','600');
       		$objPopupA->set('height','150');
       		$objPopupA->set('left','200');
       		$objPopupA->set('top','200');
       		$objPopupA->set('scrollbars','yes');
       		$objPopupA->set('resizable','yes');
       		$objPopupA->putJs(); // you only need to do this once per page

      		}
	        // View table.

       	$this->objIcon->title=$this->objLanguage->languageText("word_view")."&nbsp;".$this->objLanguage->languageText("rubric_rubric","rubric");
       	$this->objIcon->setIcon('comment_view');
       	$commentIconB = $this->objIcon->show();

       	$objPopupB = new windowpop();
       	$objPopupB->set('location',$this->uri(array('action' => $viewTableAction,'tableId'=>$table['id'],'studentId' => $userId),$uriModule));
       	$objPopupB->set('linktext',$commentIconB);
       	$objPopupB->set('width','600');
       	$objPopupB->set('height','150');
       	$objPopupB->set('left','200');
       	$objPopupB->set('top','200');
       	$objPopupB->set('scrollbars','yes');
       	$objPopupB->set('resizable','yes');
        if ($contextCode != "root") {
         $tblclassB->addCell($objPopupA->show().$objPopupB->show(), "null", "top", "left", $oddOrEven, null);
       	}else{
       	 $tblclassB->addCell($objPopupB->show(), "null", "top", "left", $oddOrEven, null);
       	}
         $tblclassB->endRow();

	       }
    if (empty($tables)) {        

        $tblclassB->startRow();        
        $tblclassB->addCell("<div class=\"noRecordsMessage\">" . $this->objLanguage->languageText('mod_rubric_norecords','rubric') . "</div>", "null", "top", "left", "", 'colspan="3"');
        $tblclassB->endRow();
    }

    //Show predefined rubrics if any	
    if (!empty($pdtables)) {
	$pageTitle = $this->newObject('htmlheading','htmlelements');
	$pageTitle->type=3;
	$pageTitle->align='left';
	$pageTitle->str=$this->objLanguage->languageText('rubric_rubrics','rubric');
    
        $tblclassC=$this->newObject('htmltable','htmlelements');
        $tblclassC->width='100%';
        $tblclassC->border='0';
        $tblclassC->cellspacing='1';
        $tblclassC->cellpadding='5';    
    
        $tblclassC->startRow();
    	
        $tblclassC->addCell($pageTitle->show(), "null", "top", "left", "",null);
        $tblclassC->endRow();

        $tblclassD = $this->newObject('htmltable','htmlelements');
        $tblclassD->width='100%';
        $tblclassD->border='0';
        $tblclassD->cellspacing='1';
        $tblclassD->cellpadding='5';	
        
        $tblclassD->startHeaderRow();
        $tblclassD->addHeaderCell($this->objLanguage->languageText('word_title'), 60);
        $tblclassD->addHeaderCell($this->objLanguage->languageText('rubric_description','rubric'), 60);
        $tblclassD->addHeaderCell("&nbsp;", 20);    
        $tblclassD->endHeaderRow();        
        
        $oddOrEven = "odd";
        if (isset($pdtables)) {
        foreach ($pdtables as $pdtable) {
            $tblclassD->startRow();
            $oddOrEven = ($oddOrEven=="even")? "odd":"even";
        
            $tblclassD->addCell("<b>" . $pdtable['title'] . "</b>", "null", "top", "left", $oddOrEven, null);
            $tblclassD->addCell("<b>" . $pdtable['description'] . "</b>", "null", "top", "left", $oddOrEven, null);        
       	    $this->objIcon->title=$this->objLanguage->languageText("word_view")."&nbsp;".$this->objLanguage->languageText("rubric_rubric","rubric");
      	     $this->objIcon->setIcon('comment_view');
       	    $commentIconC = $this->objIcon->show();

       	    $objPopupC = new windowpop();
       	    $objPopupC->set('location',$this->uri(array('action' => $viewTableAction,'tableId'=>$pdtable['id'],'studentId' => $userId),$uriModule));
       	    $objPopupC->set('linktext',$commentIconC);
       	    $objPopupC->set('width','600');
       	    $objPopupC->set('height','150');
       	    $objPopupC->set('left','200');
       	    $objPopupC->set('top','200');
       	    $objPopupC->set('scrollbars','yes');
       	    $objPopupC->set('resizable','yes');

            $tblclassD->addCell($objPopupC->show(), "null", "top", "left", $oddOrEven, null);
            $tblclassD->endRow();
         }
       }
    
      if (empty($pdtables)) {
        $tblclassD->startRow();       
        $tblclassD->addCell("<div class=\"noRecordsMessage\">" . $this->objLanguage->languageText('mod_rubric_norecords','rubric') . "</div>", "null", "top", "left", "", 'colspan="3"');
        $tblclassD->endRow();
      }
       return $tblclassB->show().$tblclassC->show().$tblclassD->show();
      }else{
       return $tblclassB->show();
      }
      }else{
       return False;
    }
   }    
    /**
     * 
     *Method to output student rubrics
     *@param string $contextCode
     * @return array
     *example: displayrubric($contextCode, $userId, $uriModule='eportfolio', $assessmentAction='assessments', $viewTableAction='viewtable'); 
     */
    public function displayrubricFull($contextCode, $userId=Null, $uriModule, $assessmentAction, $viewTableAction)
    { 
					$tables = $this->objDbRubricTables->listAll($contextCode, $contextCode == 'root' ? $userId : NULL);
					if(!empty($tables)){
							if ($contextCode != 'root') {
								$pdtables = $this->objDbRubricTables->listAll("root", $userId);
							}
						// Load needed class
						$this->loadClass('link', 'htmlelements');
						$tblclassB = $this->newObject('htmltable','htmlelements');
						$tblclassB->width=500;
						$tblclassB->border=1;
						$tblclassB->cellspacing=1;
						$tblclassB->cellpadding='0';	

						// Display tables.	
						$oddOrEven = "odd";
						foreach ($tables as $table) {        
						// Start of Rubric Options
							$options = NULL;

							if ($contextCode != "root") {
								$tableInfo = $this->objDbRubricTables->listSingle($table['id']);
								$title = $tableInfo[0]['title'];
								$description = $tableInfo[0]['description'];
								$rows = $tableInfo[0]['rows'];
								$cols = $tableInfo[0]['cols'];
								$maxtotal=$cols*$rows;
								$assessments = $this->objDbRubricAssessments->listAll($table['id']);
								//Do we want to show student names?
								$showStudentNames = 'yes';
								//Get Objects
								$pageTitle = $this->newObject('htmlheading','htmlelements');
								$pageTitle->type=3;
								$pageTitle->align='left';
								$pageTitle->str=$this->objLanguage->languageText('rubric_rubric','rubric')." : " . $title;

								// Show Title
								//$fststr = $pageTitle->show();
								$fststr = "";
								$tblclass = $this->newObject('htmltable','htmlelements');
								$tblclass->width=490;
								$tblclass->border=1;
								$tblclass->cellspacing=1;
								$tblclass->cellpadding=1;	
								$oddOrEven = "odd";
								//Title
								$fstitle = "<b>".$this->objLanguage->languageText('rubric_rubric','rubric')." : " . $title."</b>";	
								$tblclass->startRow();
								$tblclass->addCell($fstitle,"490","","","","bgcolor='#D3D3D3' colspan=2");
								$tblclass->endRow();
								//Description
								$tblclass->startRow();
								$tblclass->addCell($description,"490","","","","bgcolor='#FFFFFF' colspan=2");
								$tblclass->endRow();

								foreach ($assessments as $assessment) {
									$oddOrEven = ($oddOrEven=="even")? "odd":"even";
									$tblclass->startRow();
									$tblclass->addCell("<b>".$this->objLanguage->languageText('rubric_name','rubric').": </b>","90","","","","bgcolor='#D3D3D3'");
									$tblclass->addCell($assessment['studentno'],"400","","","","bgcolor='#FFFFFF'");
									$tblclass->endRow();


									$scores = explode(",", $assessment['scores']);
									$total = 0;
									foreach ($scores as $score) {
									$total += $score;
									}
									if ($total==0 || $maxtotal == 0){
										$tblclass->startRow();
										$tblclass->addCell("<b>" . "" . "</b>","490","","","","colspan=2");
										$tblclass->endRow();
									}else{
										$tblclass->startRow();
										$tblclass->addCell("<b>".$this->objLanguage->languageText('rubric_score','rubric').": </b>","90","","","","bgcolor='#D3D3D3'");
										$tblclass->addCell("$total/$maxtotal","400","","","","");
										$tblclass->endRow();
										$tblclass->startRow();
										$tblclass->addCell("<b>".$this->objLanguage->languageText('rubric_score','rubric').": </b>","90","","","","bgcolor='#D3D3D3'");
										$tblclass->addCell("<b>".$this->objLanguage->languageText('rubric_score','rubric').": </b>" . "$total/$maxtotal","400","","","","bgcolor='#FFFFFF'");
										$tblclass->endRow();
									}
									$tblclass->startRow();
									$tblclass->addCell("<b>".ucfirst($this->objLanguage->code2Txt('rubric_teacher','rubric')).": </b>","90","","","","bgcolor='#D3D3D3'");
									$tblclass->addCell($assessment['teacher'],"400","","","","bgcolor='#FFFFFF'");	
									$tblclass->endRow();
									$tblclass->startRow();
									$tblclass->addCell("<b>".$this->objLanguage->languageText('rubric_date','rubric').": </b>".$assessment['timestamp'],"90","","","","bgcolor='#D3D3D3'");
									$tblclass->addCell("<b>".$this->objLanguage->languageText('rubric_date','rubric').": </b>".$assessment['timestamp'],"400","","","","bgcolor='#FFFFFF'");
									$tblclass->endRow();									
								}								
							}
							// View table.
							$tableInfo = $this->objDbRubricTables->listSingle($table['id']);
							$title = $tableInfo[0]['title'];
							$description = $tableInfo[0]['description'];
							$rows = $tableInfo[0]['rows'];
							$cols = $tableInfo[0]['cols'];
							// Build the performances array
							$performances = array();
							for ($j=0;$j<$cols;$j++) {
								$performance = $this->objDbRubricPerformances->listSingle($table['id'], $j);
								if(!empty($performance))
 								$performances[] = $performance[0]['performance'];
							}				
							// Build the objectives array
							$objectives = array();
							for ($i=0;$i<$rows;$i++) {
								$objective = $this->objDbRubricObjectives->listSingle($table['id'], $i);
							if(!empty($objective))
								$objectives[] = $objective[0]['objective'];
							}
							// Build the cells matrix
							$cells = array();
							for ($i=0;$i<$rows;$i++) {
								$cells[$i] = array();
								for ($j=0;$j<$cols;$j++) {
									$cell = $this->objDbRubricCells->listSingle($table['id'], $i, $j);
									if(!empty($cell))
									 $cells[$i][$j] = $cell[0]['contents'];
								}
							}

							$pageTitle = $this->newObject('htmlheading','htmlelements');
							$pageTitle->type=3;
							$pageTitle->align='left';
							$pageTitle->str=$this->objLanguage->languageText('rubric_rubric','rubric') . " : " . $title ;
							//$sndstr = $pageTitle->show();
							$sndstr = "";
							/*
							$sndstr = "<b>".$this->objLanguage->languageText('rubric_rubric','rubric') . " : " . $title ."</b><br />";

							$labelDescription = $description."<br />";

							$sndstr .= $labelDescription;
							*/
							// If this is an assessment then display details.
							if (isset($IsAssessment)) {
								$objTable =& $this->newObject('htmltable','htmlelements');
								$objTable->border = 1;
								$objTable->width=500;        
								$objTable->cellspacing=1;
								$objTable->cellpadding='0';

								$objTable->startRow();
								$objTable->addCell("<b>".ucfirst($this->objLanguage->code2Txt("rubric_teacher","rubric"))."</b>","100","","","","");
								$objTable->addCell($teacher,"400","","","","");
								$objTable->endRow();
								$objTable->startRow();
								$objTable->addCell("<b>".ucfirst($this->objLanguage->code2Txt("rubric_studentno","rubric"))."</b>","100","","","","");
								$objTable->addCell($studentNo,"100","","","","");
								$objTable->endRow();
								$objTable->startRow();
								$objTable->addCell("<b>".ucfirst($this->objLanguage->code2Txt("rubric_student","rubric"))."</b>","100","","","","");
								$objTable->addCell($student,"400","","","","");
								$objTable->endRow();
								$objTable->startRow();
								$objTable->addCell("<b>".$this->objLanguage->languageText("rubric_datesubmitted","rubric")."</b>","100","","","","");
								$objTable->addCell($date,"400","","","","");
								$objTable->endRow();
							}
							$mytable =& $this->newObject("htmltable","htmlelements");
							$mytable->border = 1;
							$mytable->width = 490;	
							$mytable->cellspacing=1;
							$mytable->cellpadding='0'; 

							//Title
							$sndtitle = "<b>".$this->objLanguage->languageText('rubric_rubric','rubric') . " : " . $title ."</b>";
							$mytable->startRow();
							$mytable->addCell($sndtitle,"490","","","","bgcolor='#D3D3D3' colspan=2");
							$mytable->endRow();							
							//Description
							$mytable->startRow();
							$mytable->addCell($description,"490","","","","bgcolor='#FFFFFF' colspan=2");
							$mytable->endRow();							
							
							//Get Rubric
							$tableInfo = $this->objDbRubricTables->listSingle($table['id']);
							$title = $tableInfo[0]['title'];
							$description = $tableInfo[0]['description'];
							$rows = $tableInfo[0]['rows'];
							$cols = $tableInfo[0]['cols'];
							// Build the performances array
							$performances = array();
							for ($j=0;$j<$cols;$j++) {
								$performance = $this->objDbRubricPerformances->listSingle($table['id'], $j);
							if(!empty($performance))
								$performances[] = $performance[0]['performance'];
							}				
							// Build the objectives array
							$objectives = array();
							for ($i=0;$i<$rows;$i++) {
								$objective = $this->objDbRubricObjectives->listSingle($table['id'], $i);
							if(!empty($objective))
								$objectives[] = $objective[0]['objective'];
							}
							// Build the cells matrix
							$cells = array();
							for ($i=0;$i<$rows;$i++) {
								$cells[$i] = array();
								for ($j=0;$j<$cols;$j++) {
									$cell = $this->objDbRubricCells->listSingle($table['id'], $i, $j);
							if(!empty($cell))
									$cells[$i][$j] = $cell[0]['contents'];
								}
							}

							$mytable->startRow();
							$mytable->addCell("<b>".$this->objLanguage->languageText("word_objectives","rubric")."</b>","490","","","","bgcolor='#D3D3D3' colspan=2");
							$mytable->endRow();							

							// Display performances.
							if(!empty($performances[$j])){
								for ($j=0;$j<$cols;$j++) {
									if(!empty($performances[$j])){
										$mytable->startRow();
										$mytable->addCell("<b>".$performances[$j]."</b>","490","","","","colspan=2");
										$mytable->endRow();							
									}	
								}
							}
							if (isset($IsAssessment)) {
								$mytable->startRow();
								$mytable->addCell("<b>Score</b>","490","","","","colspan=2");
								$mytable->endRow();
							}

							for ($i=0;$i<$rows;$i++) {
								// Display objective.
								if(!empty($objectives[$i])){
									$mytable->startRow();
									$mytable->addCell($objectives[$i],"490","","","","colspan=2");
									$mytable->endRow();
								}
								for ($j=0;$j<$cols;$j++) {
									if(!empty($cells[$i][$j])){
										$mytable->startRow();
										$mytable->addCell($cells[$i][$j],"490","","","","colspan=2");
										$mytable->endRow();
									}
								}
								if (isset($IsAssessment) && !empty($scores[$i])) {
									$mytable->startRow();
									$mytable->addCell($scores[$i],"490","","","","colspan=2");
									$mytable->endRow();
								}
							}
							// If this is an assessment display the total score.
							if (isset($IsAssessment)) {
								/*
								for ($j=0;$j<($cols-1);$j++) {
								 //Do nothing
								}*/
									$mytable->startRow();
									$mytable->addCell($this->objLanguage->languageText("rubric_total","rubric") . "&nbsp;","490","","","","bgcolor='#D3D3D3' colspan=2");
									$mytable->endRow();
								if ($total==0 || $maxtotal == 0){
								}else{
									$mytable->startRow();
								 $mytable->addCell($total/$maxtotal,"490","","","","colspan=2");
									$mytable->endRow();
								}	
							}

							$sndstr .= $mytable->show();
							// If this is an assessment then display details.
							if (isset($IsAssessment)) {
								$sndstr .= $objTable->show();
							}
							if ($contextCode != "root") {

								$tblclassB->startRow();
								$tblclassB->addCell($fststr.$tblclass->show(),"500","","","","colspan=2");
								$tblclassB->endRow();

								$tblclassB->startRow();
								$tblclassB->addCell($sndstr,"500","","","","colspan=2");
								$tblclassB->endRow();

							}else{
								$tblclassB->startRow();
								$tblclassB->addCell($sndstr,"500","","","","colspan=2");
								$tblclassB->endRow();
							}
						}
						if (empty($tables)) {        

						$tblclassB->startRow();        
						$tblclassB->addCell("<div class=\"noRecordsMessage\">" . $this->objLanguage->languageText('mod_rubric_norecords','rubric') . "</div>","500","","","","colspan=2");
						$tblclassB->endRow();
						}

						//Show predefined rubrics if any	
						if (!empty($pdtables)) {
						$pageTitle = $this->newObject('htmlheading','htmlelements');
						$pageTitle->type=3;
						$pageTitle->align='left';
						$pageTitle->str=$this->objLanguage->languageText('rubric_rubrics','rubric');
/*
						$tblclassC=$this->newObject('htmltable','htmlelements');
						$tblclassC->width=500;
						$tblclassC->border=1;
						$tblclassC->cellspacing=1;
						$tblclassC->cellpadding='0';    

						$tblclassC->startRow();

						$tblclassC->addCell($pageTitle->show());
						$tblclassC->endRow();
*/
						$tblclassD = $this->newObject('htmltable','htmlelements');
						$tblclassD->width=500;
						$tblclassD->border='1';
						$tblclassD->cellspacing=1;
						$tblclassD->cellpadding='0';	
						if (isset($pdtables)) {
						foreach ($pdtables as $pdtable) {
/*
							$tblclassD->startRow();
							$tblclassD->addCell("<b>".$this->objLanguage->languageText('word_title').": ". "</b>","100","","","", "bgcolor='#D3D3D3'");
							$tblclassD->addCell($pdtable['title'],"400","","","", "bgcolor='#FFFFFF'");
						$tblclassD->endRow();
							$tblclassD->startRow();						
							$tblclassD->addCell("<b>" .$this->objLanguage->languageText('rubric_description','rubric').": ". "</b>","100","","","", "bgcolor='#FFFFFF'");
							$tblclassD->addCell($pdtable['description'] ,"400","","","", "bgcolor='#FFFFFF'");
							$tblclassD->endRow();
*/						
							$tableInfo = $this->objDbRubricTables->listSingle($pdtable['id']);
							$title = $tableInfo[0]['title'];
							$description = $tableInfo[0]['description'];
							$rows = $tableInfo[0]['rows'];
							$cols = $tableInfo[0]['cols'];
							// Build the performances array
							$performances = array();
							for ($j=0;$j<$cols;$j++) {
								$performance = $this->objDbRubricPerformances->listSingle($pdtable['id'], $j);
								$performances[] = $performance[0]['performance'];
							}				
							// Build the objectives array
							$objectives = array();
							for ($i=0;$i<$rows;$i++) {
								$objective = $this->objDbRubricObjectives->listSingle($pdtable['id'], $i);
								$objectives[] = $objective[0]['objective'];
							}
							// Build the cells matrix
							$cells = array();
							for ($i=0;$i<$rows;$i++) {
								$cells[$i] = array();
								for ($j=0;$j<$cols;$j++) {
									$cell = $this->objDbRubricCells->listSingle($pdtable['id'], $i, $j);
									$cells[$i][$j] = $cell[0]['contents'];
								}
							}

							$pageTitle = $this->newObject('htmlheading','htmlelements');
							$pageTitle->type=3;
							$pageTitle->align='left';
							$pageTitle->str=$this->objLanguage->languageText('rubric_rubric','rubric') . " : " . $title ;
							//$thrdstr = $pageTitle->show();
							$thrdstr = "";
							/*
							$thrdstr = "<b>".$this->objLanguage->languageText('rubric_rubric','rubric') . " : " . $title."</b><br />";
							$labelDescription = "" . $description . "<br />";
							$thrdstr .= $labelDescription;
							*/
							// If this is an assessment then display details.
							if (isset($IsAssessment)) {
								$objTable =& $this->newObject('htmltable','htmlelements');
								$objTable->border = 1;
								$objTable->width=490;        
								$objTable->cellspacing=1;
								$objTable->cellpadding='0';

								$objTable->startRow();
								$objTable->addCell("<b>".ucfirst($this->objLanguage->code2Txt("rubric_teacher","rubric"))."</b>","90","","","", "bgcolor='#D3D3D3'");
								$objTable->addCell($teacher,"400","","","", "bgcolor='#FFFFFF'");
								$objTable->endRow();
								$objTable->startRow();
								$objTable->addCell("<b>".ucfirst($this->objLanguage->code2Txt("rubric_studentno","rubric"))."</b>","90","","","", "bgcolor='#D3D3D3'");
								$objTable->addCell($studentNo,"400","","","", "bgcolor='#FFFFFF'");
								$objTable->endRow();
								$objTable->startRow();
								$objTable->addCell("<b>".ucfirst($this->objLanguage->code2Txt("rubric_student","rubric"))."</b>","90","","","", "bgcolor='#D3D3D3'");
								$objTable->addCell($student,"400","","","", "bgcolor='#FFFFFF'");
								$objTable->endRow();
								$objTable->startRow();
								$objTable->addCell("<b>".$this->objLanguage->languageText("rubric_datesubmitted","rubric")."</b>","90","","","", "bgcolor='#D3D3D3'");
								$objTable->addCell($date,"400","","","", "bgcolor='#FFFFFF'");
								$objTable->endRow();
							}
							$ftable =& $this->newObject("htmltable","htmlelements");
							$ftable->border = 1;
							$ftable->width = 490;	
							$ftable->cellspacing = 1;
							
								$ftable->startRow();
								$ftable->addCell("<b>".$this->objLanguage->languageText('rubric_rubric','rubric') . " : ".$title." </b>","490","","","","bgcolor='#D3D3D3' colspan=2");
								$ftable->endRow();
								$ftable->startRow();
								$ftable->addCell($description,"490","","","", "bgcolor='#FFFFFF' colspan=2");
								$ftable->endRow();
							//$class = 'odd';
							for ($i=0;$i<$rows;$i++) {
								// Display objective.
								$ftable->startRow();
								$ftable->addCell("<b>".$this->objLanguage->languageText("word_objectives","rubric")." </b>","490","","","", "bgcolor='#D3D3D3' colspan=2");
								$ftable->endRow();
								$ftable->startRow();
								$ftable->addCell($objectives[$i],"490","","","", "bgcolor='#FFFFFF' colspan=2 ");
								$ftable->endRow();
								// Display cells.								
								for ($j=0;$j<$cols;$j++) {
									$ftable->startRow();
									$ftable->addCell("<b>".$performances[$j]."</b>","490","","","", "bgcolor='#D3D3D3' colspan=2");
									$ftable->endRow();
									$ftable->startRow();
									$ftable->addCell($cells[$i][$j],"490","","","", "bgcolor='#FFFFFF' colspan=2");
									$ftable->endRow();
								}
								if (isset($IsAssessment)) {
								 $ftable->startRow();
									$ftable->addCell("<b>"."Score".": </b>","90","","","", "bgcolor='#D3D3D3'");
									$ftable->addCell($scores[$i],"400","","","", "bgcolor='#FFFFFF'");
								 $ftable->endRow();
								}


								$class = $class == 'odd' ? 'even' : 'odd';
							}
							// If this is an assessment display the total score.
							if (isset($IsAssessment)) {

								for ($j=0;$j<($cols-1);$j++) {

								}
								$ftable->startRow();
								$ftable->addCell($this->objLanguage->languageText("rubric_total","rubric") . "&nbsp;","490","","","", "bgcolor='#FFFFFF' colspan=2");
								$ftable->endRow();
								if ($total==0 || $maxtotal == 0){

								}else{
									$ftable->startRow();
									$ftable->addCell($total/$maxtotal,"490","","","", "bgcolor='#FFFFFF' colspan=2");
									$ftable->endRow();
								}

							}
							$thrdstr .= $ftable->show();
							// If this is an assessment then display details.
							if (isset($IsAssessment)) {
								$thrdstr .= $objTable->show();
							}
							$tblclassD->startRow();
							$tblclassD->addCell($thrdstr,"500","","","", "colspan=2");
							$tblclassD->endRow();
						}
						}

						if (empty($pdtables)) {
						$tblclassD->startRow();       
						$tblclassD->addCell("<div class=\"noRecordsMessage\">" . $this->objLanguage->languageText('mod_rubric_norecords','rubric') . "</div>", "500", "top", "left", "", 'colspan="2"');
						$tblclassD->endRow();
						}
						//return $ftable->show();
						return $tblclassB->show().$tblclassD->show();
						}else{
						return $tblclassB->show();
						}
					}else{
					return False;
					}
	}
}
?>

<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Class for general functions within worksheet
* @author Paul Mungai
* @copyright (c) 2009 UoN
* @package worksheet
* @version 0.1
*/

class functions_worksheet extends object
{

    public function init()
    {
    	$this->objWorksheet =& $this->getObject('dbworksheet', 'worksheet');	
    	$this->objWorksheetResults =& $this->getObject('dbworksheetresults', 'worksheet');	
	$this->loadClass('htmlheading', 'htmlelements');
	$this->loadClass('link', 'htmlelements');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objContext = &$this->newObject('dbcontext', 'context');
        $this->objIcon= $this->newObject('geticon','htmlelements');
        $this->objWorksheetQuestions = $this->getObject('dbworksheetquestions', 'worksheet');
        $this->objWorksheetAnswers = $this->getObject('dbworksheetanswers', 'worksheet');
        $this->objWorksheetResults = $this->getObject('dbworksheetresults', 'worksheet');   
        $this->objUser = &$this->getObject('user', 'security');             
	$objPopup=&$this->loadClass('windowpop','htmlelements');
    }
    
    /**
     * 
     *Method to output worksheets in context
     *@param string $contextCode
     * @return array
     */
    public function displayWorksheets($contextCode, $userId=Null)
    { 
	//Get worksheets
	$worksheets = $this->objWorksheet->getWorksheetsInContext($contextCode);

	$header = new htmlheading();
	$header->type = 3;
	$header->str = $this->objContext->getTitle($contextCode).': '.$this->objLanguage->languageText('mod_worksheet_worksheets', 'worksheet', 'Worksheets');
	//Load Table Object
	$table = $this->newObject('htmltable', 'htmlelements');

	if (count($worksheets) == 0) {
		return False;
	} else {
		$table->startHeaderRow();
		    $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_worksheetname', 'worksheet', 'Worksheet Name'),'20%');
		    $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions'),'13%');
		    $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date'),'15%');
		    $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_datecompleted', 'worksheet', 'Date Completed'),'15%');
		    $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_mark', 'worksheet', 'Mark'),'10%');
		    $table->addHeaderCell($this->objLanguage->languageText('word_view', 'worksheet', 'View'),'13%');
		$table->endHeaderRow();		
		foreach ($worksheets as $worksheet)
		{
		 $wkshtResults = $this->objWorksheetResults->getWorksheetResult($userId, $worksheet['id']);
			 if($wkshtResults['userid']==$userId){
			 if(!empty($wkshtResults['mark'])){
			   $theMark = round(($wkshtResults['mark']/$worksheet['total_mark']*100),2)." %";
			 }else{
			   $theMark = "";
			 }
			    $table->startRow();
				$link = new link ($this->uri(array('action'=>'worksheetinfo', 'id'=>$worksheet['id'])));
				$link->link = $worksheet['name'];
				if(!empty($theMark)){
					$this->objIcon->title=$this->objLanguage->languageText('word_view', 'worksheet', 'View');
				    	$this->objIcon->setIcon('comment_view');
				   	$commentIcon = $this->objIcon->show();

					$objPopup = new windowpop();
				    	$objPopup->set('location',$this->uri(array('action'=>'viewworksheet','id'=>$worksheet['id']),"eportfolio"));
				    	$objPopup->set('linktext',$commentIcon);
				    	$objPopup->set('width','800');
				    	$objPopup->set('height','400');
				    	$objPopup->set('left','200');
				    	$objPopup->set('top','200');
				    	$objPopup->set('scrollbars','yes');
				    	$objPopup->set('resizable','yes');
				    	$objPopup->putJs(); // you only need to do this once per page
				}
				
				$table->addCell($worksheet['name']);
				$table->addCell($worksheet['questions']);
				$table->addCell($worksheet['closing_date']);
				$table->addCell($wkshtResults['last_modified']);
				$table->addCell($theMark);
				if(!empty($theMark)){
				  $table->addCell('<p>'.$objPopup->show().'</p>');
				}else{
				  $table->addCell("&nbsp;");
				}
			    $table->endRow();
			}
		}
	    return $header->show().$table->show();		
     }
   }
    /**
     * 
     *Method to output worksheets in context
     *@param string $contextCode
     * @return array
     */
    public function displayWorksheetsFull($contextCode, $userId=Null)
    { 
					//Get worksheets
					$worksheets = $this->objWorksheet->getWorksheetsInContext($contextCode);

					$courseHeader = new htmlheading();
					$courseHeader->type = 3;
					$courseHeader->str = $this->objContext->getTitle($contextCode).': '.$this->objLanguage->languageText('mod_worksheet_worksheets', 'worksheet', 'Worksheets');
					//Load Table Object
					$mytable = $this->newObject('htmltable', 'htmlelements');
					$mytable->border = 1;
					$mytable->cellspacing = 1;
					$mytable->width = 500;					

					if (count($worksheets) == 0) {
						return False;
					} else {
						foreach ($worksheets as $worksheet)
					{
					$wkshtResults = $this->objWorksheetResults->getWorksheetResult($userId, $worksheet['id']);
					if($wkshtResults['userid']==$userId){
						if(!empty($wkshtResults['mark'])){
							$theMark = round(($wkshtResults['mark']/$worksheet['total_mark']*100),2)." %";
						}else{
							$theMark = "";
						}
						if(!empty($theMark)){
						$questions = $this->objWorksheetQuestions->getQuestions($worksheet['id']);

						$worksheetResult = $this->objWorksheetResults->getWorksheetResult($userId, $worksheet['id']);

						if ($worksheet['activity_status'] == 'open' && !$worksheetResult) {
						} else {
							$header = new htmlheading();
							$header->type = 4;
							$header->str = $this->objLanguage->languageText('mod_worksheet_worksheet', 'worksheet', 'Worksheet').': '.$worksheet['name'];
	
							$mytable->startRow();
							$mytable->addCell("<b>".$this->objLanguage->languageText('mod_worksheet_worksheet', 'worksheet', 'Worksheet').': '.$worksheet['name']."</b>","500","","","","bgcolor='#D3D3D3' colspan='2'");
							$mytable->endRow();
							//$firstStr = '<br />'.$header->show();

							//$firstStr .= $worksheet['description'];

							$mytable->startRow();
							$mytable->addCell($worksheet['description'],"500","","","","bgcolor='#FFFFFF' colspan='2'");
							$mytable->endRow();

							$objDateTime = $this->getObject('dateandtime', 'utilities');

							//			$table = $this->newObject('htmltable', 'htmlelements');
							//			$table->border = 1;
							//			$table->cellspacing = 1;
							$mytable->startRow();
							$mytable->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date').'</strong>: ',"100","","","","bgcolor='#D3D3D3'");
							$mytable->addCell($objDateTime->formatDate($worksheet['closing_date']),"400","","","","bgcolor='#FFFFFF'");
							$mytable->endRow();
							$mytable->startRow();
							$mytable->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions').'</strong>: ',"100","","","","bgcolor='#D3D3D3'");
							$mytable->addCell(count($questions),"400","","","","bgcolor='#FFFFFF'");
							$mytable->endRow();
							$mytable->startRow();
							$mytable->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_percentage', 'worksheet', 'Percentage').'</strong>: ',"100","","","","bgcolor='#D3D3D3'");
							$mytable->addCell($worksheet['percentage'].'%',"400","","","","bgcolor='#FFFFFF'");
							$mytable->endRow();
							$mytable->startRow();
							$mytable->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark').'</strong>: ',"100","","","","bgcolor='#D3D3D3'");
							$mytable->addCell($worksheet['total_mark'],"400","","","","bgcolor='#FFFFFF'");
							$mytable->endRow();
							/*
							$mytable->startRow();
							$mytable->addCell($table->show());
							$mytable->endRow();
							*/
							//$firstStr .= $table->show();

							//$firstStr .= '<hr />';
							/*
							$header = new htmlheading();
							$header->type = 3;
							$header->str = $this->objLanguage->languageText('mod_worksheet_result', 'worksheet', 'Result').':';
							//$firstStr .= $header->show();
							*/
							$mytable->startRow();
							$mytable->addCell("<b>".$this->objLanguage->languageText('mod_worksheet_result', 'worksheet', 'Result').':</b>',"100","","","","bgcolor='#D3D3D3'");



							if ($worksheetResult == FALSE) {
								$notcomplete .= $this->objLanguage->languageText('mod_worksheet_result_notcompleted', 'worksheet', 'Worksheet not completed prior to expiry date').' - 0';
								$mytable->addCell($notcomplete,"400","","","","bgcolor='#FFFFFF'");
							} else {
								if ($worksheetResult['mark'] == '-1') {
									$notmarked .= $this->objLanguage->languageText('mod_worksheet_result_notmarked', 'worksheet', 'Worksheet submitted but not yet marked').'';
									$mytable->addCell($notmarked,"400","","","","bgcolor='#FFFFFF'");
								} else {
									$score = $this->objLanguage->code2Txt('mod_worksheet_result_marked', 'worksheet', NULL, '[-mark-] out of [-total-]');
									$score = str_replace('[-mark-]', $worksheetResult['mark'], $score);
									$score = str_replace('[-total-]', $worksheet['total_mark'], $score);
									//$firstStr .= '<p>'.$score.'</p>';
									$mytable->addCell($score,"400","","","","bgcolor='#FFFFFF'");
								}
							}
							$mytable->endRow();
							//$firstStr .= '<hr />';

							$objWashout = $this->getObject('washout', 'utilities');

							$counter = 1;
							foreach ($questions as $question)
							{
							$str = '<div class="newForumContainer">';
							$str .= '<div class="newForumTopic">';
							$str .= '<strong>'.$this->objLanguage->languageText('mod_worksheet_question', 'worksheet', 'Question').' '.$counter.':</strong>';
							$str .= $objWashout->parseText($question['question']);
							$str .= '<strong>'.$this->objLanguage->languageText('mod_worksheet_marks', 'worksheet', 'Marks').'</strong> ('.$question['question_worth'].')';
							$str .= '</div>';
							$str .= '<div class="newForumContent">';
							$studentAnswer = $this->objWorksheetAnswers->getAnswer($question['id'], $userId);
							if ($studentAnswer != FALSE) {
							$str .= $studentAnswer['answer'];

							$str .= '</div><br /><div class="newForumContent">';

							if ($studentAnswer['mark'] == NULL) {
							$str .= '<p class="error">'.'Your answer has not been marked yet.'.'</p>';
							} else {
							$table = $this->newObject('htmltable', 'htmlelements');
							$table->width = 495;
							$table->cellspacing = 1;
							$table->border = 1;
							$table->startRow();
							$table->addCell("<b>".$this->objLanguage->languageText('mod_worksheet_mark', 'worksheet', 'Mark').': </b>',"90","","","","bgcolor='#D3D3D3'");
							$table->addCell($studentAnswer['mark'],"400","","","","bgcolor='#FFFFFF'");
							$table->endRow();
							$table->startRow();
							$table->addCell("<b>".$this->objLanguage->languageText('mod_worksheet_comment', 'worksheet', 'Comment').': </b>',"90","","","","bgcolor='#D3D3D3'");
							$table->addCell($studentAnswer['comments'],"400","","","","bgcolor='#FFFFFF'");
							$table->endRow();
							$table->startRow();
							$table->addCell("<b>".ucwords($this->objLanguage->code2Txt('mod_worksheet_lecturer', 'worksheet', NULL, '[-author-]')).': </b>',"90","","","","bgcolor='#D3D3D3'");
							$table->addCell($this->objUser->fullName($studentAnswer['lecturer_id']),"400","","","","bgcolor='#FFFFFF'");
							$table->endRow();
							$str .= $table->show();
							}

							} else {
							$str .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_worksheet_notanswered', 'worksheet', 'Not answered').'</div>';
							}

							$str .= '</div>';

							$str .= '</div>';

							//echo $str;
							$mytable->startRow();
							$mytable->addCell($str,"500","","","","colspan='2'");
							$mytable->endRow();
							$counter++;
							}
						}
						}else{
						$mytable->startRow();
						$mytable->addCell("&nbsp;","","","","","bgcolor='#FFFFFF' colspan='2'");
						$mytable->endRow();
						}
					}
					}
					return $courseHeader->show().$mytable->show();
					}
					}
}
?>

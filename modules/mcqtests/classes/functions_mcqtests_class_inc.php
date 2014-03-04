<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Class for general functions within mcqtests
* @author Paul Mungai
* @copyright (c) 2009 UoN
* @package mcqtests
* @version 0.1
*/

class functions_mcqtests extends object
{
    public $dbTestadmin;
    public $dbQuestions;
    public $dbResults;
    public $dbMarked;
    public $objWashout;
    public $objLanguage;
    public $objContext;
    public $objIcon;
    public $objUser;
    public $contextCode;

    public function init()
    {
        $this->dbTestadmin = $this->newObject('dbtestadmin', 'mcqtests');
        $this->dbQuestions = $this->newObject('dbquestions', 'mcqtests');
        $this->dbResults = $this->newObject('dbresults', 'mcqtests');
        $this->dbMarked = $this->newObject('dbmarked', 'mcqtests');
        $this->objWashout = $this->getObject('washout','utilities');
								$this->loadClass('htmlheading', 'htmlelements');
								$this->loadClass('link', 'htmlelements');
        $this->loadClass('layer','htmlelements');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objContext = &$this->newObject('dbcontext', 'context');
        $this->objIcon= $this->newObject('geticon','htmlelements');
        $this->objUser = &$this->getObject('user', 'security');
								$objPopup=&$this->loadClass('windowpop','htmlelements');							
      		// Get the context
      		$this->contextCode = $this->objContext->getContextCode();
    }
    
    /**
     * 
     *Method to output student mcq's
     *@param string $contextCode
     * @return array
     *example: $this->objMcqtestsFunctions->displaymcq($contextCode, $userId, $uriAction='showtest', $uriModule='eportfolio'); 
     */
    public function displaymcq($contextCode, $userId=Null, $uriAction, $uriModule)
    { 
	 $data = $this->dbTestadmin->getTests($contextCode);

	 if (!empty($data)) {
		$objmcqTable = $this->newObject('htmltable', 'htmlelements');
		$objmcqTable->startHeaderRow();
		$objmcqTable->addHeaderCell($this->objLanguage->languageText('word_name', 'system', 'Name'),'23%');
		$objmcqTable->addHeaderCell($this->objLanguage->languageText('mod_mcqtests_percentage', 'mcqtests', 'Percentage')." ".$this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests', 'Mark'), '15%');
		$objmcqTable->addHeaderCell($this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests', 'Mark'),'5%');
		$objmcqTable->addHeaderCell($this->objLanguage->languageText('mod_mcqtests_closingdate', 'mcqtests', 'Closing Date'),'15%');
		$objmcqTable->addHeaderCell($this->objLanguage->languageText('mod_assignment_datesubmitted', 'assignment', 'Date Submitted'),'27%');
		$objmcqTable->addHeaderCell($this->objLanguage->languageText('mod_eportfolio_view', 'eportfolio', 'View'),'15%');
		$objmcqTable->endHeaderRow();
	  foreach($data as $myData){
	   $studentMark = "";
	   $studentSubmitDate = "";   
	   $totalmark = $this->dbQuestions->sumTotalmark($myData['id']);
	   $resultsData = $this->dbResults->getResults($myData['id']);
	   $stdntTests = $this->dbTestadmin->getStudentTests($contextCode, $userId);
	   if(!empty($stdntTests)){
	    foreach($stdntTests as $stdntTest){
	     if($stdntTest["testid"] == $myData['id'] && $stdntTest["studentid"] == $userId){
	       $studentSubmitDate = $stdntTest["endtime"];
	     }

	    }
	   }

if(!empty($resultdata))
{ 
	   foreach($resultsData as $myResults){
	    if($myResults["studentid"] == $userId){
	      //var_dump($myResults);

	      $studentMark = $myResults["mark"];
	    }
	    $objLink = new link($this->uri(array('action' => 'showtest','id' => $myData['id'],'studentId' => $userId)));

		$this->objIcon->title=$this->objLanguage->languageText("mod_eportfolio_view", 'eportfolio');
		$this->objIcon->setIcon('comment_view');
		$commentIcon = $this->objIcon->show();

		$objPopup = new windowpop();
		$objPopup->set('location',$this->uri(array('action' => $uriAction,'id' => $myData['id'],'studentId' => $userId),$uriModule));
		$objPopup->set('linktext',$commentIcon);
		$objPopup->set('width','600');
		$objPopup->set('height','350');
		$objPopup->set('left','200');
		$objPopup->set('top','200');
	    	$objPopup->set('scrollbars','yes');
	    	$objPopup->set('resizable','yes');
		$objPopup->putJs(); // you only need to do this once per page
		//echo $objPopup->show();
	
	   //var_dump($myData['id'].' - '.$myData['name'].' - Closing Date - '.$myData["closingdate"].' - Date Submitted - '.$studentSubmitDate." - Percentage Mark - ".$myData["percentage"]." Total Mark - ".$totalmark."<br>"." Student Mark - ".$studentMark);
		$objmcqTable->startRow();
		$objmcqTable->addCell($myData['name'],'','','','','');
		$objmcqTable->addCell($myData["percentage"]." %",'','','','','');
		$objmcqTable->addCell(round(($studentMark/$totalmark*100),2)." %",'','','','','');
		$objmcqTable->addCell($myData["closingdate"],'','','','','');
		$objmcqTable->addCell($studentSubmitDate,'','','','','');
		$objmcqTable->addCell($objPopup->show(),'','','','','');
		$objmcqTable->endRow();

	   }
       }
	  }
	    return $objmcqTable->show();
	 }else{
	  return False;
	 }	
   }
    /**
     * 
     *Method to output student mcq's
     *@param string $contextCode
     * @return array
     *example: $this->objMcqtestsFunctions->displaymcq($contextCode, $userId); 
     */
    public function displaymcqFull($contextCode, $userId=Null)
    {
					// set up language items
					$studentLabel = ucfirst($this->objLanguage->languageText('mod_context_readonly', 'context'));
					$heading = $this->objLanguage->languageText('mod_mcqtests_testresults', 'mcqtests');
					$testLabel = $this->objLanguage->languageText('mod_mcqtests_test', 'mcqtests');
					$totalLabel = $this->objLanguage->languageText('mod_mcqtests_totalmarks', 'mcqtests');
					$markLabel = $this->objLanguage->languageText('mod_mcqtests_mark', 'mcqtests');
					$questionLabel = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
					$commentLabel = $this->objLanguage->languageText('mod_mcqtests_comment', 'mcqtests');
					$correctAnsLabel = $this->objLanguage->languageText('mod_mcqtests_correctans', 'mcqtests');
					$noAnsLabel = $this->objLanguage->languageText('mod_mcqtests_unanswered', 'mcqtests');
					$yourAnsLabel = $this->objLanguage->languageText('mod_mcqtests_answer', 'mcqtests');
					$exitLabel = $this->objLanguage->languageText('word_exit');
					$nextLabel = $this->objLanguage->languageText('mod_mcqtests_next', 'mcqtests');

					$data = $this->dbTestadmin->getTests($contextCode);
                    $ifnotempty = 0;
					if (!empty($data)) {
						foreach($data as $myData){
						    
							$studentMark = "";
							$studentSubmitDate = "";   
							$totalmark = $this->dbQuestions->sumTotalmark($myData['id']);
							$resultsData = $this->dbResults->getResults($myData['id']);
							$stdntTests = $this->dbTestadmin->getStudentTests($contextCode, $userId);
							if(!empty($stdntTests)){
								foreach($stdntTests as $stdntTest){
									if($stdntTest["testid"] == $myData['id'] && $stdntTest["studentid"] == $userId){
									$studentSubmitDate = $stdntTest["endtime"];
									}
								}
							}

							//foreach($resultsData as $myResults){
       if(!empty($resultsData)){
        $myResults = $resultsData[0];
								if($myResults["studentid"] == $userId){
									$studentMark = $myResults["mark"];
								}
								$result = $this->dbResults->getResult($userId, $myData['id']);

								$test = $this->dbTestadmin->getTests($this->contextCode, 'name, totalmark', $myData['id']);
								if(!empty($result))
								$result = array_merge($result[0], $test[0]);
								$totalmark = $this->dbQuestions->sumTotalmark($myData['id']);	
								$qNum = $this->getParam('qnum');

								if (empty($qNum)) {
									$data = $this->dbQuestions->getQuestionCorrectAnswer($myData['id']);
								} else {
									$data = $this->dbQuestions->getQuestionCorrectAnswer($myData['id'], $qNum);
								}
								if (!empty($data)) {
									foreach($data as $key => $line) {
										$marked = $this->dbMarked->getMarked($userId, $line['questionid'], $myData['id']);
										$data[$key]['studcorrect'] = $marked[0]['correct'];
										$data[$key]['studans'] = $marked[0]['answer'];
										$data[$key]['studorder'] = $marked[0]['answerorder'];
										$data[$key]['studcomment'] = $marked[0]['commenttext'];
									}
								}
								$objTable = new htmltable();
								$objTable->cellspacing = 1;
								$objTable->width = 500;
								$objTable->border = 1;
								
								$ifnotempty = 1;

								$percent = round($result['mark']/$totalmark*100, 2);
								$studentName = $this->objUser->fullName($result['studentid']);
								$objTable->startRow();
								$objTable->addCell('<b>'.$studentLabel.':</b>',"100","","","","bgcolor='#D3D3D3'");
								$objTable->addCell($studentName,"400","","","","bgcolor='#FFFFFF'");
								$objTable->endRow();

								//$str = '<font size="3"><b>'.$studentLabel.':</b>&nbsp;&nbsp;&nbsp;'.$studentName.'<br />';
								$objTable->startRow();
								$objTable->addCell('<b>'.$testLabel.':</b>',"100","","","","bgcolor='#D3D3D3'");
								$objTable->addCell($result['name'],"400","","","","bgcolor='#FFFFFF'");
								$objTable->endRow();

								//$str.= '<b>'.$testLabel.':</b>&nbsp;&nbsp;&nbsp;'.$result['name'].'<br />';
								$objTable->startRow();
								$objTable->addCell('<b>'.$totalLabel.':</b>'.$totalmark,"100","","","","bgcolor='#D3D3D3'");
								$objTable->addCell($totalmark,"400","","","","bgcolor='#FFFFFF'");
								$objTable->endRow();

								//$str.= '<b>'.$totalLabel.':</b>&nbsp;&nbsp;&nbsp;'.$totalmark.'<br />';
								$objTable->startRow();
								$objTable->addCell('<b>'.$markLabel.':</b>',"100","","","","bgcolor='#D3D3D3'");
								$objTable->addCell($result['mark'].'&nbsp;&nbsp;('.$percent.'%)',"400","","","","bgcolor='#FFFFFF'");
								$objTable->endRow();
								//$str.= '<font size="3"><b>'.$markLabel.':</b>&nbsp;&nbsp;&nbsp;'.$result['mark'].'&nbsp;&nbsp;('.$percent.'%)</font>';

								if (!empty($data)) {
									$qNum = $data[0]['questionorder'];
									$this->objIcon->setIcon('greentick');
									$tickIcon = $this->objIcon->show();
									$this->objIcon->setIcon('redcross');
									$crossIcon = $this->objIcon->show();
									foreach($data as $line) {
										$ansNum = '&nbsp;&nbsp;&nbsp;'.$line['answerorder'];
										$content = '<b>'.$yourAnsLabel.':'.$ansNum.'</b>&nbsp;&nbsp;&nbsp;'.$line['answer'];
										if (!$line['studcorrect']) {
										if (!empty($line['studorder']) && !empty($line['studans'])) {
											$ansNum = '&nbsp;&nbsp;&nbsp;'.$alpha[$line['studorder']].')';
											$content.= '<b>'.$yourAnsLabel.':'.$ansNum.'</b>&nbsp;&nbsp;&nbsp;'.$line['studans'];
										} else {
											$content.= $noAnsLabel;
										}
											$icon = $crossIcon;
										} else {
											$icon = $tickIcon;
										}
										if (!empty($line['studcomment'])) {
											$content.= '<b>'.$commentLabel.':</b>&nbsp;&nbsp;&nbsp;'.$line['studcomment'];
										}

										$objLayer = new layer();
										$objLayer->str = $icon;
										$objLayer->align = 'left';
										$objLayer->cssClass = 'forumTopic';
										$iconLayer = $objLayer->show();

										$objLayer = new layer();
										$objLayer->left = 'margin-right: 20px; float:left';
										$objLayer->cssClass = 'forumTopic';
										$parsedQuestion = $this->objWashout->parseText($line['question']);
										$objLayer->str = '<b>'.$questionLabel.' '.$line['questionorder'].':</b>'.$parsedQuestion;
										$question = $objLayer->show();


										$objLayer = new layer();
										$objLayer->cssClass = 'forumContent';
										$objLayer->str = $content;
										$answers = $objLayer->show();

										$objLayer = new layer();
										$objLayer->cssClass = 'topicContainer';
										$objLayer->str = $question.$answers.$iconLayer;

										//$str.= $objLayer->show();
										$objTable->startRow();
										$objTable->addCell($objLayer->show(),"500","","","","colspan=2 bgcolor='#FFFFFF'");
										$objTable->endRow();
										$objLayer = new layer();

										$objLayer->cssClass = 'forumBase';
										$objLayer->str = '';
										//$str.= $objLayer->show() .'<br />';

										$objTable->startRow();
										$objTable->addCell($objLayer->show(),"500","","","","colspan=2 bgcolor='#FFFFFF'");
										$objTable->endRow();
										$qNum = $line['questionorder'];
									}
								}
							}
						}
						if($ifnotempty == 1){
						 return $objTable->show();
						}else{
						 return false;
						}
					}else{
					return false;
					}
   }
}
?>

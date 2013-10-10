<?php
/**
* polltools class extends object
* @package poll
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* polltools class
* @author Megan Watson, Paul Mungai
* @copyright (c) 2007 UWC
* @version 0.1
*/

class polltools extends object
{   
    /**
    * @var string $contextCode The current context .. to do.
    * @access private
    */
    private $contextCode = 'root';
    
    /**
    * Constructor method
    */
    public function init()
    {
        try {
            $this->dbQuestions = $this->getObject('dbquestions', 'poll');
            $this->dbResponse = $this->getObject('dbresponse', 'poll');
            $this->dbPoll = $this->getObject('dbpoll', 'poll');
            $this->dbAnswers = $this->getObject('dbanswers', 'poll');            
            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            
            $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->loadClass('htmlheading', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
            $this->loadClass('textarea', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
            $this->loadClass('radio', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
	    $this->loadClass('hiddeninput', 'htmlelements');
	    // Load Context Object
	    $this->objContext = $this->getObject('dbcontext', 'context');
	            
	    // Store Context Code
	    $this->contextCode = $this->objContext->getContextCode();

        } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to display the left side menu
    *
    * @access public
    * @return string html
    */
    public function leftMenu()
    {
        $pollBlock = $this->getPollBlock();
        $configBlock = $this->getConfigBlock();
        
        $lbPoll = $this->objLanguage->languageText('word_poll');
        $hdConfig = $this->objLanguage->languageText('word_configuration');
        
        $str = $this->objFeatureBox->show($lbPoll, $pollBlock);
        $str .= $this->objFeatureBox->show($hdConfig, $configBlock);
        
        return $str;
    }
    
    /**
    * Method to display a form for adding or editing a poll
    *
    * @access public
    * @param array The data for editing
    * @return string html
    */
    public function showAdd($data = NULL)
    {
        $hdAdd = $this->objLanguage->languageText('phrase_createnewpoll');
        $lbQuestion = $this->objLanguage->languageText('word_question');
        $lbType = $this->objLanguage->languageText('phrase_questiontype');
        $lbYesNo = $this->objLanguage->languageText('mod_poll_yesno', 'poll');
        $lbBool = $this->objLanguage->languageText('mod_poll_truefalse', 'poll');
        $lbVary = $this->objLanguage->languageText('mod_poll_varying', 'poll');
        $lbUser = $this->objLanguage->languageText('phrase_userdefined');
        $lbVisible = $this->objLanguage->languageText('phrase_isvisible');
        $lbAddAns = $this->objLanguage->languageText('phrase_addanswer');
        $lbAnswer = $this->objLanguage->languageText('word_answer');
        $lbYes = $this->objLanguage->languageText('word_yes');
        $lbNo = $this->objLanguage->languageText('word_no');
        $btnSave = $this->objLanguage->languageText('word_save');
        
        $question = ''; $type = 'yes'; $visible = 'yes'; $id = '';
        if(!empty($data)){
            $hdAdd = $this->objLanguage->languageText('phrase_editpoll');
            $question = $data['question'];
            $type = $data['question_type'];
            $visible = $data['is_visible'];
            $id = $data['id'];
        }
        
        $objHead = new htmlheading();
        $objHead->str = $hdAdd;
        $objHead->type = 1;
        $str = $objHead->show();
        
        // question text
        $objLabel = new label($lbQuestion, 'input_question');
        $objText = new textarea('question', $question);
        $objText->setId('input_question');
        $formStr = '<p>'.$objLabel->show().': <br />'.$objText->show().'</p>';
        
        // question type
        $objLabel = new label($lbType, 'input_type');
        $objRadio = new radio('type');
        $objRadio->addOption('yes', '&nbsp;'.$lbYesNo);
        $objRadio->addOption('bool', '&nbsp;'.$lbBool);
        $objRadio->addOption('open', '&nbsp;'.$lbVary);
        //$objRadio->addOption('user', '&nbsp;'.$lbUser); ... to do.
        $objRadio->setSelected($type);
        $objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;&nbsp;');
        $objRadio->extra = '';
        $formStr .= '<p>'.$objLabel->show().': <br />'.$objRadio->show().'</p>';
        
        // is visible
        $objLabel = new label($lbVisible, 'input_visible');
        $objRadio = new radio('visible');
        $objRadio->addOption('1', '&nbsp;'.$lbYes);
        $objRadio->addOption('0', '&nbsp;'.$lbNo);
        $objRadio->setSelected($visible);
        $objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;&nbsp;');
        $objRadio->extra = '';
        $formStr .= '<p>'.$objLabel->show().': <br />'.$objRadio->show().'</p>';
        
        // Display answer layer ... to do.
        
        if(!empty($id)){
            $objInput = new textinput('id', $id, 'hidden');
            $formStr .= $objInput->show();
        }
        
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show().'</p>';
        
        $objForm = new form('addquestion', $this->uri(array('action' => 'savequestion')));
        $objForm->addToForm($formStr);
        $str .= $objForm->show();
        
        return $str;
    }
    /**
    * Method to display a form for adding or editing a poll for the context module
    *
    * @access public
    * @param array The data for editing
    * @return string html
    */
    public function showAddInContext($data = NULL)
    {
        $lbAdd = $this->objLanguage->languageText('word_add');
        $lbQuestion = $this->objLanguage->languageText('word_question');
        $hdAdd = $lbAdd." ".$lbQuestion;
        $lbType = $this->objLanguage->languageText('phrase_questiontype');
        $lbYesNo = $this->objLanguage->languageText('mod_poll_yesno', 'poll');
        $lbBool = $this->objLanguage->languageText('mod_poll_truefalse', 'poll');
        $lbVary = $this->objLanguage->languageText('mod_poll_varying', 'poll');
        $lbTypeOptions = $this->objLanguage->languageText('mod_poll_typeoption', 'poll');
        $lbUser = $this->objLanguage->languageText('phrase_userdefined');
        $lbVisible = $this->objLanguage->languageText('phrase_isvisible');
        $lbAddAns = $this->objLanguage->languageText('phrase_addanswer');
        $lbAnswer = $this->objLanguage->languageText('word_answer');
        $lbYes = $this->objLanguage->languageText('word_yes');
        $lbNo = $this->objLanguage->languageText('word_no');
        $lbEdit = $this->objLanguage->languageText('word_edit');
        $lbDelete = $this->objLanguage->languageText('word_delete');
        $btnSave = $this->objLanguage->languageText('word_save');
	$btnCancel = $this->objLanguage->languageText('word_cancel');
	// Store Context Code
	$contextCode = $this->contextCode;    
	//store the context code
	$code = new textinput('contextcode', $contextCode, 'hidden','10');
    
        $question = ''; $type = 'yes'; $visible = 'yes'; $id = '';
        if(!empty($data)){
            $hdAdd = $lbEdit." ".$lbQuestion;
            $question = $data['question'];
            $type = $data['question_type'];
            $visible = $data['is_visible'];
            $id = $data['id'];
        }
	if($data['id']==null){
            $id = '0';
	}
        
        $objHead = new htmlheading();
        $objHead->str = $hdAdd;
        $objHead->type = 1;
        $str = $objHead->show();
        
        // question text
        $objLabel = new label($lbQuestion, 'input_question');
        $objText = new textarea('question', $question);
        $objText->setId('input_question');
        $formStr = '<p>'.$objLabel->show().': <br /> '.$objText->show().'</p>';
        
        // question type
        $objLabel = new label($lbType, 'input_type');
        $objRadio = new radio('qntype');
        $objRadio->addOption('yes', '&nbsp;'.$lbYesNo);
        $objRadio->addOption('bool', '&nbsp;'.$lbBool);
        $objRadio->addOption('open', '&nbsp;'.$lbVary);
        //$objRadio->addOption('user', '&nbsp;'.$lbUser); ... to do.
        $objRadio->setSelected($type);
        $objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;&nbsp;');
        $objRadio->extra = 'onclick="onClickRadio()"';
        $formStr .= '<p>'.$objLabel->show().': <br />'.$objRadio->show().'</p>';
	//Get The answers
	$formStr .= '<div id="theoptions">';
        if(!empty($id) || $id!=='0'){
		$yourOptions = $this->dbAnswers->getAnswers($id);
		if(!empty($yourOptions) && $type=='open'){
			$formStr .= "<b>Options</b><br />";
			foreach($yourOptions as $options){
				$formStr .= '<br /><label><input type="radio" name="'.$id.'" value="'.$options['answer'].'" />&nbsp;&nbsp;'.$options['answer'].'</label>  <a href="#" onclick="editAnswer(\''.$options['id'].'\',\''.$options['question_id'].'\',\''.$options['answer'].'\')">'.$lbEdit.'</a>    <a href="#" onclick="onDeleteAns(\''.$options['id'].'\')">'.$lbDelete.'</a>';
			}
		}elseif(!empty($yourOptions) && $type!=='open'){
			$formStr .= "<b>Options</b><br />";
			foreach($yourOptions as $options){
				$formStr .= '<br /><label><input type="radio" name="'.$id.'" value="'.$options['answer'].'" />&nbsp;&nbsp;'.$options['answer'].'</label>';
			}
		}

	}
	if($type=='open' && $id!=='0'){
		$formStr .= '</div><p><div id=\'question_options\'>'.$lbTypeOptions.': <input name=\'opt_'.$id.'\' id=\'ans_'.$id.'\' type=\'text\' /> <a href=\'#\' onclick=\'saveAnswer("'.$id.'","0")\'>Save</a></div></p>';	
	}else{
		$formStr .= "</div><p><div id='question_options'> </div></p>";
        }
        // is visible
        $objLabel = new label($lbVisible, 'input_visible');
        $objRadio = new radio('visible');
        $objRadio->addOption('1', '&nbsp;'.$lbYes);
        $objRadio->addOption('0', '&nbsp;'.$lbNo);
        $objRadio->setSelected($visible);
        $objRadio->setBreakSpace('&nbsp;&nbsp;&nbsp;&nbsp;');
        $objRadio->extra = '';
        $formStr .= '<p>'.$objLabel->show().': <br />'.$objRadio->show()."<br />".$code->show().'<br />'.' <span id="contextcodemessage">'.$contextCodeMessage.'</span>'.'</p>';
        
        // Display answer layer ... to do.
        

            $objInput = new textinput('id', $id, 'hidden');
            $formStr .= $objInput->show();
	//Save Button
        $objButton = new button('save', $btnSave);
	$objButton->setId('onfinish');
	$objButton->setOnClick('clickButton()');
	//Cancel Button
        $objCancelButton = new button('cancel', $btnCancel);
	$objCancelButton->setId('oncancel');
	$objCancelButton->setOnClick('viewPolls()');

        //$objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show()." ".$objCancelButton->show().'</p>';
        
        $objForm = new form('addquestion', $this->uri(array('action' => 'savequestion')));
        $objForm->addToForm($formStr);
        $str .= $objForm->show();        
        return $str;
    }
    
    
    /**
    * Method to display the recently added polls in a table
    *
    * @access public
    * @return string html
    */
    public function showPolls($pollData)
    {
        $hdPolls = $this->objLanguage->languageText('word_polls');
        $lbQuestion = $this->objLanguage->languageText('word_question');
        $lbOrder = $this->objLanguage->languageText('word_order');
        $lnAdd = $this->objLanguage->languageText('phrase_createnewpoll');
        $lbNoPolls = $this->objLanguage->languageText('mod_poll_nopollscreated', 'poll');
        $lbDelete = $this->objLanguage->languageText('mod_poll_deletequestionconfirm', 'poll');
        
        $objHead = new htmlheading();
        $objHead->str = $hdPolls;
        $objHead->type = 1;
        $str = $objHead->show();
        
        if(!empty($pollData)){
            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->cellspacing = '2';
            
            $hdArr = array();
            $hdArr[] = $lbOrder;
            $hdArr[] = $lbQuestion;
            $hdArr[] = '';
            
            $objTable->addHeader($hdArr);
            
            $class = 'odd';
            foreach($pollData as $item){
                $class = ($class == 'even') ? 'odd':'even';
                
                $icons = $this->objIcon->getEditIcon($this->uri(array('action' => 'showedit', 'id' => $item['id'])));
                $icons .= $this->objIcon->getDeleteIconWithConfirm('', array('action' => 'deletequestion', 'id' => $item['id']), 'poll', $lbDelete);
                
                $rowArr = array();
                $rowArr[] = $item['order_num'];
                $rowArr[] = $item['question'];
                $rowArr[] = $icons;
                
                $objTable->addRow($rowArr);
            }
            $str .= $objTable->show();
        }else{
            $str .= '<p class="noRecordsMessage">'.$lbNoPolls.'</p>';
        }
        
        $objLink = new link($this->uri(array('action' => 'showadd')));
        $objLink->link = $lnAdd;
        $str .= '<p>'.$objLink->show().'</p>';
        
        return $str;
    }
    /**
    * Method to display the recently added context polls in a table
    *
    * @access public
    * @return string html
    */
    public function showContextPolls($pollData)
    {
        $hdPolls = $this->objLanguage->languageText('word_polls');
        $hdHappiness = $this->objLanguage->languageText('mod_poll_happiness','poll');
        $hdQuestions = $this->objLanguage->languageText('mod_poll_questions','poll');
        $lbQuestion = $this->objLanguage->languageText('word_question');
        $lbOrder = $this->objLanguage->languageText('word_order');
        $lnAdd = $this->objLanguage->languageText('mod_poll_createquestion','poll');
        $lbNoPolls = $this->objLanguage->languageText('mod_poll_nopollscreated', 'poll');
        $lbDelete = $this->objLanguage->languageText('mod_poll_deletequestionconfirm', 'poll');
        $btnEdit = $this->objLanguage->languageText('word_edit');        
        $btnDelete = $this->objLanguage->languageText('word_delete');
        $btnResult = $this->objLanguage->languageText('word_results');                
        $objHead = new htmlheading();
        $objHead->str = $hdHappiness.' '.$hdQuestions;
        $objHead->type = 1;
        $str = $objHead->show();
        
        if(!empty($pollData)){
	$objTable = new htmltable();
	$objTable->cellpadding = '5';
	$objTable->cellspacing = '5';
	$objTable->startRow();
	$objTable->addHeaderCell($lbOrder, 5, 'top', 'left');
	$objTable->addHeaderCell($lbQuestion, 300, 'top', 'left');
	$objTable->addHeaderCell('&nbsp;', 15, 'top', 'left');
	$objTable->addHeaderCell('&nbsp;', 15, 'top', 'left');
	$objTable->addCell('&nbsp;', 10, 'top', 'left');
	$objTable->endRow();		
	$qnId = "200";            
            $class = 'odd';
            foreach($pollData as $item){
                $class = ($class == 'even') ? 'odd':'even';
		//Store Id in hidden textinput
	        $objInput = new textinput($qnId, $item['id'], 'hidden');
		//Edit button
        	$objEdButton = new button('edit', $btnEdit);
		$objEdButton->setId('onedit');
		$objEdButton->setOnClick('javascript:onEdit('.$qnId.')');
		//Delete Button
        	$objDtButton = new button('delete', $btnDelete);
		$objDtButton->setId('ondelete');
		$objDtButton->setOnClick('javascript:onDelete('.$qnId.')');

		$qnId = $qnId + 1;
                $icons = $this->objIcon->getDeleteIconWithConfirm('', array('action' => 'deletecontextqn', 'id' => $item['id']), 'poll', $lbDelete);
		$objTable->startRow();
		$objTable->addCell($item['order_num'], 5, 'top', 'left');
		$objTable->addCell($item['question'], 300, 'top', 'left');
		$objTable->addCell($objEdButton->show(), 15, 'top', 'right');
		$objTable->addCell($objDtButton->show(), 15, 'top', 'left');
		$objTable->addCell($objInput->show(), 10, 'top', 'right');
		$objTable->endRow();
		//Get the Answer Options
                $answerOptions = $this->dbAnswers->getAnswers($item['id']);
		//Add Options
	        $objRadio = new radio($item['id']);
		if($answerOptions){
	                $ansArr = array();
			foreach($answerOptions as $ansOptions){
				$option = $ansOptions['answer'];
			        $objRadio->addOption($option, '&nbsp;'.$option);
			        $objRadio->setSelected(null);
			        $objRadio->setBreakSpace('<br />');
			        $objRadio->extra = '';
			}
			$ansArr[] = '&nbsp;';
			$ansArr[] = $objRadio->show();
			$ansArr[] = '&nbsp;';
			$ansArr[] = '&nbsp;';
			$ansArr[] = '&nbsp;';
	                $objTable->addRow($ansArr);
		}

            }
            $str .= $objTable->show();
        }else{
            $str .= '<p class="noRecordsMessage">'.$lbNoPolls.'</p>';
        }
        
        $objLink = new link($this->uri(array('action' => 'showadd')));
        $objLink->link = $lnAdd;
        	$objAdButton = new button('add', $lnAdd);
		$objAdButton->setId('onadd');
		$objAdButton->setOnClick('javascript:onAdd()');
        $objLink = new link($this->uri(array('action' => 'showadd')));
        $objLink->link = $lnAdd;
        	$objResultsButton = new button('results', $btnResult);
		$objResultsButton->setId('viewresults');
		$objResultsButton->setOnClick('javascript:viewResults()');

        $str .= '<span id="contextcodemessage">'.$contextCodeMessage.'</span>'.'<p>'.$objAdButton->show()." ".$objResultsButton->show().'</p>';
        
        return $str;
    }
    /**
    * Method to display the recently added context polls in for learner submision
    *
    * @access public
    * @return string html
    */
    public function castContextPolls($pollData)
    {
        $hdPolls = $this->objLanguage->languageText('word_polls');
        $hdHappiness = $this->objLanguage->languageText('mod_poll_happiness','poll');
        $lbQuestion = $this->objLanguage->languageText('word_question');
        $lbOrder = $this->objLanguage->languageText('word_order');
        $lnAdd = $this->objLanguage->languageText('phrase_createnewpoll');
        $lnSubmit = $this->objLanguage->languageText('word_submit');
        $lbNoPolls = $this->objLanguage->languageText('mod_poll_nopollscreated', 'poll');
        $lbDelete = $this->objLanguage->languageText('mod_poll_deletequestionconfirm', 'poll');
        $btnEdit = $this->objLanguage->languageText('word_edit');        
        $btnDelete = $this->objLanguage->languageText('word_delete');                
        $objHead = new htmlheading();
        $objHead->str = $hdHappiness." ".$hdPolls;
        $objHead->type = 1;
        $str = $objHead->show();
        
        if(!empty($pollData)){
            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->cellspacing = '2';
            
            $hdArr = array();
            $hdArr[] = $lbOrder;
            $hdArr[] = $lbQuestion;
            $hdArr[] = '';
            
            $objTable->addHeader($hdArr);
	$rowCount = "1";            
            $class = 'odd';
            foreach($pollData as $item){
	    //Display question if visible is set as true
	    if($item['is_visible']==1){
                $class = ($class == 'even') ? 'odd':'even';
		//Store Id in hidden textinput
	        $objInput = new textinput($rowCount, $item['id'], 'hidden');
		//Store the qn type in a hidden input
	        $objQntype = new textinput("qntype_".$rowCount, $item['question_type'], 'hidden');
		//increment row count
		$rowCount = $rowCount + 1;
                $rowArr = array();
                $rowArr[] = $item['order_num'];
                $rowArr[] = $item['question'];
                $rowArr[] = $objInput->show();
                $rowArr[] = $objQntype->show();
                $objTable->addRow($rowArr);
		//Get the Answer Options
                $answerOptions = $this->dbAnswers->getAnswers($item['id']);
		//Add Options
	        $objRadio = new radio($item['id']);
		if($answerOptions){
	                $ansArr = array();
			foreach($answerOptions as $ansOptions){
				$option = $ansOptions['answer'];
			        $objRadio->addOption($option, '&nbsp;'.$option);
			        $objRadio->setSelected(null);
			        $objRadio->setBreakSpace('<br />');
			        $objRadio->extra = '';
				//Store Id in hidden textinput
			        //$objOptionsInput = new textinput($rowCount, $item['id'], 'hidden');

			}
			$ansArr[] = '&nbsp;';
			$ansArr[] = $objRadio->show();
			$ansArr[] = '&nbsp;';
			$ansArr[] = '&nbsp;';
			$ansArr[] = '&nbsp;';
	                $objTable->addRow($ansArr);
		}
	      }

            }
            $str .= $objTable->show();
        }else{
            $str .= '<p class="noRecordsMessage">'.$lbNoPolls.'</p>';
        }
	//Store Id in hidden textinput
        $objRowcount = new textinput("rowcount", $rowCount, 'hidden');
        
        $objLink = new link($this->uri(array('action' => 'showadd')));
        $objLink->link = $lnAdd;
        	$objAdButton = new button('add', $lnSubmit);
		$objAdButton->setId('onsubmit');
		$objAdButton->setOnClick('javascript:saveResponse()');

        $str .= '<span id="contextcodemessage">'.$contextCodeMessage.'<p>'.$objAdButton->show().$objRowcount->show().'</p>'.'</span>';
        
        return $str;
    }
    /**
    * Method to display the poll results per question in a given course
    *
    * @access public
    * @return string html
    */
    public function analyseContextPolls($pollData)
    {
        $hdPoll = $this->objLanguage->languageText('word_poll');
        $hdHappiness = $this->objLanguage->languageText('mod_poll_happiness','poll');
        $lbPolls = $this->objLanguage->languageText('word_polls');
        $hdResults = $this->objLanguage->languageText('word_results');
        $lbQuestion = $this->objLanguage->languageText('word_question');
        $lbOrder = $this->objLanguage->languageText('word_order');
        $lnAdd = $this->objLanguage->languageText('phrase_createnewpoll');
        $lnSubmit = $this->objLanguage->languageText('word_submit');
        $lbNoPolls = $this->objLanguage->languageText('mod_poll_nopollscreated', 'poll');
        $lbBack = $this->objLanguage->languageText('word_back');                
        $lbDelete = $this->objLanguage->languageText('mod_poll_deletequestionconfirm', 'poll');
        $btnEdit = $this->objLanguage->languageText('word_edit');        
        $btnDelete = $this->objLanguage->languageText('word_delete');                
        $objHead = new htmlheading();
        $objHead->str = $hdHappiness." ".$hdResults;
        $objHead->type = 1;
        $str = $objHead->show();
        
        if(!empty($pollData)){
            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->cellspacing = '2';
            
            $hdArr = array();
            $hdArr[] = '&nbsp;';
            $hdArr[] = '';
            
            $objTable->addRow($hdArr);
	$rowCount = "1";            
            $class = 'odd';
            foreach($pollData as $item){
                $class = ($class == 'even') ? 'odd':'even';
		//Store Id in hidden textinput
	        $objInput = new textinput($rowCount, $item['id'], 'hidden');
		//Store the qn type in a hidden input
	        $objQntype = new textinput("qntype_".$rowCount, $item['question_type'], 'hidden');
		//increment row count
		$rowCount = $rowCount + 1;
                $rowArr = array();
                $rowArr[] = "<b>".$item['question']."</b>";
                $rowArr[] = $objInput->show();
                $rowArr[] = $objQntype->show();
                $objTable->addRow($rowArr);
		//Get the Answer Options
                $answerOptions = $this->dbAnswers->getAnswers($item['id']);
		//Get the Total responses for the question
		$totalResponses = $this->dbResponse->getPollResponses($item['id']);
		$totalResponsesCount = count($totalResponses);
		if($answerOptions){
	                $ansArr = array();
			foreach($answerOptions as $ansOptions){
				$option = $ansOptions['id'];
				$ansResponses = $this->dbResponse->getAnswerResponses($item['id'], $option);
				$responseCount = count($ansResponses);
				if($responseCount==0){
				$responsePercent = 0;
				}else{
				$responsePercent = ($responseCount/$totalResponsesCount)*100;
				}
		                $rowAnsArr = array();
				$rowAnsArr[] = $ansOptions['answer'].'&nbsp; : '.$responsePercent."%";
		                $objTable->addRow($rowAnsArr);
			}
			$ansArr[] = 'Total Responses : '.$totalResponsesCount;
	                $objTable->addRow($ansArr);
		}
            }
            $str .= $objTable->show();
        }else{
            $str .= '<p class="noRecordsMessage">'.$lbNoPolls.'</p>';
        }
	//Store Id in hidden textinput
        $objRowcount = new textinput("rowcount", $rowCount, 'hidden');
        
        $objLink = new link($this->uri(array('action' => 'showadd')));
        $objLink->link = $lnAdd;
        	$objBkButton = new button('back', $lbBack);
		$objBkButton->setId('onsubmit');
		$objBkButton->setOnClick('javascript:viewPolls()');

        $str .= '<span id="contextcodemessage">'.$contextCodeMessage.'<p>'.$objBkButton->show().'</p>'.'</span>';
        
        return $str;
    }
        
    /**
    * Method to display the configurations
    *
    * @access private
    * @return string html
    */
    private function getConfigBlock()
    {
        $id = $this->getSession('pollId');
        $config = $this->dbPoll->getPollData($id);
        $rate = 'weekly'; $date = date('Y-m-d');
        if(!empty($config)){
            $rate = $config['cycle_rate'];
            $date = $config['active_date'];
        }
        
        $lbCycle = $this->objLanguage->languageText('phrase_cyclerate');
        $lbDate = $this->objLanguage->languageText('mod_poll_datefirstpoll', 'poll');
        $lbWeekly = $this->objLanguage->languageText('word_weekly');
        $lbBiWeekly = $this->objLanguage->languageText('word_biweekly');
        $lbMonthly = $this->objLanguage->languageText('word_monthly');
        $btnSave = $this->objLanguage->languageText('word_save');
        $objLabel = new label($lbCycle.': ', 'input_rate');
        $objRadio = new radio('rate');
        $objRadio->setId('input_rate');
        $objRadio->addOption('weekly', '&nbsp;'.$lbWeekly);
        $objRadio->addOption('biweekly', '&nbsp;'.$lbBiWeekly);
        $objRadio->addOption('monthly', '&nbsp;'.$lbMonthly);
        $objRadio->setSelected($rate);
        $objRadio->setBreakSpace('<br />');
        
        $str = '<p>'.$objLabel->show().'<br />'.$objRadio->show().'</p>';
        
        $objLabel = new label($lbDate.': ', 'input_date');
        $objInput = new textinput('date', $date);
        $objInput->setId('input_date');
        
        $str .= '<p>'.$objLabel->show().'<br />'.$objInput->show().'</p>';
        
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $str .= '<p>'.$objButton->show().'</p>';
        
        $objForm = new form('configure', $this->uri(array('action' => 'saveconfig')));
        $objForm->addToForm($str);
        
        return $objForm->show();
    }
    
    /**
    * Method to display current poll
    *
    * @access public
    * @return string html
    */
    public function getPollBlock()
    {
        $config = $this->dbPoll->getPollByContext($this->contextCode);
        
        if(empty($config)){
            return '';
        }
        
        $days = 0; $num = 1;
        $active = strtotime($config['active_date']);
        $now = time();
        
        if($now != $active){
            $days = floor(($now-$active) / (60*60*24));
        }
                    
        if($days > 0){
            switch($config['cycle_rate']){
                case 'weekly':
                    $num = ceil($days / 7);
                    break;
                case 'biweekly':
                    $num = ceil($days / 14);
                    break;
                case 'monthly':
                    $num = ceil($days / 30);
                    break;
            }
        }
        
        $data = $this->dbQuestions->getCurrentPoll($config['id'], $num);
        
        if(empty($data)){
            return '';
        }
        
        $lbYes = $this->objLanguage->languageText('word_yes');
        $lbNo = $this->objLanguage->languageText('word_no');
        $lbFalse = $this->objLanguage->languageText('word_false');
        $lbTrue = $this->objLanguage->languageText('word_true');
        $btnSave = $this->objLanguage->languageText('word_save');
        
        $str = '';
        if(!empty($data)){
            $str = '<p>'.$data['question'].'</p>';
            
            // radio buttons for the answer
            $objRadio = new radio('answer');
            
            switch($data['question_type']){
                case 'bool':
                    $objRadio->addOption('true', '&nbsp;'.$lbTrue);
                    $objRadio->addOption('false', '&nbsp;'.$lbFalse);
                    $objRadio->setSelected('true');
                    break;
                   
                case 'yes':
                default:
                    $objRadio->addOption('yes', $lbYes);
                    $objRadio->addOption('no', $lbNo);
                    $objRadio->setSelected('yes');
                    break;
            }
            $objRadio->setBreakSpace('<br />');
            $formStr = '<p>'.$objRadio->show().'</p>';
            
            $objInput = new textinput('questionId', $data['id'], 'hidden');
            $formStr .= $objInput->show();
            $objInput = new textinput('questionType', $data['question_type'], 'hidden');
            $formStr .= $objInput->show();
            
            $objButton = new button('save', $btnSave);
            $objButton->setToSubmit();
            $formStr .= '<p>'.$objButton->show().'</p>';
            
            $objForm = new form('response', $this->uri(array('action' => 'saveresponse')));
            $objForm->addToForm($formStr);
            $str .= $objForm->show();
        }
        return $str;
    }
}
?>

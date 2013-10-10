<script type="text/javascript">
//<![CDATA[
function init () {
	$('input_redraw').onclick = function () {
		redraw();
	}
}
function redraw () {
	var url = 'index.php';
	var pars = 'module=security&action=generatenewcaptcha';
	var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
}
function showLoad () {
	$('load').style.display = 'block';
}
function showResponse (originalRequest) {
	var newData = originalRequest.responseText;
	$('captchaDiv').innerHTML = newData;
}
//]]>
</script>
<?php
$objmsg = $this->getObject('timeoutmessage', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$tabBox = $this->newObject('tabpane', 'htmlelements');
$tabBox->width = '100%';
$this->loadClass('form','htmlelements');
//Load the textarea class
$objLink = $this->loadClass('link', 'htmlelements');
$this->loadClass('textarea','htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$this->loadClass("checkbox","htmlelements");
//Load the button object
//$objForm_edit = new form('edit_questions', $this->uri(array('action' => 'edit')));
$objForm_save_edit_questions = new form('edit_questions', $this->uri(array('action' => 'save_questions')));
$objForm_save_questions = new form('question_to_save_form', $this->uri(array('action' => 'insert_questions')));
$objViewForm = new form('edit_questions', $this->uri(array('action' => 'view')));
$table = $this->newObject('htmltable', 'htmlelements');
$view_fbtable = $this->newObject('htmltable', 'htmlelements');

$objPopupcal = $this->newObject('datepickajax', 'popupcalendar');
$startField = $objPopupcal->show('start', 'no', 'no');
//$objPopupcal->show();
//echo "start ->".$tart;

$objForm_save_questions ->displayType= 1; 
$objViewForm->displayType = 3; 
// Set columns to 3
$cssLayout->setNumColumns(3);
$table->cellpadding = '5';
$table->cellspacing = '5';
$table->width = '100%';
$view_fbtable->cellpadding = '5';
$view_fbtable->cellspacing = '5';

$leftMenu = NULL;
$rightSideColumn = NULL;
$leftCol = NULL;
$middleColumn = NULL;

//check for messages...
if ($msg == 'save') {
    $objmsg->message = $this->objLanguage->languageText('mod_feedback_recsaved', 'feedback');
    echo $objmsg->show();
} elseif($msg == 'nodata') {
	$objmsg->message = $this->objLanguage->languageText('mod_feedback_elaborate', 'feedback');
    echo $objmsg->show();
    $msg = NULL;
}
elseif($msg == 'badcaptcha')
{
	$objmsg->message = $this->objLanguage->languageText('mod_feedback_badcaptcha', 'feedback');
    echo $objmsg->show();
}
else {
	$msg = NULL;
}

if($this->objUser->isLoggedIn())
{
	$leftCol .= $objSideBar->show();
}
else {
	$leftCol = $this->objFb->loginBox(TRUE);
}
if(empty($insarr))
{
	$insarr = NULL;
}
//-----------------------------------------------------------------------------------------------------------------
	if($questions_array != null){
        $question_array_size = count($questions_array);
        for ($i = 0; $i < $question_array_size; $i ++){
            $question_label = "Question ".($i + 1).": ";
            $objForm_save_edit_questions->addToForm($question_label);
		    $objQuestion = new textArea('question_'.($i + 1), $questions_array[$i]['fb_question']);
		    $objForm_save_edit_questions->addToForm($objQuestion->show());
            // hidden elements
            //echo "question ->::::".$questions_array[$i]['fb_question']."question_id ::: ".$questions_array[$i]['puid']."<br/>";
            $objHidden_field = new textinput('question_id_'.($i + 1), $questions_array[$i]['puid']);
            $objHidden_field->fldType = 'hidden';
            $objForm_save_edit_questions->addToForm($objHidden_field->show());
		    $objForm_save_edit_questions->addToForm("<br/>");
        }
        $objHidden_field = new textinput('question_array_size', $question_array_size);
        $objHidden_field->fldType = 'hidden';
        $objForm_save_edit_questions->addToForm($objHidden_field->show());
		$objElement = new button('submit');
		// Set the button type to submit
		$objElement->setToSubmit();
		// with the word save
		$objElement->setValue(' SAVE ');
		//Add the comment element to the form
		$objForm_save_edit_questions->addToForm($objElement->show());	
        $objFbFeaturebox = $this->getObject('featurebox', 'navigation');

		$tabBox->addTab(array('name'=> $objLanguage->languageText("mod_feedback_edit_questions"),'content'=>$objFbFeaturebox->show('Edit Questions', $objForm_save_edit_questions->show())));
        //echo $objFbFeaturebox->show('Edit Questions', $objForm_save_edit_questions->show());
	}
	else{
		//Add the comment element to the form
		$objViewForm->addToForm("Please select the date from the popup calendar"."<br/>");
		$objSearhButton = new button('submit');
		// Set the button type to submit
		$objSearhButton->setToSubmit();
		// with the word save
		$objSearhButton->setValue('Edit');
		$table->width = '80%';
		$table->startHeaderRow();
		$table->addHeaderCell('Question #');
		$table->addHeaderCell('Question');
		$table->addHeaderCell('Delete');
        $table->addHeaderCell('Edit');
        $table->endHeaderRow();
		//$table->addHeaderCell('&nbsp;');
		
		//echo $view_fbtable->show();
		$editUrl = $this->uri(array(
    		'action' => 'edit',
    		//'id' => $data['id']
		));
		$editLink = $objIcon->getEditIcon($editUrl);
         
        for($i = 0; $i < count($questions); $i ++){
                $deleteArray= array('action' => 'delete_question','delete_id' => $questions[$i]['puid']);
                $deleteIcon=$objIcon->getDeleteIconWithConfirm('', $deleteArray,'feedback','Are you sure you want to delete this question?
');
               // $editLink = $objIcon->getEditIcon($editUrl);
                $tableRow  = array(($i + 1),$questions[$i]['fb_question'], $deleteIcon, $editLink);
                $table->addRow($tableRow, ($i + 1));
        }
        //$objCheck = new checkbox('mycheckbox', null, true);
        //$objCheck = new checkbox('lecturers',NULL,true);
       // echo  "here is the checkbox ->".$objCheck->show();
        $objForm_add_questions = new form('question_to_save_form', $editUrl);
        if(count($questions) == 0){
             $editUrl = $this->uri(array(
    		'action' => 'insert_questions'
    		//'id' => 
		     ));
             $editLink = $objIcon->getEditIcon($editUrl);
             $tableRow  = array(0,"The Questions table is empty.  Please select the edit button to add questions",'', $editLink);
             $table->addRow($tableRow, 0);
             
        }
        else{
             $objForm_save_questions = new form('question_to_save_form', $editUrl);
             $objForm_save_questions ->displayType= 1; 
             $editUrl = $this->uri(array(
    		'action' => 'insert_questions'
    		//'id' => 
		     ));
            $editLink = $objIcon->getEditIcon($editUrl);
           // $tableRow  = array(count($questions),"Plese select the edit button to add new questions", $editLink);
            //$table->addRow($tableRow, count($questions));
            $objForm_add_questions = new form('question_to_save_form', $editUrl);
            $objForm_add_questions ->displayType= 1; 
            $objElement = new button('submit');
		    // Set the button type to submit
		    $objElement->setToSubmit();
		    // with the word save
		    $objElement->setValue('Add question');
		    //Add the comment element to the form
		    $objForm_add_questions->addToForm($objElement->show());	
        }
        if($insert_questions == true){
            $objForm_save_questions = new form('question_to_save_form', $editUrl);
            $objForm_save_questions ->displayType= 1; 
            $objHidden_field = new textinput('question_number', $question_number);
            $objHidden_field->fldType = 'hidden';
            $objForm_save_questions->addToForm($objHidden_field->show());
            
            //echo "insert_questions = ".$insert_questions;
            $question_label = "Question # ".$question_number.": ";
            $objForm_save_questions->addToForm($question_label);
            
		   
            //echo $objForm_save_questions->show();
		    $objQuestion = new textArea('question_to_save', "Fill your question here");
		    $objForm_save_questions->addToForm($objQuestion->show()."<br/>");
            
            $objElement = new button('submit');
		    // Set the button type to submit
            $objElement->setToSubmit();
		    // with the word save
            $objElement->setValue(' SAVE ');
		    //Add the comment element to the form
		    $btnStr = $objElement->show();	

   
            $objElement = new button('cancel');
		    // Set the button type to submit
            $objElement->setToSubmit();
		    // with the word save
            $objElement->setValue(' CANCEL ');
		    $btnStr .= '&nbsp;'.$objElement->show();	
            $objForm_save_questions->addToForm($btnStr);

            $objFbFeaturebox = $this->getObject('featurebox', 'navigation');
            $mod_pane =  $objFbFeaturebox->show("Please enter your question in the text area below and click 'SAVE' to save",$objForm_save_questions->show());
            $tabBox->addTab(array('name'=> "Edit Questions",'content' =>$mod_pane));
        }
        else {
            $objFbFeaturebox = $this->getObject('featurebox', 'navigation');
            $edit_pane =  $objFbFeaturebox->show('Modify or add questions',$table->show().$objForm_add_questions->show());
            //echo $edit_pane;
		    $tabBox->addTab(array('name'=> "Edit Questions",'content' =>$edit_pane));
        }	

	}
//-----------------------------------------------------------------------------------------------------------------
    $results_tables = '';
    if($return_arr!= null){
        
        $view_fbtable->width = '80%';
		$view_fbtable->startHeaderRow();
		$view_fbtable->addHeaderCell('Number');
		$view_fbtable->addHeaderCell('Question');
		$view_fbtable->addHeaderCell('Response');
        //$view_fbtable->addHeaderCell('Action');
        $view_fbtable->endHeaderRow();
		//$table->addHeaderCell('&nbsp;');
        $editUrl = $this->uri(array(
    		'action' => 'edit',
    		//'id' => $data['id']
		));
		$editLink = $objIcon->getEditIcon($editUrl);
		//$objIcon->title = $listLabel;
		//$objIcon->setIcon('edit');
		//echo $questions[0]['question'];
        $length = count($return_arr);
        //echo "length = ".$length;
        //$response = "question_# ";
        $tmp_email = ''; $tmp_name = '';
        $tmp_resp = '';
        $resp_table = $this->newObject('htmltable', 'htmlelements');
        $resp_table->width = '80%';
        $cnt = 0;
        $table_display =  $this->newObject('htmltable', 'htmlelements');
        $table_display->width= '80%';
       // $header = 'Name: '.$return_arr[0]['name'].'Email: '.$return_arr[0]['email'];
        $objFbFeaturebox = $this->getObject('featurebox', 'navigation');
        $table_disp_started = false;
        for($i = 0; $i < $length + 1;$i++){
	         //$tableRow_ret = array($i,$return_arr[$i]['fbname'] , $editLink);
             $cnt++;
           //  echo 'tmp name = '.$temp_name.'  tmp_email = '.$tmp_email.'<br/>'; 
             $name = $return_arr[$i]['name']; $email = $return_arr[$i]['email'];
             $date = $return_arr[$i]['modified'];
           
              //$view_fbtable  = $this->newObject('htmltable', 'htmlelements');
           //echo  'resp_id->'.$return_arr[$i]['resp_id'];
         
            //if($tmp_email == $email && $tmp_name == $name) ;//echo "current = = previous";
            if($tmp_resp == $return_arr[$i]['resp_id']) ;
            else {
                 // $resp_table = $this->newObject('htmltable', 'htmlelements');
                  //echo 'name = '.$return_arr[$i]['name'].'  email = '.$return_arr[$i]['email'].'<br/>';
                  $cnt = 1;
                  //$header = 'Name: '.$return_arr[$i]['name'].'Email: '.$return_arr[$i]['email'];
                  //$tmp_email = $return_arr[$i]['email']; $tmp_name = $return_arr[$i]['name'];
                  $tmp_resp = $return_arr[$i]['resp_id'];
                  $namelabel = new label('Name: ', 'name_label');
                  $name_label_val = new label($name, 'name_label_val');
                  $date_label = new label('Posted On: ', 'name_label_val');
                  $date_label_val = new label($date, 'posted_on');
                  $tableRow_ret  = array('',$namelabel->show(), $name_label_val->show(), '', '',$date_label->show(), $date_label_val->show());
                  $resp_table = $this->newObject('htmltable', 'htmlelements');
                  $resp_table->addRow($tableRow_ret, $i);
                  $view_fbtable->addRow($tableRow_ret, $i);

                  $namelabel = new label('Email: ', 'name_label');
                  $name_label_val = new label($email, 'email_label_val');

                  $tableRow_ret  = array('',$namelabel->show(), $name_label_val->show());
                  $view_fbtable->addRow($tableRow_ret, $i);
                  $resp_table->addRow($tableRow_ret, $i);
                  //echo  $objFbFeaturebox->show($header, $table_display->show());
                 // if ($table_disp_started == true)
                  $results_tables =  $results_tables.$objFbFeaturebox->show($header, $table_display->show());
                  //echo $table_display->show();
                  $table_display =  $this->newObject('htmltable', 'htmlelements');
                  $table_displa->width = '80%';
                  $table_display->cellpadding = '5';
                  $table_display->cellspacing = '5';
		          $table_display->startHeaderRow();
		          $table_display->addHeaderCell('Number');
		          $table_display->addHeaderCell('Question');
		          $table_display->addHeaderCell('Response');
                  $table_display->endHeaderRow();
                  //$table_disp_started = true;
                  $header = $resp_table->show();
                  //$body = $view_fbtable->show();
                  //$view_fbtable=$this->newObject('htmltable', 'htmlelements');
                  
                    
             }
             //echo $i.'body = '.$body;
             //$objFbFeaturebox = $this->getObject('featurebox', 'navigation');
             //echo $objFbFeaturebox->show($resp_table->show(), $body);
             $tableRow_ret  = array($cnt,$return_arr[$i]['fb_question'], $return_arr[$i]['fb_response'] );
             $view_fbtable->addRow($tableRow_ret, $i); 
             $table_display->addRow($tableRow_ret, $i);
                 
        }
        $tabBox->addTab(array('name'=> $objLanguage->languageText("mod_feedback_View_Feed_Back_Comments", "feedback"),'content' => $view_fbtable->show()));	
        //$objFbFeaturebox = $this->getObject('featurebox', 'navigation');
        //echo $objFbFeaturebox->show('my Header', 'my body');
        $results_tables = $results_tables.'<br/>';
    }
//-----------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------
//Add the comment element to the form
$objViewForm->addToForm($startField);
$objSearhButton = new button('submit');
// Set the button type to submit
$objSearhButton->setToSubmit();
// with the word save

$objSearhButton->setValue($objLanguage->languageText("mod_feedback_search", "feedback"));
$objViewForm->addToForm("<br/>");
$objViewForm->addToForm($objSearhButton->show());

$objFbFeaturebox = $this->getObject('featurebox', 'navigation');
$view_question_pane =  $objFbFeaturebox->show('Select a date on the popup calendar to view feedback comments',$results_tables.$objViewForm->show());
$tabBox->addTab(array('name'=> $objLanguage->languageText("mod_feedback_View_Feed_Back_Comments", "feedback"),'content' =>$view_question_pane));

//-----------------------------------------------------------------------------------------------------------------
//print_r($insarr);

//-----------------------------------------------------------------------------------------------------------------

$tabBox->addTab(array('name'=> $objLanguage->languageText("mod_feedback_Feed_Back_Board", "feedback"),'content' =>$this->objFb->dfbform($insarr)));
$middleColumn = $tabBox->show();
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); 
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>

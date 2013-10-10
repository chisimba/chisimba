<?php
// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

 /**
 * @package survey
 * Class for generating questions
 * @access public
 * @author Kevin Cyster
 */

class questions extends object
{
    /**
     * @var object $dbType The dbtype class in the survey module
     * @access private
     */
    private $dbType;

    /**
     * @var object $objLanguage The language class in the language module
     * @access private
     */
    private $objLanguage;

    /**
     * @var object $objHeader The htmlheading class in the htmlelements module
     * @access private
     */
    private $objHeader;

    /**
     * @var object $objTable The htmltable class in the htmlelements module
     * @access private
     */
    private $objTable;

    /**
     * @var object $objInput The textinput class in the htmlelements module
     * @access private
     */
    private $objInput;

    /**
     * @var object $objText The textarea class in the htmlelements module
     * @access private
     */
    private $objText;

    /**
     * @var object $objCheck The checkbox class in the htmlelements module
     * @access private
     */
    private $objCheck;

    /**
     * @var object $objRadio The radio class in the htmlelements module
     * @access private
     */
    private $objRadio;

    /**
     * @var object $objDrop The dropdown class in the htmlelements module
     * @access private
     */
    private $objDrop;

    /**
     * @var object $objButton The button class in the htmlelements module
     * @access private
     */
    private $objButton;

    /**
     * @var object $objForm The form class in the htmlelements module
     * @access private
     */
    private $objForm;

    /**
     * @var object $objLink The link class in the htmlelements module
     * @access private
     */
    private $objLink;

    /**
     * @var object $objIcon The geticon class in the htmlelements module
     * @access private
     */
    private $objIcon;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    function init()
    {
        // set up db object
        $this->dbType=&$this->newObject('dbtype');
        // set up language elements
        $this->objLanguage=&$this->newObject('language','language');
        // set up htmlelements
        $this->objHeader=&$this->loadClass('htmlheading','htmlelements');
        $this->objTable=&$this->loadClass('htmltable','htmlelements');
        $this->objInput=&$this->loadClass('textinput','htmlelements');
        $this->objText=&$this->loadClass('textarea','htmlelements');
        $this->objCheck=&$this->loadClass('checkbox','htmlelements');
        $this->objRadio=&$this->loadClass('radio','htmlelements');
        $this->objDrop=&$this->loadClass('dropdown','htmlelements');
        $this->objButton=&$this->loadClass('button','htmlelements');
        $this->objForm=&$this->loadClass('form','htmlelements');
        $this->objLink=&$this->loadClass('link','htmlelements');
        $this->objIcon=&$this->newObject('geticon','htmlelements');
    }

    /**
    * Method to return the questions types table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrTypeDropdown The question type data array
    */
    public function arrTypeDropdown($arrQuestionData)
    {
        $selectLabel=$this->objLanguage->languageText('mod_survey_type', 'survey');

        $this->objDrop=new dropdown('type_id');
        $arrTypeList=$this->dbType->listQuestionType();
        $this->objDrop->addOption(NULL,$selectLabel);
        foreach($arrTypeList as $type){
            $this->objDrop->addOption($type['id'],$type['question_description']);
        }
        $this->objDrop->setSelected($arrQuestionData['type_id']);
        $this->objDrop->extra=' onchange="javascript:
            document.getElementById(\'input_update\').value=\'update\';
            document.getElementById(\'form_typeupdate\').submit();
        "';
        $questionTypeDrop=$this->objDrop->show();

        $arrTypeDropdown=array($questionTypeDrop);
        return $arrTypeDropdown;
    }

    /**
    * Method to return the questions table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrQuestionTextinput  The question textinput data array
    */
    public function arrQuestionTextinput($arrQuestionData)
    {
        $questionDescLabel=$this->objLanguage->languageText('mod_survey_text', 'survey');
        $subtextLabel=$this->objLanguage->languageText('mod_survey_subtext', 'survey');

        $this->objText=new textarea('question_text',$arrQuestionData['question_text'],'3','85');
        $questionArea=$this->objText->show();

        $this->objText=new textarea('question_subtext',$arrQuestionData['question_subtext'],'3','85');
        $subtextArea=$this->objText->show();

        $arrQuestionTextinput=array($questionDescLabel,$questionArea,$subtextLabel,$subtextArea);
        return $arrQuestionTextinput;
    }

    /**
    * Method to return the options table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrRequiredCheckbox The compulsory checkbox data array
    */
    public function arrRequiredCheckbox($arrQuestionData)
    {
        $requiredLabel=$this->objLanguage->languageText('mod_survey_required', 'survey');

        $this->objCheck=new checkbox('compulsory_question');
        if($arrQuestionData['compulsory_question']=='1'){
            $this->objCheck->ischecked=TRUE;
        }
        $this->objCheck->value='1';
        $requiredCheck=$this->objCheck->show();

        $arrRequiredCheckbox=array($requiredCheck.' '.$requiredLabel);
        return $arrRequiredCheckbox;
    }

    /**
    * Method to return the alignment table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrAlignmentRadio The alignment radio data array
    */
    public function arrAlignmentRadio($arrQuestionData)
    {
        $alignmentLabel=$this->objLanguage->languageText('mod_survey_alignment', 'survey');
        $verticalLabel=$this->objLanguage->languageText('mod_survey_vertical', 'survey');
        $horizontalLabel=$this->objLanguage->languageText('mod_survey_horizontal', 'survey');

        $this->objRadio=new radio('vertical_alignment');
        $this->objRadio->addOption(0,$verticalLabel);
        $this->objRadio->addOption(1,$horizontalLabel);
        $this->objRadio->setSelected($arrQuestionData['vertical_alignment']);
        $this->objRadio->setBreakSpace('<br />');
        $alignRadio=$this->objRadio->show();

        $arrAlignmentRadio=array($alignmentLabel,$alignRadio);
        return $arrAlignmentRadio;
    }

    /**
    * Method to return the answer rows table data
    *
    * @access public
    * @param array $arrRowData The array with row data
    * @return array $arrRowTable  The answer rows table data array
    */
    public function arrRowTable($arrRowData)
    {
        $rowLabel=$this->objLanguage->languageText('mod_survey_row', 'survey');
        $answerLabel=$this->objLanguage->languageText('mod_survey_answer', 'survey');
        $rowtextLabel=$this->objLanguage->languageText('mod_survey_rowtext', 'survey');

        $array=array();
        $array=array('item'=>strtolower($rowLabel));
        $addRowLabel=$this->objLanguage->code2Txt('mod_survey_add', 'survey',$array);
        $deleteRowLabel=$this->objLanguage->code2Txt('mod_survey_delete', 'survey',$array);
        $deleteconfirmLabel=$this->objLanguage->code2Txt('mod_survey_deleteconfirm','survey',$array);

        $this->objIcon->title=$addRowLabel;
        $this->objIcon->setIcon('add');
        $this->objIcon->extra=' onclick="javascript:
            var el=document.getElementsByName(\'update\');
            if(el.length==1){
                el[0].value=\'addrow\';
            }else{
                el[1].value=\'addrow\';
            };
            document.getElementById(\'form_questionForm\').submit();
        "';
        $addIcon=$this->objIcon->show();

        $arrRowTable=array($rowtextLabel.' <a href="#">'.$addIcon.'</a>');

        $i=1;
        foreach($arrRowData as $rowKey=>$row){
            $rowId=$row['id'];
            $rowOrder=$row['row_order'];

            $str=$i.'.';

            $this->objInput=new textinput('arrRowId[]',$row['id']);
            $this->objInput->fldType='hidden';
            $idText=$this->objInput->show();

            $this->objInput=new textinput('arrRowNo[]',$row['row_order']);
            $this->objInput->fldType='hidden';
            $noText=$this->objInput->show();

            $this->objInput=new textinput('arrRowText[]',$row['row_text']);
            $this->objInput->size=82;
            $textText=$this->objInput->show();

            if(count($arrRowData)>'2'){
                $deleteRow='deleterow_'.$rowKey;

                $this->objIcon->title=$deleteRowLabel;
                $this->objIcon->setIcon('delete');
                $this->objIcon->extra=' onclick="javascript:
                    if(confirm(\''.$deleteconfirmLabel.'\')){
                        var el=document.getElementsByName(\'update\');
                        if(el.length==1){
                            el[0].value=\''.$deleteRow.'\';
                        }else{
                            el[1].value=\''.$deleteRow.'\';
                        };
                        document.getElementById(\'form_questionForm\').submit();
                    }
                "';
                $deleteIcon=$this->objIcon->show();
            }else{
                $deleteIcon='';
            }

            $str.='    '.$idText.$noText.$textText.' <a href="#">'.$deleteIcon.'</a>';

            $arrRowTable[]=$str;

            $i++;
        }
        return $arrRowTable;
    }

    /**
    * Method to return the answer columns table data
    *
    * @access public
    * @param array $arrColumnData The array with column data
    * @return array $arrColumnTable  The answer column table data array
    */
    public function arrColumnTable($arrColumnData)
    {
        $columnLabel=$this->objLanguage->languageText('mod_survey_column', 'survey');
        $answerLabel=$this->objLanguage->languageText('mod_survey_answer', 'survey');
        $columntextLabel=$this->objLanguage->languageText('mod_survey_columntext', 'survey');

        $array=array();
        $array=array('item'=>strtolower($columnLabel));
        $addColumnLabel=$this->objLanguage->code2Txt('mod_survey_add', 'survey',$array);
        $deleteColumnLabel=$this->objLanguage->code2Txt('mod_survey_delete', 'survey',$array);
        $deleteconfirmLabel=$this->objLanguage->code2Txt('mod_survey_deleteconfirm', 'survey',$array);

        $this->objIcon->title=$addColumnLabel;
        $this->objIcon->setIcon('add');
        $this->objIcon->extra=' onclick="javascript:
            var el=document.getElementsByName(\'update\');
            if(el.length==1){
                el[0].value=\'addcolumn\';
            }else{
                el[1].value=\'addcolumn\';
            }
            document.getElementById(\'form_questionForm\').submit();
        "';
        $addIcon=$this->objIcon->show();

        $arrColumnTable=array($columntextLabel.' <a href="#">'.$addIcon.'</a>');

        $i=1;
        foreach($arrColumnData as $columnKey=>$column){
            $columnId=$column['id'];

            $str=$i.'.';

            $this->objInput=new textinput('arrColumnId[]',$column['id']);
            $this->objInput->fldType='hidden';
            $idText=$this->objInput->show();

            $this->objInput=new textinput('arrColumnNo[]',$column['column_order']);
            $this->objInput->fldType='hidden';
            $noText=$this->objInput->show();

            $this->objInput=new textinput('arrColumnText[]',$column['column_text']);
            $this->objInput->size=82;
            $textText=$this->objInput->show();

            if(count($arrColumnData)>'2'){
                $deleteColumn='deletecolumn_'.$columnKey;

                $this->objIcon->title=$deleteColumnLabel;
                $this->objIcon->setIcon('delete');
                $this->objIcon->extra=' onclick="javascript:
                    if(confirm(\''.$deleteconfirmLabel.'\')){
                        var el=document.getElementsByName(\'update\');
                        if(el.length==1){
                            el[0].value=\''.$deleteColumn.'\';
                        }else{
                            el[1].value=\''.$deleteColumn.'\';
                        }
                        document.getElementById(\'form_questionForm\').submit();
                    }
                "';
                $deleteIcon=$this->objIcon->show();
            }else{
                $deleteIcon='';
            }

            $str.='    '.$idText.$noText.$textText.'<a href="#">'.$deleteIcon.'</a>';

            $arrColumnTable[]=$str;

            $i++;
        }
        return $arrColumnTable;
    }

    /**
    * Method to return the other table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrCommentsCheckbox  The comments table data array
    */
    public function arrCommentsCheckbox($arrQuestionData)
    {
        $commentsLabel=$this->objLanguage->languageText('mod_survey_comments', 'survey');
        $commentsTextLabel=$this->objLanguage->languageText('mod_survey_commentstext', 'survey');

        $this->objCheck=new checkbox('comment_requested');
        if($arrQuestionData['comment_requested']=='1'){
            $this->objCheck->ischecked=TRUE;
        }
        $this->objCheck->value='1';
        $this->objCheck->extra=' onclick="javascript:
            if(document.getElementById(\'input_comment_requested\').checked){
                document.getElementById(\'input_comment_request_text\').type=\'text\';
                document.getElementById(\'input_comment_request_text\').select();
            }else{
                document.getElementById(\'input_comment_request_text\').type=\'hidden\'
            }
        "';
        $commentCheck=$this->objCheck->show();

        if(empty($arrQuestionData['comment_request_text'])){
            if($arrQuestionData['comment_requested']!='1'){
                $this->objInput=new textinput('comment_request_text',$commentsTextLabel,'hidden');
            }else{
                $this->objInput=new textinput('comment_request_text',$commentsTextLabel);
            }
        }else{
            if($arrQuestionData['comment_requested']!='1'){
                $this->objInput=new textinput('comment_request_text',$arrQuestionData['comment_request_text'],'hidden');
            }else{
                $this->objInput=new textinput('comment_request_text',$arrQuestionData['comment_request_text']);
            }
        }
        $this->objInput->size=85;
        $commentText=$this->objInput->show();

        $arrCommentsCheckbox=array($commentCheck.' '.$commentsLabel,$commentText);
        return $arrCommentsCheckbox;
    }

    /**
    * Method to return the element choice table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrRadioElement The html element choice table data array
    */
    public function arrRadioElement($arrQuestionData)
    {
        $choicetextLabel=$this->objLanguage->languageText('mod_survey_choicetext', 'survey');
        $choiceDropdownLabel=$this->objLanguage->languageText('mod_survey_choicedropdown', 'survey');
        $choiceRadioLabel=$this->objLanguage->languageText('mod_survey_choiceradio', 'survey');
        $choiceSingleLabel=$this->objLanguage->languageText('mod_survey_choicesingle', 'survey');
        $choiceMultipleLabel=$this->objLanguage->languageText('mod_survey_choicemultiple', 'survey');

        $this->objRadio=new radio('radio_element');
        if($arrQuestionData['type_id']=='init_2'){
            $this->objRadio->addOption(0,$choiceDropdownLabel);
            $this->objRadio->addOption(1,$choiceRadioLabel);
        }else{
            $this->objRadio->addOption(0,$choiceSingleLabel);
            $this->objRadio->addOption(1,$choiceMultipleLabel);
        }
        $this->objRadio->setBreakSpace('<br />');
        $this->objRadio->setSelected($arrQuestionData['radio_element']);
        if($arrQuestionData['type_id']=='init_2'){
            $this->objRadio->extra=' onchange="javascript:
                var el=document.getElementsByName(\'update\');
                if(el.length==1){
                    el[0].value=\'update\';
                }else{
                    el[1].value=\'update\';
                }
                document.getElementById(\'form_questionForm\').submit();
            "';
        }
        $elementChoiceRadio=$this->objRadio->show();

        $arrRadioElement=array($choicetextLabel,$elementChoiceRadio);
        return $arrRadioElement;
    }

    /**
    * Method to return the option choice table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @param string $mode The mode of the template (add or edit)
    * @return array $arrOptionType  The option type choice table data array
    */
    public function arrPresetOptions($arrQuestionData,$mode)
    {
        $optionsLabel=$this->objLanguage->languageText('mod_survey_options', 'survey');
        $multipleLabel=$this->objLanguage->languageText('mod_survey_multipleoptions', 'survey');
        $booleanLabel=$this->objLanguage->languageText('mod_survey_booleanoptions', 'survey');

        $this->objRadio=new radio('preset_options');
        $this->objRadio->addOption(0,$multipleLabel);
        $this->objRadio->addOption(1,$booleanLabel);
        $this->objRadio->setBreakSpace('<br />');
        $this->objRadio->setSelected($arrQuestionData['preset_options']);
        $this->objRadio->extra=' onchange="javascript:
            var el=document.getElementsByName(\'update\');
            if(el.length==1){
                el[0].value=\'update\';
            }else{
                el[1].value=\'update\';
            }
            document.getElementById(\'form_questionForm\').submit();
        "';

        if($mode=='edit'){
            $this->objRadio->extra=' disabled="true"';
            $optionRadio=$this->objRadio->show();

            $this->objInput=new textinput('preset_options',$arrQuestionData['preset_options']);
            $this->objInput->fldType='hidden';
            $optionText=$this->objInput->show();
            $arrOptionType=array($optionsLabel,$optionRadio,$optionText);
        }else{
            $optionRadio=$this->objRadio->show();

            $arrOptionType=array($optionsLabel,$optionRadio);
        }

        return $arrOptionType;
    }

    /**
    * Method to return the choice table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrBooleanType  The boolean type choice table data array
    */
    public function arrBooleanType($arrQuestionData)
    {
        $selectLabel=$this->objLanguage->languageText('mod_survey_selectboolean', 'survey');
        $trueOrFalseLabel=$this->objLanguage->languageText('mod_survey_trueorfalse', 'survey');
        $yesOrNoLabel=$this->objLanguage->languageText('mod_survey_yesorno', 'survey');

        $this->objRadio=new radio('true_or_false');
        $this->objRadio->addOption(0,$trueOrFalseLabel);
        $this->objRadio->addOption(1,$yesOrNoLabel);
        $this->objRadio->setBreakSpace('<br />');
        $this->objRadio->setSelected($arrQuestionData['true_or_false']);
        $optionRadio=$this->objRadio->show();

        $arrBooleanType=array($selectLabel,$optionRadio);
        return $arrBooleanType;
    }

    /**
    * Method to return the rating table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrRatingDropdown  The rating data array
    */
    public function arrRatingDropdown($arrQuestionData)
    {
        $rateLabel=$this->objLanguage->languageText('mod_survey_rate', 'survey');
        $scaleLabel=$this->objLanguage->languageText('mod_survey_scale', 'survey');

        $this->objDrop=new dropdown('rating_scale');
        for($i=2;$i<=10;$i++){
            $this->objDrop->addOption($i,$i);
        }
        $this->objDrop->setSelected($arrQuestionData['rating_scale']);
        $rateDrop=$this->objDrop->show();

        $arrRatingDropdown=array($rateLabel,$rateDrop);
        return $arrRatingDropdown;
    }

    /**
    * Method to return the constant sum table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrSumTextinput  The constant sum data array
    */
    public function arrSumTextinput($arrQuestionData)
    {
        $sumLabel=$this->objLanguage->languageText('mod_survey_sum', 'survey');

        $this->objInput=new textinput('constant_sum',$arrQuestionData['constant_sum']);
        $this->objInput->size=4;
        $this->objInput->extra='maxlength="4"';
        $sumText=$this->objInput->show();

        $arrSumTextinput=array($sumLabel,$sumText);
        return $arrSumTextinput;
    }

    /**
    * Method to return the minimum number table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrMinimumTextinput The number data array
    */
    public function arrMinimumTextinput($arrQuestionData)
    {
        $minnoLabel=$this->objLanguage->languageText('mod_survey_minno', 'survey');

        $this->objInput=new textinput('minimum_number',$arrQuestionData['minimum_number']);
        $this->objInput->size=7;
        $this->objInput->extra='maxlength="7"';
        $minNo=$this->objInput->show();

        $arrMinimumTextinput=array($minnoLabel,$minNo);
        return $arrMinimumTextinput;
    }

    /**
    * Method to return the maximum number table data
    *
    * @access public
    * @param array $arrQuestionData The array with question data
    * @return array $arrMaximumTextinput The number data array
    */
    public function arrMaximumTextinput($arrQuestionData)
    {
        $maxnoLabel=$this->objLanguage->languageText('mod_survey_maxno', 'survey');

        $this->objInput=new textinput('maximum_number',$arrQuestionData['maximum_number']);
        $this->objInput->size=7;
        $this->objInput->extra='maxlength="7"';
        $maxNo=$this->objInput->show();

        $arrMaximumTextinput=array($maxnoLabel,$maxNo);
        return $arrMaximumTextinput;
    }

    /**
    * Method to display data in a table
    *
    * @access public
    * @param array $array  The data to display
    * @return string $table  The display table
    */
    public function makeTable($array)
    {
        $this->objTable=new htmltable();
        $this->objTable->cellspacing='2';
        $this->objTable->cellpadding='2';

        foreach($array as $element){
            $this->objTable->startRow();
            $this->objTable->addCell($element,'','','','even','');
            $this->objTable->endRow();
        }
        $table=$this->objTable->show();
        return $table;
    }

    /**
    * Method to display results bar
    *
    * @access public
    * @param integer $number The percent to display
    * @param integer $label The label for the bar
    * @param string $colour The colour of the bar
    * @return The bar icon
    */
    public function bar($percentage,$label,$colour=NULL,$long=TRUE)
    {
        switch($colour){
            case 'blue':
                $this->objIcon->setIcon('bar_left');
                break;

            case 'green':
                $this->objIcon->setIcon('bar_left_green');
                break;

            case 'red':
                $this->objIcon->setIcon('bar_left_red');
                break;

            default:
                $this->objIcon->setIcon('bar_left');
                break;
        }
        $this->objIcon->title=$label;
        $barIcon=$this->objIcon->show();
        if($long){
            $percentage=$percentage*2;
        }
        for($i=1;$i<=$percentage;$i++){
            switch($colour){
                case 'blue':
                    $this->objIcon->setIcon('bar');
                    break;

                case 'green':
                    $this->objIcon->setIcon('bar_green');
                    break;

                case 'red':
                    $this->objIcon->setIcon('bar_red');
                    break;

                default:
                    $this->objIcon->setIcon('bar');
                    break;
            }
            $this->objIcon->title=$label;
            $barIcon.=$this->objIcon->show();
        }
        switch($colour){
            case 'blue':
                $this->objIcon->setIcon('bar_right');
                break;

            case 'green':
                $this->objIcon->setIcon('bar_right_green');
                break;

            case 'red':
                $this->objIcon->setIcon('bar_right_red');
                break;

            default:
                $this->objIcon->setIcon('bar_right');
                break;
        }
        $this->objIcon->title=$label;
        $barIcon.=$this->objIcon->show();
        return $barIcon;
    }
}
?>
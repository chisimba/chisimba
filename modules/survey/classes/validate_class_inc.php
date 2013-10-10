<?php
// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

 /**
 * @package survey
 * Class to validate templates
 * @access public
 * @author Kevin Cyster
 */

class validate extends object
{
    /**
     * @var object $objLanguage The language class in the language module
     * @access private
     */
    private $objLanguage;

    /**
     * @var object $session The session class in the survey module
     * @access private
     */
    private $session;

    /**
     * @var object $dbQuestion The dbquestion class in the survey module
     * @access private
     */
    private $dbQuestion;

    /**
     * @var object $dbRows The dbquestionrow class in the survey module
     * @access private
     */
    private $dbRows;

    /**
     * @var object $dbColumns The dbquestioncol class in the survey module
     * @access private
     */
    private $dbColumns;

    /**
     * @var object $dbPages The dbpages class in the survey module
     * @access private
     */
    private $dbPages;

    /**
     * @var object $dbPageQuestions The dbpagequestions class in the survey module
     * @access private
     */
    private $dbPageQuestions;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        $this->objLanguage=&$this->newObject('language','language');
        $this->session=&$this->newObject('surveysession');
        $this->dbQuestion=&$this->newObject('dbquestion');
        $this->dbRows=&$this->newObject('dbquestionrow');
        $this->dbColumns=&$this->newObject('dbquestioncol');
        $this->dbPages=&$this->newObject('dbpages');
        $this->dbPageQuestions=&$this->newObject('dbpagequestions');
    }

    /**
    * Method to validate the survey data
    *
    * @access public
    * @return boolean $valid A variable showing if the validation is successful
    */
    public function checkSurveyData()
    {
        // set up error message variables
        $surveyname=$this->objLanguage->languageText('mod_survey_surveyname', 'survey');
        $maxresponse=$this->objLanguage->languageText('mod_survey_maximumresponse' ,'survey');
        $startdate=$this->objLanguage->languageText('mod_survey_startdate', 'survey');
        $enddate=$this->objLanguage->languageText('mod_survey_enddate', 'survey');
        $currentdate=$this->objLanguage->languageText('mod_survey_currentdate', 'survey');
        $introhead=$this->objLanguage->languageText('mod_survey_introductionheading', 'survey');
        $thankshead=$this->objLanguage->languageText('mod_survey_thankyouheading', 'survey');
        $intro=$this->objLanguage->languageText('mod_survey_introductionnote', 'survey');
        $thanks=$this->objLanguage->languageText('mod_survey_thankyounote', 'survey');

        // get survey data from the session
        $arrSurveyData=$this->getSession('survey');

        $valid=TRUE;
        foreach($arrSurveyData as $field=>$value){
            if($field=='survey_name'||$field=='start_date'||$field=='end_date'||$field=='max_responses'||$field=='intro_label'||$field=='intro_text'||$field=='thanks_label'||$field=='thanks_text'){
                if($value===""){
                    $valid=FALSE;
                    if($field=='survey_name'){
                        $array=array('fieldname'=>$surveyname);
                    }elseif($field=='start_date'){
                        $array=array('fieldname'=>$startdate);
                    }elseif($field=='end_date'){
                        $array=array('fieldname'=>$enddate);
                    }elseif($field=='max_responses'){
                        $array=array('fieldname'=>$maxresponse);
                    }elseif($field=='intro_label'){
                        $array=array('fieldname'=>$introhead);
                    }elseif($field=='intro_text'){
                        $array=array('fieldname'=>$intro);
                    }elseif($field=='thanks_label'){
                        $array=array('fieldname'=>$thankshead);
                    }else{
                        $array=array('fieldname'=>$thanks);
                    }
                    $errMsg[$field]=$this->objLanguage->code2Txt('mod_survey_error_1','survey',$array);
                }
            }
            if($field=='start_date'){
                $date=strtotime(date("Y-m-d"));
                $start=strtotime($value);
                if($start<$date){
                    $valid=FALSE;
                    $array=array('fieldname_1'=>$startdate,'fieldname_2'=>strtolower($currentdate));
                    $errMsg[$field]=$this->objLanguage->code2Txt('mod_survey_error_4','survey',$array);
                }
            }
            if($field=='end_date'){
                $date=strtotime(date("Y-m-d"));
                $start=strtotime($arrSurveyData['start_date']);
                $end=strtotime($value);
                if($end<=$date){
                    $valid=FALSE;
                    $array=array('fieldname_1'=>$enddate,'fieldname_2'=>strtolower($currentdate));
                    $errMsg[$field]=$this->objLanguage->code2Txt('mod_survey_error_5','survey',$array);
                }elseif($end<=$start){
                    $valid=FALSE;
                    $array=array('fieldname_1'=>$enddate,'fieldname_2'=>strtolower($startdate));
                    $errMsg[$field]=$this->objLanguage->code2Txt('mod_survey_error_5','survey',$array);
                }
            }
            if($field=='max_responses'){
                if($value!==""){
                    if(!is_numeric($value)){
                        $valid=FALSE;
                        $array=array('fieldname'=>$maxresponse);
                        $errMsg[$field]=$this->objLanguage->code2Txt('mod_survey_error_2','survey',$array);
                    }elseif($value<=0){
                        $valid=FALSE;
                        $array=array('fieldname'=>$maxresponse);
                        $errMsg[$field]=$this->objLanguage->code2Txt('mod_survey_error_3','survey',$array);
                    }
                }
            }
        }
        if(!$valid){
            $this->session->addErrorData($errMsg);
            return $valid;
        }else{
            return $valid;
        }
    }

    /**
    * Method to validate the question data
    *
    * @access public
    * @return boolean $valid A variable showing if the validation is successful
    */
    public function checkQuestionData()
    {
        // set up error message variables
        $question=$this->objLanguage->languageText('mod_survey_question','survey');
        $commentbox=$this->objLanguage->languageText('mod_survey_commentboxtext','survey');
        $changecomment=$this->objLanguage->languageText('mod_survey_error_6','survey');
        $rows=$this->objLanguage->languageText('mod_survey_rows','survey');
        $columns=$this->objLanguage->languageText('mod_survey_columns','survey');
        $constantsum=$this->objLanguage->languageText('mod_survey_constantsum','survey');
        $minimumnumber=$this->objLanguage->languageText('mod_survey_minimumnumber','survey');
        $maximumnumber=$this->objLanguage->languageText('mod_survey_maximumnumber','survey');

        // get survey data from the session
        $arrQuestionData=$this->getSession('question');
        $arrRowData=$this->getSession('row');
        $arrColumnData=$this->getSession('column');
        // set up data
        $typeId=$arrQuestionData['type_id'];
        $booleanType=$arrQuestionData['preset_options'];

        $valid=TRUE;
        // checks the question text
        if($arrQuestionData['question_text']==''){
            $valid=FALSE;
            $array=array('fieldname'=>$question);
            $errMsg['question_text']=$this->objLanguage->code2Txt('mod_survey_error_1','survey',$array);
        }

        // checks the comment box option
        if($arrQuestionData['comment_requested']=='1'){
            if($arrQuestionData['comment_request_text']=='Enter the text to go above the comments box' || $arrQuestionData['comment_request_text']==''){
                $valid=FALSE;
                $errMsg['comment_request_text']=$changecomment;
            }
        }

        // checks questions with rows
        if($typeId!='init_7' && $typeId!='init_9' && $typeId!='init_10' && $booleanType!='1'){
            foreach($arrRowData as $row){
                if($row['row_text']==''){
                    $valid=FALSE;
                    $array=array('fieldname'=>$rows);
                    $errMsg['rows']=$this->objLanguage->code2Txt('mod_survey_error_1','survey',$array);
                }
            }
        }

        // checks questions with columns
        if($typeId=='init_3' || $typeId=='init_4' || $typeId=='init_5'){
            foreach($arrColumnData as $column){
                if($column['column_text']==''){
                    $valid=FALSE;
                    $array=array('fieldname'=>$columns);
                    $errMsg['columns']=$this->objLanguage->code2Txt('mod_survey_error_1','survey',$array);
                }
            }
        }

        // checks the constant sum value
        if($typeId=='init_8'){
            if(!is_numeric($arrQuestionData['constant_sum']) && $arrQuestionData['constant_sum']!=''){
                $valid=FALSE;
                $array=array('fieldname'=>$constantsum);
                $errMsg['constant_sum']=$this->objLanguage->code2Txt('mod_survey_error_2','survey',$array);
            }else{
                if($arrQuestionData['constant_sum']<'0' && $arrQuestionData['constant_sum']!=''){
                    $valid=FALSE;
                    $array=array('fieldname'=>$constantsum);
                    $errMsg['constant_sum']=$this->objLanguage->code2Txt('mod_survey_error_7','survey',$array);
                }
            }
        }

        // checks the minimum and maximum numbers
        if($typeId=='init_9'){
            if(!is_numeric($arrQuestionData['minimum_number'])){
                $valid=FALSE;
                $array=array('fieldname'=>$minimumnumber);
                $errMsg['minimum_number']=$this->objLanguage->code2Txt('mod_survey_error_2','survey',$array);
            }else{
                if($arrQuestionData['minimum_number']<'0'){
                    $valid=FALSE;
                    $array=array('fieldname'=>$minimumnumber);
                    $errMsg['minimum_number']=$this->objLanguage->code2Txt('mod_survey_error_7','survey',$array);
                }
            }
            if(!is_numeric($arrQuestionData['maximum_number'])){
                $valid=FALSE;
                $array=array('fieldname'=>$maximumnumber);
                $errMsg['maximum_number']=$this->objLanguage->code2Txt('mod_survey_error_2','survey',$array);
            }else{
                if($arrQuestionData['maximum_number']<=$arrQuestionData['minimum_number']){
                    $valid=FALSE;
                    $array=array('fieldname_1'=>$maximumnumber,'fieldname_2'=>strtolower($minimumnumber));
                    $errMsg['maximum_number']=$this->objLanguage->code2Txt('mod_survey_error_5','survey',$array);
                }
            }
        }

        if(!$valid){
            $this->session->addErrorData($errMsg);
            return $valid;
        }else{
            return $valid;
        }
    }

    /**
    * Method to validate the answer data
    *
    * @access public
    * @param string $surveyId The id of the survey
    * @param string $pageNo The number of the page to validate
    * @return boolean $valid A variable showing if the validation is successful
    */
    public function checkAnswerData($surveyId,$pageNo=NULL)
    {
        // set up error messages
        $compulsoryError=$this->objLanguage->languageText('mod_survey_error_8','survey');
        $incompleteError=$this->objLanguage->languageText('mod_survey_error_12','survey');
        $constantSum=$this->objLanguage->languageText('mod_survey_constantsum','survey');
        $answer=$this->objLanguage->languageText('mod_survey_answer','survey');

        $arrPageList=$this->dbPages->listPages($surveyId);
        if(empty($arrPageList)){
            $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
        }else{
            $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
            foreach($arrQuestionList as $key=>$question){
                $arrPageQuestionData=$this->dbPageQuestions->getQuestionRecord($question['id']);
                $arrPageQuestionList[$arrPageQuestionData['0']['page_id']][$arrPageQuestionData['0']['question_order']-1]=$question;
                unset($arrQuestionList[$key]);
            }

            $i=1;
            foreach($arrPageQuestionList as $questionList){
                $arrQuestionList[$i]=$questionList;
                $i++;
            }

            $arrQuestionList=$arrQuestionList[$pageNo];

            foreach($arrQuestionList as $key=>$question){
                unset($arrQuestionList[$key]);
                $arrQuestionList[$question['question_order']-1]=$question;
            }

        }
        $arrAnswerData=$this->getSession('answer');

        $valid=TRUE;
        foreach($arrQuestionList as $key=>$question){
            $questionId=$question['id'];
            $typeId=$question['type_id'];
            $booleanType=$question['preset_options'];
            $htmlElementType=$question['radio_element'];
            $compulsoryQuestion=$question['compulsory_question'];
            $minimumNumber=$question['minimum_number'];
            $maximumNumber=$question['maximum_number'];
            $constantSum=$question['constant_sum'];

            if($typeId!='init_7' && $typeId!='init_9' && $typeId!='init_10' && $booleanType!='1'){
                $arrRowList=$this->dbRows->listQuestionRows($questionId);
            }
            if($typeId=='init_3' || $typeId=='init_4' || $typeId=='init_5'){
                $arrColumnList=$this->dbColumns->listQuestionColumns($questionId);
            }

            switch($typeId){
                case 'init_1': //Choice-Multiple answers-Checkboxes
                    if($compulsoryQuestion=='1'){
                        $responded=FALSE;
                        foreach($arrRowList as $rowKey=>$row){
                            $temp="check_".($rowKey+1);
                            if(isset($arrAnswerData[$key][$temp])){
                                $responded=TRUE;
                            }
                        }
                        if(!$responded){
                            $valid=FALSE;
                            $errMsg[$key][]=$compulsoryError;
                        }
                    }
                    break;

                case 'init_2':// Choice-One answer-Options or dropdown
                    if($compulsoryQuestion=='1'){
                        $responded=FALSE;
                        if($htmlElementType!='1'){
                            if($arrAnswerData[$key]['drop']!='0'){
                                $responded=TRUE;
                            }
                        }else{
                            if(isset($arrAnswerData[$key]['radio'])){
                                $responded=TRUE;
                            }
                        }
                        if(!$responded){
                            $valid=FALSE;
                            $errMsg[$key][]=$compulsoryError;
                        }
                    }
                    break;

                case 'init_3':// Matrix-Multiple answers per row-Checkboxes
                    $started=FALSE;
                    $responded=TRUE;
                    foreach($arrRowList as $rowKey=>$row){
                        $respondedRow=FALSE;
                        foreach($arrColumnList as $columnKey=>$column){
                            $temp="check_".($rowKey+1)."_".($columnKey+1);
                            if(isset($arrAnswerData[$key][$temp])){
                                $respondedRow=TRUE;
                                $started=TRUE;
                            }
                        }
                        if(!$respondedRow){
                            $responded=FALSE;
                        }
                    }
                    if($compulsoryQuestion=='1'){
                        if(!$responded){
                            if($started){
                                $valid=FALSE;
                                $errMsg[$key][]=$incompleteError;
                            }else{
                                $valid=FALSE;
                                $errMsg[$key][]=$compulsoryError;
                            }
                        }
                    }else{
                        if($started && !$responded){
                            $valid=FALSE;
                            $errMsg[$key][]=$incompleteError;
                        }
                    }
                    break;

                case 'init_4':// Matrix-Multiple answers per row textboxes
                    $started=FALSE;
                    $responded=TRUE;
                    foreach($arrRowList as $rowKey=>$row){
                        foreach($arrColumnList as $columnKey=>$column){
                            $temp="text_".($rowKey+1)."_".($columnKey+1);
                            if(!empty($arrAnswerData[$key][$temp])){
                                $started=TRUE;
                            }else{
                                $responded=FALSE;
                            }
                        }
                    }
                    if($compulsoryQuestion=='1'){
                        if(!$responded){
                            if($started){
                                $valid=FALSE;
                                $errMsg[$key][]=$incompleteError;
                            }else{
                                $valid=FALSE;
                                $errMsg[$key][]=$compulsoryError;
                            }
                        }
                    }else{
                        if($started && !$responded){
                            $valid=FALSE;
                            $errMsg[$key][]=$incompleteError;
                        }
                    }
                    break;

                case 'init_5':// Matrix-Multiple answers per row-Options
                    $started=FALSE;
                    $responded=TRUE;
                    foreach($arrRowList as $rowKey=>$row){
                        $temp="radio_".($rowKey+1);
                        if(isset($arrAnswerData[$key][$temp])){
                            $started=TRUE;
                        }else{
                            $responded=FALSE;
                        }
                    }
                    if($compulsoryQuestion=='1'){
                        if(!$responded){
                            if($started){
                                $valid=FALSE;
                                $errMsg[$key][]=$incompleteError;
                            }else{
                                $valid=FALSE;
                                $errMsg[$key][]=$compulsoryError;
                            }
                        }
                    }else{
                        if($started && !$responded){
                            $valid=FALSE;
                            $errMsg[$key][]=$incompleteError;
                        }
                    }
                    break;

                case 'init_6':// Matrix-Rating scale (Numeric)
                    $started=FALSE;
                    $responded=TRUE;
                    foreach($arrRowList as $rowKey=>$row){
                        $temp="radio_".($rowKey+1);
                        if(isset($arrAnswerData[$key][$temp])){
                            $started=TRUE;
                        }else{
                            $responded=FALSE;
                        }
                    }
                    if($compulsoryQuestion=='1'){
                        if(!$responded){
                            if($started){
                                $valid=FALSE;
                                $errMsg[$key][]=$incompleteError;
                            }else{
                                $valid=FALSE;
                                $errMsg[$key][]=$compulsoryError;
                            }
                        }
                    }else{
                        if($started && !$responded){
                            $valid=FALSE;
                            $errMsg[$key][]=$incompleteError;
                        }
                    }
                    break;

                case 'init_7':// Open ended-Textarea(Comments box)
                    if($compulsoryQuestion=='1'){
                        if($arrAnswerData[$key]['area']==''){
                            $valid=FALSE;
                            $errMsg[$key][]=$compulsoryError;
                        }
                    }
                    break;

                case 'init_8':// Open ended-Constant sum
                    $responded=FALSE;
                    $noErrors=TRUE;
                    $number=0;
                    foreach($arrRowList as $rowKey=>$row){
                        $temp="text_".($rowKey+1);
                        $data=$arrAnswerData[$key][$temp];
                        if($data!=''){
                            $responded=TRUE;
                            $number=$number+$data;
                        }
                    }
                    if(!$responded){
                        if($compulsoryQuestion=='1'){
                            $noErrors=FALSE;
                            $valid=FALSE;
                            $errMsg[$key][]=$compulsoryError;
                        }

                    }else{
                        foreach($arrRowList as $rowKey=>$row){
                            $temp="text_".($rowKey+1);
                            $data=$arrAnswerData[$key][$temp];
                            if($data==''){
                                $noErrors=FALSE;
                                $valid=FALSE;
                                $array=array('fieldname'=>$row['row_text']);
                                $errMsg[$key][]=$this->objLanguage->code2Txt('mod_survey_error_1','survey',$array);
                            }elseif(!is_numeric($data)){
                                $noErrors=FALSE;
                                $valid=FALSE;
                                $array=array('fieldname'=>$row['row_text']);
                                $errMsg[$key][]=$this->objLanguage->code2Txt('mod_survey_error_2','survey',$array);
                            }
                        }
                    }
                    if($noErrors){
                        if(!empty($constantSum) && $number!=$constantSum){
                            $valid=FALSE;
                            $array=array('value'=>$constantSum);
                            $errMsg[$key][]=$this->objLanguage->code2Txt('mod_survey_error_11','survey',$array);
                        }
                    }
                    break;

                case 'init_9':// Open ended-Number
                    $data=$arrAnswerData[$key]['text'];
                    if($data!=''){
                        if(!is_numeric($data)){
                            $valid=FALSE;
                            $array=array('fieldname'=>$answer);
                            $errMsg[$key][]=$this->objLanguage->code2Txt('mod_survey_error_2','survey',$array);
                        }else{
                            if($data<$minimumNumber){
                                $valid=FALSE;
                                $array=array('fieldname_1'=>$answer,'fieldName_2'=>$minimumNumber);
                                $errMsg[$key][]=$this->objLanguage->code2Txt('mod_survey_error_4','survey',$array);
                            }
                            if($data>$maximumNumber){
                                $valid=FALSE;
                                $array=array('fieldname_1'=>$answer,'fieldName_2'=>$maximumNumber);
                                $errMsg[$key][]=$this->objLanguage->code2Txt('mod_survey_error_10','survey',$array);
                            }
                        }
                    }else{
                        if($compulsoryQuestion=='1'){
                            $valid=FALSE;
                            $errMsg[$key][]=$compulsoryError;
                        }
                    }
                    break;

                case 'init_10':// Open-ended-Date
                    if($compulsoryQuestion=='1'){
                        if(empty($arrAnswerData[$key]['date'])){
                            $valid=FALSE;
                            $errMsg[$key]['compulsory']=$compulsoryError;
                        }
                    }
                    break;

            }
        }

        if(!$valid){
            $this->session->addErrorData($errMsg);
            return $valid;
        }else{
            return $valid;
        }
    }

    /**
    * Method to check if a question has been answered
    *
    * @access public
    * @param string $surveyId The id of the survey
    * @return NULL
    */
    public function checkIfAnswered($surveyId)
    {
        $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);
        $arrAnswerData=$this->getSession('answer');

        foreach($arrQuestionList as $key=>$question){
            $questionId=$question['id'];
            $typeId=$question['type_id'];
            $booleanType=$question['preset_options'];
            $htmlElementType=$question['radio_element'];

            if($typeId!='init_7' && $typeId!='init_9' && $typeId!='init_10' && $booleanType!='1'){
                $arrRowList=$this->dbRows->listQuestionRows($questionId);
            }
            if($typeId=='init_3' || $typeId=='init_4' || $typeId=='init_5'){
                $arrColumnList=$this->dbColumns->listQuestionColumns($questionId);
            }

            $answered=FALSE;
            switch($typeId){
                case 'init_1':
                    foreach($arrRowList as $rowKey=>$row){
                        $temp="check_".($rowKey+1);
                        if(isset($arrAnswerData[$key][$temp])){
                            $answered=TRUE;
                            break;
                        }
                    }
                    break;

                case 'init_2':
                    if($htmlElementType!='1'){
                        if($arrAnswerData[$key]['drop']!='0'){
                            $answered=TRUE;
                        }
                    }else{
                        if(isset($arrAnswerData[$key]['radio'])){
                            $answered=TRUE;
                        }
                    }
                    break;

                case 'init_3':
                    foreach($arrRowList as $rowKey=>$row){
                        foreach($arrColumnList as $columnKey=>$column){
                            $temp="check_".($rowKey+1)."_".($columnKey+1);
                            if(isset($arrAnswerData[$key][$temp])){
                                $answered=TRUE;
                                break;
                            }
                        }
                    }
                    break;

                case 'init_4':
                    foreach($arrRowList as $rowKey=>$row){
                        foreach($arrColumnList as $columnKey=>$column){
                            $temp="text_".($rowKey+1)."_".($columnKey+1);
                            if(!empty($arrAnswerData[$key][$temp])){
                                $answered=TRUE;
                                break;
                            }
                        }
                    }
                    break;

                case 'init_5':
                    foreach($arrRowList as $rowKey=>$row){
                        $temp="radio_".($rowKey+1);
                        if(isset($arrAnswerData[$key][$temp])){
                            $answered=TRUE;
                            break;
                        }
                    }
                    break;

                case 'init_6':
                    foreach($arrRowList as $rowKey=>$row){
                        $temp="radio_".($rowKey+1);
                        if(isset($arrAnswerData[$key][$temp])){
                            $answered=TRUE;
                            break;
                        }
                    }
                    break;

                case 'init_7':
                    // var_dump($arrAnswerData);
                    if($arrAnswerData[$key]['area']!=''){
                        $answered=TRUE;
                    }
                    break;

                case 'init_8':
                    foreach($arrRowList as $rowKey=>$row){
                        $temp="text_".($rowKey+1);
                        $data=$arrAnswerData[$key][$temp];
                        if($data!=''){
                            $answered=TRUE;
                        }
                    }
                    break;

                case 'init_9':
                    if($arrAnswerData[$key]['text']!=''){
                        $answered=TRUE;
                    }
                    break;

                case 'init_10':
                    if($arrAnswerData[$key]['date']!=''){
                        $answered=TRUE;
                    }
                    break;

            }
            $arrAnswerData[$key]['answered']=$answered;
        }
        $this->session->addAnswerData($arrAnswerData);
    }

    /**
    * Method to validate the survey page data
    *
    * @access public
    * @param string $surveyId the Id of the survey
    * @return NULL
    */
    public function checkPageData($surveyId)
    {
        // set up error messages
        $titleLabel=$this->objLanguage->languageText('mod_survey_pagetitle', 'survey');
        $pages=$this->objLanguage->languageText('mod_survey_numberpages');
        $questions=$this->objLanguage->languageText('mod_survey_numberquestions');

        $arrPageData=$this->getSession('page');
        $arrQuestionList=$this->dbQuestion->listQuestions($surveyId);

        $valid=TRUE;
        if(count($arrPageData)>count($arrQuestionList)){
            $valid=FALSE;
            $array=array('fieldname_1'=>$pages,'fieldname_2'=>strtolower($questions));
            $errMsg[]=$this->objLanguage->code2Txt('mod_survey_error_10','survey',$array);
        }else{
            foreach($arrPageData as $key=>$page){
                $pageLabel=$page['page_label'];
                if(empty($pageLabel)){
                    $valid=FALSE;
                    $array=array('fieldname'=>$titleLabel);
                    $errMsg['page_'.$key]=$this->objLanguage->code2Txt('mod_survey_error_1','survey',$array);
                }
            }
        }

        if(!$valid){
            $this->session->addErrorData($errMsg);
            return $valid;
        }else{
            return $valid;
        }
    }
}
?>

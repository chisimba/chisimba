<?php

/* ----------- data class extends dbTable for tbl_survey ---------- */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Model class for the table tbl_survey
 * @author Kevin Cyster
 */
class dbsurvey extends dbTable {

    /**
     * @var string $table The name of the database table to be affected
     * @access private
     */
    private $table;
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
     * @var object $dbResponse The dbresponse class in the survey module
     * @access private
     */
    private $dbResponse;
    /**
     * @var object $dbAnswer The dbanswer class in the survey module
     * @access private
     */
    private $dbAnswer;
    /**
     * @var object $dbItem The dbitem class in the survey module
     * @access private
     */
    private $dbItem;
    /**
     * @var object $dbComments The dbcomments class in the survey module
     * @access private
     */
    private $dbComments;
    /**
     * @var object $objLanguage The language class in the language module
     * @access private
     */
    private $objLanguage;
    /**
     * @var object $objUser The user class in the security module
     * @access private
     */
    private $objUser;
    /**
     * @var string $userId The userid of the current user
     * @access private
     */
    private $userId;

    /**
     * Method to construct the class
     *
     * @access public
     * @return
     */
    public function init() {
        parent::init('tbl_survey');
        $this->table = 'tbl_survey';

        $this->dbQuestion = &$this->newObject('dbquestion');
        $this->dbRows = &$this->newObject('dbquestionrow');
        $this->dbColumns = &$this->newObject('dbquestioncol');
        $this->dbPages = &$this->newObject('dbpages');
        $this->dbPageQuestions = &$this->newObject('dbpagequestions');
        $this->dbResponse = &$this->newObject('dbresponse');
        $this->dbAnswer = &$this->newObject('dbanswer');
        $this->dbItem = &$this->newObject('dbitem');
        $this->dbComments = &$this->newObject('dbcomments');

        $this->objLanguage = &$this->newObject('language', 'language');
        $this->objUser = &$this->newObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to add a survey record
     *
     * @access public
     * @return string $surveyId The id of the survey that was added
     * */
    public function addSurvey() {
        $arrSurveyData = $this->getSession('survey');
        $fields = array();
        $fields['survey_name'] = $arrSurveyData['survey_name'];
        $fields['start_date'] = $arrSurveyData['start_date'];
        $fields['end_date'] = $arrSurveyData['end_date'];
        $fields['max_responses'] = $arrSurveyData['max_responses'];
        $fields['recorded_responses'] = $arrSurveyData['recorded_responses'];
        $fields['single_responses'] = $arrSurveyData['single_responses'];
        $fields['view_results'] = $arrSurveyData['view_results'];
        $fields['login'] = $arrSurveyData['login'];
        $fields['intro_label'] = $arrSurveyData['intro_label'];
        $fields['intro_text'] = $arrSurveyData['intro_text'];
        $fields['thanks_label'] = $arrSurveyData['thanks_label'];
        $fields['thanks_text'] = $arrSurveyData['thanks_text'];
        $fields['date_created'] = date("Y-m-d H:i:s");
        $fields['creator_id'] = $this->userId;
        $fields['updated'] = date("Y-m-d H:i:s");
        $surveyId = $this->insert($fields);
        return $surveyId;
    }

    /**
     * Method for editing a survey in the database.
     *
     * @access public
     * @return
     */
    public function editSurvey() {
        $arrSurveyData = $this->getSession('survey');
        $surveyId = $arrSurveyData['survey_id'];
        $fields = array();
        $fields['survey_name'] = $arrSurveyData['survey_name'];
        $fields['start_date'] = $arrSurveyData['start_date'];
        $fields['end_date'] = $arrSurveyData['end_date'];
        $fields['max_responses'] = $arrSurveyData['max_responses'];
        $fields['recorded_responses'] = $arrSurveyData['recorded_responses'];
        $fields['single_responses'] = $arrSurveyData['single_responses'];
        $fields['view_results'] = $arrSurveyData['view_results'];
        $fields['login'] = $arrSurveyData['login'];
        $fields['intro_label'] = $arrSurveyData['intro_label'];
        $fields['intro_text'] = $arrSurveyData['intro_text'];
        $fields['thanks_label'] = $arrSurveyData['thanks_label'];
        $fields['thanks_text'] = $arrSurveyData['thanks_text'];
        $fields['date_modified'] = date("Y-m-d H:i:s");
        $fields['modifier_id'] = $this->userId;
        $fields['updated'] = date("Y-m-d H:i:s");
        $this->update('id', $surveyId, $fields);
    }

    /**
     * Method for editing fields on a survey in the database.
     *
     * @access public
     * @param string $surveyId The id of the survey being edited
     * @param string $field The field being edited
     * @param string $value The new value of the field
     * @return
     */
    public function editSurveyField($surveyId, $field, $value) {
        $fields = array();
        $fields[$field] = $value;
        $this->update('id', $surveyId, $fields);
    }

    /**
     * Method to return a survey record
     *
     * @access public
     * @param string $surveyId the id of the survey to retrieve
     * @return array $data The survey data
     * */
    public function getSurvey($surveyId) {
        $sql = "SELECT * FROM " . $this->table;
        $sql.=" WHERE id='$surveyId' ";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to return all survey records
     *
     * @access public
     * @return array $data The survey data
     * */
    public function listSurveys($surveyId=NULL) {
        $sql = "SELECT * FROM " . $this->table;
        if (!is_null($surveyId)){
            $sql.=" WHERE id='$surveyId' ";
        }
        $sql.=" ORDER BY 'date_created' ";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method for deleting a survey
     *
     * @access public
     * @param string $surveyId  The id of the survey to be deleted
     * @return
     */
    public function deleteSurvey($surveyId) {
        $this->delete('id', $surveyId);
        $this->dbQuestion->delete('survey_id', $surveyId);
        $this->dbRows->delete('survey_id', $surveyId);
        $this->dbColumns->delete('survey_id', $surveyId);
        $this->dbPages->delete('survey_id', $surveyId);
        $this->dbPageQuestions->delete('survey_id', $surveyId);
        $this->dbResponse->delete('survey_id', $surveyId);
        $this->dbAnswer->delete('survey_id', $surveyId);
        $this->dbItem->delete('survey_id', $surveyId);
        $this->dbComments->delete('survey_id', $surveyId);
    }

    /**
     * Method to copy a survey
     *
     * @access public
     * @param string $surveyId The id of the survey to copy
     * @return string $newSurveyId The id of the new survey
     */
    public function copySurvey($surveyId) {
        $copyLabel = $this->objLanguage->languageText('mod_survey_copyof', 'survey');
        $arrSurveyData = $this->getSurvey($surveyId);
        $arrSurveyData = $arrSurveyData['0'];
        unset($arrSurveyData['id']);
        unset($arrSurveyData['modifier_id']);
        unset($arrSurveyData['date_modified']);
        unset($arrSurveyData['puid']);
        $arrSurveyData['survey_name'] = $copyLabel . " - " . $arrSurveyData['survey_name'];
        $arrSurveyData['survey_active'] = '';
        $arrSurveyData['response_counter'] = '';
        $arrSurveyData['email_sent'] = '';
        $arrSurveyData['creator_id'] = $this->userId;
        $arrSurveyData['date_created'] = date('Y-m-d H:i:s');
        $arrSurveyData['updated'] = date('Y-m-d H:i:s');
        $newSurveyId = $this->insert($arrSurveyData);
        return $newSurveyId;
    }

}

?>

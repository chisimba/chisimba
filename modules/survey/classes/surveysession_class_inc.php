<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
die( "You cannot view this page directly" );
}
/**
*
* @copyright (c) 2000-2005, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package survey
* @version 0.1
* @since 20 September 2005
* @author Kevin Cyster
*/

/**
* The session class is responsible for processing and managing the
* sessions of the survey manager
*
* @author Kevin Cyster
*/
class surveysession extends object
{
    // -------------- survey session methods -------------//
    /**
    * Method for adding survey data to the session variable.
    *
    * @access public
    * @param array $arrSurveyData The survey data
    * @return
    */
    public function addSurveyData($arrSurveyData)
    {
        $this->setSession('survey',$arrSurveyData);
    }

    // -------------- error session methods -------------//
    /**
    * Method for adding error data to the session variable.
    *
    * @access public
    * @param array $errorMessages The error messages
    * @return
    */
    public function addErrorData($errorMessages)
    {
        $this->setSession('error',$errorMessages);
    }

    // -------------- question session methods -------------//
    /**
    * Method for adding question data to the session variable.
    *
    * @access public
    * @param array $arrQuestionData The question data
    * @return
    */
    public function addQuestionData($arrQuestionData)
    {
        $this->setSession('question',$arrQuestionData);
    }

    // -------------- question row session methods -------------//
    /**
    * Method for adding question row data to the session variable.
    *
    * @access public
    * @param array $arrRowData The question row data
    * @return
    */
    public function addRowData($arrRowData)
    {
        $this->setSession('row',$arrRowData);
    }

    /**
    * Method for adding deleted question row ids to the session variable.
    *
    * @access public
    * @param string $rowId The question row id
    * @return
    */
    public function addDeletedRowId($rowId)
    {
        $arrRowId=$this->getSession('deletedRows');
        $arrRowId[]=$rowId;
        $this->setSession('deletedRows',$arrRowId);
    }

    // -------------- question column session methods -------------//
    /**
    * Method for adding question column data to the session variable.
    *
    * @access public
    * @param array $arrColumnData The question column data
    * @return
    */
    public function addColumnData($arrColumnData)
    {
        $this->setSession('column',$arrColumnData);
    }

    /**
    * Method for adding deleted question column ids to the session variable.
    *
    * @access public
    * @param string $columnId The question column id
    * @return
    */
    public function addDeletedColumnId($columnId)
    {
        $arrColumnId=$this->getSession('deletedColumns');
        $arrColumnId[]=$columnId;
        $this->setSession('deletedColumns',$arrColumnId);
    }

    // -------------- take survey session methods -------------//
    /**
    * Method for adding answer data to the session variable.
    *
    * @access public
    * @param array $arrAnswerData The answer data array
    * @return
    */
    public function addAnswerData($arrAnswerData)
    {
        $this->setSession('answer',$arrAnswerData);
    }

    // -------------- survey pages session methods -------------//
    /**
    * Method for adding answer data to the session variable.
    *
    * @access public
    * @param array $arrAnswerData The answer data array
    * @return
    */
    public function addPageData($arrPageData)
    {
        $this->setSession('page',$arrPageData);
    }

    /**
    * Method for adding deleted survey page ids to the session variable.
    *
    * @access public
    * @param string $pageId The survey page id
    * @return
    */
    public function addDeletedPageId($pageId)
    {
        $arrPageId=$this->getSession('deletedPages');
        $arrPageId[]=$pageId;
        $this->setSession('deletedPages',$arrPageId);
    }

    // -------------- delete sessions method -------------//
    /**
    * Method for deleteing question column data from the session variable.
    *
    * @access public
    * @param array $sessions The sessions to delete
    * @return
    */
    public function deleteSessionData($sessions)
    {
        foreach($sessions as $session){
            $this->unsetSession($session);
        }
    }
    
    
    /**
     * Method to move the question row data to the session variable
     *
     * @param string $update A variable indicating what action is to be performed
     * @param array $arrRowIdData
     * @param array $arrRowNoData
     * @param array $arrRowTextData
     * @return NULL
     */
    function moveRowData($update, $arrRowIdData, $arrRowNoData, $arrRowTextData) {
        $arrRowData = array ();
        foreach ( $arrRowIdData as $key => $id ) {
            $arrRowData [] = array ('id' => $id, 'row_order' => $arrRowNoData [$key], 'row_text' => $arrRowTextData [$key] );
        }
        if ($update == 'addrow') {
            $arrRowData [] = array ('id' => '', 'row_order' => '', 'row_text' => '' );
        }
        $temp = explode ( "_", $update );
        if ($temp ['0'] == 'deleterow') {
            if (isset ( $temp ['1'] )) {
                $rowId = $arrRowData [$temp ['1']] ['id'];
                if ($rowId != '') {
                    $this->addDeletedRowId ( $rowId );
                }
                unset ( $arrRowData [$temp ['1']] );
            }
        }
        $arrRowData = array_merge ( $arrRowData );
        $this->addRowData ( $arrRowData );
    }
    
    /**
     * Method to move the question column data to the session variable
     *
     * @param string $update A variable indicating what action is to be performed
     * @param array $arrColumnIdData
     * @param array $arrColumnIdData
     * @param array $arrColumnIdData
     * @return NULL
     */
    function moveColumnData($update, $arrColumnIdData, $arrColumnNoData, $arrColumnTextData) {
        $arrColumnData = array ();
        foreach ( $arrColumnIdData as $key => $id ) {
            $arrColumnData [] = array ('id' => $id, 'column_order' => $arrColumnNoData [$key], 'column_text' => $arrColumnTextData [$key] );
        }
        if ($update == 'addcolumn') {
            $arrColumnData [] = array ('id' => '', 'column_order' => '', 'column_text' => '' );
        }
        $temp = explode ( "_", $update );
        if ($temp ['0'] == 'deletecolumn') {
            if (isset ( $temp ['1'] )) {
                $columnId = $arrColumnData [$temp ['1']] ['id'];
                if ($columnId != '') {
                    $this->addDeletedColumnId ( $columnId );
                }
                unset ( $arrColumnData [$temp ['1']] );
            }
        }
        $arrColumnData = array_merge ( $arrColumnData );
        $this->addColumnData ( $arrColumnData );
    }

    /**
     * Method to move the survey page data to the session variable
     *
     * @param string $update A variable indicating what action is to be performed
     * @return NULL
     */
    function movePageData($update, $arrPageIdData, $arrPageOrderData, $arrPageLabelData, $arrPageTextData) 
    {
        $arrPageData = array ();
        foreach ( $arrPageIdData as $key => $id ) {
            $arrPageData [] = array ('id' => $id, 'page_order' => $arrPageOrderData [$key], 'page_label' =>$arrPageLabelData [$key], 'page_text' => $arrPageTextData [$key] );
        }
        if ($update == 'addpage') {
            $arrPageData [] = array ('id' => '', 'page_order' => '', 'page_label' => '', 'page_text' => '' );
        }
        $temp = explode ( "_", $update );
        if ($temp ['0'] == 'deletepage') {
            if (isset ( $temp ['1'] )) {
                $pageId = $arrPageData [$temp ['1']] ['id'];
                if ($pageId != '') {
                    $this->addDeletedPageId ( $pageId );
                }
                unset ( $arrPageData [$temp ['1']] );
            }
        }
        if ($temp ['0'] == 'movepage') {
            if ($temp ['2'] == 'down') {
                $firstPage = $arrPageData [$temp ['1']];
                $secondPage = $arrPageData [($temp ['1'] + 1)];
                $arrPageData [$temp ['1']] = $secondPage;
                $arrPageData [($temp ['1'] + 1)] = $firstPage;
            } else {
                $firstPage = $arrPageData [$temp ['1']];
                $secondPage = $arrPageData [($temp ['1'] - 1)];
                $arrPageData [$temp ['1']] = $secondPage;
                $arrPageData [($temp ['1'] - 1)] = $firstPage;
            }
        }
        $arrPageData = array_merge ( $arrPageData );
        $this->addPageData ( $arrPageData );
        if ($temp ['0'] == 'movepage') {
            $this->dbPages = $this->newObject ('dbpages');
            $this->dbPages->editPages ();
        }
    }

}
?>

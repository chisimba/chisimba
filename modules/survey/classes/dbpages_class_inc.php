<?php
/* ----------- data class extends dbTable for tbl_survey_pages ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_response
* @author Kevin Cyster
*/

class dbpages extends dbTable
{
    /**
     * @var string $table The name od the database table to be affected
     * @access private
     */
    private $table;

    /**
     * @var object $dbPageQuestions The dbpagequestions class in the survey module
     * @access private
     */
    private $dbPageQuestions;

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
    public function init()
    {
        parent::init('tbl_survey_pages');
        $this->table='tbl_survey_pages';

        $this->dbPageQuestions=&$this->newObject('dbpagequestions');
        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
    * Method for adding a page to the database.
    *
    * @access public
    * @param string $surveyId  The id of the survey the pages are being added to
    * @return
    */
    public function addPages($surveyId)
    {
        $arrPageData=$this->getSession('page');
        $arrPageList=$this->listPages($surveyId);
        foreach($arrPageData as $key=>$page){
            if($page['id']==''){
                $fields=array();
                $fields['survey_id']=$surveyId;
                $fields['page_order']=($key+1);
                $fields['page_label']=$page['page_label'];
                $fields['page_text']=$page['page_text'];
                $fields['date_created']=date("Y-m-d H:i:s");
                $fields['creator_id']=$this->userId;
                $fields['updated']=date("Y-m-d H:i:s");
                $this->insert($fields);
            }
        }
    }

    /**
    * Method for editing a page on the database.
    *
    * @access public
    * @return
    */
    public function editPages()
    {
        $arrPageData=$this->getSession('page');
        foreach($arrPageData as $key=>$page){
            $pageId=$page['id'];
            if($pageId!=''){
                $fields=array();
                $fields['page_order']=($key+1);
                $fields['page_label']=$page['page_label'];
                $fields['page_text']=$page['page_text'];
                $fields['date_modified']=date("Y-m-d H:i:s");
                $fields['modifier_id']=$this->userId;
                $fields['updated']=date("Y-m-d H:i:s");
                $this->update('id',$pageId,$fields);
            }
        }
    }

    /**
    * Method for deleting pages
    *
    * @access public
    * @return
    */
    public function deletePages()
    {
        $arrPageData=$this->getSession('deletedPages');
        if(!empty($arrPageData)){
            foreach($arrPageData as $pageId){
                $this->delete('id',$pageId);
                $this->dbPageQuestions->delete('page_id',$pageId);
            }
        }
    }

    /**
    * Method for deleting all pages
    *
    * @access public
    * @param string $surveyId The id of the survey the pages are to be deleted from
    * @return
    */
    public function deleteAllPages($surveyId)
    {
        $this->delete('survey_id',$surveyId);
        $this->dbPageQuestions->delete('survey_id',$surveyId);
    }

    /**
    * Method for listing all rows
    *
    * @access public
    * @return array $data  All row information.
    */
    public function listPages($surveyId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE survey_id='$surveyId'";
        $sql.=" ORDER BY 'page_order' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for listing all rows
    *
    * @access public
    * @param string $pageId The id of the page to retrieve
    * @return array $data  All row information.
    */
    public function getPage($pageId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE id='$pageId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to copy pages for a survey
    *
    * @access public
    * @param string $surveyId The id of the survey to copy pages from
    * @param string $newSurveyId The id of the survey to copy pags to
    * @return
    */
    public function copyPages($surveyId,$newSurveyId)
    {
        $arrPageList=$this->listPages($surveyId);
        if(!empty($arrPageList)){
            foreach($arrPageList as $page){
                $pageId=array_shift($page);
                $page['survey_id']=$newSurveyId;
                $page['creator_id']=$this->userId;
                $page['date_created']=date('Y-m-d H:i:s');
                $page['updated']=date('Y-m-d H:i:s');
                unset($page['modifier_id']);
                unset($page['date_modified']);
                unset($page['puid']);
                $newPageId=$this->insert($page);
                $this->dbPageQuestions->copyPagequestions($pageId,$newPageId,$newSurveyId);
            }
        }
    }
}
?>
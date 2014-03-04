<?php
/**
* Data class extends dbTable for tbl_worksheet.
* @package worksheet
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
        die("You cannot view this page directly");
}

/**
* Model class for the table tbl_worksheet.
* @author Tohir Solomons
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package worksheet
* @version 0.2
*/
class dbworksheet extends dbTable
{

    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_worksheet');
        $this->table = 'tbl_worksheet';

        $this->objUser = $this->getObject('user', 'security');
    }

    /**
    * Method to get all the worksheets in a context ordered by chapter.
    * @param string $context The current context
    * @return array $result The list of worksheets
    */
    public function getWorksheetsInContext($context)
    {

        $sql = 'SELECT ws.id, ws.context, ws.chapter, ws.name, ws.activity_status, ws.percentage, ws.total_mark, ';
        $sql .='ws.closing_date, ws. description, count(quest.worksheet_id) AS questions ';
        $sql .= 'FROM tbl_worksheet AS ws ';
        $sql .= 'LEFT JOIN tbl_worksheet_questions AS quest ON (quest.worksheet_id = ws.id) ';
        $sql .= "WHERE ws.context='{$context}' ";
        $sql .= "GROUP BY quest.worksheet_id, ws.activity_status, ws.context, ws.name, ws.id";

        $result = $this->getArray($sql);
        return $result;
    }

    public function getStatusText($status)
    {
        $this->objLanguage = $this->getObject('language', 'language');
        switch ($status)
        {
            case 'inactive':
                return $this->objLanguage->languageText('mod_worksheet_activityinactive', 'worksheet', 'Not Active');
            case 'open':
                return $this->objLanguage->languageText('mod_worksheet_activityopen', 'worksheet', 'Open for Entry');
            case 'closed':
                return $this->objLanguage->languageText('mod_worksheet_activityclosed', 'worksheet', 'Closed for Marking');
            case 'marked':
                return $this->objLanguage->languageText('mod_worksheet_activitymarked', 'worksheet', 'Marked and Viewable');
        }
    }

    /**
    * Added by otim samuel: sotim@dicts.mak.ac.ug, Muk, Uganda
    * Specifically added for use within the gradebook module
    * getWorksheetsInContext returns results ordered by chapter
    * and the gradebook module rans a routine whose accuracy is
    * compromised when results are ordered by anything other than the id
    * hence the inclusion of this method.
    * Method to get a test or list of worksheets.
    * @param string $fields A list of fields to be returned. Default=*.
    * @param string $id The id of the required worksheet. Default=NULL.
    * @return array $data The details of the test or list of worksheets.
    */
    public function getWorksheets($filter=NULL, $fields='*', $id=NULL)
    {
        $sql = "SELECT $fields FROM ".$this->table;
        if($id){
            $sql .= " WHERE id='$id'";
        }else{
            $sql .= " WHERE $filter";
        }
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get a single worksheet.
    * @param string $id The id of the required worksheet.
    * @param string $fields The details required from the worksheet.
    * @return array $data The worksheet details.
    */
    public function getWorksheet($id,$fields='*')
    {
        $sql = "SELECT $fields FROM ".$this->table." WHERE id='$id' LIMIT 1";
        $data = $this->getArray($sql);

        if($data){
            return $data[0];
        }
        return FALSE;
    }

    /**
    * Method to search worksheets in a context.
    * @param string $field The table field in which to search.
    * @param string $value The value to search for.
    * @param string $context The current context.
    * @return array $data The results of the search.
    */
    public function search($field, $value, $context)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE $field LIKE '$value%'";
        $sql .= " AND context='$context'";
        $sql .= ' ORDER BY closing_date, name';

        $data = $this->getArray($sql);

        if($data){
            return $data;
        }
        return FALSE;
    }
    /**
     * Method to add or update a worksheet.
     *
     * @access public
     * @param array $fields The data to be inserted into the table
     * @param string $id The id of the worksheet to be updated. Default=NULL.
     * @return
     */
    public function addWorksheet($fields, $id = NULL) {
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }
    /**
    * Method to add a new worksheet
    * @param string $context The current context.
    * @param string $chapter The chapter or context node to add the worksheet to.
    * @param string $worksheet_name The name of the worksheet.
    * @param string $activity_status The status of the worksheet.
    * @param string $percentage The percentage of the final mark that the worksheet counts.
    * @param string $closing_date The closing date for submitted worksheets.
    * @param string $description A description of the worksheet content.
    * @param string $userId The user id of the creator.
    * @param string $lastModified The time of creation.
    * @return
    */
    public function insertWorkSheet($context, $chapter, $worksheet_name, $activity_status, $percentage, $closing_date, $description)
    {
        $id = $this->insert(array(
                'context' => $context,
                'chapter' => $chapter,
                'name' => $worksheet_name,
                'activity_status' => $activity_status,
                'percentage' => $percentage,
                'closing_date' => $closing_date,
                'description' => $description,
                'userid' => $this->objUser->userId(),
                'last_modified' => strftime('%Y-%m-%d %H:%M:%S', mktime())));
        return $id;
    }

    /**
    * Method to update a worksheet
    * @param string $id The id of the worksheet being editted.
    * @param string $chapter The chapter or context node to add the worksheet to.
    * @param string $worksheet_name The name of the worksheet.
    * @param string $activity_status The status of the worksheet.
    * @param string $percentage The percentage of the final mark that the worksheet counts.
    * @param string $closing_date The closing date for submitted worksheets.
    * @param string $description A description of the worksheet content.
    * @param string $userId The user id of the creator.
    * @param string $lastModified The time of creation.
    * @return
    */
    public function updateWorkSheet($id, $chapter, $worksheet_name, $activity_status, $percentage, $closing_date, $description, $userId, $lastModified)
    {
        $this->update("id", $id, array(
                'chapter' => $chapter,
                'name' => $worksheet_name,
                'activity_status' => $activity_status,
                'percentage' => $percentage,
                'closing_date' => $closing_date,
                'description' => $description,
                'userid' => $userId,
                'last_modified' => $lastModified));
        return $id;
    }

    /**
    * Method to set the total mark for a worksheet
    * @param string $id The id of the worksheet to update
    * @param string $total The mark to be added to the total
    * @param bool $new If TRUE ? add $total to total_mark : replace total_mark with $total
    * @return
    */
    public function setTotal($id, $total, $new=TRUE)
    {
        if($new){
            //$sql="SELECT total_mark from tbl_worksheet WHERE id='$id'";
            //$rows=$this->getArray($sql);
            //$total=$total+$rows[0]['total_mark'];
            return $this->updateTotalMark($id);
        } else {
            return $this->update("id", $id, array('total_mark' => $total));
        }
    }

    public function updateTotalMark($id)
    {
        $worksheetQuestions = $this->getObject('dbworksheetquestions');
        $questions = $worksheetQuestions->getQuestions($id, 0, FALSE);

        $total = 0;

        foreach ($questions as $question)
        {
            $total += $question['question_worth'];
        }

        return $this->setTotal($id, $total, FALSE);
    }

    public function updateStatus($id, $status, $closingDate)
    {
        return $this->update('id', $id, array('activity_status'=>$status, 'closing_date'=>$closingDate));
    }

    /**
    * Delete a worksheet.
    * @param string $id The ID of the worksheet
    * @return void
    */
    public function deleteWorksheet($id)
    {
        $dbWorksheetQuestions = $this->getObject('dbworksheetquestions');
        $dbWorksheetQuestions->deleteQuestions($id);
        $dbWorksheetResults = $this->getObject('dbworksheetresults');
        $dbWorksheetResults->deleteResults($id);
        $this->delete('id',$id);
        return;
    }

} //end of class
?>
<?php
/**
* Data class extends dbTable for tbl_worksheet_results
* @package worksheet
* @filesource
*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
        die("You cannot view this page directly");
}


/**
* Model class for the table tbl_worksheet_results
* @author Megan Watson,  James Kariuki
* @copyright (c) 2004 UWC
* @package worksheet
* @version 0.2
*/

class dbworksheetresults extends dbtable
{
    /**
    * Constructor function.
    */
    public function init()
    {
        parent::init('tbl_worksheet_results');
        $this->table='tbl_worksheet_results';
    }


    public function setWorksheetCompleted($userId, $worksheet)
    {
        return $this->insert(array(
                'worksheet_id' => $worksheet,
                'completed' => 'Y',
                'userid' => $userId,
                'last_modified' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                'updated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
    }

    public function checkWorksheetCompleted($userId, $worksheet)
    {
        $result = $this->getRecordCount(" WHERE worksheet_id='{$worksheet}' AND userid='{$userId}' AND completed='Y' LIMIT 1");

        if (count($result) == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getWorksheetResult($userId, $worksheet)
    {
        $result = $this->getAll(" WHERE worksheet_id='{$worksheet}' AND userid='{$userId}' AND completed='Y' LIMIT 1");

        if (count($result) == 1) {
            return $result[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to insert a students results into the database.
    * @param array $fields The values to be inserted.
    * @param string $id The id of the result to be updated. Default=NULL if the result doesn't exist.
    * @return
    */
    public function addResult($fields,$id=NULL)
    {
        if($id){
            $this->update('id',$id,$fields);
        }else{
            $this->insert($fields);
        }
    }

    /**
    * Method to retrieve submitted worksheets for display.
    * @param string $worksheet_id The id of the worksheet being viewed.
    * @param string $filter An alternative filter to the default used in the method. Default=NULL
    * @return array $rows The list of submissions
    */
    public function getResults($worksheet_id, $filter=NULL)
    {
        $sql = 'SELECT * FROM '.$this->table;
        if($filter){
            $sql .= " WHERE $filter";
        }else{
            $sql .= " WHERE worksheet_id='$worksheet_id' AND completed='Y'";
        }
        $rows = $this->getArray($sql);
        if($rows){
            return $rows;
        }
        return FALSE;
    }

    /**
    * added by otim samuel, sotim@dicts.mak.ac.ug: 13th Jan 2006
	* for specific use within the gradebook module
	* Method to get all worksheet results
	* as a percentage of the total year's mark
    * @param string $filter
    * @param string $fields The required fields. Default = * (all);
	* @param string $tables The tables to be queried
    * @return array $data The result.
    */
    public function getAnnualResults($filter, $fields='*', $tables='tbl_worksheet_results')
    {
        $sql = "SELECT $fields FROM ".$tables;
        $sql .= " WHERE $filter";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to determine which worksheets have been completed and submitted.
    * @param string $userid The id of the student submitting.
    * @return array $rows A list of worksheets that have been submitted.
    */
    public function checkSubmit($userId)
    {
        $sql='SELECT worksheet_id FROM '.$this->table." WHERE userId='$userId'
        AND completed='Y'";
        $rows=$this->getArray($sql);

        if($rows){
            return $rows;
        }
        return FALSE;
    }

    /**
    * Method to determine if a result exists and return its id.
    * @param string $userId The id of the user whose result is being checked.
    * @param string $worksheet_id The id of the current worksheet.
    * @return string $id The id of the result
    */
    public function getId($userId,$worksheet_id)
    {
        $sql='SELECT id FROM '.$this->table." WHERE userId='$userId' and worksheet_id='$worksheet_id'";
        $rows=$this->getArray($sql);
        if($rows){
            return $rows[0]['id'];
        }
        return FALSE;
    }

	 /**
    * Method to reset a submited users test.
    * @param string $userId The id of the user whose result is being reset.
    * @param string $worksheet_id The id of the current worksheet - to be reset.
    * @author James Kariuki
    * @return string $id The id of the result
    */
    public function reset4Student($userId,$worksheet_id)
    {
		$id=$this->getId($userId,$worksheet_id);
		$this->update('id',$id,array('completed'=>'N'));
        return False;
    }

    /**
    * Delete results in a worksheet.
    * @param string $worksheetId The ID of the worksheet
    * @return void
    */
    public function deleteResults($worksheetId)
    {
        $sql = "SELECT id FROM {$this->_tableName} WHERE worksheet_id = '{$worksheetId}'";
        $rs = $this->getArray($sql);
        if (!empty($rs)) {
            foreach ($rs as $row){
                $this->delete('id', $row['id']);
            }
        }
        return;
    }

}
?>
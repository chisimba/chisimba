<?php
/**
* viewFaculty class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* viewFaculty class provides functionality for browsing titles in a faculty via the viewbrowse class.
*
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

$this->loadClass('dbthesis', 'etd');
class viewFaculty extends dbthesis
{
    /**
    * Method to get metadata records limited to a given number of results.
    *
    * @access public
    * @param string $limit The number of metadata records to return. Default = 10 results.
    * @param string $start The metadata record for the result set to start from. Default = Null, return from the start.
    * @return array Metadata result set
    */
    public function getData($limit = 10, $start = NULL, $joinId = NULL)
    {
        $faculty = $this->getSession('faculty');
        $sqlNorm = "SELECT dc.{$this->col1Field} as col1, dc.{$this->col2Field} as col2, dc.{$this->col3Field} as col3, thesis.id as id ";
        $sqlFound = "SELECT COUNT(*) AS count ";
        
        $sql = "FROM {$this->table} AS thesis, {$this->submitTable} AS submit, {$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = thesis.submitid AND dc.id = thesis.dcmetaid ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND submit.status = 'archived' ";
        $sql .= "AND thesis.thesis_degree_faculty = '{$faculty}' ";
        
        $sqlLimit = "ORDER BY LOWER({$this->col1Field}) ";
        
        $sqlLimit .= $limit ? "LIMIT $limit " : NULL;
        $sqlLimit .= $start ? "OFFSET $start " : NULL;
        
        // Get result set
        $data = $this->getArray($sqlNorm.$sql.$sqlLimit);
        
        // Get number of results
        $data2 = $this->getArray($sqlFound.$sql);
        if(!empty($data2)){
            $this->recordsFound = $data2[0]['count'];
        }
        
        return $data;
    }

    /**
    * Method to get metadata records by letter.
    *
    * @access public
    * @param string $letter The letter to display. Default = a.
    * @param string $limit The number of metadata records to return. Default = 10 results.
    * @param string $start The metadata record for the result set to start from. Default = Null, return from the start.
    * @return array Metadata result set
    */
    public function getByLetter($letter = 'a', $limit = 10, $start = NULL, $joinId = NULL)
    {
        $faculty = $this->getSession('faculty');
        $letter = strtolower($letter);
        $sqlNorm = "SELECT dc.{$this->col1Field} as col1, dc.{$this->col2Field} as col2, dc.{$this->col3Field} as col3, thesis.id as id ";
        $sqlFound = "SELECT COUNT(*) AS count ";
        
        $sql = "FROM {$this->table} AS thesis, {$this->submitTable} AS submit, {$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = thesis.submitid AND dc.id = thesis.dcmetaid ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND submit.status = 'archived' ";
        $sql .= "AND thesis.thesis_degree_faculty = '{$faculty}' ";
        $sql .= "AND ( LOWER({$this->col1Field}) LIKE '$letter%' ";
        
        if(strtolower($letter) == 'a'){
            $sql .= "AND NOT ( LOWER({$this->col1Field}) LIKE 'a %' OR LOWER({$this->col1Field}) LIKE 'an %') ";
        }
        if(strtolower($letter) == 't'){
            $sql .= "AND NOT ( LOWER({$this->col1Field}) LIKE 'the %') ";
        }
        
        $sql .= " OR LOWER({$this->col1Field}) LIKE 'the $letter%' ";
        $sql .= " OR LOWER({$this->col1Field}) LIKE 'a $letter%' ";
        $sql .= " OR LOWER({$this->col1Field}) LIKE 'an $letter%' ";
        $sql .= " OR LOWER({$this->col1Field}) LIKE '`n $letter%' ) ";
        
        $sqlLimit = "ORDER BY LOWER({$this->col1Field}) ";
        
        $sqlLimit .= $limit ? "LIMIT $limit " : NULL;
        $sqlLimit .= $start ? "OFFSET $start " : NULL;
                
        // Get result set
        $data = $this->getArray($sqlNorm.$sql.$sqlLimit);
                
        // Get number of results
        $data2 = $this->getArray($sqlFound.$sql);
        if(!empty($data2)){
            $this->recordsFound = $data2[0]['count'];
        }
        
        return $data;
    }
    
}
?>
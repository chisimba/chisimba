<?php
/**
* viewDegrees class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* viewDegrees class provides functionality for browsing titles related to a degree via the viewbrowse class.
*
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

$this->loadClass('dbthesis', 'etd');
class viewDegrees extends dbthesis
{
    /**
    * Class constructor extends dbthesis constructor
    */
    public function init()
    {
        parent::init();
        
        $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
        $this->loadClass('htmltable', 'htmlelements'); 
        $this->loadClass('link', 'htmlelements');
    }
    
    /**
    * Method to display a list of degrees
    *
    * @access public
    * @return string html
    */
    public function listDegrees()
    {
        $data = $this->getDegrees('');
        //echo '<pre>'; print_r($data);
        
        $head = $this->objLanguage->languageText('word_degrees');
        $lbNoDegree = $this->objLanguage->languageText('mod_etd_nodegreesavailable', 'etd');
        
        $str = '<br />';
        if(!empty($data)){
            $class = 'even';
            $objTable = new htmltable();
            $i = 1;
            foreach($data as $item){
                $class = ($class == 'even') ? 'odd' : 'even';
                
                $objLink = new link($this->uri(array('action' => 'viewdegrees', 'id' => $item['id'])));
                $objLink->link = $item['col1'];
                $name = $objLink->show();
                
                $objTable->addRow(array($i++, $name), $class);
            }
            $str .= $objTable->show();
        }else{
            $str = '<p class="noRecordsMessage">'.$lbNoDegree.'</p>';
        }
        
        return $this->objFeatureBox->showContent($head, $str);
    }
    
    /**
    * Method to get metadata records limited to a given number of results.
    *
    * @access public
    * @param string $limit The number of metadata records to return. Default = 10 results.
    * @param string $start The metadata record for the result set to start from. Default = Null, return from the start.
    * @return array Metadata result set
    */
    public function getDegrees($limit = 10, $start = NULL, $joinId = NULL)
    {
        $sqlNorm = "SELECT DISTINCT thesis.thesis_degree_name as col1, count(*) as cnt, thesis.thesis_degree_name as id ";
        $sqlFound = "SELECT COUNT(*) AS count ";
        
        $sql = "FROM {$this->table} AS thesis, {$this->submitTable} AS submit, {$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = thesis.submitid AND dc.id = thesis.dcmetaid AND thesis.thesis_degree_name != '' ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND submit.status = 'archived' ";
        $sql .= "GROUP BY thesis.thesis_degree_name ";
        
        $sqlLimit = "ORDER BY thesis.thesis_degree_name ";
        
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
    * Method to get metadata records limited to a given number of results.
    *
    * @access public
    * @param string $limit The number of metadata records to return. Default = 10 results.
    * @param string $start The metadata record for the result set to start from. Default = Null, return from the start.
    * @return array Metadata result set
    */
    public function getData($limit = 10, $start = NULL, $joinId = NULL)
    {
        $degree = $this->getSession('degree');
        $sqlNorm = "SELECT dc.{$this->col1Field} as col1, dc.{$this->col2Field} as col2, dc.{$this->col3Field} as col3, thesis.id as id ";
        $sqlFound = "SELECT COUNT(*) AS count ";
        
        $sql = "FROM {$this->table} AS thesis, {$this->submitTable} AS submit, {$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = thesis.submitid AND dc.id = thesis.dcmetaid ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND submit.status = 'archived' ";
        $sql .= "AND thesis.thesis_degree_name = '{$degree}' ";
        
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
        $degree = $this->getSession('degree');
        $letter = strtolower($letter);
        $sqlNorm = "SELECT dc.{$this->col1Field} as col1, dc.{$this->col2Field} as col2, dc.{$this->col3Field} as col3, thesis.id as id ";
        $sqlFound = "SELECT COUNT(*) AS count ";
        
        $sql = "FROM {$this->table} AS thesis, {$this->submitTable} AS submit, {$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = thesis.submitid AND dc.id = thesis.dcmetaid ";
        $sql .= "AND submit.submissiontype = '{$this->subType}' AND submit.status = 'archived' ";
        $sql .= "AND thesis.thesis_degree_name = '{$degree}' ";
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
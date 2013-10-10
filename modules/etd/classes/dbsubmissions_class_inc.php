<?php
/**
* dbsubmissions class extends dbtable
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbsubmissions class for managing the data in the tbl_etd_submissions table.
* @author Megan Watson
* @author Jonathan Abrahams
* @copyright (c) 2004 UWC
* @version 0.2
* @modified Megan Watson 2006 10 27 Ported to chisimba framework
*/

class dbsubmissions extends dbtable
{
    /**
    * @var string $subType The type of submission - etd/other.
    * Used to distiguish the source of the document in the database table.
    */
    private $subType = 'etd';

    /**
    * @var string $metaType The type of extended metadata for the document being submitted.
    * Thesis (tbl_etd_metadata_thesis) vs qualified (tbl_etd_metadata_qualified) metadata
    */
    private $metaType = 'thesis';

    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_etd_submissions');
        $this->table = 'tbl_etd_submissions';
        
        $this->dcTable = 'tbl_dublincoremetadata';
        $this->thesisTable = 'tbl_etd_metadata_thesis';
        //$this->qualifiedTable = 'tbl_etd_metadata_qualified';
        $this->embargoTable = 'tbl_etd_embargos';

        $this->xmlMeta = $this->getObject('xmlmetadata', 'etd');
        
    }

    /**
    * Method to set the table type where the extended metadata is stored.
    *
    * @access public
    * @param string $type The table type - thesis / qualified
    * @return
    */
    public function setDocType($type = 'thesis')
    {
        $this->metaType = $type;
    }

    /**
    * Method to set the type of submission - etd/other.
    * Used to distiguish the source of the document in the database table.
    *
    * @access public
    * @param string $type The submission type
    * @return
    */
    public function setSubmitType($type)
    {
        $this->subType = $type;
    }

    /**
    * Method to update a submitted resource.
    *
    * @param string $id The Id of the resource.
    * @param string $userId The user Id for the modifier.
    * @return string $id
    */
    public function editSubmission($userId, $id = NULL, $status = 'assembly', $level = 0)
    {
        $fields = array();
        $fields['status'] = $status;
        $fields['approvalLevel'] = $level;

        if(!empty($id)){
            $fields['modifierid'] = $userId;
            $fields['datemodified'] = date('Y-m-d H:i:s');
            $fields['updated'] = date('Y-m-d H:i:s');
            $this->update('id', $id, $fields);
        }else{
            $fields['creatorid'] = $userId;
            $fields['datecreated'] = date('Y-m-d H:i:s');
            $fields['submissiontype'] = $this->subType;
            $fields['updated'] = date('Y-m-d H:i:s');
            $id = $this->insert($fields);
        }
        return $id;
    }
    
    /**
    * Method to check if the user is currently in the process of submitting a document, returns the submission id
    *
    * @access public
    * @param string $userId The current user
    * @return string $submitId The users submission
    */
    public function getUserSubmission($userId)
    {
        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE creatorid = '{$userId}' AND status = 'assembly'";
        
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0]['id'];
        }
        return FALSE;
    }

    /**
    * Method to get a resource from the submissions table with metadata.
    *
    * @access public
    * @param string $id The Id of the resource.
    * @param bool $archive TRUE = get data from the xml - not archived; default is FALSE = get data from the database - archived
    */
    public function getSubmission($id)//, $archive = FALSE)
    {
        $sql = "SELECT submit.id as submitid, submit.*, extra.id AS metaid, extra.*, dc.id as dcid, dc.* FROM {$this->table} AS submit, ";
        
        if($this->metaType == 'qualified'){
            $sql .= "{$this->qualifiedTable} AS extra, ";
        }else{
            $sql .= "{$this->thesisTable} AS extra, ";
        }        
        $sql .= "{$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = extra.submitid AND dc.id = extra.dcmetaid ";
        $sql .= "AND submit.id = '{$id}'";
        
        $data = $this->getArray($sql);
                
        if(!empty($data)){
            return $data[0];
        }
        return array();
    }

    /**
    * Method to fetch a set of resources based on given information.
    * The method can either match the info, or match similar information.
    *
    * @access public
    * @param array $criteria The filter on the resources - array('field' => 'title', 'compare' = 'LIKE', 'value' => 'something')
    * @param integer $start The resource to start from - for displaying 10 at a time
    * @param integer $limit The number of resources to fetch
    * @return array The resource data and the number of total resources based on the filter
    */
    public function fetchResources($criteria = array(), $start = 0, $limit = NULL)
    {
        $sqlNorm = "SELECT * FROM {$this->table} AS submit, ";
        $sqlCount = "SELECT COUNT(*) AS count FROM {$this->table} AS submit, ";
        
        if($this->metaType == 'qualified'){
            $sql = "{$this->qualifiedTable} AS extra, ";
        }else{
            $sql = "{$this->thesisTable} AS extra, ";
        }        
        $sql .= "{$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = extra.submitid AND dc.id = extra.dcmetaid ";
        $sql .= "AND submissiontype = '{$this->subType}' AND status = 'archived' ";
        
        if(!empty($criteria)){
            $critSql = '';
            foreach($criteria as $item){
                if(!empty($critSql)){
                    $critSql .= ' OR ';
                }
                $value = strtolower($item['value']);
                $critSql .= "LOWER({$item['field']}) {$item['compare']} '{$value}'";
            }
            $sql .= " AND ($critSql) ";
        }
            
        if(!is_null($limit)){
            $sql .= " LIMIT $limit";
        }
        //$offset = "LIMIT 10 OFFSET $start";
        
        $data = $this->getArray($sqlNorm.$sql);//.$offset);
        $count = $this->getArray($sqlCount.$sql);
        
        return array($data, $count[0]['count']);
    }
    
    /**
    * Method to fetch the most recent submitted resources
    *
    * @access public
    * @return array The resource data
    */
    public function getLatest()
    {
        $sqlNorm = "SELECT *, extra.id as thesis_id FROM {$this->table} AS submit, ";
        
        if($this->metaType == 'qualified'){
            $sql = "{$this->qualifiedTable} AS extra, ";
        }else{
            $sql = "{$this->thesisTable} AS extra, ";
        }        
        $sql .= "{$this->dcTable} AS dc ";
        
        $sql .= "WHERE submit.id = extra.submitid AND dc.id = extra.dcmetaid ";
        $sql .= "AND submissiontype = '{$this->subType}' AND status = 'archived' ";
        
        $sql .= "ORDER BY dc.enterdate DESC LIMIT 10";
        
        $data = $this->getArray($sqlNorm.$sql);        
        return $data;
    }

    /**
    * Method to get a list of non-archived submissions by status
    *
    * @access public
    * @param string $levels The levels of approval to display
    * @return array $data The submissions
    */
    public function getNewSubmissions($levels)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE (status = 'metadata' OR status = 'pending') 
        AND submissiontype = '{$this->subType}' ";
        
        if(!empty($levels)){
            $filter = '';
            foreach($levels as $item){
                if(!empty($filter)){
                    $filter .= 'OR ';
                }
                $filter .= "approvallevel = '{$item}' ";
            }
            $sql .= "AND ({$filter})";
        }
        $data = $this->getArray($sql);
        
        // For each submission get the related xml file
        if(!empty($data)){
            foreach($data as $key => $item){
                $xml = $this->getXmlMeta($item['id']);
                $newdata = array_merge($data[$key], $xml);
                $data[$key] = $newdata;
            }
        }
        
        return $data;
    }

    /**
    * Method to get a list of identifiers / urls for all the given resources
    *
    * @access public
    */
    function getResourceUrls()
    {
        $sql = 'SELECT dc.dc_identifier, extra.dateCreated, extra.dateModified FROM '.$this->table.' AS submit, ';
        
        if($this->metaType == 'qualified'){
            $sql .= "{$this->qualifiedTable} AS extra, ";
        }else{
            $sql .= "{$this->thesisTable} AS extra, ";
        }
            
        $sql .= "{$this->dcTable} AS dc ";
        $sql .= "WHERE dc.id = extra.dcMetaId AND submit.id = extra.submitId ";
        $sql .= "AND submissionType = '{$this->subType}' AND status = 'archived' ";
            
        $data = $this->getArray($sql);

        return $data;
    }
    
    /**
    * Method to get the xml files for a submission
    *
    * @access private
    * @param string $submitId The submission id
    * @return array The xml data
    */
    private function getXmlMeta($submitId)
    {
        $xmlData = array();
        $xml = $this->xmlMeta->openXML('etd_'.$submitId);
        if(is_array($xml) && !empty($xml)){
            $xmlData = array_merge($xml['metadata']['dublincore'], $xml['metadata'][$this->metaType]);
        }
        return $xmlData;
    }

    /**
    * Method to set the level of approval on an ETD.
    *
    * @access public
    * @param string $id The Id of the ETD.
    * @param string $userId The user Id for the modifier.
    * @param string $level The level of approval.
    * @param string $access The level of access - private = user only; public = available; protected = metadata is available, file is hidden
    * @return string $id
    */
    public function changeApproval($id, $userId, $level, $status, $access = 'private')
    {
        $fields['approvalLevel'] = $level;
        $fields['status'] = $status;
        $fields['accessLevel'] = $access;
        $fields['modifierId'] = $userId;
        $fields['dateModified'] = date('Y-m-d H:i:s');
        $fields['updated'] = date('Y-m-d H:i:s');
        $this->update('id', $id, $fields);
        return $id;
    }

    /**
    * Method to delete a resource
    *
    * @access public
    * @param string $id The id of the submission to delete
    * @return
    */
    public function deleteSubmission($id)
    {
        $this->delete('id', $id);
    }
    
    /** 	 
     * Method to return the total number of archived submissions 	 
     * 	 
     * @access publice 	 
     * @return int $count 	 
     */ 	 
     function getCount() 	 
     { 	 
         $sql = 'SELECT count(*) AS cnt FROM '.$this->table; 	 
         $sql .= " WHERE submissionType = '".$this->subType."' and status = 'archived'"; 	 
  	 
         $data = $this->getArray($sql); 	 
         if(!empty($data)){ 	 
             return $data[0]['cnt']; 	 
         } 	 
         return 0; 	 
     }
}
?>

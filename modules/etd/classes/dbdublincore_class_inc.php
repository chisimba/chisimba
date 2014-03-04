<?php
/**
* dbDublinCore class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbDublinCore class for managing the dublin core metadata.
* The class accesses the dublincore class in the dublincoremetadata class for moving metadata
* through the tbl_dublincoremetadata table.
*
* @author Megan Watson
* @author Jonathan Abrahams
* @copyright (c) 2005 UWC
* @version 0.2
* @modified by Megan Watson on 2006 11 04 Ported to 5ive / chisimba
*/

class dbDublinCore extends object
{
    /**
    * Constructor method
    */
    public function init()
    {
        $this->dbDublinCore = $this->getObject('dublincore', 'dublincoremetadata');
        $this->dbThesis = $this->getObject('dbthesis', 'etd');
//        $this->dbQualified = $this->getObject('dbqualified', 'etd');
        $this->dcTable = $this->dbDublinCore->table;
        $this->thesisTable = $this->dbThesis->table;

        $this->objUser = $this->getObject('user', 'security');
    }
/*
    function patch()
    {
        $sql = "SELECT dc.id, dc.url, th.datecreated, th.updated FROM {$this->dcTable} AS dc, $this->thesisTable AS th 
        WHERE dc.id = th.dcmetaid";
        
        $data = $this->dbDublinCore->getArray($sql);
        
        if(!empty($data)){
            foreach($data as $item){
                $url = ''; $new_url = ''; 
                $id = $item['id'];
                //$url = $item['url'];
                
                //$new_url = urlencode(urldecode($url));
                
                //echo '<p>Url: '.$url."<br /> ".$new_url.'</p>';
                
                //$fields['url'] = $new_url;
                $fields['enterdate'] = $item['datecreated'];
                $fields['datestamp'] = isset($item['updated']) ? $item['updated'] : $item['datecreated'];
                $fields['updated'] = isset($item['updated']) ? $item['updated'] : $item['datecreated'];
                $this->dbDublinCore->update('id', $id, $fields);
            }
        }
    }
*/

    /**
    * Method to insert the metadata extracted from xml into the dublincore table.
    *
    * @access public
    * @param array $xml The xml file as an array
    * @param string $submitId The submission to link to in the database
    * @return array The id of the data in the dublincore and extended thesis tables
    */
    public function moveXmlToDb($xml, $submitId)
    {
        $dcData = $xml['metadata']['dublincore'];
        $dcId = $this->dbDublinCore->addMetaData($dcData);
        
        $thesisData = $xml['metadata']['thesis'];
        $thesisData['dcMetaId'] = $dcId;
        $thesisData['submitId'] = $submitId;
        $thesisId = $this->dbThesis->insertMetadata($thesisData);
        return array('dcId' => $dcId, 'thesisId' => $thesisId);
    }

    /**
    * Method to insert the metadata extracted from xml into the dublincore table.
    *
    * @access public
    * @param array $xml The xml file as an array
    * @param string $submitId The submission to link to in the database
    * @return array The id of the data in the dublincore and extended qualified tables
    *
    public function moveQualifiedXmlToDb($xml, $submitId)
    {
        $dcData = $xml['metadata']['dublincore'];
        $dcId = $this->dbDublinCore->addMetaData($dcData);

        $qualifiedData = $xml['metadata']['qualified'];
        $qualifiedData['dcMetaId'] = $dcId;
        $qualifiedData['submitId'] = $submitId;
        $qualifiedId = $this->dbQualified->insertMetadata($qualifiedData);
        return array('dcId' => $dcId, 'qualId' => $qualifiedId);
    }

    /**
    * Method to update a set of elements in the metadata
    *
    * @access public
    * @param array $fields The fields and values to add / update in the table row.
    * @param string $id The table row to update.
    * @return string $id The pk id of the row.
    */
    public function updateElement($fields, $id)
    {
        $id = $this->dbDublinCore->addMetaData($fields, $id);
        return $id;
    }

    /**
    * Method to insert the metadata for an etd into the database.
    *
    * @deprecated
    *
    public function addMetaData($id)
    {
        $fields = array();
        $fields['dc_publisher'] = $this->getParam('publisher');
        $fields['dc_contributor'] = $this->getParam('contributor');
        $fields['dc_type'] = $this->getParam('type');
        $fields['dc_format'] = $this->getParam('format');
        $fields['dc_identifier'] = $this->getParam('identifier');
        $fields['dc_source'] = $this->getParam('source');
        $fields['dc_language'] = $this->getParam('language');
        $fields['dc_relationship'] = $this->getParam('relation');
        $fields['dc_coverage'] = $this->getParam('coverage');

        $this->dbDublinCore->addMetaData($fields, $id);
        return $id;
    }

    /**
    * Method to insert the minimal metadata required for an etd.
    * This is usually inserted by the student.
    *
    * @deprecated
    *
    public function addThesisMetaData($id)
    {
        $surname = $this->getParam('surname', '');
        $firstname = $this->getParam('firstname', '');
        $name = $surname.$firstname;
        if(!empty($surname) && !empty($firstname)){
            $name = $surname.', '.$firstname;
        }

        $fields = array();
        $fields['dc_title'] = $this->getParam('title', '');
        $fields['dc_creator'] = $name;
        $fields['dc_subject'] = $this->getParam('keywords', '');
        $fields['dc_description'] = $this->getParam('abstract', '');
        $fields['dc_date'] = $this->getParam('year', '');

        $this->dbDublinCore->addMetaData($fields, $id);
        return $id;
    }
    
    /**
    * Method to permanently remove the metadata
    *
    * @access public
    * @param string $id
    * @return
    */
    public function deleteMetadata($id)
    {
        $this->dbDublinCore->deleteMetaData($id);
    }    
}
?>
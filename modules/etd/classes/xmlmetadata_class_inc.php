<?php
/**
* xmlmetadata class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* class for handling metadata using xml.
* @author Megan Watson
* @copyright (c) 2006 UWC
* @version $Id: xmlmetadata_class_inc.php 7059 2007-08-30 06:54:01Z megan $
*/

class xmlmetadata extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->etdFiles = $this->getObject('etdfiles', 'etd');
        $this->objXML = $this->getObject('xmlserial', 'utilities');
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
    * Method to save the XML to file
    *
    * @access public
    * @param array $xml The xml as an associative array
    * @param string $file The name of the xml file to save
    * @return
    */
    public function saveXML($xml, $file = 'testxml')
    {
        $this->etdFiles->createFile($xml, $file);
    }

    /**
    * Method to open/read the XML from file.
    *
    * @access public
    * @param string $file The name of the xml file to open
    * @return
    */
    public function openXML($file = 'testxml')
    {
        $ext = '.xml';
        $path = $this->etdFiles->getPath();
        if($this->etdFiles->checkFile($path.$file, $ext)){
            $xml = $this->objXML->readXML($path.$file.$ext);
        }else{
            $xml = array();
        }
        
        return $xml;
    }

    /**
    * Method to delete the XML file.
    *
    * @access public
    * @param string $file The name of the xml file to delete
    * @return
    */
    public function deleteXML($file = 'testxml')
    {
        $ext = '.xml';
        $this->etdFiles->removeFile($file, $ext);
        return TRUE;
    }

    /**
    * Method to set a single xml element.
    *
    * @access public
    * @param string $value The value to set the element to
    * @param string $file The name of the xml file to open
    * @param string $field The xml element name
    * @param string $type The outer xml element
    * @return
    */
    public function setXMLField($submitId, $value, $file, $field = 'dc_rights', $type = 'dublincore')
    {
        // Get saved xml
        $xml = $this->openXML($file);

        $xml['metadata']['modifierId'] = $this->objUser->userId();
        $xml['metadata']['dateModified'] = date('Y-m-d H:i:s');
        $xml['metadata'][$type][$field] = $value;

        $xml = $this->objXML->writeXML($xml);
        $this->saveXML($xml, $file);
    }

    /**
    * Method to save the document details and metadata as xml.
    * The data is stored in an array and converted to xml. On updating, the modified data is compared
    * to the stored xml and only modified fields are saved.
    *
    * @access public
    * @param array $data The new data to save to xml
    * @param string $submitId The current submission id
    * @param string $file The xml file
    * @return
    */
    function saveToXml($data, $file)
    {
        $xmlArr = array();

        // Get saved xml
        $xml = $this->openXML($file);
        
//        if(empty($xml)){
//            $xmlArr['metadata']['extra']['creatorId'] = $this->objUser->userId();
//            $xmlArr['metadata']['extra']['dateCreated'] = date('Y-m-d H:i:s');
//            
//            $xmlArr = array_merge($xmlArr, $data); 
//        }else{
//            $xmlArr['metadata']['extra']['creatorId'] = $xml['metadata']['extra']['creatorId'];
//            $xmlArr['metadata']['extra']['dateCreated'] = $xml['metadata']['extra']['dateCreated'];
//
//            $xmlArr['metadata']['extra']['modifierId'] = $this->objUser->userId();
//            $xmlArr['metadata']['extra']['dateModified'] = date('Y-m-d H:i:s');
//            
//            $xmlArr = array_merge($xmlArr, $data);
//        }
        $xmlArr = $data;
        
        // Create xml and write to file
        $xmlNew = $this->objXML->writeXML($xmlArr);
        $this->saveXML($xmlNew, $file);
    }
    
    /**
    * Method to save the document details and metadata as xml.
    * The data is stored in an array and converted to xml. On updating, the modified data is compared
    * to the stored xml and only modified fields are saved.
    *
    * @deprecated
    */
    function _saveToXml($submitId, $file)
    {
        $xmlArr = array();

        // Get saved xml
        $xml = $this->openXML($file);

        if(empty($xml)){
            $xmlArr['metadata']['creatorId'] = $this->objUser->userId();
            $xmlArr['metadata']['dateCreated'] = date('Y-m-d H:i:s');
        }else{
            $xmlArr['metadata']['creatorId'] = $xml['metadata']['creatorId'];
            $xmlArr['metadata']['dateCreated'] = $xml['metadata']['dateCreated'];

            $xmlArr['metadata']['modifierId'] = $this->objUser->userId();
            $xmlArr['metadata']['dateModified'] = date('Y-m-d H:i:s');
        }

        $xmlArr['metadata']['submissionId'] = $submitId;

        $surname = $this->getParam('surname', '');
        $firstname = $this->getParam('firstname', '');
        $name = $surname.$firstname;
        if(!empty($surname) && !empty($firstname)){
            $name = $surname.', '.$firstname;
        }

        $numAuthors = $this->getParam('numauthors', 0);
        if($numAuthors > 0){
            $authors = '';
            for($i = 1; $i <= $numAuthors; $i++){
                $surname = $this->getParam('surname'.$i, '');
                $firstname = $this->getParam('firstname'.$i, '');
                $addName = $surname.$firstname;

                if(!empty($surname) && !empty($firstname)){
                    $addName = $surname.', '.$firstname;
                }
                if(!empty($addName)){
                    $authors .= '; '.$addName;
                }
            }
            if(!empty($authors)){
                $name = $name.$authors;
            }
        }

        // Get the country as the collection the resource will be part of
        $country = $this->getParam('country', NULL);
        if(empty($country) && isset($xml['metadata']['collections']['country'])){
            $xmlArr['metadata']['collections']['country'] = $xml['metadata']['collections']['country'];
        }else{
            $xmlArr['metadata']['collections']['country'] = $country;
        }

        /* ** Dublin core metadata ** */
        if(empty($name) && isset($xml['metadata']['dublincore']['dc_creator'])){
            $xmlArr['metadata']['dublincore']['dc_creator'] = $xml['metadata']['dublincore']['dc_creator'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_creator'] = $name;
        }

        $title = $this->getParam('title', NULL);
        if(is_null($title) && isset($xml['metadata']['dublincore']['dc_title'])){
            $xmlArr['metadata']['dublincore']['dc_title'] = $xml['metadata']['dublincore']['dc_title'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_title'] = $title;
        }

        $subject = $this->getParam('keywords', NULL);
        if(is_null($subject) && isset($xml['metadata']['dublincore']['dc_subject'])){
            $xmlArr['metadata']['dublincore']['dc_subject'] =  $xml['metadata']['dublincore']['dc_subject'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_subject'] = $subject;
        }

        $description = $this->getParam('abstract', NULL);
        if(is_null($description) && isset($xml['metadata']['dublincore']['dc_description'])){
            $xmlArr['metadata']['dublincore']['dc_description'] = $xml['metadata']['dublincore']['dc_description'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_description'] = $description;
        }

        $date = $this->getParam('year', NULL);
        if(is_null($date) && isset($xml['metadata']['dublincore']['dc_date'])){
            $xmlArr['metadata']['dublincore']['dc_date'] = $xml['metadata']['dublincore']['dc_date'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_date'] = $date;
        }

        $publisher = $this->getParam('publisher', NULL);
        if(is_null($publisher) && isset($xml['metadata']['dublincore']['dc_publisher'])){
            $xmlArr['metadata']['dublincore']['dc_publisher'] = $xml['metadata']['dublincore']['dc_publisher'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_publisher'] = $publisher;
        }

        $contributor = $this->getParam('contributor', NULL);
        if(is_null($contributor) && isset($xml['metadata']['dublincore']['dc_contributor'])){
            $xmlArr['metadata']['dublincore']['dc_contributor'] = $xml['metadata']['dublincore']['dc_contributor'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_contributor'] = $contributor;
        }

        $type = $this->getParam('type', NULL);
        if(is_null($type) && isset($xml['metadata']['dublincore']['dc_type'])){
            $xmlArr['metadata']['dublincore']['dc_type'] = $xml['metadata']['dublincore']['dc_type'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_type'] = $type;
        }

        $format = $this->getParam('format', NULL);
        if(is_null($format) && isset($xml['metadata']['dublincore']['dc_format'])){
            $xmlArr['metadata']['dublincore']['dc_format'] = $xml['metadata']['dublincore']['dc_format'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_format'] = $format;
        }

        $identifier = $this->getParam('identifier', NULL);
        if(is_null($identifier) && isset($xml['metadata']['dublincore']['dc_identifier'])){
            $xmlArr['metadata']['dublincore']['dc_identifier'] = $xml['metadata']['dublincore']['dc_identifier'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_identifier'] = $identifier;
        }

        $source = $this->getParam('source', NULL);
        if(is_null($source) && isset($xml['metadata']['dublincore']['dc_source'])){
            $xmlArr['metadata']['dublincore']['dc_source'] = $xml['metadata']['dublincore']['dc_source'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_source'] = $source;
        }

        $language = $this->getParam('language', NULL);
        if(is_null($language) && isset($xml['metadata']['dublincore']['dc_language'])){
            $xmlArr['metadata']['dublincore']['dc_language'] = $xml['metadata']['dublincore']['dc_language'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_language'] = $language;
        }

        $relation = $this->getParam('relation', NULL);
        if(is_null($relation) && isset($xml['metadata']['dublincore']['dc_relationship'])){
            $xmlArr['metadata']['dublincore']['dc_relationship'] = $xml['metadata']['dublincore']['dc_relationship'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_relationship'] = $relation;
        }

        $rights = $this->getParam('rights', NULL);
        if(is_null($rights) && isset($xml['metadata']['dublincore']['dc_rights'])){
            $xmlArr['metadata']['dublincore']['dc_rights'] = $xml['metadata']['dublincore']['dc_rights'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_rights'] = $rights;
        }

        $coverage = $this->getParam('coverage', NULL);
        if(is_null($coverage) && isset($xml['metadata']['dublincore']['dc_coverage'])){
            $xmlArr['metadata']['dublincore']['dc_coverage'] = $xml['metadata']['dublincore']['dc_coverage'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_coverage'] = $coverage;
        }
        
        $audience = $this->getParam('audience', NULL);
        if(is_null($audience) && isset($xml['metadata']['dublincore']['dc_audience'])){
            $xmlArr['metadata']['dublincore']['dc_audience'] = $xml['metadata']['dublincore']['dc_audience'];
        }else{
            $xmlArr['metadata']['dublincore']['dc_audience'] = $audience;
        }


        /* *** Thesis Metadata *** */
        $degree = $this->getParam('degree', NULL);
        if(is_null($degree) && isset($xml['metadata']['thesis']['thesis_degree_name'])){
            $xmlArr['metadata']['thesis']['thesis_degree_name'] = $xml['metadata']['thesis']['thesis_degree_name'];
        }else{
            $xmlArr['metadata']['thesis']['thesis_degree_name'] = $degree;
        }

        $degree = $this->getParam('degree', NULL);
        if(is_null($degree) && isset($xml['metadata']['thesis']['thesis_degree_level'])){
            $xmlArr['metadata']['thesis']['thesis_degree_level'] = $xml['metadata']['thesis']['thesis_degree_level'];
        }else{
            $xmlArr['metadata']['thesis']['thesis_degree_level'] = $degree;
        }

        $department = $this->getParam('department', NULL);
        if(is_null($department) && isset($xml['metadata']['thesis']['thesis_degree_discipline'])){
            $xmlArr['metadata']['thesis']['thesis_degree_discipline'] = $xml['metadata']['thesis']['thesis_degree_discipline'];
        }else{
            $xmlArr['metadata']['thesis']['thesis_degree_discipline'] = $department;
        }

        $grantor = $this->getParam('grantor', NULL);
        if(is_null($grantor) && isset($xml['metadata']['thesis']['thesis_degree_grantor'])){
            $xmlArr['metadata']['thesis']['thesis_degree_grantor'] = $xml['metadata']['thesis']['thesis_degree_grantor'];
        }else{
            $xmlArr['metadata']['thesis']['thesis_degree_grantor'] = $grantor;
        }

        /* *** Qualified Metadata *** */

//        $place = $this->getParam('place', NULL);
//        if(is_null($place) && isset($xml['metadata']['qualified']['publicationPlace'])){
//            $xmlArr['metadata']['qualified']['publicationPlace'] = $xml['metadata']['qualified']['publicationPlace'];
//        }else{
//            $xmlArr['metadata']['qualified']['publicationPlace'] = $place;
//        }
//
//        $citation = $this->getParam('citation', NULL);
//        if(is_null($citation) && isset($xml['metadata']['qualified']['citation'])){
//            $xmlArr['metadata']['qualified']['citation'] = $xml['metadata']['qualified']['citation'];
//        }else{
//            $xmlArr['metadata']['qualified']['citation'] = $citation;
//        }

        $xml = $this->objXML->writeXML($xmlArr);
        $this->saveXML($xml, $file);
    }
}
?>
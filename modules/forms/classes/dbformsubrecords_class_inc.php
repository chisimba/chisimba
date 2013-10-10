<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the forms module.
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert
*/

    class dbformsubrecords extends dbTable
    {

        /**
        * The user object
        *
        * @access private
        * @var object
        */
        protected $_objUser;


        /**
        * The dbfrontpage object
        *
        * @access private
        * @var object
        */
        protected $_objFrontPage;

        /**
        * The language object
        *
        * @access private
        * @var object
        */
        protected $_objLanguage;

        /**
        * The blocks object
        *
        * @access private
        * @var object
        */
        protected $_objBlocks;

        /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                parent::init('tbl_form_subrecords');
                $this->table = 'tbl_form_subrecords';
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->_objSysConf = $this->getObject('dbsysconfig', 'sysconfig');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
         * Method to add a form element name/value pair to the database
         * This element maps to the record being submitted.
         *
         * @access public
         * @return bool
         */
        public function add($recordId, $name, $value)
        {
            $fields = array(
                          'record_id' => $recordId ,
                          'name' => $name ,
                          'value' => $value
            );
            $newId = $this->insert($fields);

            return $newId;
        }


        /**
         * Method to edit a record
         *
         * @access public
         * @return bool
         */
        public function edit()
        {
            $id = $this->getParam('id', '');

            //Get details of the new entry
            $title = $this->getParam('title');
            $imagePath = $this->getParam('imagepath',null);
            $description = $this->getParam('description');
            $published = ($this->getParam('published') == '1') ? 1 : 0;
            $creatorid = $this->getParam('creator',null);
            if ($creatorid==NUll) {
                $creatorid = $this->_objUser->userId();
            }
            $fullText = $this->getParam('body');
            $fullText = str_ireplace("<br />", " <br /> ", $fullText);
            $created_by = $this->getParam('title_alias',null);

            $newArr = array(
                          'title' => $title ,
                          'description' => $description ,
                          'image' => $imagePath ,
                          'body' => addslashes($fullText),
                          'published' => $published,
                          'created' => $this->now(),
                          'created_by' => $creatorid
            );

            $result = $this->update('id', $id, $newArr);

            //Saving the FCKEditor Forms XML File
            $this->saveXml();
            
            return $result;
        }

        /**
         * Method to update a forms record's body text
         *
         * @param string $id The id of the record that needs to be changed
         * @access public
         * @return bool
         */
        public function updateFormBody($formsid, $body)
        {  
            $fields['body'] = $body;
            $this->update('id', $formsid, $fields);	
            return TRUE;
        }


        /**
        * Method to delete a forms
        *
        * @param string $id The id of the forms
        * @return boolean
        * @access public
        */
        public function deleteForm($id)
        {
            //Delete Form
            $result = $this->delete('id', $id);
            
            return $result;
        }

        /**
         * Method to get the published forms
         *
         * @param string $filter The Filter
         * @return  array An array of associative arrays of all forms pages in relationto filter specified
         * @access public
         */
        public function getPublishedForms()
        {
                $filter = ' WHERE published=1 ';
            
            //return $this->getAll($filter.' ORDER BY ordering'); //TODO: Will implement ordering for formss at a later stage
            return $this->getAll($filter);
        }

        /**
         * Method to get all forms
         *
         * @return  array An array of associative arrays of all forms pages in relationto filter specified
         * @access public
         */
        public function getForms()
        {
            return $this->getAll();
        }

        
        /**
         * Method to toggle the publish field
         *
         * @param string id The id if the forms
         * @access public
         * @return boolean
         * @author Wesley Nitsckie
         */
        public function togglePublish($id)
        {
            $row = $this->getFormPage($id);

            if ($row['published'] == 1) {
                return $this->update('id', $id , array('published' => 0, 'end_publish' => $this->now(), 'start_publish' => '') );
            } else {
                return $this->update('id', $id , array('published' => 1, 'start_publish' => $this->now()) );
            }
        }
        
        /**
         * Method to publish or unpublish forms 
         * 
         * @param string id The id if the forms
         * @param string $task Publish or unpublish
         * @access public
         * @return boolean
         * @author Megan Watson, Charl Mert
         */
        public function publish($id, $task = 'publish')
        {
            switch($task){
                case 'publish':
                $fields['published'] = 1;
                break;
                case 'unpublish':
                $fields['published'] = 0;
                break;
            }
            $newId = $this->update('id', $id, $fields);

            //Saving the FCKEditor Forms XML File
            $this->saveXml();
            
            return $newId;
        }


        /**
         * Method to scrub grubby html
         *
         * @param string $document
         * @return string
         */
        public function html2txt($document, $scrub = TRUE)
        {
            if($scrub == TRUE)
            {
                $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                   /*'@<[\/\!]*?[^<>]*?>@si',*/            // Strip out HTML tags
                   /*'@<style[^>]*?>.*?</style>@siU',*/    // Strip style tags properly
                   '@<![\s\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
                );

            }
            else {
                $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                   '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                   /*'@<style[^>]*?>.*?</style>@siU',*/    // Strip style tags properly
                   '@<![\s\S]*?--[ \t\n\r]*>@',        // Strip multi-line comments including CDATA
                   '!(\n*(.+)\n*!x',                   //strip out newlines...
                );
            }
            $text = preg_replace($search, '', $document);
            $text = str_replace("<br /><br />", '' ,$text);
            //$text = str_replace("<br />", '' ,$text);
            //$text = str_replace( '\n\n\n' , '\n' ,$text);
            $text = str_replace("<br />  <br />", "<br />", $text);
            $text = str_replace("<br\">","",$text);
            $text = str_replace("<br />", " <br /> ", $text);
            //$text = str_replace("<", " <", $text);
            //$text = str_replace(">", "> ", $text);
            $text = rtrim($text, "\n");
            return $text;
        }
        
        /**
         * The method implements the lucene indexer
         * The method accepts an array of data,
         * generates a document to be indexed based on the
         * url and forms inserted into the database 
         *
         * @param array $data
         */
        public function luceneIndex($data)
        {
            $objLucene = $this->getObject('indexdata', 'search');
        
            $docId = 'forms_page_'.$data['id'];
        
            $url = $this->uri(array
                ('module' => 'forms', 
                            'action' => 'showform', 
                            'id' => $data['id'],
                            'sectionid'=> $data['sectionid']), 'cms');
        
            $objLucene->luceneIndex($docId, $data['created'], $url, $data['title'], $data['title'].$data['body'], $data['introtext'], 'cms', $data['created_by']);
        }


    }

?>

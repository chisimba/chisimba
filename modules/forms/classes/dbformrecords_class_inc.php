<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the forms module.
* Used to access the data from the form records
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert
*/

    class dbformrecords extends dbTable
    {

        /**
        * The user object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

        /**
        * The language object
        *
        * @access private
        * @var object
        */
        protected $_objLanguage;

        /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                parent::init('tbl_form_records');
                $this->table = 'tbl_form_records';
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->_objSysConf = $this->getObject('dbsysconfig', 'sysconfig');
 
                $this->objForm = & $this->getObject('dbforms', 'forms');
                $this->objFormElements = & $this->getObject('dbformsubrecords', 'forms');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
         * Method to add a form record to the database
         *
         * 
         * @access public
         * @param string $formId The id of the record that needs to be changed
         * @return bool
         */
        public function add($formId)
        {

            if (!isset($formId)) {
                $formId = $this->getParam('form_id', '');
            }

            $formdata = $this->objForm->getForm($formId);
            
            $fields['name'] = $formdata['name'];
            $fields['title'] = $formdata['title'];

            $fields['ip'] = $_SERVER['REMOTE_ADDR'];
            $fields['browser'] = $_SERVER['HTTP_USER_AGENT'];

            //Adding Record
            $recordId = $this->insert($fields);

            //Extracting the form details from the php request variables

            if (isset($formdata['method'])) {
                $method = $formdata['method'];
            } else {
                $method = 'POST';
            }

            if ($method == 'POST') {

                foreach ($_POST as $key=>$value) {
                    $this->objFormElements->add($recordId, $key, $value);                 
                }

            } else {

                foreach ($_GET as $key=>$value) {
                    $this->objFormElements->add($recordId, $key, $value);                 
                }
            }
    
            return $recordId;
        }

        /**
        * Method to delete a forms
        *
        * @param string $id The id of the forms
        * @return boolean
        * @access public
        */
        public function delete($id)
        {
            //Delete Form
            $this->delete('id', $id);
            return true;
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
            
            //return $this->getAll($filter.' ORDER BY ordering'); //TODO: Will implement ordering for forms at a later stage
            return $this->getAll($filter);
        }

        /**
         * Method to get all forms
         *
         * @return  array An array of associative arrays of all forms pages in relationto filter specified
         * @access public
         */
        public function getRecords()
        {
            return $this->getAll();
        }

        /**
         * Method to get a specific record
         *
         * @param string $recordId
         * @return  array An array of associative arrays of all records in relationto filter specified
         * @access public
         */
        public function getRecord($recordId)
        {
            $data = $this->getAll(" WHERE id = '$recordId'");
            if (isset($data[0])) {
                return $data[0];
            } else {
                return FALSE;
            }
        }

        /**
         * Method to get the content for a form
         *
         * @param string $formId
         * @return  array An array of associative arrays of all forms pages in relation to filter specified
         * @access public
         */
        public function getFormContent($formId)
        {
            $query = "select body from tbl_forms WHERE id = '$formId'";
            $data = $this->query($query);
            return $data[0];
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

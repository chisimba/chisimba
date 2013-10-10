<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
* Data access class for the formelements module.
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Charl Mert
*/

    class dbformelements extends dbTable
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
        * @var string $formelementsBasePath Path to fckeditor formelementss (not uri but file access path)
        */
        public $formelementsBasePath;

        /**
        * @var string $fck_version Which version of FCKEditor to load (2.5.1 vs 2.6.3)
        */
        public $fckVersion;


        /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                parent::init('tbl_formelements');
                $this->table = 'tbl_formelements';
                $this->_objUser = & $this->getObject('user', 'security');
                $this->_objLanguage = & $this->newObject('language', 'language');
                $this->_objSysConf = $this->getObject('dbsysconfig', 'sysconfig');
                //Loading the default FCK version from config
                $this->fckVersion = $this->_objSysConf->getValue('FCKEDITOR_VERSION', 'htmlelements');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
         * Method to add a form to the database
         *
         * @access public
         * @return bool
         */
        public function add()
        {
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
            $created_by = $this->getParam('title_alias',null);
            $fullText = str_ireplace("<br />", " <br /> ", $fullText);

            $newArr = array(
                          'title' => $title ,
                          'description' => $description ,
                          'image' => $imagePath ,
                          'body' => addslashes($fullText),
                          'published' => $published,
                          'created' => $this->now(),
                          'created_by' => $creatorid
            );
            $newId = $this->insert($newArr);
            $newArr['id'] = $newId;

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

            //Saving the FCKEditor Element XML File
            $this->saveXml();
            
            return $result;
        }

        /**
         * Method to update a formelements record's body text
         *
         * @param string $id The id of the record that needs to be changed
         * @access public
         * @return bool
         */
        public function updateFormBody($formelementsid, $body)
        {  
            $fields['body'] = $body;
            $this->update('id', $formelementsid, $fields);	
            return TRUE;
        }


        /**
        * Method to delete a formelements
        *
        * @param string $id The id of the formelements
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
         * Method to get the published formelements
         *
         * @param string $filter The Filter
         * @return  array An array of associative arrays of all formelements pages in relationto filter specified
         * @access public
         */
        public function getPublishedElement()
        {
                $filter = ' WHERE published=1 ';
            
            //return $this->getAll($filter.' ORDER BY ordering'); //TODO: Will implement ordering for formelementss at a later stage
            return $this->getAll($filter);
        }

        /**
         * Method to get all formelements
         *
         * @return  array An array of associative arrays of all formelements pages in relationto filter specified
         * @access public
         */
        public function getElements()
        {
            return $this->getAll();
        }

    }

?>

<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check
/**
* Data access class for the cmsadmin module. Used to access data in the sections table. 
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR 
* @license GNU GPL
* @author Wesley Nitsckie
* @author Warren Windvogel
*/

class dbsections extends dbTable
{

        /**
        * The dbcontent object
        *
        * @access private
        * @var object
        */
        protected $_objDBContent;

        /**
        * The language object
        *
        * @access private
        * @var object
        */
        protected $_objLanguage;
        /**
         * The user object
         *
         * @var object
         */
        protected $_objUser;
        /**
	     * The sections  object
	     *
	     * @access private
	     * @var object
	    */
	    protected $TreeNodes;


	   /**
	    * Class Constructor
	    *
	    * @access public
	    * @return void
	    */
        public function init()
        {
        	try {        
                parent::init('tbl_cms_sections');
                $this->table = 'tbl_cms_sections';
                $this->_objDBContent = $this->getObject('dbcontent', 'cmsadmin');
                $this->_objLanguage = $this->getObject('language', 'language');
                $this->_objUser =  $this->getObject('user', 'security');
                $this->_objGroupAdmin =  $this->getObject('groupadminmodel', 'groupadmin');
				$this->_objSecurity = $this->getObject('dbsecurity', 'cmsadmin');
                $this->TreeNodes = & $this->newObject('treenodes', 'cmsadmin');
           } catch (Exception $e){
       		    throw customException($e->getMessage());
        	    exit();
     	   }
        }

        /**
         * Method to get the list of sections
         *
         * @access public
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array An array of associative arrays of all sections
         */
        public function getSections($isPublished = NULL, $filter = null)
        {
            if ($isPublished||$filter==null) {
                return $this->getAll('WHERE published = 1 AND trash = 0 ORDER BY ordering');
            }elseif(!$isPublished||$filter!=null) {
                return $this->getAll('WHERE title LIKE '%".$filter."%' AND trash = 0 ORDER BY ordering');
            }
           // if ($filter!=null) {
            ///	return $this->getAll('WHERE title LIKE '%".$filter."%' ORDER BY ordering');
           // }
        }
		
	   /**
        * Method to get a filtered list of sections
        *
        * @author Megan Watson
        * @access public
        * @return array An array of associative arrays of the sections
        */
        public function getFilteredSections($text = '', $publish = FALSE)
        {
            $sql = "SELECT * FROM {$this->table} ";
            $filter = '';
            if(!($publish === FALSE)){
                $filter .= "published = {$publish} ";
            }
            
            if(!empty($text)){
                if(!empty($filter)){
                    $filter .= ' AND ';
                }
                $filter .= "(LOWER(title) LIKE '%".strtolower($text)."%' OR LOWER(menutext) LIKE '%".strtolower($text)."%')";
            }
            
            if(!empty($filter)){
                $sql .= "WHERE {$filter} AND trash = 0 ";
            }else{
                $sql .= "WHERE trash = 0 ";
            }
            $sql .= ' ORDER BY ordering';
            
            return $this->getArray($sql);
        }

        /**
         * Method to get the archived content
         *
         * @author Megan Watson
         * @param string $filter The Filter
         * @return  array An array of associative arrays of all content pages in relationto filter specified
         * @access public
         */
        public function getArchiveSections($filter = '')
        {
            $sql = "SELECT * FROM {$this->table} WHERE trash = 1 ";
            
            if(!empty($filter)){
                $sql .= "AND LOWER(title) LIKE '%".strtolower($filter)."%' ";
            }
            
            $sql .= 'ORDER BY ordering';
            return $this->getArray($sql);
        }
        
        /**
         * Method to get the list of root nodes
         *
         * @access public
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @param string contextcode The current context the user is in
         * @return array An array of associative arrays of all root nodes
         */
        public function getRootNodes($isPublished = FALSE, $contextcode = NULL)
        {
            $sql = '';
            // Check for published / visible
            if($isPublished){
                $sql = 'published = 1 ';
            }
            // Check for the context code
            if(!empty($contextcode)){
                if(!empty($sql)){
                    $sql .= 'AND ';
                }
                $sql .= "contextcode = '$contextcode' ";
            }
            
            if(!empty($sql)){
                $sql .= 'AND ';
            }
            
            $filter = "WHERE {$sql} nodelevel = 1 AND trash = 0 ORDER BY ordering";
            $results = $this->getAll($filter);
	   
	    $secureSections = array();
	    //Filterring the list based on READ ACCESS
	    foreach ($results as $section){
		$section_id = $section['id'];
		if ($this->_objSecurity->canUserReadSection($section_id)){
			array_push($secureSections, $section);
		}
	    }

            return $secureSections;
        }

        /**
         * Method to get a Section
         *
         * @param  string $id The section id
         * @return array An array of the sections details
         * @access public
         */
        public function getSection($id)
        {
            return $this->getRow('id', $id);
        }


        /**
         * Method to return the Parent of the given sub section
         *
         * @access public
         * @return array (Parent items record) or false if record couldn't be found
         */
        public function getParent($sectionId){
            //getting the parent record
            $arrSection = $this->getArray("SELECT * FROM tbl_cms_sections WHERE id = '$sectionId'");
            return $arrSection[0];	
        }
        
        /**
         * Method to get the first sections id(pk)
         *
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return string First sections id
         * @access public
         */
        public function getFirstSectionId($isPublished = FALSE)
        {
            $firstSectionId = '';
            $firstSection = $this->getAll('WHERE parentid=0 AND trash = 0 ORDER BY ordering');
            if(!empty($firstSection)) {
                if($isPublished) {
                    foreach($firstSection as $section) {
                        if($section['published'] == 1) {
                            $firstSectionId = $section['id'];
                            break;
                        }
                    }
                } else {
                    $firstSectionId = $firstSection['0']['id'];
                }
            }
            return $firstSectionId;
        }

        /**
		 * Depricated: Use addSection instead
		 *
         * Method to add a section to the database
         *
         * @access public
         * @return bool
         */
        public function add($contextcode=null)
        {
            //get param from dropdown
            $parentSelected = $this->getParam('parent');
            //get parent type "subsection", "root" or "param is null"(new section will be root level) and its id
            $id = $parentSelected;
            $parentid = $id;

            if ($this->getLevel($parentid) == '0') {
                $rootid = $parentid;
                $rootnode = $this->checkindex($rootid);
                //Get section details
                $title = $this->getParam('title');
                $menuText = $this->getParam('menutext');
                $access = $this->getParam('access');
                $description = str_ireplace("<br />", " <br /> ",$this->getParam('introtext'));
                if ($description==' <br /> '){
                    $description='';
                }
                $published = $this->getParam('published');
                $layout = $this->getParam('display');
                $showdate = $this->getParam('showdate');
                $hidetitle = $this->getParam('hidetitle');
                $showintroduction = $this->getParam('showintro');
                $user = $this->_objUser->userId();

                //Preventing Duplicates (title as key)
                $checkData = $this->getSection($id);
                if (is_array($checkData)) {
                    return false;
                }

        		if($this->getParam('pagenum') == 'custom') {
                	$numpagedisplay = $this->getParam('customnumber');
                } else {
                	$numpagedisplay = $this->getParam('pagenum');
                }
                $ordertype = $this->getParam('pageorder');
                $ordering = $this->getOrdering($parentid);

                //Add section
                $index = array(
                'rootid' => $rootid,
                'parentid' => $parentid,
                'title' => $title,
                'menutext' => $menuText,
                'access' => $access,
                'layout' => $layout,
                'ordering' => $ordering,
                'description' => $description,
                'published' => $published,
                'hidetitle' => $hidetitle,
                'showdate' => $showdate,
                'showintroduction' => $showintroduction,
                'numpagedisplay' => $numpagedisplay,
                'ordertype' => $ordertype,
                'nodelevel' => $this->getLevel($parentid) + '1',
                'datecreated'=>$this->now(),
                'userid' => $user,
                'link' => $this->getParam('imagesrc'),
                'contextcode' =>$contextcode
                );

                $result = $this->insert($index);
                
                if ($result != FALSE) {
                    $index['id'] = $result;
                    $this->luceneIndex($index);
                    $this->_objSecurity->inheritSectionPermissions($result);
                }
                
                return $result;
               
             
            } else {

                $rootid = $this->getRootNodeId($id);
                $rootnode = $this->checkindex($rootid);
                //Get section details
                $title = $this->getParam('title');
                $menuText = $this->getParam('menutext');
                $access = $this->getParam('access');
                $description = str_ireplace("<br />", " <br /> ",$this->getParam('introtext'));
                if ($description==' <br /> '){
                    $description='';
                }
                $published = $this->getParam('published');
                $layout = $this->getParam('display');
                $showdate = $this->getParam('showdate');
                $hidetitle = $this->getParam('hidetitle');
                $showintroduction = $this->getParam('showintro');
                $user = $this->_objUser->userId();
                if($this->getParam('pagenum') == 'custom') {
                	$numpagedisplay = $this->getParam('customnumber');
                } else {
                	$numpagedisplay = $this->getParam('pagenum');
                }
                $ordertype = $this->getParam('pageorder');
                $ordering = $this->getOrdering($parentid);

                //Add section
                $index = array(
                'rootid' => $rootid,
                'parentid' => $parentid,
                'title' => $title,
                'menutext' => $menuText,
                'access' => $access,
                'layout' => $layout,
                'ordering' => $ordering,
                'description' => $description,
                'published' => $published,
                'showdate' => $showdate,
                'hidetitle' => $hidetitle,
                'showintroduction' => $showintroduction,
                'numpagedisplay' => $numpagedisplay,
                'ordertype' => $ordertype,
                'nodelevel' => $this->getLevel($parentid) + '1',
                'datecreated'=>$this->now(),
                'userid' => $user,
                'link' => $this->getParam('imagesrc'),
                'contextcode' =>$contextcode
                );
                
                $result = $this->insert($index);
                
                if ($result != FALSE) {
                    $index['id'] = $result;
                    $this->luceneIndex($index);
                    $this->_objSecurity->inheritSectionPermissions($result);
                }
                
                return $result;
              
                
            }
            
        }


        /**
         * Method to add a section to the database specifying all parameters
         *
         * @access public
         * @return bool
         */
        public function addSection($title,
								   $parentId = 0,
								   $menuText = '',
								   $access = null,
								   $description = '',
								   $published = 0,
								   $layout = 'page',
								   $showIntroduction = 0,
								   $showTitle = 'g',
								   $showAuthor = 'g',
								   $showDate = 'g',
								   $pageNum = '0',
								   $customNum = null,
								   $pageOrder = 'pagedate_asc',
								   $imageUrl = null,
								   $contextCode = null)
        {
			
			$user = $this->_objUser->userId();

            //if ($this->getLevel($parentId) == '0') {
                $rootId = $parentId;
                //$rootNode = $this->checkindex($rootId);
			//} else {
                //$rootId = $this->getRootNodeId($parentId);
                //$rootNode = $this->checkindex($rootId);
            //}
			
			$description = str_ireplace("<br />", " <br /> ",$description);
			
			//Preventing Duplicates (title, parentId as key)
			$isDuplicate = $this->isDuplicate($title,$parentId);

			if ($isDuplicate == TRUE){
				return FALSE;
			}
			
			if($pageNum == 'custom') {
				$numPageDisplay = $customNum;
			} else {
				$numPageDisplay = $pageNum;
			}

			$ordering = $this->getOrdering($parentId);

			//Add section
			$index = array(
			'rootid' => $rootId,
			'parentid' => $parentId,
			'title' => $title,
			'menutext' => $menuText,
			'access' => $access,
			'layout' => $layout,
			'ordering' => $ordering,
			'description' => $description,
			'published' => $published,
			'show_introduction' => $showIntroduction,
			'show_title' => $showTitle,
			'show_user' => $showAuthor,
			'show_date' => $showDate,
			'numpagedisplay' => $numPageDisplay,
			'ordertype' => $pageOrder,
			'nodelevel' => $this->getLevel($parentId) + '1',
			'datecreated'=>$this->now(),
			'userid' => $user,
			'link' => $imageUrl,
			'contextcode' => $contextCode
			);

			$result = $this->insert($index);
			
			if ($result != FALSE) {
				$index['id'] = $result;
				$this->luceneIndex($index);
				$this->_objSecurity->inheritSectionPermissions($result);
			}
			
			return $result;
        }
        
        private function checkindex($rootid=null,$parentid=null){
        	
        	$rootid = $this->TreeNodes->getArtifact($rootid);
        	return $rootid;
        }


        /**
         * Method to check if a section exists
         *
         * @param string $name the title to check for duplicates against
         * @param string $the parent_id of the section range to check in (section may have the same name in other parents/children)
         * @access public
         * @return bool
         */
        public function isDuplicate($name, $parentid) {
			//Preventing Duplicates (title, parentId as key)
			$checkData = $this->query("SELECT id FROM tbl_cms_sections WHERE parentId = '$parentid' AND title = '$name'");
			if (isset($checkData[0]['id'])) {
				return TRUE;
			} else {
				return FALSE;				
			}
		}

        /**
		 * Depricated: Use addSection(params...) instead
         * Method to add a section to the database
         *
         * @param string $parent The id of the parent node. '0' for root nodes
         * @param string $title The title of the new section
         * @param string $menuText The text that will appear in the tree menu
         * @param bool $published Whether page will be visible or not
         * @param bool $access True if "registered" page False if "public" page
         * @param string $description The introduction text 
         * @param string $layout The layout type of the section
         * @param bool $showdate Whether date will be visible or not
         * @param bool $showintroduction Whether introduction will be visible or not
         * @param int $numpagedisplay Number of pages to display 
         * @param string $ordertype How the page should be ordered
         * @param string $contextCode The context code if you are using the cms as the context section manager
         * @access public
         * @return bool
         */
        public function addNewSection($parent, $title, $menuText, $access, $description, $published, $layout, $showdate, $showintroduction, $numpagedisplay, $ordertype, $contextCode = null)
        {
            //get param from dropdown
            $parentSelected = $parent;
            //get parent type "subsection", "root" or "param is null"(new section will be root level) and its id
            $id = $parentSelected;
            $parentid = $id;

            if ($this->getLevel($parentid) == '1' || $this->getLevel($parentid) == '0') {
                $rootid = $parentid;
            } else {
                $rootid = $this->getRootNodeId($id);
            }
            //Set ordering
            $ordering = $this->getOrdering($parentid);
            //Add section
            $newIndex =array(        'rootid' => $rootid,
                                     'parentid' => $parentid,
                                     'title' => $title,
                                     'menutext' => $menuText,
                                     'access' => $access,
                                     'layout' => $layout,
                                     'ordering' => $ordering,
                                     'description' => str_ireplace("<br />", " <br /> ",$description),
                                     'published' => $published,
                                     'showdate' => $showdate,
                                     'showintroduction' => $showintroduction,
                                     'numpagedisplay' => $numpagedisplay,
                                     'ordertype' => $ordertype,
                                     'nodelevel' => $this->getLevel($parentid) + '1',
                                     'contextcode' => $contextCode
                                 );
            $result = $this->insert($newIndex);
            
            if ($result != FALSE) {
                $newIndex['id'] = $result;
                $this->luceneIndex($newIndex);
            }
            
            return $result;
        }

        /**
		 * Depricated: use editSection instead
		 *
         * Method to edit a section in the database
         *
         * @access public
         * @return bool
         */
        public function edit()
        {
            //Get section details
            $id = $this->getParam('id');
            $parentid = $this->getParam('parent');
            $rootid = $this->getParam('rootid');
            $title = $this->getParam('title');
            $menuText = $this->getParam('menutext');
            $access = $this->getParam('access');
            $description = str_ireplace("<br />", " <br /> ",$this->getParam('introtext'));
            if ($description==' <br /> '){
               $description='';
            }
            $published = $this->getParam('published');
            $layout = $this->getParam('display');
            $showdate = $this->getParam('showdate');
            $hidetitle = $this->getParam('hidetitle');
            $showintroduction = $this->getParam('showintro');
            if($this->getParam('pagenum') == 'custom') {
                $numpagedisplay = $this->getParam('customnumber');
            } else {
                $numpagedisplay = $this->getParam('pagenum');
            }
            $ordertype = $this->getParam('pageorder');
            $ordering = $this->getParam('ordering');
            $count = $this->getParam('count');
            $arrFields = array(
                             'rootid' => $rootid,
                             'parentid' => $parentid,
                             'title' => $title,
                             'menutext' => $menuText,
                             'access' => $access,
                             'layout' => $layout,
                             'ordering' => $ordering,
                             'showdate' => $showdate,
                             'hidetitle' => $hidetitle,
                             'showintroduction' => $showintroduction,
                             'numpagedisplay' => $numpagedisplay,
                             'ordertype' => $ordertype,
                             'description' => $description,
                             'nodelevel' => $count,
                             'lastupdatedby'=> $this->_objUser->userid(),
                             'updated' => $this->now(),
                             'link' => $this->getParam('imagesrc'),
                             'published' => $published);
            $result = $this->update('id', $id, $arrFields);
    
            if ($result != FALSE) {
                $arrFields['id'] = $id;
                $this->luceneIndex($arrFields);
            }
            
            return $result;
        }


		/**
         * Method to edit a section to the database specifying all parameters
         *
         * @access public
         * @return bool
         */
        public function editSection($id,
								    $parentId = 0,
                                    $rootId = 0,
									$title,
								    $menuText = '',
								    $access = null,
								    $description = '',
								    $published = 0,
								    $layout = 'page',
								    $showIntroduction = 0,
								    $showTitle = 'g',
								    $showAuthor = 'g',
								    $showDate = 'g',
								    $pageNum = '0',
								    $customNum = null,
								    $pageOrder = 'pagedate_asc',
								    $imageUrl = null,
								    $contextCode = null)
        {
			$user = $this->_objUser->userId();

			if ($this->getLevel($parentId) == '0') {
                $rootId = $parentId;
			} else {
                $rootId = $this->getRootNodeId($parentId);
            }
			
            if($pageNum == 'custom') {
				$numPageDisplay = $customNum;
			} else {
				$numPageDisplay = $pageNum;
			}
			
			$ordering = $this->getOrdering($parentId);
			
            $arrFields = array(
                            'rootid' => $rootId,
							'parentid' => $parentId,
							'title' => $title,
							'menutext' => $menuText,
							'access' => $access,
							'layout' => $layout,
							'ordering' => $ordering,
							'description' => $description,
							'published' => $published,
							'show_introduction' => $showIntroduction,
							'show_title' => $showTitle,
							'show_user' => $showAuthor,
							'show_date' => $showDate,
							'numpagedisplay' => $numPageDisplay,
							'ordertype' => $pageOrder,
							'nodelevel' => $this->getLevel($parentId) + '1',
							'userid' => $user,
							'link' => $imageUrl,
							'contextcode' => $contextCode);
            $result = $this->update('id', $id, $arrFields);
    
            if ($result != FALSE) {
                $arrFields['id'] = $id;
                $this->luceneIndex($arrFields);
				return TRUE;
            }
            
            return $result;
        }

        /**
         * Method to check if there are any sections
         *
         * @access public
         * @return boolean
         */
        public function isSections()
        {
            $list = $this->getAll();

            if (count($list) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        /**
         * Method to get the menutext for a section
         *
         * @return string $menutext The title that will appear on the tree menu
         * @access public
         * @param string $id The id of the section
         */
        public function getMenuText($id)
        {
            $line = $this->getSection($id);
            
            $menutext = $line['menutext'];
            return $menutext;
        }

        /**
         * Method to toggle the publish field 
         * 
         * @param string id The id if the section
         * @access public
         * @return boolean
         * @author Wesley Nitsckie
         */
        public function togglePublish($id)
        {
            $row = $this->getSection($id);

            if ($row['published'] == 1) {
                return $this->update('id', $id , array('published' => 0) );
            } else {
                return $this->update('id', $id , array('published' => 1) );
            }
        }

        /**
         * Method to publish or unpublish sections 
         * 
         * @param string id The id if the section
         * @param string $task Publish or unpublish
         * @access public
         * @return boolean
         * @author Megan Watson
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
            
            return $this->update('id', $id, $fields);
        }

        /**
         * Method to check if a section has child/leaf node(s)
         *
         * @param string $id The id(pk) of the section
         * @return bool True if has nodes else False
         * @access public
         */
        public function hasNodes($id)
        {
			//Checking for child sections
            $nodes = $this->getAll("WHERE id = '$id' AND trash = 0");
            if (count($nodes) > 0) {
                $hasNodes = True;
            } else {
                $hasNodes = False;
				//Checking for child content items
				$this->_tableName = 'tbl_cms_content';
        	    $nodes = $this->getAll("WHERE sectionid = '$id' AND trash = 0");
				$this->_tableName = 'tbl_cms_sections';

	            if (count($nodes) > 0) {
	                $hasNodes = True;
	            } else {
	                $hasNodes = False;
				}
            }

            return $hasNodes;
        }

        /**
         * Method to check if a section has child content
         *
         * @param string $id The id(pk) of the section
         * @return bool True if has nodes else False
         * @access public
         */
        public function hasChildContent($id)
        {

            $hasNodes = false;
            //Checking for child content items
            $this->_tableName = 'tbl_cms_content';
            $nodes = $this->getAll("WHERE sectionid = '$id' AND trash = 0");

            if (count($nodes) > 0) {
                $hasNodes = true;
            } else {
                $hasNodes = false;
            }

            $this->_tableName = 'tbl_cms_sections';
            return $hasNodes;
        }

        /**
         * Method to check if a section has child sections
         *
         * @param string $id The id(pk) of the section
         * @return bool True if has nodes else False
         * @access public
         */
        public function hasChildSections($id)
        {

            $hasNodes = false;
            //Checking for child sections
            $nodes = $this->getAll("WHERE parentid = '$id' AND trash = 0");
            if (count($nodes) > 0) {
                $hasNodes = true;
            }

            return $hasNodes;
        }


        /**
         * Method to return the count value of a section
         *
         * @param string $id The id(pk) of the section
         * @return int $count The value of the count field
         * @access public
         */
        public function getLevel($id)
        {
            $count = 0;
            //get entry
            $section = $this->getRow('id', $id);

            if (!empty($section)) {
                //get and return value of count field
                $count = $section['nodelevel'];
            }

            return $count;
        }

        /**
         * Method to return a sections root node id
         *
         * @param string $id The id(pk) of the section
         * @return string $rootId The id(pk) of the sections root node
         * @access public
         */
        public function getRootNodeId($id)
        {
            //get entry
            $section = $this->getRow('id', $id);
            //get and return value of count field
            $rootId = $section['rootid'];
            return $rootId;
        }

        /**
        * Method to return all sections
        *
        * @access public
        */
        public function getAllSections()
        {
            return $this->getAll("WHERE trash = '0'");
        }


        /**
         * Method to get all subsections in a specific section
         *
         * @param string $sectionId The id(pk) of the section
         * @param int $level The node level in question  
         * @param string $order Either DESC or ASC
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array $subsections An array of associative arrays for all categories in the section
         * @access public
         */
        public function getSubSectionsInSection($sectionId, $order = 'ASC', $isPublished = FALSE)
        {
	        $this->_tableName = 'tbl_cms_sections';
			//echo "Section ID Get SubSections : ".$sectionId;
            if ($isPublished) {
                //return all subsections
		        $secureSections = array();
		        $sections = $this->getAll("WHERE published = 1 AND parentid = '$sectionId' AND trash = 0 ORDER BY ordering $order");
	
		        foreach ($sections as $sec){
			        if ($this->_objSecurity->canUserReadSection($sec['id'])){
				        array_push($secureSections, $sec);
			        }
		        }
    
                return $secureSections;
            } else {
        		$secureSections = array();
                $sections = $this->getAll("WHERE parentid = '$sectionId' AND trash = 0 ORDER BY ordering $order");
                foreach ($sections as $sec){
                        if ($this->_objSecurity->canUserReadSection($sec['id'])){
                                array_push($secureSections, $sec);
                        }
                }

		        return $secureSections;
            }
        }

        /**
         * Method to get all subsections in a specific root
         *
         * @param string $rootId The id(pk) of the section
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array $subsections An array of associative arrays for all categories in the section
         * @access public
         */
        public function getSubSectionsInRoot($rootId, $order = 'ASC',$isPublished = FALSE)
        {
	    
	    $this->_tableName = 'tbl_cms_sections';
            if ($isPublished) {
                //return all subsections
                return $this->getAll("WHERE published = 1 AND rootid = '$rootId' AND trash = 0 ORDER BY ordering");
            } else {
                return $this->getAll("WHERE rootid = '$rootId' AND trash = 0 ORDER BY ordering $order");
            }
        }

        /**
         * Method to get all subsections in a specific level
         *
         * @param string $rootId The id(pk) of the sections root node
         * @param int $level The node level in question  
         * @param int $order Either DESC or ASC 
         * @param bool $isPublished TRUE | FALSE To get published sections
         * @return array $subsections An array of associative arrays for all sub sections in the section
         * @access public
         */
        public function getSubSectionsForLevel($rootId, $level, $order = 'ASC', $isPublished = FALSE)
        {
            if ($isPublished) {
                //return all subsections
                return $this->getAll("WHERE published = 1 AND nodelevel = '$level' AND rootid = '$rootId' AND trash = 0 ORDER BY ordering $order");
            } else {
                return $this->getAll("WHERE nodelevel = '$level' AND rootid = '$rootId' AND trash = 0 ORDER BY ordering $order");
            }
        }

        /**
         * Method to get the number of sub sections in a section
         *
         * @param string $sectionId The id(pk) of the section
         * @return int $noSubSecs The number of subsections in the section
         * @access public
         */
        public function getNumSubSections($sectionId)
        {
	        $this->_tableName = 'tbl_cms_sections';
            $subSecs = $this->getAll("WHERE parentid = '$sectionId' AND trash = 0");
	        $noSubSecs = count($subSecs);
            return $noSubSecs;
        }

        /**
         * Method to delete a section
         *
         * @param string $id The id(pk) of the section
         * @return bool
         * @access public
         */
        public function deleteSection($id)
        {
            $sectionData = $this->getSection($id);
            
            // if section is root - archive everything below it
            if($sectionData['nodelevel'] == 1){
                $nodes = $this->getAll("WHERE rootid = '{$id}'");
                
                if(!empty($nodes)){
                    foreach($nodes as $item){
                        $this->_objDBContent->resetSection($item['id']);
                        $this->archive($item['id']);
                    }
                }
                // Restore root node
                $this->_objDBContent->resetSection($id);
                $this->archive($id);
            }else{
                // find nodes below section
                $nodeData = $this->getAll("WHERE parentid = '{$id}'");
                
                if(!empty($nodeData)){
                    foreach($nodeData as $item){
                        $this->deleteSection($item['id']);
                    }
                }
                $this->_objDBContent->resetSection($id);
                $this->archive($id);
            }
        }

        /**
        * Method to archive a section
        *
        * @access private
        * @param string $id The section id
        * @return bool
        */
        private function archive($id, $restore = FALSE)
        {
            $trash = 1;
            $order = '';
            
            if($restore){
                $trash = 0;
                $order = $this->getOrdering($id);
            }
            $fields = array('trash' => $trash, 'ordering' => $order);
            $result =  $this->update('id', $id, $fields);
            
            $this->removeLuceneIndex($id);
        }

        /**
        * Method to restore a section
        *
        * @access public
        * @param string $id The section id
        * @return bool
        */
        public function unarchiveSection($id)
        {
            $sectionData = $this->getSection($id);
            
            $this->luceneIndex($sectionData);
            
            if($sectionData['nodelevel'] == 1){
                $nodes = $this->getAll("WHERE rootid = '{$id}'");
                
                if(!empty($nodes)){
                    foreach($nodes as $item){
                        $this->unarchiveSectionsection($item['id']);
                        $this->archive($item['id'], TRUE);
                    }
                }
                // Restore root node
                $this->unarchiveSectionsection($id);
                $this->archive($id, TRUE);
            }else{
                // find nodes below section
                $nodeData = $this->getAll("WHERE parentid = '{$id}'");
                
                if(!empty($nodeData)){
                    foreach($nodeData as $item){
                        $this->unarchiveSection($item['id']);
                    }
                }
                $this->unarchiveSectionsection($id);
                $this->archive($id, TRUE);
            }
        }
        
        /**
        * Method to loop through and restore the section in a section
        *
        * @access private
        * @param string $id The section id
        * @return bool
        */
        private function unarchiveSectionsection($id)
        {
            return $this->_objDBContent->unarchiveSection($id);
        }

        /**
        * Method to permanently delete a section
        *
        * @access public
        * @param string $id The section id
        * @return bool
        */
        public function permanentlyDelete($id)
        {
            $result = $this->delete('id', $id);
            
            if ($result) {
                $this->removeLuceneIndex($id);
            }
            
            return $result;
        }

        /**
         * Method to return the ordering value of new section (gets added last)
         *
         * @param string $parentid The id(pk) of the parent. Uses root node order if NULL
         * @return int $ordering The value to insert into the ordering field
         * @access public
         * @author Warren Windvogel
         */
        public function getOrdering($parentid = NULL)
        {
            $ordering = 1;
            //get last order value
            $lastOrder = $this->getAll("WHERE parentid = '$parentid' AND trash = 0 ORDER BY ordering DESC LIMIT 1");
            //add after this value

            if (!empty($lastOrder)) {
                $ordering = $lastOrder['0']['ordering'] + 1;
            }

            return $ordering;
        }

        /**
         * Method to return the links to be displayed in the order column on the table
         * 
         * @param string $id The id of the entry 
         * @return string $links The html for the links
         * @access public
         * @return bool
         * @author Warren Windvogel
         */
        public function getOrderingLink($id)
        {
            //Get the parent id
            $entry = $this->getRow('id', $id);
            $parentId = $entry['parentid'];

            if (empty($parentId)) {
                //Get the number of root sections
                $lastOrd = $this->getAll("WHERE nodelevel = 1 AND trash = 0 ORDER BY ordering DESC LIMIT 1");
            } else {
                //Get the number of sub sections in section
                $lastOrd = $this->getAll("WHERE parentid = '$parentId' AND trash = 0 ORDER BY ordering DESC LIMIT 1");
            }

            $topOrder = $lastOrd['0']['ordering'];
            $links = " ";

            if ($topOrder > '1') {
                //Create geticon obj
                $this->objIcon = & $this->newObject('geticon', 'htmlelements');

                if ($entry['ordering'] == '1') {
                    //return down arrow link
                    //icon
                    $this->objIcon->setIcon('downend');
                    $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
                    //link
                    $downLink = & $this->newObject('link', 'htmlelements');
                    $downLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'up', 'parent' => $entry['parentid']));
                    $downLink->link = $this->objIcon->show();
                    $links .= $downLink->show();
                } else if ($entry['ordering'] == $topOrder) {
                    //return up arrow
                    //icon
                    $this->objIcon->setIcon('upend');
                    $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
                    //link
                    $upLink = & $this->newObject('link', 'htmlelements');
                    $upLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'down', 'parent' => $entry['parentid']));
                    $upLink->link = $this->objIcon->show();

                    $links .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $upLink->show();
                } else {
                    //return both arrows
                    //icon
                    $this->objIcon->setIcon('down');
                    $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderdown', 'cmsadmin');
                    //link
                    $downLink = & $this->newObject('link', 'htmlelements');
                    $downLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'up', 'parent' => $entry['parentid']));
                    $downLink->link = $this->objIcon->show();
                    //icon
                    $this->objIcon->setIcon('up');
                    $this->objIcon->title = $this->_objLanguage->languageText('mod_cmsadmin_changeorderup', 'cmsadmin');
                    //link
                    $upLink = & $this->newObject('link', 'htmlelements');
                    $upLink->href = $this->uri(array('action' => 'changesectionorder', 'id' => $id, 'ordering' => 'down', 'parent' => $entry['parentid']));
                    $upLink->link = $this->objIcon->show();
                    $links .= $downLink->show() . '&nbsp;' . $upLink->show();
                }
            }

            return $links;
        }

        /**
         * Method to update the order of the frontpage
         * 
         * @param string $id The id of the entry to move
         * @param int $ordering How to update the order(up or down).
         * @access public
         * @return bool
         * @author Warren Windvogel
         */
        public function changeOrder($id, $ordering, $parentid)
        {
            //Get array of all sections in level
            $fpsection = $this->getAll("WHERE parentid = '$parentid' AND trash = 0 ORDER BY ordering ");
            //Search for entry to be reordered and update order
            foreach($fpsection as $section) {
                if ($section['id'] == $id) {
                    if ($ordering == 'up') {
                        $changeTo = $section['ordering'];
                        $toChange = $section['ordering'] + 1;
                        $updateArray = array(
                                           'ordering' => $toChange
                                       );
                        $this->update('id', $id, $updateArray);
                    } else {
                        $changeTo = $section['ordering'];
                        $toChange = $section['ordering'] - 1;
                        $updateArray = array(
                                           'ordering' => $toChange
                                       );
                        $this->update('id', $id, $updateArray);
                    }
                }
            }

            //Get other entry to change
            $entries = $this->getAll("WHERE parentid = '$parentid' AND ordering = '$toChange' AND trash = 0");
            foreach($entries as $entry) {
                if ($entry['id'] != $id) {
                    $upArr = array(
                                 'ordering' => $changeTo
                             );
                    $this->update('id', $entry['id'], $upArr);
                }
            }
            $this->reorderSections($parentid);
        }
        
        /**
        * Method to reorder the sections
        *
        * @author Megan Watson
        * @param string $parentid The parent id of the sections to be re ordered
        * @access private
        * @return void
        */
        private function reorderSections($parentid)
        {   
            // Get all pages
            $sectionData = $this->getAll("WHERE parentid = '$parentid' AND trash = 0 ORDER BY ordering ");
            
            if(!empty($sectionData)){
                    
                $i = 1;
                foreach($sectionData as $key => $item){
                    $this->update('id', $item['id'], array('ordering' => $i));
                    $sectionData[$key]['ordering'] = $i++;
                }
                        
                // Get the ordering position of the last page
                $newData = array_reverse($sectionData);
                $lastOrder = $newData[0]['ordering']+1;
                            
                // Remove all null and negative numbers
                foreach($sectionData as $key => $item){
                    if($item['ordering'] < 0 || is_null($item['ordering'])){
                        $this->update('id', $item['id'], array('ordering' => $lastOrder++));
                    }
                }
            }
        }

        /**
        * Method to get the type of section in a human readable format
        *
        * @access public
        * @param string $orderType Type of Order Code
        * @return string containing the type of order in a human readable format.
        */
        public function getPageOrderType($orderType)
        {
            switch ($orderType) {
            case 'pageorder':
                    $order = $this->_objLanguage->languageText('mod_cmsadmin_order_pageorder', 'cmsadmin');
                break;

            case 'pagedate_asc':
                $order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagedate_asc', 'cmsadmin');
                break;

            case 'pagedate_desc':
                $order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagedate_desc', 'cmsadmin');
                break;

            case 'pagetitle_asc':
                $order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagetitle_asc', 'cmsadmin');
                break;

            case 'pagetitle_desc':
                $order = $this->_objLanguage->languageText('mod_cmsadmin_order_pagetitle_desc', 'cmsadmin');
                break;

            default:
                $order = $this->_objLanguage->languageText('word_unknown');
                break;
            }

            return $order;
        }


        /**
         * Method to add a section to the search database
         * @param array $data
         */
        public function luceneIndex($data)
        {
            $objLucene = $this->getObject('indexdata', 'search');
            $docId = 'cms_section_'.$data['id'];
            $url = $this->uri(array('action' => 'showsection', 'id' => $data['id']), 'cms');

            //Removing Notices
            $fields = array('creation', 'title', 'body', 'description', 'userid');
            foreach ($fields as $field){
                if (!isset($data[$field])){
                    $data[$field] = '';
                }
            }

            $objLucene->luceneIndex($docId, $data['creation'], $url, $data['title'], $data['title'].$data['body'], $data['description'], 'cms', $data['userid']);
        }
        
        public function removeLuceneIndex($id)
        {
            $objLucene = $this->getObject('indexdata', 'search');
            $objLucene->removeIndex('cms_section_'.$id);
        }
        
        
        /**
         * Method to get the section id from the 
         * contextcode
         * @param string $contextCode The Context Code
         * @return string
         * @access public
         * @author Wesley Nitsckie
         * 
         */
        public function getSectionByContextCode()
        {
            $objDBContext = $this->getObject('dbcontext', 'context');
            $contextCode = $objDBContext->getContextCode();
            //return $this->getAll("WHERE contextCode='".$contextCode."' AND rootid=0" );
            $ret =  $this->getRow("contextcode", $contextCode);
            
            if($ret == FALSE)
            {
                //create an entry
                //die('no section');
                $this->addNewSection(0,
                                $objDBContext->getTitle(),
                                $objDBContext->getMenuText(),
                                0,
                                $objDBContext->getAbout(),
                                1,
                                'page',
                                1,
                                1,
                                1,
                                'pageorder',
                                $contextCode);
                 return $this->getSectionByContextCode();
            } else {
                return $ret;
            }
        }
        
        
        /**
         * Method to get the JSON list of child sections for use as input to the jQuery jqGrid control
         *
         * The list returned is a list of child sections with fields pertaining to 
         * Sections Manager display
         *
         * @param string $sectionid The Section Id
         * @return string
         * @access public
         * @author Charl Mert
         * 
         */
        public function getJSONSectionChildren($sectionid = 0)
        {
            //Getting ASC ordered list of sections
            $sections = getSubSectionsInSection($sectionid);
            
            //Output 1 row -> {id:'cjm_8774_1221818255',cell:['cjm_8774_1221818255','Sub Mofo Mofo','1','1','2008-09-19 11:59:22']},
            $json = '{';
            foreach ($sections as $section){

                //Setting up the fields for display
                $folderName = $section['title'];
                $sectionName = $section['menutext'];
                $pages = $this->_objContent->getNumberOfPagesInSection($section['id']);
                $layout = $this->_objLayouts->getLayoutDescription($section['layout']);
                $order = $this->_objSections->getOrderingLink($section['id']);//$this->_objSections->getPageOrderType($section['ordertype']);


                $json .= "id:'$section[id]',cell:['$section[title]','$section[menutext]','$section[menutext]']";
            }
        }

}
?>

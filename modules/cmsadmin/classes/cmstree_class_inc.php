<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
* This object is a wrapper class for building a tree using the cms sections
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR 
* @license GNU GPL
* @author Serge Meunier
*/

    class cmstree extends object
    {

        /**
        * The sections  object
        *
        * @access private
        * @var object
        */
        protected $_objSections;

        /**
        * The Content object
        *
        * @access private
        * @var object
        */
        protected $_objContent;

        /**
        * The User object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

           /**
            * Class Constructor
            *
            * @access public
            * @return void
            */
        public function init()
        {
            try {
                $this->_objSecurity = & $this->newObject('dbsecurity', 'cmsadmin');
                $this->_objSections = & $this->newObject('dbsections', 'cmsadmin');
                $this->_objContent = & $this->newObject('dbcontent', 'cmsadmin');
                $this->_objFrontPage = & $this->newObject('dbcontentfrontpage', 'cmsadmin');
                $this->_objUser = & $this->newObject('user', 'security');
                $buildtree = $this->newObject('buildtree');
                $this->objLanguage =& $this->newObject('language', 'language');
            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

        /**
        * Method to return back the tree code
        *
        * @param string $currentNode The currently selected node, which should remain open
        * @param bool $admin Select whether admin user or not
        * @return string
        * @access public
        */
        public function show($currentNode = null, $admin = FALSE)
        {
            $html = $this->buildTree($currentNode, $admin);
            return $html;
        }

        /**
         * Method to build the tree
         *
         * @param string $currentNode The currently selected node, which should remain open
         * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildTree($currentNode = null, $admin = FALSE)
        {
            //check if there are any root nodes
            if ($this->getChildNodeCount('0') > 0) {
                $html = '<div id="productsandservices" class="yuimenu">
                                 <div class="bd">
                                 <ul class="first-of-type">';
                //start the tree building
                $html .= $this->buildLevel('0', $currentNode, $admin);
                $html .= '</ul></div></div>';
            } else {
                $html = '';
            }
            return $html;
        }

        /**
         * Method to build the next level in tree
         *
         * @param string $parentid The node id whose child nodes need to be built
         * @param string $currentNode The currently selected node, which should remain open
         * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildLevel($parentId, $currentNode, $admin)
        {
            //gets all the child nodes of id
            $nodes = $this->getChildNodes($parentId);
            if (count($nodes)) {

                $htmlLevel = '';
                foreach($nodes as $node) {
                    if ($this->getChildNodeCount($node['id']) > 0) {
                        //if node has further child nodes, recursively call buildLevel
                        $htmlChildren = $this->buildLevel($node['id'], $currentNode, $admin);

                        //get any content for a section
                        if(($this->getNodeContentCount($node['id']) == 0) || ($node['published'] == 0)) {
                            $htmlLevel .= '';
                        } else {

                            $nodeUri = $this->uri(array('action'=>'showsection', 'id'=>$node['id'], 'sectionid'=>$node['id']), 'cms');
                            $htmlLevel .= '<li class="yuimenuitem first-of-type"><a href="'.$nodeUri.'">'.$node['menutext'].'</a><ul>';
                            $htmlLevel .= $htmlChildren;
                            $htmlLevel .= '</ul></li>';
                        }
                    } else {
                        if(($this->getNodeContentCount($node['id']) == 0) || ($node['published'] == 0)) {
                            $htmlLevel .= '';
                        } else {
                            $nodeUri = $this->uri(array('action'=>'showsection', 'id'=>$node['id'], 'sectionid'=>$node['id']), 'cms');
                            $htmlLevel .= '<li class="sectionfolder"><a href="'.$nodeUri.'">'.$node['menutext'].'</a></li>';
                        }
                    }
                }

                return $htmlLevel;
            } else {
                //if no nodes return empty string
                return '';
            }
        }

        /**
         * Method to get add all content for a particular section node
         *
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
        public function addContent($id)
        {
            $contentNodes = $this->getContent($id);
            if (count($contentNodes)) {
                $htmlContent = '';
                foreach($contentNodes as $contentNode) {
                    $contentUri = $this->uri(array('action'=>'showcontent', 'id'=>$contentNode['id'], 'sectionid'=>$contentNode['sectionid']), 'cms');
                    $htmlContent .= '<li><a href="'.$contentUri.'">'.html_entity_decode($contentNode['title']).'</a></li>';
                }
                return $htmlContent;
            } else {
                return '';
            }
        }

        /**
         * Method to get all child nodes for a particular node
         *
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function getChildNodes($parentId)
        {
            return $this->_objSections->getSubSectionsInSection($parentId);
        }

        /**
         * Method to get node for a particular id
         *
         * @param string $id The node id
         * @return array
         * @access public
         */
        public function getNode($id)
        {
            return $this->_objSections->getSection($id);
        }

        /**
         * Method to get all content nodes for a particular section node
         *
         * @param string $sectionId The section id
         * @return array
         * @access public
         */
        public function getContent($sectionId)
        {
            return $this->_objContent->getPagesInSection($sectionId);
        }

        /**
         * Method to get number of child nodes for a particular node
         *
         * @param string $parentId The parent node id
         * @return int
         * @access public
         */
        public function getChildNodeCount($parentId)
        {
            return $this->_objSections->getNumSubSections($parentId);
        }

        /**
         * Method to get the number of content nodes for a section id
         *
         * @param string $id The section id
         * @return int
         * @access public
         */
        public function getNodeContentCount($sectionId)
        {
            return $this->_objContent->getNumberOfPagesInSection($sectionId);
        }
                

        
                /**
                * @author Charl Mert
                * Method to get the jquery simple tree to display on the CMS module
                * @return string
                */
        public function getSimpleCMSTree($current)
        {
            $this->_objSimpleTree = & $this->newObject('simplecontenttree', 'cmsadmin');
            return $this->_objSimpleTree->show($current,FALSE,'cms','showsection','showfulltext');
        }


                /**
                * Method to get the tree to display on the CMS module
                * @return string
                */
        public function getCMSTree($current)
        {
            //$includeFrontPage = $this->_objFrontPage->hasFrontPageContent();
            //$buildtree = $this->newObject('buildtree');
            //$list = new htmllist($menu, array('topMostListClass'=>'treefolder'));
            //$list = $buildtree->buildFoldoutMenu($parentId = '0', $onlyPublished = true);
            $this->_objTree = & $this->newObject('contenttree', 'cmsadmin');
            return $this->_objTree->show($current,FALSE,'cms','showsection','showfulltext');
        }
        

                /**
                * Method to get the tree to display on the CMS Admin module
                * @return string
                */
        public function getCMSAdminTree($current)
        {
            //$menu = $this->getTree('cmsadmin', FALSE);
            $this->_objTree = & $this->newObject('contenttree', 'cmsadmin');
            return $this->_objTree->show($current,TRUE,'cmsadmin','viewsection','viewcontent');
        }
                
       /**
        * Method to get the tree drop down when creating a section
        * @param string $defaultSelected The Item to be default selccted on the drop down
        * @param boolean $includeRoot Flag on whether to include root or not
        * @return string
        */
        public function getCMSAdminDropdownTree($defaultSelected=NULL, $includeRoot=TRUE)
        {
            $objDBContext = $this->getObject('dbcontext', 'context');
            if($objDBContext->isInContext())
            {		        
                $menu = $this->getTreeForInContext('cmsadmin', $includeRoot, FALSE);
            } else {
                $menu = $this->getTree('cmsadmin', $includeRoot, FALSE);  
            }
                        
            //var_dump($menu); exit;
            $this->loadClass('htmldropdown', 'tree');
                        
            $htmldropdown = new htmldropdown($menu, array('inputName'=>'parent', 'selected'=>$defaultSelected));
            return $htmldropdown->getMenu();
        }

      /**
        * Method to get the tree drop down when creating a section
        * @param string $defaultSelected The Item to be default selccted on the drop down
        * @param boolean $includeRoot Flag on whether to include root or not
        * @return string
        */
        public function getCMSAdminSectionDropdownTree($defaultSelected=NULL, $includeRoot=TRUE)
        {
            $objDBContext = $this->getObject('dbcontext', 'context');
            if($objDBContext->isInContext())
            {		        
                $menu = $this->getTreeForInContext('cmsadmin', $includeRoot, FALSE);
            } else {
                $menu = $this->getSectionTree('cmsadmin', $includeRoot, FALSE);  
            }
                        
            //var_dump($menu); exit;
            $this->loadClass('htmldropdown', 'tree');
                        
            $htmldropdown = new htmldropdown($menu, array('inputName'=>'parent', 'selected'=>$defaultSelected));
            return $htmldropdown->getMenu();
        }

                /**
                * Method to generate trees when ADDING CONTENT modified to work with the NEW PERMISSIONS MODEL
                * @param string $defaultSelected The Item to be default selccted on the drop down
                * @param boolean $includeRoot Flag on whether to include root or not
                * @return string
                */
        public function getCMSAdminFlatDropdownTree($defaultSelected=NULL, $includeRoot=TRUE)
        {
            $objDBContext = $this->getObject('dbcontext', 'context');
            if($objDBContext->isInContext())
            {		
                //TODO : Create Flat Tree for context        
                $menu = $this->getTreeForInContext('cmsadmin', $includeRoot, FALSE);
            } else {
                $menu = $this->getFlatTree('cmsadmin', $includeRoot, FALSE);  
            }
                        
                        
            $this->loadClass('htmldropdown', 'tree');

            //var_dump($menu);
                        
            $htmldropdown = new htmldropdown($menu, array('inputName'=>'parent', 'selected'=>$defaultSelected));
            return $htmldropdown->getMenu($menu);
        }



                /**
                * Method to generate trees for the CMS : getTree modified to work with the NEW PERMISSIONS MODEL
                * @param string $module Calling Module for which to set the link to
                * @param boolean $includeRoot Flag to add --Root-- to menu. For CMS, this is the front page
                * @param boolean $useLinks Flag whether to generate a URI or pass the ID only
                */
        public function getFlatTree($module='cmsadmin', $includeRoot=FALSE, $useLinks=TRUE)
        {
                            
            $sql = 'SELECT tbl_cms_sections.* , tbl_cms_content.id AS pagevisible
                                        FROM tbl_cms_sections
                                        LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid 
                                        AND tbl_cms_sections.published = \'1\')
                                        WHERE tbl_cms_sections.trash = \'0\' 
                                        ';
            $useIcon = TRUE;
                        
            //$where = ' GROUP BY tbl_cms_sections.id ORDER BY nodelevel, ordering';
            $where = ' GROUP BY tbl_cms_sections.id ORDER BY tbl_cms_sections.datecreated ASC';
            
            $sections = $this->_objSections->getArray($sql.$where);
        
            //echo $sql.$where; exit;

            $secureSections = array();
            //Filterring the list based on WRITE ACCESS
            foreach ($sections as $section){
                //echo $section['title']."<br/>\n";
                $section_id = $section['id'];
                if ($this->_objSecurity->canUserWriteSection($section_id)){
                    array_push($secureSections, $section);
                }
                                
            }
                        
            $sections = $secureSections;

            return $sections;
            
            
        }
        






                /**
                * Method to generate trees for the CMS
                * @param string $module Calling Module for which to set the link to
                * @param boolean $includeRoot Flag to add --Root-- to menu. For CMS, this is the front page
                * @param boolean $useLinks Flag whether to generate a URI or pass the ID only
                */
        private function getTree($module='cmsadmin', $includeRoot=FALSE, $useLinks=TRUE)
        {
            $this->loadClass('treemenu', 'tree');
            $this->loadClass('treenode', 'tree');
            $this->loadClass('htmllist', 'tree');
                        
            $action = ($module == 'cms') ? 'showsection' : 'viewsection';
                        
            if ($module == 'cmsadmin') {
                            
                $sql = 'SELECT tbl_cms_sections.* , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid 
                                                AND tbl_cms_content.published = \'1\')
                                                WHERE tbl_cms_sections.trash = \'0\' 
                                                ';
                $useIcon = TRUE;
            } else {
                $sql = 'SELECT tbl_cms_sections. * , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid
                                                AND tbl_cms_content.published = \'1\' )
                                                WHERE tbl_cms_sections.published = \'1\' AND tbl_cms_content.published = \'1\' 
                                                AND tbl_cms_sections.trash = \'0\'
                                                ';
            }
                        
            //$where = ' GROUP BY tbl_cms_sections.id ORDER BY nodelevel, ordering';
            $where = ' GROUP BY tbl_cms_sections.id';
            
            $sections = $this->_objSections->getArray($sql.$where);
        
            //var_dump($sections); exit;
            
            $secureSections = array();
            //Filterring the list based on WRITE ACCESS
            foreach ($sections as $section){
                $section_id = $section['id'];
                if ($this->_objSecurity->canUserWriteSection($section_id)){
                    array_push($secureSections, $section);
                }
                                
            }
            
            $sections = $secureSections;
                
            $menu = new treemenu();
            
            $nodesArray = array();
            $rootNodesArray = array();
            $visibleNodes = array();
            
            if ($includeRoot) {
                if ($module == 'cmsadmin') {
                    $rootNode =& new treenode (array('text'=>'[- Root -]', 'link'=>0));
                } else {
                    $rootNode =& new treenode (array('text'=>$this->objLanguage->languageText('word_home'), 'link'=>$this->uri(NULL, 'cms'), 'liClass'=>'sectionfolder'));
                }
                $menu->addItem($rootNode);
            }
            
            if (count($sections) > 0) {
            
                foreach ($sections as $section)
                {
                    
                    if ($useLinks) {
                        $link = $this->uri(array('action'=>$action, 'id'=>$section['id']), $module);
                    } else {
                        $link = $section['id'];
                    }
                                        
                    // Determine the Colour Coding for Sections based on settings
                    if ($section['published'] == '0') { // If section is not visible - code is orange
                        $cssClass = 'orangefolder';
                    } else {
                        $cssClass = 'sectionfolder'; // Default - Yellow folder
                                                
                        // If section has no content - gets white folder
                        $content = $this->_objContent->getNumberOfPagesInSection($section['id']);
                                                
                        if ($content == 0) {
                            $cssClass = 'whitefolder';
                        } else {
                            // Lastly, check if parent will be shown
                            if ($section['parentid'] == '0') { // Root Folder. Meets all criteria, so add
                                $visibleNodes[] = $section['id'];
                            } else {
                                // For others check whether parents are visible - give green folder if not
                                if (!in_array($section['parentid'], $visibleNodes)) {
                                    $cssClass = 'greenfolder';
                                } else {
                                    // if parents are visible. add to list of visible items
                                    $visibleNodes[] = $section['id'];
                                }
                            }
                        }
                    }
                                        
                    //var_dump($section);                    
                    $node =& new treenode(array('text'=>$section['menutext'], 'link'=>$link, 'liClass'=>$cssClass));
                    
                    //Parent ID is null for some reason??
                    if ($section['parentid'] == NULL) $section['parentid'] = '0';
                    
                    if ($section['parentid'] == '0') {
                        $nodesArray[$section['id']] =& $node;
                                                
                        if ($includeRoot && $module == 'cmsadmin') {
                            $rootNode->addItem($node);
                        } else {
                            $menu->addItem($node);
                        }
                    } else {
                        if (array_key_exists($section['parentid'], $nodesArray)) {
                            $nodesArray[$section['id']] =& $node;
                            $nodesArray[$section['parentid']]->addItem($node);
                        }
                    }
                }
            }
            //var_dump($rootNode);
            //var_dump($menu); exit;
            return $menu;
            
            
        }

        
  /**
    * Method to generate trees for the CMS //Made for section dropdown due to security filters
    * @param string $module Calling Module for which to set the link to
    * @param boolean $includeRoot Flag to add --Root-- to menu. For CMS, this is the front page
    * @param boolean $useLinks Flag whether to generate a URI or pass the ID only
    */
        private function getSectionTree($module='cmsadmin', $includeRoot=FALSE, $useLinks=TRUE)
        {
            $this->loadClass('treemenu', 'tree');
            $this->loadClass('treenode', 'tree');
            $this->loadClass('htmllist', 'tree');
                        
            $action = ($module == 'cms') ? 'showsection' : 'viewsection';
                        
            if ($module == 'cmsadmin') {
                            
                $sql = 'SELECT tbl_cms_sections.* , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid 
                                                AND tbl_cms_content.published = \'1\')
                                                WHERE tbl_cms_sections.trash = \'0\' 
                                                ';
                $useIcon = TRUE;
            } else {
                $sql = 'SELECT tbl_cms_sections. * , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid
                                                AND tbl_cms_content.published = \'1\' )
                                                WHERE tbl_cms_sections.published = \'1\' AND tbl_cms_content.published = \'1\' 
                                                AND tbl_cms_sections.trash = \'0\'
                                                ';
            }
                        
            //$where = ' GROUP BY tbl_cms_sections.id ORDER BY nodelevel, ordering';
            $where = ' GROUP BY tbl_cms_sections.id';
            
            $sections = $this->_objSections->getArray($sql.$where);
        
            //var_dump($sections); exit;
            
            $secureSections = array();
            //Filterring the list based on WRITE ACCESS
            foreach ($sections as $section){
                $section_id = $section['id'];
                if ($this->_objSecurity->canUserWriteSection($section_id)){
                    array_push($secureSections, $section);
                }
                                
            }
            
            $sections = $secureSections;
                
            $menu = new treemenu();
            
            $nodesArray = array();
            $rootNodesArray = array();
            $visibleNodes = array();
            
            if ($includeRoot) {
                if ($module == 'cmsadmin') {
                    $rootNode =& new treenode (array('text'=>'[- Root -]', 'link'=>0));
                } else {
                    $rootNode =& new treenode (array('text'=>$this->objLanguage->languageText('word_home'), 'link'=>$this->uri(NULL, 'cms'), 'liClass'=>'sectionfolder'));
                }
                $menu->addItem($rootNode);
            }
            
            if (count($sections) > 0) {
            
                foreach ($sections as $section)
                {
                    
                    if ($useLinks) {
                        $link = $this->uri(array('action'=>$action, 'id'=>$section['id']), $module);
                    } else {
                        $link = $section['id'];
                    }
                                        
                    // Determine the Colour Coding for Sections based on settings
                    if ($section['published'] == '0') { // If section is not visible - code is orange
                        $cssClass = 'orangefolder';
                    } else {
                        $cssClass = 'sectionfolder'; // Default - Yellow folder
                                                
                        // If section has no content - gets white folder
                        $content = $this->_objContent->getNumberOfPagesInSection($section['id']);
                                                
                        if ($content == 0) {
                            $cssClass = 'whitefolder';
                        } else {
                            // Lastly, check if parent will be shown
                            if ($section['parentid'] == '0') { // Root Folder. Meets all criteria, so add
                                $visibleNodes[] = $section['id'];
                            } else {
                                // For others check whether parents are visible - give green folder if not
                                if (!in_array($section['parentid'], $visibleNodes)) {
                                    $cssClass = 'greenfolder';
                                } else {
                                    // if parents are visible. add to list of visible items
                                    $visibleNodes[] = $section['id'];
                                }
                            }
                        }
                    }
                                        
                    //var_dump($section);                    
                    $node =& new treenode(array('text'=>$section['menutext'], 'link'=>$link, 'liClass'=>$cssClass));
                    
                    //Parent ID is null for some reason??
                    if ($section['parentid'] == NULL) $section['parentid'] = '0';
                    
                    if ($section['parentid'] == '0') {
                        $nodesArray[$section['id']] =& $node;
                                                
                        if ($includeRoot && $module == 'cmsadmin') {
                            //$rootNode->addItem($node);
                            $menu->addItem($node);
                        } else {
                            $menu->addItem($node);
                        }
                    } else {
                        if (array_key_exists($section['parentid'], $nodesArray)) {
                            $nodesArray[$section['id']] =& $node;
                            $nodesArray[$section['parentid']]->addItem($node);
                        }
                    }
                }
            }
            //var_dump($rootNode);
            //var_dump($menu); exit;
            return $menu;
            
            
        }        
        
        
        /**
        * Method to get tree count for use with Security module when adding content
        * @param string $module Calling Module for which to set the link to
        * @param boolean $includeRoot Flag to add --Root-- to menu. For CMS, this is the front page
        * @param boolean $useLinks Flag whether to generate a URI or pass the ID only
        */
        public function getTreeCount($module='cmsadmin', $includeRoot=FALSE, $useLinks=TRUE)
        {
                        
            $action = ($module == 'cms') ? 'showsection' : 'viewsection';
                        
            if ($module == 'cmsadmin') {
                            
                $sql = 'SELECT tbl_cms_sections.* , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid 
                                                AND tbl_cms_content.published = \'1\')
                                                WHERE tbl_cms_sections.trash = \'0\' 
                                                ';
                $useIcon = TRUE;
            } else {
                $sql = 'SELECT tbl_cms_sections. * , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid
                                                AND tbl_cms_content.published = \'1\' )
                                                WHERE tbl_cms_sections.published = \'1\' AND tbl_cms_content.published = \'1\' 
                                                AND tbl_cms_sections.trash = \'0\'
                                                ';
            }
                        

            $where = ' GROUP BY tbl_cms_sections.id ORDER BY nodelevel, ordering';
            $sections = $this->_objSections->getArray($sql.$where);
        
            $secureSections = array();
            //Filterring the list based on READ ACCESS
            foreach ($sections as $section){
                $section_id = $section['id'];
                if ($this->_objSecurity->canUserWriteSection($section_id)){
                    array_push($secureSections, $section);
                }
            }
                        
            $sections = $secureSections;
                        
            return count($sections);
        }		
                

                /**
                * Method to get tree count for use with Security module when adding content
                * @param string $module Calling Module for which to set the link to
                * @param boolean $includeRoot Flag to add --Root-- to menu. For CMS, this is the front page
                * @param boolean $useLinks Flag whether to generate a URI or pass the ID only
                */
        public function getTreeRootCount($module='cmsadmin', $includeRoot=FALSE, $useLinks=TRUE)
        {
                        
            $action = ($module == 'cms') ? 'showsection' : 'viewsection';
                        
            if ($module == 'cmsadmin') {
                            
                $sql = 'SELECT tbl_cms_sections.* , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid 
                                                AND tbl_cms_content.published = \'1\')
                                                WHERE tbl_cms_sections.trash = \'0\' 
                                                AND tbl_cms_sections.parentid = \'0\'
                                                ';
                $useIcon = TRUE;
            } else {
                $sql = 'SELECT tbl_cms_sections. * , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid
                                                AND tbl_cms_content.published = \'1\' )
                                                WHERE tbl_cms_sections.published = \'1\' AND tbl_cms_content.published = \'1\' 
                                                AND tbl_cms_sections.trash = \'0\'
                                                AND tbl_cms_sections.parentid = \'0\'
                                                ';
            }
                        
            $where = 'ORDER BY nodelevel, ordering';
            
            $sections = $this->_objSections->getArray($sql);
        
            $secureSections = array();
            //Filterring the list based on READ ACCESS
            foreach ($sections as $section){
                $section_id = $section['id'];
                if ($this->_objSecurity->canUserWriteSection($section_id)){
                    array_push($secureSections, $section);
                }
            }
                        
            $sections = $secureSections;
                        
            return count($sections);
        }		
        
        /**
                * Method to generate trees for the CMS
                * @param string $module Calling Module for which to set the link to
                * @param boolean $includeRoot Flag to add --Root-- to menu. For CMS, this is the front page
                * @param boolean $useLinks Flag whether to generate a URI or pass the ID only
                */
        private function getTreeForInContext($module='cmsadmin', $includeRoot=FALSE, $useLinks=TRUE)
        {
            $this->loadClass('treemenu', 'tree');
            $this->loadClass('treenode', 'tree');
            $this->loadClass('htmllist', 'tree');
                        
            $action = ($module == 'cms') ? 'showsection' : 'viewsection';
                        
            if ($module == 'cmsadmin') {
                $objDBContext = $this->getObject('dbcontext', 'context');
                $sql = 'SELECT tbl_cms_sections.* , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid 
                                                AND tbl_cms_content.published = \'1\')
                                                WHERE tbl_cms_sections.trash = \'0\'AND tbl_cms_sections.contextcode=\''.$objDBContext->getContextCode().'\'
                                                ';
                $useIcon = TRUE;
            } else {
                $sql = 'SELECT tbl_cms_sections. * , tbl_cms_content.id AS pagevisible
                                                FROM tbl_cms_sections
                                                LEFT JOIN tbl_cms_content ON ( tbl_cms_sections.id = tbl_cms_content.sectionid
                                                AND tbl_cms_content.published = \'1\' )
                                                WHERE tbl_cms_sections.published = \'1\' AND tbl_cms_content.published = \'1\' 
                                                AND tbl_cms_sections.trash = \'0\' 
                                                ';
            }
                        
            $where = 'ORDER BY nodelevel, ordering';
            
            $sections = $this->_objSections->getArray($sql);
            //var_dump($sectionitems);
            $menu = new treemenu();
            
            $nodesArray = array();
            $rootNodesArray = array();
            $visibleNodes = array();
            $includeRoot = false;
            if ($includeRoot) {
                if ($module == 'cmsadmin') {
                    $rootNode =& new treenode (array('text'=>'[- Root WES-]', 'link'=>0));
                } else {
                    $rootNode =& new treenode (array('text'=>$this->objLanguage->languageText('word_home'), 'link'=>$this->uri(NULL, 'cms'), 'liClass'=>'sectionfolder'));
                }
                $menu->addItem($rootNode);
            }
            
            if (count($sections) > 0) {
            
                foreach ($sections as $section)
                {
                    if ($useLinks) {
                        $link = $this->uri(array('action'=>$action, 'id'=>$section['id']), $module);
                    } else {
                        $link = $section['id'];
                    }
                                        
                    // Determine the Colour Coding for Sections based on settings
                    if ($section['published'] == '0') { // If section is not visible - code is orange
                        $cssClass = 'orangefolder';
                    } else {
                        $cssClass = 'sectionfolder'; // Default - Yellow folder
                                                
                        // If section has no content - gets white folder
                        $content = $this->_objContent->getNumberOfPagesInSection($section['id']);
                                                
                        if ($content == 0) {
                            $cssClass = 'whitefolder';
                        } else {
                            // Lastly, check if parent will be shown
                            if ($section['parentid'] == '0') { // Root Folder. Meets all criteria, so add
                                $visibleNodes[] = $section['id'];
                            } else {
                                // For others check whether parents are visible - give green folder if not
                                if (!in_array($section['parentid'], $visibleNodes)) {
                                    $cssClass = 'greenfolder';
                                } else {
                                    // if parents are visible. add to list of visible items
                                    $visibleNodes[] = $section['id'];
                                }
                            }
                        }
                    }
                                        
                                        
                    $node =& new treenode(array('text'=>$section['menutext'], 'link'=>$link, 'liClass'=>$cssClass));
                    
                    if ($section['parentid'] == '0') {
                        $nodesArray[$section['id']] =& $node;
                                                
                        if ($includeRoot && $module == 'cmsadmin') {
                            //$rootNode->addItem($node);
                            $menu->addItem($node);
                        } else {
                            $menu->addItem($node);
                        }
                    } else {
                        $menu->addItem($node);
                        /*
                        if (array_key_exists($section['parentid'], $nodesArray)) {
                            $nodesArray[$section['id']] =& $node;
                            $nodesArray[$section['parentid']]->addItem($node);
                        }
                        */
                    }
                }
            }
            
            return $menu;
            
            
        }
    }
?>

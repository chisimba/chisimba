<?php

/* -------------------- cmstree class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check
/**
* This object is a wrapper class for building a tree using the cms sections
* @package cms
* @category cmsadmin
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Serge Meunier, Prince Mbekwa, Charl Mert
* @example :
*/

class simpletreemenu extends object
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
        * The Folder Array. 
        * Contains the folders that where encountered while constructing the sections menu
        *
        * @access private
        * @var folders
        */
        protected $_folders;


        /**
         * Constructor
         */
        public function init()
        {
            try {
                $this->_objSec = & $this->newObject('dbsections', 'cmsadmin');
                $this->_objSections = & $this->newObject('dbsectiongroup', 'cmsadmin');
                $this->_objContent = & $this->newObject('dbcontent', 'cmsadmin');
                $this->_objUser = & $this->newObject('user', 'security');
                $this->objLanguage = & $this->newObject('language', 'language');
                $this->_folders = array();
            } catch (Exception $e) {
                throw customException($e->getMessage());
                exit();
            }

        }


		/**
        * Method to get the simple jquery tree to display on the CMS Admin module
        * @return string
        */
        public function getCMSAdminTree($current, $ajaxMode = TRUE)
        {
            $menu_html =  $this->show($current,TRUE,'cmsadmin','viewsection','addcontent', $ajaxMode);
            return $menu_html;
        }

		/**
        * Method to get the simple jquery tree to display on the CMS module
        * @return string
        */
        public function getCMSTree($current)
        {

            $menu_html =  $this->show($current,TRUE,'cms','showsection','showfulltext');

            return $menu_html;
        }

        /**
        * Method to return back the tree code
        * @param string $currentNode The currently selected node, which should remain open
        * @param bool $admin Select whether admin user or not
        * @return string
        * @access public
        */
        public function show($currentNode, $admin, $module = 'cms', $sectionAction = 'viewsection', $contentAction = 'addcontent', $ajaxMode = TRUE)
        {
            $html = $this->showTree($currentNode, $admin, $module, $sectionAction, $contentAction, $ajaxMode);
            return $html;
        }



       /**
        * Method to show the menu tree
        * @param string $currentNode The currently selected node, which should remain open
        * @param bool $admin Select published or not published
        * @return string
        * @author Charl Mert
        * @access public
        */
        public function showTree($currentNode, $admin, $module = 'cms', $sectionAction = 'viewsection', $contentAction = 'addcontent', $ajaxMode = TRUE)
        {
            //check if there are any root nodes
            
            if ($this->getChildNodeCount(0) > 0) {
                $html = "<div id=\"sectionstreemenu\" class=\"sectionstreemenu\">\n
                         <ul class=\"simpleTree\">\n";
                $html .= "<li class='root'><span><a id='dir_root' href='?module=cms'> Home </a></span>
                           <ul>";

                $html .= $this->buildTree(0, $admin, $module, $sectionAction, $contentAction);
            
                $html .= '</ul></li></ul></div><!-- end: simpletree tree div -->';
            } else {
                $html = '';
            }

            return $html;
        }


        /**
         * Method to build the tree
         * @param string $currentNode The currently selected node, which should remain open
         * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildTree($currentNodeId, $admin, $module = 'cms', $sectionAction = 'viewsection', $contentAction = 'addcontent', $isChild = false, $ajaxMode = TRUE)
        {
            //gets all the child nodes of id
            $nodes = $this->getChildNodes($currentNodeId, $admin);

            $html = '';
            if (!empty($nodes)) {
                foreach($nodes as $node) {
                        //var_dump($node['title']);

                        //Process the Current Section
                        if (!empty($sectionAction)) {
                            $nodeUri = $this->uri(array('action' => $sectionAction, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                            if (strlen($node['title'])>24){
                                $text = wordwrap(trim($node['title']),24,"<br />\n");
                            }else{
                                $text = trim($node['title']);
                            }
                            
                            $link = '<a href="'.$nodeUri.'">'.$text.'</a>'."\n";
                            array_push($this->_folders, $node);
                        } else {
                            $link = $node['title'];
                        }
    
                        // small fix here for problem with wraparounds
                        if (strlen($node['title'])>24){
                            $link.="<br />\n";
                        }

                        //var_dump($currentNodeId);
                        $sec = $this->_objSec->getSection($currentNodeId);
                        //var_dump('CURR : '.$sec['title']);
                        //var_dump('NODE : '.$node['title']);

                        $html .= '<li id="'.$node['id'].md5(date('YMD hms')).'" ><span>'.$link."</span>\n";
                        //If the Section has child sections recurse
                        
                        $contentItem = '';
                        $hasChildContent = $this->hasChildContent($node['id']);
                        if ($hasChildContent) {

                            //Adding the content for the parent node
                            $contentNodes = $this->getChildContent($node['id']);
                            if (!empty($contentNodes)) {
                                foreach($contentNodes as $cNode) {
                                    //Process the Current Content Item
                                    if (!empty($contentAction)) {
                                        $cNodeUri = $this->uri(array('action' => $contentAction, 'id' => $cNode['id'], 'sectionid' => $cNode['id']), $module);
                                        if (strlen($cNode['title'])>24){
                                            $text = wordwrap(trim($cNode['title']),24,"<br />\n");
                                        }else{
                                            $text = trim($cNode['title']);
                                        }
                                        
                                        $link = '<a href="'.$cNodeUri.'">'.$text.'</a>'."\n";
                                    } else {
                                        $link = $cNode['title'];
                                    }
                
                                    // small fix here for problem with wraparounds
                                    if (strlen($cNode['title'])>24){
                                        $link.="<br />\n";
                                    }                                
        
                                    $contentItem .= '<li id="'.$cNode['id'].'" ><span>'.$link.'</span></li>'."\n";
                                    
                                    //echo "CONTENT NODE HERE : "; 
                                    //var_dump($cNode); 
                                }
                            }
                        } else {
                            //Catering for empty sections
                            $html .= "<ul><li id='".$node['id']."' ><span>--Empty--</span></ul>\n";
                        }

                        $hasChildNodes = $this->hasChildNodes($node['id']);
                        if ($hasChildNodes){
                        //Call Recursively to add children sections and content
                            $level = $this->buildTree($node['id'], $admin, $module, $sectionAction, $contentAction, true) . $contentItem;
                            if (trim($level) != ''){
								if ($ajaxMode) {
									$ajaxLink = $this->uri(array('action' => 'getmenuchildnodes', 'id' => $node['id']), 'cms');
                                	$html .= '<ul class="ajax"><li id="'.$node['id'].'" >{url:'.$ajaxLink.'}</li></ul>'."\n";
								} else {
                                	$html .= '<ul>' .$level . '</ul>'."\n";
								}
                            }
                        }

                        $html .= '</li>'."\n";

                        //echo "SECTION NODE HERE : "; 
                        //var_dump($node);

                }
            }
            return $html;
        }

        /**
         * Method to get check if a node has any children (Sections or Content)
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function hasChildNodes($parentId)
        {
            return $this->_objSec->hasNodes($parentId);
        }

        /**
         * Method to get check if a node has any child CONTENT
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function hasChildContent($parentId)
        {
            return $this->_objSec->hasChildContent($parentId);
        }


        /**
         * Method to get check if a node has any child CONTENT
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function hasChildSections($parentId)
        {
            return $this->_objSec->hasChildSections($parentId);
        }


        /**
         * Method to get all child sections for a particular node id
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function getChildNodes($parentId, $noPermissions = FALSE)
        {
            return $this->_objSections->getChildNodes($parentId, $noPermissions);
        }

        /**
         * Method to get all child content items for the given section id
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function getChildContent($parentId)
        {   
            //Get all content items that haven't been sent to trash
            return $this->_objContent->getPagesInSection($parentId);
        }

        /**
         * Method to get node for a particular id
         * @param string $id The node id
         * @return array
         * @access public
         */
        public function getNode($id, $noPermissions = FALSE)
        {
            return $this->_objSections->getNode($id, $noPermissions);
        }

        /**
         * Method to get all content nodes for a particular section node
         * @param string $sectionId The section id
         * @return array
         * @access public
         */
        public function getContent($sectionId, $admin)
        {
            $published = $admin;
            return $this->_objContent->getPagesInSection($sectionId, $published);
        }

        /**
         * Method to get number of child nodes for a particular node
         * @param string $parentId The parent node id
         * @return int
         * @access public
         */
        public function getChildNodeCount($parentId, $noPermissions = FALSE)
        {
        	
            return $this->_objSections->getChildNodeCount($parentId, $noPermissions);
        }

        /**
         * Method to get the number of content nodes for a section id
         * @param string $id The section id
         * @return int
         * @access public
         */
        public function getNodeContentCount($sectionId)
        {
            return $this->_objContent->getNumberOfPagesInSection($sectionId);
        }


       /**
        *  This method returns the immediate children of the given section to ease the tree
        *  menu via Ajax
        *
        * @author Charl Mert
        * @param sectionid the id of the parent section to get child items for
        * @return list of formatted child items
        */
        public function getMenuChildNodes($sectionid, $contentAction = 'addcontent'){
            $item .= $this->addNextAjaxContent($sectionid, 'cmsadmin', $contentAction, $admin);
            return $item;
        }


        /**
         * Method to get add ONLY the IMEDIATE/next level content/sections for a particular section node
		 * FOR USE WITH AJAX in JQuery Simple Tree
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
     	public function addNextAjaxContent($id, $module, $action, $admin = FALSE)
        {    
            $contentNodes = $this->getContent($id, $admin);

            $htmlContent = '';
            if (!empty($contentNodes)) {
		        //$htmlContent =		'<ul>'."\n";	
                foreach($contentNodes as $contentNode) {

					$contentTitle = $contentNode[title];
					if ($id != '0'){
						if (strlen($contentTitle) > 20){
							if (!strcmp(' ',$contentTitle)){
								$contentTitle = wordwrap($contentNode[title], 20, '<br/>'."\n");
							} else {
								$contentTitle = substr($contentNode[title], 0, 16).'...';
							}
						}
					}

					if ($this->hasChildNodes($contentNode[id], $admin)){
						$action = 'viewsection';
					} else {
						$action = 'addcontent';
					}
                    if (!empty($action)) {
                        $url = $this->uri(array('action' => $action, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
                        $link = '<a href="'.$url.'">'.trim($contentTitle).'</a>'."\n";
                    } else {
                        $link = $contentTitle;
                    } 
             
                    $htmlContent .='<li id="'.$contentNode['id'].'" ><span>'.$link.'</span></li>'."\n";
                   
                }

                if ($this->getChildNodes($id, $admin)) {
                	$htmlContent .= $this->addNextAjaxChildren($id, $module, $action, $admin);
		        }

                return $htmlContent;
            }
            return '';
        }
        
 

        /**
         * Method to get add ONLY IMMEDIATE content for a particular child node
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
        public function addNextAjaxChildren($id, $module, $action, $admin = FALSE)
        {	
        	 //gets all the child nodes of id
            $nodes = $this->getChildNodes($id, $admin);

            $htmlContent = '';
        	foreach($nodes as $node) {
					//var_dump($node);
					$contentTitle = $node[title];
				
                    if ($id != '0'){
                        if (strlen($contentTitle) > 20){
							if (!strcmp(' ',$contentTitle)){
								$contentTitle = wordwrap($contentTitle, 20, '<br/>'."\n");
							} else {
								$contentTitle = substr($contentTitle, 0, 18).'...';
							}
                        }
                    }

                    if ($this->hasChildNodes($contentNode[id], $admin)){
                        $action = 'viewsection';
                    } else {
                        $action = 'viewsection';
                    }
	
                    if (!empty($action)) {
                        $nodeUri = $this->uri(array('action' => $action, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                        $link = '<a href="'.$nodeUri.'">'.$contentTitle.'</a>'."\n";
                    } else {
                        $link = $contentTitle;
                    }

                    
					if ($this->hasChildNodes($id, $admin)){
						$ajaxLink = $this->uri(array('action' => 'getmenuchildnodes', 'id' => $node['id']), 'cms');
						$htmlContent .= "<li  id='".$node['id']."'><span>$link</span><ul class='ajax'><li id='".$node['id']."'> {$ajaxLink} </li></ul></li>";
					} else {
                        //Catering for empty sections
                        $htmlContent .= "<ul><li id='".$node['id']."' ><span>--Empty--</span></ul>\n";
                   		//$htmlContent .= "<li><span>".$link."</span>\n";
					}
                }
                       
           
            return $htmlContent;
        }


        /**
         * Method to cache a piece of the menu for quick ajax retrieval
      	 * The cache is written to usrfiles/cmsmenucache/[sectionid]_siblings.html
     	 *
         * @access public
         * @param  sectionId The id of the section whos children will be cached
         * @return bool
         */
        public function cacheMenu($sectionId)
        {
            $objConfig =  $this->newObject('altconfig', 'config');
			$menuCachePath = 'cmstreemenucache/';
			$basePath = $objConfig->getcontentBasePath();

			if ($basePath[strlen($basePath)] != '/' &&
				$basePath[strlen($basePath)] != '\\' ){
				$basePath .= '/';
			}

			$basePath = $objConfig->getcontentBasePath().$menuCachePath;

            //Ensuring the menu cache exists
            if(!file_exists($basePath))
            {
                mkdir($basePath, 0777, true);
            }

			$fileName = 'section_id'.'_siblings.html';

            //Writting the file to disk
            $fp = fopen($basePath.$fileName, 'w');
			if (!$fp) {
				log_debug('CMS Tree Menu Cache: Could\'nt Save cache file: ['.$basePath.']');
				return FALSE;
			}

            fwrite($fp, $basePath.$fileName);
            fclose($fp);

            return TRUE;
        }

}

?>

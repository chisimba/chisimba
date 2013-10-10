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

class simplecontenttree extends object
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

            } catch (Exception $e) {
                throw customException($e->getMessage());
                exit();
            }

        }






        /**
        * Method to get the simple jquery tree to display on the CMS Admin module
        * @return string
        */
        public function getSimpleCMSAdminTree($current)
        {
            return $this->show($current,TRUE,'cmsadmin','viewsection','addcontent');
        }

        /**
        * Method to get the simple jquery tree to display on the CMS Admin module
        * @return string
        */
        public function getCMSAdminTree($current)
        {
            return $this->show($current,TRUE,'cmsadmin','viewsection','viewcontent');
        }


		/**
		*  This method returns the immediate children of the given section to ease the tree
		*  menu via Ajax
		*
		* @author Charl Mert
		* @param sectionid the id of the parent section to get child items for
		* @return list of formatted child items
		*/
		public function getMenuChildNodes($sectionid){
			//$htmlLevel .= "<li>".$link."\n";
			$contentAction = 'showfulltext';
        	$item .= $this->addNextAjaxContent($sectionid, 'cms', $contentAction, $admin);
			//$htmlLevel .= $this->buildLevelPart($sectionid, $sectionid, FALSE, 'cms', 'viewsection', 'showfulltext');
        	//$htmlLevel .= $item.'</li>' ."\n";

			$htmlLevel = $item;

			return $htmlLevel;
		}

        /**
        * Method to return back the tree code
        * @param string $currentNode The currently selected node, which should remain open
           * @param bool $admin Select whether admin user or not
        * @return string
        * @access public
        */
        public function show($currentNode, $admin, $module = 'cms', $sectionAction = 'viewsection', $contentAction = 'addcontent')
        {
            //$html = $this->buildTree($currentNode, $admin, $module, $sectionAction, $contentAction);
            $html = $this->buildTreePart($currentNode, $admin, $module, $sectionAction, $contentAction);
            return $html;
        }




        /**
         * Method to build the PARTIAL TREE (FIRST 2 LEVELS)
         * @param string $currentNode The currently selected node, which should remain open
            * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildTreePart($currentNode, $admin, $module = 'cms', $sectionAction = 'viewsection', $contentAction = 'addcontent')
        {
            //check if there are any root nodes
            if ($this->getChildNodeCount(0) > 0) {
            	$html = "<div id=\"tree1\" class=\"tree\">\n
                		 <ul class='simpleTree'>\n";
                // build the home link
                $nodeUri = $this->uri(array(),'cms');
                $text = $this->objLanguage->languageText('word_home');
                $link = '<a href="'.$nodeUri.'">'.$text.'</a>'."\n";
                
            	//start the tree building
                $html .= "<li class='root'><span>$link</span><ul>\n";
                $html .= $this->buildLevelPart(0, $currentNode, $admin, $module, $sectionAction, $contentAction);
                $html .= '</ul> </li>'."\n".'</ul></div><!-- end: simple tree div -->';
            } else {
                $html = '';
            }
			return $html;
        }



        /**
         * Method to build the next level in tree iterating for ONLY 2 Levels
            * @param string $parentid The node id whose child nodes need to be built
         * @param string $currentNode The currently selected node, which should remain open
            * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildLevelPart($parentId, $currentNode, $admin, $module, $sectionAction, $contentAction)
        {
            //gets all the child nodes of id
            $nodes = $this->getChildNodes($parentId, $admin);
        	/*
			var_dump($admin);
			var_dump($module);
			var_dump($sectionAction);
			var_dump($contentAction);
	*/
            //get the list of nodes that need to stay open for the currently selected node

			/*
           echo "PARENT IDs : [".$parentId."]<br/>"; 
			if ($parentId == 'gen9Srv16Nme30_2000_1207657570' && $parentId != 0){ 
				echo "PARENT ID : ".$parentId.'<br/>';
				//var_dump($nodes);
				echo "Node Title : ". $nodes['title'];
			}
			*/

            if (!empty($nodes)) {

                $htmlLevel = '';
                foreach($nodes as $node) {


                    $item = '';
                    if (!empty($sectionAction)) {
                        $nodeUri = $this->uri(array('action' => $sectionAction, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                        if (strlen($node['title'])>24){
                        	$text = wordwrap(trim($node['title']),24,"<br />\n");
                        }else{
                        	$text = trim($node['title']);
                        }
                        
                        $link = '<a  href="'.$nodeUri.'">'.$text.'</a>'."\n";
                    } else {
                        $link = $node['title'];
                    }
                    // small fix here for problem with wraparounds
				    if (strlen($node['title'])>24){
				    	$link.="<br />\n";
					}
                    // if node has further child nodes, recursively call buildLevel
					//echo 'Admin : '.$admin . '<br/>';
					//echo "Child Nodes : ".var_dump($this->getChildNodes($node['id'], $admin))."<br/>";
					//echo "NODE ID : $node[id] <br/>";





                    //if ($this->getChildNodes($node['id'], $admin)) {
                    	$htmlLevel .= "<li><span>".$link."</span>\n";
						
						//if ($node['parentid'] != '0'){
							//$item = $this->addNextAjaxContent($node['id'], $module, $contentAction, $admin);
						//} else {
							$item = $this->addNextContent($node['id'], $module, $contentAction, $admin);
						//}

                    	$htmlLevel .= $item.'</li>' ."\n";
                        
                        //$this->buildLevel($node['id'], $currentNode, $admin, $module, $sectionAction, $contentAction);

					/*
                    }else{
                    	  $htmlLevel .= "<li>".$link."\n";
                    	  $item .= $this->addNextContent($node['id'], $module, $contentAction, $admin);
                    	  $htmlLevel .= $item.'</li>' ."\n";
                    }
			  		*/

					//if ($node['title'] == 'Events') {
					//	var_dump($node);
					//}
                                     
                }

                return $htmlLevel;
            }
            // if no nodes return empty string
            return '';
        }








        /**
         * Method to build the tree
         * @param string $currentNode The currently selected node, which should remain open
            * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildTree($currentNode, $admin, $module = 'cms', $sectionAction = 'viewsection', $contentAction = 'addcontent')
        {
            //check if there are any root nodes
				
            if ($this->getChildNodeCount(0) > 0) {
            	$html = "<div id=\"tree1\" class=\"tree\">\n
                		 <ul>\n";
                // build the home link
                $nodeUri = $this->uri(array(),'cms');
                $text = $this->objLanguage->languageText('word_home');
                $link = '<a href="'.$nodeUri.'">'.$text.'</a>'."\n";
                
            	//start the tree building
                $html .= "<li class='root'>$link <ul>\n";
                $html .= $this->buildLevel(0, $currentNode, $admin, $module, $sectionAction, $contentAction);
                $html .= '</ul> </li>'."\n".'</ul></div><!-- end: simple tree div -->';
            } else {
                $html = '';
            }
			return $html;
        }

        /**
         * Method to build the next level in tree
            * @param string $parentid The node id whose child nodes need to be built
         * @param string $currentNode The currently selected node, which should remain open
            * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildLevel($parentId, $currentNode, $admin, $module, $sectionAction, $contentAction)
        {
            //gets all the child nodes of id
            $nodes = $this->getChildNodes($parentId, $admin);
        
            //get the list of nodes that need to stay open for the currently selected node

			/*
           echo "PARENT IDs : [".$parentId."]<br/>"; 
			if ($parentId == 'gen9Srv16Nme30_2000_1207657570' && $parentId != 0){ 
				echo "PARENT ID : ".$parentId.'<br/>';
				//var_dump($nodes);
				echo "Node Title : ". $nodes['title'];
			}
			*/

            if (!empty($nodes)) {

                $htmlLevel = '';
                foreach($nodes as $node) {
                    $item = '';
                    if (!empty($sectionAction)) {
                        $nodeUri = $this->uri(array('action' => $sectionAction, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                        if (strlen($node['title'])>24){
                        	$text = wordwrap(trim($node['title']),24,"<br />\n");
                        }else{
                        	$text = trim($node['title']);
                        }
                        
                        $link = '<a  class="yuimenuitemlabel" href="'.$nodeUri.'">'.$text.'</a>'."\n";
                    } else {
                        $link = $node['title'];
                    }
                    // small fix here for problem with wraparounds
				    if (strlen($node['title'])>24){
				    	$link.="<br />\n";
					}
                     // if node has further child nodes, recursively call buildLevel
					//echo 'Admin : '.$admin . '<br/>';
					//echo "Child Nodes : ".var_dump($this->getChildNodes($node['id'], $admin))."<br/>";
					//echo "NODE ID : $node[id] <br/>";
                    if ($this->getChildNodes($node['id'], $admin)) {
                    	$htmlLevel .= "<li>".$link."\n";
                    	$item = $this->addContent($node['id'], $module, $contentAction, $admin);
                    	$htmlLevel .= $item.'</li>' ."\n";
                        
                        $this->buildLevel($node['id'], $currentNode, $admin, $module, $sectionAction, $contentAction);
                    }else{
                    	  $htmlLevel .= "<li>".$link."\n";
                    	  $item .= $this->addContent($node['id'], $module, $contentAction, $admin);
                    	  $htmlLevel .= $item.'</li>' ."\n";
                    }
                                       
                }

                return $htmlLevel;
            }
            // if no nodes return empty string
            return '';
        }

        /**
         * Method to get a list of all nodes to remain open
         * @param string $currentNode The currently selected node
         * @return array
         * @access public
         */
        public function getOpenNodes($currentNode)
        {
            $nodeId = $currentNode;

            $openNodes = array();

            $openNodes[0] = $currentNode;
            $i = 1;

            while ($nodeId != '0') {
                $node = $this->getNode($nodeId);

                if (count($node)) {
                    $nodeId = $node['parentid'];
                    $openNodes[$i] = $nodeId;
                    $i++;
                } else {
                    $nodeId = '0';
                }
            }

            return $openNodes;
        }






        /**
         * Method to get add ONLY the IMEDIATE/next level content/sections for a particular section node
		 * FOR USE WITH AJAX in JQuery Simple Tree
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
     public function addNextAjaxContent($id, $module, $action, $admin = FALSE, $call = true)
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
						$action = 'showfulltext';
					}
                    if (!empty($action)) {
                        $url = $this->uri(array('action' => $action, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
                        $link = '<a href="'.$url.'">'.trim($contentTitle).'</a>'."\n";
                    } else {
                        $link = $contentTitle;
                    } 
             
                    $htmlContent .='<li><span>'.$link.'</span></li>'."\n";
                   
                }
                 if ($this->getChildNodes($id, $admin)) {
                 	$htmlContent .= $this->addNextAjaxChildren($id, $module, $action, $admin);
		          }
                return $htmlContent;
            }
            return '';
        }
        
 








        /**
         * Method to get add ONLY the IMEDIATE/next level content/sections for a particular section node
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
     public function addNextContent($id, $module, $action, $admin = FALSE, $call = true)
        {       	
            
            $contentNodes = $this->getContent($id, $admin);
			$nodes = $this->getChildNodes($id, $admin);

            $htmlContent = '';
            if (!empty($contentNodes) || $nodes) {
		        $htmlContent =		'<ul>'."\n";	
                foreach($contentNodes as $contentNode) {

					$contentTitle = $contentNode[title];
                    if ($id != '0'){
                        if (strlen($contentTitle) > 15){
							if (!strcmp(' ',$contentTitle)){
								$contentTitle = wordwrap($contentNode[title], 20, '<br/>'."\n");
							} else {
								$contentTitle = substr($contentNode[title], 0, 16).'...';
							}

                        }
                    }

					//TODO: Check why the hasChildren function isnt working!!
					if ($this->hasChildNodes($contentNode[id], $admin)){
						$action = 'viewsection';
					} else {
						$action = 'viewsection';
					}


                    if (!empty($action)) {
                        $url = $this->uri(array('action' => $action, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
                        $link = '<a href="'.$url.'">'.trim($contentTitle).'</a>'."\n";
                    } else {
                        $link = $contentTitle;
                    } 
             
                    $htmlContent .='<li><span>'.$link.'</span></li>'."\n";
                   
                }
			
                 if ($this->_objSections->getChildNodes($id, $admin)) {
                 	$htmlContent .= $this->addNextAjaxChildren($id, $module, $action, $admin);
		          }
                return $htmlContent .'</ul>'."\n";
            }
            return '';
        }
        
 









        /**
         * Method to get add all content for a particular section node
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
     public function addContent($id, $module, $action, $admin = FALSE)
        {       	
            
            $contentNodes = $this->getContent($id, $admin);

            $htmlContent = '';
            if (!empty($contentNodes)) {
		        $htmlContent =		'<ul>'."\n";	
                foreach($contentNodes as $contentNode) {
                    if (!empty($action)) {
                        $url = $this->uri(array('action' => $action, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
                        $link = '<a href="'.$url.'">'.trim($contentNode['title']).'</a>'."\n";
                    } else {
                        $link = $contentNode['title'];
                    } 
             
                    $htmlContent .='<li><span>'.$link.'</span></li>'."\n";
                   
                }
                 if ($this->getChildNodes($id, $admin)) {
		         
                 	$htmlContent .= $this->addChildren($id, $module, $action, $admin);
		          }
                return $htmlContent .'</ul>'."\n";
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
				//TODO: Check why the hasChildren function isnt working!!
                    if ($this->hasChildNodes($contentNode[id], $admin)){
                        $action = 'viewsection';
                    } else {
                        $action = 'viewsection';
                    }
	
					//var_dump($contentTitle);
                    if (!empty($action)) {
                        $nodeUri = $this->uri(array('action' => $action, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                        $link = '<a href="'.$nodeUri.'">'.$contentTitle.'</a>'."\n";
                    } else {
                        $link = $contentTitle;
                    }

                    
					if ($this->hasChildNodes($id, $admin)){
						$ajax_link = $this->uri(array('action' => 'getmenuchildnodes', 'id' => $node['id']), 'cmsadmin');
						
						$htmlContent .= "<li><span>$link</span><ul class='ajax'><li> {$ajax_link} </li></ul></li>";
					} else {
                 	 
                   		$htmlContent .= "<li><span>".$link."</span>\n";
					}
                }
                       
           
            return $htmlContent;
        }












        /**
         * Method to get add ONLY IMMEDIATE content for a particular child node
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
        public function addNextChildren($id, $module, $action, $admin = FALSE)
        {
        	 //gets all the child nodes of id
            $nodes = $this->getChildNodes($id, $admin);

			
            $htmlContent = '';
        	foreach($nodes as $node) {
           	
                    if (!empty($action)) {
                        $nodeUri = $this->uri(array('action' => $action, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                        $link = '<a href="'.$nodeUri.'">'.$node['title'].'</a>'."\n";
                    } else {
                        $link = $node['title'];
                    }

                   	$htmlContent .= "<li>".$link."\n";
                        	                        
                    //$contentNodes = $this->getContent($node['id'], $admin);
                    $contentNodes = '';
			         
		            if (!empty($contentNodes)) {
		            	$htmlContent .=	'<ul>'."\n";		
			             foreach($contentNodes as $contentNode) {
			                    if (!empty($action)) {
			                        $url = $this->uri(array('action' => $action, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
			                        $link = '<a href="'.$url.'">'.$contentNode['title'].'</a>'."\n";
			                    } else {
			                        $link = $contentNode['title'];
			                    }                                      
			                    $htmlContent .='<li>'.$link.'</li>'."\n";
			                }
		                if ($this->getChildNodes($node['id'], $admin)) {
		                	 $sibling = $this->getChildNodes($node['id'], $admin);
		                	 
                 			foreach($sibling as $ctNodes) {
			                     if (!empty($action)) {
                        			$nodeUri = $this->uri(array('action' => $action, 'id' => $ctNodes['id'], 'sectionid' => $ctNodes['id']), $module);
                        			$link = '<a href="'.$nodeUri.'">'.$ctNodes['title'].'</a>'."\n";
                    			} else {
                        			$link = $ctNodes['title'];
                    			}
                   				$htmlContent .= "<li>".$link."\n";
		           				$cNodes = $this->getContent($ctNodes['id'], $admin);
		           				$htmlContent .='<span class="text">'.$cNodes[0]['title'].'"</span>'."\n";
		            			$htmlContent .=	'<ul>'."\n";	
                   				foreach ($cNodes as $value) {
                   					if (!empty($action)) {
			                        	$url = $this->uri(array('action' => $action, 'id' => $value['id'], 'sectionid' => $value['sectionid']), $module);
			                        	$link = '<a href="'.$url.'">'.$value['title'].'</a>';
			                    	} else {
			                        	$link = $value['title'];
			                    	}
			                    	$htmlContent .='<li>'.$link.'</li>'."\n";
                   				}
                 				$htmlContent .='</ul>'."\n";
		                		$htmlContent .='</li>'."\n";
			                }
		          		}
		                $htmlContent .='</ul>'."\n";
		                $htmlContent .='</li>'."\n";
		               
		                
		            }
                  

                }
                       
           
            return $htmlContent;
        }



        
        /**
         * Method to get add all content for a particular child node
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
        public function addChildren($id, $module, $action, $admin = FALSE)
        {
        	 //gets all the child nodes of id
            $nodes = $this->getChildNodes($id, $admin);
            $htmlContent = '';
        	foreach($nodes as $node) {
                  
                    if (!empty($action)) {
                        $nodeUri = $this->uri(array('action' => $action, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                        $link = '<a href="'.$nodeUri.'">'.$node['title'].'</a>'."\n";
                    } else {
                        $link = $node['title'];
                    }

                   	$htmlContent .= "<li>".$link."\n";
                        	                        
                    $contentNodes = $this->getContent($node['id'], $admin);
			         
		            if (!empty($contentNodes)) {
		            	$htmlContent .=	'<ul>'."\n";		
			             foreach($contentNodes as $contentNode) {
			                    if (!empty($action)) {
			                        $url = $this->uri(array('action' => $action, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
			                        $link = '<a href="'.$url.'">'.$contentNode['title'].'</a>'."\n";
			                    } else {
			                        $link = $contentNode['title'];
			                    }                                      
			                    $htmlContent .='<li>'.$link.'</li>'."\n";
			                }
		                if ($this->getChildNodes($node['id'], $admin)) {
		                	 $sibling = $this->getChildNodes($node['id'], $admin);
		                	 
                 			foreach($sibling as $ctNodes) {
			                     if (!empty($action)) {
                        			$nodeUri = $this->uri(array('action' => $action, 'id' => $ctNodes['id'], 'sectionid' => $ctNodes['id']), $module);
                        			$link = '<a href="'.$nodeUri.'">'.$ctNodes['title'].'</a>'."\n";
                    			} else {
                        			$link = $ctNodes['title'];
                    			}
                   				$htmlContent .= "<li>".$link."\n";
		           				$cNodes = $this->getContent($ctNodes['id'], $admin);
		           				$htmlContent .='<span class="text">'.$cNodes[0]['title'].'"</span>'."\n";
		            			$htmlContent .=	'<ul>'."\n";	
                   				foreach ($cNodes as $value) {
                   					if (!empty($action)) {
			                        	$url = $this->uri(array('action' => $action, 'id' => $value['id'], 'sectionid' => $value['sectionid']), $module);
			                        	$link = '<a href="'.$url.'">'.$value['title'].'</a>';
			                    	} else {
			                        	$link = $value['title'];
			                    	}
			                    	$htmlContent .='<li>'.$link.'</li>'."\n";
                   				}
                 				$htmlContent .='</ul>'."\n";
		                		$htmlContent .='</li>'."\n";
			                }
		          		}
		                $htmlContent .='</ul>'."\n";
		                $htmlContent .='</li>'."\n";
		               
		                
		            }
                  

                }
                       
           
            return $htmlContent;
        }

        /**
         * Method to get check if a node has any children
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function hasChildNodes($parentId)
        {
            return $this->_objSec->hasNodes($parentId);
        }


        /**
         * Method to get all child nodes for a particular node
         * @param string $parentId The parent node id
         * @return array
         * @access public
         */
        public function getChildNodes($parentId, $noPermissions = FALSE)
        {
            return $this->_objSections->getChildNodes($parentId, $noPermissions);
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

}

?>

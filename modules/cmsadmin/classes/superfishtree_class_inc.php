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

class superfishtree extends object
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
        public function getCMSAdminTree($current)
        {

            $menu_html =  '<div id="superfishadminmenu">'.$this->show($current,TRUE,'cmsadmin','addsection','addcontent') . '</div>';

            //echo $menu_html; exit;

            return $menu_html;
        }

		/**
        * Method to get the simple jquery tree to display on the CMS module
        * @return string
        */
        public function getCMSTree($current)
        {
            /*
            $script = "<script type='text/javascript'>
                jQuery(document).ready(function(){
                    jQuery('#cmsleftblockscontainer').css('padding', '10px');
                    var height = jQuery('#chis_sf_menu').height();
                    window.console.log('height : ' + height);
                    jQuery('#cmsleftblockscontainer').insertAfter('#chis_sf_menu');
                });

            </script>";
            //$this->appendArrayVar('headerParams', $script);
            */
            $menu_html =  '<div id="superfishmenu">'.$this->show($current,TRUE,'cms','showfulltext','showfulltext') . '</div>';

            return $menu_html;
        }

        /**
        * Method to return back the tree code
        * @param string $currentNode The currently selected node, which should remain open
           * @param bool $admin Select whether admin user or not
        * @return string
        * @access public
        */
        public function show($currentNode, $admin, $module = 'cms', $sectionAction = 'showsection', $contentAction = 'showcontent')
        {
            $html = $this->showTree($currentNode, $admin, $module, $sectionAction, $contentAction);
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
        public function showTree($currentNode, $admin, $module = 'cms', $sectionAction = 'showsection', $contentAction = 'showcontent')
        {
            //check if there are any root nodes
            
            if ($this->getChildNodeCount(0) > 0) {
                $html = "<div id=\"chis_sf_menu\" class=\"chis_sf_menu\">\n
                         <ul class=\"sf-menu sf-vertical\">\n";
                //$html .= $this->buildLevel(0, $currentNode, $admin, $module, $sectionAction, $contentAction);

                $html .= $this->buildTree(0, $admin, $module, $sectionAction, $contentAction);
            
                $html .= '</ul></div><!-- end: superfish tree div -->';
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
        public function buildTree($currentNodeId, $admin, $module = 'cms', $sectionAction = 'showsection', $contentAction = 'showcontent', $isChild = false)
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

                        $html .= '<li>'.$link."\n";
                        //If the Section has child sections recurse
                        
                        $contentItem = '';
                        if ($this->hasChildContent($node['id'])) {

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
        
                                    $contentItem .= '<li>'.$link.'</li>'."\n";
                                    
                                    //echo "CONTENT NODE HERE : "; 
                                    //var_dump($cNode); 
                                }
                            }
                        }

                        if ($this->hasChildNodes($node['id'])){
                        //Call Recursively to add children sections and content
                            $level = $this->buildTree($node['id'], $admin, $module = 'cms', $sectionAction = 'showsection', $contentAction = 'showcontent', true) . $contentItem;
                            if (trim($level) != ''){
                                $html .= '<ul>' .$level . '</ul>'."\n";
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
         * Method to build the tree
         * @param string $currentNode The currently selected node, which should remain open
            * @param bool $admin Select whether admin user or not
         * @return string
         * @access public
         */
        public function buildTree_old($currentNode, $admin, $module = 'cms', $sectionAction = 'showsection', $contentAction = 'showcontent')
        {
            //check if there are any root nodes
                
            if ($this->getChildNodeCount(0) > 0) {
                $html = "<div id=\"chis_sf_menu\" class=\"chis_sf_menu\">\n
                         <ul class=\"sf-menu sf-vertical\">\n";
                // build the home link
                //$nodeUri = $this->uri(array(),'cms');
                //$text = $this->objLanguage->languageText('word_home');
                //$link = '<a href="'.$nodeUri.'">'.$text.'</a>'."\n";
                
                //start the tree building
                //$html .= "<li class='root'>$link <ul>\n";
                $html .= $this->buildLevel(0, $currentNode, $admin, $module, $sectionAction, $contentAction);
                //$html .= '</ul> </li>'."\n";
                $html .= '</ul></div><!-- end: superfish tree div -->';
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

            //var_dump($nodes);
            if (!empty($nodes)) {


                $htmlLevel = '';
                foreach($nodes as $node) {
                       //var_dump($node['title']);
    
                    $item = '';
                    if (!empty($sectionAction)) {
                        $nodeUri = $this->uri(array('action' => $sectionAction, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
                        if (strlen($node['title'])>24){
                        	$text = wordwrap(trim($node['title']),24,"<br />\n");
                        }else{
                        	$text = trim($node['title']);
                        }
                        
                        $link = '<a href="'.$nodeUri.'">'.$text.'</a>'."\n";
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
                    	$item = $this->addContent($node['id'], $module, $sectionAction, $contentAction, $admin);

                        $htmlLevel .= '<ul>'.$this->buildLevel($node['id'], $currentNode, $admin, $module, $sectionAction, $contentAction).'</ul>';

                        $htmlLevel .= $item.'</li>' ."\n";
                    }else{

                    	  $htmlLevel .= "<li>".$link."\n";
                    	  $item .= $this->addContent($node['id'], $module, $sectionAction, $contentAction, $admin);
                    	  $htmlLevel .= $item.'</li>' ."\n";

                    }
                                       
                }

				//echo "CALLED: ".$htmlLevel . '<br/>'.$link. "</br><br/>";
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
         * Method to get add all content for a particular section node
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
     	public function addContent($id, $module, $sectionAction, $contentAction, $admin = FALSE)
        {       	
            
            $contentNodes = $this->getContent($id, $admin);

            $htmlContent = '';
            if (!empty($contentNodes)) {
		        $htmlContent =		'<ul>'."\n";	
                foreach($contentNodes as $contentNode) {
                    if (!empty($contentAction)) {
                        $url = $this->uri(array('action' => $contentAction, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
                        $link = '<a href="'.$url.'">'.trim($contentNode['title']).'</a>'."\n";
                    } else {
                        $link = $contentNode['title'];
                    } 
             
                    $htmlContent .='<li>'.$link.'</li>'."\n";
                   
                }
                if ($this->getChildNodes($id, $admin)) {
		         
                 	$htmlContent .= $this->addChildren($id, $module, $sectionAction, $contentAction, $admin);
		        }

                return $htmlContent .'</ul>'."\n";
            }
            return '';
        }

        
        /**
         * Method to get add all content/sections for a particular child node
         * @param string $id The id of the section node
         * @return string
         * @access public
         */
        public function addChildren($id, $module, $sectionAction, $contentAction, $admin = FALSE)
        {
        	 //gets all the child nodes of id
            $nodes = $this->getChildNodes($id, $admin);
            $htmlContent = '';
        	foreach($nodes as $node) {
					
					//Correctly marking the section
					$hasChildren = $this->hasChildNodes($node['id']);
					//var_dump($hasChildren);
					//var_dump($node['id'] . ' : ' . $node['title']);
					if ($hasChildren){
						//var_dump('has children: '.$node['title']);						

	                    if (!empty($sectionAction)) {
    	                    $nodeUri = $this->uri(array('action' => $sectionAction, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
        	                $link = '<a href="'.$nodeUri.'">'.$node['title'].'</a>'."\n";
            	        } else {
                	        $link = $node['title']; 
                    	}

					} else {
						//var_dump('no kids: '. $node['title']);						

	                    if (!empty($contentAction)) {
    	                    $nodeUri = $this->uri(array('action' => $contentAction, 'id' => $node['id'], 'sectionid' => $node['id']), $module);
        	                $link = '<a href="'.$nodeUri.'">'.$node['title'].'</a>'."\n";
            	        } else {
                	        $link = $node['title'];
                    	}

					}


                   	$htmlContent .= "<li>".$link."\n";
                        	                        
                    $contentNodes = $this->getContent($node['id'], $admin);
			         
		            if (!empty($contentNodes)) {
		            	$htmlContent .=	'<ul>'."\n";		
			             foreach($contentNodes as $contentNode) {
			                    if (!empty($contentAction)) {
			                        $url = $this->uri(array('action' => $contentAction, 'id' => $contentNode['id'], 'sectionid' => $contentNode['sectionid']), $module);
			                        $link = '<a href="'.$url.'">'.$contentNode['title'].'</a>'."\n";
			                    } else {
			                        $link = $contentNode['title'];
			                    }                                      
			                    $htmlContent .='<li>'.$link.'</li>'."\n";
			                }
                        
                        //var_dump($this->getChildNodes($node['id'], $admin));

		                if ($this->getChildNodes($node['id'], $admin)) {
		                	$sibling = $this->getChildNodes($node['id'], $admin);
		                	 
                 			foreach($sibling as $ctNodes) {
			                     if (!empty($contentAction)) {
                        			$nodeUri = $this->uri(array('action' => $contentAction, 'id' => $ctNodes['id'], 'sectionid' => $ctNodes['id']), $module);
                        			$link = '<a href="'.$nodeUri.'">'.$ctNodes['title'].'</a>'."\n";
                    			} else {
                        			$link = $ctNodes['title'];
                    			}
                   				$htmlContent .= "<li>".$link."\n";
		           				$cNodes = $this->getContent($ctNodes['id'], $admin);

                                //if (!empty($cNodes)){
		                            $htmlContent .='<span class="text">'.$cNodes[0]['title'].'</span>'."\n";
		                            $htmlContent .=	'<ul>'."\n";	
                                    foreach ($cNodes as $value) {
                                        if (!empty($contentAction)) {
			                                $url = $this->uri(array('action' => $contentAction, 'id' => $value['id'], 'sectionid' => $value['sectionid']), $module);
			                                $link = '<a href="'.$url.'">'.$value['title'].'</a>';
			                            } else {
			                                $link = $value['title'];
			                            }
			                            $htmlContent .='<li>'.$link.'</li>'."\n";
                                    }
                                    $htmlContent .='</ul>'."\n";
		                            $htmlContent .='</li>'."\n";
                                //}
			                }
		          		}

		                $htmlContent .='</ul>'."\n";
		                $htmlContent .='</li>'."\n";
		               
		                
		            }
                  

                }
                       
           
            return $htmlContent;
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

}

?>

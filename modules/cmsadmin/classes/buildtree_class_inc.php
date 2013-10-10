<?php

/* -------------------- cmstree class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
* This object is a wrapper class for building various types of trees and menus for cms
* @package cms
* @category cmsadmin
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version
* @author Serge Meunier, Prince Mbekwa
* @example :???
*/
class buildtree extends object
{

	/**
     * The sections  object
     *
     * @access private
     * @var object
    */
    protected $objTreeNodes;

     /**
     * The Content object
     *
     * @access private
     * @var object
    */
    protected $objContent;



	/**
	 * Constructor
	 */
	public function init()
	{
		try {
			$this->objTreeNodes = & $this->newObject('treenodes', 'cmsadmin');
			$this->objContent = & $this->newObject('dbcontent', 'cmsadmin');
        } catch(customException $e) {
            echo customException::cleanUp();
            die();
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
    public function show($currentId = null, $parentId = '0', $treeType = 'tree', $linkClass = null, $headerClass = null, $recursionDepth = 999999, $specifiedLink = null, $onlyPublished = TRUE, $noPermissions = TRUE)
    {
        $html = '';

        if ($treeType == 'cooljs') {
            $html = $this->buildCoolJSMenu($parentId, $onlyPublished, 400, 100);
        } elseif ($treeType == 'flatmenu') {
            $html = $this->buildFlatMenu($parentId, FALSE, $linkClass, $onlyPublished);
        } elseif ($treeType == 'flatbarmenu') {
            $html = $this->buildFlatMenu($parentId, TRUE, $linkClass, $onlyPublished);
        } elseif ($treeType == 'foldoutmenu') {
            $html = $this->buildFoldoutMenu($parentId, $onlyPublished);
        } elseif ($treeType == 'staticmenu') {
            $html = $this->buildStaticMenu($parentId, $linkClass, $headerClass, $onlyPublished);
        } elseif ($treeType == 'listmenu') {
            $html = $this->buildListMenu($parentId, $linkClass, $onlyPublished);
        } elseif ($treeType == 'dynamictree') {
            $html = $this->buildDynamicTree($currentId, $parentId, $onlyPublished);
        } elseif ($treeType == 'standardtree') {
            $html = $this->buildStandardTree($currentId, $parentId, $recursionDepth, $specifiedLink, $onlyPublished, $noPermissions);
        }
        return $html;
    }

	/**
	 * Method to build a flat menu
     *
     * @param string $parentId Parent node of nodes to put into the menu
     * @param bool $useBar Flag to use bar or not
     * @param string $linkClass css class to set menu to
	 * @return string
	 * @access public
	 */
    public function buildFlatMenu($parentId = '0', $useBar = false, $linkClass = null, $onlyPublished = FALSE)
    {
        $i = 1;
        $nodeCount = $this->getChildNodeCount($parentId, $onlyPublished);

        if (is_null($linkClass)) {
            $style = '';
        } else {
            $style = ' class="'.$linkClass.'"';
        }
        //check if there are any root nodes
        if ($nodeCount > 0){
            $html = '<table><tr>';

            $nodes = $this->getChildNodes($parentId, $onlyPublished);
            foreach ($nodes as $node) {
                $html .= '<td'.$style.'>'.$this->buildLink($node).'</td>';
                //Add a spacer bar into the menu
                if ($useBar && ($i < $nodeCount)) {
                    $html .= '<td'.$style.'>&nbsp;|&nbsp;</td>';
                }
                $i++;
            }

            $html .= '</tr></table>';
        }else{
            $html = '';
        }
        return $html;
    }

	/**
	 * Method to build a link from a node
     *
     * @param object $node The node to build a link from
	 * @return string the html code for the link
	 * @access public
	 */
    public function buildLink($node, $specifiedLink = null)
    {
		$link = & $this->newObject('link','htmlelements');

        if (!is_null($specifiedLink)) {
            $finalStr = str_replace('[-ID-]', $node['id'], $specifiedLink);
            $finalStr = str_replace('%5B-ID-%5D', $node['id'], $specifiedLink);
            $link->href = $finalStr;
        } else {
            if ($node['node_type'] == 1) {
                //cms content page
                $link->href = $this->uri(array('sectionid'=>$node['id']), 'cms');
            } elseif ($node['node_type'] == 2) {
                //external link
                $link->href = htmlspecialchars($node['link_reference']);
            } elseif ($node['node_type'] == 0) {
                //root link
                 $link->href = $this->uri(array('sectionid'=>trim($node['id'])), 'cms');
        	}
        }
       
        $link->link = $node['title'];
         return $link->show();
    }

    public function buildFoldoutMenu($parentId = '0', $onlyPublished = FALSE)
    {
        $html = '';
   		$objImg = & $this->newObject('image', 'htmlelements');
        $objImg->src='skins/_common/icons/block.gif';
        $objImg->border='0';

        $nodeCount = $this->getChildNodeCount($parentId, $onlyPublished);
        if ($this->getChildNodeCount($parentId, $onlyPublished) > 0) {
            $html .= '<div class="navbar">';
            $nodes = $this->getChildNodes($parentId, $onlyPublished);
            foreach ($nodes as $node) {
                $html .= '<div class="mainDiv">';
                $html .= '<div class="topItem">'.$node['title'].
                      $this->buildLink($node).'</div>';
                $html .= '<div class="dropMenu"><!-- -->';
                $html .= '<div class="subMenu">';
                if ($this->getChildNodeCount($node['id'], $onlyPublished) > 0) {
                    $childNodes = $this->getChildNodes($node['id'], $onlyPublished);
                    foreach ($childNodes as $childNode) {
                        $html .= '<div class="subItem">';

                        $objImg->alt=$childNode['title'];
                        $html .= $objImg->show().'&nbsp;';
                        $html .= $this->buildLink($childNode);
                        $html .= '</div>';
                    }
                }

                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }

            $html .= $this->getJavascriptFile('xpmenuv21.js','cmsadmin')."</div>";
        }
        return $html;
    }

	/**
	 * Method to build a static menu
     *
     * @param string $parentId Parent node of nodes to put into the menu
     * @param string $linkClass css class to set items in menu to
     * @param string $headerClass css class to set headings in menu to
	 * @return string
	 * @access public
	 */
    public function buildStaticMenu($parentId = '0', $linkClass = null, $headerClass = null, $onlyPublished = FALSE)
    {
        $html = '';
        if (!is_null($linkClass)) {
            $linkStyle = ' class="'.$linkClass.'"';
        } else {
            $linkStyle = '';
        }
        if (!is_null($headerClass)) {
            $headerStyle = ' class="'.$headerClass.'"';
        } else {
            $headerStyle = '';
        }

        $nodeCount = $this->getChildNodeCount($parentId, $onlyPublished);
        if ($nodeCount > 0) {
			$html = '<table>';
            $nodes = $this->getChildNodes($parentId, $onlyPublished);
            foreach ($nodes as $node) {
                $html .= '<tr><td'.$headerStyle.'>'.$this->buildLink($node).'</td></tr>';
                if ($this->getChildNodeCount($node['id'], $onlyPublished) > 0) {
                    $childNodes = $this->getChildNodes($node['id'], $onlyPublished);
                    foreach ($childNodes as $childNode) {
                        $html .= '<tr><td'.$linkStyle.'>'.$this->buildLink($childNode).'</td></tr>';
                    }
                }
				$html .= '<tr><td>&nbsp;</td></tr>';

            }
			$html .= '</table><br />';
        }
        return $html;
    }

	/**
	 * Method to build a list menu
     *
     * @param string $parentId Parent node of nodes to put into the menu
     * @param string $linkClass css class to set items in menu to
	 * @return string
	 * @access public
	 */
    public function buildListMenu($parentId = '0', $linkClass = null, $onlyPublished = FALSE)
    {
        $html = '';
   		$objImg = & $this->newObject('image', 'htmlelements');
        $objImg->src='skins/_common/icons/block.gif';
        $objImg->border='0';

        if (!is_null($linkClass)) {
            $linkStyle = ' class="'.$linkClass.'"';
        } else {
            $linkStyle = '';
        }
        $nodeCount = $this->getChildNodeCount($parentId, $onlyPublished);
        if ($nodeCount > 0) {
            $nodes = $this->getChildNodes($parentId, $onlyPublished);
            foreach ($nodes as $node) {
                $html .= '<span>'.$objImg->show().'&nbsp;'.$this->buildLink($node).'</span><br />';
            }
        }
        return $html;
    }

	/**
	 * Method to build a coolJS menu
     *
     * @param string $rootId Parent node of nodes to put into the menu
	 * @return string
	 * @access public
	 */
    public function buildCoolJSMenu($rootId, $onlyPublished = FALSE, $xPos = '10', $yPos = '170')
    {
        $script = '<script type="text/javascript">
        			
                   BLANK_IMAGE = "skins/_common/icons/block.gif";

                      var STYLE = {
                      	border:1,
                      	shadow:1,
                      	color:{
                            border:"navy",
                            shadow:"silver",
                            bgON:"white",
		                    bgOVER:"navy"
	                    },
	                    css:{
		                    ON:"clsCMOn",
		                    OVER:"clsCMOver"
	                    }
                      };

                      var MENU_ITEMS = [
                        {pos:['.$xPos.','.$yPos.'], itemoff:[20,0], leveloff:[20,40], style:STYLE, size:[22,180]},
                        '.$this->buildCoolJSItems($rootId, $onlyPublished).'];
						 
                      </script>
                      <script type="text/javascript">
                      
                         var m1 = new COOLjsMenu("menu1", MENU_ITEMS);
                      
                      </script>
                       ';
        return $script;

    }

    public function buildCoolJSItems($rootId, $onlyPublished)
    {
        $nodes = $this->getChildNodes($rootId, $onlyPublished);
        $str = '';

        //check if there are any root nodes
        if (count($nodes) > 0){
            foreach($nodes as $node){
                if ($node['node_type'] == 1) {
                    //cms content page
                    $link = htmlspecialchars("index.php?module=cms&pageid=".$node['id']);
                } elseif ($node['node_type'] == 2) {
                    //external link
                    $link = htmlspecialchars($node['link_reference']);
                } else {
                    $link = '';
                }
                $str .= '{code:"'.$node['title'].'", url:"'.$link.'"';

                if ($this->getChildNodeCount($node['id'], $onlyPublished) > 0){
                    $str .= ', sub:[{}, ';

                    $str .= $this->buildCoolJSItems($node['id'], $onlyPublished);
                    if (substr($str, strlen($str) - 1, 1) == ',') {
                        $str = substr($str, 0, strlen($str) - 1);
                    }
                    $str .= ']';
                }
                $str .= '},';
            }
            $str = substr($str, 0, strlen($str) - 1);
        }
        return $str;
    }

    /**
	 * Method to get a list of all nodes to remain open
	 * @param string $currentNode The currently selected node
	 * @return array
	 * @access public
	 */
    public function getOpenNodes($nodeId, $rootId = '0')
    {
        $openNodes = array();

        $i = 0;
        while($nodeId != $rootId){
            $node = $this->getNode($nodeId);
            if (count($node)){
                $nodeId = $node[0]['parent_id'];
                $openNodes[$i] = $node[0];
                $i++;
            }else{
                $nodeId = $rootId;
            }
        }
        return $openNodes;
    }
    /**
	 * Method to get a list of all nodes to remain open
	 * @param string $currentNode The currently selected node
	 * @return array
	 * @access public
	 */
    public function getOpenNodeIds($nodeId, $rootId = '0')
    {
        $openNodes = array();

        $i = 0;
        while($nodeId != $rootId){
            $node = $this->getNode($nodeId);
            if (count($node)){
                $nodeId = $node[0]['parent_id'];
                $openNodes[$i] = $nodeId;
                $i++;
            }else{
                $nodeId = $rootId;
            }
        }
        return $openNodes;
    }

	/**
	 * Method to get all child nodes for a particular node
	 * @param string $parentId The parent node id
	 * @return array
	 * @access public
	 */
    public function getChildNodes($parentId, $onlyPublished = FALSE, $noPermissions = TRUE)
    {
        return $this->objTreeNodes->getChildNodes($parentId, $onlyPublished, $noPermissions);
    }

	/**
	 * Method to get node for a particular id
	 * @param string $id The node id
	 * @return array
	 * @access public
	 */
    public function getNode($id, $noPermissions = TRUE)
    {
         return $this->objTreeNodes->getNode($id, $noPermissions);
    }

	/**
	 * Method to get number of child nodes for a particular node
	 * @param string $parentId The parent node id
	 * @return int
	 * @access public
	 */
    public function getChildNodeCount($parentId, $onlyPublished = FALSE, $noPermissions = TRUE)
    {
         return $this->objTreeNodes->getChildNodeCount($parentId, $onlyPublished, $noPermissions);
    }

	/**
	 * Method to get a value from a node recursively (ie if null, then try get it from the parent)
	 * @param string $id The node id
     * @param string $field The field to get
	 * @return array
	 * @access public
	 */
    public function getRecursiveValue($id, $field, $rootId = '1')
    {
        $node = $this->objTreeNodes->getNode($id);

        if (count($node) > 0) {
            if ($rootId == $node[0]['parent_id']) {
                return $node[0][$field];
            }
            if (is_null($node[0][$field])) {
                return $this->getRecursiveValue($node[0]['parent_id'], $field, $rootId);
            } else {
                return $node[0][$field];
            }
        } else {
            return NULL;
        }
    }

        /**
	 * Method to retrieve the root node
	 * @param string $currentNode The currently selected node
	 * @return array
	 * @access public
	 */
    public function getRoot($nodeId)
    {
       $node = $this->getNode($nodeId);

       if (count($node) > 0) {
           if ($node[0]['parent_id'] == '0') {
               return $node[0]['id'];
           } else {
               return $this->getRoot($node[0]['parent_id']);
           }
       } else {
           return $nodeId;
       }
    }


     /**
     * Method to show the bread crumb
     *
     * @access public
     * @param string nodeId The id of the node to start from
     * @param string rootId The id of the root node to stop at
     * @return string The code for the breadcrumb
     */
    public function getBreadCrumb($nodeId, $rootId = NULL)
    {
        if (($rootId) == NULL) {
            $rootId= $this->getRoot($nodeId);
        }
        $nodes = $this->getOpenNodes($nodeId, $rootId);
        $breadCrumb = '';
        $i = 0;
        foreach ($nodes as $node) {
            if ($i == 0) {
                $breadCrumb .= $node['title'];
            } else {
                $breadCrumb = $this->buildLink($node).'/'.$breadCrumb;
            }
            $i++;
        }
        return $breadCrumb;
    }

 	/**
	 * Method to return back the tree code
	 * @param string $nodeId The currently selected node
     * @param string $rootId The base root to start from
	 * @return string html code containing the tree
	 * @access public
	 */
    public function buildDynamicTree($nodeId, $rootId = '0', $onlyPublished = FALSE)
	{
        if ($nodeId == null) {
            $nodeId = $rootId;
        }

		$table = $this->buildDynamicTreeTable($this->getChildNodes($rootId), 0, $nodeId, $onlyPublished);
		return  $table;
	}


 	/**
	 * Method to build the tables for the tree
     * @param string $nodeArray The array of nodes to build
     * @param string $level The current recursion level within the tree
	 * @param string $nodeId The currently selected node
	 * @return string html code containing the tree
	 * @access public
	 */
	public function buildDynamicTreeTable($nodeArray, $level = 0, $nodeId)
	{
		$table = & $this->newObject('htmltable','htmlelements');
		$link = & $this->newObject('link','htmlelements');
		$table->init();
		$table->cssClass="menulink";

		$indent="";

		for ($i = 1; $i <= $level; $i++) {
			$indent .= "&nbsp;";
		}

		$finalTable = '';
		$path = 'modules/esb/resources/';

        $ancestorNodes = $this->getOpenNodes($nodeId);

		foreach ($nodeArray as $node) {

			//setup the icons
			$selectBold=false;

			if ($node['id']==$nodeId) {
				$icon = 'indentsel';
				$selectBold = true;
			} else {
				$icon = 'indent';
			}

		    $objImg = & $this->newObject('image', 'htmlelements');
    		$objImg->src = $path.$icon.'.gif';
	    	$menuIcon = $objImg->show();

            $table->startRow();
            $link->href = $this->uri(array('pageid'=>$node['id']), 'cmsadmin');

            if ($selectBold)	{
                $theLink='<span class="dtreeselected">'.ucfirst($node['title']).'</span>';
            } else {
                $link->link = ucfirst($node['title']);
                $theLink=$link->show();
            }

            $table->addCell($menuIcon, null, 'top');
            $table->addCell($theLink, '90%', 'top');
            $table->endRow();

            //Builds next level if matches
            if (($this->getChildNodeCount($node['id']) > 0) && (in_array($node['id'], $ancestorNodes))) {
                $subTable = $this->buildDynamicTreeTable($this->getChildNodes($node['id']), $level + 1, $nodeId);

                $table->startRow();
                $table->addCell('', null, 'top');
                $table->addCell($subTable, '90%', 'top', 'left');
                $table->endRow();
            }
		}

		$finalTable.=$table->show();

		return($finalTable);
	}



	/**
	 * Method to get a list of all ancestor nodes to the current node
	 * @param string $currentNode The currently selected node
	 * @return array
	 * @access public
	 */
    public function getAncestorNodes($currentNode)
    {
        $nodeId = $currentNode;

        $openNodes = array();

        $openNodes[0] = $currentNode;
        $i = 1;
        while($nodeId != '0'){
            $node = $this->getNode($nodeId);
            if (count($node)){
                $nodeId = $node[0]['parent_id'];
                $openNodes[$i] = $nodeId;
                $i++;
            } else {
                $nodeId = '0';
            }
        }
        return $openNodes;
    }

	/**
	 * Method to build the tree
	 * @param string $currentNode The currently selected node, which should remain open
     * @param bool $admin Select whether admin user or not
	 * @return string
	 * @access public
	 */
    public function buildStandardTree($currentNode = null, $startingNode = '0', $recursionDepth = 999999, $specifiedLink = null, $onlyPublished = FALSE, $noPermissions = TRUE)
    {
        //check if there are any root nodes
        if ($this->getChildNodeCount('0', $onlyPublished) > 0){
            $html = '<ul class="menuList">';
            //start the tree building
            $html .= $this->buildStandardTreeLevel($startingNode, $currentNode, 0, $recursionDepth, $specifiedLink, $onlyPublished, $noPermissions);
            $html .= '</ul>';
        }else{
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
    public function buildStandardTreeLevel($parentId, $currentNode, $currentLevel, $recursionDepth, $specifiedLink = null, $onlyPublished, $noPermissions)
    {
        $nodes = $this->getChildNodes($parentId, $onlyPublished, $noPermissions);
        $htmlLevel = '';

         //get the list of nodes that need to stay open for the currently selected node
        $openNodes = $this->getOpenNodeIds($currentNode);
        if (count($nodes)){

            foreach($nodes as $node){
                $htmlChildren = '';
                $link = $this->buildLink($node, $specifiedLink);
                if ($this->getChildNodeCount($node['id'], $onlyPublished, $noPermissions) > 0){
                    //if node has further child nodes, recursively call buildLevel
                    if ($currentLevel < $recursionDepth) {
                        $htmlChildren .= $this->buildStandardTreeLevel($node['id'], $currentNode, $currentLevel + 1, $recursionDepth, $specifiedLink, $onlyPublished, $noPermissions);
                    }
                    if(in_array($node['id'], $openNodes)){
                        $htmlLevel .= '<li>';
                    }else{
                        $htmlLevel .= '<li class="open">';
                    }
                    $htmlLevel .= $link.'<ul>';
                    $htmlLevel .= $htmlChildren;
                    $htmlLevel .= '</ul></li>';
                }else{
                    if(in_array($node['id'], $openNodes)){
                        $htmlLevel .= '<li>';
                    }else{
                        $htmlLevel .= '<li class="closed">';
                    }
                    $htmlLevel .= $link.'<ul>'.$htmlChildren.'</ul></li>';
                }

            }
        }
        return $htmlLevel;
    }
   
}
?>

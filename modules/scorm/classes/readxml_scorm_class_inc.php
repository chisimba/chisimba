<?php
/* ----------- readxml_Scorm class extends object------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for reading imsmanifest to create navigation
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape
 */

class readxml_scorm extends object {
/**
 * @var groupAdminModel an object reference.
 */
    var $_objGroupAdminModel;
    /**
     * @var treeMenu an object reference.
     */
    var $_objTreeMenu;
    /**
     * @var array extra tree options for display.
     */
    var $_extra;
    /**
     * @var string Context Id of root node.
     */
    var $_rootNode;
    /**
     * @var string Location of tree icons.
     */
    var $_treeIcons='';
    /**
     * @var array Icons for the tree.
     */
    var $_arrTreeIcons=array();

    /**
     * @var string Module Link for Trees
     */
    var $treeTargetModule = 'scorm';

    /**
     * @var string Action Link for Trees
     */
    var $treeTargetAction = 'main';

    /**
     * @var string Additional Id Parameter for Trees
     */
    var $treeTargetId = NULL;

    /**
     * @var string Target Window for Trees
     */
    var $treeTargetWindow = Null;

    /**
     *
     * Intialiser for the readxml_Scorm controller
     * @access public
     *
     */
    public function init() {
    // Get the DB object.
        $this->objConfig =& $this->newObject('altconfig','config');
        $this->objUser =& $this->getObject('user', 'security');
        $this->loadClass('treemenu', 'tree');
        $this->loadClass('treenode', 'tree');
        $this->loadClass('dhtml','tree');
        $this->loadClass('hiddeninput', 'htmlelements');
        // Initialise icons
        $this->objSkin = $this->getObject( 'skin', 'skin' );
        $this->_treeIcons = 'icons/tree/';

        $this->_arrTreeIcons = array();
        $this->_arrTreeIcons['root'] = 'treefolder_green';
        $this->_arrTreeIcons['subroot'] = 'treefolder_white';
        $this->_arrTreeIcons['empty']['selected'] = 'folder-expanded_selected';
        $this->_arrTreeIcons['empty']['open'] = 'treefolder-expanded_green';
        $this->_arrTreeIcons['empty']['closed'] = 'treefolder_green';

    }

    /**
     *
     *Method to create the course navigations
     *@param string $rootFolder
     *@param string $courseFolder
     * @return array
     */
    public function readManifest2($rootFolder, $courseFolder=Null) {

        $doc = new DOMDocument();
        $doc->load( 'usrfiles/'.$rootFolder.'/imsmanifest.xml' );

        $books = $doc->getElementsByTagName( "item" );
        $allbooks = $doc->getElementsByTagName( "organization" );
        $resources = $doc->getElementsByTagName( "resource" );
        $nodesItem = $books->item(0);
        $content = $this->xmlMicroxTree($nodesItem,$resources,$rootFolder,$ident="");

        $hiddenInput = new hiddeninput('input_rootFolder', $rootFolder);
        return $content."<br /><input type='hidden' name='input_rootfolder' id='input_rootfolder' value='".$rootFolder."' />";
    }
    public function readManifest($rootFolder, $courseFolder=Null) {

        $doc = new DOMDocument();
        try {
            $doc->load( 'usrfiles/'.$rootFolder.'/imsmanifest.xml' );
        } catch (Exception $e)  {
            return "error";
         }
        $books = $doc->getElementsByTagName( "item" );
        $allbooks = $doc->getElementsByTagName( "organization" );
        $resources = $doc->getElementsByTagName( "resource" );
        $nodesItem = $books->item(0);
        //Prepare the tree
        $this->_objTreeMenu =& new treemenu();
        $menu  = &$this->_objTreeMenu;
        $rootMenu = &$this->createRootNode();
        $this->_objTreeMenu->addItem( $rootMenu );

        $content = $this->xmlMicroxTree($nodesItem,$resources,$rootFolder,$ident="",$rootMenu,$rootMenu2=Null,$rootMenu3=Null,$rootMenu4=Null,$treenode=Null,$a=Null,$myLevl=Null);

        $hiddenInput = new hiddeninput('input_rootFolder', $rootFolder);
        return $this->showDHTML()."<br /><input type='hidden' name='input_rootfolder' id='input_rootfolder' value='".$rootFolder."' />";
    }
    function xmlMicroxTree($nod,$resources,$rootFolder,$ident,$rootMenu=Null,$rootMenu2=Null,$rootMenu3=Null,$rootMenu4=Null,$treenode=Null,$a=Null,$myLevl=Null) {
        $row = array();
        $NodList=$nod->childNodes;
        $treeTxt = "";//var to content temp
        for( $j=0 ;  $j < $NodList->length; $j++ ) { 	//each child node
            $nod2=$NodList->item($j);//Node j
            //no white spaces
            if($nod2->nodeType==1) {
                if($nod2->nodeName=="title") {
                    $treeTxt = "<br />\n".$treeTxt.$ident;
                }
                if($nod2->childNodes->length==0) {//no children.Get nodeValue
                    $treeTxt = $treeTxt.$nod2->nodeValue;
                    $atribId = $nod2->parentNode->getAttribute('identifierref');
                    foreach ( $resources as $myresources ) {
                        if( $myresources->getAttribute('identifier') ==  $atribId) {
                            $resourcePath = $myresources->getAttribute('href');
                            //An Iframe with name content is the link target
                            $treeTxt = '<a href = "usrfiles/'.$rootFolder.'/'.$resourcePath.'" target = "content">'.$treeTxt.'</a>';
                        }
                    }

                    $treeTxt = $treeTxt."<br />\n";
                }else if ($nod2->childNodes->length>0) {//children.Get first child
                    //$treeTxt = $treeTxt.$nod2->firstChild->nodeValue;
                        $atribId = $nod2->parentNode->getAttribute('identifierref');
                        foreach ( $resources as $myresources ) {
                            if( $myresources->getAttribute('identifier') ==  $atribId) {
                                $resourcePath = $myresources->getAttribute('href');
                                //An Iframe with name content is the link target
                                $pageName = $nod2->firstChild->nodeValue;
                                $treeTxt = $treeTxt.'<a href = "usrfiles/'.$rootFolder.'/'.$resourcePath.'" target = "content">'.$pageName.'</a>';
                                if($treenode==Null) {
                                    $row['id'] = $atribId;
                                    $row['uri'] = "";
                                    $row['text'] = '<a href = "usrfiles/'.$rootFolder.'/'.$resourcePath.'" target = "content">'.$pageName.'</a>';

                                    if(empty($a)) {
                                        $a = 1;
                                        if(trim($pageName)!== "") {
                                        //		                                    echo $a." ".$row['text']."<br> ";
                                            $treenode = &$this->createTreeNode( $row );
                                            $rootMenu->addItem( $treenode );
                                            $myLevl = 12;
                                        }
                                    }
                                }
                                else {
                                    $a = $a+1;
                                    $subRow = array();
                                    $subRow['id'] = $atribId;
                                    $subRow['uri'] = "";
                                    $subRow['text'] = '<a href = "usrfiles/'.$rootFolder.'/'.$resourcePath.'" target = "content">'.$pageName.'</a>';

                                    if(trim($pageName)!== "") {
                                        if($myLevl==12) {
                                            $rootMenu2 = &$this->createTreeNode( $subRow );
                                            $treenode->addItem( $rootMenu2 );
                                            $myLevl = $myLevl + 12;
                                        }elseif($myLevl==24) {
                                            $rootMenu3 = &$this->createTreeNode( $subRow );
                                            $rootMenu2->addItem( $rootMenu3 );
                                            $myLevl = $myLevl + 12;
                                        }elseif($myLevl==36) {
                                            $rootMenu4 = &$this->createTreeNode( $subRow );
                                            $rootMenu3->addItem( $rootMenu4 );
                                            $myLevl = $myLevl + 12;
                                        }else {
                                            $rootMenu5 = &$this->createTreeNode( $subRow );
                                            $rootMenu4->addItem( $rootMenu5 );
                                        }
                                    }

/*
					  if(trim($pageName)!== ""){
						  if(strlen($ident)==12){
				                    $rootMenu2 = &$this->createTreeNode( $subRow );			        	  
		                                    $treenode->addItem( $rootMenu2 );
		                                  }elseif(strlen($ident)==24){
				                    $rootMenu3 = &$this->createTreeNode( $subRow );			        	  
		                                    $rootMenu2->addItem( $rootMenu3 );	                                   
		                                  }elseif(strlen($ident)==36){
				                    $rootMenu4 = &$this->createTreeNode( $subRow );			        	  
		                                    $rootMenu3->addItem( $rootMenu4 );	                                   
		                                  }else{
				                    $rootMenu5 = &$this->createTreeNode( $subRow );			        	  
		                                    $rootMenu4->addItem( $rootMenu5 );
		                                  }
					  }
*/
                                }

                            }
                        }
                        //recursive to child of children
                        $treeTxt = $treeTxt.$this->xmlMicroxTree($nod2,$resources,$rootFolder,$ident."&nbsp;&nbsp;",$rootMenu,$rootMenu2,$rootMenu3,$rootMenu4,$treenode, $a, $myLevl);

                    }
            }
        }
        return $treeTxt;
    }

    function getContent($nod,$resources,$rootFolder) {
        $NodList=$nod->childNodes;
        for( $j=0 ;  $j < $NodList->length; $j++ ) {       $nod2=$NodList->item($j);//Node j
            if($nod2->childNodes->length>0) {
                $this->getContent($nod2,$resources,$rootFolder);
            }
            $nodemane=$nod2->nodeName;
            $nodevalue=$nod2->nodeValue;
            $nodeAttr=$nod2->attribute;
            $NodeContent .=  $nodevalue.$nodeAttr."<br>";

            $cid = $nod->getAttribute('identifierref');
            //an array to hold the tag id
            //$identifier = $book->getAttribute('identifier');
            //$arrIdentifier[$arrId] = "'".$identifier."'";
            foreach ( $resources as $myresources ) {
                if( $myresources->getAttribute('identifier') ==  $cid ) {
                    $resourcePath = $myresources->getAttribute('href');
                    //An Iframe with name content is the link target
                    $fullPath = '<a href = "usrfiles/'.$rootFolder.'/'.$resourcePath.'" target = "content">'.$nodevalue.'</a>';
                }
            }
            $navigation = $navigation."<div>$fullPath</div>";
        }
        return $navigation;
    }
    function xmlMicroxTree2($nod,$resources,$rootFolder,$ident) {
        $NodList=$nod->childNodes;
        $treeTxt = "";//var to content temp
        for( $j=0 ;  $j < $NodList->length; $j++ ) { 	//each child node
            $nod2=$NodList->item($j);//Node j
            //no white spaces
            if($nod2->nodeType==1) {
                if($nod2->nodeName=="title") {
                    $treeTxt = "<br />\n".$treeTxt.$ident;
                }
                if($nod2->childNodes->length==0) {//no children.Get nodeValue
                    $treeTxt = $treeTxt.$nod2->nodeValue;
                    $atribId = $nod2->parentNode->getAttribute('identifierref');
                    foreach ( $resources as $myresources ) {
                        if( $myresources->getAttribute('identifier') ==  $atribId) {
                            $resourcePath = $myresources->getAttribute('href');
                            //An Iframe with name content is the link target
                            $treeTxt = '<a href = "usrfiles/'.$rootFolder.'/'.$resourcePath.'" target = "content">'.$treeTxt.'</a>';
                        }
                    }
                    $treeTxt = $treeTxt."<br />\n";
                }else if ($nod2->childNodes->length>0) {//children.Get first child
                    //$treeTxt = $treeTxt.$nod2->firstChild->nodeValue;
                        $atribId = $nod2->parentNode->getAttribute('identifierref');
                        foreach ( $resources as $myresources ) {
                            if( $myresources->getAttribute('identifier') ==  $atribId) {
                                $resourcePath = $myresources->getAttribute('href');
                                //An Iframe with name content is the link target
                                $treeTxt = $treeTxt.'<a href = "usrfiles/'.$rootFolder.'/'.$resourcePath.'" target = "content">'.$nod2->firstChild->nodeValue.'</a>';
                            }
                        }
                        //recursive to child of children
                        $treeTxt = $treeTxt.$this->xmlMicroxTree($nod2,$resources,$rootFolder,$ident."&nbsp;&nbsp;");

                    }
            }
        }
        return $treeTxt;
    }
    function xmlNextPage($pagePath,$rootFolder) {
    //$pagePath = 'http://10.2.31.176/chisimba_frameworks/app/usrfiles/users/4733080702/dba203/l1introduction_to_organization_theory.html';
        $doc = new DOMDocument();
        $doc->load( 'usrfiles/'.$rootFolder.'/imsmanifest.xml' );
        //get the kewl_root_path
        $rootPath = $this->objConfig->getsiteRoot();
        $books = $doc->getElementsByTagName( "item" );
        $resources = $doc->getElementsByTagName( "resource" );
        $nod = $books->item(0);
        $NodList=$nod->childNodes;
        $treeTxt = "";//var to content temp
        if($arrId==null) {
            $arrId = 0;
        }
        foreach( $books as $book ) {
            $nodes = $book->getElementsByTagName( "item" );
            //Display root node
            $res = $book->getElementsByTagName( "title" );
            $resname = $res->item(0)->nodeValue;
		/* get attribute */
            $cid = $book->getAttribute('identifierref');
            foreach ( $resources as $myresources ) {
                if( $myresources->getAttribute('identifier') ==  $cid ) {
                    $resourcePath = $myresources->getAttribute('href');
                    $fullPath = $rootPath.'usrfiles/'.$rootFolder.'/'.$resourcePath;
                    $arrPath[$arrId] = $fullPath;
                    $arrId = $arrId + 1;
                }
            }
        }
        //step through the array and get next page
        foreach($arrPath as $key=>$myarrPath) {
            if ($myarrPath==$pagePath) {
                $nextPage = $arrPath[$key+1];
            }
        }
        return $nextPage;
    }
    function xmlPrevPage($pagePath,$rootFolder) {
    //http://10.2.31.176/chisimba_frameworks/app/usrfiles/users/4733080702/dba203/l1introduction_to_organization_theory.html';
        $doc = new DOMDocument();
        $doc->load( 'usrfiles/'.$rootFolder.'/imsmanifest.xml' );
        //get the kewl_root_path
        $rootPath = $this->objConfig->getsiteRoot();
        $books = $doc->getElementsByTagName( "item" );
        $resources = $doc->getElementsByTagName( "resource" );
        $nod = $books->item(0);
        $NodList=$nod->childNodes;
        $treeTxt = "";//var to content temp
        if($arrId==null) {
            $arrId = 0;
        }
        foreach( $books as $book ) {
            $nodes = $book->getElementsByTagName( "item" );
            //Display root node
            $res = $book->getElementsByTagName( "title" );
            $resname = $res->item(0)->nodeValue;
		/* get attribute */
            $cid = $book->getAttribute('identifierref');
            foreach ( $resources as $myresources ) {
                if( $myresources->getAttribute('identifier') ==  $cid ) {
                    $resourcePath = $myresources->getAttribute('href');
                    $fullPath = $rootPath.'usrfiles/'.$rootFolder.'/'.$resourcePath;
                    $arrPath[$arrId] = $fullPath;
                    $arrId = $arrId + 1;
                }
            }
        }
        //step through the array and get next page
        foreach($arrPath as $key=>$myarrPath) {
            if ($myarrPath==$pagePath) {
                $nextPage = $arrPath[$key-1];
            }
        }
        return $nextPage;
    }
    function xmlFirstPage($rootFolder) {
        $doc = new DOMDocument();
        $doc->load( 'usrfiles/'.$rootFolder.'/imsmanifest.xml' );
        //get the kewl_root_path
        $rootPath = $this->objConfig->getsiteRoot();
        $books = $doc->getElementsByTagName( "item" );
        $resources = $doc->getElementsByTagName( "resource" );
        $nod = $books->item(0);
        $NodList=$nod->childNodes;
        $treeTxt = "";//var to content temp
        //if($arrId==null) {
            $arrId = 0;
        //}
        foreach( $books as $book ) {
            $nodes = $book->getElementsByTagName( "item" );
            //Display root node
            $res = $book->getElementsByTagName( "title" );
            $resname = $res->item(0)->nodeValue;
		/* get attribute */
            $cid = $book->getAttribute('identifierref');
            foreach ( $resources as $myresources ) {
                if( $myresources->getAttribute('identifier') ==  $cid ) {
                    $resourcePath = $myresources->getAttribute('href');
                    $fullPath = $rootPath.'usrfiles/'.$rootFolder.'/'.$resourcePath;
                    $arrPath[$arrId] = $fullPath;
                    $arrId = $arrId + 1;
                }
            }
        }
        //step through the array and get next page
        foreach($arrPath as $key=>$myarrPath) {
            if ($arrPath[$key]==0) {
                $firstPage = $arrPath[0];
            }
        }
        return $firstPage;
    }
    /**
     * Method to get the tree menu object
     * @access public
     * @return object reference
     */
    function &getTreeMenu() {
        return $this->_objTreeMenu;
    }
    /**
     * Method to create a tree node.
     * @param  array   contains the current group row.
     * @access private
     * @return object  reference
     */
    function &createTreeNode( &$row ) {
    // Initialize locals
        $icons = $this->_arrTreeIcons;
        $pageId   = $row['id'];

        $linkText = $row['text'];
        $link = $row['uri'];

        $icon   = $icons['empty']['closed'].'.gif';// Empty closed folder
        $expand = $icons['empty']['open'].'.gif';// Empty expanded folder


        $treenode = new treenode(  array (
            'text'         => $linkText,
            'link'         => $link,
            'value'        => $pageId,
            'icon'         => $icon,
            'expandedIcon' => $expand,
            'cssClass'     => '',
            'linkTarget'   => $this->treeTargetWindow
        ));
        return $treenode;
    }

    /**
     * Method to create a root node.
     * @access private
     * @return object  reference
     */
    function &createRootNode() {
    // Context Aware;
        $icons = $this->_arrTreeIcons;
        $this->_rootNode = NULL;
        $treenode = new treenode( array ( 'text' => '<STRONG>Content List</STRONG>', 'icon' => $icons['root'].'.gif' ));
        return $treenode;

    }
    /**
     * Method to create a tree menu object.
     * @access private
     * @return nothing
     */
    function createTreeMenu() {
        $this->_objTreeMenu =& new treemenu();

        $rootMenu = &$this->createRootNode();
        $this->_objTreeMenu->addItem( $rootMenu );

        $this->recureTree( $this->_rootNode, $rootMenu );
    }

    /**
     * Method to show the tree as a tree like structure.
     * @access public
     * @return string the HTML output
     */
    function showDHTML() {
        $this->_extra = array(
            'images' => $this->objSkin->getCommonSkinURL().$this->_treeIcons,
            'defaultClass' => 'treeMenuDefault' );

        $dhtmlMenu = new dhtml( $this->_objTreeMenu, $this->_extra );

        $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js','tree'));

        return $dhtmlMenu->getMenu();
    }

    /**
     * Method to build the tree structure.
     * @access private
     * @return nothing
     */
    function recureTree( &$node, &$parentNode ) {
        $menu  = &$this->_objTreeMenu;
        $newNode = &$this->createTreeNode( $node );
        if  ( is_null( $parentNode ) ) {
            $menu->addItem( $newNode );
        } else {
            $parentNode->addItem( $newNode );
        }
    }

}
?>

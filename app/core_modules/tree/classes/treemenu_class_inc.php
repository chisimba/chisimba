<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002-2003, Richard Heyes, Harald Radi                        |
// | All rights reserved.                                                  |
// |                                                                       |
// | Redistribution and use in source and binary forms, with or without    |
// | modification, are permitted provided that the following conditions    |
// | are met:                                                              |
// |                                                                       |
// | o Redistributions of source code must retain the above copyright      |
// |   notice, this list of conditions and the following disclaimer.       |
// | o Redistributions in binary form must reproduce the above copyright   |
// |   notice, this list of conditions and the following disclaimer in the |
// |   documentation and/or other materials provided with the distribution.|
// | o The names of the authors may not be used to endorse or promote      |
// |   products derived from this software without specific prior written  |
// |   permission.                                                         |
// |                                                                       |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
// |                                                                       |
// +-----------------------------------------------------------------------+
// | Author: Richard Heyes <richard@phpguru.org>                           |
// |         Harald Radi <harald.radi@nme.at>                              |
// +-----------------------------------------------------------------------+
//
// $Id$

/**
* HTML_TreeMenu Class
*
* A simple couple of PHP classes and some not so simple
* Jabbascript which produces a tree menu. In IE this menu
* is dynamic, with branches being collapsable. In IE5+ the
* status of the collapsed/open branches persists across page
* refreshes.In any other browser the tree is static. Code is
* based on work of Harald Radi.
*
* Usage.
*
* After installing the package, copy the example php script to
* your servers document root. Also place the TreeMenu.js and the
* images folder in the same place. Running the script should
* then produce the tree.
*
* Thanks go to Chip Chapin (http://www.chipchapin.com) for many
* excellent ideas and improvements.
*
* @author  Richard Heyes <richard@php.net>
* @author  Harald Radi <harald.radi@nme.at>
* @access  public
* @package HTML_TreeMenu
*/

//class HTML_TreeMenu
class treemenu
{
    /**
    * Indexed array of subnodes
    * @var array
    */
    var $items;

    /**
    * Constructor
    *
    * @access public
    */
    function treemenu()
    {
        // Not much to do here :(
    }

    /**
    * This function adds an item to the the tree.
    *
    * @access public
    * @param  object $node The node to add. This object should be
    *                      a HTML_TreeNode object.
    * @return object       Returns a reference to the new node inside
    *                      the tree.
    */
    function &addItem(&$node)
    {
        $this->items[] = &$node;
        return $this->items[count($this->items) - 1];
    }

    /**
    * Import method for creating HTML_TreeMenu objects/structures
    * out of existing tree objects/structures. Currently supported
    * are Wolfram Kriesings' PEAR Tree class, and Richard Heyes' (me!)
    * Tree class (available here: http://www.phpguru.org/). This
    * method is intended to be used statically, eg:
    * $treeMenu = &HTML_TreeMenu::createFromStructure($myTreeStructureObj);
    *
    * @param  array  $params   An array of parameters that determine
    *                          how the import happens. This can consist of:
    *                            structure   => The tree structure
    *                            type        => The type of the structure, currently
    *                                           can be either 'heyes' or 'kriesing'
    *                            nodeOptions => Default options for each node
    *                            
    * @return object           The resulting HTML_TreeMenu object
    */
    function createFromStructure($params)
    {
        if (!isset($params['nodeOptions'])) {
            $params['nodeOptions'] = array();
        }

        switch (@$params['type']) {

            /**
            * Wolfram Kriesings' PEAR Tree class
            */
            case 'kriesing':
                $className = strtolower(get_class($params['structure']->dataSourceClass));
                $isXMLStruct = strpos($className,'_xml') !== false ? true : false;

                // Get the entire tree, the $nodes are sorted like in the tree view
                // from top to bottom, so we can easily put them in the nodes
                $nodes = $params['structure']->getNode();

                // Make a new menu and fill it with the values from the tree
                $treeMenu  = new treemenu();
                $curNode[0] = &$treeMenu;   // we need the current node as the reference to the

                foreach ( $nodes as $aNode ) {
                    $events = array();
                    $data = array();

                    // In an XML, all the attributes are saved in an array, but since they might be
                    // used as the parameters, we simply extract them here if we handle an XML-structure
                    if ( $isXMLStruct && sizeof($aNode['attributes']) ){
                        foreach ( $aNode['attributes'] as $key=>$val ) {
                            if ( !$aNode[$key] ) { // dont overwrite existing values
                                $aNode[$key] = $val;
                            }
                        }
                    }

                    // Process all the data that are saved in $aNode and put them in the data and/or events array
                    foreach ( $aNode as $key=>$val ) {
                        if ( !is_array($val) ) {
                            // Dont get the recursive data in here! they are always arrays
                            if ( substr($key,0,2) == 'on' ){  // get the events
                                $events[$key] = $val;
                            }

                            // I put it in data too, so in case an options starts with 'on' its also passed to the node ... not too cool i know
                            $data[$key] = $val;
                        }
                    }

                    // Normally the text is in 'name' in the Tree class, so we check both but 'text' is used if found
                    $data['text'] = $aNode['text'] ? $aNode['text'] : $aNode['name'];

                    // Add the item to the proper node
                    $thisNode = &$curNode[$aNode['level']]->addItem( new treenode( $data , $events ) );
                    $curNode[$aNode['level']+1] = &$thisNode;
                }
                break;

            /**
            * Richard Heyes' (me!) second (array based) Tree class
            */
            case 'heyes_array':
                // Need to create a HTML_TreeMenu object ?
                if (!isset($params['treeMenu'])) {
                    $treeMenu = &new treemenu();
                    $parentID = 0;
                } else {
                    $treeMenu = &$params['treeMenu'];
                    $parentID = $params['parentID'];
                }
                
                // Loop thru the trees nodes
                foreach ($params['structure']->getChildren($parentID) as $nodeID) {
                    $data = $params['structure']->getData($nodeID);
                    $parentNode = &$treeMenu->addItem(new treenode(array_merge($params['nodeOptions'], $data)));

                    // Recurse ?
                    if ($params['structure']->hasChildren($nodeID)) {
                        $recurseParams['type']        = 'heyes_array';
                        $recurseParams['parentID']    = $nodeID;
                        $recurseParams['nodeOptions'] = $params['nodeOptions'];
                        $recurseParams['structure']   = &$params['structure'];
                        $recurseParams['treeMenu']    = &$parentNode;
                        treemenu::createFromStructure($recurseParams);
                    }
                }
                
                break;

            /**
            * Richard Heyes' (me!) original OO based Tree class
            */
            case 'heyes':
            default:
                // Need to create a HTML_TreeMenu object ?
                if (!isset($params['treeMenu'])) {
                    $treeMenu = &new treemenu();
                } else {
                    $treeMenu = &$params['treeMenu'];
                }
                
                // Loop thru the trees nodes
                foreach ($params['structure']->nodes->nodes as $node) {
                    $tag = $node->getTag();
                    $parentNode = &$treeMenu->addItem(new treenode(array_merge($params['nodeOptions'], $tag)));

                    // Recurse ?
                    if (!empty($node->nodes->nodes)) {
                        $recurseParams['structure']   = $node;
                        $recurseParams['nodeOptions'] = $params['nodeOptions'];
                        $recurseParams['treeMenu']    = &$parentNode;
                        treemenu::createFromStructure($recurseParams);
                    }
                }
                break;

        }

        return $treeMenu;
    }
    
    /**
    * Creates a treeMenu from XML. The structure of your XML should be
    * like so:
    *
    * <treemenu>
    *     <node text="First node" icon="folder.gif" expandedIcon="folder-expanded.gif" />
    *     <node text="Second node" icon="folder.gif" expandedIcon="folder-expanded.gif">
    *         <node text="Sub node" icon="folder.gif" expandedIcon="folder-expanded.gif" />
    *     </node>
    *     <node text="Third node" icon="folder.gif" expandedIcon="folder-expanded.gif">
    * </treemenu>
    *
    * Any of the options you can supply to the HTML_TreeNode constructor can be supplied as
    * attributes to the <node> tag. If there are no subnodes for a particular node, you can
    * use the XML shortcut <node ... /> instead of <node ... ></node>. The $xml argument can
    * be either the XML as a string, or an pre-created XML_Tree object. Also, this method
    * REQUIRES my own Tree class to work (http://phpguru.org/tree.html). If this has not
    * been include()ed or require()ed this method will die().
    *
    * @param  mixed  $xml  This can be either a string containing the XML, or an XML_Tree object
    *                      (the PEAR::XML_Tree package).
    * @return object       The HTML_TreeMenu object
    */
    function createFromXML($xml)
    {
        if (!class_exists('Tree')) {
            die('Could not find Tree class');
        }

        // Supplied $xml is a string
        if (is_string($xml)) {
            require_once('XML/Tree.php');
            $xmlTree = &new XML_Tree();
            $xmlTree->getTreeFromString($xml);

        // Supplied $xml is an XML_Tree object
        } else {
            $xmlTree = $xml;
        }

        // Now process the XML_Tree object, setting the XML attributes
        // to be the tag data (with out the XML tag name or contents).
        $treeStructure = Tree::createFromXMLTree($xmlTree, true);
        $treeStructure->nodes->traverse(create_function('&$node', '$tagData = $node->getTag(); $node->setTag($tagData["attributes"]);'));

        
        return HTML_TreeMenu::createFromStructure(array('structure' => $treeStructure));
    }
} // HTML_TreeMenu
//require_once('other_classes.php');

?>
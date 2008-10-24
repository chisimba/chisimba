<?php

/**
 * Content nodes
 *
 * Class to handle the content nodes in Chisimba context
 *
 * PHP versions 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Content nodes
 *
 * Class to handle the content nodes in Chisimba context
 *
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dbcontentnodes extends dbtable {

    /**
     * The id of the user currently logged in
     *
     * @var string $userId :
     */
    public $userId;

    /**
     * The context database object
     *
     * @var object $objDBContext :
     */
    public $objDBContext;

    /**
     * This list of nodes that needs to be deleted
     *
     * @var array $deleteList :
     */
    public $deleteList = array ();

    /**
     * Description for var
     *
     * @var    object
     * @access public
     */
    public $objDBParents;

    /**
     * Description for var
     *
     * @var    unknown
     * @access public
     */
    public $objDBParentContext;

    /**
     * Initializing the object
     */
    public function init() {
        parent::init ( 'tbl_context_nodes' );
        $user = $this->newObject ( 'user', 'security' );
        $this->objDBContext = $this->newObject ( 'dbcontext', 'context' );
        $this->objDBParent = $this->newObject ( 'dbparentnodes', 'context' );
        $this->objDBParentContext = $this->newObject ( 'dbcontextparentnodes', 'context' );
        $this->userId = $user->userId ();
    }

    /**
     * Method that retrieves the
     * child nodes from a give nodeId
     *
     * @param  $nodeId     int: the Node ID
     * @return bool|array: The list of child nodes
     * @access public
     */
    public function getChildNodes($nodeId, $orderBy = NULL) {
        //Check if a node id was sent
        if ($nodeId) {
            //get the result set
            if (isset ( $orderBy )) {
                $rs = $this->getAll ( ' WHERE parent_Node="' . $nodeId . '" ORDER BY "' . $orderBy . '"' );
            } else {
                $rs = $this->getAll ( ' WHERE parent_Node="' . $nodeId . '"' );
            }
            //return the result set
            return $rs;
        } else
            return false;
    }

    /**
     * Method to get a field from the
     * current table
     *
     * @param  $fiedname  string : the name of the field
     * @param  $nodeId    int    : the node id
     * @return array|bool : The value of the field
     * @access public
     */
    public function getField($fiedname, $nodeId) {
        $this->resetTable ();
        //get the row value
        $line = $this->getRow ( 'id', $nodeId );
        if ($line [$fiedname]) {
            return $line [$fiedname];
        } else
            return false;
    }

    /**
     * Method to save a node after editing
     *
     * @return null
     * @access public
     */
    public function saveEdit() {
        $nodeId = TRIM ( $_POST ['nodeid'] );
        $title = addslashes ( TRIM ( $_POST ['title'] ) );
        $this->changeTable ( 'tbl_context_nodes' );
        $this->update ( 'id', $nodeId, array ('title' => $title ) );

        $this->changeTable ( 'tbl_context_page_content' );
        $rsArr = array ('body' => $body, 'dateModified' => $this->getDate () );
        $this->update ( 'id', $this->getPageId ( $nodeId ), $rsArr );
        $this->resetTable ();
    }

    /**
     * Method  to find the Page Id for
     * a give node
     *
     * @param  unknown $nodeId Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function getPageId($nodeId) {
        $line = $this->getArray ( "SELECT * FROM tbl_context_page_content WHERE tbl_context_nodes_id = '$nodeId'" );
        if ($line [0] ['id']) {
            return $line [0] ['id'];
        } else {
            return false;
        }
    }

    /**
     * Method to save a Node
     *
     * @param  $mode         string : Either edit or add mode
     * @param  $userId       int    : The user id
     * @param  $parentNodeId int    : The parent Node Id
     * @return null
     * @access public
     */
    public function saveNode($mode) {
        $userId = $this->userId;
        $nodeId = $this->getParam ( 'nodeId' );
        $body = $this->getParam ( 'body' );
        $body = stripslashes ( $body );
        $objMMedia = $this->getObject ( 'parse4mmedia', 'filters' );
        $body = $objMMedia->parseAll ( $body );
        //$body = addslashes($body);
        $script = $this->getParam ( 'script' );

        $lastModified = date ( "Y-m-d H:i:s" );
        $title = $this->getParam ( 'nodetitle' );
        $addType = $this->getParam ( 'addtype' );
        $menutext = $this->getParam ( 'label' );
        $hasmetadata = $this->getParam ( 'hasmetadata' );

        $dublincore = $this->newObject ( 'dublincore', 'dublincoremetadata' );
        $creativecommons = $this->newObject ( 'dbcreativecommons', 'creativecommons' );
        $this->resetTable ();

        switch ($mode) {
            case 'edit' :
                //change to nodes table
                $this->changeTable ( 'tbl_context_nodes' );
                //update the title in the nodes table
                $this->update ( 'id', $nodeId, array ('title' => $title, 'script' => $script ) );

                //update dublin core metadata
                $dublincore->insertMetaData ( $nodeId );

                //CREATIVE Commons LICENSE
                $creativecommons->saveRecord ();

                //check if the node has a body
                //if not add one
                if (! $this->getPageId ( $nodeId )) {
                    //insert the body
                    $this->changeTable ( 'tbl_context_page_content' );
                    $newNodeId = $this->insert ( array ('ownerId' => $userId, 'menu_text' => $menutext, 'body' => $body, 'tbl_context_nodes_id' => $nodeId, 'fullname' => $title ) );

                } else {
                    //edit the body
                    $this->changeTable ( 'tbl_context_page_content' );
                    $rsArray = array ('menu_text' => $menutext, 'body' => $body );
                    $this->update ( "id", $this->getPageId ( $nodeId ), $rsArray );
                    return $nodeId;
                }
                break;

            case 'add' :

                //are you adding a child node ot sibling node
                if ($addType == 'sibling') {
                    $parentNodeId = $this->getParentId ( $nodeId );
                    $sortIndex = $this->setNodePosition ( $nodeId );
                } else {
                    $parentNodeId = $nodeId;
                    $sortIndex = $this->getNextOrderIndex ( $parentNodeId );
                }

                //create a node
                $this->changeTable ( 'tbl_context_nodes' );

                $prev_Node = $this->getNewPrev ( $parentNodeId, $sortIndex );
                $next_Node = $this->getNewNext ( $parentNodeId, $sortIndex );
                $nodeId = $this->insert ( array ('tbl_context_parentnodes_id' => $this->objDBContext->getRootNodeId (), 'title' => $title, 'prev_Node' => $prev_Node, 'next_Node' => '', 'sortindex' => $sortIndex, 'script' => $script, 'parent_Node' => $parentNodeId ) );
                //update the previous nodes' next field
                $this->update ( "id", $prev_Node, array ('next_Node' => $nodeId ) );
                //add metadata
                if ($hasmetadata) {

                    $dublincore->insertMetaData ( $nodeId );
                }

                //CREATIVE Commons LICENSE
                $creativecommons->saveRecord ();

                //insert some content if it was supplied
                if (! $body == '') {
                    $this->changeTable ( 'tbl_context_page_content' );
                    $contentId = $this->insert ( array ('ownerId' => $userId, 'menu_text' => $menutext, 'body' => $body, 'tbl_context_nodes_id' => $nodeId, 'fullname' => $title ) );
                }

                $this->reOrderNodes ();
                return $nodeId;
                break;
        }
    }

    /**
     * get new prev
     *
     * @param  unknown $parentId  Parameter description (if any) ...
     * @param  number  $sortIndex Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function getNewPrev($parentId, $sortIndex) {
        $arrPrevNode = $this->getNode ( $parentId, ($sortIndex - 1) );
        //  print_r($arrPrevNode);
        if (is_array ( $arrPrevNode )) {
            if (array_key_exists ( 0, $arrPrevNode )) {
                return $arrPrevNode [0] ['id'];
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Get new next
     *
     * @param  unknown $parentId  Parameter description (if any) ...
     * @param  unknown $sortIndex Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    public function getNewNext($parentId, $sortIndex) {
        return NULL;
    }

    /**
     * Method to delete a node
     *
     * @param  $nodeId int : The node id
     * @return bool
     * @access private
     */
    public function _deleteNode($nodeId) {
        return $this->delete ( 'id', $nodeId );
    }

    /**
     * Method to delete a node
     * with its children recursively
     *
     * @param  $nodeId int : the node id
     * @return bool
     * @access public
     */
    public function deleteNodeRecursively($nodeId) {
        $this->_biuldDeleteList ( $nodeId );
        foreach ( $this->deleteList as $list ) {
            $this->_deleteNode ( $list );
        }
        $this->_deleteNode ( $nodeId );
    }

    /**
     * Method to biuld the delete list
     *
     * @param  $nodeId int : the node id
     * @return NULL
     * @access private
     */
    public function _biuldDeleteList($nodeId) {
        $rs = $this->getChildNodes ( $nodeId );
        foreach ( $rs as $line ) {
            $this->_biuldDeleteList ( $line ['id'] );
            array_push ( $this->deleteList, $line ['id'] );
        }
    }

    /**
     * Method to get the Title for the context root Node
     *
     * @return string $title : The title
     * @access public
     * @param  string $rootNodeId : The id for the root node
     */
    public function getRootTitle($rootNodeId) {
        $this->changeTable ( 'tbl_context_parentnodes' );
        $line = $this->getRow ( 'id', $rootNodeId );
        $this->resetTable ();
        return $line ['title'];
    }

    /**
     * Method to get the body for a given node
     *
     * @param  string $nodeId : The node id of the body field
     * @access public
     * @return string : The body ,
     */
    public function getBody($nodeId) {
        $this->resetTable ();
        $line = $this->getRow ( 'id', $nodeId );
        $script = stripslashes ( $line ['script'] );
        $this->appendArrayVar ( 'headerParams', $script );

        $this->changeTable ( 'tbl_context_page_content' );
        $arr = $this->getRow ( 'tbl_context_nodes_id ', $nodeId );
        //TODO .. body needs to be parsed through a HTML validator
        return $arr ['body'];
    }

    /**
     * Method to get the first peice of content for a course
     *
     * @access prive
     * @return string $nodeId
     */
    public function _getFirstContentNodeId() {
        $rootnodeid = $this->objDBContext->getRootNodeId ();
        $this->resetTable ();
        $nodesArr = $this->getAll ( "WHERE tbl_context_parentnodes_id='$rootnodeid'" );
        foreach ( $nodesArr as $list ) {
            if ($list ['parent_Node'] == null) {
                return $this->_hasContent ( $list ['id'], $nodesArr );
            }
        }
    }

    /**
     * MEthod to check if a node has content
     *
     * @param  string  $nodeId The node ID
     * @return boolean
     */
    public function _hasContent($nodeId, $nodesArr) {
        $this->changeTable ( 'tbl_context_nodes' );
        if ($this->getBody ( $nodeId ) == '') {
            foreach ( $nodesArr as $list ) {
                if ($nodeId == $list ['parent_Node'])
                    return $this->_hasContent ( $list ['id'], $nodesArr );
            }
        } else {
            return $nodeId;
        }
    }

    /**
     * Method to get the title or menu text for a given node
     *
     * @param  string $nodeId : The node id of the body field
     * @access public
     * @return string : The menu text ,
     */
    public function getMenuText($nodeId) {
        $this->changeTable ( 'tbl_context_page_content' );
        $arr = $this->getRow ( 'tbl_context_nodes_id ', $nodeId );
        $this->resetTable ();
        //TODO .. body needs to be parsed through a HTML validator
        return $arr ['menu_text'];
    }

    /**
     * Method to change the working table
     *
     * @param  string $tName : The name of the table
     * @return null
     * @access public
     */
    public function changeTable($tName) {
        parent::init ( $tName );
    }

    /**
     * Method to reset the working table to 'tbl_context'
     *
     * @return null
     * @access public
     */
    public function resetTable() {
        parent::init ( 'tbl_context_nodes' );
    }

    /**
     * Method to check whether a node is the first node
     *
     * @return null
     * @access public
     */
    public function isFirstNode($nodeId) {
        //INCOMPLETE .. I am still working on this one
        return false;
    }

    /**
     * Method to get a Parent Id
     *
     * @param  string $nodeId : The nodeId
     * @access public
     * @return string : The parent Id
     */
    public function getParentId($nodeId) {
        $this->resetTable ();
        $line = $this->getRow ( 'id', $nodeId );

        return $line ['parent_Node'];
    }

    /**
     * Method to check whether a
     * a context has nodes
     */
    public function hasNodes($contextId = NULL) {
        $this->changeTable ( 'tbl_context_parentnodes_has_tbl_context' );
        $ret = $this->valueExists ( 'tbl_context_contextCode', $this->objDBContext->getContextCode () );

        return $ret;
    }

    /**
     * Method to create  the first node
     */
    public function createParentNode() {
        if (! $this->objDBParents->valueExists ( 'tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode', $this->objDBContext->getContextCode () )) {
            $contextId = $this->objDBContext->getContextId ();
            $contextCode = $this->objDBContext->getContextCode ();
            //add a bridge to context and parent
            $this->objDBContextParents->insert ( array ('tbl_context_id' => $contextId, 'tbl_context_contextCode' => $contextCode ) );

            //add a parent node
            $parentId = $this->objDBParents->insert ( array ('tbl_context_parentnodes_has_tbl_context_tbl_context_id' => $contextId, 'tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode' => $contextCode, 'userId' => $this->userId, 'dateCreated' => $this->objDBContext->getDate (), 'datemodified' => $this->objDBContext->getDate (), 'menu_text' => $this->objDBContext->getMenuText (), 'title' => $this->objDBContext->getTitle () ) );
        }
        $this->resetTable ();
    }

    //******************************************************
    //***** SHARED Nodes  Methods ************************
    //*******************************************************


    /**
     * Method to get all the shared nodes for a
     * given node
     *
     * @param string $nodeId The node Id that is being searched
     */
    public function getSharedNodes($parentNodeId) {
        $this->changeTable ( 'tbl_context_sharednodes' );
        $arr = $this->getAll ( 'WHERE  root_nodeid = "' . $parentNodeId . '"' );
        $this->resetTable ();

        return $arr;
    }

    /**
     * Method to insert a new shared node
     *
     * @param  string $sharedNodeId         The Shared Node ID
     * @param  string $currentContextNodeId The current node  ID
     * @param  string $contextId            The content Id
     *
     * @return string the new id that was generated
     */
    public function shareNodes($sharedNodeId, $currentContextNodeId, $contextId = NULL) {
        $this->changeTable ( 'tbl_context_sharednodes' );
        $res = $this->getAll ( 'WHERE shared_nodeid = "' . $sharedNodeId . '" AND nodeid = "' . $currentContextNodeId . '"' );

        $rootNodeId = $this->objDBContext->getRootNodeId ();

        if (! array_key_exists ( 0, $res )) {
            $newId = $this->insert ( array ('nodeid' => $currentContextNodeId, 'root_nodeid' => $rootNodeId, 'shared_nodeid' => $sharedNodeId ) );

            return $newId;
        }
        $this->resetTable ();
    }

    /**
     * Method to get a list of shared nodes from the database
     *
     * @param  string $rootNodeId The Root node id of course
     * @return array  The list of shared nodes
     */
    public function getSharedList($rootNodeId) {
        $this->changeTable ( 'tbl_context_sharednodes' );
        $list = $this->getAll ( "WHERE root_nodeid='" . $rootNodeId . "'" );
        $this->resetTable ();

        return $list;
    }

    /**
     * Method to delete a shared node
     *
     * @param string id The id of the shared node
     */
    public function deleteSharedNode($id) {
        $this->changeTable ( 'tbl_context_sharednodes' );
        return $this->delete ( 'id', $id );
    }
    //******************************************************
    //***** END OF --> SHARED Nodes Methods *************
    //*******************************************************


    //******************************************************
    //*****  Node Organising Methods ***********************


    /**
     * Method to get the next Order Index
     *
     * @param  string $parentId Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
    public function getMaxOrderIndex($parentId = NULL) {
        $this->resetTable ();
        $rootId = $this->objDBContext->getRootNodeId ();
        if ($parentId == NULL) {
            return $this->getArray ( "SELECT MAX(sortindex) as highestindex FROM tbl_context_nodes WHERE tbl_context_parentnodes_id = '" . $rootId . "' AND isNULL(parent_Node)" );
        } else {
            return $this->getArray ( "SELECT MAX(sortindex) as highestindex FROM tbl_context_nodes WHERE  tbl_context_parentnodes_id = '" . $rootId . "' AND parent_Node = '" . $parentId . "'" );
        }
    }

    /**
     * Method to get the next Sort Index
     *
     * @param  string PArentID The PArent Id
     * @return int
     */
    public function getNextOrderIndex($parentId = NULL) {
        $arr = $this->getMaxOrderIndex ( $parentId );

        return $arr [0] ['highestindex'] + 1;
    }

    /**
     * Method to get a sorted array of nodes for a parent
     *
     * @param  string $parentId The  parent Id
     * @return array
     */
    public function getSortedNodes($parentId = NULL) {
        return $this->getChildNodes ( $parentId, 'sortindex' );
    }

    /**
     * Method to get the next Sort Index node Id
     *
     * @param  string parentId The ParentId
     * @param  int    sortIdex The Sort Index
     * @return array  node
     */
    public function getNode($parentId = NULL, $sortIndex = NULL) {
        if ($parentId == NULL) {
            return $this->getAll ( 'WHERE isNULL(parent_Node) AND sortindex = ' . $sortIndex );
        } else {
            return $this->getAll ( 'WHERE parent_Node = "' . $parentId . '" AND sortindex = ' . $sortIndex );
        }
    }

    /**
     * Method to check if a node is the first node
     *
     * @param string $nodeId The NodeId
     *                       return bool
     */
    public function isFirstSibling($nodeId) {
        $line = $this->getRow ( 'id', $nodeId );
        if ($line ['sortindex'] == NULL) {
            return FALSE;
        } else {
            if ($line ['parent_Node'] == NULL) {
                $arr = $this->getArray ( 'SELECT MIN(sortindex) as lowest FROM tbl_context_nodes WHERE isNull(parent_Node)' );
            } else {
                $arr = $this->getArray ( 'SELECT MIN(sortindex) as lowest FROM tbl_context_nodes WHERE parent_Node = "' . $line ['parent_Node'] . '" ' );
            }
            if (is_array ( $arr [0] )) {
                if ($arr [0] ['lowest'] == $line ['sortindex']) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Method to check if a node is the last node
     *
     * @param string $nodeId The NodeId
     *                       return bool
     */
    public function isLastSibling($nodeId) {
        $line = $this->getRow ( 'id', $nodeId );
        $arr = $this->getMaxOrderIndex ( $line ['parent_Node'] );
        if (is_array ( $arr [0] )) {
            if ($arr [0] ['highestindex'] == $line ['sortindex']) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
        return false;
    }

    /**
     * MEthod to check if a node is part another node
     *
     * @param  string $sourceId      The source
     * @param  string $destinationId The destination
     * @return bool
     */
    public function isFamily($sourceId, $destinationId) {
        $ret = FALSE;
        //if both id's are the same
        if ($sourceId == $destinationId) {
            $ret = TRUE;
        }

        //check if the destinationId is a child
        if ($this->isChild ( $sourceId, $destinationId )) {
            $ret = TRUE;
        }

        return $ret;

    }

    /**
     * Method to check if a
     * node is a child
     *
     * @param  string $parentId The source
     * @param  string $childId  The destination
     * @return bool
     */
    public function isChild($parentId = NULL, $childId = NULL) {
        $ret = FALSE;
        if (($parentId == NULL) && ($childId == NULL)) {
            return $ret;
        }
        $desArr = $this->getAll ( 'WHERE parent_Node = "' . $parentId . '"' );
        foreach ( $desArr as $arr ) {
            if (! $arr ['id'] == $childId) {
                if ($this->hasChildren ( $arr ['id'] )) {
                    $ret = $this->isChild ( $arr ['id'] );
                }
            } else {
                $ret = TRUE;
            }

        }

        return $ret;
    }

    /**
     * Method to check if a
     * node has children
     *
     * @param  string parentId The parent Id
     * @return bool
     */
    public function hasChildren($parentId) {
        $arr = $this->getAll ( 'WHERE parent_Node ="' . $parentId . '"' );
        if (count ( $arr ) < 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Method to reorder the node sort index
     * or the position of the nodes
     *
     * @param string $nodeId The node Id
     */
    public function setNodePosition($nodeId) {
        //get the parent node
        $parentNodeId = $this->getParentId ( $nodeId );
        //get the previous node (the node before the new one)
        $prevNode = $this->getRow ( 'id', $nodeId );
        //get the list of nodes after the previous node
        if ($prevNode ['sortindex'] == NULL) {
            $sql = "WHERE parent_Node = '" . $parentNodeId . "' ORDER BY sortindex";
        } else {
            $sql = "WHERE parent_Node = '" . $parentNodeId . "' AND sortindex > " . $prevNode ['sortindex'] . " ORDER BY sortindex";
        }
        // print $parentNodeId;
        //die($sql);
        $nodesArr = $this->getAll ( $sql );
        if (! array_key_exists ( 0, $nodesArr )) {
            return $this->getNextOrderIndex ( $parentNodeId );
        }
        //the new node will be the one after the previous node
        if ($nodesArr [0] ['sortindex'] == NULL) {
            $ret = 1;
        } else {
            $ret = $nodesArr [0] ['sortindex'];
        }
        $newIndex = $ret;

        //reorder the rest of the nodes by adding one to them
        foreach ( $nodesArr as $arr ) {
            $newIndex = $newIndex + 1;
            $newArr = array ('sortindex' => $newIndex );
            $this->update ( 'id', $arr ['id'], $newArr );
        }

        return $ret;
    }

    /**
     * Method to reorder all the nodes
     *
     * @param string $rootNode The root Node
     */
    public function reOrderNodes($rootNode = NULL) {
        $this->next_Node = null;
        $prev_Node = null;
        $cnt = 1;
        $this->prevId = null;

        $this->resetTable ();
        if ($rootNode == NULL) {
            $rootNode = $this->objDBContext->getRootNodeId ();
        }

        //get all the nodes for the context
        $nodes = $this->getAll ( 'WHERE tbl_context_parentnodes_id = "' . $rootNode . '" AND isNull(parent_Node) ORDER BY sortindex' );

        //paginate the root nodes
        foreach ( $nodes as $node ) {

            //set the next node
            $this->update ( 'id', $this->prevId, array ('next_Node' => $node ['id'] ) );

            //setup the array to update table
            $newArr = array ('sortindex' => $cnt, 'prev_Node' => $this->prevId );

            //set the previous node
            $prev_Node = $node ['id'];
            $this->prevId = $node ['id'];

            //do the update
            $this->update ( 'id', $node ['id'], $newArr );

            //update the previous node's next_Node field with this nodeId


            //increment the counter
            $cnt = $cnt + 1;

            //recurse the other nodes
            $this->_recursiveReOrderNodes ( $node ['id'] );

        }
    }

    /**
     * Method that recurses through the nodes for
     * reorder the nodes
     *
     * @param string $parentID The ParentId
     */
    public function _recursiveReOrderNodes($parentId) {
        $next_Node = null;
        $prev_Node = null;
        $cnt = 1;

        $nodes = $this->getAll ( 'WHERE parent_Node = "' . $parentId . '" ORDER BY sortindex' );

        //paginate the root nodes
        foreach ( $nodes as $node ) {
            //set the next node
            //update the previous node's next_Node field with this nodeId
            $this->update ( 'id', $this->prevId, array ('next_Node' => $node ['id'] ) );

            //setup the array to update table
            $newArr = array ('sortindex' => $cnt, 'prev_Node' => $this->prevId );

            //set the previous node
            $prev_Node = $node ['id'];
            $this->prevId = $node ['id'];

            //do the update
            $this->update ( 'id', $node ['id'], $newArr );

            //increment the counter
            $cnt = $cnt + 1;

            //recurse the other nodes
            $this->_recursiveReOrderNodes ( $node ['id'] );
        }
    }

//******************************************************
//***** END OF -->  Node Organising Methods ************
//*******************************************************
}

?>
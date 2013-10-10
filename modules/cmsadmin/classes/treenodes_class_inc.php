<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the treenodes table for cms module.
* This class is based on the publicportal menu functionality
* 
*
* @package cms
* @category cms
* @copyright AVOIR
* @license GNU GPL
* @author Serge Meunier, Prince Mbekwa
*/

class treenodes extends dbTable
{
    /**
    * The user object
    *
    * @access public
    * @var object
    */
    public $objUser;

   /**
    * Class Constructor
    *
    * @access public
    * @return void
    */
    public function init()
    {
        try {
            parent::init('tbl_cms_treenodes');
            $this->objUser = & $this->newObject('user', 'security');

       } catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage();
            exit();
       }
    }

    /**
     * Method to save a record to the database specifying all params
     *
     * @access public
     * @return bool
     */
    public function add($title, $nodeType, $linkReference, $banner, $parentId, $layout, $css, $published, $publisherId, $ordering,$artifact=null)
    {
        $newArr = array(
                      'title' => $title ,
                      'node_type' => $nodeType,
                      'link_reference' => $linkReference,
                      'banner' => $banner,
                      'parent_id' => $parentId,
                      'layout' => $layout,
                      'css' => $css,
                      'published' => $published,
                      'publisher_id' => $publisherId,
                      'ordering' => $ordering,
                      'artifact_id' => $artifact
                      
                  );

        $newId = $this->insert($newArr);
        return $newId;
    }

    /**
     * Method to save a record to the database specifying all params
     *
     * @access public
     * @return bool
     */
    public function edit($id, $title, $nodeType, $linkReference, $banner, $parentId, $layout, $css, $published, $publisherId, $ordering,$artifact=null)
    {
        $newArr = array(
                      'title' => $title ,
                      'node_type' => $nodeType,
                      'link_reference' => $linkReference,
                      'banner' => $banner,
                      'parent_id' => $parentId,
                      'layout' => $layout,
                      'css' => $css,
                      'published' => $published,
                      'publisher_id' => $publisherId,
                      'ordering' => $ordering,
                       'artifact_id' => $artifact
                  );

        $newId = $this->update('id', $id, $newArr);
        return $newId;
    }

     /**
	 * Method to return the child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return array The array of child nodes
     * @access public
	 */
    public function getChildNodes($parentId, $onlyPublished = TRUE, $noPermissions = TRUE)
    {
		try {
            $published = '';

            if ($onlyPublished) {
                $published = ' AND published = 1';
            }

            if (($noPermissions) || ($this->objUser->isAdmin())) {
                $sql = "SELECT id, title, node_type, link_reference, banner, parent_id, layout, css, ordering, published, publisher_id,artifact_id FROM tbl_cms_treenodes"
                      ." WHERE parent_id = '".$parentId."'".$published." ORDER BY ordering";
            } else {
                $userId = $this->objUser->userId();
                $userPKId = $this->objUser->PKid($userId);

                $sql = "SELECT tbl_cms_treenodes.id, title, node_type, link_reference, banner, parent_id, layout, css, ordering, published, publisher_id,artifact_id FROM "
                      ."(tbl_cms_treenodes INNER JOIN tbl_groupadmin_groupuser ON publisher_id = group_id)"
                      ." WHERE parent_id = '".$parentId."'".$published." AND user_id = '".$userPKId."' ORDER BY ordering";
            }
			return $this->query($sql);
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
	 * Method to return the number of child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return integer The number of records
     * @access public
	 */
    public function getChildNodeCount($parentId, $onlyPublished = TRUE, $noPermissions = TRUE)
    {
		try {
            $published = '';

            if ($onlyPublished) {
                $published = ' AND published = 1';
            }
            if (($noPermissions) || ($this->objUser->isAdmin())) {
                $sql = "SELECT COUNT(*) AS cnt FROM tbl_cms_treenodes"
                      ." WHERE parent_id = '".$parentId."'".$published;
            } else {
                $userId = $this->objUser->userId();
                $userPKId = $this->objUser->PKid($userId);

                $sql =  $sql = "SELECT COUNT(*) AS cnt FROM tbl_cms_treenodes"
                      ." WHERE parent_id = '".$parentId."'".$published;
            }
			$nodeCount = $this->query($sql);
            if (count($nodeCount)){
                return $nodeCount[0]['cnt'];
            }else{
                return 0;
            }
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
	 * Method to return a particular node
     *
	 * @param string $id The id of the node to return
     * @return array The node record from the db
     * @access public
	 */
  	public function getNode($id, $noPermissions = TRUE)
	{
		try {
            if (($noPermissions) || ($this->objUser->isAdmin())) {
                $sql = "SELECT id, title, node_type, link_reference, banner, parent_id, layout, css, ordering, published, publisher_id,artifact_id FROM tbl_cms_treenodes"
                      ." WHERE id = '".$id."'";
            } else {
                $userId = $this->objUser->userId();
                $userPKId = $this->objUser->PKid($userId);

                $sql = "SELECT tbl_cms_treenodes.id, title, node_type, link_reference, banner, parent_id, layout, css, ordering, published, publisher_id,artifact_id FROM "
                      ."(tbl_cms_treenodes INNER JOIN tbl_groupadmin_groupuser ON publisher_id = group_id)"
                      ." WHERE tbl_cms_treenodes.id = '".$id."' AND user_id = '".$userPKId."'";
            }
			return $this->query($sql);
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
	}

    /**
     * Method to update the order field of a node
     *
     * @access public
     * @return bool
     */
    public function updateOrder($id, $ordering)
    {
        $newArr = array(
                      'ordering' => $ordering
                  );

        $newId = $this->update('id', $id, $newArr);
        return $newId;
    }

    /**
	 * Method to move a node up in the ordering
     *
	 * @param string $menuNode The node to move
     * @access public
	 */
    public function moveNodeUp($menuNode)
    {
        $orderNum = $this->getPrevOrderNum($menuNode);
        $id = $this->updateOrder($menuNode, $orderNum);
        return $id;
    }

    /**
	 * Method to move a node down in the ordering
     *
	 * @param string $menuNode The node to move
     * @access public
	 */
    public function moveNodeDown($menuNode)
    {
        $orderNum = $this->getNextOrderNum($menuNode);
        $id = $this->updateOrder($menuNode, $orderNum);
        return $id;
    }

    /**
	 * Method to get the next ordering number
     *
	 * @param string $afterNode Node to insert after
     * @access public
	 */
    public function getNextOrderNum($currentNode, $orderIncrement = 10000)
    {
        $orderNum = 10000;
        $node = $this->getNode($currentNode);
        $siblings = $this->getChildNodes($node[0]['parent_id']);

        for ($i = 0; $i < count($siblings); $i++) {
            if ($siblings[$i]['id'] == $currentNode){
                if (isset($siblings[$i + 1])) {
                    if (isset($siblings[$i + 2])) {
                        $orderNum = (int) (($siblings[$i + 1]['ordering'] + $siblings[$i + 2]['ordering']) / 2);
                    } else {
                        $orderNum = (int) ($siblings[$i + 1]['ordering'] + $orderIncrement);
                    }
                } else {
                    $orderNum = $node['ordering'];
                }
                break;
            }
        }
        return $orderNum;
    }

    /**
	 * Method to get the previous ordering number
     *
	 * @param string $beforeNode Node to insert before
     * @access public
	 */
    public function getPrevOrderNum($currentNode)
    {
        $orderNum = 10000;
        $node = $this->getNode($currentNode);
        $siblings = $this->getChildNodes($node[0]['parent_id']);

        for ($i = 0; $i < count($siblings); $i++) {
            if ($siblings[$i]['id'] == $currentNode){
                if (isset($siblings[$i - 1])) {
                    if (isset($siblings[$i - 2])) {
                        $orderNum = (int) (($siblings[$i - 1]['ordering'] + $siblings[$i - 2]['ordering']) / 2);
                    } else {
                        $orderNum = (int) ($siblings[$i - 1]['ordering'] / 2);
                    }
                } else {
                    $orderNum = $node['ordering'];
                }
                break;
            }
        }
        return $orderNum;
    }
    
    public function getArtifact($id){
    	$result = $this->getRow('artifact_id', $id);
    	if ($result==null) {
    		return FALSE;
    	}else {
    		return $result['id'];
    	}
    
    }
    
    public function getRootNodes(){
    	$result = $this->getAll("WHERE node_type=0");
    	return $result;
    }

    /**
	 * Method to get the new ordering number for a particular parent id
     *
	 * @param string $parentNode Parent node of order to get
     * @access public
	 */
    public function getNewOrderNum($parentNode, $orderIncrement = 10000)
    {
        $orderNum = 10000;
		try {
            $sql = "SELECT ordering FROM tbl_cms_treenodes"
                  ." WHERE parent_id = '".$parentNode."' ORDER BY ordering DESC LIMIT 1";
			$order = $this->query($sql);
            if (count($order)) {
                $orderNum = $order[0]['ordering'] + $orderIncrement;
            } else {
                $orderNum = $orderIncrement;
            }
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
        return $orderNum;
    }
     /**
     * Method to recursively delete nodes
     *
     * @access public
     * @return bool
     */
    public function deleteWithChildren($id)
    {
        if ($this->getChildNodeCount($id, FALSE, TRUE)) {
            $childNodes = $this->getChildNodes($id, FALSE, TRUE);
            foreach ($childNodes as $node) {
                $this->deleteWithChildren($node['id']);
            }
        }
        return $this->delete('id', $id);
    }
}

?>
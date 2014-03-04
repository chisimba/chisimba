<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the sectiongroup table for publicportal module.
*
* @package publicportal
* @category sems
* @copyright AVOIR
* @license GNU GPL
* @author Serge Meunier
*/

class dbsectiongroup extends dbTable
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
            parent::init('tbl_cms_sectiongroup');
			
			$this->_objSecurity = & $this->newObject('dbsecurity', 'cmsadmin');
            $this->objUser = & $this->newObject('user', 'security');

       } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
       }
    }


     /**
	 * Method to return the child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return array The array of child nodes
     * @access public
	 */
    public function getChildNodes($parentId, $admin = FALSE) //$noPermissions)
    {
        try {
            
            if($admin){
                $sql = "SELECT * FROM tbl_cms_sections 
                    WHERE parentid = '{$parentId}' AND trash = 0
                    ORDER BY ordering";
            }else{
                $sql = "SELECT * FROM tbl_cms_sections 
                    WHERE parentid = '{$parentId}' AND trash = 0 AND published = 1 
                    ORDER BY ordering";
            }
            
            $data = $this->getArray($sql);

			$secureData = array();
            foreach ($data as $d) {
                if ($this->_objSecurity->canUserReadSection($d['id'])){
                    array_push($secureData, $d);
                }
            }

            return $secureData;
            
        }catch(Exception $e){
            throw customException($e->getMessage());
            exit();
        }
        return '';
        
    }

    /**
	 * Method to return the number of child nodes for a particular node id
     *
	 * @param string $parentId The id of the parent node
     * @param boolean $onlyPublished Flag as to whether to show only published menu nodes or not
     * @return integer The number of records
     * @access public
	 */
    public function getChildNodeCount($parentId, $noPermissions)
    {
		try {
						
			if ($noPermissions != TRUE) {
                $sql = "SELECT COUNT(*) AS cnt FROM "
                      . "tbl_cms_sections "
                      ." WHERE parentid = '".$parentId."'";
            } else {
                $userId = $this->objUser->userId();
                $userPKId = $this->objUser->PKid($userId);

                $sql = "SELECT COUNT(*) AS cnt FROM "
                      . "(tbl_cms_sections INNER JOIN tbl_cms_sectiongroup ON tbl_cms_sections.id = tbl_cms_sectiongroup.section_id"
                      ." INNER JOIN tbl_groupadmin_groupuser ON tbl_cms_sectiongroup.group_id = tbl_groupadmin_groupuser.group_id)"
                      ." WHERE parentid = '".$parentId."' AND user_id = '".$userPKId."'";
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
  	public function getNode($id, $admin = FALSE) //$noPermissions = TRUE)
	{
	   try {
	       $sql = "SELECT * FROM tbl_cms_sections WHERE id = '{$id}'";
	       
	       $data = $this->getArray($sql);
	       if(!empty($data)){
	           return $data[0];
	       }
	       return $data;
	       
	   }catch(Exception $e) {
	       throw customException($e->getMessage());
	       exit();
	   }
	   return '';
	   
	}


	 /**
	 * Method to return the group_id for a particular section id
     *
	 * @param string $sectionId The id of the section
     * @return boolean The array of child nodes
     * @access public
	 */
    public function getGroupBySection($sectionId)
    {
		try {

                $sql = "SELECT group_id from tbl_cms_sectiongroup WHERE section_id = '".$sectionId."'";

				$result=$this->query($sql);

			if($result){
                return $result[0]['group_id'];
            } else {
                return FALSE;
            }
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }


	/**
	 * Method to return the section_id for a particular group
     *
	 * @param string $sectionId The id of the section
     * @return boolean or id of section
     * @access public
	 */
    public function getSectionByGroup($groupId)
    {
		try {

                $sql = "SELECT section_id from tbl_cms_sectiongroup WHERE group_id = '".$groupId."'";

				$result=$this->query($sql);

			if($result){
                return $result[0]['section_id'];
            } else {
                return FALSE;
            }
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }


	public function add($sectionId,$groupId)
    {
        $newArr = array(
                      'section_id' => $sectionId ,
                      'group_id' => $groupId
                  );

        $newId = $this->insert($newArr);
        return $newId;
    }

	public function edit($id,$sectionId,$groupId)
    {
        $newArr = array(
                      'section_id' => $sectionId ,
                      'group_id' => $groupId
                  );

        $newId = $this->update('id', $id, $newArr);
        return $newId;
    }

	public function getSectionGroupId($sectionId)
    {
		try {

                $sql = "SELECT id from tbl_cms_sectiongroup WHERE section_id = '".$sectionId."'";

				$result=$this->query($sql);

			if($result){
                return $result[0]['id'];
            } else {
                return FALSE;
            }
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

	public function getGroupNameBySection($sectionId)
    {
		try {

                $sql = "SELECT g.name 
				from tbl_cms_sectiongroup a,
				tbl_groupadmin_group g
				WHERE g.id=a.group_id
				AND a.section_id = '".$sectionId."'";

				$result=$this->query($sql);

			if($result){
                return $result[0]['name'];
            } else {
                return FALSE;
            }
		} catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
}

?>

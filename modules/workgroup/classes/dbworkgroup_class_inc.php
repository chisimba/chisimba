<?php

/* ----------- data class extends dbTable for tbl_blog------------ */// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Model class for the table tbl_faq
 * @author Jeremy O'Connor, Juliet Mulindwa
 * @copyright 2004 University of the Western Cape
 */
class dbWorkgroup extends dbTable {

    /**
     * Constructor method to define the table
     */
    function init() {
        parent::init('tbl_workgroup');
        //$this->USE_PREPARED_STATEMENTS=True;
        $this->objUser = &$this->newObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Sets the workgroupId session variable
     * @param string The workgroup ID
     */
    function setWorkgroupId($workgroupId) {
        $this->setSession('workgroupId', $workgroupId);
    }

    /**
     * Return the workgroupId session variable
     * @return string The workgroup ID
     */
    function getWorkgroupId() {
        return $this->getSession('workgroupId', NULL);
    }

    /**
     * Unsets the workgroupId session variable
     * @param string The workgroup ID
     */
    function unsetWorkgroupId() {
        $this->unsetSession('workgroupId');
    }

    /**
     * Return the ID for a workgroup given its description.
     * @param string The description of the workgroup
     * @return string The ID of the workgroup or false if the workgroup was not found
     */
    function getId($description) {
        $sql = "SELECT id FROM $this->_tableName WHERE description = '$description'";
        $list = $this->getArray($sql);
        if (empty($list)) {
            return false;
        } else {
            return $list[0]['id'];
        }
        //return $this->getRow("id", $id);
    }

    /**
     * Return the workgroup description
     * @param string The id of the workgroup
     * @return string The workgroup description
     */
    function getDescription($id) {
        $list = $this->listSingle($id);
        if (empty($list)) {
            return "";
        } else {
            return $list[0]['description'];
        }
    }

    /**
     * Return all records
     * @param string The context code
     * @return array Workgroups for a context
     */
    function getAll($contextCode) {
        if ($contextCode == NULL) {
            return parent::getAll("WHERE contextcode IS NULL ORDER BY description");
        } else {
            return parent::getAll("WHERE contextcode='" . $contextCode . "' ORDER BY description");
        }
    }

    /**
     * Return all records for a user
     * @param string The context code
     * @return array Workgroups for a context
     */
    function getAllForUser($contextCode, $userId) {
        $sql = "SELECT 
            {$this->_tableName}.id,
            {$this->_tableName}.description
            FROM {$this->_tableName}, tbl_workgroup_users
            WHERE {$this->_tableName}.id = tbl_workgroup_users.workgroupid";
        if ($contextCode == NULL) {
            $sql .= " AND {$this->_tableName}.contextcode IS NULL";
        } else {
            $sql .= " AND {$this->_tableName}.contextcode = '$contextCode'";
        }
        $sql .=
                " AND tbl_workgroup_users.userid = '$userId'
            ORDER BY {$this->_tableName}.description";
        return $this->getArray($sql);
    }

    /**
     * Return all records
     * @param string The context code
     * @return array Workgroups for a context
     */
    function listAll($contextCode) {
        //$sql = "SELECT id, question, answer FROM tbl_faq";
        //return $this->getArray($sql);
        if ($contextCode == NULL) {
            return parent::getAll("WHERE contextcode IS NULL ORDER BY description");
        } else {
            return parent::getAll("WHERE contextcode='" . $contextCode . "' ORDER BY description");
        }
    }

    /**
     * Return a single record
     * @param string The id of the workgroup
     * @return array The workgroup
     */
    function listSingle($id) {
        $sql = "SELECT * FROM $this->_tableName WHERE id = '" . $id . "'";
        return $this->getArray($sql);
        //return $this->getRow("id", $id);
    }

    /**
     * Insert a record
     * @param string The context code
     * @param string The description
     * @return string The ID
     */
    function insertSingle($contextCode, $description) {
        return $this->insert(array(
            'contextcode' => $contextCode,
            'description' => $description,
            'creatorid' => $this->userId,
        ));
    }

    /**
     * Update a record
     * @param string The workgroup id
     * @param string The description
     */
    function updateSingle($id, $description) {
        $this->update("id", $id,
                array(
                    'description' => $description
                )
        );
    }

    /**
     * Delete a record
     * @param string The workgroup id
     */
    function deleteSingle($id) {
        $this->delete("id", $id);
    }

    /**
     * Insert a file.
     * @param string $contextCode Context Code
     * @param string $workgroupId Workgroup ID
     * @param string $filename
     * @param string $filetype
     * @param string $filesize
     * @param string $path The full filesystem path to the file
     * @param string $title
     * @param string $description
     * @param string $version
     */
    /* public function insertFile(
      $fileid,
      //$contextCode,
      $workgroupId,
      $userId,
      $filename,
      //$filetype,
      //$filesize,
      $path,
      $title,
      $description,
      $version
      )
      {
      //if (preg_match('/^.*\.(.*)$/i',$filename,$matches)) {
      //	if (strtolower( $matches[1] )=='php')
      //    	return;
      //}
      //$filename = preg_replace('/^(.*)\.php$/i', '\\1.phps', $filename);
      $sql=array(
      'fileid'=>$fileid,
      //'contextCode'=>$contextCode,
      'workgroupid'=>$workgroupId,
      'userid'=>$userId,
      'filename'=>$filename,
      //'filetype'=>$filetype,
      //'filesize'=>$filesize,
      'path'=>$path,
      'title'=>$title,
      'description'=>$description,
      'version'=>$version,
      'uploadtime'=>mktime()
      );
      $this->insert($sql);
      } */

    /**
     * Upload a file onto the filesystem and into the database.
     * @param string $contextCode Context Code
     * @param string $workgroupId Workgroup ID
     * @param string $title Title
     * @param string $description Description
     * @param string $version Version
     * @return boolean
     */
    /* public function uploadFile(
      //$contextCode,
      $workgroupId,
      $userId,
      $path,
      $title,
      $description,
      $version
      )
      {
      $this->objUpload =& $this->getObject('upload','filemanager');
      $result = $this->objUpload->uploadFile('upload');
      if ($result['success']=='1') {
      $this->insertFile(
      $result['fileid'],
      //$contextCode,
      $workgroupId,
      $userId,
      $result['name'],
      //$filetype,
      //$filesize,
      $path,
      $title,
      $description,
      $version
      );

      }

      } */
}

?>
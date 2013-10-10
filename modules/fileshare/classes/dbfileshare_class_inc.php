<?php
/**
* Manage workgroup files.
* @copyright (C) 2004,2005,2006 UWC
* @license GNU GPL
* @author James Scoble
* @author Jeremy O'Connor
*/

class dbfileshare extends dbtable
{

    public $objConfig;
    //var $objUser;

    public function init()
    {
         parent::init('tbl_fileshare');
         $this->objConfig=$this->getObject('altconfig','config');
         //$this->objUser=$this->getObject('user','security');
    }

    /**
    * Method to list files.
    * @param string $contextCode Context Code
	* @param string $workgroupId Workgroup ID
    */
    public function listAll($contextCode, $workgroupId)
    {
		$sql = "SELECT {$this->_tableName}.*, tbl_files.path
		FROM {$this->_tableName}, tbl_files
		WHERE {$this->_tableName}.fileid = tbl_files.id
		ORDER BY {$this->_tableName}.filename";
		return $this->getArray($sql);
//        return $this->getAll("WHERE 
//			contextcode = '$contextCode' 
//			AND workgroupid = '$workgroupId'
//		");
  	}

    /**
    * Method to list a singe file.
    * @param string $id File ID
    */
    public function listSingle($id)
    {
        return $this->getAll("WHERE id = '$id'");
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
    public function insertFile(
		$fileid,
		$contextCode,
		$workgroupId,
		$filename,
		//$filetype,
		//$filesize,
		//$path,
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
			'contextCode'=>$contextCode,
			'workgroupid'=>$workgroupId,
			'filename'=>$filename,
			//'filetype'=>$filetype,
			//'filesize'=>$filesize,
			//'path'=>$path,
			'title'=>$title,
			'description'=>$description,
			'version'=>$version,
			'uploadtime'=>mktime()
		);
        $this->insert($sql);
    }

    /**
    * Update a file.
    * @param string $id File ID
	* @param string $title
	* @param string $description
	* @param string $version
	*/
	public function updateFile($id,$title,$description,$version)
	{
		$this->update(
			'id', $id,
			array(
				'title'=>$title,
				'description'=>$description,
				'version'=>$version
			)
		);
	}

    /**
    * Delete a file.
    * @param string $id File ID
    */
    public function deleteFile($id)
    {
		/*
		$records = $this->listSingle($id);
		$record = $records[0];
		@unlink($record['path']);
        $this->delete('id',$id);
		*/
    }

    /**
    * Upload a file onto the filesystem and into the database.
    * @param string $contextCode Context Code
    * @param string $workgroupId Workgroup ID
	* @param string $title Title
	* @param string $description Description
	* @param string $version Version
    * @return boolean 
    */
    public function uploadFile(
		$contextCode,
		$workgroupId,
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
				$contextCode,
				$workgroupId,
				$result['name'],
				//$filetype,
				//$filesize,
				//$path,
				$title,
				$description,
				$version
			);
		    
		}
		
	}
	/*
    public function uploadFile(
		$contextCode,
		$workgroupId,
		$title,
		$description,
		$version
	)
    {
        $objFileUpload =& $this->newObject('fileupload','utilities');
		try {
            //$objConfig =& $this->getObject('config', 'config');
            //$siteRoot = $objConfig->siteRoot();
            //$userfiles = $objConfig->userfiles();
            $siteRootPath = $this->objConfig->getsiteRootPath();
            $contentPath = $this->objConfig->getcontentPath();
            $contentPath = substr($contentPath,0,strlen($contentPath)-1);
            //$contextCode = $this->contextCode;
            $type = $_FILES['upload']['type'];
            $pos = strpos($type, "/");
            $type_l = substr($type,0,$pos);
            $type_r = substr($type,$pos+1,255);
            if ($type_l == "image") {
                $filetype = 'images';
            }
            else if ($type_l == "audio" || $type_l == "video") {
                $filetype = 'media';
            }
            else if ($type == 'application/x-ogg' ) {
                $filetype = 'media';
            }
            else if ($type == 'application/x-shockwave-flash' ) {
                $filetype = 'flash';
            }
            else {
                $filetype = 'documents';
            }
			//$dir = "{$siteRootPath}{$contentPath}/content/$contextCode/workgroup/$workgroupId/$filetype";
			$dir = "{$siteRootPath}{$contentPath}";
			$filename = $objFileUpload->upload_file($dir.'/', false, true);
	        $filetype=$_FILES['upload']['type'];
	        $filesize=$_FILES['upload']['size'];
			$path = $dir.'/'.$filename;
            $this->insertFile(
				$contextCode,
				$workgroupId,
				$filename,
				$filetype,
				$filesize,
				$path,
				$title,
				$description,
				$version
			);
			return true;
		}
		catch (CustomException $e) {
			return false;			
		}
    }
	*/
}
?>
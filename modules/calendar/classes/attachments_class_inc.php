<?php
/**
* Handles attachments to events.
*/
class attachments extends object {
	/**
	* Constructor
	*/
	function init()
	{
		$this->objConfig=$this->getObject('altconfig','config');
	    $this->objFileUpload =& $this->newObject('fileupload','utilities');
		// Create directory structure if neccessary
        $siteRootPath = $this->objConfig->getsiteRootPath();
        $contentPath = $this->objConfig->getcontentPath();
        $contentPath = substr($contentPath,0,strlen($contentPath)-1);
		$dir = "{$siteRootPath}{$contentPath}/calendar";
		if (!file_exists($dir)) {
		    //mkdir($dir,0777);
		}
		$dir = "{$siteRootPath}{$contentPath}/calendar/attachments";
		if (!file_exists($dir)) {
		    //mkdir($dir,0777);
		}
	}
	function uploadFile($id)
	{
		try {
            $siteRootPath = $this->objConfig->getsiteRootPath();
            $contentPath = $this->objConfig->getcontentPath();
            $contentPath = substr($contentPath,0,strlen($contentPath)-1);
			$dir = "{$siteRootPath}{$contentPath}/calendar/attachments/$id";
			if (!file_exists($dir)) {
			    mkdir($dir,0777);
			}
			$filename = $this->objFileUpload->upload_file($dir.'/', false, true);
			return true;
		}
		catch (CustomException $e) {
			return false;			
		}
	}
	function listFiles($id)
	{
		$siteRootPath = $this->objConfig->getsiteRootPath();
		$contentPath = $this->objConfig->getcontentPath();
		$contentPath = substr($contentPath,0,strlen($contentPath)-1);
		$dir = "{$siteRootPath}{$contentPath}/calendar/attachments/$id";
		if (!file_exists($dir)) {
		    return array();
		}
		$files = array();
		if ($handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) { 
		        if ($file != "." && $file != ".." && is_file($dir.'/'.$file)) { 
					$files[] = array(
						'filename'=>$file, 
						'path'=>$dir.'/'.$file
					);
		        } 
		    }
		    closedir($handle); 
		}
		return $files;
	}
	function deleteFile($id, $filename)
	{
		$siteRootPath = $this->objConfig->getsiteRootPath();
		$contentPath = $this->objConfig->getcontentPath();
		$contentPath = substr($contentPath,0,strlen($contentPath)-1);
		$path = "{$siteRootPath}{$contentPath}/calendar/attachments/$id/$filename";
		unlink($path);
	}
	function deleteAllFiles($id)
	{
		$siteRootPath = $this->objConfig->getsiteRootPath();
		$contentPath = $this->objConfig->getcontentPath();
		$contentPath = substr($contentPath,0,strlen($contentPath)-1);
		$files = $this->listFiles($id);
		foreach ($files as $file) {
			unlink($file['path']);
		}
		$dir = "{$siteRootPath}{$contentPath}/calendar/attachments/$id";
		if (file_exists($dir)) {
			rmdir($dir);
		}
	}
	function transfer($tempId, $event)
	{
		$siteRootPath = $this->objConfig->getsiteRootPath();
		$contentPath = $this->objConfig->getcontentPath();
		$contentPath = substr($contentPath,0,strlen($contentPath)-1);
		$tempDir = "{$siteRootPath}{$contentPath}/calendar/attachments/$tempId";
		$dir = "{$siteRootPath}{$contentPath}/calendar/attachments/$event";
		if (file_exists($tempDir)) {
			rename($tempDir,$dir);
		}
	}
}

?>
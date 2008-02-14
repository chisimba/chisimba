<?php
class splstdlib extends object
{
	public function init()
	{
		require_once($this->getResourcePath('stdlib_class_inc.php', 'files'));
		//require_once('/var/www/chisimba_framework/app/core_modules/files/resources/stdlib_class_inc.php');
	}

	/**
	 * Create a tree representation of a directory recursively
	 *
	 * @param string $dir
	 * @return string
	 */
	public function dirTree($dir)
	{
		$it = new DirectoryTreeIterator($dir);
		foreach($it as $path) {
			$files[] = $path;
		}

		return $files;
	}

	/**
	 * List a directory omitting the . and .. directories
	 *
	 * @param string $dir
	 * @return array
	 */
	public function dirFilterDots($dir)
	{
		$it = new DirectoryFilterDots($dir);
		foreach($it as $path) {
			if($path->isDir())
			{
				$str = $path->getFilename();
				if($str{0} !== '_')
				{
					$dirs[] = $path->getPath()."/".$path->getFilename();
				}
			}
		}
		if(isset($dirs))
		{
			return $dirs;
		}
		else {
			return NULL;
		}
	}

	/**
	 * recursively list directory contents
	 *
	 * @param string $dir
	 * @param string $type
	 * @return array
	 */
	public function recDir($dir, $type='files')
	{
		$iterator = new RecursiveDirectoryIterator($dir);
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file)
		{
			if ($file->isDir()) {
				$dirs[] = $file->getPathname();
			} else {
				$files[] = $file->getPathname();
			}
		}
		if($type = 'files')
		{
			return $files;
		}
		else {
			return $dirs;
		}
	}

	/**
	 * Use SPL to clean up frontpage directories
	 *
	 * @param string $directory
	 * @param array $filter
	 */
	public function frontPageDirCleaner($directory, $filter = array('_vti_cnf', '_vti_private', '_vti_txt', '_private', '_themes', 'msupdate', 'vti_pvt', 'vti_script', '_vti_log', '_template','Thumbs.db'))
	{
		$iterator = new RecursiveDirectoryIterator($directory);
		foreach(new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file)
		{
			// prune empty dirs
			if(file_exists($file->getFilename()))
			{
				if(sizeof($file->getSize()) == 0)
				{
					unlink($file->getPath());
				}
			}
			if($file->getFileName() == 'Thumbs.db' || $file->getFileName() == 'top_menubar.htm' || $file->getFileName() == 'navigation.htm')
			{
				if(file_exists($file->getPath()."/".$file->getFilename()))
				{
					unlink($file->getPath()."/".$file->getFilename());
				}
			}
			$parts = explode("/",$file->getPath());
			if(in_array(end($parts), $filter))
			{
				if(file_exists($file->getPath()))
				{
					//$this->dirDeleter($file->getPath());
					$this->recursiveRemoveDirectory($file->getPath());
				}
			}
			
		}
	}

	/**
	 * Method to get information about all the files in a directory (recursive)
	 *
	 * @param string $directory
	 * @param array $filter
	 * @return array
	 */
	public function fileCounter($directory, $filter = array('php', 'xsl', 'xml', 'htm', 'html','css'))
	{
		$count_directories = 0;
		$count_files       = 0;
		$count_lines       = 0;
		$count_bytes       = 0;

		$iterator = new RecursiveDirectoryIterator($directory);

		foreach(new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file)
		{
			if(false == $file->isDir())
			{
				if(in_array(end(explode('.', $file->getFileName())), $filter))
				{
					$count_files++;
					$count_bytes += $file->getSize();
					$count_lines += sizeof(explode("n", file_get_contents($file->getPathName())));
				}
			}
			else if(false == strpos($file->getPathname(), 'CVS') && $file->isDir())
			{
				$count_directories++;
			}
		}

		return array('bytes'       => $count_bytes,
		'files'       => $count_files,
		'lines'       => $count_lines,
		'directories' => $count_directories);
	}

	/**
	 * return information about the files in a dir
	 *
	 * @param string $dir
	 * @return array
	 */
	public function fileInformationDir($dir)
	{
		$dir = new SmartDirectoryIterator($dir);
		foreach ( $dir as $file )
		{
			$filearr[] = $file;
		}
		return $filearr;
	}

	/**
	 * Recursively delete a directory and all subdirs
	 *
	 * @param string $dir
	 */
	public function dirDeleter($dir)
	{
		$iterator = new RecursiveDirectoryIterator($dir);
		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file)
		{
			if ($file->isDir()) {
				rmdir($file->getPathname());
				// $this->dirDeleter($file->getPathname());
			} else {
				unlink($file->getPathname());
				@rmdir($dir);
			}
		}
		@rmdir($dir);
	}
	
	/**
	 * Recursively remove a directory
	 *
	 * @param string $path
	 */
	public function recursiveRemoveDirectory($path)
    {   
        	$dir = new RecursiveDirectoryIterator($path);
        	//Remove all files
        	foreach(new RecursiveIteratorIterator($dir) as $file)
        	{
        		if(file_exists($file))
        		{
            		unlink($file);
        		}
        	}
        	//Remove all subdirectories
        	foreach($dir as $subDir)
        	{
            	//If a subdirectory can't be removed, it's because it has subdirectories, so recursiveRemoveDirectory is called again passing the subdirectory as path
            	if(!@rmdir($subDir)) //@ suppress the warning message
            	{
               	 $this->recursiveRemoveDirectory($subDir);
               	 @rmdir($subDir);
               	 @unlink($subDir);
            	}
        	}

        	//Remove main directory
        	@rmdir($path);
    	
    }

    /**
     * Find a file by regex in a directory
     *
     * @param string $path
     * @param string $regex
     * @return array
     */
	public function fileFinder($path, $regex)
	{
		$fileList = new DirMach($path, $regex);
		foreach ($fileList as $file) {
			$match[] = $file;
		}
		return $match;
	}


	/**
	 * List files of a certain extension
	 *
	 * @param string $path
	 * @param string $ext
	 * @return array
	 */
	public function fileExtension($path, $ext)
	{
		$filtered = new ExtensionFilter(
		new DirectoryIterator($path), $ext);

		foreach ( $filtered as $file ) {
			$files[] = $file;
		}
		return $files;
	}
	
	/**
	 * List files in a directory
	 *
	 * @param string $dir
	 * @return array
	 */
	public function fileLister($dir)
	{
		$files = new DirectoryFilterDots($dir."/");
		foreach ($files as $file)
		{
			if($file->isDir())
			{
				continue;
			}
			else {
				$file = $file->getFilename();
				$ret[] = $file;
			}
		}
		if(isset($ret))
		{
			return $ret;
		}
		else {
			$ret = NULL;
		}
	}
	
}
?>
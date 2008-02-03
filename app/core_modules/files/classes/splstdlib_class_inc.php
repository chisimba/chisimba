<?php

class splstdlib extends object
{
	public function init()
	{
		require_once($this->getResourcePath('stdlib_class_inc.php', 'files'));
	}

	public function dirTree($dir)
	{
		$it = new DirectoryTreeIterator($dir);
		foreach($it as $path) {
			$files[] = $path;
		}

		return $files;
	}
	
	public function dirFilterDots($dir)
	{
		$it = new DirectoryFilterDots($dir);
		foreach($it as $path) {
			$files[] = $path;
		}
		
		return $files;
	}

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
	
	public function fileInformationDir($dir)
	{
		$dir = new SmartDirectoryIterator($dir);
		foreach ( $dir as $file ) 
		{
    		$filearr[] = $file;
		}
		return $filearr;
	}
	
	public function dirDeleter($dir)
	{
   		$iterator = new RecursiveDirectoryIterator($dir);
   		foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file)
   		{
      		if ($file->isDir()) {
         		rmdir($file->getPathname());
      		} else {
         		unlink($file->getPathname());
      		}
   		}
   		rmdir($dir);
	}
	
	public function fileFinder($path, $regex)
	{
		$fileList = new DirMach($path, $regex);
		foreach ($fileList as $file) {
  			$match[] = $file;
		}
		return $match;
	}
	
	
	public function fileExtension($path, $ext)
	{
		$filtered = new ExtensionFilter(
                new DirectoryIterator($path), $ext);

		foreach ( $filtered as $file ) {
    		// Do something with $file
		}
	}
}
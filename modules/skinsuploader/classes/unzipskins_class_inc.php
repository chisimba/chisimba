<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class unzipskins that manages 
 * the extraction of skin content 
 * @package unzipskins
 * @category context
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version 1.0
 * @author Jarrett L. Jordaan
 */

class unzipskins extends dbTable 
{
	/**
	 * File handler
	 *
	 * @var object
	*/
	public $objConf;

	function init()
	{
		$this->objConf = & $this->getObject('altconfig','config');
	}

	/**
	 * Executes all needed functions
	 * And performs necessary error checking
	*/
	function doAll($FILES)
	{
		//Retrieve site root directory
		$siteRoot = $this->objConf->getsiteRootPath();
		//Retrieve skins directory
		$skinRoot = $this->objConf->getskinRoot();
		$location = $siteRoot.$skinRoot;
		//Unzip files to /tmp directory
		$folder = $this->uploadZipFile($FILES);
		//Store the first directory
		$foldertemp[0] = $folder;
		//Check for errors
		if($folder == "error")
		{
			return "error";
		}
		//Retrieve folder paths
		$folderLocations = $this->list_dir($folder,1);
		//Check for errors
		if($folderLocations == "error")
		{
			return "error";
		}
		//Add the first directory
		$folderLocations = array_merge($foldertemp,$folderLocations);
		//Retrieve file paths
		$filesLocation = $this->list_dir_files($folder,1);
		//Check for errors
		if($filesLocation == "error")
		{
			return "error";
		}
		//Move folders and files to new location
		$movedFiles = $this->move_Files($folderLocations, $filesLocation, $location);
		return $movedFiles;
	}

	/**
	 * Extracts zip file to /tmp directory
	 * @param array $FILES - $_FILES global variable
	 * @return $folder - Location of extacted folder inside /tmp directory
	*/
	function uploadZipFile($FILES)
	{
		if (isset($FILES['upload']))
		{
			if(!is_uploaded_file($FILES['upload']['tmp_name']))
			{
			echo "error uploading file";
			}
			else if ($FILES['upload']['error'] != UPLOAD_ERR_OK)
			{
				echo "upload error ok";
			}
			else
			{
				$type = $FILES['upload']['type'];
				$name = $FILES['upload']['name'];
				$name = preg_replace('/^(.*)\.php$/i', '\\1.phps', $name);
                    		for ($i=0;$i<strlen($name);$i++) 
				{
                        		if ($name{$i} == ' ') 
					{
                            			$name{$i} = '_';
                        		}
                    		}
				$extension = "";
                    		$len = strlen($name);
                    		$i = $len-1;
                    		while($i >= 0 && $name[$i]!='.')
				{
                        		$extension = $name[$i].$extension;
                        		$i--;
                    		}
				$j = 0;
				$newname = "";
				while($len > $j && $name[$j]!='.')
				{
					$newname .= $name[$j];
					$j++;
				}
				$name = $newname;
				$size = $FILES['file']['size'];
				if ($extension == 'zip')
				{
					$tempfile=$FILES['upload']['tmp_name'];
					$tempdir=substr($tempfile,0,strrpos($tempfile,'/'));
					$objDir=&$this->getObject('dircreate','utilities');
					$objDir->makeFolder("$name.unzip",$tempdir);
					$folder="$tempdir/$name";
					$objWZip=&$this->getObject('wzip','utilities');
					$objWZip->unzip($tempfile,$folder);
				}
				else
				{
				$folder = "error";
				}
			}
		}
	return $folder;
	}

	/**
	 * Scans a specified folder and returns all folder Locations
	 * @param string $dir - Location of folder to scan
	 * @param int $bool - 0 to return folder names and 1 to return folder Locations
	 * @return array $dir_list
	*/
	public function list_dir($dir, $bool) 
	{
		$dir_list = '';
		$stack[] = $dir;
		$i = 0;
		while ($stack) 
		{
           		$current_dir = array_pop($stack);
           		if ($dh = opendir($current_dir)) 
			{
               			while (($file = readdir($dh))) 
				{
				
                 			if ($file !== '.' AND $file !== '..' AND is_dir("{$current_dir}/$file")) 
					{
						$current_file = "{$current_dir}/{$file}";
						if($bool == "0")
							$dir_list[$i]=$file;
						else
							$dir_list[$i]="$current_file";
						$stack[] = $current_file;
						$i++;
        		           	}
				
               			}
			closedir($dh);
			}
		}
		return $dir_list;
	}

	/**
	 * Scans a specified folder and returns all file Locations
	 * @param string $dir - Location of folder to scan
	 * @param int $bool - 0 to return filenames and 1 to return file Locations
	 * @return array $file_list
	*/
	public function list_dir_files($dir, $bool) 
	{
		$file_list = '';
		$stack[] = $dir;
		while ($stack) 
		{
			$current_dir = array_pop($stack);
			if ($dh = opendir($current_dir)) 
			{
				while (($file = readdir($dh)) !== false) 
				{
                   			if ($file !== '.' AND $file !== '..') 
					{
						$current_file = "{$current_dir}/{$file}";
						if (is_file($current_file)) 
						{
							if($bool == 0)
        	                   				$file_list[] = $file;
                	          			else
                        	   				$file_list[] = "{$current_dir}/{$file}";
                       				}
						elseif (is_dir($current_file)) 
						{
                           				$stack[] = $current_file;
                       				}
                   			}
               			}
           		}
       		}
	return $file_list;
   	}

	/**
	 * Moves directory and all content to specified location
	 * @param array $foldersLocation - List of all folder locations
	 * @param array $filesLocation - List of all file locations
	 * @param string $newLocation - Path to new directory
	 * @return NULL
	*/
	public function move_Files($foldersLocation, $filesLocation, $newLocation)
	{
		for($i=0;$i<count($foldersLocation);$i++)
		{
			//Remove /tmp from folder location
			$foldersLocation[$i] = str_replace("/tmp/","", $foldersLocation[$i]);
			//Modify directory path
			$filepath = $newLocation.$foldersLocation[$i];
			//Remove contents of the folder
			if(file_exists($filepath))
			{
					$this->recursive_remove_directory($filepath, TRUE);
					//Remove the folder itself
					rmdir($filepath);
					//Create directory
					mkdir($filepath);
					chmod($filepath,0777);
			}
			else
			{
			//Create directory
			if(mkdir($filepath))
			{
				
			}
			else
			{
				return "filewriterror";
			}
			//mkdir($filepath);
			chmod($filepath,0777);
			}
		}

		for($i=0;$i<count($filesLocation);$i++)
		{
			$contentsOfFile = file_get_contents($filesLocation[$i]);
			$filesLocation[$i] = str_replace("/tmp/","", $filesLocation[$i]);
			$filepath = $newLocation.$filesLocation[$i];
			$fp = fopen($filepath,'w');
			fwrite($fp,$contentsOfFile);
			fclose($fp);
		}
	return "success";
	}

/*
 ------------ lixlpixel recursive PHP functions -------------
 recursive_remove_directory( directory to delete, empty )
 expects path to directory and optional TRUE / FALSE to empty
 of course PHP has to have the rights to delete the directory
 you specify and all files and folders inside the directory
 ------------------------------------------------------------

 to use this function to totally remove a directory, write:
 recursive_remove_directory('path/to/directory/to/delete');

 to use this function to empty a directory, write:
 recursive_remove_directory('path/to/full_directory',TRUE);
*/
public function recursive_remove_directory($directory, $empty=FALSE)
{
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... else if the path is readable
	}else{

		// we open the directory
		$handle = opendir($directory);

		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;

				// if the new path is a directory
				if(is_dir($path)) 
				{
					// we call this function with the new path
					$this->recursive_remove_directory($path);

				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if($empty == FALSE)
		{
			// try to delete the now empty directory
			if(!rmdir($directory))
			{
				// return false if not possible
				return FALSE;
			}
		}
		// return success
		return TRUE;
	}
}

}
?>
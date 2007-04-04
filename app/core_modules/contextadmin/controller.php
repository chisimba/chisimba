<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

 /**
 * Class to access the ContextModules Tables 
 * @package context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Wesley  Nitsckie
 * @version $Id$ 
 **/
class contextadmin extends controller 
{
   /*
    * @var object objExportContent
    */
    public $objExportContent;

    /**
     * Import object
     *
     * @var object
     */
    public $objImport;
 
     /**
     * File handler
     *
     * @var object
     */
    public $objConf;
    
    /**
     * The constructor
     */
    public function init()
    {
        
        $this->_objDBContext = & $this->newObject('dbcontext', 'context');
        $this->_objDBContextModules = & $this->newObject('dbcontextmodules', 'context');
        $this->_objUser = & $this->newObject('user', 'security');
        $this->_objLanguage = & $this->newObject('language', 'language');
        //$this->_objUtilsContent = & $this->newObject('utils', 'contextpostlogin');
        $this->_objUtils = & $this->newObject('utils', 'contextadmin');
        $this->_objDBContextParams = & $this->newObject('dbcontextparams', 'context');
	     $this->objExportContent = & $this->newObject('export','contextadmin');
        $this->objConf = &$this->getObject('altconfig','config');
        
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        $this->isBlog = FALSE;
        if($this->objModules->checkIfRegistered('blog')){
            $this->objImport = $this->getObject('blogimporter','blog');
            $this->isBlog = TRUE;
        }
    }
    
    
    /**
     * The standard dispatch function
     */
    public function dispatch()
    {
        $action = $this->getParam('action');
        
        switch ($action)
        {
        	
            case '':
	        case 'default':
	            $this->setLayoutTemplate('main_layout_tpl.php');
	            $this->setVar('contextList', $this->_objUtils->getContextList());
	            $this->setVar('otherCourses', $this->_objUtils->getOtherContextList());
	            $this->setVar('filter', $this->_objUtils->getFilterList($this->_objUtils->getContextList()));
	            return 'main_tpl.php';
                
            //the following cases deals with adding a context
            
            //use a layout template for this wizard

            case 'addstep1':
                $this->setLayoutTemplate('layout_tpl.php');
                $this->setVar('error', $this->getParam('error'));
                
                return 'addstep1_tpl.php';
            case 'savestep1':
                 if($this->_objDBContext->createContext() != FALSE)
                 {
                     $this->_objDBContext->joinContext($this->getParam('contextcode'));
                     
                     return $this->nextAction('addstep2');
                 } else {
                     return $this->nextAction('addstep1', array('error' => $this->_objLanguage->languageText("mod_context_error_createcontext",'context') ));
                 }
            case 'addstep2':
                $this->setLayoutTemplate('layout_tpl.php');
                return 'addstep2_tpl.php';
            case 'savestep2':
                $this->_objDBContext->update('contextcode', $this->_objDBContext->getContextCode(), array('about' => $this->getParam('about')));
                return $this->nextAction('addstep3');
            case 'addstep3':
                $this->setLayoutTemplate('layout_tpl.php');
                return 'addstep3_tpl.php';
                
            case 'savestep3':
                $this->_objDBContextModules->save();
                $this->_objDBContext->setLastUpdated();
                return $this->nextAction('default');
           
                
            //the next steps deals with actions coming from the 
            //config page         
            case 'saveedit';
            	$this->_objDBContext->saveEdit();
            	 return $this->nextAction('default');
           	case 'saveaboutedit';
            	$this->_objDBContext->saveAboutEdit();
            	 return $this->nextAction('default');
            case 'savedefaultmod':
            	//if($this->getParam('defaultmodule') != '')
            	//{
            		$this->_objDBContextParams->setParam($this->_objDBContext->getContextCode(), 'defaultmodule',$this->getParam('defaultmodule'));
            	//}
            	return $this->nextAction('default');
           	case 'admincontext':
           		$this->_objDBContext->joinContext($this->getParam('contextcode'));
           		return $this->nextAction('default');
	/**
	Author : Jarrett Jordaan
	Update : Import and Export of IMS Content
	*/
      case 'importcourse' :
      /**
      Reads All course data from old database(nextgen)
      And passes it to import template
      Only courses are displayed
      Need to make provisions for courses which already exist
      */
          if($this->isBlog){
    			$this->setLayoutTemplate('importcoures_tpl.php');
    			$this->setVar('dbData',$this->accessCourseList("SELECT * from tbl_context"));
    			return 'importcourse_tpl.php';
          }
          return $this->nextAction('');
		case 'submitcourse':
		/**
		Reads course data specific to choice from dropdown
		And passes it to viewcourse template
		All information is displayed
		Need to make provision for editing
		Need to extract information without another database access 
		*/
		if($this->isBlog){
			$this->setLayoutTemplate('viewcoures_tpl.php');		
			$choice = $this->getParam('blah');
		   $this->setVar('dbData',$this->accessCourseList("SELECT * from tbl_context where contextcode = '$choice'"));
			return 'viewcourse_tpl.php';
		}
		return $this->nextAction('');
		
		case 'passcourse':
		/**
		All data is retrieved from view template and passed to the new database
		*/
			$this->setLayoutTemplate('passcourse_tpl.php');
			$this->_objDBContext->createContext();
			$this->_objDBContext->joinContext($this->getParam('contextcode'));
			return 'passcourse_tpl.php';
		case 'createzip':
		/**
		Dud case just called to instantiate the exporttoxml function
		Which in turn creates the imsmanifest file and zips all documents
		*/
			$this->setLayoutTemplate('createzip_tpl.php');
			return 'createzip_tpl.php';
		case 'exporttoxml':
		/**
		Creates IMS manifest file
		Creates Zip file and packages all files into zip
		*/
			$this->setLayoutTemplate('exporttoxml_tpl.php');
			//Get context of course we are in
			$context = $this->_objDBContext->getContextCode();
			//Retrieve base path
			$basepath = $this->objConf->getcontentBasePath();
			$filepath = $basepath."/content/".$context;
			$imsfilepath = $basepath."/content/".$context."imsmanifest.xml";
			//Get all directories in context
			$dirlist = $this->list_dir($filepath, 0);
			//Get all files starting in root directory
			$filelist = $this->list_dir_files($filepath, 0);
			//Get all files starting in root directory
			$filelistrel = $this->list_dir_files($filepath, 1);
			//Instantiate
         $this->objExportContent->doXMLExport($context, $filelist, $dirlist, $imsfilepath);
			//Retrieve context code
         $context = $this->_objDBContext->getContextCode();
         //Set context code in export template 
			$this->setVar('context',$context);
			//Create a single directory for all files
			//Check if directory exists and remove it 
			if(file_exists($basepath.$context))
			{
				//Remove contents of the folder
				$this->recursive_remove_directory($basepath.$context, TRUE);
				//Remove the folder itself
				rmdir($basepath.$context);
			}
			//Create new folder
			mkdir($basepath.$context,0700);
			//Write files to new directory
			foreach($filelist as $file)
			{
				//echo $file."<br />";
				//echo file_get_contents($file)."<br />";
			}
			for($i=0;$i<count($filelist);$i++)
			{
				//echo $file."<br />";
				//$filepath = $basepath.$context.$filelist[$i];
				//echo $filepath;
				//echo $basepath.$context.$filelist[$i];
				//$contentsOfFile = file_get_contents($filelistrel[$i]);
				//$fp = fopen($filepath,'w');
				//fwrite($fp,$contentsOfFile);
				//fclose($fp);
			}
         return 'exporttoxml_tpl.php';
		case 'importzip':
			/** 
			Retrieves zip file from old database.
			Writes it to new database directory
			*/
			//Get context of course we are in
			$context = $this->_objDBContext->getContextCode();
			//Set path to module within context
			$path = "/opt/lampp/htdocs/nextgen/usrfiles/exportedimscontext/".$context."-ims.zip";
			$contentsOfZip = file_get_contents($path);
			//Path to new location
			$filePath = "/opt/lampp/htdocs/chisimba_framework/app/usrfiles/exportedimscontext/".$context."-ims.zip";
			//Create the zip file
			$fp = fopen($filePath,'w');
			//Write to file
			fwrite($fp,$contentsOfZip);
			//Close the file
			fclose($fp);
			die('kewl');
		default:
			return $this->nextAction(null);
        }
    }
    
    public function accessCourseList($filter)
	{
		$dsn = "localhost";
		$table = "tbl_context";
		//$filter = "SELECT * from tbl_context";
		//set up to connect to the server
		$dsn1 = $this->objImport->setup($dsn);
		//connect to the remote db
		$dbobj = $this->objImport->_dbObject();
		$datas = $this->objImport->queryTable($table,$filter);
		return $datas;
	}

	 /**
     * Method to load an HTML element's class.
     * @param string $name The name of the element
     * @return The element object
     */
     public function loadHTMLElement($name)
     {
         return $this->loadClass($name, 'htmlelements');
     }
 
    /**
     * Method to get the left widget
     * @return string
     * 
     */
    public function getLeftWidgets()
    {
    	
    	$str = $this->_objUtils->getLeftContent();
    	
    	return $str;
    }
    
    
    
    /**
     * Method to get right left widget
     * @return string
     * 
     */
    public function getRightWidgets()
    {
    	
    	$str = $this->_objUtils->getRightContent();
    	
    	return $str;
    }

	public function list_dir($dir, $bool) {
       $dir_list = '';
       $stack[] = $dir;
       while ($stack) {
           $current_dir = array_pop($stack);
           if ($dh = opendir($current_dir)) {
               while (($file = readdir($dh))) {
                 if ($file !== '.' AND $file !== '..' AND is_dir("{$current_dir}/$file")) {
							$current_file = "{$current_dir}/{$file}";
							if($bool == "0")
								$dir_list[]=$file;
							else
								$dir_list[]="$current_file";
							$stack[] = $current_file;
                   }
               }
			   closedir($dh);
           }
       }
       return $dir_list;
   }

	public function list_dir_files($dir, $bool) {
       $file_list = '';
       $stack[] = $dir;
       while ($stack) {
           $current_dir = array_pop($stack);
           if ($dh = opendir($current_dir)) {
               while (($file = readdir($dh)) !== false) {
                   if ($file !== '.' AND $file !== '..') {
                       $current_file = "{$current_dir}/{$file}";
                       if (is_file($current_file)) {
                       	if($bool == 0)
                           $file_list[] = $file;
                          else
                           $file_list[] = "{$current_dir}/{$file}";                          
                       } elseif (is_dir($current_file)) {
                           $stack[] = $current_file;
                       }
                   }
               }
           }
       }
       return $file_list;
   	}

// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// of course PHP has to have the rights to delete the directory
// you specify and all files and folders inside the directory
// ------------------------------------------------------------

// to use this function to totally remove a directory, write:
// recursive_remove_directory('path/to/directory/to/delete');

// to use this function to empty a directory, write:
// recursive_remove_directory('path/to/full_directory',TRUE);

function recursive_remove_directory($directory, $empty=FALSE)
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

			/*
			//get all directories in context
			$dir_list = $this->list_dir($path);
			//get all files in root directory
			$file_list = $this->list_dir_files($path);
			//loop through files
			foreach($file_list as $files)
			{
				$arrayOfFiles = file_get_contents($files);
				echo file_get_contents($files);
			}
			//echo $arrayOfFiles;
			//check all folders for contents 
			foreach($dir_list as $dirs)
			{
				//retrieve files in folders 
				$file_list = $this->list_dir_files($dirs);
				//loop through files
				foreach($file_list as $files)
				{
				$arrayOfFiles = file_get_contents($files);
				}				
			}
			
			//Straight forward call to execute xml writing
		case 'writetonew':
			$this->objExportContent->doXMLWrite();
			return $this->nextAction(null);
			
			//For future user import
			/*
			if($this->getParam('importusers') == check)
			{
				return $this->nextAction('importusers');
			}
			else
			{
				return $this->nextAction(null);
			}
		*/	
			/*
			if(file_exists($filePath.$context)) 
			{
				echo "folder exists"."<br />";
				rmdir($basepath.$context);
				echo "folder removed"."<br />";
				echo $basepath.$context."<br />";
				//Get all files starting in root directory
				$filelist = $this->list_dir_files($basepath.$context, 1);
				echo $filelist;
				for($i=0;$i<count($filelist);$i++)
					{
						//unlink($filelist[$i]);
						echo "file removed"."<br />";
					}							
			}
			mkdir($basepath.$context,0700);
			*/
			/*
			$filePath = $this->objConf->getcontentBasePath();
			echo $filePath."<br />";
			if(file_exists($filePath.$context)) 
			{
				rmdir($filePath.$context);
			}
			mkdir($filePath.$context,0700);
			//Get all files starting in root directory
			$filelist = $this->list_dir_files($filepath, 1);
			for($i=1; $i<count($filelist); $i++)
			{
				echo $filelist[$i]."<br />";
				$contents = file_get_contents($filelist[$i]);
				//echo $contents;
				$fp = fopen($filelist[$i],'w');
				//Write to file
				fwrite($fp,$contents);
				//Close the file
				fclose($fp);
			}
				//echo $basepath.$context." folder exists"."<br />";
			//echo $basepath.$context." folder created"."<br />";
			*/
   	    
}

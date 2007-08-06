<?php

/**
 * File system content
 * 
 * File system content class for Chisimba context
 * 
 * PHP version 3
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
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check


/**
 * File system content
 * 
 * File system content class for Chisimba context
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
 class fscontext extends object
 {
     
     /**
    * @var object $objConfig
    */
    var  $objConfig;
    
    /**
    * @var object $objZip
    */
    var $objZip;
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
     function init()
     {
         $this->objConfig=$this->newObject('altconfig','config');
         $this->objZip=$this->newObject('depzip','utilities');
     }
 
     /**
     * Method to create the folders
     * @param string $contextId The Context Id
     */
     function createContextFolder($contextCode)
     {
         //check if content folder exists
         
         //creat a context folder
        $ret=$this->makeFolder($contextCode);
         //create a images folder
        if ($ret){
            $ret = $this->makeFolder('flash/',$contextCode);
            $ret = $this->makeFolder('maps/',$contextCode);
            $ret = $this->makeFolder('documents/',$contextCode);
            $ret = $this->makeFolder('media/',$contextCode);
            $ret = $this->makeFolder('staticcontent/',$contextCode);
            $ret = $this->makeFolder('images/',$contextCode);
        }
        return $ret;
     }
     
     /**
     * Method to delete a context Folder
     * @param string $contextId The Context Id
     */
     function deleletContextFolder($contextCode)
     {
         //STILL NEED A DECISION ON BLOBS OR FILE SYSTEM
         $this->objZip->deldir($this->objConfig->getsiteRootPath().'/usrfiles/content/'.$contextCode.'/');
     }
     
     /**
     * Method copy images
     * @param string $contextCode The Context Code
     * @param string $path        the path of the file
     * @param string $file        The image file
     */
    function copyImage($contextCode,$path,$file)
    {
        //STILL NEED A DECISION ON BLOBS OR FILE SYSTEM
        //$copyResult=copy($path.'/'.$file,$this->objConfig->contentBasePath().'/'.$contextCode.'/images/'.$file);
    }
    
    /**
    * Method to create a new folder in  the images folder
    * @param string $folder The Folder path
    */
    function newFolderInImages($contextCode, $folder)
    {
        $this->makeFolder('images/'.$folder, $contextCode);
    }     
     
     /**
    * method to create specified folder
    * @access public
    * @param  string $folder The folder that needs to be created
    */
    function makeFolder($folder,$contextCode=NULL)
    {   
        $dir = $this->objConfig->getsiteRootPath().'/usrfiles/content';
        if (!(file_exists($dir))){
            $oldumask = umask(0);
            $ret = mkdir($dir, 0777);
            umask($oldumask);
        }
        
        if ($contextCode==''){
            $dir = $this->objConfig->getsiteRootPath().'/usrfiles/content/'.$folder;
        } else {
            $dir=$this->objConfig->getsiteRootPath().'/usrfiles/content/'.$contextCode.'/'.$folder;
        }
        if (!(file_exists($dir))){
            $oldumask = umask(0);
            $ret = mkdir($dir, 0777);  //STILL NEED A DECISION ON BLOBS OR FILE SYSTEM
            umask($oldumask);
        }
        else
        {
            $ret = FALSE;
        }
        
        return $ret;
    }
    
    /*
    * MEthod to check if a folder exists
    * @param string $path The Path to the folder
    * @return boolean
    */

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  string $contextCode Parameter description (if any) ...
     * @return void  
     * @access public
     */
    function folderExists($contextCode){
        $dir = $this->objConfig->getsiteRootPath().'/usrfiles/content/'.$contextCode;
        
        file_exists($dir);
    }
 }
 ?>
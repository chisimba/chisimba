<?php
/**
 * 
 * Archive Manager
 * 
 * Controller for archivemanager
 *
 * PHP version 5
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
 * @package   archivemanager
 * @author    Mofolo Mamabolo <mofolom@gmail.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.1
 * @link      http://avoir.uwc.ac.za
 */
 
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 * 
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *         
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

class archivemanager extends controller
	{
		
                function init()
		{
                    $this->objFile = $this->getObject('dbfile', 'filemanager');
                    $this->objFolders = $this->getObject('dbfolder', 'filemanager');
                    $this->objUserFolders = $this->getObject('userfoldercheck','filemanager');
                    $this->objUpload = $this->getObject('upload','filemanager');

                    $this->objUser = $this->getObject('user', 'security');

                    $this->archiveManager = $this->getObject('archivefactory','archivemanager');

                    $this->objMime = $this->getObject('mimetypes','files');
		}
		
		function dispatch( $action )
		{
			switch( $action )
			{
				case 'extract':{
                                        
                                        $Zipfilename = $this->getParam('archivefilename');
                                        $archive = $this->archiveManager->open($Zipfilename);

                                        $this->objUserFolders->checkUserFolder($this->objUser->userId());

                                        $parentId = $this->objFolders->getFolderId('users/'.$this->objUser->userId());
                                        $folderdId = $this->getParam('folderdropdown');
                                        $folderName = $this->objFolders->getFolderName($folderdId);
                                        $uploadFolder = $this->objFolders->getFullFolderPath($parentId)."/".$folderName;

                                        $files = $this->getParam('files');
                                        
                                        if(is_array($files)){
                                            foreach($files as $file){
                                                $archive->extractTo($uploadFolder, $file, NULL);
                                                $path = $this->objFolders->getFolderPath($parentId)."/".$folderName."".$file;
                                                $mimetype = $this->objMime->getMimeType($uploadFolder."/".$file);
                                                $this->objFile->addFile($file, $path, filesize($uploadFolder."/".$file), $mimetype, 'document' );
                                            }
                                        }

                                        return $this->nextAction('viewfolder',array('folder'=>$folderdId),'filemanager');
                                        break;
                                }
                                default:
                                    return $this->nextAction(NULL);
                                    break;
			}	
		}
	}

?>
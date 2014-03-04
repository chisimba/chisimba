<?php

/**
 * Context blocks
 * 
 * Chisimba Context blocks class
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
 * @package   workgroupops
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: block_browsecontext_class_inc.php 11989 2008-12-29 22:27:44Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */


// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 * Context blocks
 * 
 * Chisimba Context blocks class
 * 
 * @category  Chisimba
 * @package   workgroup
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class workgroupops extends object
{
	
	/**
	*Constructor
	*/
	public function init()
	{
		$this->objFiles = $this->getObject('dbworkgroupfiles', 'workgroup');
		$this->objCleanUrl = $this->getObject('cleanurl', 'filemanager');
		$this->objUser =& $this->getObject('user', 'security');
		$this->objFile = $this->getObject('dbfile', 'filemanager');
		$this->objConfig = $this->getObject('altconfig', 'config');
		$this->objFileIcons = $this->getObject('fileicons', 'files');
		$this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
		$this->loadClass('formatfilesize', 'files');
	}
	
	/**
	*Method to show the workgroup files
	*@param string workgroudid
	*/
	public function showFiles($workgroupId)
	{
		$files = $this->objFiles->getWorkgroupFiles($workgroupId);
		
		if(count($files) > 0)
		{
			$objFile = $this->getObject('dbfile', 'filemanager');
			$objIcon = $this->getObject('geticon', 'htmlelements');
			$objLink =  $this->getObject('link', 'htmlelements');
			$objFileSize = new formatfilesize();
			$str = '<span class="subdued">'.count($files) .' Files Found </div><table id="workgrouptable" width="80%">';
			foreach ($files as $file)
			{
				
				
				//download link
				$fileDownloadPath = $this->objFile->getFilePath($file['fileid']);
				$fileDownloadPath = $this->objCleanUrl->cleanUpUrl($fileDownloadPath);
				
				$objIcon->setIcon('download');
				$objLink->link = $objIcon->show();
				$objLink->href = $fileDownloadPath;//$this->uri(array('action'=>'editfile', 'fileid'=>$file['id']));
				$download = $objLink->show();
					
				$filename = $objFile->getFileName($file['fileid']);
				$fileSize = $objFileSize->formatsize($objFile->getFileSize($file['fileid']));
				$icon = $this->objFileIcons->getFileIcon($filename);
				$objLink->link = $filename;
				$fileNameLink = $objLink->show();
				
				if(($this->objUser->userId() == $file['modifierid']) || $this->objUser->isAdmin() || $this->objContextGroups->isContextLecturer())
				{
					//delete link
					$objIcon->setIcon('delete');
					$objLink->link = $objIcon->show();
					$objLink->href = $this->uri(array('action'=>'deletefile', 'fileid'=>$file['id']));
					$delete = $objLink->show();
					
					//edit link
					$objIcon->setIcon('edit');
					$objLink->link = $objIcon->show();
					$objLink->href = $this->uri(array('action'=>'editfile', 'fileid'=>$file['id']));
					$edit = $objLink->show();
				} else {
					$edit = "";
					$delete = "";
				}
				$rec ='<tr ><td style="border-top:1px dotted;">';
				
				$rec .='<div class="colorbox greenbox"><table ><tr>';
				$rec .='<td>'. $icon.'  '.$fileNameLink.'  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="subdued">'.$fileSize.'</span></td>';
				$rec .= '<td></td><td rowspan="2"><div class="colorbox pinkbox">'.$file['description'].'</div></td>';
				$rec .= '</tr><tr>';
				$rec .='<td>Modified by '. $this->objUser->fullname($file['modifierid']).'</td>';
				$rec .='<td>on '. $file['datemodified'].'</td>';
				$rec .='</tr>';
				
				$rec .='</table></td>';
				$rec .='<td style="border-top:1px dotted;">File Type: '.$objFile->getFileMimetype($file['fileid']).'</td>';
				$rec .='<td style="border-top:1px dotted;">Version:'.$file['version'].'</td>';
				$rec .='<td style="border-top:1px dotted;">'.$download.'&nbsp;'.$edit.'&nbsp;'.$delete.'</td>';
				$rec .='</tr></div>';
				
			

				$str .= $rec;
			}	
			
			return $str.'</table>';
		} else {
			return '<span class="subdued"><i>No files for this workgroup</i></span>';
			}
	
		
	}
	
}

?>
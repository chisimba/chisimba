<?php

/**
 *
 * Archive class
 *
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
 * @version   0.5
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

	class archive extends object{
	
		private $_status;
		private $_comment;
		private $_filename;
		private $_extension;
                private $_passwd;

		public function init(){
                    /* init some commonalities in here */
                    $this->loadClass('formatfilesize', 'files');

                    $this->objFileIcons = $this->getObject('fileicons', 'files');
                    $this->objFileIcons->size = 'small';

                    /* For previewing */
                    $this->loadClass('checkbox','htmlelements');
                    $this->loadClass('button','htmlelements');
                    $this->loadClass('form','htmlelements');
                    $this->loadClass('dropdown','htmlelements');
                    $this->loadClass('hiddeninput','htmlelements');

                    $this->objUser = $this->getObject('user', 'security');
                    $this->objFolders = $this->getObject('dbfolder', 'filemanager');
                    

                    $this->appendArrayVar( 'headerParams', $this->getJavascriptFile('selectall.js','htmlelements') );
		}

		public function getfilename(){
			return $this->_filename;
		}
		
		public function setfilename( $filename ){
			$this->_filename = $filename;
                        $ext = pathinfo( $filename );
                        $this->_extension = $ext['extension'];
		}

                public function setpassword($passwd){
                        $this->_passwd=$passwd;
                }

                public function getpassword(){
                    return $this->_passwd;
                }

		public function getcomment(){
			return $this->_comment;
		}
		
		public function getstatus(){
			return $this->_status;
		}
		
		public function getfilextension(){
			return $this->_extension;
		}

                public function preview($files, $ArchiveFilename){

                    $objFileSize = new formatfilesize();

                    $tblEntries = $this->newObject( 'htmltable','htmlelements' );

                    $tblEntries->width = '40%';
                    $tblEntries->cellpadding = 5;
                    $tblEntries->cellspacing = 2;

                    $tblEntries->startHeaderRow();
                    $tblEntries->addHeaderCell( 'Filename' );
                    $tblEntries->addHeaderCell( 'Size' );
                    $tblEntries->addHeaderCell( 'Status');
                    $tblEntries->addHeaderCell( 'Select');
                    $tblEntries->endHeaderRow();

                    $oddEven = 'odd';
                    foreach( $files as $file ) {
                        if($file['folder']){
                            continue;
                        }

                        $tblEntries->row_attributes = "class=\"$oddEven\"";
                        $tblEntries->startRow();
                        $tblEntries->addCell($this->objFileIcons->getFileIcon($file['filename']).' '.$file['filename']);

                        // Sort your column by meaning data
                        //$lnkEntry = $this->newObject( 'link', 'htmlelments' );
                        //$lnkEntry->href = "Your link stuff";
                        //$lnkEntry->link = "Your link text";// which get used for the sort;
                        //$tblEntries->sortData = $lnkEntry->link;
                        $tblEntries->addCell($objFileSize->formatsize($file['size']));
                        $tblEntries->addCell($file['status']);
                        //$tblEntries->addCell($file['id']);
                        //$objElement = new checkbox($file['filename']);
                        $chck = new checkbox('files[]');
                        $chck->setId($file['filename']);
                        $chck->value = $file['filename'];
                        $chck->cssId = htmlentities($file['filename']);
                        
                        //$objElement = $this->getObject( 'checkbox','htmlelements' );
                        //$check.=$objElement->show();
                        //$tblEntries->addCell($objElement->show());
                        $tblEntries->addCell($chck->show(),20);
                        $tblEntries->endRow();
                        $oddEven = $oddEven=='odd'?'even':'odd';

                    }

                    $form1 = new form('extract_files', $this->uri(array('action'=>'extract'),$modulename));
                    $form1->addToForm($tblEntries->show());

                    $button = new button ('submit_form', 'Extract');
                    $button->setToSubmit();

                    $selectallbutton = new button ('selectall', 'Select All');
                    $selectallbutton->setOnClick("SetAllCheckBoxes('extract_files',files[], true);");

                    $deselectallbutton = new button ('deselectall', 'Deselect All');
                    $deselectallbutton->setOnClick("Javascript:SetAllCheckBoxes('extract_files', 'files[]', false);");

                    $this->userId = $this->objUser->userId();
                    $folderpath = 'users/'.$this->userId;
                    $userfolders = $this->objFolders->getUserFolders($this->userId);
                    $folderdropdown = new dropdown('folderdropdown');
                    $folderdropdown->addOption('Myfiles','Myfiles');
                    foreach($userfolders as $folder){
                        $folderdropdown->addOption( $folder['id'],$this->objFolders->getFolderName($folder['id']));
                    }

                    $archivefilename = new hiddeninput('archivefilename',$ArchiveFilename);

                    $form1->addToForm($button->show().' <strong>to</strong> '.$folderdropdown->show().' '.$selectallbutton->show().' '.$deselectallbutton->show());
                    $form1->addToForm($archivefilename);

                    echo $form1->show();

                    

                }

		public function extractTo( $foldername ){}
		
	}

?>

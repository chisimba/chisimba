<?
/**
* Class to Handle Zip Files for Previews and Extraction
*
* @author Tohir Solomons
* @package filemanager
*/
class archivehandler extends object
{

    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objZip =& $this->getObject('wzip', 'utilities');
        $this->objFileIcons =& $this->getObject('fileicons', 'files');
        $this->objFileIcons->size = 'small';
        $this->loadClass('formatfilesize', 'files');
    }
    
    /**
	* Enter description here...
	*
	* @param string $path Path to the Zip File
	* @return string Preview of the Zip File
	*/
    public function previewZip($path)
    {
        $files = $this->objZip->listArchiveFiles($path);
        
        if ($files == FALSE) {
            return 'Error: Could not process file';
        } else {
            
            // echo '<pre>';
            // print_r($files);
            // echo '</pre>';
            
            $path_parts = pathinfo($path);
            
            
            
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->startHeaderRow();
            $table->addHeaderCell('&nbsp;');
            $table->addHeaderCell('Name of File');
            $table->addHeaderCell('File Size');
            $table->addHeaderCell('Status');
            $table->endHeaderRow();
            
            $filecount = 0;
            $foldercount = 0;
            
            $objFileSize = new formatfilesize();
            
            foreach ($files as $file)
            {
                if ($file['folder']) {
                    continue;
                }
                
                $table->startRow();
                $table->addCell('&nbsp;');
                $table->addCell($this->objFileIcons->getFileIcon($file['filename']).' '.$file['filename']);
                $table->addCell($objFileSize->formatsize($file['size']));
                $table->addCell($file['status']);
                $table->endRow();
            }
            
            return $table->show();
        }
    }
    

    
    
    

}

?>
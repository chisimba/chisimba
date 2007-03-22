<?
/**
 * Class to Show a File Selector Input
 *
 * @author Tohir Solomons
 * @package filemanager
 */
class previewfolder extends object
{

    /**
    * Constructor
    */
    public function init()
    {
        $this->objFileIcons =& $this->getObject('fileicons', 'files');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('formatfilesize', 'files');
    }
    
    function previewContent($subFolders, $files)
    {
        return $this->previewLongView($subFolders, $files);
    
    }
    
    function previewLongView($subFolders, $files)
    {
        $objTable = $this->newObject('htmltable', 'htmlelements');
        
        $objTable->startHeaderRow();
        $objTable->addHeaderCell('&nbsp;', '20');
        $objTable->addHeaderCell('&nbsp;', '20');
        $objTable->addHeaderCell('Name');
        $objTable->addHeaderCell('Size', 60);
        $objTable->addHeaderCell('&nbsp;', '30');
        
        $objTable->endHeaderRow();
        
        if (count($subFolders) == 0 && count($files) == 0) {
            $objTable->startRow();
            $objTable->addCell('<em>No files or folders found</em>', NULL, NULL, NULL, 'noRecordsMessage', 'colspan="5"');
            $objTable->endRow();
        } else {
        
            if (count($subFolders) > 0) {
                $folderIcon = $this->objFileIcons->getExtensionIcon('folder');
                
                foreach ($subFolders as $folder)
                {
                    $objTable->startRow();
                    $objTable->addCell('&nbsp;');
                    $objTable->addCell($folderIcon);
                    
                    $folderLink = new link ($this->uri(array('action'=>'viewfolder', 'folder'=>$folder['id'])));
                    $folderLink->link = basename($folder['folderpath']);
                    $objTable->addCell($folderLink->show());
                    $objTable->addCell('<em>Folder</em>');
                    $objTable->endRow();
                }
            }
            
            if (count($files) > 0) {
                
                $fileSize = new formatfilesize();
                foreach ($files as $file)
                {
                    $objTable->startRow();
                    $objTable->addCell('&nbsp;');
                    
                    $fileIcon = $this->objFileIcons->getFileIcon($file['filename']);
                    $objTable->addCell($fileIcon);
                    
                    $fileLink = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'])));
                    $fileLink->link = basename($file['filename']);
                    $objTable->addCell($fileLink->show());
                    $objTable->addCell($fileSize->formatsize($file['filesize']));
                    $objTable->endRow();
                }
            }
        }
        return $objTable->show();
    }
    
    

    
    
    

}

?>
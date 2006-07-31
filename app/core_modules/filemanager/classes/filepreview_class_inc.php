<?
/**
* Class to detemine which subfolder a file should be placed in
*
* It does this based on an analysis of either:
* 1) mimetype
* 2) extension
*
* @author Tohir Solomons
*/
class filepreview extends object
{

    /**
    * Constructor
    */
    function init()
    {
        $this->objFileParts =& $this->getObject('fileparts', 'files');
        $this->objFiles =& $this->getObject('dbfile');
        $this->objThumbnails = $this->getObject('thumbnails');
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objFileEmbed =& $this->getObject('fileembed');
    }
    
    /**
    * Method to determine which sub folder a file should be placed in
    *
    * Note: This function is pretty hardcoded in determining the result
    * More dynamic options are welcome.
    *
    * @param string $name Name of the File
    * @param string $mimetype Mimetype of the File
    * @return string Sub Folder file must be placed in
    */
    function previewFile($fileId)
    {
        $preview = 'No Preview Available';
        
        $this->file = $this->objFiles->getFileInfo($fileId);
        
        $this->file['path'] = $this->objConfig->getcontentPath().$this->file['path'];
        // Fix Up - Try to get file using controller, instead of hard linking to file
        // $this->uri(array('action'=>'file', 'id'=>$this->file['id'], 'filename'=>$this->file['filename']), 'filemanager');
        
        switch ($this->file['category'])
        {
            case 'images': $preview = $this->showImage(); break;
            case 'obj3d': $preview = $this->show3dObject(); break;
            case 'freemind': $preview = $this->showFreemind(); break;
            case 'audio': $preview = $this->showAudio(); break;
            case 'video': $preview = $this->showVideo(); break;
            case 'flash': $preview = $this->showFlash(); break;
            case 'scripts': $preview = $this->showScript(); break;
        }
        return $preview;
    }
    
    /**
    * Method to preview an image
    */
    function showImage()
    {
        return $this->objFileEmbed->embed($this->file['path'], 'image');
    }
    
    /**
    * Method to preview a 3d Object
    */
    function show3dObject()
    {
        switch ($this->file['datatype'])
        {
            case 'obj': return $this->objFileEmbed->embed($this->file['path'], 'obj3d');
            case 'wrl': return $this->objFileEmbed->embed($this->file['path'], 'vrml');
            default: return $this->objFileEmbed->embed($this->file['path'], 'unknown');
        }
    }
    
    /**
    * Method to preview a Freemind Map
    */
    function showFreemind()
    {
        return $this->objFileEmbed->embed($this->file['path'], 'freemind');
    }
    
    /**
    * Method to preview an Audio File
    */
    function showAudio()
    {
        return $this->objFileEmbed->embed($this->file['path'], 'audio');
    }
    
    /**
    * Method to preview a Video
    */
    function showVideo()
    {
        if (array_key_exists('width', $this->file) && $this->file['width'] != '') {
            $width = $this->file['width'] < 200 ? '200' : $this->file['width']; 
        } else {
            $width = '100%';
        }
        
        if (array_key_exists('height', $this->file) && $this->file['height'] != '') {
            $height = $this->file['height'] < 200 ? '200' : $this->file['height']; 
        } else {
            $height = '100%';
        }
        
        switch ($this->file['datatype'])
        {
            case 'mov': return $this->objFileEmbed->embed($this->file['path'], 'quicktime', $width, $height);
            case '3gp': return $this->objFileEmbed->embed($this->file['path'], 'quicktime', $width, $height);
            case 'wmv': return $this->objFileEmbed->embed($this->file['path'], 'wmv', $width, $height);
            default: return $this->objFileEmbed->embed($this->file['path'], 'unknown');
        }
    }
    
    /**
    * Method to preview a Flash file
    */
    function showFlash()
    {
        if (array_key_exists('width', $this->file) && $this->file['width'] != '') {
            $width = $this->file['width'];
        } else {
            $width = '100%';
        }
        
        if (array_key_exists('height', $this->file) && $this->file['height'] != '') {
            $height = $this->file['height'];
        } else {
            $height = '100%';
        }
        
        return $this->objFileEmbed->embed($this->file['path'], 'flash', $width, $height);
    }
    
    /**
    * Method to preview a Script
    */
    function showScript()
    {
        // Get Extension
        $filetype = $this->objFileParts->getExtension($this->file['filename']);
        
        // Convert Extension to Language
        switch ($filetype)
        {
            case 'phps': $filetype = 'php'; break;
            case 'pl': $filetype = 'perl'; break;
            case 'js': $filetype = 'javascript'; break;
            case 'py': $filetype = 'python'; break;
        }
        
        // Open File, Read Contents, Close
        $handle = fopen ($this->file['path'], "r"); 
        $contents = fread ($handle, filesize ($this->file['path'])); 
        fclose ($handle);
        
        $objGeshi = $this->getObject('geshiwrapper', 'wrapgeshi');
        $objGeshi->source = $contents;
        $objGeshi->language = $filetype;
        
        $objGeshi->startGeshi();
        $objGeshi->enableLineNumbers(2);
        
        return $objGeshi->show();
    }

}

?>
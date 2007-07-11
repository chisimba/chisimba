<?php
/**
 * Class to Show an Image Selector Input
 *
 * @author Tohir Solomons
 * @package filemanager
 */
class selectimage extends object
{
    /**
    * @var string $name Name of the Image Selector Input
    */
    public $name;
    
    /**
    * @var string $defaultFile Record Id of the Default File
    */
    public $defaultFile;
    
    /**
    * @var boolean $context Flag to only include Context Files
    * @todo Implement this Feature
    */
    public $context;
    
    /**
    * @var boolean $workgroup Flag to only include Workgroup Files
    * @todo Implement this Feature
    */
    public $workgroup;
    
    /**
    * @var int $widthOfInput Width of Text Input
    */
    public $widthOfInput;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->name = 'imageselect';
        $this->restrictFileList = array();
        
        $this->defaultFile = '';
        
        $this->context = FALSE;
        $this->workgroup = FALSE;
        
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        
        $this->objFile = $this->getObject('dbfile');
        $this->objThumbnails = $this->getObject('thumbnails');
        
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('windowpop', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        
        $this->widthOfInput = '80%';
    }
    
    /**
    * Method to set the default File
    * @access public
    * @param string $fileId Record Id of the Default File
    */
    public function setDefaultFile($fileId)
    {
        $this->defaultFile = $fileId;
    }
    
    /**
    * Method to return the JavaScript to clear an input
    * @return string
    */
    public function showClearInputJavaScript()
    {
        $script = '
<script type="text/javascript">

function clearFileInputJS(name)
{
    //document.getElementById(\'selectfile_\'+name).value = \'\';
    document.getElementById(\'imagepreview_\'+name).src = \'skins/_common/icons/imagepreview.gif\';
    document.getElementById(\'hidden_\'+name).value = \'\';
}
</script>';

        return $script;
    }
    
    /**
    * Method to show the file selector input
    * @return string File Selector
    */
    public function show()
    {
        $this->appendArrayVar('headerParams', $this->showClearInputJavaScript());
        if ($this->defaultFile == '') {
            $defaultId = '';
            $defaultName = '';
        } else {
            $file = $this->objFile->getFile($this->defaultFile);
            
            if ($file == FALSE) {
                $defaultId = '';
                $defaultName = '';
            } else {
                $defaultId = $file['id'];
                $defaultName = $file['filename'];
            }
        }
        
        $input = new hiddeninput($this->name, $defaultId);
        $input->extra = ' id="hidden_'.$this->name.'"';
        
       
        $objPop = new windowpop;
        
        
        if ($this->context) {
            $context = 'yes';
        } else {
            $context = 'no';
        }
        
        if ($this->workgroup) {
            $workgroup = 'yes';
        } else {
            $workgroup = 'no';
        }
        
        
        $location = $this->uri(array('action'=>'selectimagewindow', 'name'=>$this->name, 'context'=>$context, 'workgroup' => $workgroup), 'filemanager');
        
        // Couldnt do this via uri function due to embedded JS
        $location .= '&amp;value=\'+document.getElementById(\'hidden_'.$this->name.'\').value+\'&amp;';
        
        $objPop->set('location', $location);
        
        $this->objIcon->setIcon('find_file');
        $this->objIcon->alt = 'Select File';
        $this->objIcon->title = 'Select File';
        
        
        
        $objPop->set('linkType', 'button');
        $objPop->set('linktext', 'Select File');
        
        //$objPop->set('linktext', $this->objIcon->show());
        $objPop->set('width','600'); 
        $objPop->set('height','400');
        $objPop->set('resizable','yes');
        $objPop->set('scrollbars','auto');
        $objPop->set('left','300');
        $objPop->set('top','200');
        $objPop->set('status','yes');
        //leave the rest at default values
        $objPop->putJs();
        
        if ($defaultId == '') {
            $this->objIcon->setIcon('imagepreview');
            $this->objIcon->alt = 'Image Preview';
            $this->objIcon->title = 'Image Preview';
            $this->objIcon->extra = ' id="imagepreview_'.$this->name.'"';
            $previewImg = $this->objIcon->show();
        } else {
            $img = $this->objThumbnails->getThumbnail($defaultId, $file['filename']);
            
            $previewImg = '<img src="'.$img.'" id="imagepreview_'.$this->name.'" />';
        }
        
        $textinput = new textinput ('selectfile_'.$this->name, $defaultName);
        $textinput->setId('selectfile_'.$this->name);
        $textinput->extra = ' readonly="true" style="width:'.$this->widthOfInput.'" ';
        
        $button = new button('clear', 'Reset', 'clearFileInputJS(\''.$this->name.'\');');
        
        // Option for showing via submodal window
        // $objSubModalWindow = $this->getObject('submodalwindow', 'htmlelements');
        // $subModal = $objSubModalWindow->show('Select', $location, 'button');
        // return $input->show().$textinput->show().' &nbsp; '.$subModal.$button->show();
        
        return $input->show().'<div style="width:100px; height:100px;line-height:100px;vertical-align:center;text-align:center;">'.$previewImg.'</div><br /><div>'.$objPop->show().' '.$button->show().'</div>';
        //$textinput->show()
    }
    
    
    

}

?>
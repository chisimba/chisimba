<?
/**
 * Class to Show a File Selector Input
 *
 * @author Tohir Solomons
 * @package filemanager
 */
class selectfile extends object
{
    /**
    * @var string $name Name of the File Selector Input
    */
    public $name;
    
    /**
    * @var array $subFolders List of Possible Subfolders for storing files
    */
    public $restrictFileList;
    
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
    * Constructor
    */
    public function init()
    {
        $this->name = 'fileselect';
        $this->restrictFileList = array();
        
        $this->defaultFile = '';
        
        $this->context = FALSE;
        $this->workgroup = FALSE;
        
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        
        $this->objFile = $this->getObject('dbfile');
        
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('windowpop', 'htmlelements');
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
    * Method to show the file selector input
    * @return string File Selector
    */
    public function show()
    {
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
        
        if (count($this->restrictFileList) == 0) {
            $ext = '';
        } else {
            
            $ext = '';
            $divider = '';
            
            foreach ($this->restrictFileList as $type)
            {
                $ext .= $divider.$type;
                $divider = '____';
            }
        }
        
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
        
        
        $location = $this->uri(array('action'=>'selectfilewindow', 'restrict'=>$ext, 'name'=>$this->name, 'context'=>$context, 'workgroup' => $workgroup), 'filemanager');
        
        // Couldnt do this via uri function due to embedded JS
        $location .= '&amp;value=\'+document.getElementById(\'hidden_'.$this->name.'\').value+\'&amp;';
        
        $objPop->set('location', $location);
        
        $this->objIcon->setIcon('find_file');
        $this->objIcon->alt = 'Select File';
        $this->objIcon->title = 'Select File';
        
        
        //$objPop->set('linktext', 'Select File');
        $objPop->set('linktext', $this->objIcon->show());
        $objPop->set('width','600'); 
        $objPop->set('height','400');
        $objPop->set('resizable','yes');
        $objPop->set('scrollbars','auto');
        $objPop->set('left','300');
        $objPop->set('top','200');
        $objPop->set('status','yes');
        //leave the rest at default values
        $objPop->putJs();
        
        $textinput = new textinput ('selectfile_'.$this->name, $defaultName);
        $textinput->setId('selectfile_'.$this->name);
        $textinput->extra = ' readonly="true"';
        $textinput->size = '60';
        
        return $input->show().$textinput->show().' &nbsp; '.$objPop->show();
    }
    
    
    

}

?>
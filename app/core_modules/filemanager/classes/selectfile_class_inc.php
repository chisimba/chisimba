<?
/**
 * Adaptor Pattern around the PEAR::Config Object
 * This class will provide the kng configuration to Engine
 *
 * @author Tohir Solomons
 * @package filemanager
 */
class selectfile extends object
{
    /**
    * @var array $subFolders List of Possible Subfolders for storing files
    */
    private $subFolders;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->name = 'fileselect';
        $this->restrictFileList = array();
        
        $this->context = FALSE;
        $this->workgroup = FALSE;
        
        
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('windowpop', 'htmlelements');
    }
    
    /**
    * Method to check that the user folder for uploads, and subfolders exist
    * @param string $userId UserId of the User
    */
    public function show()
    {
        $input = new hiddeninput($this->name);
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
        
        $objPop->set('linktext','Select File');
        $objPop->set('width','600'); 
        $objPop->set('height','400');
        $objPop->set('resizable','yes');
        $objPop->set('scrollbars','auto');
        $objPop->set('left','300');
        $objPop->set('top','200');
        //leave the rest at default values
        $objPop->putJs();
        
        return $input->show().'<span id="selectfile_'.$this->name.'"></span>'.$objPop->show();
    }
    
    
    

}

?>
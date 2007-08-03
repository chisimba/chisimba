<?php

/**
 * Class to Show an Image Selector for the Realtime Tools
 *
 * This class was designed as a image input for the realtime tools,
 * and as such should only be used for that purpose. This class may
 * be moved out of file manager
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to Show an Image Selector for the Realtime Tools
 *
 * This class was designed as a image input for the realtime tools,
 * and as such should only be used for that purpose. This class may
 * be moved out of file manager
 * 
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
class selectrealtimeimage extends object
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
    * @var  boolean $context Flag to only include Context Files
    * @todo Implement this Feature
    */
    public $context;
    
    /**
    * @var  boolean $workgroup Flag to only include Workgroup Files
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
        
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('windowpop', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        
        $this->widthOfInput = '80%';
    }
    
    /**
    * Method to set the default File
    * @access public
    * @param  string $fileId Record Id of the Default File
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
        
        $input = new textinput($this->name, $defaultId);
        $input->extra = ' id="hidden_'.$this->name.'" size="100"';
        $input->cssId = 'hidden_'.$this->name;
        
        
       
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
        
        
        $location = $this->uri(array('action'=>'selectrealtimeimagewindow', 'name'=>$this->name, 'context'=>$context, 'workgroup' => $workgroup), 'filemanager');
        
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
        
        $this->objIcon->setIcon('imagepreview');
        $this->objIcon->alt = 'Image Preview';
        $this->objIcon->title = 'Image Preview';
        $this->objIcon->extra = ' id="imagepreview_'.$this->name.'"';
        $previewImg = $this->objIcon->show();
        
        $textinput = new textinput ('selectfile_'.$this->name, $defaultName);
        $textinput->setId('selectfile_'.$this->name);
        $textinput->extra = ' readonly="true" style="width:'.$this->widthOfInput.'" ';
        
        $button = new button('clear', 'Reset', 'clearFileInputJS(\''.$this->name.'\');');
        
        // Option for showing via submodal window
        // $objSubModalWindow = $this->getObject('submodalwindow', 'htmlelements');
        // $subModal = $objSubModalWindow->show('Select', $location, 'button');
        // return $input->show().$textinput->show().' &nbsp; '.$subModal.$button->show();
        
        return $input->show().'<div style="width:100px; height:100px;line-height:100px;vertical-align:center;text-align:center;">'.$previewImg.'</div><br /><div>'.$objPop->show().'</div>';
        //$textinput->show()$button->show()
    }
    
    
    

}

?>
<?php

/**
 * Class to Show a Multi File Selector Input
 *
 * This is where the user can select multiple files for a single input
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
 * @version   CVS: $Id: selectfile_class_inc.php 10221 2008-08-22 08:59:18Z tohir $
 * @link      http://avoir.uwc.ac.za
 * @see
 */


/**
 * Class to Show a Multi File Selector Input
 *
 * This is where the user can select multiple files for a single input
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
$this->loadClass('filemanagerobject', 'filemanager');
class multifileselect extends filemanagerobject
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
    * @var string $defaultFiles Record Id of the Default File
    */
    private $defaultFiles;

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
    * @var string $deleteIcon Delete Icon
    */
    private $deleteIcon;

    /**
    * Constructor
    */
    public function init()
    {
        $this->name = 'fileselect';
        $this->restrictFileList = array();

        $this->defaultFiles = '';

        $this->context = FALSE;
        $this->workgroup = FALSE;

        $this->objIcon = $this->newObject('geticon', 'htmlelements');

        $this->objFile = $this->getObject('dbfile');

        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');

        $this->widthOfInput = '80%';
        
        $this->objIcon->setIcon('delete');
        $this->deleteIcon = $this->objIcon->show();
    }

    /**
    * Method to set the default File
    * @access public
    * @param  string $fileId Record Id of the Default File
    */
    public function setDefaultFiles($list)
    {
        $this->defaultFiles = $list;
    }

    /**
    * Method to return the JavaScript for adding and removing items
    * @return string
    */
    public function showJavaScript()
    {
        
        
        $script = '
<script type="text/javascript">

/* Delete Icon */
deleteIcon = \''.$this->deleteIcon.'\';

/* Method to remove an item from the multi files list */
function removeMultiFile(name)
{
    // If user confirms removal
    if (confirm("Remove this file?")) {
    
        // Get list of items
        defaultVal = jQuery(\'#hidden_headerscripts\').attr(\'value\');
        
        if (defaultVal == null) {
            defaultVal = \'\';
        }
        
        // Prepare regex
        var myReg = new RegExp(name+",", \'gi\');
        
        // Remove from list
        replacement = defaultVal.replace(myReg, "");
        
        // Replace adjusted list
        jQuery(\'#hidden_headerscripts\').attr(\'value\', replacement);
        
        // Remove from display
        jQuery(\'span.multiitem_\'+name).each(function (i) {
            jQuery(this).html("");
        });
    }
}

/* Method to add an item to the multi files list */
function addToMultiList()
{
    // Get List of Items
    defaultVal = jQuery(\'#hidden_headerscripts\').attr(\'value\');
    
    if (defaultVal == null) {
        defaultVal = \'\';
    }
    
    // Get List of Items on Display
    defaultText = jQuery(\'#multifileslist\').html();
    
    if (defaultText == null) {
        defaultText = \'\';
    }
    
    // Get Value to be added
    newVal = jQuery(\'#hidden_temp_multiselect\').attr(\'value\');
    
    // Check if it is valid
    if (newVal != null) {
    
        // Add to List
        jQuery(\'#hidden_headerscripts\').attr(\'value\', defaultVal+newVal+\',\');
        
        // Generate Delete Portion
        deleteStr = "<a href=\"javascript:removeMultiFile(\'"+newVal+"\');\">"+deleteIcon+"</a>";
        
        // Prepare Item before adding to display list
        str = \'<span class="multiitem_\'+newVal+\'">\'+jQuery(\'#input_selectfile_temp_multiselect\').attr(\'value\')+\' \'+deleteStr+\'<br /></span>\';
        
        // 
        jQuery(\'#multifileslist\').html(defaultText+str);
        
        clearFileInputJS(\'temp_multiselect\');
    }
}
</script>';

        return $script;
    }

    /**
    * Method to show the multi file selector input
    * @return string File Selector
    */
    public function show()
    {
        $this->appendArrayVar('headerParams', $this->showJavaScript());
        
        $defaultValue = '';
        $defaultDisplay = '';
        
        if ($this->defaultFiles != '') {
            // Explode into array
            $scripts = explode(',', $this->defaultFiles);
            
            // Loop through array
            foreach ($scripts as $script)
            {
                // Check if valid
                if (trim($script) != '') {
                    
                    // Get Path
                    $fileInfo = $this->objFile->getFile($script);
                    
                    // If Valid
                    if ($fileInfo != FALSE) {
                        
                        $defaultValue .= $fileInfo['id'].',';
                        $defaultDisplay .= '<span class="multiitem_'.$fileInfo['id'].'">'.$fileInfo['filename'].' <a href="javascript:removeMultiFile(\''.$fileInfo['id'].'\');">'.$this->deleteIcon.'</a><br /></span>';
                    }
                }
            }
        }

        $input = new textinput($this->name, $this->defaultFiles); // change back to hidden
        $input->cssId = 'hidden_headerscripts';
        $input->extra = ' size="60"';
        $input->value = $defaultValue;
        $input->fldType = 'hidden';
        
        $selectFile = $this->newObject('selectfile', 'filemanager');
        $selectFile->name = 'temp_multiselect';
        $selectFile->restrictFileList = $this->restrictFileList;
        $selectFile->context = $this->context;
        $selectFile->workgroup = $this->workgroup;
        
        // Option for showing via submodal window
        // $objSubModalWindow = $this->getObject('submodalwindow', 'htmlelements');
        // $subModal = $objSubModalWindow->show('Select', $location, 'button');
        // return $input->show().$textinput->show().' &nbsp; '.$subModal.$button->show();
        
        $addButton = new button ('addtolist', 'Add to List');
        $addButton->setOnClick('addToMultiList();');
        
        $str = '<div id="multifiles">';
        $str .= '<div id="multifileslist">'.$defaultDisplay.'</div>';
        $str .= $input->show().$selectFile->show().'<br />'.$addButton->show();
        $str .= '</div>';
        
        return $str;
    }




}

?>
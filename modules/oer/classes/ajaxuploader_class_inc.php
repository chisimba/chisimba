<?php
/**
 * This class contains util methods for displaying full original product details
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
 * @version    0.001
 * @package    oer

 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * @author     pwando paulwando@gmail.com
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end of security


class ajaxuploader extends object {

    /**
     * Constructor
     */
    public function init() {
        // Load Classes Needed to Create the form and iframe
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    /**
     * Function generates random strings
     *
     * @return string
     */

    public function genRandomString() {
        $length = 5;
        $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
        $string = "";

        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters)-1)];
        }

        return $string;
    }

    /**
     * Method to render the form
     * 
     * @return string Form
     */
    public function show($itemid,$action) {
        
        // Generate an ID - In case multiple uploads occur on one page
        $id = $this->genRandomString(); // mktime().rand();
        // Generate Iframe
        $objIframe = $this->newObject('iframe', 'htmlelements');
        $objLanguage = $this->getObject("language", "language");

        $objIframe->src = $this->uri(array('action' => 'tempiframe', 'id' => $id));
        $objIframe->id = 'ifra_upload_' . $id;
        $objIframe->name = 'iframe_upload_' . $id;
        $objIframe->frameborder = 1;
        $objIframe->width = 600;
        $objIframe->height = 400;
        $objIframe->extra = ' style="display:none" ';

        // Create Loading Icon - Hidden by Default
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('loading_bar');

        // Create Form
        $form = new form('uploadfile_' . $id, $this->uri(array('action' => $action,
                            'itemid' => $itemid)));
        $form->extra = 'enctype="multipart/form-data" target="iframe_upload_' . $id . '"';
        ;
        $form->id = 'form_upload_' . $id;


        // File Input
        $fileInput = new textinput('fileupload');
        $fileInput->fldType = 'file';
        $fileInput->size = 60;
        $fileInput->extra = 'onchange="changeFileName(\'' . $id . '\');"';

        // Button
        $button = new button('upload', 'Upload');
        $button->setOnClick('doUpload(\'' . $id . '\');');
        // Hidden Inputs
        $filename = new hiddeninput('filename', '');
        $hiddenInput = new hiddeninput('id', $id);

        $form->addToForm($fileInput->show() . ' ' . $filename->show() . $hiddenInput->show().'<div id="selectedimage"></div>');
        $form->addToForm($button->show());

        // Append JavaScript
        $this->addJS();

        $progress = $objLanguage->languageText("mod_oer_uploadinprogress", "oer");

        return $form->show() . '<div id="div_upload_' . $id . '" style="display:none;">' . $objIcon->show() . ' ' . $progress . '</div><div id="uploadresults"></div><div id="updateform"></div>' . $objIframe->show();
    }

    /**
     * Method to append JavaScript to the header
     *
     * These are run when the forms are submitted.
     */
    private function addJS() {
        $this->appendArrayVar('headerParams', '<script type="text/javascript">
// <![CDATA[

function doUpload(id)
{

    if (document.forms[\'uploadfile_\'+id].fileupload.value == \'\') {
        alert(\'Please select a file\');
    } else {
        document.getElementById(\'form_upload_\'+id).style.display=\'none\';
        document.getElementById(\'div_upload_\'+id).style.display=\'block\';
        document.getElementById(\'uploadresults\').style.display=\'block\';
        document.forms[\'uploadfile_\'+id].submit();
    
    }
}

function changeFileName(id)
{
    //document.forms[\'uploadfile\'].filename.value = document.forms[\'uploadfile\'].fileupload.value;
    
    var tr = document.forms[\'uploadfile_\'+id].fileupload.value;
    len = tr.length;
    rs = 0;
    for (i = len; i > 0; i--) {
        vb = tr.substring(i,i+1)
        if (vb == "/" && rs == 0) {
            document.forms[\'uploadfile_\'+id].filename.value = tr.substring(i+1,len);
            rs = 1;
        }
    }
}

// ]]>
</script>');
    }

}

?>
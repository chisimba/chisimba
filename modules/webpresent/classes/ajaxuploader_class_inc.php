<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end of security

/**
* Class to Display the Ajax Presentation Uploader
*
* This isn't really an Ajax Uploader, but gives that impression
* Instead, it targets a form to a hidden iframe, and updated a div
* on the parent window when the upload is completed.
*
* It does two things once the upload has been successfully completed:
* 1) Instantiates an ajax call for a form for the user to upload the details of the presentation
* 2) Instantiates an ajax call for the conversion process. This process is backgrounded,
* allowing the user to continue browsing the site whilst the conversion takes place.
*/
class ajaxuploader extends object
{

    /**
    * Constructor
    */
    public function init()
    {
        // Load Classes Needed to Create the form and iframe
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
    }

    /**
    * Method to render the form
    * @return string Form
    */
    public function show()
    {
        // Generate an ID - In case multiple uploads occur on one page
        $id = mktime().rand();

        // Generate Iframe
        $objIframe = $this->newObject('iframe', 'htmlelements');

        $objIframe->src = $this->uri(array('action'=>'tempiframe', 'id'=>$id));
        $objIframe->id = 'ifra_upload_'.$id;
        $objIframe->name = 'iframe_upload_'.$id;
        $objIframe->frameborder = 1;
        $objIframe->width = 600;
        $objIframe->height = 400;
        $objIframe->extra = ' style="display:none" ';

        // Create Loading Icon - Hidden by Default
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('loading_bar');

        // Create Form
        $form = new form ('uploadfile_'.$id, $this->uri(array('action'=>'doajaxupload')));
        $form->extra = 'enctype="multipart/form-data" target="iframe_upload_'.$id.'"';;
        $form->id = 'form_upload_'.$id;

        // File Input
        $fileInput = new textinput('fileupload');
        $fileInput->fldType = 'file';
        $fileInput->size = 60;
        $fileInput->extra = 'onchange="changeFileName(\''.$id.'\');"';

        // Button
        $button = new button ('upload', 'Upload');
        $button->setOnClick('doUpload(\''.$id.'\');');

        // Hidden Inputs
        $filename = new hiddeninput('filename', '');
        $hiddenInput = new hiddeninput('id', $id);

        $form->addToForm($fileInput->show().' '.$button->show().$filename->show().$hiddenInput->show());

        // Append JavaScript
        $this->addJS();

        return $form->show().'<div id="div_upload_'.$id.'" style="display:none;">'.$objIcon->show().' Upload in Progress</div><div id="uploadresults"></div><div id="updateform"></div>'.$objIframe->show();
    }

    /**
    * Method to append JavaScript to the header
    *
    * These are run when the forms are submitted.
    */
    private function addJS()
    {
        $this->appendArrayVar('headerParams', '<script type="text/javascript">
// <![CDATA[

function doUpload(id)
{
    if (document.forms[\'uploadfile_\'+id].fileupload.value == \'\') {
        alert(\'Please select a file\');
    } else {
        document.getElementById(\'form_upload_\'+id).style.display=\'none\';
        document.getElementById(\'div_upload_\'+id).style.display=\'block\';
        document.getElementById(\'uploadresults\').style.display=\'none\';
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
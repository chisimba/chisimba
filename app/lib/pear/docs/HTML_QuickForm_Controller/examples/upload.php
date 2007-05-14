<?php
/**
* Example for HTML_QuickForm_Controller
* Handling file uploads and dynamic form generation
*
* $Id$
*
* @author Bertrand Mansion <bmansion@mamasam.com>
*/

//
// For this example to work, you'll need to create an 'uploads' directory
// in the directory where this script is located and give write permissions
// on it to your webserver.
//

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Action/Display.php';

// Start the session

session_start();

// Class for first page
// Will propose 2 layouts.
// The selection will change the 2nd page.

class Page_CMS_Layout extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $image = '<div class="image">O</div>';
        $text = '<div class="text">In the next page, you will write your text in this box. The other box besides will contain the picture that will have to be uploaded.</div>';

        $radios[0] =& $this->createElement('radio', null, $image.$text, '&nbsp;', 'A');
        $radios[1] =& $this->createElement('radio', null, $text.$image, '&nbsp;', 'B');
        $this->addGroup($radios, 'layout', 'Choose a layout');

        $buttons[0] =& $this->createElement('submit', $this->getButtonName('next'), 'Next step >>');
        $this->addGroup($buttons, 'buttons', '', '&nbsp', false);

        $this->setDefaultAction('next');
    }
}

// Class for second page
// Layout will reflect choices made in the first page.

class Page_CMS_Fill extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->registerRule('isImage',  'callback', '_ruleIsImage', get_class($this));

        $text  =& $this->createElement('textarea', 'content', 'Type text here...', array('cols'=>30, 'rows'=>4));
        $upped =& $this->createElement('file', 'file', 'Upload your image here...');

        if ($this->controller->exportValue('page1', 'layout') == 'A') {
            $this->addGroup(array($upped, $text), 'contents', 'Enter the contents', null, false);
        } else {
            $this->addGroup(array($text, $upped), 'contents', 'Enter the contents', null, false);
        }

        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('back'), '<< Previous step');
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('upload'), 'Preview >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);

        $rules['file'][0] = array('Must be *.jpg, *.gif or *.png', 'filename', '/\.(jpe?g|gif|png)$/i');
        $rules['file'][1] = array('The file must be an image', 'isImage');

        $this->addGroupRule('contents', $rules);

        $this->setDefaultAction('upload');
    }

    function _ruleIsImage($data)
    {
        if (
              ((isset($data['error']) && 0 == $data['error']) ||
               (!empty($data['tmp_name']) && 'none' != $data['tmp_name'])) &&
              is_uploaded_file($data['tmp_name'])
           ) {
            $info = @getimagesize($data['tmp_name']);
            return is_array($info) && (1 == $info[2] || 2 == $info[2] || 3 == $info[2]);
        } else {
            return true;
        }
    }
}

// Class for third page
// Will show the preview for the text and the image

class Page_CMS_Preview extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $data =& $this->controller->container();
        if (!empty($data['_upload'])) {
            $image = '<img src="uploads/'.$data['_upload'].'" alt="uploaded image" />';
        } else {
            $image = '';
        }
        $text   =  wordwrap($this->controller->exportValue('page2', 'content'), 50, '<br />');
        $theTxt =& HTML_QuickForm::createElement('static', 'thetext', 'Your text...', $text);
        $theImg =& HTML_QuickForm::createElement('static', 'thefile', 'Your file...', $image);

        if ($this->controller->exportValue('page1', 'layout') == 'A') {
            $this->addGroup(array($theImg, $theTxt), 'contents', 'Your contents', null, false);
        } else {
            $this->addGroup(array($theTxt, $theImg), 'contents', 'Your contents', null, false);
        }

        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('back'), '<< Previous step');
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('next'), 'Finish');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);

        $this->setDefaultAction('next');
    }
}

// Special action for dealing with uploads

class ActionUpload extends HTML_QuickForm_Action
{
    function perform(&$page, $actionName)
    {
        // like in Action_Next
        $page->isFormBuilt() or $page->buildForm();

        $pageName =  $page->getAttribute('id');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        if (PEAR::isError($valid = $page->validate())) {
            return $valid;
        }
        $data['valid'][$pageName] = $valid;

        if (!$data['valid'][$pageName]) {
            return $page->handle('display');
        }

        // get the element containing the upload
        $group    =& $page->getElement('contents');
        $elements =& $group->getElements();
        foreach (array_keys($elements) as $key) {
            if ('file' == $elements[$key]->getType()) {
                break;
            }
        }

        // move the file and store the data
        if ($elements[$key]->isUploadedFile()) {
            $elements[$key]->moveUploadedFile('./uploads/');
            $value = $elements[$key]->getValue();
            if (!empty($data['_upload'])) {
                @unlink('./uploads/' . $data['_upload']);
            }
            $data['_upload'] = basename($value['name']);
        }

        // redirect to next page
        $next =& $page->controller->getPage($page->controller->getNextName($pageName));
        $next->handle('jump');
    }
}

// Class for form rendering

class ActionDisplay extends HTML_QuickForm_Action_Display
{
    function _renderForm(&$page)
    {
        require_once 'HTML/Template/Sigma.php';
        require_once 'HTML/QuickForm/Renderer/ITDynamic.php';

        $tpl =& new HTML_Template_Sigma('./templates');
        $tpl->loadTemplateFile('upload.html');

        $renderer =& new HTML_QuickForm_Renderer_ITDynamic($tpl);
        $renderer->setElementBlock(array(
           'layout'     => 'qf_layout',
           'buttons'    => 'qf_buttons',
           'contents'   => 'qf_group_table'
        ));

        $page->accept($renderer);
        $tpl->show();
    }
}

// Class for form processing

class ActionProcess extends HTML_QuickForm_Action
{
    function perform(&$page, $actionName)
    {
        $values = $page->controller->exportValues();
        echo '<pre>';
        var_dump($values);
        echo '</pre>';
        $data =& $page->controller->container();
        echo '<pre>';
        var_dump($data['_upload']);
        echo '</pre>';
    }
}

$wizard =& new HTML_QuickForm_Controller('uploadWizard', true);
$wizard->addPage(new Page_CMS_Layout('page1'));
$wizard->addPage(new Page_CMS_Fill('page2'));
$wizard->addPage(new Page_CMS_Preview('page3'));

$wizard->setDefaults(array('layout' => 'A'));

$wizard->addAction('upload',  new ActionUpload());
$wizard->addAction('display', new ActionDisplay());
$wizard->addAction('process', new ActionProcess());

$wizard->run();
?>

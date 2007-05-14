<?php
/**
 * Example 2 for HTML_QuickForm_Controller: Wizard
 * 
 * $Id$
 */

require_once 'HTML/QuickForm/Controller.php';

// Load some default action handlers
require_once 'HTML/QuickForm/Action/Next.php';
require_once 'HTML/QuickForm/Action/Back.php';
require_once 'HTML/QuickForm/Action/Jump.php';
require_once 'HTML/QuickForm/Action/Display.php';

// Start the session, form-page values will be kept there
session_start();

class PageFirst extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'Wizard page 1 of 3');

        $radio[] = &$this->createElement('radio', null, null, 'Yes', 'Y');
        $radio[] = &$this->createElement('radio', null, null, 'No', 'N');
        $this->addGroup($radio, 'iradYesNo', 'Are you absolutely sure?');

        $this->addElement('submit',     $this->getButtonName('next'), 'Next >>');

        $this->addRule('iradYesNo', 'Check Yes or No', 'required');

        $this->setDefaultAction('next');
    }
}

class PageSecond extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'Wizard page 2 of 3');

        $name['last']  = &$this->createElement('text', 'last', null, array('size' => 30));
        $name['first'] = &$this->createElement('text', 'first', null, array('size' => 20));
        $this->addGroup($name, 'name', 'Name (last, first):', ',&nbsp;');

        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('back'), '<< Back');
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('next'), 'Next >>');
        $this->addGroup($prevnext, null, '', '&nbsp;', false);
        
        $this->addGroupRule('name', array('last' => array(array('Last name is required', 'required'))));

        $this->setDefaultAction('next');
    }
}

class PageThird extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'Wizard page 3 of 3');

        $this->addElement('textarea',   'itxaTest', 'Parting words:', array('rows' => 5, 'cols' => 40));

        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('back'), '<< Back');
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('next'), 'Finish');
        $this->addGroup($prevnext, null, '', '&nbsp;', false);

        $this->addRule('itxaTest', 'Say something!', 'required');

        $this->setDefaultAction('next');
    }
}



class ActionProcess extends HTML_QuickForm_Action
{
    function perform(&$page, $actionName)
    {
        echo "Submit successful!<br>\n<pre>\n";
        var_dump($page->controller->exportValues());
        echo "\n</pre>\n";
    }
}

$wizard =& new HTML_QuickForm_Controller('Wizard');
$wizard->addPage(new PageFirst('page1'));
$wizard->addPage(new PageSecond('page2'));
$wizard->addPage(new PageThird('page3'));

// We actually add these handlers here for the sake of example
// They can be automatically loaded and added by the controller
$wizard->addAction('display', new HTML_QuickForm_Action_Display());
$wizard->addAction('next', new HTML_QuickForm_Action_Next());
$wizard->addAction('back', new HTML_QuickForm_Action_Back());
$wizard->addAction('jump', new HTML_QuickForm_Action_Jump());

// This is the action we should always define ourselves
$wizard->addAction('process', new ActionProcess());

$wizard->run();
?>

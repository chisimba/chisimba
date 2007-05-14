<?php
/**
 * Example 1 for HTML_QuickForm_Controller: using the Controller
 * infrastructure to create and process the basic single-page form
 * 
 * $Id$
 */

require_once 'HTML/QuickForm/Controller.php';

// Load some default action handlers
require_once 'HTML/QuickForm/Action/Submit.php';
require_once 'HTML/QuickForm/Action/Display.php';


class SimplePage extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'Controller example 1: a simple form');
        $this->addElement('text',       'tstText', 'Please enter something:', array('size'=>20, 'maxlength'=>50));
        // Bind the button to the 'submit' action
        $this->addElement('submit',     $this->getButtonName('submit'), 'Send');

        $this->applyFilter('tstText', 'trim');
        $this->addRule('tstText', 'Pretty please!', 'required');

        $this->setDefaultAction('submit');
    }
}


class ActionProcess extends HTML_QuickForm_Action
{
    function perform(&$page, $actionName)
    {
        echo "Submit successful!<br>\n<pre>\n";
        var_dump($page->exportValues());
        echo "\n</pre>\n";
    }
}

$page =& new SimplePage('page1');

// We actually add these handlers here for the sake of example
// They can be automatically loaded and added by the controller
$page->addAction('display', new HTML_QuickForm_Action_Display());
$page->addAction('submit', new HTML_QuickForm_Action_Submit());

// This is the action we should always define ourselves
$page->addAction('process', new ActionProcess());

$controller =& new HTML_QuickForm_Controller('simpleForm');
$controller->addPage($page);
$controller->run();
?>

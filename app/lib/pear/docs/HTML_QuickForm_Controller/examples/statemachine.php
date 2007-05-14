<?php
/**
 * Example for HTML_QuickForm_Controller: Statemachine
 * Going to either of the two pages based on user input
 *
 * @author Donald Lobo <lobo@groundspring.org>
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

class PageFirstActionNext extends HTML_QuickForm_Action_Next
{
    function perform(&$page, $actionName)
    {
      // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();
        $pageName =  $page->getAttribute('id');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        if (PEAR::isError($valid = $page->validate())) {
            return $valid;
        }
        $data['valid'][$pageName] = $valid;

        // Modal form and page is invalid: don't go further
        if ($page->controller->isModal() && !$data['valid'][$pageName]) {
            return $page->handle('display');
        }

        $nextName = $data['values'][$pageName]['iradPageAB'];
        if (empty($nextName)) {
            $nextName = 'page1';
        }
        if ($nextName == 'page2a') {
            $data['valid']['page2b'] = true;
        } else {
            $data['valid']['page2a'] = true;
        }
        $next =& $page->controller->getPage($nextName);
        $next->handle('jump');
    }
}


class PageSecondActionNext extends HTML_QuickForm_Action_Next
{
    function perform(&$page, $actionName)
    {
      // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();
        $pageName =  $page->getAttribute('id');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        if (PEAR::isError($valid = $page->validate())) {
            return $valid;
        }
        $data['valid'][$pageName] = $valid;

        // Modal form and page is invalid: don't go further
        if ($page->controller->isModal() && !$data['valid'][$pageName]) {
            return $page->handle('display');
        }

        // More pages?
        $next =& $page->controller->getPage('page3');
        $next->handle('jump');
    }
}


class PageSecondActionBack extends HTML_QuickForm_Action_Back
{
    function perform(&$page, $actionName)
    {
        // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();
        $pageName =  $page->getAttribute('id');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        if (!$page->controller->isModal()) {
            if (PEAR::isError($valid = $page->validate())) {
                return $valid;
            }
            $data['valid'][$pageName] = $valid;
        }

        $prev =& $page->controller->getPage('page1');
        $prev->handle('jump');
    }
}


class PageThirdActionBack extends HTML_QuickForm_Action_Back
{
    function perform(&$page, $actionName)
    {
        // save the form values and validation status to the session
        $page->isFormBuilt() or $page->buildForm();
        $pageName =  $page->getAttribute('id');
        $data     =& $page->controller->container();
        $data['values'][$pageName] = $page->exportValues();
        if (!$page->controller->isModal()) {
            if (PEAR::isError($valid = $page->validate())) {
                return $valid;
            }
            $data['valid'][$pageName] = $valid;
        }

        $prev =& $page->controller->getPage($data['values']['page1']['iradPageAB']);
        $prev->handle('jump');
    }
}


class PageFirst extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'StateMachine page 1 of 3');

        $radio[] = &$this->createElement('radio', null, null, 'Page 2A', 'page2a');
        $radio[] = &$this->createElement('radio', null, null, 'Page 2B', 'page2b');
        $this->addGroup($radio, 'iradPageAB', 'Proceed to page:');

        $this->addElement('submit',     $this->getButtonName('next'), 'Next >>');

        $this->addRule('iradPageAB', 'Select the page', 'required');

        $this->setDefaultAction('next');
    }
}

class PageSecondAlpha extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'StateMachine page 2A of 3');

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

class PageSecondBeta extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'StateMachine page 2B of 3');

        $this->addElement('textarea',   'itxaTest', 'Description:', array('rows' => 5, 'cols' => 40));

        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('back'), '<< Back');
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('next'), 'Next >>');
        $this->addGroup($prevnext, null, '', '&nbsp;', false);

        $this->addRule('itxaTest', 'Say something!', 'required');

        $this->setDefaultAction('next');
    }
}

class PageThird extends HTML_QuickForm_Page
{
    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header',     null, 'StateMachine page 3 of 3');

        $this->addElement('text',   'fourthTextBox', 'Final Text:', array('size' => 20, 'maxlen' => 40));

        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('back'), '<< Back');
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('next'), 'Finish');
        $this->addGroup($prevnext, null, '', '&nbsp;', false);

        $this->addRule('fourthTextBox', 'Say something!', 'required');

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

$statemachine =& new HTML_QuickForm_Controller('StateMachine');

$page1  =& new PageFirst('page1');
$page2a =& new PageSecondAlpha('page2a');
$page2b =& new PageSecondBeta('page2b');
$page3  =& new PageThird('page3');

$statemachine->addPage($page1);
$statemachine->addPage($page2a);
$statemachine->addPage($page2b);
$statemachine->addPage($page3);

$page1->addAction('next', new PageFirstActionNext());
$page2a->addAction('next', new PageSecondActionNext());
$page2a->addAction('back', new PageSecondActionBack());
$page2b->addAction('next', new PageSecondActionNext());
$page2b->addAction('back', new PageSecondActionBack());
$page3->addAction('back', new PageThirdActionBack());

// We actually add these handlers here for the sake of example
// They can be automatically loaded and added by the controller
$statemachine->addAction('display', new HTML_QuickForm_Action_Display());
$statemachine->addAction('next', new HTML_QuickForm_Action_Next());
$statemachine->addAction('back', new HTML_QuickForm_Action_Back());
$statemachine->addAction('jump', new HTML_QuickForm_Action_Jump());

// This is the action we should always define ourselves
$statemachine->addAction('process', new ActionProcess());

$statemachine->run();
?>

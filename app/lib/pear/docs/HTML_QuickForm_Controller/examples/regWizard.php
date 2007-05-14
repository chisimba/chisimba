<?php
/**
 * Example for HTML_QuickForm_Controller: registration wizard
 * 
 * $Id$
 * 
 * @author Bertrand Mansion <bmansion@mamasam.com>
 */

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Action/Display.php';

// Start the session

session_start();

// Rule for passwords comparison
function comparePassword($fields)
{
    if (strlen($fields['password1']) && strlen($fields['password2']) && 
        $fields['password1'] != $fields['password2']) {
        return array('password1' => 'Passwords are not the same');
    }
    return true;
}

// Class for first page : credentials

class Page_Account_Credentials extends HTML_QuickForm_Page
{

    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header', 'credential_header', 'Your credentials');
        
        $this->addElement('text', 'username', 'Your email address :', array('size' => 30, 'maxlength' => 63));
        $this->addElement('password', 'password1', 'Your password :', array('size' => 16, 'maxlength' => 32));
        $this->addElement('password', 'password2', 'Confirm your password :', array('size' => 16, 'maxlength' => 32));
        
        $buttons[0] =& HTML_QuickForm::createElement('button', 'cancel', 'Cancel', array('onclick'=>"javascript:location.href='http://pear.php.net/package/HTML_QuickForm';"));
        $buttons[1] =& HTML_QuickForm::createElement('submit', $this->getButtonName('next'), 'Next step >>');
        $this->addGroup($buttons, 'buttons', '', '&nbsp', false);
    
        $this->addRule('username', 'Your email address is required', 'required', null, 'client');
        $this->addRule('username', 'Your email address is incorrect', 'email', null, 'client');

        $this->addRule('password1', 'The password is required', 'required', '', 'client');
        $this->addRule('password1', 'The password is too short: 6 chars minimum', 'minlength', 6, 'client');

        $this->addRule('password2', 'The password confirmation is required', 'required', '', 'client');

        $this->addFormRule('comparePassword');

        $this->setDefaultAction('next');
    }
}

// Class for second page : user data

class Page_Account_Information extends HTML_QuickForm_Page
{

    function buildForm()
    {
        $this->_formBuilt = true;

        $this->addElement('header', 'data_header', 'Your personal data');
        
        $name[] = &HTML_QuickForm::createElement('text', 'first', 'Firstname', array('size' => 16, 'maxlength' => 63));
        $name[] = &HTML_QuickForm::createElement('text', 'last', 'Lastname', array('size' => 16, 'maxlength' => 63));
        $this->addGroup($name, 'name', 'Your name :', null, false);
        
        $this->addElement('text', 'company', 'Your company :',  array('size' => 30, 'maxlength' => 63));
        
        $this->addElement('text', 'address1', 'Your address :',  array('size' => 30, 'maxlength' => 63));
        $this->addElement('text', 'address2', '&nbsp;',  array('size' => 30, 'maxlength' => 63));
        
        $this->addElement('text', 'zip', 'Zip code :',  array('size' => 16, 'maxlength' => 16));
        $this->addElement('text', 'city', 'City :',  array('size' => 30, 'maxlength' => 63));
        
        $countries = array('FR'=>'France', 'GE'=>'Germany', 'RU'=>'Russia', 'UK'=>'United Kingdom');
        $this->addElement('select', 'country', 'Your country :', $countries);
        
        $this->addElement('text', 'phone', 'Phone number :',  array('size' => 16, 'maxlength' => 16));
        $this->addElement('text', 'fax', 'Fax number :',  array('size' => 16, 'maxlength' => 16));
    
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('back'), '<< Previous step');
        $prevnext[] =& $this->createElement('submit',   $this->getButtonName('next'), 'Finish');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        
        $this->addGroupRule('name', 'First and last names are required', 'required');
        $this->addRule('company', 'Company is required', 'required');
        $this->addRule('address1', 'Address is required', 'required');
        $this->addRule('city', 'City is required', 'required');

        $this->setDefaultAction('next');
    }
}

// Class for form rendering

class ActionDisplay extends HTML_QuickForm_Action_Display
{
    function _renderForm(&$page) 
    {
        $renderer =& $page->defaultRenderer();

        $page->setRequiredNote('<font color="#FF0000">*</font> shows the required fields.');
        $page->setJsWarnings('Those fields have errors :', 'Thanks for correcting them.');
        
        $renderer->setFormTemplate('<table width="450" border="0" cellpadding="3" cellspacing="2" bgcolor="#CCCC99"><form{attributes}>{content}</form></table>');
        $renderer->setHeaderTemplate('<tr><td style="white-space:nowrap;background:#996;color:#ffc;" align="left" colspan="2"><b>{header}</b></td></tr>');
        $renderer->setGroupTemplate('<table><tr>{content}</tr></table>', 'name');
        $renderer->setGroupElementTemplate('<td>{element}<br /><span style="font-size:10px;"><!-- BEGIN required --><span style="color: #f00">*</span><!-- END required --><span style="color:#996;">{label}</span></span></td>', 'name');

        $page->accept($renderer);
        echo $renderer->toHtml();
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
    }
}


$wizard =& new HTML_QuickForm_Controller('regWizard', true);
$wizard->addPage(new Page_Account_Credentials('page1'));
$wizard->addPage(new Page_Account_Information('page2'));

$wizard->setDefaults(array('country' => 'FR'));

$wizard->addAction('display', new ActionDisplay());
$wizard->addAction('process', new ActionProcess());

$wizard->run();
?>

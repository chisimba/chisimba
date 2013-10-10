<?php

/**
* Class to show the login form for the Discussion
* It only appears if you use the thetha_page_tpl.php Page Template
* @author Tohir Solomons
*/
class discussionlogin extends object
{

    /**
    * Constructor
    */
    function init()
    {  
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
    * Method to output the links
    */
    function show()
    {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        
        $form = new form ('login', $this->uri(array('action'=>'login'), 'security'));
        
        $label = new label($this->objLanguage->languageText('word_username', 'Username').': ', 'input_username');
        $username = new textinput('username');
        
        $passwordlabel = new label($this->objLanguage->languageText('word_password', 'Password').': ', 'input_password');
        $password = new textinput('password');
        $password->fldType = 'password';
        
        $button = new button ('submitform');
        $button->cssClass = 'login';
        $button->value = $this->objLanguage->languageText('word_login', 'Login');
        $button->setToSubmit();
        
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->width = '';
        $table->cellpadding = 5;
        
        $table->startRow();
        
        $table->addCell($label->show());
        $table->addCell($username->show());
        
        $table->endRow();
        
        $table->startRow();
        
        $table->addCell($passwordlabel->show());
        $table->addCell($password->show());
        
        $table->endRow();
        
        $objConfig = $this->getObject('config', 'config');
        
        if ($objConfig->useLDAP()){
         	$table->startRow();
                
                $table->addCell('<input id="LdapCheckbox" type="checkbox" name="useLdap" value="yes" class="transparentbgnb">'
            .'<label for="LdapCheckbox">'
            .$this->objLanguage->languageText("phrase_networkid")
            .'</label>', NULL, NULL, NULL,  'colspan="2"');
                
                $table->endRow();
        }
        
        
        $str = $table->show().$button->show();
        
        //$ldap = new hiddeninput('useLdap', 'no');.$ldap->show()
        
        $form->addToForm($str);
        
        $link = new link ($this->uri(array('action'=>'register'), 'useradmin'));
        $link->link = 'Register on Site';
        
        
        
        $fieldset = $this->getObject('fieldset', 'htmlelements');
        $fieldset->addContent($form->show().'<p>'.$link->show().'</p>');
        
        return '<h3>Discussion Login</h3>'.$fieldset->show();
    }


}

?>
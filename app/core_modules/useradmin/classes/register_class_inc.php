<?php
/**
* This small class makes the functionality for sending email to
* a newly-registered user available to other modules
* @author James Scoble
* @version $Id$
* @copyright 2004
* @license GNU GPL
*/

class register extends object
{

    /**
    * Method to compose and send an email
    * @param string $firstname
    * @param string $surname
    * @param string $userId
    * @param string $username
    * @param string $title
    * @param string $email
    * @param string $password
    * @param string $accesslevel
    */
    public function sendRegisterInfo($firstname,$surname,$userId,$username,$title,$email,$password,$accesslevel)
    {
        $emailtext=str_replace('SURNAME',$surname,str_replace('FIRSTNAME',$firstname,$this->objLang->languageText('mod_useradmin_greet1')))."\n"
        .$this->objLang->languageText('mod_useradmin_greet2')."\n"
        .$this->objLang->languageText('mod_useradmin_greet3')."\n"
        .$this->objLang->languageText('mod_useradmin_greet4')."\n"
        .$this->objLang->languageText('word_userid').": $userId\n"
        .$this->objLang->languageText('word_surname').": $surname\n"
        .$this->objLang->languageText('phrase_firstname').": $firstname\n"
        .$this->objLang->languageText('word_title').": $title\n"
        .$this->objLang->languageText('word_username').": $username\n"
        .$this->objLang->languageText('word_password').": $password\n"
        .$this->objLang->languageText('phrase_emailaddress').": $email\n"
        .$this->objLang->languageText('word_sincerely')."\n"
        .$this->objLang->languageText('mod_useradmin_greet5')."\n";
        $subject=$this->objLang->languageText('mod_useradmin_greet6');
        @mail($email,$subject,$emailtext);
    }


}
?>

<?php

echo '<h1>'.$this->objLanguage->languageText('mod_security_newpasswordgenerated', 'security', 'New Password Generated').'</h1>';

echo '<p>'.$this->objLanguage->languageText('mod_security_newpasswordgeneratedsent', 'security', 'We have generated a new password for you, and sent it to your email address').' ('.$user['emailaddress'].').</p>';

echo '<p>'.$this->objLanguage->languageText('mod_security_checkemailaccount', 'security', 'Please check your email address and try logging in again').'.</p>';

echo '<p>&nbsp; - '.$this->objConfig->getSiteName().' '.$this->objLanguage->languageText('mod_security_registrationsystem', 'security', 'Registration System').'</p>';


?>
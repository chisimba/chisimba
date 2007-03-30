<?php

echo '<h1>'.$this->objLanguage->languageText('mod_userregistration_registrationsuccess', 'userregistration', 'You have successfully registered on the site').'</h1>';

$objBizCard = $this->getObject('userbizcard', 'useradmin');
$objBizCard->setUserArray($user);

echo $objBizCard->show();

echo '<p>'.$this->objLanguage->languageText('mod_userregistration_emailsent', 'userregistration', 'An email has been sent to your email address with your details').':</p>';

echo '<ul>';
echo '<li><strong>'.$this->objLanguage->languageText('word_username', 'system').'</strong>: '.$user['username'].'</li>';
echo '<li><strong>'.$this->objLanguage->languageText('word_password', 'system').'</strong>: ***** </li>';
echo '</ul>';

echo '<br /><br />';
?>
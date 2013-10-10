<?php
$str = '<h1>' . $this->objLanguage->languageText('mod_userregistration_registrationsuccess', 'userregistration', 'You have successfully registered on the site') . '</h1>';
$objBizCard = $this->getObject('userbizcard', 'useradmin');
$objBizCard->setUserArray($user);
$str.= $objBizCard->show();
$str.= '<p>' . $this->objLanguage->languageText('mod_userregistration_emailsent', 'userregistration', 'An email has been sent to your email address with your details') . ':</p>';
$str.= '<ul>';
$str.= '<li><strong>' . $this->objLanguage->languageText('word_username', 'system') . '</strong>: ' . $user['username'] . '</li>';
//echo '<li><strong>'.$this->objLanguage->languageText('word_password', 'system').'</strong>: ***** </li>';
$str.= '</ul>';
$str.= '<br /><br />';
echo $str;
?>

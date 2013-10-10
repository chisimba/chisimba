<?php

echo "<div class='CMS-frontpage-item'>" . $content . "</div>";
if($fromadmin)
{
    $objLanguage = $this->newObject('language', 'language');
    $objBackToAdminLink = $this->newObject('link', 'htmlelements');
    $objBackToAdminLink->link($this->uri(array('action'=>'viewsection', 'id'=>$sectionId), 'cmsadmin'));
    $objBackToAdminLink-> link = $objLanguage->languageText('mod_cms_backtoadmin', 'cms');

    echo '<p>'.$objBackToAdminLink->show().'</p>';
}
?>

<?php
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressSearch', TRUE);
$this->setVar('footerStr', '');
$lbView = $this->objLanguage->languageText('mod_blog_clicklinkviewselectedformat', 'blog');
$objLayer = $this->newObject('layer', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$str = '<b>' . $lbView . ': </b>';
$objLink = new link($feed);
$objLink->link = $feed;
$str.= '<p>' . $objLink->show() . '</p>';
$objLayer->str = $str;
$objLayer->padding = '5px';
echo $objLayer->show();
?>
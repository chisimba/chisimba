<?php
/**
 * Template file for the keepsessionalive popup window
 */
$config = $this->getObject('altconfig', 'config');
$skinroot = $config->getskinRoot();
$defaultskin = $config->getdefaultSkin();
$siteRoot = $config->getsiteRoot();
$siteRootPath = $config->getsiteRootPath();

$objSkin = $this->getObject('skin', 'skin');

/*
 Get the background Image for Keep Session Alive
 
 If not found in the current skin, it checks the default skin
 If not found in the default skin, it selects one from /_common/
 
*/
$path1 = $siteRootPath . $skinroot . $objSkin->getSkin() . '/images/keepsessionalive.gif';
$path2 = $siteRootPath . $skinroot . $defaultskin . '/images/keepsessionalive.gif';
if (file_exists($path1)) {
    $path = $siteRoot . $skinroot . $objSkin->getSkin()  . '/images/keepsessionalive.gif';
} else if (file_exists($path2)) {
    $path = $siteRoot . $skinroot . $defaultskin . '/images/keepsessionalive.gif';
} else {
    $path = $siteRoot . $skinroot . '/_common/images/keepsessionalive.gif';
} 

// Prepare as a style, and append to header
$background = '
<style>
body { 
    background-image: url('.$path.');
    background-repeat: no-repeat;
    background-position: top left;
    padding-top: 50px;
}
</style>

';
// Append to Header
$this->appendArrayVar('headerParams', $background);


echo '<p align="center">' . $this->objLanguage->languageText('mod_keepsessionalive_keepalive','keepsessionalive') . '</p>';
echo '<p align="center"><a href="javascript:;" onclick="window.close();">' . $this->objLanguage->languageText('mod_keepsessionalive_closewindow','keepsessionalive').'</a></p>';


$objUser = $this->getObject('user', 'security');
if (!($objUser->isLoggedIn())) {

    ?>
    <b>you should not see this!</b>
    <script language="javascript">
    window.close();
    </script>
<?php
} 

?>

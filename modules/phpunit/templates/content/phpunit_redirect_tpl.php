<?php
/**
* @package redirect
*/

/**
* Redirect the user based on the phpunit parameters
*/

$objMap = $this->getObject('dbmap', 'phpunit');

$requestUri = $_SERVER['REQUEST_URI'];
$siteRoot = $this->objConf->getItem('KEWL_SITE_ROOT');

//Make comparisons relative to the site root
$siteRootParts = parse_url($siteRoot);
$compareStr = str_replace($siteRootParts['path'], '', $requestUri);

//Get Target URL to direct to
$targetUrl = $objMap->getTarget($compareStr);

log_debug('REQUEST : '.$requestUri);
log_debug('SITEROOT: '.$siteRoot);
log_debug('TARGET  : '.$targetUrl);

//TODO: Get click through stats here

if (preg_match('/\?module=phpunit&action=redirect/', $targetUrl)){
    echo "<b>Short URL Error Caught</b>: <br/> &nbsp;&nbsp;&nbsp;-Request will never complete because the redirect handler can't be invoked on its'self";
    exit;
}
header('Location: '. $targetUrl);

?>
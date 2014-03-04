<?php

ini_set('include_path', 'resources/swordapp-php-library-0.9');

require_once 'packager_mets_swap.php';
require_once 'swordappclient.php';

$package = new PackagerMetsSwap('tmp', 'test', 'tmp', 'test.zip');
$package->addCreator('Charl van Niekerk');
$package->setTitle('Test Title');
$package->setAbstract('Test Abstract');
$package->create();

// Add a file to the package
$package->addFile($filename, $mimetype);

$client = new SWORDAPPClient();
$client->deposit('http://localhost:8080/sword/collection:open', 'fedoraAdmin', 'charlvn', 'Charl van Niekerk', 'tmp/test.zip', 'http://purl.org/net/sword-types/METSDSpaceSIP', 'application/zip', false, true);

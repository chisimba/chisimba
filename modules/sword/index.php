<?php

require_once 'resources/swordapp-php-library-0.9/swordappclient.php';
$sac = new SWORDAPPClient();
$sdr = $sac->servicedocument('http://localhost:8080/sword', 'fedoraAdmin', 'charlvn', 'Charl van Niekerk');

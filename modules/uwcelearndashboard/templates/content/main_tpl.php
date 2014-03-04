<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2009
 */

$objSiteLoad = $this->newObject('siteload');
$userCount = $objSiteLoad->Count();
echo "Users currently logged in: ".$userCount;

?>
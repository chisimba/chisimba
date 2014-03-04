<?php
header ( "Content-Type: application/xhtml+xml;charset=utf-8" );
$objImView = $this->getObject ( 'jbviewer' );
echo $objImView->renderOutputForBrowser ( $msgs );

exit ();

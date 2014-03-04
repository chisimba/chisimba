<?php
$this->requiresLogin(FALSE);
header ( "Content-Type: text/html;charset=utf-8" );

$objImView = $this->getObject ( 'sugviewer' );
echo $objImView->renderOutputForBrowser ( $records );

exit ();

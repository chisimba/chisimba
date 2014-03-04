<?php
header ( "Content-Type: text/html;charset=utf-8" );
$this->objOps = $this->getObject ( 'tweetops' );
echo $this->objOps->renderOutputForBrowser ( $msgs );

exit ();

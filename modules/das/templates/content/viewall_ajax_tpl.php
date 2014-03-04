<?php
header("Content-Type: text/html;charset=utf-8");
$objImView = $this->getObject('imviewer', 'im');
echo $objImView->renderOutputForBrowser($msgs);

exit;

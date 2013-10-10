<?php
echo $this->objGetall->viewSingleAssertion($assertionId);
echo $this->objGetall->viewPartForm('singleassertion', 'assertionId', $assertionId );
echo $this->objGetall->getCloseBtn();
?>

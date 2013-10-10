<?php
echo $this->objGetall->viewSingleGoal($goalId);
echo $this->objGetall->viewPartForm('singlegoal', 'goalId', $goalId );
echo $this->objGetall->getCloseBtn();
?>

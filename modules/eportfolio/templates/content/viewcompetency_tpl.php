<?php
echo $this->objGetall->viewSingleCompetency($competencyId);
echo $this->objGetall->viewPartForm('singlecompetency', 'competencyId', $competencyId );
echo $this->objGetall->getCloseBtn();
?>

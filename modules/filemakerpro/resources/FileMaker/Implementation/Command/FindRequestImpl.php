<?php
 require_once dirname(__FILE__) . '/../CommandImpl.php';
 class FileMaker_Command_FindRequest_Implementation
{
  var $_findCriteria = array();

  var $_omit;
 function FileMaker_Command_FindRequest_Implementation()
 {
 $this->_omit = false;
}

  function addFindCriterion($Vd1148ee8, $Ve9de89b0)
 {
 $this->_findCriteria[$Vd1148ee8] = $Ve9de89b0;
}
 function setOmit($V2063c160)
 {
 $this->_omit = $V2063c160;
}

  function clearFindCriteria()
 {
 $this->_findCriteria = array();
}
}

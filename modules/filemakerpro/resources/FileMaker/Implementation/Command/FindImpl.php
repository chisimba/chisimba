<?php
  require_once dirname(__FILE__) . '/../CommandImpl.php';
 class FileMaker_Command_Find_Implementation extends FileMaker_Command_Implementation
{
  var $_findCriteria = array();
 var $Vd65662c5 = array();
 var $Va9136a07 = array();
 var $Vf951bdce;
 var $V83f28691;
 var $V85fd701e;

  var $V6da136ea;
 var $V568aa2ec;
 function FileMaker_Command_Find_Implementation($V0ab34ca9, $Vc6140495)
 {
 FileMaker_Command_Implementation::FileMaker_Command_Implementation($V0ab34ca9, $Vc6140495);
}
 function &execute()
 { 
 $V21ffce5b = $this->_getCommandParams(); 
 $this->_setSortParams($V21ffce5b);
$this->_setRangeParams($V21ffce5b);
$this->_setRelatedSetsFilters($V21ffce5b);   
 if (count($this->_findCriteria) || $this->_recordId) {
 $V21ffce5b['-find'] = true;
} else {
 $V21ffce5b['-findall'] = true;
} 
 if ($this->_recordId) {
 $V21ffce5b['-recid'] = $this->_recordId;
} 
 if ($this->Vf951bdce) {
 $V21ffce5b['-lop'] = $this->Vf951bdce;
} 
 foreach ($this->_findCriteria as $Vd1148ee8 => $Ve9de89b0) {
 $V21ffce5b[$Vd1148ee8] = $Ve9de89b0;
$V21ffce5b[$Vd1148ee8 . '.op'] = 'bw';
} 
 $V0f635d0e = $this->_fm->_execute($V21ffce5b);
if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e;
} 
 return $this->_getResult($V0f635d0e);
}
 function addFindCriterion($Vd1148ee8, $Ve9de89b0)
 {
 $this->_findCriteria[$Vd1148ee8] = $Ve9de89b0;
}
 function clearFindCriteria()
 {
 $this->_findCriteria = array();
}
 function addSortRule($Vd1148ee8, $Vffbd028a, $V70a17ffa = null)
 {
 $this->Vd65662c5[$Vffbd028a] = $Vd1148ee8;
if ($V70a17ffa !== null) {
 $this->Va9136a07[$Vffbd028a] = $V70a17ffa;
}
}
 function clearSortRules()
 {
 $this->Vd65662c5= array();
$this->Va9136a07= array();
}
 function setLogicalOperator($V4b583376)
 {
 switch ($V4b583376) {
 case FILEMAKER_FIND_AND:
 case FILEMAKER_FIND_OR:
 $this->Vf951bdce= $V4b583376;
break;
}
}
 function setRange($V08b43519 = 0, $V2ffe4e77 = null)
 {
 $this->V83f28691= $V08b43519;
$this->V85fd701e= $V2ffe4e77;
}
 function getRange()
 {
 return array('skip' => $this->V83f28691,
 'max' => $this->V85fd701e);
}
 function setRelatedSetsFilters($Vdba51d08, $V01a8ebbf = null)
 {
 $this->V6da136ea= $Vdba51d08;
$this->V568aa2ec= $V01a8ebbf;
}
 function getRelatedSetsFilters()
 {
 return array('relatedsetsfilter' => $this->V6da136ea,
 'relatedsetsmax' => $this->V568aa2ec);
}
 function _setRelatedSetsFilters(&$V21ffce5b)
 {
 if ($this->V6da136ea) {
 $V21ffce5b['-relatedsets.filter'] = $this->V6da136ea;
}
if ($this->V568aa2ec) {
 $V21ffce5b['-relatedsets.max'] = $this->V568aa2ec;
}
}
 function _setSortParams(&$V21ffce5b)
 {
 foreach ($this->Vd65662c5 as $Vffbd028a => $Vd1148ee8) {
 $V21ffce5b['-sortfield.' . $Vffbd028a] = $Vd1148ee8;
}
foreach ($this->Va9136a07 as $Vffbd028a => $V70a17ffa) {
 $V21ffce5b['-sortorder.' . $Vffbd028a] = $V70a17ffa;
}
}
 function _setRangeParams(&$V21ffce5b)
 {
 if ($this->V83f28691) {
 $V21ffce5b['-skip'] = $this->V83f28691;
}
if ($this->V85fd701e) {
 $V21ffce5b['-max'] = $this->V85fd701e;
}
}
}

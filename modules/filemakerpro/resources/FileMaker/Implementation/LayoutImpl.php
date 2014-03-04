<?php
  require_once dirname(__FILE__) . '/../Field.php';
require_once dirname(__FILE__) . '/Parser/FMPXMLLAYOUT.php';
 class FileMaker_Layout_Implementation
{
  var $_fm;
 var $_name;
 var $_fields = array();
 var $_relatedSets = array();
 var $_valueLists = array();
 var $_database;
 var $_extended = false;
 function FileMaker_Layout_Implementation(&$V0ab34ca9)
 {
 $this->_fm =& $V0ab34ca9;
}
 function getName()
 {
 return $this->_name;
}
 function getDatabase()
 {
 return $this->_database;
}
 function listFields()
 {
 return array_keys($this->_fields);
}
 function &getField($V972bf3f0)
 {
 if (isset($this->_fields[$V972bf3f0])) {
 return $this->_fields[$V972bf3f0];
}
return $Vcb5e100e =& new FileMaker_Error($this->_fm, 'Field Not Found');
}
 function &getFields()
 {
 return $this->_fields;
}
 function listRelatedSets()
 {
 return array_keys($this->_relatedSets);
}
 function &getRelatedSet($Vaca007a7)
 {
 if (isset($this->_relatedSets[$Vaca007a7])) {
 return $this->_relatedSets[$Vaca007a7];
}
return $Vcb5e100e =& new FileMaker_Error($this->_fm, 'RelatedSet Not Found');
}
 function &getRelatedSets()
 {
 return $this->_relatedSets;
}
 function listValueLists()
 {
 $Vb4a88417 = $this->loadExtendedInfo();
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
return array_keys($this->_valueLists);
}
 function getValueList($V993fcb1e, $Vd33e904c = null)
 {
 $Vb4a88417 = $this->loadExtendedInfo($Vd33e904c);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
return isset($this->_valueLists[$V993fcb1e]) ?
 $this->_valueLists[$V993fcb1e] : null;
}
 function getValueLists($Vd33e904c = null)
 {
 $Vb4a88417 = $this->loadExtendedInfo($Vd33e904c);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
return $this->_valueLists;
}
 function loadExtendedInfo($Vd33e904c = null)
 {
 if (!$this->_extended) {
 
 if($Vd33e904c != null){
 $V0f635d0e = $this->_fm->_execute(array('-db' => $this->_fm->getProperty('database'),
 '-lay' => $this->getName(),
 '-recid' => $Vd33e904c,
 '-view' => null),
 'FMPXMLLAYOUT');
}else{
 $V0f635d0e = $this->_fm->_execute(array('-db' => $this->_fm->getProperty('database'),
 '-lay' => $this->getName(),
 '-view' => null),
 'FMPXMLLAYOUT');

 }
$V3643b863 =& new FileMaker_Parser_FMPXMLLAYOUT($this->_fm);
$Vb4a88417 = $V3643b863->parse($V0f635d0e);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
$V3643b863->setExtendedInfo($this);
$this->_extended = true;
}
return $this->_extended;
}
}

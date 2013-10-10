<?php
  class FileMaker_Record_Implementation
{
  var $_fields = array();
 var $V5e7ec2d5 = array();
 var $_recordId;
 var $_modificationId;
 var $_layout;
 var $_fm;
 var $_relatedSets = array();
 var $_parent = null;
 function FileMaker_Record_Implementation(&$Vc6140495)
 {
 $this->_layout =& $Vc6140495;
$this->_fm =& $Vc6140495->_impl->_fm;
}
 function &getLayout()
 {
 return $this->_layout;
}
 function getFields()
 {
 return $this->_layout->listFields();
}
 function getField($V06e3d36f, $V6d786dc7 = 0)
 {
 if (!isset($this->_fields[$V06e3d36f])) {
 $this->_fm->log('Field "' . $V06e3d36f . '" not found.', FILEMAKER_LOG_INFO);
return "";
}
if (!isset($this->_fields[$V06e3d36f][$V6d786dc7])) {
 $this->_fm->log('Repetition "' . (int)$V6d786dc7 . '" does not exist for "' . $V06e3d36f . '".', FILEMAKER_LOG_INFO);
return "";
}
return htmlspecialchars($this->_fields[$V06e3d36f][$V6d786dc7]);
}

	 function getFieldUnencoded($V06e3d36f, $V6d786dc7 = 0)
 {
 if (!isset($this->_fields[$V06e3d36f])) {
 $this->_fm->log('Field "' . $V06e3d36f . '" not found.', FILEMAKER_LOG_INFO);
return "";
}
if (!isset($this->_fields[$V06e3d36f][$V6d786dc7])) {
 $this->_fm->log('Repetition "' . (int)$V6d786dc7 . '" does not exist for "' . $V06e3d36f . '".', FILEMAKER_LOG_INFO);
return "";
}
return $this->_fields[$V06e3d36f][$V6d786dc7];
}
 function getFieldAsTimestamp($V972bf3f0, $V6d786dc7 = 0)
 {
 $V2063c160 = $this->getField($V972bf3f0, $V6d786dc7);
if (FileMaker::isError($V2063c160)) {
 return $V2063c160;
}
$V06e3d36f =& $this->_layout->getField($V972bf3f0);
if (FileMaker::isError($V06e3d36f)) {
 return $V06e3d36f;
}
switch ($V06e3d36f->getResult()) {
 case 'date': 
 $V78f0805f = explode('/', $V2063c160);
if (count($V78f0805f) != 3) {
 return new FileMaker_Error($this->_fm, 'Failed to parse "' . $V2063c160 . '" as a FileMaker date value.');
}
$Vd7e6d55b = @mktime(0, 0, 0, $V78f0805f[0], $V78f0805f[1], $V78f0805f[2]);
if ($Vd7e6d55b === false) {
 return new FileMaker_Error($this->_fm, 'Failed to convert "' . $V2063c160 . '" to a UNIX timestamp.');
}
break;
case 'time': 
 $V78f0805f = explode(':', $V2063c160);
if (count($V78f0805f) != 3) {
 return new FileMaker_Error($this->_fm, 'Failed to parse "' . $V2063c160 . '" as a FileMaker time value.');
}
$Vd7e6d55b = @mktime($V78f0805f[0], $V78f0805f[1], $V78f0805f[2], 1, 1, 1970);
if ($Vd7e6d55b === false) {
 return new FileMaker_Error($this->_fm, 'Failed to convert "' . $V2063c160 . '" to a UNIX timestamp.');
}
break;
case 'timestamp':  
 $Vd7e6d55b = @strtotime($V2063c160);
if ($Vd7e6d55b === false) {
 return new FileMaker_Error($this->_fm, 'Failed to convert "' . $V2063c160 . '" to a UNIX timestamp.');
}
break;
default:
 $Vd7e6d55b = new FileMaker_Error($this->_fm, 'Only time, date, and timestamp fields can be converted to UNIX timestamps.');
break;
}
return $Vd7e6d55b;
}
 function setField($V06e3d36f, $V2063c160, $V6d786dc7 = 0)
 {
 $this->_fields[$V06e3d36f][$V6d786dc7] = $V2063c160;
$this->V5e7ec2d5[$V06e3d36f][$V6d786dc7] = true;
return $V2063c160;
}
 function setFieldFromTimestamp($V972bf3f0, $Vd7e6d55b, $V6d786dc7 = 0)
 {
 $V06e3d36f = $this->_layout->getField($V972bf3f0);
if (FileMaker::isError($V06e3d36f)) {
 return $V06e3d36f;
}
switch ($V06e3d36f->getResult()) {
 case 'date':
 return $this->setField($V972bf3f0, date('m/d/Y', $Vd7e6d55b), $V6d786dc7);
case 'time':
 return $this->setField($V972bf3f0, date('H:i:s', $Vd7e6d55b), $V6d786dc7);
case 'timestamp':
 return $this->setField($V972bf3f0, date('m/d/Y H:i:s', $Vd7e6d55b), $V6d786dc7);
}
return new FileMaker_Error($this->_fm, 'Only time, date, and timestamp fields can be set to the value of a timestamp.');
}
 function getRecordId()
 {
 return $this->_recordId;
}
 function getModificationId()
 {
 return $this->_modificationId;
}
 function &getRelatedSet($Vaca007a7)
 {
 if (empty($this->_relatedSets[$Vaca007a7])) {
 $Vcb5e100e =& new FileMaker_Error($this->_fm, 'Related set "' . $Vaca007a7 . '" not present.');
return $Vcb5e100e;
}
return $this->_relatedSets[$Vaca007a7];
}
 function &newRelatedRecord(&$Vd0e45878, $Vaca007a7)
 {
 $V3a2d7564 =& $this->_layout->getRelatedSet($Vaca007a7);
if (FileMaker::isError($V3a2d7564)) {
 return $V3a2d7564;
}
$Vde17f0f2 =& new FileMaker_Record($V3a2d7564);
$Vde17f0f2->_impl->_parent =& $Vd0e45878;
return $Vde17f0f2;
}
 function &getParent()
 {
 return $this->_parent;
}
 function validate($V972bf3f0 = null)
 {
 $V1dccadfe =& $this->_fm->newAddCommand($this->_layout->getName(), $this->_fields);
return $V1dccadfe->validate($V972bf3f0);
}
 function commit()
 {  
 if ($this->_fm->getProperty('prevalidate')) {
 $V9f7d0ee8 = $this->validate();
if (FileMaker::isError($V9f7d0ee8)) {
 return $V9f7d0ee8;
}
}   
 if (is_null($this->_parent)) {
 if ($this->_recordId) {
 return $this->_commitEdit();
} else {
 return $this->_commitAdd();
}
} else {    
 if (!$this->_parent->getRecordId()) {
 return new FileMaker_Error($this->_fm, 'You must commit the parent record first before you can commit its children.');
}
if ($this->_recordId) {
 return $this->_commitEditChild();
} else {
 return $this->_commitAddChild();
}
}
}
 function delete()
 {
 if (empty($this->_recordId)) {
 return new FileMaker_Error($this->_fm, 'You cannot delete a record that does not exist on the server.');
} 
 if ($this->_parent) {
 $Vd05b6ed7 = array(); 
 $V1dccadfe =& $this->_fm->newEditCommand($this->_parent->_impl->_layout->getName(),
 $this->_parent->_impl->_recordId,
 $Vd05b6ed7); 
 $V1dccadfe->_impl->_setdeleteRelated($this->_layout->getName().".".$this->_recordId);

 return $V1dccadfe->execute();
} 
 else {
 $Vc6140495 = $this->_layout->getName();

 $V1dccadfe =& $this->_fm->newDeleteCommand($Vc6140495, $this->_recordId);
return $V1dccadfe->execute();
}
}
 function _commitAdd()
 { 
 $V1dccadfe =& $this->_fm->newAddCommand($this->_layout->getName(), $this->_fields);
$Vd1fc8eaf = $V1dccadfe->execute();
if (FileMaker::isError($Vd1fc8eaf)) {
 return $Vd1fc8eaf;
} 
 $V6e52c40b =& $Vd1fc8eaf->getRecords();
return $this->_updateFrom($V6e52c40b[0]);
}
 function _commitEdit()
 {  
 foreach ($this->_fields as $V972bf3f0 => $Vd4680e80) {
 foreach ($Vd4680e80 as $V6d786dc7 => $V2063c160) {
 if (isset($this->V5e7ec2d5[$V972bf3f0][$V6d786dc7])) {
 $V8977dfac[$V972bf3f0][$V6d786dc7] = $V2063c160;
}
}
}
$V1dccadfe =& $this->_fm->newEditCommand($this->_layout->getName(),
 $this->_recordId,
 $V8977dfac);
$Vd1fc8eaf = $V1dccadfe->execute();
if (FileMaker::isError($Vd1fc8eaf)) {
 return $Vd1fc8eaf;
} 
 $V6e52c40b =& $Vd1fc8eaf->getRecords();
return $this->_updateFrom($V6e52c40b[0]);
}
 function _commitAddChild()
 {  
 $Vd05b6ed7 = array();
foreach ($this->_fields as $Vb068931c => $Vee0525e4) {
 $Vd05b6ed7[$Vb068931c . '.0'] = $Vee0525e4;
} 
 $V1dccadfe =& $this->_fm->newEditCommand($this->_parent->_impl->_layout->getName(),
 $this->_parent->getRecordId(),
 $Vd05b6ed7);
$Vd1fc8eaf = $V1dccadfe->execute();
if (FileMaker::isError($Vd1fc8eaf)) {
 return $Vd1fc8eaf;
} 
 $V6e52c40b =& $Vd1fc8eaf->getRecords();
$Vd0e45878 =& $V6e52c40b[0];
$V268184c1 =& $Vd0e45878->getRelatedSet($this->_layout->getName());
$V98bd1c45 = array_pop($V268184c1);
return $this->_updateFrom($V98bd1c45);
}
 function _commitEditChild()
 {  
 foreach ($this->_fields as $V972bf3f0 => $Vee0525e4) {
 foreach ($Vee0525e4 as $V6d786dc7 => $V2063c160) {
 if (!empty($this->V5e7ec2d5[$V972bf3f0][$V6d786dc7])) {
 $V8977dfac[$V972bf3f0 . '.' . $this->_recordId][$V6d786dc7] = $V2063c160;
}
}
}
$V1dccadfe =& $this->_fm->newEditCommand($this->_parent->_impl->_layout->getName(),
 $this->_parent->getRecordId(),
 $V8977dfac);
$Vd1fc8eaf = $V1dccadfe->execute();
if (FileMaker::isError($Vd1fc8eaf)) {
 return $Vd1fc8eaf;
} 
 $V6e52c40b =& $Vd1fc8eaf->getRecords();
$Vd0e45878 =& $V6e52c40b[0];
$V268184c1 =& $Vd0e45878->getRelatedSet($this->_layout->getName());
foreach ($V268184c1 as $V1b7d5726) {
 if ($V1b7d5726->getRecordId() == $this->_recordId) {
 return $this->_updateFrom($V1b7d5726);
break;
}
}
return new FileMaker_Error('Failed to find the updated child in the response.');
}
 function _updateFrom(&$Vde17f0f2)
 {
 $this->_recordId = $Vde17f0f2->getRecordId();
$this->_modificationId = $Vde17f0f2->getModificationId();
$this->_fields = $Vde17f0f2->_impl->_fields;
$this->_layout =& $Vde17f0f2->_impl->_layout;
$this->_relatedSets =& $Vde17f0f2->_impl->_relatedSets; 
 $this->V5e7ec2d5= array();
return true;
}

  function getRelatedRecordById($V97f7e518, $Va6ec9c02)
 {
 
 $Vaca007a7 = $this->getRelatedSet($V97f7e518);
if(FileMaker::IsError($Vaca007a7)){
 
 $Vcb5e100e =& new FileMaker_Error($this->_fm, 'Related set "' . $Vaca007a7 . '" not present.');
return $Vcb5e100e;
}else{ 
  foreach ($Vaca007a7 as $V1b7d5726) {
 if( $V1b7d5726->getRecordId() == $Va6ec9c02){
 return $V1b7d5726;
}
}
$Vcb5e100e =& new FileMaker_Error($this->_fm, 'Record not present.');
return $Vcb5e100e;	
 
 } 
 }

}

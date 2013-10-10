<?php
  require_once dirname(__FILE__) . '/../CommandImpl.php';
 class FileMaker_Command_Add_Implementation extends FileMaker_Command_Implementation
{
  var $_fields = array();
 function FileMaker_Command_Add_Implementation($V0ab34ca9, $Vc6140495, $Vf09cc7ee = array())
 { 
 FileMaker_Command_Implementation::FileMaker_Command_Implementation($V0ab34ca9, $Vc6140495);
foreach ($Vf09cc7ee as $V06e3d36f => $V2063c160) {
 if (!is_array($V2063c160)) {
 $V2063c160 = array($V2063c160);
}
$this->_fields[$V06e3d36f] = $V2063c160;
}
}
 function &execute()
 {  
 if ($this->_fm->getProperty('prevalidate')) {
 $V9f7d0ee8 = $this->validate();
if (FileMaker::isError($V9f7d0ee8)) {
 return $V9f7d0ee8;
}
} 
 $Vc6140495 =& $this->_fm->getLayout($this->_layout);
if (FileMaker::isError($Vc6140495)) {
 return $Vc6140495;
} 
 $V21ffce5b = $this->_getCommandParams(); 
 $V21ffce5b['-new'] = true; 
 foreach ($this->_fields as $V972bf3f0 => $Vee0525e4) {
 if (strpos($V972bf3f0, '.') !== false) {
 list($Vb068931c, $V11e868ac) = explode('.', $V972bf3f0, 2);
$V11e868ac = '.' . $V11e868ac;
} else {
 $Vb068931c = $V972bf3f0;
$V06e3d36f = $Vc6140495->getField($V972bf3f0);
if (FileMaker::isError($V06e3d36f)) {
 return $V06e3d36f;
}
if ($V06e3d36f->isGlobal()) {
 $V11e868ac = '.global';
} else {
 $V11e868ac = '';
}
}
foreach ($Vee0525e4 as $V6a992d55 => $V3a6d0284) {
 $V21ffce5b[$Vb068931c . '(' . ($V6a992d55 + 1) . ')' . $V11e868ac] = $V3a6d0284;
}
} 
 $V0f635d0e = $this->_fm->_execute($V21ffce5b);
if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e;
} 
 return $this->_getResult($V0f635d0e);
}
 function setField($V06e3d36f, $V2063c160, $V6d786dc7 = 0)
 {
 $this->_fields[$V06e3d36f][$V6d786dc7] = $V2063c160;
return $V2063c160;
}
 function setFieldFromTimestamp($V972bf3f0, $Vd7e6d55b, $V6d786dc7 = 0)
 {
 $Vc6140495 =& $this->_fm->getLayout($this->_layout);
if (FileMaker::isError($Vc6140495)) {
 return $Vc6140495;
}
$V06e3d36f = $Vc6140495->getField($V972bf3f0);
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
}

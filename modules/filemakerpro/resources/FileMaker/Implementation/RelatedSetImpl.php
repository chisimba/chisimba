<?php
  class FileMaker_RelatedSet_Implementation
{
  var $_layout;
 var $_fm;
 var $_name;
 var $_fields = array();
 function FileMaker_RelatedSet_Implementation(&$Vc6140495)
 {
 $this->_layout =& $Vc6140495;
$this->_fm =& $Vc6140495->_impl->_fm;
}
 function getName()
 {
 return $this->_name;
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
$Vcb5e100e =& new FileMaker_Error($this->_fm, 'Field Not Found');
return $Vcb5e100e;
}
 function &getFields()
 {
 return $this->_fields;
}
 function loadExtendedInfo()
 {
 return new FileMaker_Error($this->_fm, 'Related sets do not support extended information.');
}
}

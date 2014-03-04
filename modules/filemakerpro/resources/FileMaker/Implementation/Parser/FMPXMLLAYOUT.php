<?php
  class FileMaker_Parser_FMPXMLLAYOUT
{
  var $Vd05b6ed7;
 var $Ve3ad9440;
 var $_fm;
 var $V5431b8d4;
 var $V6de51026 = false;
 var $V191be3bd;
 var $V32e51cce;
 function FileMaker_Parser_FMPXMLLAYOUT(&$V0ab34ca9)
 {
 $this->_fm =& $V0ab34ca9;
}
 function parse($V0f635d0e)
 {
 if (empty($V0f635d0e)) {
 return new FileMaker_Error($this->_fm, 'Did not receive an XML document from the server.');
} 
 $this->V5431b8d4= xml_parser_create();
xml_set_object($this->V5431b8d4, $this);
xml_parser_set_option($this->V5431b8d4, XML_OPTION_CASE_FOLDING, false);
xml_parser_set_option($this->V5431b8d4, XML_OPTION_TARGET_ENCODING, 'UTF-8');
xml_set_element_handler($this->V5431b8d4, '_start', '_end');
xml_set_character_data_handler($this->V5431b8d4, '_cdata'); 
 if (!@xml_parse($this->V5431b8d4, $V0f635d0e)) {
 return new FileMaker_Error(sprintf('XML error: %s at line %d',
 xml_error_string(xml_get_error_code($this->V5431b8d4)),
 xml_get_current_line_number($this->V5431b8d4)));
} 
 xml_parser_free($this->V5431b8d4); 
 if (!empty($this->Vcb5e100e)) {
 return new FileMaker_Error($this->_fm, null, $this->Vcb5e100e);
}
$this->V6de51026= true;
return true;
}
 function setExtendedInfo(&$Vc6140495)
 {
 if (!$this->V6de51026) {
 return new FileMaker_Error($this->_fm, 'Attempt to set extended information before parsing data.');
}
$Vc6140495->_valueLists = $this->Ve3ad9440;
foreach ($this->Vd05b6ed7 as $V972bf3f0 => $V77be71a4) {
 $V8fa14cdd =& $Vc6140495->getField($V972bf3f0);
$V8fa14cdd->_impl->_styleType = $V77be71a4['styleType'];
$V8fa14cdd->_impl->_valueList = $V77be71a4['valueList'] ? $V77be71a4['valueList'] : null;
}
}
 function _start($V3643b863, $Vb068931c, $V5d06e8a3)
 { 
 $V5d06e8a3 = $this->_fm->toOutputCharset($V5d06e8a3);
switch ($Vb068931c) {
 case 'FIELD':
 $this->V191be3bd= $V5d06e8a3['NAME'];
break;
case 'STYLE':
 $this->Vd05b6ed7[$this->V191be3bd]['styleType'] = $V5d06e8a3['TYPE'];
$this->Vd05b6ed7[$this->V191be3bd]['valueList'] = $V5d06e8a3['VALUELIST'];
break;
case 'VALUELIST':
 $this->Ve3ad9440[$V5d06e8a3['NAME']] = array();
$this->V32e51cce= $V5d06e8a3['NAME'];
break;
case 'VALUE':
 $this->Ve3ad9440[$this->V32e51cce][] = '';
break;
}
}
 function _end($V3643b863, $Vb068931c)
 {
 switch ($Vb068931c) {
 case 'FIELD':
 $this->V191be3bd= null;
break;
case 'VALUELIST':
 $this->V32e51cce= null;
break;
}
}
 function _cdata($V3643b863, $V8d777f38)
 {
 if ($this->V32e51cce!== null && preg_match('|\S|', $V8d777f38)) {
 $this->Ve3ad9440[$this->V32e51cce][count($this->Ve3ad9440[$this->V32e51cce]) - 1] .= $this->_fm->toOutputCharset($V8d777f38);
}
}
}

<?php
  require_once dirname(__FILE__) . '/../../Layout.php';
require_once dirname(__FILE__) . '/../../RelatedSet.php';
require_once dirname(__FILE__) . '/../../Record.php';
require_once dirname(__FILE__) . '/../../Field.php';
 class FileMaker_Parser_FMResultSet
{
  var $Vcb5e100e;
 var $Vf5bf48aa;
 var $V1ea7e575;
 var $V9f81f3c0 = array();
 var $Vaae0d98d;
 var $Vae581270 = array();
 var $V6e52c40b = array();
 var $Ve13f1c92;
 var $V43432a31;
 var $V51bc3e3b;
 var $V26005321;
 var $V6468d939;
 var $_fm;
 var $V5431b8d4;
 var $V6de51026 = false;
 var $_result;
 var $_layout;
 function FileMaker_Parser_FMResultSet(&$V0ab34ca9)
 {
 $this->_fm =& $V0ab34ca9;
}
 function parse($V0f635d0e)
 {
 if (empty($V0f635d0e)) {
 return new FileMaker_Error($this->_fm, 'Did not receive an XML document from the server.');
} 
 $this->V5431b8d4= xml_parser_create('UTF-8');
xml_set_object($this->V5431b8d4, $this);
xml_parser_set_option($this->V5431b8d4, XML_OPTION_CASE_FOLDING, false);
xml_parser_set_option($this->V5431b8d4, XML_OPTION_TARGET_ENCODING, 'UTF-8');
xml_set_element_handler($this->V5431b8d4, '_start', '_end');
xml_set_character_data_handler($this->V5431b8d4, '_cdata'); 
 if (!@xml_parse($this->V5431b8d4, $V0f635d0e)) {
 return new FileMaker_Error($this->_fm,
 sprintf('XML error: %s at line %d',
 xml_error_string(xml_get_error_code($this->V5431b8d4)),
 xml_get_current_line_number($this->V5431b8d4)));
} 
 xml_parser_free($this->V5431b8d4); 
 if (!empty($this->Vcb5e100e)) {
 return new FileMaker_Error($this->_fm, null, $this->Vcb5e100e);
}  
 if (version_compare($this->Vf5bf48aa['version'], FileMaker::getMinServerVersion(), '<')) {
 return new FileMaker_Error($this->_fm, 'This API requires at least version ' . FileMaker::getMinServerVersion() . ' of FileMaker Server to run (detected ' . $this->Vf5bf48aa['version'] . ').');
}
$this->V6de51026= true;
return true;
}
 function setResult(&$Vb4a88417, $V561b2299 = 'FileMaker_Record')
 {
 if (!$this->V6de51026) {
 return new FileMaker_Error($this->_fm, 'Attempt to get a result object before parsing data.');
}
if ($this->_result) {
 $Vb4a88417 =& $this->_result;
return true;
} 
 $Vb4a88417->_impl->_layout =& new FileMaker_Layout($this->_fm);
$this->setLayout($Vb4a88417->_impl->_layout); 
 $Vb4a88417->_impl->_tableCount = $this->V1ea7e575['total-count'];
$Vb4a88417->_impl->_foundSetCount = $this->Vaae0d98d['count'];
$Vb4a88417->_impl->_fetchCount = $this->Vaae0d98d['fetch-size']; 
 $V6e52c40b = array();
foreach ($this->V6e52c40b as $Vde17f0f2) {
 $V4b43b0ae =& new $V561b2299($Vb4a88417->_impl->_layout);
$V4b43b0ae->_impl->_fields = $Vde17f0f2['fields'];
$V4b43b0ae->_impl->_recordId = $Vde17f0f2['record-id'];
$V4b43b0ae->_impl->_modificationId = $Vde17f0f2['mod-id'];
if ($Vde17f0f2['children']) {
 foreach ($Vde17f0f2['children'] as $Vaca007a7 => $V268184c1) {
 $V4b43b0ae->_impl->_relatedSets[$Vaca007a7] = array();
foreach ($V268184c1 as $V1b7d5726) {
 $V4a8a08f0 =& new $V561b2299($Vb4a88417->_impl->_layout->getRelatedSet($Vaca007a7));
$V4a8a08f0->_impl->_fields = $V1b7d5726['fields'];
$V4a8a08f0->_impl->_recordId = $V1b7d5726['record-id'];
$V4a8a08f0->_impl->_modificationId = $V1b7d5726['mod-id'];
$V4a8a08f0->_impl->_parent =& $V4b43b0ae;
$V4b43b0ae->_impl->_relatedSets[$Vaca007a7][] =& $V4a8a08f0;
}
}
}
$V6e52c40b[] =& $V4b43b0ae;
}
$Vb4a88417->_impl->_records =& $V6e52c40b;
$this->_result =& $Vb4a88417;
true;
}
 function setLayout(&$Vc6140495)
 {
 if (!$this->V6de51026) {
 return new FileMaker_Error($this->_fm, 'Attempt to get a layout object before parsing data.');
}
if ($this->_layout) {
 $Vc6140495 =& $this->_layout;
return true;
}
$Vc6140495->_impl->_name = $this->V1ea7e575['layout'];
$Vc6140495->_impl->_database = $this->V1ea7e575['database'];
foreach ($this->V9f81f3c0 as $V06e3d36f) {
 $V8fa14cdd =& new FileMaker_Field($Vc6140495);
$V8fa14cdd->_impl->_name = $V06e3d36f['name'];
$V8fa14cdd->_impl->_autoEntered = (bool)($V06e3d36f['auto-enter'] == 'yes');
$V8fa14cdd->_impl->_global = (bool)($V06e3d36f['global'] == 'yes');
$V8fa14cdd->_impl->_maxRepeat = (int)$V06e3d36f['max-repeat'];
$V8fa14cdd->_impl->_result = $V06e3d36f['result'];
$V8fa14cdd->_impl->_type = $V06e3d36f['type']; 
 if ($V06e3d36f['not-empty'] == 'yes') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_NOTEMPTY] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_NOTEMPTY;
}
if ($V06e3d36f['numeric-only'] == 'yes') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_NUMERICONLY] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_NUMERICONLY;
}
if (array_key_exists('max-characters', $V06e3d36f)) {
 $V8fa14cdd->_impl->_maxCharacters = (int) $V06e3d36f['max-characters'];
$V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_MAXCHARACTERS] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_MAXCHARACTERS;
}
if ($V06e3d36f['four-digit-year'] == 'yes') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_FOURDIGITYEAR] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_FOURDIGITYEAR;
}
if ($V06e3d36f['time-of-day'] == 'yes' || $V06e3d36f['result'] == 'time') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_TIMEOFDAY] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_TIMEOFDAY;
}
if ($V06e3d36f['four-digit-year'] == 'no' && $V06e3d36f['result'] == 'timestamp') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_TIMESTAMP_FIELD] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_TIMESTAMP_FIELD;
}
if ($V06e3d36f['four-digit-year'] == 'no' && $V06e3d36f['result'] == 'date') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_DATE_FIELD] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_DATE_FIELD;
}
if ($V06e3d36f['time-of-day'] == 'no' && $V06e3d36f['result'] == 'time') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_TIME_FIELD] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_TIME_FIELD;
}
$Vc6140495->_impl->_fields[$V8fa14cdd->getName()] =& $V8fa14cdd;
}
foreach ($this->Vae581270 as $Vaca007a7 => $V53256610) {
 $V4b43b0ae =& new FileMaker_RelatedSet($Vc6140495);
$V4b43b0ae->_impl->_name = $Vaca007a7;
foreach ($V53256610 as $V06e3d36f) {
 $V8fa14cdd =& new FileMaker_Field($V4b43b0ae);
$V8fa14cdd->_impl->_name = $V06e3d36f['name'];
$V8fa14cdd->_impl->_autoEntered = (bool)($V06e3d36f['auto-enter'] == 'yes');
$V8fa14cdd->_impl->_global = (bool)($V06e3d36f['global'] == 'yes');
$V8fa14cdd->_impl->_maxRepeat = (int)$V06e3d36f['max-repeat'];
$V8fa14cdd->_impl->_result = $V06e3d36f['result'];
$V8fa14cdd->_impl->_type = $V06e3d36f['type']; 
 if ($V06e3d36f['not-empty'] == 'yes') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_NOTEMPTY] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_NOTEMPTY;
}
if ($V06e3d36f['numeric-only'] == 'yes') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_NUMERICONLY] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_NUMERICONLY;
}
if (array_key_exists('max-characters', $V06e3d36f)) {
 $V8fa14cdd->_impl->_maxCharacters = (int) $V06e3d36f['max-characters'];
$V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_MAXCHARACTERS] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_MAXCHARACTERS;
}
if ($V06e3d36f['four-digit-year'] == 'yes') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_FOURDIGITYEAR] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_FOURDIGITYEAR;
}
if ($V06e3d36f['time-of-day'] == 'yes' || $V06e3d36f['result'] == 'time') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_TIMEOFDAY] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_TIMEOFDAY;
}
if ($V06e3d36f['four-digit-year'] == 'no' && $V06e3d36f['result'] == 'timestamp') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_TIMESTAMP_FIELD] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_TIMESTAMP_FIELD;
}
if ($V06e3d36f['four-digit-year'] == 'no' && $V06e3d36f['result'] == 'date') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_DATE_FIELD] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_DATE_FIELD;
}
if ($V06e3d36f['time-of-day'] == 'no' && $V06e3d36f['result'] == 'time') {
 $V8fa14cdd->_impl->_validationRules[FILEMAKER_RULE_TIME_FIELD] = true;
$V8fa14cdd->_impl->_validationMask |= FILEMAKER_RULE_TIME_FIELD;
}
$V4b43b0ae->_impl->_fields[$V8fa14cdd->getName()] =& $V8fa14cdd;
}
$Vc6140495->_impl->_relatedSets[$V4b43b0ae->getName()] =& $V4b43b0ae;
}
$this->_layout =& $Vc6140495;
return true;
}
 function _start($V3643b863, $Vb068931c, $V5d06e8a3)
 { 
 $V5d06e8a3 = $this->_fm->toOutputCharset($V5d06e8a3);
switch ($Vb068931c) {
 case 'error':
 $this->Vcb5e100e= $V5d06e8a3['code'];
break;
case 'product':
 $this->Vf5bf48aa= $V5d06e8a3;
break;
case 'datasource':
 $this->V1ea7e575= $V5d06e8a3;
break;
case 'relatedset-definition':
 $this->Vae581270[$V5d06e8a3['table']] = array();
$this->Ve13f1c92= $V5d06e8a3['table'];
break;
case 'field-definition':
 if ($this->Ve13f1c92) {
 $this->Vae581270[$this->Ve13f1c92][] = $V5d06e8a3;
} else {
 $this->V9f81f3c0[] = $V5d06e8a3;
}
break;
case 'resultset':
 $this->Vaae0d98d= $V5d06e8a3;
break;
case 'relatedset':
 $this->Ve13f1c92= $V5d06e8a3['table'];
$this->V51bc3e3b= $this->V43432a31;
$this->V51bc3e3b['children'][$this->Ve13f1c92] = array();
$this->V43432a31= null;
break;
case 'record':
 $this->V43432a31=
 array('record-id' => $V5d06e8a3['record-id'],
 'mod-id' => $V5d06e8a3['mod-id'],
 'fields' => array(),
 'children' => array());
break;
case 'field':
 $this->V26005321= $V5d06e8a3['name'];
$this->V43432a31['fields'][$this->V26005321] = array();
break;
case 'data':
 $this->V6468d939= '';
break;
}
}
 function _end($V3643b863, $Vb068931c)
 {
 switch ($Vb068931c) {
 case 'relatedset-definition':
 $this->Ve13f1c92= null;
break;
case 'relatedset':
 $this->Ve13f1c92= null;
$this->V43432a31= $this->V51bc3e3b;
$this->V51bc3e3b= null;
break;
case 'record':
 if ($this->Ve13f1c92) {
 $this->V51bc3e3b['children'][$this->Ve13f1c92][] = $this->V43432a31;
} else {
 $this->V6e52c40b[] = $this->V43432a31;
}
$this->V43432a31= null;
break;
case 'field':
 $this->V26005321= null;
break;
case 'data':
 $this->V43432a31['fields'][$this->V26005321][] = trim($this->V6468d939);
$this->V6468d939= null;
break;
}
}
 function _cdata($V3643b863, $V8d777f38)
 {
 $this->V6468d939.= $this->_fm->toOutputCharset($V8d777f38);
}
}

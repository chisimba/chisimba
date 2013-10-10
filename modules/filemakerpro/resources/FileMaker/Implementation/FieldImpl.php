<?php
  require_once dirname(__FILE__) . '/../Error/Validation.php';
 class FileMaker_Field_Implementation
{
  var $_layout;
 var $_name;
 var $_autoEntered = false;
 var $_global = false;
 var $_maxRepeat = 1;
 var $_validationMask = 0;
 var $_validationRules = array();
 var $_result;
 var $_type;
 var $_valueList = null;
 var $_styleType;
 var $_maxCharacters = 0;
 function FileMaker_Field_Implementation(&$Vc6140495)
 {
 $this->_layout =& $Vc6140495;
}
 function getName()
 {
 return $this->_name;
}
 function &getLayout()
 {
 return $this->_layout;
}
 function isAutoEntered()
 {
 return $this->_autoEntered;
}
 function isGlobal()
 {
 return $this->_global;
}
 function getRepetitionCount()
 {
 return $this->_maxRepeat;
}
 function validate($V2063c160, $Vcb5e100e = null)
 {
 $V1c0c74f6 = true;
if ($Vcb5e100e === null) {
 $V1c0c74f6 = false;
$Vcb5e100e =& new FileMaker_Error_Validation($this->_layout->_impl->_fm);
}
foreach ($this->getValidationRules() as $V981c1e7b) {
 switch ($V981c1e7b) {
 case FILEMAKER_RULE_NOTEMPTY:
 if (empty($V2063c160)) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
break;
case FILEMAKER_RULE_NUMERICONLY :
 if (!empty ($V2063c160)) {
 if ($this->checkNumericOnly($V2063c160)) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
}
break;
case FILEMAKER_RULE_MAXCHARACTERS :
 if (!empty ($V2063c160)) {
 $V2fa47f7c = strlen($V2063c160);
if ($V2fa47f7c > $this->_maxCharacters) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
}
break;
case FILEMAKER_RULE_TIME_FIELD :
 if (!empty ($V2063c160)) {
 if (!$this->checkTimeFormat($V2063c160)) {
 
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else {
 $this->checkTimeValidity($V2063c160, $V981c1e7b, $Vcb5e100e, FALSE);
}
}
break;
case FILEMAKER_RULE_TIMESTAMP_FIELD :
 if (!empty ($V2063c160)) {
 if (!$this->checkTimeStampFormat($V2063c160)) {
 
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else {
 $this->checkDateValidity($V2063c160, $V981c1e7b, $Vcb5e100e);
$this->checkTimeValidity($V2063c160, $V981c1e7b, $Vcb5e100e, FALSE);
}
}
break;
case FILEMAKER_RULE_DATE_FIELD :
 if (!empty ($V2063c160)) {
 if (!$this->checkDateFormat($V2063c160)) {
 
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else {
 $this->checkDateValidity($V2063c160, $V981c1e7b, $Vcb5e100e);
}
}
break;
case FILEMAKER_RULE_FOURDIGITYEAR :
 if (!empty ($V2063c160)) {
 switch ($this->_result) {
 case 'timestamp' : 
 if ($this->checkTimeStampFormatFourDigitYear($V2063c160)) { 
 ereg('^([0-9]{1,2})[-,/,\\]([0-9]{1,2})[-,/,\\]([0-9]{4})', $V2063c160, $V9c28d32d);
$V7436f942 = $V9c28d32d[1];
$V628b7db0 = $V9c28d32d[2];
$V84cdc76c = $V9c28d32d[3];  
 if ($V84cdc76c < 1 || $V84cdc76c > 4000) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else
 if (!checkdate($V7436f942, $V628b7db0, $V84cdc76c)) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else { 
 $this->checkTimeValidity($V2063c160, $V981c1e7b, $Vcb5e100e, FALSE);
}
} else {
 
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
break;
default :
 ereg('([0-9]{1,2})[-,/,\\]([0-9]{1,2})[-,/,\\]([0-9]{1,4})', $V2063c160, $V78f0805f);
if (count($V78f0805f) != 3) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else {
 $V6c8f3f79 = strlen($V78f0805f[2]);
if ($V6c8f3f79 != 4) { 
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else { 
 if ($V78f0805f[2] < 1 || $V78f0805f[2] > 4000) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else { 
 if (!checkdate($V78f0805f[0], $V78f0805f[1], $V78f0805f[2])) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
}
}
}
break;
} 
 }
break;
case FILEMAKER_RULE_TIMEOFDAY :
 if (!empty ($V2063c160)) { 
 if ($this->checkTimeFormat($V2063c160)) { 
 $this->checkTimeValidity($V2063c160, $V981c1e7b, $Vcb5e100e, TRUE);
} else {
 
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
}
break;
} 
 }   
 if ($V1c0c74f6) {
 return $Vcb5e100e;
} else {  
 return $Vcb5e100e->numErrors() ? $Vcb5e100e : true;
}
}

	 function getLocalValidationRules() 
	{
 $V6b55d9ec = array ();
foreach (array_keys($this->_validationRules) as $V981c1e7b) {
 switch ($V981c1e7b) {
 case FILEMAKER_RULE_NOTEMPTY :
 $V6b55d9ec[] = $V981c1e7b;
break;
case FILEMAKER_RULE_NUMERICONLY :
 $V6b55d9ec[] = $V981c1e7b;
break;
case FILEMAKER_RULE_MAXCHARACTERS :
 $V6b55d9ec[] = $V981c1e7b;
break;
case FILEMAKER_RULE_FOURDIGITYEAR :
 $V6b55d9ec[] = $V981c1e7b;
break;
case FILEMAKER_RULE_TIMEOFDAY :
 $V6b55d9ec[] = $V981c1e7b;
break;
case FILEMAKER_RULE_TIMESTAMP_FIELD :
 $V6b55d9ec[] = $V981c1e7b;
break;
case FILEMAKER_RULE_DATE_FIELD :
 $V6b55d9ec[] = $V981c1e7b;
break;
case FILEMAKER_RULE_TIME_FIELD :
 $V6b55d9ec[] = $V981c1e7b;
break;
}
}
return $V6b55d9ec;
}
function checkTimeStampFormatFourDigitYear($V2063c160) 
	{
 return (ereg('^[ ]*([0-9]{1,2})[-,/,\\]([0-9]{1,2})[-,/,\\]([0-9]{4})[ ]*([0-9]{1,2})[:]([0-9]{1,2})([:][0-9]{1,2})?([ ]*((AM|PM)|(am|pm)))?[ ]*$', $V2063c160));
}
function checkTimeStampFormat($V2063c160) 
	{
 return (ereg('^[ ]*([0-9]{1,2})[-,/,\\]([0-9]{1,2})([-,/,\\]([0-9]{1,4}))?[ ]*([0-9]{1,2})[:]([0-9]{1,2})([:][0-9]{1,2})?([ ]*((AM|PM)|(am|pm)))?[ ]*$', $V2063c160));
}
function checkDateFormat($V2063c160) 
	{
 return (ereg('^[ ]*([0-9]{1,2})[-,/,\\]([0-9]{1,2})([-,/,\\]([0-9]{1,4}))?[ ]*$', $V2063c160));
}
function checkTimeFormat($V2063c160) 
	{
 return (ereg('^[ ]*([0-9]{1,2})[:]([0-9]{1,2})([:][0-9]{1,2})?([ ]*((AM|PM)|(am|pm)))?[ ]*$', $V2063c160));
}
function checkNumericOnly($V2063c160) 
	{
 return (!is_numeric($V2063c160));
}
function checkDateValidity($V2063c160, $V981c1e7b, $Vcb5e100e) 
	{
 ereg('([0-9]{1,2})[-,/,\\]([0-9]{1,2})([-,/,\\]([0-9]{1,4}))?', $V2063c160, $V78f0805f);
if ($V78f0805f[4]) {
 $V6c8f3f79 = strlen($V78f0805f[4]);
$V84cdc76c = $V78f0805f[4];
if ($V6c8f3f79 != 4) {
 $V84cdc76c = $V84cdc76c +2000;
} 
 if ($V78f0805f[4] < 1 || $V78f0805f[4] > 4000) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else { 
 if (!checkdate($V78f0805f[1], $V78f0805f[2], $V78f0805f[4])) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
}
} else {
 $V84cdc76c = date('Y');
if (!checkdate($V78f0805f[1], $V78f0805f[2], $V84cdc76c)) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
}
}
function checkTimeValidity($V2063c160, $V981c1e7b, $Vcb5e100e, $Vcaf85b7b) 
	{
 $V52124c01 = 0;
if ($Vcaf85b7b) {
 $V52124c01 = 12;
} else {
 $V52124c01 = 24;
} 
 ereg('([0-9]{1,2})[:]([0-9]{1,2})([:][0-9]{1,2})?', $V2063c160, $V9c28d32d);
$V896c55cc = $V9c28d32d[1];
$V640fd0cc = $V9c28d32d[2];
if (count($V9c28d32d) > 4) {
 $V783e8e29 = $V9c28d32d[3];
} 
 if ($V896c55cc < 1 || $V896c55cc > $V52124c01) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else if ($V640fd0cc < 1 || $V640fd0cc > 59) {
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
} else
 if (count($V9c28d32d) > 4) {
 if ($V783e8e29 < 1 || $V783e8e29 > 59)
 $Vcb5e100e->addError($this, $V981c1e7b, $V2063c160);
}
}
 function getValidationRules()
 {
 return array_keys($this->_validationRules);
}
 function getValidationMask()
 {
 return $this->_validationMask;
}
 function hasValidationRule($Ve289cc97)
 {
 return $Ve289cc97 & $this->_validationMask;
}
 function describeValidationRule($Ve289cc97)
 {
 if (is_array($this->_validationRules[$Ve289cc97])) {
 return $this->_validationRules[$Ve289cc97];
}
return null;
}
 function describeLocalValidationRules() 
	{
 $V6b55d9ec = array ();
foreach ($this->_validationRules as $V981c1e7b => $V1dee80c7) {
 switch ($V981c1e7b) {
 case FILEMAKER_RULE_NOTEMPTY :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
case FILEMAKER_RULE_NUMERICONLY :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
case FILEMAKER_RULE_MAXCHARACTERS :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
case FILEMAKER_RULE_FOURDIGITYEAR :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
case FILEMAKER_RULE_TIMEOFDAY :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
case FILEMAKER_RULE_TIMESTAMP_FIELD :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
case FILEMAKER_RULE_DATE_FIELD :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
case FILEMAKER_RULE_TIME_FIELD :
 $V6b55d9ec[$V981c1e7b] = $V1dee80c7;
break;
}
}
return $V6b55d9ec;
}
 function describeValidationRules()
 {
 return $this->_validationRules;
}
 function getResult()
 {
 return $this->_result;
}
 function getType()
 {
 return $this->_type;
}
 function getValueList($Vd33e904c = null)
 {
 $Vb4a88417 = $this->_layout->loadExtendedInfo($Vd33e904c);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
return $this->_layout->getValueList($this->_valueList);
}
 function getStyleType()
 {
 $Vb4a88417 = $this->_layout->loadExtendedInfo();
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
return $this->_styleType;
}
}

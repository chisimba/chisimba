<?php
  require_once dirname(__FILE__) . '/Parser/FMResultSet.php';
 class FileMaker_Implementation
{
  var $V73ee434e = array('charset' => 'utf-8');
 var $Vea4b3413 = null;

var $V9a3dcbce;
 function getAPIVersion()
 {
 return '1.0';
}
 function getMinServerVersion()
 {
 return '9.0.0.0';
}
 function FileMaker_Implementation($V11e0eed8, $Vccd0e374, $V14c4b06b, $V5f4dcc3b)
 {
 $V07cc694b = time();
if ((@include dirname(__FILE__) . '/../conf/filemaker-api.php') && isset($__FM_CONFIG)) {
 foreach ($__FM_CONFIG as $V23a5b8ab => $V2063c160) {
 $this->setProperty($V23a5b8ab, $V2063c160);
}
}
if (!is_null($Vccd0e374)) {
 $this->setProperty('hostspec', $Vccd0e374);
}
if (!is_null($V11e0eed8)) {
 $this->setProperty('database', $V11e0eed8);
}
if (!is_null($V14c4b06b)) {
 $this->setProperty('username', $V14c4b06b);
}
if (!is_null($V5f4dcc3b)) {
 $this->setProperty('password', $V5f4dcc3b);
}
}
 function setProperty($V23a5b8ab, $V2063c160)
 {
 $this->V73ee434e[$V23a5b8ab] = $V2063c160;
}
 function getProperty($V23a5b8ab)
 {
 return isset($this->V73ee434e[$V23a5b8ab]) ? $this->V73ee434e[$V23a5b8ab] : null;
}
 function getProperties()
 {
 return $this->V73ee434e;
}
 function setLogger(&$V6db435f3)
 {
 if (!is_a($V6db435f3, 'Log')) {
 return new FileMaker_Error($this, 'setLogger() must be passed an instance of PEAR::Log');
}
$this->Vea4b3413=& $V6db435f3;
}
 function log($V78e73102, $Vc9e9a848)
 {
 if ($this->Vea4b3413=== null) {
 return;
}
$Ve4aa4dcd = $this->getProperty('logLevel');
if ($Ve4aa4dcd === null || $Vc9e9a848 > $Ve4aa4dcd) {
 return;
}
switch ($Vc9e9a848) {
 case FILEMAKER_LOG_DEBUG:
 $this->Vea4b3413->log($V78e73102, PEAR_LOG_DEBUG);
break;
case FILEMAKER_LOG_INFO:
 $this->Vea4b3413->log($V78e73102, PEAR_LOG_INFO);
break;
case FILEMAKER_LOG_ERR:
 $this->Vea4b3413->log($V78e73102, PEAR_LOG_ERR);
break;
}
}
 function toOutputCharset($V1d770934)
 {
 if (strtolower($this->getProperty('charset')) != 'iso-8859-1') {
 return $V1d770934;
}
if (is_array($V1d770934)) {
 $Vfa816edb = array();
foreach ($V1d770934 as $V3c6e0b8a => $V3a6d0284) {
 $Vfa816edb[$this->toOutputCharset($V3c6e0b8a)] = $this->toOutputCharset($V3a6d0284);
}
return $Vfa816edb;
}
if (!is_string($V1d770934)) {
 return $V1d770934;
}
return utf8_decode($V1d770934);
}
 function &newAddCommand($Vc6140495, $Vf09cc7ee = array())
 {
 require_once dirname(__FILE__) . '/../Command/Add.php';
$Vab4d0a65 =& new FileMaker_Command_Add($this, $Vc6140495, $Vf09cc7ee);
return $Vab4d0a65;
}
 function &newEditCommand($Vc6140495, $Va6ec9c02, $Va0af1e2b = array())
 {
 require_once dirname(__FILE__) . '/../Command/Edit.php';
$Vab4d0a65 =& new FileMaker_Command_Edit($this, $Vc6140495, $Va6ec9c02, $Va0af1e2b);
return $Vab4d0a65;
}
 function &newDeleteCommand($Vc6140495, $Va6ec9c02)
 {
 require_once dirname(__FILE__) . '/../Command/Delete.php';
$Vab4d0a65 =& new FileMaker_Command_Delete($this, $Vc6140495, $Va6ec9c02);
return $Vab4d0a65;
}
 function &newDuplicateCommand($Vc6140495, $Va6ec9c02)
 {
 require_once dirname(__FILE__) . '/../Command/Duplicate.php';
$Vab4d0a65 =& new FileMaker_Command_Duplicate($this, $Vc6140495, $Va6ec9c02);
return $Vab4d0a65;
}
 function &newFindCommand($Vc6140495)
 {
 require_once dirname(__FILE__) . '/../Command/Find.php';
$Vab4d0a65 =& new FileMaker_Command_Find($this, $Vc6140495);
return $Vab4d0a65;
}

  function &newCompoundFindCommand($Vc6140495)
 {
 require_once dirname(__FILE__) . '/../Command/CompoundFind.php';
$Vcdaeeeba =& new FileMaker_Command_CompoundFind($this, $Vc6140495);
return $Vcdaeeeba;

 }

  function &newFindRequest($Vc6140495)
 {
 require_once dirname(__FILE__) . '/../Command/FindRequest.php';
$Vab4d0a65 =& new FileMaker_Command_FindRequest($this, $Vc6140495);
return $Vab4d0a65;

 }

  function &newFindAnyCommand($Vc6140495)
 {
 require_once dirname(__FILE__) . '/../Command/FindAny.php';
$Vab4d0a65 =& new FileMaker_Command_FindAny($this, $Vc6140495);
return $Vab4d0a65;
}
 function &newFindAllCommand($Vc6140495)
 {
 require_once dirname(__FILE__) . '/../Command/FindAll.php';
$Vab4d0a65 =& new FileMaker_Command_FindAll($this, $Vc6140495);
return $Vab4d0a65;
}
 function &newPerformScriptCommand($Vc6140495, $V2550889a, $V9b479e5e = null)
 {
 require_once dirname(__FILE__) . '/../Command/PerformScript.php';
$Vab4d0a65 =& new FileMaker_Command_PerformScript($this, $Vc6140495, $V2550889a, $V9b479e5e);
return $Vab4d0a65;
}
 function &createRecord($Vf43ac2d2, $Vfe0f78a8 = array())
 {
 $Vc6140495 =& $this->getLayout($Vf43ac2d2);
if (FileMaker::isError($Vc6140495)) {
 return $Vc6140495;
}
$Vde17f0f2 =& new $this->V73ee434e['recordClass']($Vc6140495);
if (is_array($Vfe0f78a8)) {
 foreach ($Vfe0f78a8 as $V3c6e0b8a => $V2063c160) {
 if (is_array($V2063c160)) {
 foreach ($V2063c160 as $V6d786dc7 => $Vb5528fe6) {
 $Vde17f0f2->setField($V3c6e0b8a, $Vb5528fe6, $V6d786dc7);
}
} else {
 $Vde17f0f2->setField($V3c6e0b8a, $V2063c160);
}
}
}
return $Vde17f0f2;
}
 function &getRecordById($Vc6140495, $Va6ec9c02)
 {
 $V10573b87 =& $this->newFindCommand($Vc6140495);
$V10573b87->setRecordId($Va6ec9c02);
$Vd1fc8eaf =& $V10573b87->execute();
if (FileMaker::isError($Vd1fc8eaf)) {
 return $Vd1fc8eaf;
}
$V6e52c40b =& $Vd1fc8eaf->getRecords();
if (!$V6e52c40b) {
 $Vcb5e100e =& new FileMaker_Error($this, 'Record . ' . $Va6ec9c02 . ' not found in layout "' . $Vc6140495 . '".');
return $Vcb5e100e;
}
return $V6e52c40b[0];
}
 function &getLayout($Vf43ac2d2)
 { 
 static $V34d59fda = array();
if (isset($V34d59fda[$Vf43ac2d2])) {
 return $V34d59fda[$Vf43ac2d2];
}
$V0f635d0e = $this->_execute(array('-db' => $this->getProperty('database'),
 '-lay' => $Vf43ac2d2,
 '-view' => true));
if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e;
}
$V3643b863 =& new FileMaker_Parser_FMResultSet($this);
$Vb4a88417 = $V3643b863->parse($V0f635d0e);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
$Vc6140495 =& new FileMaker_Layout($this);
$Vb4a88417 = $V3643b863->setLayout($Vc6140495);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
$V34d59fda[$Vf43ac2d2] =& $Vc6140495;
return $Vc6140495;
}
 function listDatabases()
 {
 $V0f635d0e = $this->_execute(array('-dbnames' => true));
if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e;
}
$V3643b863 =& new FileMaker_Parser_fmresultset($this);
$Vb4a88417 = $V3643b863->parse($V0f635d0e);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
$Ve61ce306 = array();
foreach ($V3643b863->V6e52c40b as $V0b2c082c) {
 $Ve61ce306[] = $V0b2c082c['fields']['DATABASE_NAME'][0];
}
return $Ve61ce306;
}
 function listScripts()
 {
 $V0f635d0e = $this->_execute(array('-db' => $this->getProperty('database'),
 '-scriptnames' => true));
if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e;
}
$V3643b863 =& new FileMaker_Parser_FMResultSet($this);
$Vb4a88417 = $V3643b863->parse($V0f635d0e);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
$Vd6c5855a = array();
foreach ($V3643b863->V6e52c40b as $V0b2c082c) {
 $Vd6c5855a[] = $V0b2c082c['fields']['SCRIPT_NAME'][0];
}
return $Vd6c5855a;
}
 function listLayouts()
 {
 $V0f635d0e = $this->_execute(array('-db' => $this->getProperty('database'),
 '-layoutnames' => true));
if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e;
}
$V3643b863 =& new FileMaker_Parser_FMResultSet($this);
$Vb4a88417 = $V3643b863->parse($V0f635d0e);
if (FileMaker::isError($Vb4a88417)) {
 return $Vb4a88417;
}
$V34d59fda = array();
foreach ($V3643b863->V6e52c40b as $V0b2c082c) {
 $V34d59fda[] = $V0b2c082c['fields']['LAYOUT_NAME'][0];
}
return $V34d59fda;
}
 function getContainerData($V9305b73d)
 { 
 if (!function_exists('curl_init')) {
 return new FileMaker_Error($this, 'cURL is required to use the FileMaker API.');
}
$V572d4e42 = $this->getProperty('hostspec');
if (substr($V572d4e42, -1, 1) == '/') {
 $V572d4e42 = substr($V572d4e42, 0, -1);
}
$V572d4e42 .= $V9305b73d; 
 $V572d4e42 = htmlspecialchars_decode($V572d4e42); 
 $this->log('Request for ' . $V572d4e42, FILEMAKER_LOG_INFO); 
 $Vd88fc6ed = curl_init($V572d4e42);

 curl_setopt($Vd88fc6ed, CURLOPT_RETURNTRANSFER, true);
curl_setopt($Vd88fc6ed, CURLOPT_FAILONERROR, true); 
	
 if ($this->getProperty('username')) { 
 $V313225f0 = base64_encode($this->getProperty('username'). ':' . $this->getProperty('password'));
$V44914468 = array('X-FMI-PE-Authorization: Basic ' . $V313225f0, 'X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk=');
curl_setopt($Vd88fc6ed, CURLOPT_HTTPHEADER, $V44914468);
}
else{
curl_setopt($Vd88fc6ed, CURLOPT_HTTPHEADER, array('X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk='));
}
 if ($V93da65a9 = $this->getProperty('curlOptions')) {
 foreach ($V93da65a9 as $Vef3e30e0 => $V2063c160) {
 curl_setopt($Vd88fc6ed, $Vef3e30e0, $V2063c160);
}
}  
 $Vd1fc8eaf = curl_exec($Vd88fc6ed); 
 $this->log($Vd1fc8eaf, FILEMAKER_LOG_DEBUG); 
 if ($V70106d0d = curl_errno($Vd88fc6ed)) {
 return new FileMaker_Error($this, 'Communication Error: (' . $V70106d0d . ') ' . curl_error($Vd88fc6ed));
}
curl_close($Vd88fc6ed);
return $Vd1fc8eaf;
}
 function _execute($Vf7cc8e48, $Vb3d1bd6a = 'fmresultset')
 { 
 if (!function_exists('curl_init')) {
 return new FileMaker_Error($this, 'cURL is required to use the FileMaker API.');
}
 $Ve0c6dcf8 = array();
foreach ($Vf7cc8e48 as $V3c6e0b8a => $V3a6d0284) {
 if ($this->getProperty('charset') != 'utf-8' && $V3a6d0284 !== true) {
 $V3a6d0284 = utf8_encode($V3a6d0284);
}
$Ve0c6dcf8[] = urlencode($V3c6e0b8a) . ($V3a6d0284 === true ? '' : '=' . urlencode($V3a6d0284));
} 
 $V572d4e42 = $this->getProperty('hostspec');
if (substr($V572d4e42, -1, 1) != '/') {
 $V572d4e42 .= '/';
}
$V572d4e42 .= 'fmi/xml/' . $Vb3d1bd6a . '.xml'; 
 $this->log('Request for ' . $V572d4e42, FILEMAKER_LOG_INFO); 
 $Vd88fc6ed = curl_init($V572d4e42);
curl_setopt($Vd88fc6ed, CURLOPT_POST, true);
curl_setopt($Vd88fc6ed, CURLOPT_RETURNTRANSFER, true);
curl_setopt($Vd88fc6ed, CURLOPT_FAILONERROR, true); 
	
 if ($this->getProperty('username')) { 
 $V313225f0 = base64_encode($this->getProperty('username'). ':' . $this->getProperty('password'));
$V44914468 = 'X-FMI-PE-Authorization: Basic ' . $V313225f0;
curl_setopt($Vd88fc6ed, CURLOPT_HTTPHEADER, array('X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk=', $V44914468));
}else{
 curl_setopt($Vd88fc6ed, CURLOPT_HTTPHEADER, array('X-FMI-PE-ExtendedPrivilege: tU+xR2RSsdk='));
}

 curl_setopt($Vd88fc6ed, CURLOPT_POSTFIELDS, implode('&', $Ve0c6dcf8));     
 if ($V93da65a9 = $this->getProperty('curlOptions')) {
 foreach ($V93da65a9 as $Vef3e30e0 => $V2063c160) {
 curl_setopt($Vd88fc6ed, $Vef3e30e0, $V2063c160);
}
}  
 $Vd1fc8eaf = curl_exec($Vd88fc6ed); 
 $this->log($Vd1fc8eaf, FILEMAKER_LOG_DEBUG); 
 if ($V70106d0d = curl_errno($Vd88fc6ed)) {
 
 if($V70106d0d == 52){
 return new FileMaker_Error($this, 'Communication Error: (' . $V70106d0d . ') ' . curl_error($Vd88fc6ed) . ' - The Web Publishing Core and/or FileMaker Server services are not running.', $V70106d0d);
}else if($V70106d0d == 22){	
 if (eregi("50", curl_error($Vd88fc6ed))) {
 return new FileMaker_Error($this, 'Communication Error: (' . $V70106d0d . ') ' . curl_error($Vd88fc6ed) . ' - The Web Publishing Core and/or FileMaker Server services are not running.', $V70106d0d);
}else{
 return new FileMaker_Error($this, 'Communication Error: (' . $V70106d0d . ') ' . curl_error($Vd88fc6ed) . ' - This can be due to an invalid username or password, or if the FMPHP privilege is not enabled for that user.', $V70106d0d);
}
}else{
 return new FileMaker_Error($this, 'Communication Error: (' . $V70106d0d . ') ' . curl_error($Vd88fc6ed), $V70106d0d);
}
}
curl_close($Vd88fc6ed);

 return $Vd1fc8eaf;
}
}

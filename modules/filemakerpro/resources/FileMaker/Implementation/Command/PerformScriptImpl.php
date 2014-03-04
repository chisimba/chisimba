<?php
  require_once dirname(__FILE__) . '/../CommandImpl.php';
 class FileMaker_Command_PerformScript_Implementation extends FileMaker_Command_Implementation
{
  function FileMaker_Command_PerformScript_Implementation($V0ab34ca9, $Vc6140495, $V2550889a, $V9b479e5e = null)
 {
 FileMaker_Command_Implementation::FileMaker_Command_Implementation($V0ab34ca9, $Vc6140495);
$this->_script = $V2550889a;
$this->_scriptParams = $V9b479e5e;
}
 function execute()
 { 
 $V21ffce5b = $this->_getCommandParams(); 
 $V21ffce5b['-findany'] = true; 
 $V0f635d0e = $this->_fm->_execute($V21ffce5b);
if (FileMaker::isError($V0f635d0e)) {
 return $V0f635d0e;
} 
 return $this->_getResult($V0f635d0e);
}
}

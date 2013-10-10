<?php


$objAltConfig = $this->getObject('altconfig','config');
$modPath=$objAltConfig->getModulePath();
$replacewith="";
$docRoot=$_SERVER['DOCUMENT_ROOT'];
$resourcePath=str_replace($docRoot,$replacewith,$modPath);
$codebase="http://" . $_SERVER['HTTP_HOST'].$resourcePath.'/efl/resources';
$essayid=$this->getparam('essayid');
$content=$this->essays->getEssayContent($essayid);

$str='<applet
      codebase="'.$content[0]['content'].'"
      archive="jefla.jar"
      code="efla.EflaView"
      width=1024
      height=768>

</applet>


';
echo $str;

?>
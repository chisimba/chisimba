<?php
include 'SCA/SCA.php';

$name = 'William Shakespeare';

//$smd = file_get_contents('http://fsiu.uwc.ac.za/chisimba_modules/webservices/server.php?wsdl');
//file_put_contents('HelloService.wsdl', $smd);
$service = SCA::getService('server.php');
//var_dump($service);
echo $service->lookup($name);
echo "<br />";
echo $service->hello('Paul');
echo "<br />";
echo "<br />";
$s = new SoapClient('http://fsiu.uwc.ac.za/chisimba_modules/webservices/server.php?wsdl', array('location' =>
'http://fsiu.uwc.ac.za/chisimba_modules/webservices/server.php?wsdl'));
var_dump($s->hello(array('name' => $name)));
//var_dump($s);

?>
<?php
include 'wsdlparser_class_inc.php';

$serv = new wsdlparser('http://localhost/sca/helloworld.php?wsdl', array());
echo $serv->generateObjFromWSDL();
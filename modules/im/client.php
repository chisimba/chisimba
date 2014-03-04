<?php
include('/var/www/chisimba/framework/app/lib/pear/XML/RPC.php');

$params = array(new XML_RPC_VALUE('pscott209@gmail.com/TALK82137'), new XML_RPC_VALUE(time()));
$msg = new XML_RPC_Message('im.add', $params);
		$mirrorserv = '127.0.0.1';
		$mirrorurl = '/chisimba/framework/app/index.php?module=api';
		$cli = new XML_RPC_Client($mirrorurl, $mirrorserv);
		$cli->setDebug(0);

		// send the request message
		$resp = $cli->send($msg);
		if (!$resp)
		{
			die('fok');
			exit;
		}
		if (!$resp->faultCode())
		{
			$val = $resp->value();
			return $val->serialize($val);
		}
		else
		{
			/*
			* Display problems that have been gracefully caught and
			* reported by the xmlrpc server class.
			*/
			echo $resp->faultCode() . ": ".$resp->faultString();
		}
?>
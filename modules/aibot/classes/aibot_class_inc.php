<?php

class aibot extends object
{
    protected $enabled;
    protected $objClient;
    protected $objSysConfig;

    public function init()
    {
        require_once $this->getPearResource('XML/RPC.php');

        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->enabled      = (boolean) $this->objSysConfig->getValue('enabled', 'aibot');
        $url                = parse_url($this->objSysConfig->getValue('url', 'aibot'));
        $this->objClient    = new XML_RPC_Client($url['path'], $url['host'], $url['port']);
    }

    public function chat($text, $name)
    {
        $params   = array(new XML_RPC_Value($text, 'string'), new XML_RPC_Value($name, 'string'));
        $message  = new XML_RPC_Message('chat', $params);
        $response = $this->objClient->send($message)->value()->getval();

        return $response;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}

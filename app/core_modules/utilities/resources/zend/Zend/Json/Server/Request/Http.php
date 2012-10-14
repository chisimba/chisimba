<?php

require_once 'Zend/Json/Server/Request.php';

class Zend_Json_Server_Request_Http extends Zend_Json_Server_Request
{
    /**
     * Raw JSON pulled from POST body
     * @var string
     */
    protected $_rawJson;

    /**
     * Constructor
     *
     * Pull JSON request from raw POST body and use to populate request.
     * 
     * @return void
     */
    public function __construct()
    {
        $json = file_get_contents('php://input');
        $this->_rawJson = $json;
        if (!empty($json)) {
            $this->loadJson($json);
        }
    }

    /**
     * Get JSON from raw POST body
     * 
     * @return string
     */
    public function getRawJson()
    {
        return $this->_rawJson;
    }
}

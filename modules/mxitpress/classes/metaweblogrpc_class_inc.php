<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * XML-RPC Client class
 *
 * @author Paul Scott
 * @author Nic Appleby
 * @copyright GPL
 * @package packages
 * @version 0.1
 */
class metaweblogrpc extends object
{
    /**
     * Language Object
     *
     * @var object
     */
    public $objLanguage;

    /**
     * Config object
     *
     * @var object
     */
    public $objConfig;

    /**
     * Sysconfig object
     *
     * @var object
     */
    public $sysConfig;

    /**
     * Port
     */
    public $port = 80;

    /**
     * Proxy info
     */
    public $proxy;

    /**
     * Proxy object
     */
    public $objProxy;


    /**
     * Standard init function
     *
     * @param void
     * @return void
     */
    public function init()
    {
        require_once($this->getPearResource('XML/RPC.php'));
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objProxy = $this->getObject('proxyparser', 'utilities');
        $this->objPing = $this->getObject('ping', 'utilities');

        // get the proxy info if set
        $proxyArr = $this->objProxy->getProxy();
        
        if (is_array($proxyArr) && !empty($proxyArr) && $proxyArr['proxy_protocol'] != '' && $_SERVER['HTTP_HOST'] != 'localhost') {
            if(!isset($proxyArr['proxy_user']))
            {
                $proxyArr['proxy_user'] = '';
            }
            if(!isset($proxyArr['proxy_pass']))
            {
                $proxyArr['proxy_pass'] = '';
            }
            $this->proxy = array(
                'proxy_host' => $proxyArr['proxy_host'],
                'proxy_port' => $proxyArr['proxy_port'],
                'proxy_user' => $proxyArr['proxy_user'],
                'proxy_pass' => $proxyArr['proxy_pass'],
            );
        }
        else {
            $this->proxy = array(
                'proxy_host' => '',
                'proxy_port' => '',
                'proxy_user' => '',
                'proxy_pass' => '',
            );
        }
    }
    
    /**
     * Method to get a list of available modules from the rpc server
     *
     * @param void
     * @return string
     */
    public function postToBlog($postdata)
    {
        // There are no params in this method call, so we pass an empty arrayi 
        $content = new XML_RPC_Value(array(
            "title" => new XML_RPC_Value($postdata['title'], "string"),
            "mt_excerpt" => new XML_RPC_Value($postdata['title'], "string"),
            "description" => new XML_RPC_Value($postdata['content'], "string"),
            "link" => new XML_RPC_Value('http://www.chisimba.com', "string")
            ), "struct"); 

        $params = array(new XML_RPC_VALUE('', 'string'), new XML_RPC_VALUE($postdata['username'], 'string'), new XML_RPC_VALUE(base64_decode($postdata['password']), 'string'), $content, new XML_RPC_VALUE(true, 'boolean'));

        // Construct the method call (message). 
        $msg = new XML_RPC_Message('metaWeblog.newPost', $params); 

        $cli = new XML_RPC_Client($postdata['endpoint'], $postdata['url'], $this->port, $this->proxy['proxy_host'], $this->proxy['proxy_port'], $this->proxy['proxy_user'], $this->proxy['proxy_pass']);
        $cli->setDebug(0);

        // send the request message
        $resp = $cli->send($msg);
        if (!$resp)
        {
            return $this->objLanguage->languageText("mod_packages_commserr", "packages").": ".$cli->errstr;
            exit;
        }
        if (!$resp->faultCode())
        {
            $val = $resp->value();
            $val->serialize($val);
            return $this->objLanguage->languageText("mod_mxitpress_success", "mxitpress");
        }
        else
        {
            /*
            * Display problems that have been gracefully caught and
            * reported by the xmlrpc server class.
            */
            return $this->objLanguage->languageText("mod_packages_faultcode", "packages").": ".$resp->faultCode() . $this->objLanguage->languageText("mod_packages_faultreason", "packages").": ".$resp->faultString();
        }
    }
    
}
?>

<?PHP
/**
 * Adaptor Pattern around the PEAR::Config Object
 * This class will provide the kng configuration to Engine
 *
 * @author Paul Scott
 * @package config
 */
//grab the pear::Config properties
// include class
include("Config.php");

class altconfig
{
    protected $_objPearConfig;
    protected $_root;

    public function __construct()
    {
        // instantiate object
        $this->_objPearConfig = new Config();
        $this->_root =& $this->_objPearConfig->parseConfig($config, "XML");
    }

    protected function readConfig()
    {

    }


}

?>
<?PHP
/**
 * This is a class, based on a script by Knut Urdalen, that will enable the user
 * to connect to any webservice on the internet that provides a WSDL (Web Service
 * Description Language). WSDL files are generally in the form of an XML document
 * that describes any number of functions provided by the service layer.
 *
 * Webservices have become increasingly popular in the last few years, as a way for
 * a number of legacy, as well as cross platform, applications to exchange data.
 * This has far reaching consequences, as diverse applications such as C#, Python, .Net,
 * Mono and others have the ability to communicate and exchange data in a relatively
 * standardised environment.
 *
 * This PHP Object aims to create an object for use by the end user to create a simple
 * client interface to communicate with the web services passed to the class constructor.
 *
 * The target audience for this object are developers as well as high end end users.
 * This should not be used as a normal user object.
 *
 * @filesource
 * @author Paul Scott <pscott@uwc.ac.za>
 * @author Knut Urdalen <knut.urdalen@telio.no>
 * @link http://fsiu.uwc.ac.za
 * @link http://www.urdalen.no/wsdl2php
 * @example wsdl2phptest.php
 * @package wsdl2php
 * @subpackage class wsdlparser
 * @since 07/03/2006
 * @version 1.0
 */

// disable WSDL cache
ini_set('soap.wsdl_cache_enabled', 0);

class wsdlparser
{
    /**
     * The service description array.
     *
     * @access public
     * @var array
     */
    public $service;

    /**
     * DOM Object
     *
     * @access private
     * @var object
     */
    private $dom;

    /**
     * Client instance
     *
     * @access private
     * @var object
     */
    private $client;

    /**
     * Error message
     *
     * @access private
     * @var object
     */
    private $e;

    /**
     * The WSDL passed from the constructor
     *
     * @access private
     * @var mixed
     */
    private $wsdl;

    /**
     * Namespace of target
     *
     * @access private
     * @var array
     */
    private $targetNamespace = '';

    /**
     * Documentation gleaned from the WSDL (if any)
     *
     * @access private
     * @var array
     */
    private $doc;

    /**
     * Reserved keywords that cannot be used in creation of the user object
     *
     * @access private
     * @var array
     */
    private static $keywords;

    /**
     * Primitive types
     *
     * @access private
     * @var array
     */
    private $primitive_types;

    /**
     * Class constructor
     *
     * @param mixed (string) $wsdl the wsdl file
     * @param array $options proxy options
     * @return void | $e error on failure
     */
    public function __construct($wsdl, $options)
    {
        $this->wsdl = $wsdl;

        try
        {
            $this->client = new SoapClient($this->wsdl, $options);
        }
        catch(SoapFault $e)
        {
            die($e);
        }

        try {
            $this->dom = @DOMDocument::load($this->wsdl);
        }
        catch (DOMErrorHandler $e)
        {
            die($e);
        }

    }

    /**
     * Method to grab the WSDL documentation from the exposed functions
     *
     * @access public
     * @param void
     * @return array $doc
     */
    public function getDocs()
    {
        // get documentation
        if(!isset($this->dom))
        {
            die($e);
        }
        try {
            $nodes = @$this->dom->getElementsByTagName('documentation');
        }
        catch (DOMErrorHandler $e)
        {
            die($e);
        }
        $doc = array('service' => '',
        'operations' => array());
        foreach($nodes as $node) {
            if( $node->parentNode->localName == 'service' ) {
                $doc['service'] = trim($node->parentNode->nodeValue);
            } else if( $node->parentNode->localName == 'operation' ) {
                $operation = $node->parentNode->getAttribute('name');
                //$parameterOrder = $node->parentNode->getAttribute('parameterOrder');
                $doc['operations'][$operation] = trim($node->nodeValue);
            }
        }
        $this->doc = $doc;
        return $this->doc;

    }

    /**
     * Method to get the namespace of the target WSDL file
     *
     * @access public
     * @param void
     * @return property
     */
    public function getTargetNameSpace()
    {
        // get targetNamespace
        $this->targetNamespace = '';
        $nodes = $this->dom->getElementsByTagName('definitions');
        foreach($nodes as $node) {
            $this->targetNamespace = $node->getAttribute('targetNamespace');
        }
        return $this->targetNamespace;

    }

    /**
     * Method to declare the service
     *
     * @access public
     * @param void
     * @return property
     */
    public function declareService()
    {
        // declare service
        $service = array('class' => $this->dom->getElementsByTagNameNS('*', 'service')->item(0)->getAttribute('name'),
        'wsdl' => $this->wsdl,
        'doc' => $this->doc['service'],
        'functions' => array()
        );

        // PHP keywords - can not be used as constants, class names or function names!
        $this->keywords = array('and', 'or', 'xor', 'as', 'break', 'case',
                                'cfunction', 'class',
                                'continue', 'declare', 'const', 'default', 'do', 'else',
                                'elseif', 'enddeclare', 'endfor', 'endforeach', 'endif',
                                'endswitch', 'endwhile', 'eval', 'extends', 'for', 'foreach',
                                'function', 'global', 'if', 'new', 'old_function', 'static',
                                'switch', 'use', 'var', 'while', 'array', 'die', 'echo',
                                'empty', 'exit', 'include', 'include_once', 'isset', 'list',
                                'print', 'require', 'require_once', 'return', 'unset',
                                '__file__', '__line__', '__function__', '__class__',
                                'abstract'
                                );

        // ensure legal class name (I don't think using . and whitespaces is allowed in terms of the SOAP standard, should check this out and may throw and exception instead...)
        $service['class'] = str_replace(' ', '_', $service['class']);
        $service['class'] = str_replace('.', '_', $service['class']);
        $service['class'] = str_replace('-', '_', $service['class']);

        if(in_array(strtolower($service['class']), $this->keywords)) {
            $service['class'] .= 'Service';
        }

        // verify that the name of the service is named as a defined class
        if(class_exists($service['class'])) {
            throw new Exception("Class '".$service['class']."' already exists");
        }
        $this->service = $service;
        return $this->service;

    }

    /**
     * Method to get all operations possible via the service WSDL
     *
     * @access public
     * @param void
     * @return property
     */
    public function getOperations()
    {
        // get operations
        $operations = $this->client->__getFunctions();
        foreach($operations as $operation)
        {
            $matches = array();
            if(preg_match('/^(\w[\w\d_]*) (\w[\w\d_]*)\(([\w\$\d,_ ]*)\)$/', $operation, $matches)) {
                $returns = $matches[1];
                $call = $matches[2];
                $params = $matches[3];
            } else if(preg_match('/^(list\([\w\$\d,_ ]*\)) (\w[\w\d_]*)\(([\w\$\d,_ ]*)\)$/', $operation, $matches)) {
                $returns = $matches[1];
                $call = $matches[2];
                $params = $matches[3];
            } else { // invalid function call
                throw new Exception('Invalid function call: '.$function);
            }

            $params = explode(', ', $params);

            $paramsArr = array();
            foreach($params as $param) {
                $paramsArr[] = explode(' ', $param);
            }
            //  $call = explode(' ', $call);
            $function = array('name' => $call,
            'method' => $call,
            'return' => $returns,
            'doc' => @$this->doc['operations'][$call],
            'params' => $paramsArr);

            // ensure legal function name
            if(in_array(strtolower($function['method']), $this->keywords)) {
                $function['name'] = '_'.$function['method'];
            }

            // ensure that the method we are adding has not the same name as the constructor
            if(strtolower($this->service['class']) == strtolower($function['method'])) {
                $function['name'] = '_'.$function['method'];
            }
            // ensure that there's no method that already exists with this name
            // this is most likely a Soap vs HttpGet vs HttpPost problem in WSDL
            // I assume for now that Soap is the one listed first and just skip the rest
            // this should be improved by actually verifying that it's a Soap operation that's in the WSDL file
            // QUICK FIX: just skip function if it already exists
            $add = true;
            foreach($this->service['functions'] as $func) {
                if($func['name'] == $function['name']) {
                    $add = false;
                }
            }
            if($add) {
                $this->service['functions'][] = $function;
            }
        }
        $types = $this->client->__getTypes();

        $this->primitive_types = array('string', 'int', 'long', 'float', 'boolean',
        'dateTime', 'double', 'short', 'UNKNOWN',
        'base64Binary'
        ); // TODO: dateTime is special, maybe use PEAR::Date or similar
        $this->service['types'] = array();
        foreach($types as $type) {
            $parts = explode("\n", $type);
            $class = explode(" ", $parts[0]);
            $class = $class[1];

            if( substr($class, -2, 2) == '[]' ) {
                // array skipping
                continue;
            }

            if( substr($class, 0, 7) == 'ArrayOf' ) {
                // skip 'ArrayOf*' types (from MS.NET, Axis etc.)
                continue;
            }

            $members = array();
            for($i=1; $i<count($parts)-1; $i++) {
                $parts[$i] = trim($parts[$i]);
                list($type, $member) = explode(" ", substr($parts[$i], 0, strlen($parts[$i])-1) );

                // check syntax
                if(preg_match('/^$\w[\w\d_]*$/', $member)) {
                    throw new Exception('illegal syntax for member variable: '.$member);
                    continue;
                }

                // IMPORTANT: Need to filter out namespace on member if presented
                if(strpos($member, ':')) {
                    // keep the last part
                    list($tmp, $member) = explode(':', $member);
                }

                // OBS: Skip member if already presented
                // (this shouldn't happen, but I've actually seen it in a WSDL-file)
                // "It's better to be safe than sorry" (ref Morten Harket)
                $add = true;
                foreach($members as $mem) {
                    if($mem['member'] == $member) {
                        $add = false;
                    }
                }
                if($add) {
                    $members[] = array('member' => $member, 'type' => $type);
                }
            }

            $this->service['types'][] = array('class' => $class, 'members' => $members);
        }
    }//end function

    /**
     * Method to return a formatted string of the client code that is generated
     *
     * @access public
     * @param void
     * @return mixed
     */
    public function writeCode()
    {
        // add types
        foreach($this->service['types'] as $type) {
            $code = "/**\n";
            $code .= " * ".$type['doc']."\n";
            $code .= " * \n";
            $code .= " * @package\n";
            $code .= " * @copyright\n";
            $code .= " */\n";
            $code .= "class ".$type['class']." {\n";
            foreach($type['members'] as $member) {
                $code .= "  /* ".$member['type']." */\n";
                $code .= "  public \$".$member['member'].";\n";
            }
            $code .= "}\n\n";

            //print "Writing ".$type['class'].".php...";
            $filename = $type['class'].".php";

        }

        // add service

        // page level docblock
        $code = "/**\n";
        $code .= " * ".$this->service['class']." class file\n";
        $code .= " * \n";
        $code .= " * @author    {author}\n";
        $code .= " * @copyright {copyright}\n";
        $code .= " * @package   {package}\n";
        $code .= " */\n\n";


        // require types
        foreach($this->service['types'] as $type) {
            $code .= "/**\n";
            $code .= " * ".$type['class']." class\n";
            $code .= " */\n";
            $code .= "require_once '".$type['class'].".php';\n";
        }

        $code .= "\n";

        // class level docblock
        $code .= "/**\n";
        $code .= " * ".$this->service['class']." class\n";
        $code .= " * \n";
        $code .= $this->parse_doc(" * ", $this->service['doc']);
        $code .= " * \n";
        $code .= " * @author    {author}\n";
        $code .= " * @copyright {copyright}\n";
        $code .= " * @package   {package}\n";
        $code .= " */\n";

        $code .= "class ".$this->service['class']." extends SoapClient {\n\n";
        $code .= "  public function ".$this->service['class']."(\$wsdl = \"".$this->service['wsdl']."\", \$options = array()) {\n";
        $code .= "    parent::__construct(\$wsdl, \$options);\n";
        $code .= "  }\n\n";

        foreach($this->service['functions'] as $function) {
            $code .= "  /**\n";
            $code .= $this->parse_doc("   * ", $function['doc']);
            $code .= "   *\n";

            $signature = array(); // used for function signature
            $para = array(); // just variable names
            foreach($function['params'] as $param) {
                $code .= "   * @param ".$param[0]." ".$param[1]."\n";
                $signature[] = (in_array($param[0], $this->primitive_types)) ? $param[1] : implode(' ', $param);
                $para[] = $param[1];
            }
            $code .= "   * @return ".$function['return']."\n";
            $code .= "   */\n";
            $code .= "  public function ".$function['name']."(".implode(', ', $signature).") {\n";

            $code .= "    return \$this->__call('".$function['method']."', array(";
            $params = array();
            if(!in_array('', $signature)) { // no arguments!
                foreach($signature as $param) {
                    if(strpos($param, ' ')) { // slice
                        $param = array_pop(explode(' ', $param));
                    }
                    $params[] = "      new SoapParam(".$param.", '".substr($param, 1, strlen($param))."')";
                }
                $code .= "\n      ";
                $code .= implode(",\n      ", $params);
                $code .= "\n      ),\n";
            } else {
                $code .= "),\n";
            }
            $code .= "      array(\n";
            $code .= "            'uri' => '".$this->targetNamespace."',\n";
            $code .= "            'soapaction' => ''\n";
            $code .= "           )\n";
            $code .= "      );\n";
            $code .= "  }\n\n";
        }
        $code .= "}\n\n";

        return $code;
    }

    /**
     * Method to parse the received documentation
     *
     * @param mixed $prefix
     * @param mixed $doc
     * @return mixed
     */
    public function parse_doc($prefix, $doc) {
        $code = "";
        $words = split(' ', $doc);
        $line = $prefix;
        foreach($words as $word) {
            $line .= $word.' ';
            if( strlen($line) > 90 ) { // new line
                $code .= $line."\n";
                $line = $prefix;
            }
        }
        $code .= $line."\n";
        return $code;
    }

    /**
     * Method to tie it all together
     *
     * @access public
     * @param void
     * @return mixed
     */
    public function generateObjFromWSDL()
    {
        $this->getDocs();
        $this->getTargetNameSpace();
        $this->declareService();
        $this->getOperations();
        return $this->writeCode();

    }
}//end class
?>
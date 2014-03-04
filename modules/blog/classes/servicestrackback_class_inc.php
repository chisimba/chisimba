<?php
/**
 * Services_Trackback.
 *
 * This is the main file of the Services_Trackback package. This file has to be
 * included for usage of Services_Trackback.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Webservices
 * @package   Trackback
 * @author    Tobias Schlitt <toby@php.net>
 * @author    Paul Scott - bug fixes
 * @copyright 2005-2006 The PHP Group
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @version   $Id: servicestrackback_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @link      http://pear.php.net/package/Services_Trackback
 * @since     File available since Release 0.1.0
 */
// {{{ require_once

/**
 * Load PEAR error handling
 */
require_once 'PEAR.php';
// }}}
// {{{ Constants

/**
 * This constant is used with the @see Services_Trackback::autodiscover() method.
 * Using this constant you supress the URL check described in the trackback specs.
 */
define('SERVICES_TRACKBACK_STRICTNESS_LOW', 1);
/**
 * This constant is used with the @see Services_Trackback::autodiscover() method.
 * Using this constant you use a not so strict URL check than described in the
 * trackback specs. Only the domain name is checked.
 */
define('SERVICES_TRACKBACK_STRICTNESS_MIDDLE', 2);
/**
 * This constant is used with the @see Services_Trackback::autodiscover() method.
 * Using this constant activate the URL check described in the trackback specs.
 */
define('SERVICES_TRACKBACK_STRICTNESS_HIGH', 3);
// }}}

/**
 * Trackback
 * A generic class to send/receive trackbacks.
 *
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @category  Webservices
 * @package   Trackback
 * @author    Tobias Schlitt <toby@php.net>
 * @copyright 2005-2006 The PHP Group
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Services_Trackback
 * @since     0.1.0
 * @access    public
 */
class servicestrackback extends object
{
    // {{{ var $_data
    
    /**
     * The necessary trackback data.
     *
     * @var    array
     * @since  0.1.0
     * @access protected
     */
    public $_data = array(
        'id' => '',
        'title' => '',
        'excerpt' => '',
        'blog_name' => '',
        'url' => '',
        'trackback_url' => '',
        'host' => '',
        'extra' => array() ,
    );
    // }}}
    // {{{ var $_options
    
    /**
     * Options to influence Services_Trackback.
     *
     * @see    Services_Trackback::create()
     * @since  0.4.0
     * @var    array
     * @access protected
     */
    public $_options = array(
        // Options for Services_Trackback directly
        'strictness' => SERVICES_TRACKBACK_STRICTNESS_LOW,
        'timeout' => 30, // seconds
        'fetchlines' => 30,
        'fetchextra' => true,
        // Options for HTTP_Request class
        'httprequest' => array(
            'allowRedirects' => true,
            'maxRedirects' => 2,
            'useragent' => 'Chisimba Trackback v@package_version@'
        ) ,
    );
    // }}}
    // {{{ var $_spamChecks
    
    /**
     * Description for public
     * @var    array
     * @access public
     */
    public $_spamChecks = array();
    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $objProxy;
    // }}}
    // {{{ Services_Trackback()
    
    /**
     * Constructor
     * Creates a new Trackback object. Private because of factory use.
     *
     * @since  0.1.0
     * @access protected
     * @return void
     */
    public function init() 
    {
        $this->objProxy = $this->getObject('proxyparser', 'utilities');
    }
    // }}}
    // {{{ create()
    
    /**
     * Factory
     * This static method is used to create a trackback object. (Services_Trackback::create($data))
     * The factory requires a data array as described below for creation. The 'id' key is
     * obligatory for this method. Every other data is not quite necessary for the creation, but
     * might be necessary for calling other methods afterwards. See the specific methods for
     * further info on which data is required.
     *
     * @since  0.2.0
     * @static
     * @access public
     * @param  array                      $data    Data for the trackback, which is obligatory:
     *                                             'id'                The ID of the trackback target.
     *                                             Data which is optional (for construction, not for specific methods):
     *                                             'title'             string  Title of the trackback sending/receiving blog entry.
     *                                             'excerpt'           string  Abstract of the trackback sending/receiving blog entry.
     *                                             'blog_name'         string  Name of the trackback sending/receiving weblog.
     *                                             'url'               string  URL of the trackback sending/receiving blog entry.
     *                                             'trackback_url'     string  URL to send trackbacks to.
     *                                             'extra'             array   Content of $_SERVER, captured while doing Services_Trackback::receive().
     * @param  array                      $options Options to set for this trackback. Valid options:
     *                                             'strictness':       int     The default strictness to use in @see Services_Trackback::autodiscover().
     *                                             'timeout':          int     The default timeout for network operations in seconds.
     *                                             'fetchlines':       int     The max number of lines to fetch over the network.
     *                                             'httprequest'       array   The options utilized by HTTP_Request are stored here.
     *                                             The following options are the most commonly used for HTTP_Request in
     *                                             Services_Trackback. All other options are supported too,
     * @see    HTTP_Request::HTTP_Request() for more detailed documentation.
     *         Some options for HTTP_Request are overwritten through the global settings of
     *         Services_Trackback (such as timeout).
     *         'timeout':          float   THE TIMEOUT SETTING IS OVERWRITTEN BY THE GLOBAL Services_Trackback SETTING.
     *         'allowRedirects':   bool    Wether to follow HTTP redirects or not.
     *         'maxRedirects':     int     Maximum number of redirects.
     *         'useragent':        string  The user agent to use for HTTP requests.
     *
     * @return object(Services_Trackback) The newly created Trackback.
     */
    function &create($data, $options = null) 
    {
        // Sanity check
        $options = isset($options) && is_array($options) ? $options : array();
        // Create trackback
        // $trackback = new $this; //servicestrackback();
        $res = $this->_fromArray($data);
        if (PEAR::isError($res)) {
            return $res;
        }
        $res = $this->setOptions($options);
        if (PEAR::isError($res)) {
            return $res;
        }
        return $trackback;
    }
    // }}}
    // {{{ setOptions()
    
    /**
     * setOptions
     * Set options for the trackback.
     *
     * @since  0.4.0
     * @access public
     * @see    Services_Trackback::create()
     * @see    Services_Trackback::getOptions()
     * @param  array  $options Pairs of 'option' => 'value' as described at @see Services_Trackback::create().
     * @return mixed  Bool true on success, otherwise PEAR_Error.
     */
    function setOptions($options) 
    {
        foreach($options as $option => $value) {
            if (!isset($this->_options[$option])) {
                return PEAR::raiseError('Desired option "' . $option . '" not available.');
            }
            switch ($option) {
                case 'strictness':
                    if (!is_int($value) || ($value < 1) || ($value > 3)) {
                        return PEAR::raiseError('Invalid value for option "' . $option . '", must be one of SERVICES_TRACKBACK_STRICTNESS_LOW, SERVICES_TRACKBACK_STRICTNESS_MIDDLE, SERVICES_TRACKBACK_STRICTNESS_HIGH.');
                    }
                    break;

                case 'timeout':
                    if (!is_int($value) || ($value < 0)) {
                        return PEAR::raiseError('Invalid value for option "' . $option . '", must be int >= 0.');
                    }
                    break;

                case 'fetchlines':
                    if (!is_int($value) || ($value < 1)) {
                        return PEAR::raiseError('Invalid value for option "' . $option . '", must be int >= 1.');
                    }
                    break;

                case 'fetchextra':
                    if (!is_bool($value)) {
                        return PEAR::raiseError('Invalid value for option "' . $option . '", must be bool.');
                    }
                    break;

                case 'httprequest':
                    if (!is_array($value)) {
                        return PEAR::raiseError('Invalid value for option "' . $option . '", must be array.');
                    }
                    break;
            }
            $this->_options[$option] = $value;
        }
        return true;
    }
    // }}}
    // {{{ getOptions()
    
    /**
     * getOptions
     * Get the currently set option set.
     *
     * @since  0.4.0
     * @access public
     * @see    Services_Trackback::setOptions()
     * @see    Services_Trackback::create()
     * @return array  The currently active options.
     */
    function getOptions() 
    {
        return $this->_options;
    }
    // }}}
    // {{{ autodiscover()
    
    /**
     * autodiscover
     * Checks a given URL for trackback autodiscovery code.
     *
     * @since  0.2.0
     * @access public
     * @return bool   True on success, otherwise PEAR_Error.
     */
    function autodiscover() 
    {
        $necessaryData = array(
            'url'
        );
        $res = $this->_checkData($necessaryData);
        if (PEAR::isError($res)) {
            return $res;
        }
        $url = $this->_data['url'];
        /*
        Sample autodiscovery code
        <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
        <rdf:Description
        rdf:about="http://pear.php.net/package/Net_FTP"
        dc:identifier="http://pear.php.net/package/Net_FTP"
        dc:title="Net_FTP"
        trackback:ping="http://pear.php.net/trackback/trackback.php?id=Net_FTP" />
        </rdf:RDF>
        */
        // Receive file contents.
        $content = $this->_getContent($url);
        if (PEAR::isError($content)) {
            return $content;
        }
        // Get trackback identifier
        if (!preg_match('@dc:identifier\s*=\s*["\'](http:[^"\']+)"@i', $content, $matches)) {
            return PEAR::raiseError('No trackback RDF found in "' . $url . '".');
        }
        $identifier = trim($matches[1]);
        // Get trackback URI
        if (!preg_match('@trackback:ping\s*=\s*["\'](http:[^"\']+)"@i', $content, $matches)) {
            return PEAR::raiseError('No trackback URI found in "' . $url . '".');
        }
        $trackbackUrl = trim($matches[1]);
        // Check if the URL to trackback matches the identifier from the autodiscovery code
        $res = $this->_checkURLs($url, $identifier, $this->_options['strictness']);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->_data['trackback_url'] = $trackbackUrl;
        return true;
    }
    // }}}
    // {{{ send()
    
    /**
     * send
     * This method sends a trackback to the trackback_url saved in it. The
     * data array of the trackback object can be completed by submitting the
     * necessary data through the $data parameter of this method.
     * The following data has to be set to call this method:
     *              'title'             Title of the weblog entry sending the trackback.
     *              'url'               URL of the weblog entry sending the trackback.
     *              'excerpt'           Excerpt of the weblog entry sending the trackback.
     *              'blog_name'         Name of the weblog sending the trackback.
     *              'trackback_url'     URL to send the trackback to.
     * Services_Trackback::send() requires PEAR::HTTP_Request. The options for the HTTP_Request
     * object are stored in the global options array using the key 'http_request'.
     *
     * @since  0.3.0
     * @access public
     * @param  string $data Additional data to complete the trackback.
     * @return mixed  True on success, otherwise PEAR_Error.
     */
    function send($data = null) 
    {
        $proxyArr = $this->objProxy->getProxy();
        if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            $parr = array(
                'proxy_host' => $proxyArr['proxy_host'],
                'proxy_port' => $proxyArr['proxy_port'],
                'proxy_user' => $proxyArr['proxy_user'],
                'proxy_pass' => $proxyArr['proxy_pass']
            );
        }
        // Consistancy check
        if (!isset($data)) {
            $data = array();
        }
        $this->_data = array_merge($this->_data, $data);
        //print_r($this->_data);
        $necessaryData = array(
            'title',
            'url',
            'excerpt',
            'blog_name',
            'trackback_url'
        );
        $res = $this->_checkData($necessaryData);
        if (PEAR::isError($res)) {
            return $res;
        }
        // Get URL
        $url = str_replace('&amp;', '&', $this->_data['trackback_url']);
        //echo $url; die();
        $options = $this->_options['httprequest'];
        $options['timeout'] = $this->_options['timeout'];
        // init curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Chisimba Trackback Agent");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($proxyArr) && $proxyArr['proxy_protocol'] != '') {
            curl_setopt($ch, CURLOPT_PROXY, $proxyArr['proxy_host'] . ":" . $proxyArr['proxy_port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyArr['proxy_user'] . ":" . $proxyArr['proxy_pass']);
        }
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_options['httprequest']);
        $post_data = array();
        $post_data['URL'] = urlencode($this->_data['url']);
        $post_data['url'] = urlencode($this->_data['url']);
        $post_data['title'] = $this->_data['title'];
        $post_data['blog_name'] = $this->_data['blog_name'];
        $post_data['excerpt'] = $this->_data['excerpt'];
        if (isset($this->_data['itemId'])) {
            $post_data['itemId'] = strip_tags($this->_data['itemId']);
        }
        if (isset($this->_data['trackId'])) {
            $post_data['trackId'] = strip_tags($this->_data['trackId']);
        }
        if (isset($this->_data['itemName'])) {
            $post_data['itemName'] = strip_tags($this->_data['itemName']);
        }
        $o = "";
        foreach($post_data as $k => $v) {
            $o.= "$k=" . utf8_encode($v) . "&";
        }
        $post_data = substr($o, 0, -1);
        //print_r($post_data); die();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // send the request
        $code = curl_exec($ch);
        //var_dump($code); die();
        // Check return code
        if ($code != 200) {
            //log_debug("trackback failed with response code $code");
            return FALSE;
        }
        //log_debug("Successful Trackback");
        return $this->_interpretTrackbackResponse($code);
    }
    // }}}
    // {{{ getAutodiscoveryCode()
    
    /**
     * getAutodiscoverCode
     * Returns the RDF Code for a given website to let weblogs autodiscover
     * the possibility of tracking it back.
     * The following data has to be set to call this method:
     *              'id'
     *              'title'
     *              'url'
     *              'trackback_url'
     * @since  0.1.0
     * @access public
     * @param  bool   $comments Whether to include HTML comments around the RDF using <!-- -->
     * @return string RDF code
     */
    function getAutodiscoveryCode($comments = true) 
    {
        $necessaryData = array(
            'title',
            'url',
            'trackback_url'
        );
        $res = $this->_checkData($necessaryData);
        if (PEAR::isError($res)) {
            return $res;
        }
        $data = $this->_getEncodedData($necessaryData);
        $res = <<<EOD
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">
    <rdf:Description
        rdf:about="%s"
        dc:identifier="%s"
        dc:title="%s"
        trackback:ping="%s" />
</rdf:RDF>
EOD;
        $res = sprintf($res, $data['url'], $data['url'], $data['title'], $data['trackback_url']);
        if ($comments) {
            return "<!--\n" . $res . "\n-->\n";
        }
        return $res . "\n";
    }
    // }}}
    // {{{ receive()
    
    /**
     * receive
     * Receives a trackback. The following data has to be set in the data array to fulfill this
     * operation:
     *      'id'
     *
     * @since  0.1.0
     * @access public
     * @return object Services_Trackback
     */
    function receive($data = null) 
    {
        if (!isset($data)) {
            $data = $_POST;
            $data['host'] = $_SERVER['REMOTE_ADDR'];
        }
        $necessaryPostData = array(
            'title',
            'excerpt',
            'url',
            'blog_name',
            'host'
        );
        $res = $this->_checkData(array(
            'id'
        ));
        if (PEAR::isError($res)) {
            return $res;
        }
        $res = $this->_checkData($necessaryPostData, $data);
        if (PEAR::isError($res)) {
            return PEAR::raiseError('POST data incomplete: ' . $res->getMessage());
        }
        $this->_data = array_merge($this->_data, $this->_getDecodedData($necessaryPostData, $data));
        if ($this->_options['fetchextra'] === true) {
            $this->_data['extra'] = $_SERVER;
        }
        return true;
    }
    // }}}
    // {{{ getResponseSuccess()
    
    /**
     * getResponseSuccess
     * Returns an XML response for a successful trackback.
     *
     * @since  0.1.0
     * @access public
     * @static
     * @return string The XML code
     */
    function getResponseSuccess() 
    {
        return <<<EOD
<?xml version="1.0" encoding="iso-8859-1"?>
<response>
<error>0</error>
</response>
EOD;
        
    }
    // }}}
    // {{{ getResponseError()
    
    /**
     * getResponseError
     * Returns an XML response for a trackback error.
     *
     * @since  0.1.0
     * @access public
     * @param  int    $code    The error code
     * @param  string $message The error message
     * @return void
     */
    function getResponseError($message, $code) 
    {
        $data = $this->_getEncodedData(array(
            'code',
            'message'
        ) , array(
            'code' => $code,
            'message' => $message
        ));
        $res = <<<EOD
<?xml version="1.0" encoding="iso-8859-1"?>
<response>
<error>%s</error>
<message>%s</message>
</response>
EOD;
        return sprintf($res, $data['code'], $data['message']);
    }
    // }}}
    // {{{ addSpamCheck()
    
    /**
     * addSpamCheck
     * Add a spam check module to the trackback.
     *
     * @since  0.5.0
     * @access public
     * @see    Services_Trackback::removeSpamCheck()
     * @see    Services_Trackback::checkSpam()
     * @param  object(Services_Trackback_SpamCheck) $spamCheck The spam check module to add.
     * @param  int                                  $priority  A priority value for the spam check. Lower priority indices are processed earlier.
     *                                                         If no priority level is set, 0 is assumed.
     * @return mixed                                Added SpamCheck module instance on success, otherwise PEAR_Error.
     */
    function &addSpamCheck(&$spamCheck, $priority = 0) 
    {
        if (!is_object($spamCheck) || !is_subclass_of($spamCheck, 'Services_Trackback_SpamCheck')) {
            return PEAR::raiseError('Invalid spam check module.', -1);
        }
        $this->_spamChecks[$priority][] = &$spamCheck;
        return $spamCheck;
    }
    // }}}
    // {{{ createSpamCheck()
    
    /**
     * createSpamCheck
     * Create and add a spam check module to the trackback.
     *
     * @since  0.5.0
     * @access public
     * @see    Services_Trackback::addSpamCheck()
     * @see    Services_Trackback::removeSpamCheck()
     * @see    Services_Trackback::checkSpam()
     * @param  string $spamCheckType Name of the spamcheck module to create and add.
     * @param  array  $options       Options for the spamcheckmodule.
     * @param  int    $priority      A priority value for the spam check. Lower priority indices are processed earlier.
     *                               If no priority level is set, 0 is assumed.
     * @return mixed  Instance of the created SpamCheck module or PEAR_Error.
     */
    function &createSpamCheck($spamCheckType, $options = array() , $priority = 0) 
    {
        $filename = dirname(__FILE__) . '/Trackback/SpamCheck.php';
        $createfunc = array(
            'Services_Trackback_SpamCheck',
            'create'
        );
        // SpamCheck class already included?
        if (!class_exists($createfunc[0])) {
            if (!file_exists($filename)) {
                return PEAR::raiseError('SpamCheck subclass not found. Broken installation!');
            } else {
                include_once $filename;
            }
        }
        // SpamCheck class successfully included?
        if (!class_exists($createfunc[0])) {
            return PEAR::raiseError('SpamCheck subclass not found. Broken installation!');
        }
        $spamCheck = &call_user_func($createfunc, $spamCheckType, $options);
        $res = &$this->addSpamCheck($spamCheck, $priority);
        return $res;
    }
    // }}}
    // {{{ removeSpamCheck()
    
    /**
     * removeSpamCheck
     * Remove a spam check module from the trackback.
     *
     * @since  0.5.0
     * @access public
     * @see    Services_Trackback::addSpamCheck()
     * @see    Services_Trackback::checkSpam()
     * @param  object(Services_Trackback_SpamCheck) The spam check module to remove.
     * @return bool                                 True on success, otherwise PEAR_Error.
     */
    function removeSpamCheck(&$spamCheck) 
    {
        foreach($this->_spamChecks as $priority => $spamChecks) {
            foreach($spamChecks as $id => $spamCheck) {
                if ($this->_spamChecks[$priority][$id] === $spamCheck) {
                    unset($this->_spamChecks[$priority][$id]);
                    return true;
                }
            }
        }
        return PEAR::raiseError('Given spam check module not found.', -1);
    }
    // }}}
    // {{{ checkSpam()
    
    /**
     * checkSpam
     * Checks the given trackback against several spam protection sources
     * such as DNSBL, SURBL, Word BL,... The sources to check are defined using
     * Services_Trackback_SpamCheck modules.
     *
     * @since  0.5.0
     * @access public
     * @see    Services_Trackback::addSpamCheck()
     * @see    Services_Trackback::removeSpamCheck()
     * @param  bool   $continouseCheck Wether to check all spam protection modules or
     *                                 quit checking if one modules returns a positive result.
     * @return bool   True, if one of the sources
     */
    function checkSpam($continouse = false) 
    {
        $spam = false;
        foreach($this->_spamChecks as $priority => $spamChecks) {
            foreach($spamChecks as $id => $spamCheck) {
                if (!$continouse && $spam) {
                    // No need to check further
                    $this->_spamChecksResults[$priority][$id] = false;
                } else {
                    $tmpRes = $this->_spamChecks[$priority][$id]->check($this);
                    $this->_spamChecksResults[$priority][$id] = $tmpRes;
                    $spam = ($spam || $tmpRes);
                }
            }
        }
        return $spam;
    }
    // }}}
    // {{{ get()
    
    /**
     * get
     * Get data from the trackback. Returns the value of a given
     * key or PEAR_Error.
     *
     * @since  0.2.0
     * @access public
     * @param  string $key The key to fetch a value for.
     * @return mixed  A string value or a PEAR_Error on failure.
     */
    function get($key) 
    {
        return (isset($this->_data[$key])) ? $this->_data[$key] : PEAR::raiseError('Key ' . $key . ' not found.');
    }
    // }}}
    // {{{ set()
    
    /**
     * set
     * Set data of the trackback. Saves the value of a given
     * key , returning true on success, PEAR_Error on faulure.
     *
     * @since  0.2.0
     * @access public
     * @param  string $key The key to set a value for.
     * @param  string $val The value for the key.
     * @return mixed  Boolean true on success or a PEAR_Error on failure.
     */
    function set($key, $val) 
    {
        $this->_data[$key] = $val;
        return true;
    }
    // }}}
    // {{{ _fromArray()
    
    /**
     * Create a Trackback from a $data array.
     *
     * @since  0.2.0
     * @access protected
     * @param  array     $data The data array (@see Services_Trackback::create()).
     * @return mixed     True on success, otherwise PEAR_Error.
     */
    function _fromArray($data) 
    {
        $res = $this->_checkData(array(
            'id'
        ) , $data);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->_data = $data;
        return true;
    }
    // }}}
    // {{{ _getContent()
    
    /**
     * _getContent
     * Receive the content from a specific URL.
     *
     * @since  0.4.0
     * @access protected
     * @param  string    $url The URL to download data from.
     * @return string    The content.
     */
    function _getContent($url) 
    {
        $handle = @fopen($url, 'r');
        if (!is_resource($handle)) {
            return PEAR::raiseError('Could not open URL "' . $url . '"');
        }
        stream_set_timeout($handle, $this->_options['timeout']);
        $content = '';
        for ($i = 0; ($i < $this->_options['fetchlines']) && !feof($handle); $i++) {
            $content.= fgets($handle);
        }
        return $content;
    }
    // }}}
    // {{{ _getEncodedData()
    
    /**
     * _getEncodedData
     * Receives a number of data from the internal data store, encoded for XML usage.
     *
     * @since  0.1.0
     * @access protected
     * @param  array     $keys Data keys to receive
     * @param  array     $data Optionally the data to check (default is the object data).
     * @return void
     */
    function _getEncodedData($keys, $data = null) 
    {
        if (!isset($data)) {
            $data = &$this->_data;
        }
        foreach($keys as $key) {
            $res[$key] = htmlentities($data[$key]);
        }
        return $res;
    }
    // }}}
    // {{{ _getDecodedData()
    
    /**
     * _getDecodedData
     * Receives a number of data from the internal data store.
     *
     * @since  0.1.0
     * @access protected
     * @param  array     $keys Data keys to receive
     * @param  array     $data Optionally the data to check (default is the object data).
     * @return void
     */
    function _getDecodedData($keys, $data = null) 
    {
        if (!isset($data)) {
            $data = &$this->_data;
        }
        foreach($keys as $key) {
            $res[$key] = $data[$key];
        }
        return $res;
    }
    // }}}
    // {{{ _checkData()
    
    /**
     * _checkData
     * Checks a given array of keys for the validity of their data.
     *
     * @since  0.1.0
     * @access protected
     * @param  array     $keys Data keys to check.
     * @param  array     $data Optionally the data to check (default is the object data).
     * @return void
     */
    function _checkData($keys, $data = null) 
    {
        if (!isset($data)) {
            $data = &$this->_data;
        }
        //print_r($data);
        foreach($keys as $key) {
            if (empty($data[$key])) {
                return PEAR::raiseError('Invalid data. Key "' . $key . '" missing.');
            }
        }
        return true;
    }
    // }}}
    // {{{ _checkURLs()
    
    /**
     * _checkURLs
     * This little method checks if 2 URLs (the URL to trackback against the trackback
     * identifier found in the autodiscovery code) are equal.
     *
     * @see    Services_Trackback::autodiscover()
     * @since  0.2.0
     * @access protected
     * @param  string    $url1       The first URL.
     * @param  string    $url2       The second URL.
     * @param  constant  $strictness How strict to check URLs. Use one of SERVICES_TRACKBACK_STRICTNESS_* constants.
     * @return mixed     True on success, otherwise PEAR_Error.
     */
    function _checkURLs($url1, $url2, $strictness) 
    {
        switch ($strictness) {
            case SERVICES_TRACKBACK_STRICTNESS_HIGH:
                if ($url1 !== $url2) {
                    return PEAR::raiseError('URLs mismatch. "' . $url1 . '" !== "' . $url2 . '".');
                }
                break;

            case SERVICES_TRACKBACK_STRICTNESS_MIDDLE:
                $domainRegex = "@http://([^/]+).*@";
                $res = preg_match($domainRegex, $url1, $matches);
                if (!$res) {
                    return PEAR::raiseError('Invalid URL1, no domain part found ("' . $url1 . '").');
                }
                $domain1 = $matches[1];
                $res = preg_match($domainRegex, $url2, $matches);
                if (!$res) {
                    return PEAR::raiseError('Invalid URL1, no domain part found ("' . $url1 . '").');
                }
                $domain2 = $matches[1];
                if ($domain1 !== $domain2) {
                    return PEAR::raiseError('URLs missmatch. "' . $domain1 . '" !== "' . $domain2 . '".');
                }
                break;

            case SERVICES_TRACKBACK_STRICTNESS_LOW:
            default:
                // No checks, when strictness is low.
                break;
        }
        return true;
    }
    // }}}
    // {{{ _interpretTrackbackResponse()
    
    /**
     * Interpret the returned XML code, when sending a trackback.
     *
     * @see    Services_Trackback::send()
     * @since  0.3.0
     * @access protected
     * @return void      Mixed true on success, otherwise PEAR_Error.
     */
    function _interpretTrackbackResponse($response) 
    {
        if (!preg_match('@<error>([0-9]+)</error>@', $response, $matches)) {
            return PEAR::raiseError('Invalid trackback response, error code not found.');
        }
        $errorCode = $matches[1];
        // Error code 0 means no error.
        if ($errorCode == 0) {
            return true;
        }
        if (!preg_match('@<message>([^<]+)</message>@', $response, $matches)) {
            return PEAR::raiseError('Error code ' . $errorCode . ', no message received.');
        }
        return PEAR::raiseError('Error code ' . $errorCode . ', message "' . $matches[1] . '" received.');
    }
    // }}}
    
    /**
     * Overloading
     *
     * @since      0.1.0
     * @access     public
     * @deprecated
     */
    /*
    
    // Removed since 0.2.0
    
    */
}
?>
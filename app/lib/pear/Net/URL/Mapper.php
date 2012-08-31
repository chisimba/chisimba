<?php
/**
 * URL parser and mapper
 *
 * PHP version 5
 *
 * LICENSE:
 * 
 * Copyright (c) 2006, Bertrand Mansion <golgote@mamasam.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the 
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products 
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Net
 * @package    Net_URL_Mapper
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Net_URL_Mapper
 */

require_once 'Net/URL/Mapper/Path.php';
require_once 'Net/URL/Mapper/Exception.php';

/**
 * URL parser and mapper class
 *
 * This class takes an URL and a configuration and returns formatted data
 * about the request according to a configuration parameter
 *
 * @category   Net
 * @package    Net_URL_Mapper
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @version    Release: @package_version@
 */
class Net_URL_Mapper
{
    /**
    * Array of Net_URL_Mapper instances
    * @var array
    */
    private static $instances = array();

    /**
    * Mapped paths collection
    * @var array
    */
    protected $paths = array();

    /**
    * Prefix used for url mapping
    * @var string
    */
    protected $prefix = '';

    /**
    * Optional scriptname if mod_rewrite is not available
    * @var string
    */
    protected $scriptname = '';

    /**
    * Mapper instance id
    * @var string
    */
    protected $id = '__default__';

    /**
    * Class constructor
    * Constructor is private, you should use getInstance() instead.
    */
    private function __construct() { }

    /**
    * Returns a singleton object corresponding to the requested instance id
    * @param  string    Requested instance name
    * @return Object    Net_URL_Mapper Singleton
    */
    public static function getInstance($id = '__default__')
    {
        if (!isset(self::$instances[$id])) {
            $m = new Net_URL_Mapper();
            $m->id = $id;
            self::$instances[$id] = $m;
        }
        return self::$instances[$id];
    }

    /**
    * Returns the instance id
    * @return   string  Mapper instance id
    */
    public function getId()
    {
        return $this->id;
    }

    /**
    * Parses a path and creates a connection
    * @param    string  The path to connect
    * @param    array   Default values for path parts
    * @param    array   Regular expressions for path parts
    * @return   object  Net_URL_Mapper_Path
    */
    public function connect($path, $defaults = array(), $rules = array())
    {
        $pathObj = new Net_URL_Mapper_Path($path, $defaults, $rules);
        $this->addPath($pathObj);
        return $pathObj;
    }

    /**
    * Set the url prefix if needed
    *
    * Example: using the prefix to differenciate mapper instances
    * <code>
    * $fr = Net_URL_Mapper::getInstance('fr');
    * $fr->setPrefix('/fr');
    * $en = Net_URL_Mapper::getInstance('en');
    * $en->setPrefix('/en');
    * </code>
    *
    * @param    string  URL prefix
    */
    public function setPrefix($prefix)
    {
        $this->prefix = '/'.trim($prefix, '/');
    }

    /**
    * Set the scriptname if mod_rewrite not available
    *
    * Example: will match and generate url like
    * - index.php/view/product/1
    * <code>
    * $m = Net_URL_Mapper::getInstance();
    * $m->setScriptname('index.php');
    * </code>
    * @param    string  URL prefix
    */
    public function setScriptname($scriptname)
    {
        $this->scriptname = $scriptname;
    }

    /**
    * Will attempt to match an url with a defined path
    *
    * If an url corresponds to a path, the resulting values are returned
    * in an array. If none is found, null is returned. In case an url is
    * matched but its content doesn't validate the path rules, an exception is
    * thrown.
    *
    * @param    string  URL
    * @return   array|null   array if match found, null otherwise
    * @throws   Net_URL_Mapper_InvalidException
    */
    public function match($url)
    {
        $nurl = '/'.trim($url, '/');

        // Remove scriptname if needed
        
        if (!empty($this->scriptname) &&
            strpos($nurl, $this->scriptname) === 0) {
            $nurl = substr($nurl, strlen($this->scriptname));
            if (empty($nurl)) {
                $nurl = '/';
            }
        }

        // Remove prefix
        
        if (!empty($this->prefix)) {
            if (strpos($nurl, $this->prefix) !== 0) {
                return null;
            }
            $nurl = substr($nurl, strlen($this->prefix));
            if (empty($nurl)) {
                $nurl = '/';
            }
        }
        
        // Remove query string
        
        if (($pos = strpos($nurl, '?')) !== false) {
            $nurl = substr($nurl, 0, $pos);
        }

        $paths = array();
        $values = null;

        // Make a list of paths that conform to route format

        foreach ($this->paths as $path) {
            $regex = $path->getFormat();
            if (preg_match($regex, $nurl)) {
                $paths[] = $path;
            }   
        }

        // Make sure one of the paths found is valid

        foreach ($paths as $path) {
            $regex = $path->getRule();
            if (preg_match($regex, $nurl, $matches)) {
                $values = $path->getDefaults();
                array_shift($matches);
                $clean = array();
                foreach ($matches as $k => $v) {
                    $v = trim($v, '/');
                    if (!is_int($k) && $v !== '') {
                        $values[$k] = $v;
                    }
                }
                break;
            }
        }

        // A path conforms but does not validate

        if (is_null($values) && !empty($paths)) {
            $e = new Net_URL_Mapper_InvalidException('A path was found but is invalid.');
            $e->setPath($paths[0]);
            $e->setUrl($url);
            throw $e;
        }

        return $values;
    }

    /**
    * Generate an url based on given parameters
    *
    * Will attempt to find a path definition that matches the given parameters and
    * will generate an url based on this path.
    *
    * @param    array   Values to be used for the url generation
    * @param    array   Key/value pairs for query string if needed
    * @param    string  Anchor (fragment) if needed
    * @return   string|false    String if a rule was found, false otherwise
    */
    public function generate($values = array(), $qstring = array(), $anchor = '')
    {
        // Use root path if any

        if (empty($values) && isset($this->paths['/'])) {
            return $this->scriptname.$this->prefix.$this->paths['/']->generate($values, $qstring, $anchor);
        }

        foreach ($this->paths as $path) {
            $set = array();
            foreach ($values as $k => $v) {
                if ($path->hasKey($k, $v)) {
                    $set[$k] = $v;
                }
            }

            if (count($set) == count($values) &&
                count($set) <= $path->getMaxKeys()) {

                $req = $path->getRequired();
                if (count(array_intersect(array_keys($set), $req)) != count($req)) {
                    continue;
                }
                $gen = $path->generate($set, $qstring, $anchor);
                return $this->scriptname.$this->prefix.$gen;
            }
        }
        return false;
    }

    /**
    * Returns defined paths
    * @return array     Array of paths
    */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
    * Reset all paths
    * This is probably only useful for testing
    */
    public function reset()
    {
        $this->paths = array();
        $this->prefix = '';
    }

    /**
    * Add a new path to the mapper
    * @param object     Net_URL_Mapper_Path object
    */
    public function addPath(Net_URL_Mapper_Path $path)
    {
        $this->paths[$path->getPath()] = $path;
    }

}
?>
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2 base class
 *
 * PHP versions 4 and 5
 *
 * LICENSE: Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2004-2006 Lorenzo Alberton
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * require PEAR base class
 */
require_once 'PEAR.php';

/**
 * Allows redefinition of the default pageID.
 * This constant is needed to allow both NULL and EMPTY pageID values
 * and to have them match
 */
if (!defined('TRANSLATION2_DEFAULT_PAGEID')) {
    define('TRANSLATION2_DEFAULT_PAGEID', 'translation2_default_pageID');
}
/**
 * Class Error codes
 */
define('TRANSLATION2_ERROR',                      -1);
define('TRANSLATION2_ERROR_METHOD_NOT_SUPPORTED', -2);
define('TRANSLATION2_ERROR_CANNOT_CONNECT',       -3);
define('TRANSLATION2_ERROR_CANNOT_FIND_FILE',     -4);
define('TRANSLATION2_ERROR_DOMAIN_NOT_SET',       -5);
define('TRANSLATION2_ERROR_INVALID_PATH',         -6);
define('TRANSLATION2_ERROR_CANNOT_CREATE_DIR',    -7);
define('TRANSLATION2_ERROR_CANNOT_WRITE_FILE',    -8);
define('TRANSLATION2_ERROR_UNKNOWN_LANG',         -9);
define('TRANSLATION2_ERROR_ENCODING_CONVERSION', -10);
define('TRANSLATION2_ERROR_UNSUPPORTED',         -11);

/**
 * Translation2 base class
 *
 * This class provides an easy way to retrieve all the strings
 * for a multilingual site or application from a data source
 * (i.e. a db, an xml file or a gettext file).
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2004-2006 Lorenzo Alberton
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link       http://pear.php.net/package/Translation2
 */
class Translation2
{
    // {{{ class vars

    /**
     * Storage object
     * @var object
     * @access protected
     */
    var $storage = '';

    /**
     * Class options
     * @var array
     */
    var $options = array();

    /**
     * Default lang
     * @var array
     * @access protected
     */
    var $lang = array();

    /**
     * Current pageID
     * @var string
     * @access protected
     */
    var $currentPageID = null;

    /**
     * Array of parameters for the adapter class
     * @var array
     * @access protected
     */
    var $params = array();

    // }}}
    // {{{ Constructor

    /**
     * Constructor
     */
    function Translation2()
    {
        if (func_num_args()) {
            $msg = '<b>Translation2 error:</b>'
                  .' Don\'t use the constructor - use factory()';
            trigger_error($msg, E_USER_ERROR);
        }
    }

    // }}}
    // {{{ factory()

    /**
     * Return a Translation2 instance already initialized
     *
     * @param string $storageDriver Type of the storage driver
     * @param mixed  $options Additional options for the storage driver
     *                        (example: if you are using DB as the storage
     *                        driver, you have to pass the dsn string here)
     * @param array  $params Array of parameters for the adapter class
     *                      (i.e. you can set here the mappings between your
     *                      table/field names and the ones used by this class)
     * @return object Translation2 instance or PEAR_Error on failure
     * @static
     */
    function & factory($driver, $options='', $params=array())
    {
        $tr =& new Translation2;
        $tr->storage = Translation2::_storageFactory($driver, $options);
        if (PEAR::isError($tr->storage)) {
            return $tr->storage;
        }
        $tr->_setDefaultOptions();
        $tr->_parseOptions($params);
        $tr->storage->_parseOptions($params);
        return $tr;
    }

    // }}}
    // {{{ _storageFactory()

    /**
     * Return a storage driver based on $driver and $options
     *
     * @param  string $driver  Type of storage class to return
     * @param  string $options Optional parameters for the storage class
     * @return object Object   Storage object
     * @static
     * @access private
     */
    function & _storageFactory($driver, $options='')
    {
        $storage_path = 'Translation2/Container/'.strtolower($driver).'.php';
        $storage_class = 'Translation2_Container_'.strtolower($driver);
        require_once $storage_path;
        $storage =& new $storage_class;
        $err = $storage->init($options);
        if (PEAR::isError($err)) {
            return $err;
        }
        return $storage;
    }

    // }}}
    // {{{ setContainerOptions()

    /**
     * Set some storage driver options
     *
     * @param array $options
     * @return void
     * @access protected
     */
    function setContainerOptions($options)
    {
        $this->storage->_parseOptions($options);
    }

    // }}}
    // {{{ _setDefaultOptions()

    /**
     * Set some default options
     *
     * @return void
     * @access private
     */
    function _setDefaultOptions()
    {
        $this->options['ParameterPrefix']   = '&&';
        $this->options['ParameterPostfix']  = '&&';
        $this->options['ParameterAutoFree'] = true;
        $this->options['prefetch']          = true;
    }

    // }}}
    // {{{ _parseOptions()

    /**
     * Parse options passed to the base class
     *
     * @param  array
     * @access private
     */
    function _parseOptions($array)
    {
        foreach ($array as $key => $value) {
            if (isset($this->options[$key])) {
                $this->options[$key] = $value;
            }
        }
    }

    // }}}
    // {{{ getDecorator()

    /**
     * Return an instance of a decorator
     *
     * This method is used to get a decorator instance.
     * A decorator can be seen as a filter, i.e. something that can change
     * or handle the values of the objects/vars that pass through.
     *
     * @param  string $decorator  Name of the decorator
     * @return object Decorator object reference
     */
    function & getDecorator($decorator)
    {
        $decorator_path = 'Translation2/Decorator/'.$decorator.'.php';
        $decorator_class = 'Translation2_Decorator_'.$decorator;
        require_once $decorator_path;
        if (func_num_args() > 1) {
            $obj = func_get_arg(1);
            $new_decorator =& new $decorator_class($obj);
        } else {
            $new_decorator =& new $decorator_class($this);
        }
        return $new_decorator;
    }

    // }}}
    // {{{ setCharset()

    /**
     * Set charset used to read/store the translations
     *
     * @param string $charset
     */
    function setCharset($charset)
    {
        $res = $this->storage->setCharset($charset);
        if (PEAR::isError($res)) {
            return $res;
        }
    }

    // }}}
    // {{{ setLang()

    /**
     * Set default lang
     *
     * Set the language that shall be used when retrieving strings.
     *
     * @param string $langID language code (for instance, 'en' or 'it')
     */
    function setLang($langID)
    {
        $res = $this->storage->setLang($langID);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->lang = $res;
    }

    // }}}
    // {{{ setPageID($pageID)

    /**
     * Set default page
     *
     * Set the page (aka "group of strings") that shall be used when retrieving strings.
     * If you set it, you don't have to state it in each get() call.
     *
     * @param string $langID
     */
    function setPageID($pageID=null)
    {
        $this->currentPageID = $pageID;
    }

    // }}}
    // {{{ getLang()

    /**
     * get lang info
     *
     * Get some extra information about the language (its full name,
     * the localized error text, ...)
     *
     * @param string $langID
     * @param string $format ['name', 'meta', 'error_text', 'array']
     * @return mixed [string | array], depending on $format
     */
    function getLang($langID=null, $format='name')
    {
        if (is_null($langID)) {
            if (!isset($this->lang['id'])) {
                $msg = 'Translation2::getLang(): unknown language "'.$langID.'".'
                      .' Use Translation2::setLang() to set a default language.';
                return $this->storage->raiseError($msg, TRANSLATION2_ERROR_UNKNOWN_LANG);
            }
            $langID = $this->lang['id'];
        }
        $lang = $this->storage->getLangData($langID);
        if ($format == 'array') {
            return $lang;
        } elseif (isset($lang[$format])) {
            return $lang[$format];
        } elseif (isset($lang['name'])) {
            return $lang['name'];
        }
        $msg = 'Translation2::getLang(): unknown language "'.$langID.'".'
              .' Use Translation2::setLang() to set a default language.';
        return $this->storage->raiseError($msg, TRANSLATION2_ERROR_UNKNOWN_LANG);
    }

    // }}}
    // {{{ getLangs()

    /**
     * get langs
     *
     * Get some extra information about the languages (their full names,
     * the localized error text, their codes, ...)
     *
     * @param string $format ['ids', 'names', 'array']
     * @return array
     */
    function getLangs($format='name')
    {
        return $this->storage->getLangs($format);
    }

    // }}}
    // {{{ setParams()

    /**
     * Set parameters for next string
     *
     * Set the replacement for the parameters in the string(s).
     * Parameter delimiters are customizable.
     *
     * @param array $params
     */
    function setParams($params=null)
    {
        if (empty($params)) {
            $this->params = array();
        } elseif (is_array($params)) {
            $this->params = $params;
        } else {
            $this->params = array($params);
        }
    }

    // }}}
    // {{{ _replaceParams()

    /**
     * Replace parameters in strings
     * @param mixed $params
     * @access protected
     */
    function _replaceParams($strings)
    {
        if (empty($strings) || is_object($strings) || !count($this->params)) {
            return $strings;
        }
        if (is_array($strings)) {
            foreach ($strings as $key => $string) {
                $strings[$key] = $this->_replaceParams($string);
            }
        } else {
            if (strpos($strings, $this->options['ParameterPrefix']) !== false) {
                foreach ($this->params as $name => $value) {
        		    $strings = str_replace($this->options['ParameterPrefix']
        			            	       . $name . $this->options['ParameterPostfix'],
        			                       $value,
        			                       $strings);
                }
                if ($this->options['ParameterAutoFree']) {
                    $this->params = array();
                }
            }
        }
        return $strings;
    }

    // }}}
    // {{{ replaceEmptyStringsWithKeys()

    /**
     * Replace empty strings with their stringID
     * @param mixed $params
     * @static
     */
    function replaceEmptyStringsWithKeys($strings)
    {
        if (!is_array($strings)) {
            return $strings;
        }
        foreach ($strings as $key => $string) {
            if (empty($string)) {
                $strings[$key] = $key;
            }
        }
        return $strings;
    }

    // }}}
    // {{{ getRaw()

    /**
     * Get translated string (as-is)
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $langID
     * @param string $defaultText Text to display when the string is empty
     * @return string|PEAR_Error
     */
    function getRaw($stringID, $pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null, $defaultText='')
    {
        $pageID = ($pageID == TRANSLATION2_DEFAULT_PAGEID ? $this->currentPageID : $pageID);
        $str = $this->storage->getOne($stringID, $pageID, $langID);
        if (empty($str)) {
            $str = $defaultText;
        }
        return $str;
    }

    // }}}
    // {{{ get()

    /**
     * Get translated string
     *
     * First check if the string is cached, if not => fetch the page
     * from the container and cache it for later use.
     * If the string is empty, check the fallback language; if
     * the latter is empty too, then return the $defaultText.
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $langID
     * @param string $defaultText Text to display when the string is empty
     *               NB: This parameter is only used in the DefaultText decorator
     * @return string
     */
    function get($stringID, $pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null, $defaultText='')
    {
        $str = $this->getRaw($stringID, $pageID, $langID);
        if (PEAR::isError($str)) {
            return $str;
        }
        return $this->_replaceParams($str);
    }

    // }}}
    // {{{ getRawPage()

    /**
     * Get the array of strings in a page
     *
     * Fetch the page (aka "group of strings) from the container,
     * without applying any formatting and without replacing the parameters
     *
     * @param string $pageID
     * @param string $langID
     * @return array
     */
    function getRawPage($pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null)
    {
        $pageID = ($pageID == TRANSLATION2_DEFAULT_PAGEID ? $this->currentPageID : $pageID);
        return $this->storage->getPage($pageID, $langID);
    }

    // }}}
    // {{{ getPage()

    /**
     * Get an entire group of strings
     *
     * Same as getRawPage, but resort to fallback language and
     * replace parameters when needed
     *
     * @param string $pageID
     * @param string $langID
     * @return array
     */
    function getPage($pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null)
    {
        $pageData = $this->getRawPage($pageID, $langID);
        return $this->_replaceParams($pageData);
    }

    // }}}
    // {{{ getStringID()

    /**
     * Get the stringID for the given string. This method is the reverse of get().
     *
     * @param string $string This is NOT the stringID, this is a real string.
     *               The method will search for its matching stringID, and then
     *               it will return the associate string in the selected language.
     * @param string $pageID
     * @return string
     */
    function getStringID($string, $pageID=TRANSLATION2_DEFAULT_PAGEID)
    {
        $pageID = ($pageID == TRANSLATION2_DEFAULT_PAGEID ? $this->currentPageID : $pageID);
        return $this->storage->getStringID($string, $pageID);
    }

    // }}}
    // {{{ __clone()

    /**
     * Clone internal object references
     *
     * This method is called automatically by PHP5
     *
     * @access protected
     */
    function __clone()
    {
        $this->storage = clone($this->storage);
    }

    // }}}
}
?>
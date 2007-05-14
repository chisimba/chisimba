<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Admin base class
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
 * @copyright  2004-2005 Lorenzo Alberton
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * require Translation2 base class
 */
require_once 'Translation2.php';

/**
 * Administration utilities for translation string management
 *
 * Set of methods to easily add/remove languages and strings,
 * with a common API for all the containers.
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2004-2005 Lorenzo Alberton
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link       http://pear.php.net/package/Translation2
 */
class Translation2_Admin extends Translation2
{
    // {{{ class vars

    // }}}
    // {{{ factory()

    /**
     * Return a Translation2_Admin instance already initialized
     *
     * @access public
     * @static
     * @param string $storageDriver Type of the storage driver
     * @param mixed  $options Additional options for the storage driver
     *                        (example: if you are using DB as the storage
     *                        driver, you have to pass the dsn string here)
     * @param array $params Array of parameters for the adapter class
     *                      (i.e. you can set here the mappings between your
     *                      table/field names and the ones used by this class)
     * @return object Translation2 instance or PEAR_Error on failure
     */
    function & factory($driver, $options='', $params=array())
    {
        $tr =& new Translation2_Admin;
        $tr->storage = Translation2_Admin::_storageFactory($driver, $options);
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
     * Override Translation2::_storageFactory()
     *
     * @access private
     * @static
     * @param  string $driver  Type of storage class to return
     * @param  string $options Optional parameters for the storage class
     * @return object Object   Storage object
     */
    function & _storageFactory($driver, $options='')
    {
        if (is_object($driver)) {
            return $driver;
        }
        $storage_path = 'Translation2/Admin/Container/'.strtolower($driver).'.php';
        $storage_class = 'Translation2_Admin_Container_'.strtolower($driver);
        require_once $storage_path;
        $storage =& new $storage_class;
        $err = $storage->init($options);
        if (PEAR::isError($err)) {
            return $err;
        }
        return $storage;
    }

    // }}}
    // {{{ _setDefaultOptions()

    /**
     * Set some default options
     *
     * @access private
     * @return void
     */
    function _setDefaultOptions()
    {
        $this->options['autoCleanCache'] = false;
        $this->options['cacheOptions']   = array('defaultGroup' => 'Translation2');
        parent::_setDefaultOptions();
    }

    // }}}
    // {{{ getAdminDecorator()

    /**
     * Return an instance of an admin decorator
     *
     *
     * @see    Translation2::getDecorator()
     * @access public
     * @param  string $decorator  Name of the decorator
     * @return object Decorator object reference
     */
    function &getAdminDecorator($decorator)
    {
        $decorator_path = 'Translation2/Admin/Decorator/'.$decorator.'.php';
        $decorator_class = 'Translation2_Admin_Decorator_'.$decorator;
        require_once $decorator_path;
        if (func_num_args() > 1) {
            $obj =& func_get_arg(1);
            $ret = new $decorator_class($obj);
        } else {
            $ret = new $decorator_class($this);
        }
        return $ret;
    }

    // }}}
    // {{{ addLang

    /**
     * Prepare the storage container for a new lang.
     * If the langsAvail table doesn't exist yet, it is created.
     *
     * @param array $langData array('lang_id'    => 'en',
     *                              'table_name' => 'i18n',
     *                              'name'       => 'english',
     *                              'meta'       => 'some meta info',
     *                              'error_text' => 'not available');
     * @param array $options array('charset'   => 'utf8',
     *                             'collation' => 'utf8_general_ci');
     * @return mixed true on success, PEAR_Error on failure
     */
    function addLang($langData, $options = array())
    {
        $res = $this->storage->addLang($langData, $options);
        if (PEAR::isError($res)) {
            return $res;
        }
        $res = $this->storage->addLangToList($langData);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->storage->fetchLangs(); //update local cache
        if ($this->options['autoCleanCache']) {
            $this->cleanCache();
        }
        return true;
    }

    // }}}
    // {{{ removeLang

    /**
     * Remove the lang from the langsAvail table and drop the strings table.
     * If the strings table holds other langs and $force==false, then
     * only the lang column is dropped. If $force==true the whole
     * table is dropped without any check
     *
     * @param string  $langID
     * @param boolean $force
     * @return mixed true on success, PEAR_Error on failure
     */
    function removeLang($langID=null, $force=false)
    {
        if (is_null($langID)) {
            //return error
        }
        $res = $this->storage->removeLang($langID, $force);
        if (PEAR::isError($res)) {
            return $res;
        }
        unset($this->storage->langs[$langID]);
        if ($this->options['autoCleanCache']) {
            $this->cleanCache();
        }
        return true;
    }

    // }}}
    // {{{ updateLang

    /**
     * Update the lang info in the langsAvail table
     *
     * @param array  $langData
     * @return mixed true on success, PEAR_Error on failure
     */
    function updateLang($langData)
    {
        $result = $this->storage->updateLang($langData);
        if ($this->options['autoCleanCache']) {
            $this->cleanCache();
        }
        return $result;
    }

    // }}}
    // {{{ add

    /**
     * add a new translation
     *
     * @param string $stringID
     * @param string $pageID
     * @param array  $stringArray Associative array with string translations.
     *               Sample format:  array('en' => 'sample', 'it' => 'esempio')
     * @return mixed true on success, PEAR_Error on failure
     */
    function add($stringID, $pageID=null, $stringArray)
    {
        $result = $this->storage->add($stringID, $pageID, $stringArray);
        if ($this->options['autoCleanCache']) {
            $this->cleanCache();
        }
        return $result;
    }

    // }}}
    // {{{ update

    /**
     * Update an existing translation
     *
     * @param string $stringID
     * @param string $pageID
     * @param array  $stringArray Associative array with string translations.
     *               Sample format:  array('en' => 'sample', 'it' => 'esempio')
     * @return mixed true on success, PEAR_Error on failure
     * @author Ian Eure
     */
    function update($stringID, $pageID = null, $stringArray)
    {
        $result = $this->storage->update($stringID, $pageID, $stringArray);
        if ($this->options['autoCleanCache']) {
            $this->cleanCache();
        }
        return $result;
    }

    // }}}
    // {{{ remove

    /**
     * remove a translated string
     *
     * @param string $stringID
     * @param string $pageID
     * @return mixed true on success, PEAR_Error on failure
     * @todo add a third $langs option, to conditionally remove only the langs specified
     */
    function remove($stringID, $pageID=null)
    {
        $result = $this->storage->remove($stringID, $pageID);
        if ($this->options['autoCleanCache']) {
            $this->cleanCache();
        }
        return $result;
    }

    // }}}
    // {{{ getPageNames()

    /**
     * Get a list of all the pageIDs in any table.
     *
     * @return array
     */
    function getPageNames()
    {
        return $this->storage->getPageNames();
    }

    // }}}
    // {{{ cleanCache()

    /**
     * If you use the CacheLiteFunction decorator, you may want to invalidate
     * the cache after a change in the data base.
     */
    function cleanCache()
    {
        static $cacheLiteFunction = null;
        if (is_null($cacheLiteFunction)) {
            require_once 'Cache/Lite/Function.php';
            $cacheLiteFunction = new Cache_Lite_Function($this->options['cacheOptions']);
        }
        $cacheLiteFunction->clean($this->options['cacheOptions']['defaultGroup']);
    }

    // }}}
}
?>
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Container_dataobjectsimple class
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
 * @author     Alan Knowles <alan@akbkhome.com>
 * @copyright  2004-2005 Alan Knowles
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * require Translation2_Container class and DB_DataObjects
 */
require_once 'Translation2/Container.php';
require_once 'DB/DataObject.php';

/**
 * Simple storage driver for fetching data from a db with DB_DataObject
 *
 * This storage driver can use all databases which are supported
 * by the PEAR::DB abstraction layer to fetch data.
 *
 * Database Structure:
 * <pre>
 *  // meta data etc. not supported yet...
 *
 *  create table translations (
 *     id int(11) auto_increment not null primary key,
 *     string_id int(11),
 *     page varchar(128),
 *     lang varchar(10),
 *     translation text
 *     );
 * alter table translations add index page (page);
 * alter table translations add index lang (lang);
 * alter table translations add index string_id (string_id);
 * </pre>
 *
 * - then just run the dataobjects createtables script.
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Alan Knowles <alan@akbkhome.com>
 * @copyright  2004-2005 Alan Knowles
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */
class Translation2_Container_dataobjectsimple extends Translation2_Container
{
    // {{{ init

    /**
     * Initialize the container
     *
     * @param  string table name
     * @return boolean true
     */
    function init($table = null)
    {
        $this->_setDefaultOptions();
        if (!empty($table)) {
            $this->options['table'] = $table;
        }
        return true;
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
        $this->options['table'] = 'translations';
    }

    // }}}
    // {{{ fetchLangs()

    /**
     * Fetch the available langs if they're not cached yet.
     */
    function fetchLangs()
    {
        $do = DB_DataObject::factory($this->options['table']);
        $do->selectAdd();
        $do->selectAdd('distinct lang');
        $do->find();

        $ret = array();
        while ($do->fetch()) {
            $l = $do->lang;
            $ret[$l] = array(
                'id'         => $l,
                'name'       => $l,
                'meta'       => '',
                'error_text' => '',
            );
        }
        $this->langs =  $ret;
    }

    // }}}
    // {{{ getPage()

    /**
     * Returns an array of the strings in the selected page
     *
     * @param string $pageID
     * @param string $langID
     * @return array
     */
    function &getPage($pageID = null, $langID = null)
    {
        $langID = $this->_getLangID($langID);
        if (PEAR::isError($langID)) {
            return $langID;
        }
        
        // First get the array of string IDs
        $do = DB_DataObject::factory($this->options['table']);
        $do->lang = '-';
        $do->page = $pageID;
        $do->find();
        
        $stringIDs = array();
        while ($do->fetch()) {
            $stringIDs[$do->string_id] = $do->translation;
        }

        // Now get the array of strings
        $do = DB_DataObject::factory($this->options['table']);
        $do->page = $pageID;
        $do->lang = $langID;

        $do->find();
        $translations = array();
        while ($do->fetch()) {
            $translations[$do->string_id] = $do->translation;
        }
        
        // Construct an associative array of stringIDs and translations
        $strings = array();
        foreach ($translations as $key => $value) {
            $strings[$stringIDs[$key]] = $value;
        }

        return $strings;
    }

    // }}}
    // {{{ getOne()

    /**
     * Get a single item from the container, without caching the whole page
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $langID
     * @return string
     */
    function getOne($string, $pageID = null, $langID = null)
    {
        $langID = $langID ? $langID : (isset($this->currentLang['id']) ? $this->currentLang['id'] : '-');
        // get the string id
        $do = DB_DataObject::factory($this->options['table']);
        $do->lang = '-';
        $do->page = $pageID;
        $do->translation = $string;
        // we dont have the base language translation..
        if (!$do->find(true)) {
            return '';
        }
        $stringID = $do->string_id;

        $do = DB_DataObject::factory($this->options['table']);
        $do->lang = $langID;
        $do->page = $pageID;
        $do->string_id = $stringID;
        //print_r($do);
        $do->selectAdd();
        $do->selectAdd('translation');
        if (!$do->find(true)) {
            return '';
        }
        return $do->translation;

    }

    // }}}
    // {{{ getStringID()

    /**
     * Get the stringID for the given string
     *
     * @param string $stringID
     * @param string $pageID
     * @return string
     */
    function getStringID($string, $pageID = null)
    {
        // get the english version...

        $do = DB_DataObject::factory($this->options['table']);
        $do->lang = $this->currentLang['id'];
        $do->page = $pageID;
        $do->translation = $string;
        if ($do->find(true)) {
            return '';
        }
        return $do->string_id;
    }

    // }}}
}
?>
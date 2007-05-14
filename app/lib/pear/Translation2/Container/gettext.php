<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Container_gettext class
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
 * @author     Michael Wallner <mike at php dot net>
 * @copyright  2004-2005 Lorenzo Alberton, Michael Wallner
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * require Translation2_Container class
 */
require_once 'Translation2/Container.php';

/**
 * require I18Nv2 for locale handling
 */
require_once 'I18Nv2.php';

/**
 * Storage driver for fetching data with gettext
 *
 * This storage driver requires the gettext extension
 * and the PEAR::I18Nv2 class for locale handling
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @author     Michael Wallner <mike at php dot net>
 * @copyright  2004-2005 Lorenzo Alberton, Michael Wallner
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 * @see        /docs/gettext_readme.txt for an usage example
 */
class Translation2_Container_gettext extends Translation2_Container
{
    // {{{ class vars

    /**
     * domain bindings
     * @var array
     * @access private
     */
    var $_domains = array();

    /**
     * @var array
     * @access private
     */
    var $cachedDomains = array();

    /**
     * @var boolean
     * @access private
     */
    var $_native = false;

    // }}}
    // {{{ init

    /**
     * Initialize the container 
     *
     * @param  array  gettext parameters
     * @return boolean|PEAR_Error object if domains INI file doesn't exist
     */
    function init($options)
    {
        $this->_setDefaultOptions();
        $this->_parseOptions($options);
        $this->_native = (
            function_exists('gettext') &&
            ($this->options['file_type'] != 'po') &&
            !$this->options['blank_on_missing']
        );
        
        $this->_domains = @parse_ini_file($this->options['domains_path_file']);
        
        if (!$this->_domains) {
            return $this->raiseError(sprintf(
                    'Cannot find domains INI file "%s" [%s on line %d]',
                    $this->options['domains_path_file'], __FILE__, __LINE__
                ),
                TRANSLATION2_ERROR_CANNOT_FIND_FILE
            );
        }

        if ($this->_native) {
            foreach ((array) $this->_domains as $domain => $path) {
                bindtextdomain($domain, $path);
            }
        }
        $this->setLang($this->options['default_lang']);
        
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
        $this->options['langs_avail_file']  = 'langs.ini';
        $this->options['domains_path_file'] = 'domains.ini';
        $this->options['default_domain']    = 'messages';
        $this->options['carriage_return']   = "\n";
        $this->options['file_type']         = 'mo';
        $this->options['default_lang']      = 'en';
        $this->options['default_encoding']  = 'iso-8859-1';
        $this->options['blank_on_missing']  = false;
    }

    // }}}
    // {{{ _switchLang()

    /**
     * @param string new langID
     * @return string previous lang
     * @access private
     */
    function _switchLang($langID)
    {
        $langID  = $this->_getLangID($langID);
        $oldLang = $this->currentLang['id'];
        $this->setLang($langID);
        return $oldLang;
    }

    // }}}
    // {{{ fetchLangs()

    /**
     * Fetch the available langs if they're not cached yet.
     *
     * @return void
     */
    function fetchLangs()
    {
        $this->langs = @parse_ini_file($this->options['langs_avail_file'], true);
        foreach ((array) $this->langs as $id => $lang) {
            $this->langs[$id]['id'] = $id;
        }
    }

    // }}}
    // {{{ setLang()

    /**
     * Sets the current lang
     *
     * @param  string $langID
     * @return array Lang data
     */
    function setLang($langID)
    {
        if (!PEAR::isError($langData = parent::setLang($langID))) {
            I18Nv2::setLocale($langID);
        }
        return $langData;
    }

    // }}}
    // {{{ getPage()

    /**
     * Get all the strings from a domain (parsing the .mo file)
     *
     * @param string $pageID
     * @return array|PEAR_Error
     */
    function getPage($pageID = null, $langID = null)
    {
        $oldLang = $this->_switchLang($langID);
        $curLang = $this->currentLang['id'];
        
        if (empty($pageID) || $pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->options['default_domain'];
        }
        
        if (isset($this->cachedDomains[$curLang][$pageID])) {
            $this->_switchLang($oldLang);
            return $this->cachedDomains[$curLang][$pageID];
        }
        
        if (!isset($this->_domains[$pageID])) {
            $this->_switchLang($oldLang);
            return $this->raiseError(sprintf(
                    'The domain "%s" was not specified in the domains INI '.
                    'file "%s" [%s on line %d]', $pageID,
                    $this->options['domains_path_file'], __FILE__, __LINE__
                ),
                TRANSLATION2_ERROR_DOMAIN_NOT_SET
            );
        }
        
        require_once 'File/Gettext.php';
        $gtFile = &File_Gettext::factory($this->options['file_type']);
        
        $path = $this->_domains[$pageID] .'/'. $curLang .'/LC_MESSAGES/';
        $file = $path . $pageID .'.'. $this->options['file_type'];

        if (PEAR::isError($e = $gtFile->load($file))) {
            if (is_file($file)) {
                $this->_switchLang($oldLang);
                return $this->raiseError(sprintf(
                        '%s [%s on line %d]', $e->getMessage(), __FILE__, __LINE__
                    ),
                    TRANSLATION2_ERROR, PEAR_ERROR_RETURN
                );
            }
            $this->_switchLang($oldLang);
            return $this->raiseError(sprintf(
                    'Cannot find file "%s" [%s on line %d]',
                    $file, __FILE__, __LINE__
                ),
                TRANSLATION2_ERROR_CANNOT_FIND_FILE, PEAR_ERROR_RETURN
            );
        }
        
        $this->cachedDomains[$curLang][$pageID] = $gtFile->strings;
        $this->_switchLang($oldLang);
        return $gtFile->strings;
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
    function getOne($stringID, $pageID = null, $langID = null)
    {
        // native mode
        if ($this->_native) {
            $oldLang = $this->_switchLang($langID);
            $curLang = $this->currentLang['id'];

            if (empty($pageID) || $pageID == TRANSLATION2_DEFAULT_PAGEID) {
                $pageID = $this->options['default_domain'];
            }
            
            $string = dgettext($pageID, $stringID);

            $this->_switchLang($oldLang);
            return $string;
        }
        
        // use File_Gettext
        $page = $this->getPage($pageID, $langID);
        if (PEAR::isError($page = $this->getPage($pageID, $langID))) {
            if ($page->getCode() == TRANSLATION2_ERROR_CANNOT_FIND_FILE) {
                $page = array();
            } else {
                return $this->raiseError($page->getMessage(), $page->getCode());
            }
        }
        
        // return original string if there's no translation available
        if (isset($page[$stringID]) && strlen($page[$stringID])) {
            return $page[$stringID];
        } else if (false == $this->options['blank_on_missing']) {
            return $stringID;
        } else {
            return '';
        }
    }

    // }}}
    // {{{ getStringID()

    /**
     * Get the stringID for the given string
     *
     * @param string $stringID
     * @param string $pageID
     * @return string|PEAR_Error
     */
    function getStringID($string, $pageID = null)
    {
        if (empty($pageID) || $pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->options['default_domain'];
        }
        
        if (!array_key_exists($pageID, $this->_domains)) {
            return $this->raiseError(sprintf(
                    'The domain "%s" was not specified in the domains '.
                    'INI file "%s" [%s on line %d]', $pageID, 
                    $this->options['domains_path_file'], __FILE__, __LINE__
                ),
                TRANSLATION2_ERROR_DOMAIN_NOT_SET
            );
        }

        return array_search($string, $this->getPage($pageID));
    }

    // }}}
}
?>
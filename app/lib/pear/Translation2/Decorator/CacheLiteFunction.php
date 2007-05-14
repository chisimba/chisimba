<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Decorator_CacheLiteFunction class
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
 * Load Translation2 decorator base class
 * and Cache_Lite_Function class
 */
require_once 'Translation2/Decorator.php';
require_once 'Cache/Lite/Function.php';

/**
 * Decorator to cache fetched data using the Cache_Lite_Function class.
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2004-2006 Lorenzo Alberton
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */
class Translation2_Decorator_CacheLiteFunction extends Translation2_Decorator
{
    // {{{ class vars

    /**
     * Cache_Lite_Function object
     * @var object
     */
    var $cacheLiteFunction = null;

    /**
     * @var int (default 1)
     * @access private
     */
    var $tempVarNameGenerator = 1;

    /**
     * @var string
     * @access private
     */
    var $tempVarName = null;

    /**
     * Cache lifetime (in seconds)
     * @var int $lifeTime
     * @access private
     */
    var $lifeTime = 3600;

    /**
     * Directory where to put the cache files
     * (make sure to add a trailing slash)
     * @var string $cacheDir
     * @access private
     */
    var $cacheDir = '/tmp/';

    /**
     * Enable / disable fileLocking. Can avoid cache corruption under bad
     * circumstances.
     * @var string $cacheDir
     * @access private
     */
    var $fileLocking = true;

    /**
     * Enable / disable caching
     * (can be very useful to debug cached scripts)
     * @var boolean $caching
     */
    var $caching = true;

    /**
     * Frequency of cache cleaning.
     * Higher values mean lower cleaning probability.
     * Set 0 to disable. Set 1 to clean at every request.
     * @var boolean $caching
     */
    var $cleaningFrequency = 0;

    /**
     * Name of default cache group.
     * @var	string	$defaultGroup
     */
     var $defaultGroup = 'Translation2';

    // }}}
    // {{{ _prepare()

    /**
     * Istanciate a new Cache_Lite_Function object
     * and get the name for an unused global variable,
     * needed by Cache_Lite_Function
     * @access private
     */
    function _prepare()
    {
        if (is_null($this->cacheLiteFunction)) {
            $cache_options = array(
                'caching'      => $this->caching,
                'cacheDir'     => $this->cacheDir,
                'lifeTime'     => $this->lifeTime,
                'fileLocking'  => $this->fileLocking,
                'defaultGroup' => $this->defaultGroup,

            );
            $this->cacheLiteFunction = new Cache_Lite_Function($cache_options);
        }

        $this->_cleanCache();
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
        // WITHOUT THIS, IT DOESN'T WORK
        global $translation2_storage_cachelitefunction_temp;
        //generate temp variable
        $translation2_storage_cachelitefunction_temp = $this->translation2->storage;

        $res = $this->cacheLiteFunction->call(
            'translation2_storage_cachelitefunction_temp->setLang', $langID);
        if (PEAR::isError($res)) {
            return $res;
        }
        $this->translation2->lang = $res;

    }

    // }}}
    // {{{ setCacheOption()

    /**
     * Set a Cache_Lite option
     *
     * passes a Cache_Lite option forward to the Cache_Lite object
     * @see Cache_Lite constructor for available options
     *
     * @param string $name name of the option
     * @param string $value new value of the option
     * @access public
     */
    function setCacheOption($name, $value)
    {
        $this->_prepare();
        $this->cacheLiteFunction->setOption($name, $value);
    }

    // }}}
    // {{{ getRaw()

    /**
     * Get translated string (as-is)
     *
     * First check if the string is cached, if not => fetch the page
     * from the container and cache it for later use.
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $langID
     * @param string $defaultText Text to display when the strings in both
     *                            the default and the fallback lang are empty
     * @return string
     */
    function getRaw($stringID, $pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null, $defaultText='')
    {
        // WITHOUT THIS, IT DOESN'T WORK
        global $translation2_cachelitefunction_temp;
        //generate temp variable
        $translation2_cachelitefunction_temp = $this->translation2;

        if ($pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->translation2->currentPageID;
        }
        $langID = empty($langID) ? $this->translation2->lang['id'] : $langID;

        $this->_prepare();

        return $this->cacheLiteFunction->call('translation2_cachelitefunction_temp->getRaw',
            $stringID, $pageID, $langID, $defaultText);
    }

    // }}}
    // {{{ get()

    /**
     * Get translated string
     *
     * First check if the string is cached, if not => fetch the page
     * from the container and cache it for later use.
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $langID
     * @param string $defaultText Text to display when the strings in both
     *                            the default and the fallback lang are empty
     * @return string
     */
    function get($stringID, $pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null, $defaultText='')
    {
        // WITHOUT THIS, IT DOESN'T WORK
        global $translation2_cachelitefunction_temp;
        //generate temp variable
        $translation2_cachelitefunction_temp = $this->translation2->storage;

        if ($pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->translation2->currentPageID;
        }
        $langID = empty($langID) ? $this->translation2->lang['id'] : $langID;

        $this->_prepare();

        $string = $this->cacheLiteFunction->call('translation2_cachelitefunction_temp->getOne',
            $stringID, $pageID, $langID);
        if (empty($string)) {
            return $defaultText;
        }
        return $this->translation2->_replaceParams($string);
    }

    // }}}
    // {{{ getRawPage()

    /**
     * Get the array of strings in a page
     *
     * First check if the strings are cached, if not => fetch the page
     * from the container and cache it for later use.
     *
     * @param string $pageID
     * @param string $langID
     * @return array
     */
    function getRawPage($pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null)
    {
        // WITHOUT THIS, IT DOESN'T WORK
        global $translation2_cachelitefunction_temp;
        //generate temp variable
        $translation2_cachelitefunction_temp = $this->translation2;

        if ($pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->translation2->currentPageID;
        }
        $langID = empty($langID) ? $this->translation2->lang['id'] : $langID;

        $this->_prepare();

        return $this->cacheLiteFunction->call('translation2_cachelitefunction_temp->getRawPage',
            $pageID, $langID);
    }

    // }}}
    // {{{ getPage()

    /**
     * Same as getRawPage, but resort to fallback language and
     * replace parameters when needed
     *
     * @param string $pageID
     * @param string $langID
     * @return array
     */
    function getPage($pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null, $defaultText='')
    {
        // WITHOUT THIS, IT DOESN'T WORK
        global $translation2_cachelitefunction_temp;
        //generate temp variable
        $translation2_cachelitefunction_temp = $this->translation2;

        if ($pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->translation2->currentPageID;
        }
        $langID = empty($langID) ? $this->translation2->lang['id'] : $langID;

        $this->_prepare();

        return $this->cacheLiteFunction->call('translation2_cachelitefunction_temp->getPage',
            $pageID, $langID);
    }

    // }}}
    // {{{ translate()

    /**
     * Get translated string
     *
     * @param string $string This is NOT the stringID, this is a real string.
     *               The method will search for its matching stringID, and then
     *               it will return the associate string in the selected language.
     * @param string $langID
     * @return string
     */
    function getStringID($string, $pageID=TRANSLATION2_DEFAULT_PAGEID)
    {
        // WITHOUT THIS, IT DOESN'T WORK
        global $translation2_cachelitefunction_temp;
        //generate temp variable
        $translation2_cachelitefunction_temp = $this->translation2;

        if ($pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->translation2->currentPageID;
        }
        $this->_prepare();

        return $this->cacheLiteFunction->call('translation2_cachelitefunction_temp->getStringID',
            $string, $pageID);
    }

    // }}}
    // {{{ _cleanCache()

    /**
     * Statistically purge the cache
     */
    function _cleanCache()
    {
        if ($this->cleaningFrequency > 0) {
            if (mt_rand(1, $this->cleaningFrequency) == 1) {
            	$this->cacheLiteFunction->clean($this->defaultGroup);
            }
        }
    }

    // }}}
}
?>
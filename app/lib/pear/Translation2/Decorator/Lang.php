<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Decorator_Lang class
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
 * @category  Internationalization
 * @package   Translation2
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2004-2007 Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Translation2
 */

/**
 * Load Translation2 decorator base class
 */
require_once 'Translation2/Decorator.php';

/**
 * Decorator to provide a fallback language for empty strings.
 *
 * @category  Internationalization
 * @package   Translation2
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2004-2007 Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Translation2
 */
class Translation2_Decorator_Lang extends Translation2_Decorator
{
    // {{{ class vars

    /**
     * fallback lang
     * @var string
     * @access protected
     */
    var $fallbackLang;

    // }}}
    // {{{ setOption()

    /**
     * set Decorator option (intercept 'fallbackLang' option).
     * I don't know why it's needed, but it doesn't work without.
     *
     * @param string $option option name
     * @param mixed  $value  option value
     *
     * @return self
     */
    function setOption($option, $value=null)
    {
        if ($option == 'fallbackLang') {
            $this->fallbackLang = $value;
            return $this;
        }
        return parent::setOption($option, $value);
    }

    // }}}
    // {{{ get()

    /**
     * Get translated string
     *
     * If the string is empty, check the fallback language
     *
     * @param string $stringID    string ID
     * @param string $pageID      page/group ID
     * @param string $langID      language ID
     * @param string $defaultText Text to display when the strings in both
     *                            the default and the fallback lang are empty
     *
     * @return string
     */
    function get($stringID, $pageID = TRANSLATION2_DEFAULT_PAGEID, $langID = null, $defaultText = '')
    {
        $str = $this->translation2->get($stringID, $pageID, $langID, $defaultText);
        if (empty($str)) {
            $str = $this->translation2->get($stringID, $pageID, $this->fallbackLang);
        }
        return $str;
    }

    // }}}
    // {{{ getPage()

    /**
     * Same as getRawPage, but resort to fallback language and
     * replace parameters when needed
     *
     * @param string $pageID page/group ID
     * @param string $langID language ID
     *
     * @return array
     */
    function getPage($pageID = TRANSLATION2_DEFAULT_PAGEID, $langID = null)
    {
        $data1 = $this->translation2->getPage($pageID, $langID);
        if (PEAR::isError($data1)) {
            return $data1;
        }
        $data2 = $this->translation2->getPage($pageID, $this->fallbackLang);
        if (PEAR::isError($data2)) {
            return $data2;
        }
        foreach ($data1 as $key => $val) {
            if (empty($val)) {
                $data1[$key] = $data2[$key];
            }
        }
        // append keys when fallback lang contains more than current
        $diff = array_diff(array_keys($data2), array_keys($data1));
        foreach ($diff as $key) {
            $data1[$key] = $data2[$key];
        }
        return $data1;
    }

    // }}}
}
?>
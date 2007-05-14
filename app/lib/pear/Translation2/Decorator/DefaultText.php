<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Decorator_DefaultText class
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
 * @author     Rolf 'Red' Ochsenbein <red at raven dot ch>
 * @copyright  2004-2006 Lorenzo Alberton, Rolf 'Red' Ochsenbein
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * Load Translation2 decorator base class
 */
require_once 'Translation2/Decorator.php';

/**
 * Decorator to provide a fallback text for empty strings.
 *
 * If the string is empty, return the <parameter>defaultText</parameter> parameter.
 * If the <parameter>defaultText</parameter> parameter is empty too, then return
 * &quot;$emptyPostfix.$outputString.$emptyPrefix&quot;, the three variables
 * being class properties you can set to a custom string.
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @author     Rolf 'Red' Ochsenbein <red at raven dot ch>
 * @copyright  2004-2006 Lorenzo Alberton, Rolf 'Red' Ochsenbein
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */
class Translation2_Decorator_DefaultText extends Translation2_Decorator
{
    // {{{ class vars

    /**
     * String appended to the returned string when the string is empty
     * and it's replaced by its $stringID. It can be used to mark unreplaced
     * strings.
     * @var string
     * @access protected
     */
    var $emptyPostfix = '';

    /**
     * String prepended to the returned string when the string is empty
     * and it's replaced by its $stringID. It can be used to mark unreplaced
     * strings.
     * @var string
     * @access protected
     */
    var $emptyPrefix = '';

    /**
     * String to output when there was no translation
     * %stringID% will be replaced with the stringID
     * %stringID_url% will replaced with a urlencoded stringID
     * %url% will be replaced with the targeted url
     * @var string
     * @access protected
     */
    //var $outputString = '%stringID%<a href="%url%">(T)</a>';
    var $outputString = '%stringID%';

    /**
     * Targeted URL of strings without translations
     * @var string
     * @access protected
     */
    var $url = '#';

    // }}}
    // {{{ get()

    /**
     * Get translated string
     *
     * If the string is empty, return the $defaultText if not empty,
     * the $stringID otherwise.
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $langID
     * @param string $defaultText Text to display when the string is empty
     * @return string
     */
    function get($stringID, $pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null, $defaultText='')
    {
        if ($pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->translation2->currentPageID;
        }
        $str = $this->translation2->get($stringID, $pageID, $langID);

        if (!empty($str)) {
            return $str;
        }
        if (!empty($defaultText)) {
            return $this->_replaceParams($defaultText);
        }

        $search  = array(
            '%stringID%',
            '%stringID_url%',
            '%pageID_url%',
            '%url%'
        );
        $replace = array(
            $stringID,
            urlencode($stringID),
            urlencode($pageID),
            $this->url
        );
        return $this->_replaceParams(
            $this->emptyPrefix
            .str_replace($search, $replace, $this->outputString)
            .$this->emptyPostfix
        );
        //$str = (empty($defaultText) ? $this->emptyPrefix.$stringID.$this->emptyPostfix : $defaultText);
    }

    // }}}
    // {{{ getPage()

    /**
     * Replace empty strings with their $stringID
     *
     * @param string $pageID
     * @param string $langID
     * @return array
     */
    function getPage($pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null)
    {
        $data = $this->translation2->getPage($pageID, $langID);
        return $this->replaceEmptyStringsWithKeys($data);
    }

    // }}}
    // {{{ getStringID

    /**
     * Get the stringID for the given string. This method is the reverse of get().
     * If the requested string is unknown to the system,
     * the requested string will be returned.
     *
     * @param string $string This is NOT the stringID, this is a real string.
     *               The method will search for its matching stringID, and then
     *               it will return the associate string in the selected language.
     * @param string $pageID
     * @return string
     */
    function &getStringID($string, $pageID=TRANSLATION2_DEFAULT_PAGEID)
    {
        if ($pageID == TRANSLATION2_DEFAULT_PAGEID) {
            $pageID = $this->translation2->currentPageID;
        }
        $stringID = $this->storage->getStringID($string, $pageID);
        if (empty($stringID)) {
            $stringID = $string;
        }
        return $stringID;
    }
}
?>
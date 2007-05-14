<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Decorator_Iconv class
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
 * @author     Sergey Korotkov <sergey@pushok.com>
 * @copyright  2004-2005 Lorenzo Alberton, Sergey Korotkov
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * Load Translation2 decorator base class
 */
require_once 'Translation2/Decorator.php';

/**
 * Translation2 Iconv Decorator
 *
 * Decorator to change the encoding of the stored translation to the
 * one given in the 'encoding' option.
 *
 * <code>
 * $tr->setOptions(array('encoding' => 'UTF-8'));
 * </code>
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @author     Sergey Korotkov <sergey@pushok.com>
 * @copyright  2004-2005 Lorenzo Alberton, Sergey Korotkov
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 * @see http://www.php.net/htmlentities for a list of available encodings.
 */
class Translation2_Decorator_Iconv extends Translation2_Decorator
{
    // {{{ class vars

    /**
     * @var string
     * @access private
     */
    var $encoding = 'ISO-8859-1';
    
    /**
     * @var array
     * @access private
     */
    var $lang_encodings;

    // }}}
    // {{{ _getEncoding()

    /**
     * Get the encoding for the given langID
     *
     * @param string $langID
     * @return string encoding
     * @access private
     */
    function _getEncoding($langID = null)
    {
        if (!is_array($this->lang_encodings)) {
            $this->lang_encodings = array();
            foreach ($this->translation2->getLangs('encodings') as $langID => $encoding) {
                $this->lang_encodings[$langID] = $encoding;
            }
        }
        if (!is_null($langID) && isset($this->lang_encodings[$langID])) {
            return $this->lang_encodings[$langID];
        }
        return $this->lang['encoding'];
    }

    // }}}
    // {{{ get()
    
    /**
     * Get the translated string, in the new encoding
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $langID
     * @param string $defaultText Text to display when the string is empty
     * @return string
     */
    function get($stringID, $pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null, $defaultText=null)
    {
        $str = $this->translation2->get($stringID, $pageID, $langID, $defaultText);
        
        if (PEAR::isError($str) || empty($str)) {
            return $str;
        }

        return iconv($this->_getEncoding($langID), $this->encoding, $str);
    }

    // }}}
    // {{{ getPage()

    /**
     * Same as getRawPage, but apply transformations when needed
     *
     * @param string $pageID
     * @param string $langID
     * @return array
     */
    function getPage($pageID=TRANSLATION2_DEFAULT_PAGEID, $langID=null)
    {
        $data = $this->translation2->getPage($pageID, $langID);
        
        $input_encoding = $this->_getEncoding($langID);
        
        foreach (array_keys($data) as $k) {
            if (!empty($data[$k])) {
                $data[$k] = iconv($input_encoding, $this->encoding, $data[$k]);
            }
        }
        return $data;
    }

    // }}}
}
?>
<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Admin_Decorator_Autoadd class
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
 * @author     Ian Eure <ieure at php dot net>
 * @copyright  2004-2005 Lorenzo Alberton, Ian Eure
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * Load Translation2_Decorator class
 */
require_once 'Translation2/Admin/Decorator.php';

/**
 * Automatically add requested strings
 *
 * This Decorator will add strings to a language when a request for them to be
 * translated happens. The 'autoaddlang' option must be set to the language the
 * strings will be added as.
 *
 * Example:
 * <pre>
 * $tr =& Translation2_Admin::factory(...);
 * $tr->setLang('en');
 * $tr =& $tr->getAdminDecorator('Autoadd');
 * $tr->setOption('autoaddlang', 'en');
 * ...
 * $tr->get('Entirely new string', 'samplePage', 'de');
 * </pre>
 *
 * 'Entirely new string' will be added to the English language table.
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Ian Eure <ieure at php dot net>
 * @copyright  2004-2005 Ian Eure
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link       http://pear.php.net/package/Translation2
 * @since      2.0.0beta3
 */
class Translation2_Admin_Decorator_Autoadd extends Translation2_Admin_Decorator
{
    /**
     * Language to add strings in
     *
     * @var  string
     */
    var $autoaddlang = '';

    /**
     * Get a translated string
     *
     * @see   Translation2::get()
     */
    function get($stringID, $pageID = TRANSLATION2_DEFAULT_PAGEID, $langID = null)
    {
        $pageID = ($pageID == TRANSLATION2_DEFAULT_PAGEID ? $this->translation2->currentPageID : $pageID);
        $string = $this->translation2->get($stringID, $pageID, $langID);
        if (PEAR::isError($string)
            || !strlen($string)
            && !empty($this->autoaddlang)
            && $langID == $this->autoaddlang) {
            // Make sure we add a stub for all languages we know about.
            $langs = array();
            foreach ($this->translation2->getLangs('ids') as $lang) {
                $langs[$lang] = '';
            }
            $langs[$this->autoaddlang] = $stringID;

            // Add the string
            $this->translation2->add($stringID, $pageID, $langs);
        }
        return $string;
    }
}
?>
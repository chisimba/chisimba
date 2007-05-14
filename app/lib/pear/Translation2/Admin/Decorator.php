<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Admin_Decorator class
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
 * Load Translation2_Decorator class
 */
require_once 'Translation2/Decorator.php';
 
/**
 * Decorates a Translation2_Admin class.
 *
 * Create a subclass of this class for your own "decoration".
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2004-2005 Lorenzo Alberton
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link       http://pear.php.net/package/Translation2
 * @abstract
 * @todo       Don't allow stacking on top of regular Decorators, 
 *             since that will break things.
 */
class Translation2_Admin_Decorator extends Translation2_Decorator
{
    // {{{ addLang()

    /**
     * Create a new language
     *
     * @see  Translation2_Admin::addLang()
     */
    function addLang($langData)
    {
        return $this->translation2->addLang($langData);
    }
    
    // }}}
    // {{{ removeLang()

    /**
     * Remove a language
     *
     * @see  Translation2_Admin::removeLang()
     */
    function removeLang($langID = null, $force = false)
    {
        return $this->translation2->removeLang($langID, $force);
    }

    // }}}
    // {{{ updateLang()

    /**
     * Update the lang info in the langsAvail table
     *
     * @see  Translation2_Admin::updateLang()
     */
    function updateLang($langData)
    {
        return $this->translation2->updateLang($langData);
    }

    // }}}
    // {{{ add()

    /**
     * Add a translation
     *
     * @see  Translation2_Admin::add()
     */
    function add($stringID, $pageID = null, $stringArray)
    {
        return $this->translation2->add($stringID, $pageID, $stringArray);
    }

    // }}}
    // {{{ update()

    /**
     * Update a translation
     *
     * @see  Translation2_Admin::update()
     */
    function update($stringID, $pageID = null, $stringArray)
    {
        return $this->translation2->update($stringID, $pageID, $stringArray);
    }

    // }}}
    // {{{ remove()

    /**
     * Remove a translation
     *
     * @see  Translation2_Admin::remove()
     */
    function remove($stringID, $pageID = null)
    {
        return $this->translation2->remove($stringID, $pageID);
    }

    // }}}
    // {{{ getPageNames()

    /**
     * Get a list of all the pageIDs in any table.
     *
     * @see  Translation2_Admin::getPageNames()
     */
    function getPageNames()
    {
        return $this->translation2->getPageNames();
    }

    // }}}
    // {{{ cleanCache()

    /**
     * Clean the cache
     *
     * @see  Translation2_Admin::cleanCache()
     */
    function cleanCache()
    {
        return $this->translation2->cleanCache();
    }

    // }}}
}
?>
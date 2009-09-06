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
 * @category  Internationalization
 * @package   Translation2
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2004-2007 Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Translation2
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
 * @category  Internationalization
 * @package   Translation2
 * @author    Lorenzo Alberton <l.alberton@quipo.it>
 * @copyright 2004-2007 Lorenzo Alberton
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link      http://pear.php.net/package/Translation2
 * @abstract
 * @todo      Don't allow stacking on top of regular Decorators,
 *            since that will break things.
 */
class Translation2_Admin_Decorator extends Translation2_Decorator
{
    // {{{ addLang()

    /**
     * Prepare the storage container for a new lang.
     * If the langsAvail table doesn't exist yet, it is created.
     *
     * @param array $langData array('lang_id'    => 'en',
     *                              'table_name' => 'i18n',
     *                              'name'       => 'english',
     *                              'meta'       => 'some meta info',
     *                              'error_text' => 'not available');
     * @param array $options  array('charset'   => 'utf8',
     *                              'collation' => 'utf8_general_ci');
     *
     * @return mixed true on success, PEAR_Error on failure
     * @see Translation2_Admin::addLang()
     */
    function addLang($langData, $options = array())
    {
        return $this->translation2->addLang($langData, $options);
    }
    
    // }}}
    // {{{ removeLang()

    /**
     * Remove the lang from the langsAvail table and drop the strings table.
     * If the strings table holds other langs and $force==false, then
     * only the lang column is dropped. If $force==true the whole
     * table is dropped without any check
     *
     * @param string  $langID language ID
     * @param boolean $force  remove the language info without further checks
     *
     * @return mixed true on success, PEAR_Error on failure
     * @see Translation2_Admin::removeLang()
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
     * @param array $langData array containing language info
     *
     * @return mixed true on success, PEAR_Error on failure
     * @see Translation2_Admin::updateLang()
     */
    function updateLang($langData)
    {
        return $this->translation2->updateLang($langData);
    }

    // }}}
    // {{{ add()

    /**
     * Add a new translation
     *
     * @param string $stringID    string ID
     * @param string $pageID      page/group ID
     * @param array  $stringArray Associative array with string translations.
     *               Sample format:  array('en' => 'sample', 'it' => 'esempio')
     *
     * @return mixed true on success, PEAR_Error on failure
     * @see Translation2_Admin::add()
     */
    function add($stringID, $pageID, $stringArray)
    {
        return $this->translation2->add($stringID, $pageID, $stringArray);
    }

    // }}}
    // {{{ update()

    /**
     * Update an existing translation
     *
     * @param string $stringID    string ID
     * @param string $pageID      page/group ID
     * @param array  $stringArray Associative array with string translations.
     *               Sample format:  array('en' => 'sample', 'it' => 'esempio')
     *
     * @return mixed true on success, PEAR_Error on failure
     * @see Translation2_Admin::update()
     */
    function update($stringID, $pageID, $stringArray)
    {
        return $this->translation2->update($stringID, $pageID, $stringArray);
    }

    // }}}
    // {{{ remove()

    /**
     * Remove a translated string
     *
     * @param string $stringID string ID
     * @param string $pageID   page/group ID
     *
     * @return mixed true on success, PEAR_Error on failure
     * @see Translation2_Admin::remove()
     */
    function remove($stringID, $pageID = null)
    {
        return $this->translation2->remove($stringID, $pageID);
    }

    // }}}
    // {{{ removePage

    /**
     * Remove all the strings in the given page/group
     *
     * @param string $pageID page/group ID
     *
     * @return mixed true on success, PEAR_Error on failure
     * @see Translation2_Admin::removePage()
     */
    function removePage($pageID = null)
    {
        return $this->translation2->removePager($pageID);
    }

    // }}}
    // {{{ getPageNames()

    /**
     * Get a list of all the pageIDs in any table.
     *
     * @return array
     * @see Translation2_Admin::getPageNames()
     */
    function getPageNames()
    {
        return $this->translation2->getPageNames();
    }

    // }}}
    // {{{ cleanCache()

    /**
     * If you use the CacheLiteFunction decorator, you may want to invalidate
     * the cache after a change in the data base.
     *
     * @return void
     * @see Translation2_Admin::cleanCache()
     */
    function cleanCache()
    {
        return $this->translation2->cleanCache();
    }

    // }}}
}
?>
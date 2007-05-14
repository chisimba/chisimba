<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Admin_Container_xml class
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
 * @author     Olivier Guilyardi <olivier at samalyse dot com>
 * @copyright  2004-2005 Lorenzo Alberton, Olivier Guilyardi
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Translation2
 */

/**
 * require Translation2_Container_xml class
 */
require_once 'Translation2/Container/xml.php';

require_once 'XML/Util.php';

/**
 * Storage driver for storing/fetching data to/from a XML file
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @author     Olivier Guilyardi <olivier at samalyse dot com>
 * @copyright  2004-2005 Lorenzo Alberton, Olivier Guilyardi
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link       http://pear.php.net/package/Translation2
 */
class Translation2_Admin_Container_xml extends Translation2_Container_xml
{
    // {{{ class vars
    
    /**
     * Whether _saveData() is already registered at shutdown or not
     * @var boolean
     */
    var $_isScheduledSaving = false;

    // }}}
    // {{{ addLang()

    /**
     * Does nothing (here for compatibility with the container interface)
     *
     * @param array $langData
     * @param array $options
     * @return true|PEAR_Error
     */
    function addLang($langData, $options = array())
    {
        return true;
    }

    // }}}
    // {{{ addLangToList()

    /**
     * Creates a new entry in the <languages> section
     *
     * @param array $langData array('lang_id'    => 'en',
     *                              'name'       => 'english',
     *                              'meta'       => 'some meta info',
     *                              'error_text' => 'not available',
     *                              'encoding'   => 'iso-8859-1',
     *              );
     * @return true|PEAR_Error
     */
    function addLangToList($langData)
    {
        $validInput = array(
            'name'       => '',
            'meta'       => '',
            'error_text' => '',
            'encoding'   => 'iso-8859-1',
        );
        
        foreach ($validInput as $key => $val) {
            if (isset($langData[$key])) $validInput[$key] = $langData[$key];
        }
        
        $this->_data['languages'][$langData['lang_id']] = $validInput;
        return $this->_scheduleSaving();
    }

    // }}}
    // {{{ updateLang()

    /**
     * Update the lang info in the langsAvail table
     *
     * @param array  $langData
     * @return true|PEAR_Error
     */
    function updateLang($langData)
    {
        $allFields = array( //'lang_id',
            'name', 'meta', 'error_text', 'encoding',
        );
        foreach ($allFields as $field) {
            if (isset($this->_data['languages'][$langData['lang_id']][$field])) {
                $this->_data['languages'][$langData['lang_id']][$field] = $langData[$field];
            }
        }
        $success = $this->_scheduleSaving();
        $this->fetchLangs();  //update memory cache
        return $success;
    }

    // }}}
    // {{{ add()

    /**
     * Add a new entry in the strings table.
     *
     * @param string $stringID
     * @param string $pageID
     * @param array  $stringArray Associative array with string translations.
     *               Sample format:  array('en' => 'sample', 'it' => 'esempio')
     * @return true|PEAR_Error
     */
    function add($stringID, $pageID, $stringArray)
    {
        $langs = array_intersect(
            array_keys($stringArray),
            $this->getLangs('ids')
        );

        $pageID = is_null($pageID) ? '#NULL'  : $pageID;
        $pageID = empty($pageID)   ? '#EMPTY' : $pageID;

        if (!array_key_exists($pageID, $this->_data['pages'])) {
            $this->_data['pages'][$pageID] = array();
        }
        if (!array_key_exists($stringID, $this->_data['pages'][$pageID])) {
            $this->_data['pages'][$pageID][$stringID] = array();
        }
        foreach ($langs as $lang) {
            $this->_data['pages'][$pageID][$stringID][$lang] = $stringArray[$lang];
        }
        
        return $this->_scheduleSaving();
    }

    // }}}
    // {{{ update()

    /**
     * Update an existing entry in the strings table.
     *
     * @param string $stringID
     * @param string $pageID
     * @param array  $stringArray Associative array with string translations.
     *               Sample format:  array('en' => 'sample', 'it' => 'esempio')
     * @return true|PEAR_Error
     */
    function update($stringID, $pageID, $stringArray)
    {
        return $this->add($stringID, $pageID, $stringArray);
    }

    // }}}
    // {{{ remove()

    /**
     * Remove an entry from the strings table.
     *
     * @param string $stringID
     * @param string $pageID
     * @return true|PEAR_Error
     */
    function remove($stringID, $pageID)
    {
        $pageID = is_null($pageID) ? '#NULL' : $pageID;
        $pageID = empty($pageID) ? '#EMPTY' : $pageID;

        unset ($this->_data['pages'][$pageID][$stringID]);
        if (!count($this->_data['pages'][$pageID])) {
            unset ($this->_data['pages'][$pageID]);
        }

        return $this->_scheduleSaving();
    }

    // }}}
    // {{{ removeLang()

    /**
     * Remove all the entries for the given lang from the strings table.
     *
     * @param string  $langID
     * @param boolean $force (ignored)
     * @return true|PEAR_Error
     */
    function removeLang($langID, $force = true)
    {
        // remove lang metadata 
        unset($this->_data['languages'][$langID]);

        // remove the entries
        foreach (array_keys($this->_data['pages']) as $pageID) {
            foreach (array_keys($this->_data['pages'][$pageID]) as $stringID) {
                if (array_key_exists($langID, $this->_data['pages'][$pageID][$stringID])) {
                    unset($this->_data['pages'][$pageID][$stringID]);
                }
            }
        }
        return $this->_scheduleSaving();
    }

    // }}}
    // {{{ getPageNames()

    /**
     * Get a list of all the pageIDs.
     *
     * @return array
     */
    function getPageNames()
    {
        $pages = array_keys($this->_data['pages']);
        $k = array_search('#NULL', $pages);
        if ($k !== false && !is_null($k)) {
            $pages[$k] = null;
        }
        $k = array_search('#EMPTY', $pages);
        if ($k !== false && !is_null($k)) {
            $pages[$k] = '';
        }
        return $pages;
    }

    // }}}
    // {{{ _scheduleSaving()
    
    /**
     * Prepare data saving
     *
     * This methods registers _saveData() as a PEAR shutdown function. This
     * is to avoid saving multiple times if the programmer makes several 
     * changes.
     * 
     * @return true|PEAR_Error
     * @access private
     * @see Translation2_Admin_Container_xml::_saveData()
     */
    function _scheduleSaving()
    {
        if ($this->options['save_on_shutdown']) {
            if (!$this->_isScheduledSaving) {
                // save the changes on shutdown
                register_shutdown_function(array(&$this, '_saveData'));
                $this->_isScheduledSaving = true;
            }
            return true;
        }
        
        // save the changes now
        return $this->_saveData();
    }

    // }}}
    // {{{ _saveData()
    
    /**
     * Serialize and save the updated tranlation data to the XML file
     *
     * @return boolean | PEAR_Error
     * @access private
     * @see Translation2_Admin_Container_xml::_scheduleSaving()
     */
    function _saveData()
    {
        if ($this->options['save_on_shutdown']) {
            $data =& $this->_data;
        } else {
            $data =  $this->_data;
        }
        
        $this->_convertEncodings('to_xml', $data);
        $this->_convertLangEncodings('to_xml', $data);
        
        // Serializing
        
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n" .
               "<!DOCTYPE translation2 [\n" . TRANSLATION2_DTD . "]>\n\n" .
               "<translation2>\n" .
               "  <languages>\n";

        foreach ($data['languages'] as $lang => $spec) {
            extract ($spec);
            $xml .= "    <lang id=\"$lang\">\n" .
                    "      <name>" . 
                    ($name ? ' ' . XML_Util::replaceEntities($name) . ' ' : '') . 
                    "</name>\n" .
                    "      <meta>" . 
                    ($meta ? ' ' . XML_Util::replaceEntities($meta) . ' ' : "") . 
                    "</meta>\n" .
                    "      <error_text>" . 
                    ($error_text 
                        ? ' ' . XML_Util::replaceEntities($error_text) . ' ' 
                        : "") . 
                    "</error_text>\n" .
                    "      <encoding>" . ($encoding ? " $encoding " : "") . 
                    "</encoding>\n" .  
                    "    </lang>\n";
        }

        $xml .= "  </languages>\n" .
                "  <pages>\n";

        foreach ($data['pages'] as $page => $strings) {
            $xml .= "    <page key=\"" . XML_Util::replaceEntities($page) . 
                    "\">\n";
            foreach ($strings as $str_id => $translations) {
                $xml .= "      <string key=\"" . 
                        XML_Util::replaceEntities($str_id) . "\">\n";
                foreach ($translations as $lang => $str) {
                    $xml .= "        <tr lang=\"$lang\"> " .
                            XML_Util::replaceEntities($str) . " </tr>\n";
                }
                $xml .= "      </string>\n";
            }
            $xml .= "    </page>\n";
        }

        $xml .= "  </pages>\n" .
                "</translation2>\n";

        unset ($data);
        
        // Saving

        if (!$f = fopen ($this->_filename, 'w')) {
            return $this->raiseError(sprintf(
                    'Unable to open the XML file ("%s") for writing',
                    $this->_filename
                ),
                TRANSLATION2_ERROR_CANNOT_WRITE_FILE,
                PEAR_ERROR_TRIGGER,
                E_USER_ERROR
            );
        }
        @flock($f, LOCK_EX);
        fwrite ($f, $xml);
        //@flock($f, LOCK_UN);
        fclose ($f);
        return true;
    }

    // }}}
}
?>
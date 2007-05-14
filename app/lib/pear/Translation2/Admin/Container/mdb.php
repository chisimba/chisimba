<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Contains the Translation2_Admin_Container_mdb class
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
 * require Translation2_Container_mdb class
 */
require_once 'Translation2/Container/mdb.php';

/**
 * Storage driver for storing/fetching data to/from a database
 *
 * This storage driver can use all databases which are supported
 * by the PEAR::MDB abstraction layer to store and fetch data.
 *
 * @category   Internationalization
 * @package    Translation2
 * @author     Lorenzo Alberton <l dot alberton at quipo dot it>
 * @copyright  2004-2005 Lorenzo Alberton
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @link       http://pear.php.net/package/Translation2
 */
class Translation2_Admin_Container_mdb extends Translation2_Container_mdb
{
    // {{{ addLang()

    /**
     * Creates a new table to store the strings in this language.
     * If the table is shared with other langs, it is ALTERed to
     * hold strings in this lang too.
     *
     * @param array $langData
     * @param array $options
     * @return true|PEAR_Error
     */
    function addLang($langData, $options = array())
    {
        $tables = $this->db->listTables();
        if (PEAR::isError($tables)) {
            return $tables;
        }

        $lang_col = $this->_getLangCol($langData['lang_id']);

        if (in_array($langData['table_name'], $tables)) {
            //table exists
            $query = sprintf('ALTER TABLE %s ADD %s%s TEXT',
                $this->db->quoteIdentifier($langData['table_name']),
                $this->db->phptype == 'mssql' ? '' : 'COLUMN ',
                $this->db->quoteIdentifier($lang_col)
            );
            ++$this->_queries;
            return $this->db->query($query);
        }

        //table does not exist
        $queries = array();
        $queries[] = sprintf('CREATE TABLE %s ( '
                             .'%s VARCHAR(50) default NULL, '
                             .'%s TEXT NOT NULL, '
                             .'%s TEXT )',
            $this->db->quoteIdentifier($langData['table_name']),
            $this->db->quoteIdentifier($this->options['string_page_id_col']),
            $this->db->quoteIdentifier($this->options['string_id_col']),
            $this->db->quoteIdentifier($lang_col)
        );
        $mysqlClause = ($this->db->phptype == 'mysql') ? '(255)' : '';

        $index_name = sprintf('%s_%s_%s_index',
            $langData['table_name'],
            $this->options['string_page_id_col'],
            $this->options['string_id_col']
        );
        $queries[] = sprintf('CREATE UNIQUE INDEX %s ON %s (%s, %s%s)',
             $this->db->quoteIdentifier($index_name),
             $this->db->quoteIdentifier($langData['table_name']),
             $this->db->quoteIdentifier($this->options['string_page_id_col']),
             $this->db->quoteIdentifier($this->options['string_id_col']),
             $mysqlClause
        );

        $index_name = sprintf('%s_%s_index',
            $langData['table_name'],
            $this->options['string_page_id_col']
        );
        $queries[] = sprintf('CREATE INDEX %s ON %s (%s)',
             $this->db->quoteIdentifier($index_name),
             $this->db->quoteIdentifier($langData['table_name']),
             $this->db->quoteIdentifier($this->options['string_page_id_col'])
        );

        $index_name = sprintf('%s_%s_index',
            $langData['table_name'],
            $this->options['string_id_col']
        );
        $queries[] = sprintf('CREATE INDEX %s ON %s (%s%s)',
             $this->db->quoteIdentifier($index_name),
             $this->db->quoteIdentifier($langData['table_name']),
             $this->db->quoteIdentifier($this->options['string_id_col']),
             $mysqlClause
        );

        foreach($queries as $query) {
            ++$this->_queries;
            $res = $this->db->query($query);
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        return true;
    }

    // }}}
    // {{{ addLangToList()

    /**
     * Creates a new entry in the langsAvail table.
     * If the table doesn't exist yet, it is created.
     *
     * @param array $langData array('lang_id'    => 'en',
     *                              'table_name' => 'i18n',
     *                              'name'       => 'english',
     *                              'meta'       => 'some meta info',
     *                              'error_text' => 'not available',
     *                              'encoding'   => 'iso-8859-1');
     * @return true|PEAR_Error
     */
    function addLangToList($langData)
    {
        $tables = $this->db->listTables();
        if (PEAR::isError($tables)) {
            return $tables;
        }

        if (!in_array($this->options['langs_avail_table'], $tables)) {
            $queries = array();
            $queries[] = sprintf('CREATE TABLE %s ('
                                .'%s VARCHAR(16), '
                                .'%s VARCHAR(200), '
                                .'%s TEXT, '
                                .'%s VARCHAR(250), '
                                .'%s VARCHAR(16) )',
                $this->db->quoteIdentifier($this->options['langs_avail_table']),
                $this->db->quoteIdentifier($this->options['lang_id_col']),
                $this->db->quoteIdentifier($this->options['lang_name_col']),
                $this->db->quoteIdentifier($this->options['lang_meta_col']),
                $this->db->quoteIdentifier($this->options['lang_errmsg_col']),
                $this->db->quoteIdentifier($this->options['lang_encoding_col'])
            );
            $queries[] = sprintf('CREATE UNIQUE INDEX %s_%s_index ON %s (%s)',
                $this->options['langs_avail_table'],
                $this->options['lang_id_col'],
                $this->db->quoteIdentifier($this->options['langs_avail_table']),
                $this->db->quoteIdentifier($this->options['lang_id_col'])
            );

            foreach ($queries as $query) {
                ++$this->_queries;
                $res = $this->db->query($query);
                if (PEAR::isError($res)) {
                    return $res;
                }
            }
        }

        $query = sprintf('INSERT INTO %s (%s, %s, %s, %s, %s) VALUES (%s, %s, %s, %s, %s)',
            $this->db->quoteIdentifier($this->options['langs_avail_table']),
            $this->db->quoteIdentifier($this->options['lang_id_col']),
            $this->db->quoteIdentifier($this->options['lang_name_col']),
            $this->db->quoteIdentifier($this->options['lang_meta_col']),
            $this->db->quoteIdentifier($this->options['lang_errmsg_col']),
            $this->db->quoteIdentifier($this->options['lang_encoding_col']),
            $this->db->getTextValue($langData['lang_id']),
            $this->db->getTextValue($langData['name']),
            $this->db->getTextValue($langData['meta']),
            $this->db->getTextValue($langData['error_text']),
            $this->db->getTextValue($langData['encoding'])
        );

        ++$this->_queries;
        $success = $this->db->query($query);
        $this->options['strings_tables'][$langData['lang_id']] = $langData['table_name'];
        return $success;
    }

    // }}}
    // {{{ removeLang()

    /**
     * Remove the lang from the langsAvail table and drop the strings table.
     * If the strings table holds other langs and $force==false, then
     * only the lang column is dropped. If $force==true the whole
     * table is dropped without any check
     *
     * @param string  $langID
     * @param boolean $force
     * @return true|PEAR_Error
     */
    function removeLang($langID, $force)
    {
        //remove from langsAvail
        $query = sprintf('DELETE FROM %s WHERE %s = %s',
            $this->db->quoteIdentifier($this->options['langs_avail_table']),
            $this->db->quoteIdentifier($this->options['lang_id_col']),
            $this->db->getTextValue($langID)
        );
        ++$this->_queries;
        $res = $this->db->query($query);
        if (PEAR::isError($res)) {
            return $res;
        }

        $lang_table = $this->_getLangTable($langID);
        if ($force) {
            //remove the whole table
            ++$this->_queries;
            return $this->db->query('DROP TABLE ' . $this->db->quoteIdentifier($lang_table));
        }

        //drop only the column for this lang
        $query = sprintf('ALTER TABLE %s DROP COLUMN %s',
            $this->db->quoteIdentifier($lang_table),
            $this->db->quoteIdentifier($this->_getLangCol($langID))
        );
        ++$this->_queries;
        return $this->db->query($query);
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
        $allFields = array(
            //'lang_id'    => 'lang_id_col',
            'name'       => 'lang_name_col',
            'meta'       => 'lang_meta_col',
            'error_text' => 'lang_errmsg_col',
            'encoding'   => 'lang_encoding_col',
        );
        $updateFields = array_keys($langData);
        $langSet = array();
        foreach ($allFields as $field => $col) {
            if (in_array($field, $updateFields)) {
                $langSet[] = $this->db->quoteIdentifier($this->options[$col]) . ' = ' .
                             $this->db->getTextValue($langData[$field]);
            }
        }
        $query = sprintf('UPDATE %s SET %s WHERE %s=%s',
            $this->db->quoteIdentifier($this->options['langs_avail_table']),
            implode(', ', $langSet),
            $this->db->quoteIdentifier($this->options['lang_id_col']),
            $this->db->getTextValue($langData['lang_id'])
        );

        ++$this->_queries;
        $success = $this->db->query($query);
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

        if (!count($langs)) {
            //return error: no valid lang provided
            return true;
        }

        // Langs may be in different tables - we need to split up queries along
        // table lines, so we can keep DB traffic to a minimum.

        $unquoted_stringID = $stringID;
        $unquoted_pageID   = $pageID;
        $stringID = $this->db->getTextValue($stringID);
        $pageID   = is_null($pageID) ? 'NULL' : $this->db->getTextValue($pageID);
        // Loop over the tables we need to insert into.
        foreach ($this->_tableLangs($langs) as $table => $tableLangs) {
            $exists = $this->_recordExists($unquoted_stringID, $unquoted_pageID, $table);
            if (PEAR::isError($exists)) {
                return $exists;
            }
            $func  = $exists ? '_getUpdateQuery' : '_getInsertQuery';
            $query = $this->$func($table, $tableLangs, $stringID, $pageID, $stringArray);

            ++$this->_queries;
            $res = $this->db->query($query);
            if (PEAR::isError($res)) {
                return $res;
            }
        }

        return true;
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
    // {{{ _getInsertQuery()

    /**
     * Build a SQL query to INSERT a record
     *
     * @access private
     * @return string
     */
    function _getInsertQuery($table, &$tableLangs, $stringID, $pageID, &$stringArray)
    {
        $tableCols = $this->_getLangCols($tableLangs);
        $langData = array();
        foreach ($tableLangs as $lang) {
            $langData[$lang] = $this->db->getTextValue($stringArray[$lang]);
        }
        foreach (array_keys($tableCols) as $k) {
            $tableCols[$k] = $this->db->quoteIdentifier($tableCols[$k]);
        }

        return sprintf('INSERT INTO %s (%s, %s, %s) VALUES (%s, %s, %s)',
            $this->db->quoteIdentifier($table),
            $this->db->quoteIdentifier($this->options['string_id_col']),
            $this->db->quoteIdentifier($this->options['string_page_id_col']),
            implode(', ', $tableCols),
            $stringID,
            $pageID,
            implode(', ', $langData)
        );
    }

    // }}}
    // {{{ _getUpdateQuery()

    /**
     * Build a SQL query to UPDATE a record
     *
     * @access private
     * @return string
     */
    function _getUpdateQuery($table, &$tableLangs, $stringID, $pageID, &$stringArray)
    {
        $tableCols = $this->_getLangCols($tableLangs);
        $langSet = array();
        foreach ($tableLangs as $lang) {
            $langSet[] = $this->db->quoteIdentifier($tableCols[$lang]) . ' = ' .
                         $this->db->getTextValue($stringArray[$lang]);
        }

        return sprintf('UPDATE %s SET %s WHERE %s = %s AND %s = %s',
            $this->db->quoteIdentifier($table),
            implode(', ', $langSet),
            $this->db->quoteIdentifier($this->options['string_id_col']),
            $stringID,
            $this->db->quoteIdentifier($this->options['string_page_id_col']),
            $pageID
        );
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
        $tables = array_unique($this->_getLangTables());

        $stringID = $this->db->getTextValue($stringID);
        // get the tables and skip the non existent ones
        $dbTables = $this->db->listTables();
        foreach ($tables as $table) {
            if (!in_array($table, $dbTables)) {
                continue;
            }
            $query = sprintf('DELETE FROM %s WHERE %s = %s AND %s',
                $this->db->quoteIdentifier($table),
                $this->db->quoteIdentifier($this->options['string_id_col']),
                $stringID,
                $this->db->quoteIdentifier($this->options['string_page_id_col'])
            );
            if (is_null($pageID)) {
                $query .= ' IS NULL';
            } else {
                $query .= ' = ' . $this->db->getTextValue($pageID);
            }

            ++$this->_queries;
            $res = $this->db->query($query);
            if (PEAR::isError($res)) {
                return $res;
            }
        }

        return true;
    }

    // }}}
    // {{{ getPageNames()

    /**
     * Get a list of all the pageIDs in any table.
     *
     * @return array
     */
    function getPageNames()
    {
        $pages = array();
        foreach ($this->_getLangTables() as $table) {
            $query = sprintf('SELECT DISTINCT %s FROM %s',
                 $this->db->quoteIdentifier($this->options['string_page_id_col']),
                 $this->db->quoteIdentifier($table)
            );
            ++$this->_queries;
            $res = $this->db->getCol($query);
            if (PEAR::isError($res)) {
                return $res;
            }
            $pages = array_merge($pages, $res);
        }
        return array_unique($pages);
    }

    // }}}
    // {{{ _tableLangs()

    /**
     * Get table -> language mapping
     *
     * The key of the array is the table that a language is stored in;
     * the value is an /array/ of languages stored in that table.
     *
     * @param   array  $langs  Languages to get mapping for
     * @return  array  Table -> language mapping
     * @access  private
     * @see     Translation2_Container_MDB::_getLangTable()
     * @author  Ian Eure
     */
    function &_tableLangs($langs)
    {
        $tables = array();
        foreach ($langs as $lang) {
            $table = $this->_getLangTable($lang);
            $tables[$table][] = $lang;
        }
        return $tables;
    }

    // }}}
    // {{{ _getLangTables()

    /**
     * Get tables for languages
     *
     * This is like _getLangTable(), but it returns an array of the tables for
     * multiple languages.
     *
     * @param   array    $langs  Languages to get tables for
     * @return  array
     * @access  private
     * @author  Ian Eure
     */
    function &_getLangTables($langs = null)
    {
        $tables = array();
        $langs = !is_array($langs) ? $this->getLangs('ids') : $langs;
        foreach ($langs as $lang) {
            $tables[] = $this->_getLangTable($lang);
        }
        $tables = array_unique($tables);
        return $tables;
    }

    // }}}
    // {{{ _getLangCols()

    /**
     * Get table columns strings are stored in
     *
     * This is like _getLangCol(), except it returns an array which contains
     * the mapping for multiple languages.
     *
     * @param   array  $langs  Languages to get mapping for
     * @return  array  Language -> column mapping
     * @access  private
     * @see     Translation2_Container_MDB::_getLangCol()
     * @author  Ian Eure
     */
    function &_getLangCols($langs)
    {
        $cols = array();
        foreach ($langs as $lang) {
            $cols[$lang] = $this->_getLangCol($lang);
        }
        return $cols;
    }

    // }}}
    // {{{ _recordExists()

    /**
     * Check if there's already a record in the table with the
     * given (pageID, stringID) pair.
     *
     * @param string $stringID
     * @param string $pageID
     * @param string $table
     * @return boolean
     * @access private
     */
    function _recordExists($stringID, $pageID, $table)
    {
        $stringID = $this->db->getTextValue($stringID);
        $pageID = is_null($pageID) ? ' IS NULL' : ' = ' . $this->db->getTextValue($pageID);
        $query = sprintf('SELECT COUNT(*) FROM %s WHERE %s=%s AND %s%s',
            $this->db->quoteIdentifier($table),
            $this->db->quoteIdentifier($this->options['string_id_col']),
            $stringID,
            $this->db->quoteIdentifier($this->options['string_page_id_col']),
            $pageID
        );
        ++$this->_queries;
        $res = $this->db->getOne($query);
        if (PEAR::isError($res)) {
            return $res;
        }
        return ($res > 0);
    }

    // }}}
    // {{{ _filterStringsByTable()

    /**
     * Get only the strings for the langs in the given table
     *
     * @param string $pageID
     * @param array  $stringArray Associative array with string translations.
     *               Sample format:  array('en' => 'sample', 'it' => 'esempio')
     * @access private
     */
    function &_filterStringsByTable($stringArray, $table)
    {
        $strings = array();
        foreach ($stringArray as $lang => $string) {
            if ($table == $this->_getLangTable($lang)) {
                $strings[$lang] = $string;
            }
        }
        return $strings;
    }

    // }}}
    // {{{ _getLangsInTable()

    /**
     * Get the languages sharing the given table
     *
     * @param string $table table name
     * @return array
     */
    function &_getLangsInTable($table)
    {
        $this->fetchLangs(); // force cache refresh
        $langsInTable = array();
        foreach (array_keys($this->langs) as $lang) {
            if ($table == $this->_getLangTable($lang)) {
                $langsInTable[] = $lang;
            }
        }
        return $langsInTable;
    }

    // }}}
}
?>
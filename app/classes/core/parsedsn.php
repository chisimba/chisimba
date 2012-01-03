<?php

/**
 * ParseDSN function.
 *
 * Provides the ParseDSN function.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @author    Jeremy O'Connor <joconnor@uwc.ac.za>
 * @copyright 2007, 2012 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: languageconfig_class_inc.php 22414 2011-09-07 15:14:24Z davidwaf $
 * @link      http://avoir.uwc.ac.za
 * @see
 */

/**
 * Function to parse the DSN from a string style DSN to an array for
 * portability reasons.
 *
 * @param  string $dsn DSN as a string
 * @return array Parsed DSN as an array
 */
function parseDSN($dsn) {
    //$parsed = NULL;
    $parsed = array();
    //$arr = NULL;
    if (is_array ( $dsn )) {
        //$dsn = array_merge ( $parsed, $dsn );
        return $dsn;
    }

    // Find the 'phptype(dbsyntax)'
    if (($pos = strpos ( $dsn, '://' )) !== false) {
        $str = substr ( $dsn, 0, $pos );
        $dsn = substr ( $dsn, $pos + 3 );
    } else {
        //return array();
        $str = $dsn;
        $dsn = '';
    }
    // Split 'phptype'/'dbsyntax'
    if (preg_match ( '|^(.+?)\((.*?)\)$|', $str, $arr )) {
        $parsed ['phptype'] = rawurldecode($arr[1]);
        if (!empty($arr[2])) {
            $parsed ['dbsyntax'] = rawurldecode($arr[2]);
        }
    } else {
        $parsed ['phptype'] = rawurldecode($str);
    }
    if ($dsn == '') {
        return $parsed;
    }

    // Find the 'username:password'
    if (($pos = strrpos ( $dsn, '@' )) !== false) {
        $str = substr ( $dsn, 0, $pos );
        $dsn = substr ( $dsn, $pos + 1 );
        if (($pos_inner = strpos ( $str, ':' )) !== false) {
            $str_username = substr ( $str, 0, $pos_inner );
            $str_password = substr ( $str, $pos_inner + 1 );
            $parsed ['username'] = rawurldecode ( $str_username );
            $parsed ['password'] = rawurldecode ( $str_password );
        } else {
            $parsed ['username'] = rawurldecode ( $str );
        }
    }

    // Find the 'hostspec'('hostname[:port]')
    if (($pos = strrpos ( $dsn, '/' )) !== false) {
        $str_hostspec = substr ( $dsn, 0, $pos );
        $str_database = substr ( $dsn, $pos + 1 );
        $parsed ['hostspec'] = rawurldecode ( $str_hostspec );
        $parsed ['database'] = rawurldecode($str_database);
    } else {
        $parsed ['hostspec'] = rawurldecode($dsn);
    }
    $parsed ['hostspec'] = str_replace ( "+", "/", $parsed ['hostspec'] );
    //trigger_error($parsed ['hostspec']);

    return $parsed;
}

?>